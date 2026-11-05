<?php
define('BASEPATH') OR exit('No direct script access allowed');

/**
 * Validator Library
 *
 * Menyediakan fungsi validasi berbasis aturan (rule‑based) untuk presensi dan logbook.
 * Semua pengecekan tercatat pada tabel `validation_logs` sehingga guru/admin dapat
 * meninjau alasan kenapa data dianggap valid atau tidak valid.
 */
class Validator {
    protected $CI;
    protected $logModel;

    public function __construct()
    {
        // Dapatkan instance CI
        $this->CI =& get_instance();
        // Load model log validasi
        $this->CI->load->model('Validation_log_model', 'validation_log');
        $this->logModel = $this->CI->validation_log;
    }

    /**
     * Validasi presensi.
     *
     * @param array $data
     *   - siswa_id
     *   - instansi_id
     *   - tipe_presensi (Masuk / Pulang)
     *   - tanggal (Y-m-d)
     *   - latitude, longitude (GPS siswa)
     *   - foto (optional)
     * @return array
     *   - valid (bool)
     *   - status_lokasi (Dalam Radius / Di Luar Radius)
     *   - jarak (meter)
     *   - alasan (string)
     */
    public function validatePresensi($data)
    {
        $result = [
            'valid' => false,
            'status_lokasi' => 'Di Luar Radius',
            'jarak' => null,
            'alasan' => ''
        ];

        $siswa_id   = $data['siswa_id'];
        $instansi_id= $data['instansi_id'];
        $tipe       = $data['tipe_presensi']; // Masuk atau Pulang
        $tanggal    = $data['tanggal'];
        $lat_user   = $data['latitude'];
        $lon_user   = $data['longitude'];

        // 1. Cek penempatan PKL (siswa harus berada pada instansi yang sama)
        $this->CI->load->model('Penempatan_pkl_model', 'penempatan');
        $penempatan = $this->CI->penempatan->get_by_siswa($siswa_id);
        if (empty($penempatan) || $penempatan->instansi_id != $instansi_id) {
            $result['alasan'] = 'Instansi tidak sesuai penempatan PKL.';
            $this->log('presensi', $siswa_id, 'RULE-P01', $result['alasan'], 'invalid');
            return $result;
        }

        // 2. Dapatkan data instansi (latitude, longitude, radius)
        $this->CI->load->model('Instansi_model', 'instansi');
        $instansi = $this->CI->instansi->get_by_id($instansi_id);
        if (!$instansi) {
            $result['alasan'] = 'Data instansi tidak ditemukan.';
            $this->log('presensi', $siswa_id, 'RULE-P02', $result['alasan'], 'invalid');
            return $result;
        }
        $lat_inst = $instansi->latitude;
        $lon_inst = $instansi->longitude;
        $radius   = $instansi->radius; // meter

        // 3. Hitung jarak dengan helper GPS
        $this->CI->load->helper('gps'); // function get_distance()
        $jarak = get_distance($lat_user, $lon_user, $lat_inst, $lon_inst, 'meter');
        $result['jarak'] = $jarak;
        if ($jarak <= $radius) {
            $result['status_lokasi'] = 'Dalam Radius';
        }

        // 4. Cek duplikasi presensi (aturan 1,2,5,6)
        $this->CI->load->model('Presensi_model', 'presensi');
        $existing = $this->CI->presensi->get_by_siswa_date_type($siswa_id, $tanggal, $tipe);
        if ($existing) {
            $result['alasan'] = "Presensi $tipe sudah dilakukan hari ini.";
            $this->log('presensi', $siswa_id, 'RULE-P03', $result['alasan'], 'invalid');
            return $result;
        }

        // 5. Aturan masuk/pulang
        if (strtolower($tipe) === 'masuk') {
            // Boleh jika belum ada presensi masuk hari ini (sudah dicek di atas)
            $result['valid'] = true;
            $this->log('presensi', $siswa_id, 'RULE-P04', 'Presensi masuk diterima', 'valid');
        } else { // Pulang
            // Harus ada presensi masuk terlebih dahulu
            $presensi_masuk = $this->CI->presensi->get_by_siswa_date_type($siswa_id, $tanggal, 'Masuk');
            if (!$presensi_masuk) {
                $result['alasan'] = 'Tidak dapat presensi pulang sebelum presensi masuk.';
                $this->log('presensi', $siswa_id, 'RULE-P05', $result['alasan'], 'invalid');
                return $result;
            }
            $result['valid'] = true;
            $this->log('presensi', $siswa_id, 'RULE-P06', 'Presensi pulang diterima', 'valid');
        }

        // 6. Validasi lokasi
        if ($result['status_lokasi'] === 'Di Luar Radius') {
            $result['valid'] = false;
            $result['alasan'] = 'Lokasi berada di luar radius instansi.';
            $this->log('presensi', $siswa_id, 'RULE-P07', $result['alasan'], 'invalid');
        }

        return $result;
    }

    /**
     * Validasi logbook harian.
     *
     * @param array $data
     *   - siswa_id
     *   - tanggal (Y-m-d)
     *   - deskripsi
     *   - dokumentasi (nama file atau null)
     * @return array
     *   - valid (bool)
     *   - alasan (string)
     *   - nilai_pengurangan (int, default 0)
     */
    public function validateLogbook($data)
    {
        $result = ['valid' => false, 'alasan' => '', 'nilai_pengurangan' => 0];
        $siswa_id = $data['siswa_id'];
        $tanggal  = $data['tanggal'];
        $deskripsi= trim($data['deskripsi'] ?? '');
        $dokumentasi = $data['dokumentasi'] ?? null;

        // 1. Satu logbook per hari
        $this->CI->load->model('Logbook_model', 'logbook');
        $exists = $this->CI->logbook->get_by_siswa_date($siswa_id, $tanggal);
        if ($exists) {
            $result['alasan'] = 'Logbook untuk tanggal ini sudah ada.';
            $this->log('logbook', $siswa_id, 'RULE-L03', $result['alasan'], 'invalid');
            return $result;
        }

        // 2. RULE‑L01: deskripsi tidak boleh kosong
        if (empty($deskripsi)) {
            $result['alasan'] = 'Deskripsi kegiatan belum diisi.';
            $result['nilai_pengurangan'] = 5;
            $this->log('logbook', $siswa_id, 'RULE-L01', $result['alasan'], 'invalid');
            return $result;
        }

        // 3. RULE‑L02: dokumentasi wajib
        if (empty($dokumentasi)) {
            $result['alasan'] = 'Dokumentasi kegiatan belum diunggah.';
            $result['nilai_pengurangan'] = 5;
            $this->log('logbook', $siswa_id, 'RULE-L02', $result['alasan'], 'invalid');
            return $result;
        }

        // 4. RULE‑L04: harus ada presensi valid pada tanggal yang sama
        $this->CI->load->model('Presensi_model', 'presensi');
        $presensi = $this->CI->presensi->get_valid_by_siswa_date($siswa_id, $tanggal);
        if (!$presensi) {
            $result['alasan'] = 'Tidak ada presensi valid pada tanggal ini.';
            $result['nilai_pengurangan'] = 5;
            $this->log('logbook', $siswa_id, 'RULE-L04', $result['alasan'], 'invalid');
            return $result;
        }

        // Semua aturan terpenuhi → VALID
        $result['valid'] = true;
        $result['alasan'] = 'Logbook valid.';
        $this->log('logbook', $siswa_id, 'RULE-L04', $result['alasan'], 'valid');
        return $result;
    }

    /**
     * Simpan log validasi ke tabel `validation_logs`.
     */
    protected function log($jenis, $siswa_id, $rule, $keterangan, $hasil)
    {
        $data = [
            'siswa_id'       => $siswa_id,
            'jenis_validasi' => $jenis,
            'rule'           => $rule,
            'hasil'          => $hasil,
            'keterangan'     => $keterangan,
            'created_at'     => date('Y-m-d H:i:s')
        ];
        $this->logModel->insert($data);
    }
}
?>
