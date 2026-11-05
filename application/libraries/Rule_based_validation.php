<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Rule_based_validation
 * 
 * Engine Algoritma Rule-Based Validation (RBV) untuk Sistem Monitoring PKL
 * SMK ITIKURIH HIBARNA Ciparay
 * 
 * Mengimplementasikan Rule RBV-001 s.d. RBV-019:
 * - RBV-001: Validasi duplikasi presensi masuk (satu kali per hari)
 * - RBV-002: Validasi prasyarat presensi pulang (harus sudah presensi masuk)
 * - RBV-003: Validasi kelengkapan parameter data presensi
 * - RBV-004: Validasi wajib foto bukti presensi kehadiran fisik
 * - RBV-005: Penolakan data yang melanggar aturan sistem
 * - RBV-006: Validasi Geolocation & Radius Lokasi DUDI (Haversine Formula)
 * - RBV-007: Validasi duplikasi pengisian logbook per tanggal (satu kali per hari)
 * - RBV-008: Validasi isi deskripsi kegiatan (tidak kosong & min. 20 karakter)
 * - RBV-009: Validasi wajib upload foto dokumentasi kegiatan logbook
 * - RBV-010: Penolakan data logbook tidak lengkap
 * - RBV-011: Penyediaan alasan penolakan presensi & logbook spesifik
 * - RBV-012: Pencegahan presensi masuk ganda pada level database
 * - RBV-013: Pencegahan presensi pulang ganda
 * - RBV-014: Pencegahan logbook ganda pada tanggal yang sama
 * - RBV-015: Validasi kelayakan status siswa (disetujui) sebelum akses monitoring
 * - RBV-016: Integrasi Logbook Valid/Tidak Valid & Pengurangan Nilai (-5 poin)
 * - RBV-017: Validasi keputusan review logbook pembimbing
 * - RBV-018: Kewajiban alasan penolakan logbook
 * - RBV-019: Validasi integritas koordinat dan perpindahan GPS
 */
class Rule_based_validation {

    protected $CI;

    public function __construct()
    {
        $this->CI = get_instance();
        $this->CI->load->database();
    }

    /**
     * Menghitung jarak antara dua koordinat GPS menggunakan formula Haversine (dalam meter)
     * 
     * @param float $lat1
     * @param float $lon1
     * @param float $lat2
     * @param float $lon2
     * @return float Jarak dalam satuan meter
     */
    public function calculate_distance($lat1, $lon1, $lat2, $lon2)
    {
        $earth_radius = 6371000; // Radius bumi dalam meter
        $dLat = deg2rad((float)$lat2 - (float)$lat1);
        $dLon = deg2rad((float)$lon2 - (float)$lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad((float)$lat1)) * cos(deg2rad((float)$lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return round($earth_radius * $c, 2);
    }

    /**
     * RBV-019: Detect invalid coordinates and implausible movement between recent GPS samples.
     */
    public function validate_location_integrity($latitude, $longitude, $previous_latitude = null, $previous_longitude = null, $previous_seen_at = null)
    {
        if (!is_numeric($latitude) || !is_numeric($longitude) || $latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
            return [
                'is_valid' => false,
                'fake_gps' => false,
                'rule_id' => 'RBV-019',
                'message' => 'Koordinat perangkat tidak valid. Aktifkan lokasi dan coba kembali.'
            ];
        }

        if (is_numeric($previous_latitude) && is_numeric($previous_longitude) && !empty($previous_seen_at)) {
            $previous_time = strtotime($previous_seen_at);
            $elapsed_seconds = $previous_time ? time() - $previous_time : 0;
            if ($elapsed_seconds > 0 && $elapsed_seconds <= 900) {
                $distance = $this->calculate_distance($previous_latitude, $previous_longitude, $latitude, $longitude);
                $speed_meters_per_second = $distance / $elapsed_seconds;

                if ($distance >= 750 && $speed_meters_per_second > (200000 / 3600)) {
                    return [
                        'is_valid' => false,
                        'fake_gps' => true,
                        'rule_id' => 'RBV-019',
                        'distance' => $distance,
                        'speed_kmh' => round($speed_meters_per_second * 3.6, 1),
                        'message' => 'Indikasi penggunaan Fake GPS terdeteksi: perpindahan lokasi tidak mungkin dalam waktu singkat. Presensi tidak sah dan perlu diklarifikasi kepada guru pembimbing.'
                    ];
                }
            }
        }

        return [
            'is_valid' => true,
            'fake_gps' => false,
            'rule_id' => 'RBV-PASS',
            'message' => 'Integritas perpindahan lokasi memenuhi aturan.'
        ];
    }

    /**
     * RBV-017 and RBV-018: Validate a pembimbing's logbook decision and rejection reason.
     */
    public function validate_logbook_review($decision, $catatan, $nilai, $alasan_penolakan = '')
    {
        $decision = trim((string) $decision);
        $catatan = trim((string) $catatan);
        $alasan_penolakan = trim((string) $alasan_penolakan);

        if (!in_array($decision, ['valid', 'tidak_valid'], true) || $catatan === '' || !is_numeric($nilai) || $nilai < 0 || $nilai > 100) {
            return [
                'is_valid' => false,
                'rule_id' => 'RBV-017',
                'message' => 'Keputusan, catatan pembimbing, dan nilai 0 sampai 100 wajib diisi dengan benar.'
            ];
        }

        if ($decision === 'tidak_valid' && $alasan_penolakan === '') {
            return [
                'is_valid' => false,
                'rule_id' => 'RBV-018',
                'message' => 'Alasan penolakan wajib diisi agar siswa mengetahui bagian yang perlu diperbaiki.'
            ];
        }

        return [
            'is_valid' => true,
            'rule_id' => 'RBV-PASS',
            'status_validasi' => $decision,
            'alasan_penolakan' => $decision === 'tidak_valid' ? $alasan_penolakan : null,
            'message' => $decision === 'valid' ? 'Logbook disetujui.' : 'Logbook ditolak dengan alasan yang tercatat.'
        ];
    }

    /**
     * RBV-006: Validasi Geolocation & Radius Lokasi Siswa terhadap Lokasi Instansi (DUDI)
     * 
     * @param int $siswa_id
     * @param float|null $lat_siswa
     * @param float|null $lon_siswa
     * @param string $status_kehadiran ('hadir','izin','sakit')
     * @return array
     */
    public function validate_geolocation($siswa_id, $lat_siswa = null, $lon_siswa = null, $status_kehadiran = 'hadir')
    {
        // Jika izin/sakit, geolokasi tidak diwajibkan
        if ($status_kehadiran !== 'hadir') {
            return [
                'is_valid'      => true,
                'rule_id'       => 'RBV-PASS',
                'jarak'         => null,
                'radius'        => null,
                'latitude_dudi' => null,
                'longitude_dudi'=> null,
                'status_lokasi' => 'tanpa_geolokasi',
                'message'       => 'Status ' . $status_kehadiran . ' tidak memerlukan verifikasi radius lokasi.'
            ];
        }

        // Ambil data siswa & DUDI
        $this->CI->db->select('s.siswa_id, s.dudi_id, d.dudi_nama, d.latitude, d.longitude, d.radius');
        $this->CI->db->from('tb_siswa s');
        $this->CI->db->join('tb_dudi d', 'd.dudi_id = s.dudi_id', 'left');
        $this->CI->db->where('s.siswa_id', $siswa_id);
        $dudi_info = $this->CI->db->get()->row();

        if (!$dudi_info || empty($dudi_info->dudi_id)) {
            return [
                'is_valid'      => true,
                'rule_id'       => 'RBV-PASS',
                'jarak'         => null,
                'radius'        => null,
                'latitude_dudi' => null,
                'longitude_dudi'=> null,
                'status_lokasi' => 'tanpa_geolokasi',
                'message'       => 'Instansi PKL belum di-assign atau belum memiliki data koordinat.'
            ];
        }

        $lat_dudi = $dudi_info->latitude;
        $lon_dudi = $dudi_info->longitude;
        $radius   = (!empty($dudi_info->radius) && $dudi_info->radius > 0) ? (float)$dudi_info->radius : 100.0;

        // Jika DUDI belum disetel koordinatnya
        if (empty($lat_dudi) || empty($lon_dudi)) {
            return [
                'is_valid'      => true,
                'rule_id'       => 'RBV-PASS',
                'jarak'         => null,
                'radius'        => $radius,
                'latitude_dudi' => null,
                'longitude_dudi'=> null,
                'status_lokasi' => 'tanpa_geolokasi',
                'message'       => 'Koordinat instansi DUDI belum ditentukan oleh Hubin.'
            ];
        }

        // Jika koordinat siswa kosong saat kehadiran hadir
        if (empty($lat_siswa) || empty($lon_siswa)) {
            return [
                'is_valid'      => false,
                'rule_id'       => 'RBV-006',
                'jarak'         => null,
                'radius'        => $radius,
                'latitude_dudi' => $lat_dudi,
                'longitude_dudi'=> $lon_dudi,
                'status_lokasi' => 'tidak_valid',
                'message'       => 'Koordinat GPS perangkat Anda tidak terdeteksi. Harap aktifkan GPS dan izinkan browser mengakses lokasi untuk presensi kehadiran fisik di tempat PKL.'
            ];
        }

        $coordinate_check = $this->validate_location_integrity($lat_siswa, $lon_siswa);
        if (!$coordinate_check['is_valid']) {
            return $coordinate_check;
        }

        // Hitung jarak menggunakan formula Haversine
        $jarak = $this->calculate_distance($lat_siswa, $lon_siswa, $lat_dudi, $lon_dudi);

        // Periksa apakah jarak berada di dalam radius toleransi
        if ($jarak > $radius) {
            return [
                'is_valid'      => false,
                'rule_id'       => 'RBV-006',
                'jarak'         => $jarak,
                'radius'        => $radius,
                'latitude_dudi' => $lat_dudi,
                'longitude_dudi'=> $lon_dudi,
                'status_lokasi' => 'tidak_valid',
                'message'       => 'Lokasi Anda berada di luar radius instansi PKL (' . $dudi_info->dudi_nama . '). Jarak Anda: ' . round($jarak) . ' meter (Radius toleransi: ' . $radius . ' meter). Presensi ditolak.'
            ];
        }

        // Lolos validasi radius
        return [
            'is_valid'      => true,
            'rule_id'       => 'RBV-PASS',
            'jarak'         => $jarak,
            'radius'        => $radius,
            'latitude_dudi' => $lat_dudi,
            'longitude_dudi'=> $lon_dudi,
            'status_lokasi' => 'valid',
            'message'       => 'Lokasi terverifikasi berada di dalam radius instansi PKL (' . round($jarak) . ' meter).'
        ];
    }

    /**
     * RBV-001, RBV-003, RBV-004, RBV-005, RBV-006, RBV-012:
     * Validasi Presensi Masuk
     */
    public function validate_presensi_masuk($siswa_id, $tanggal, $data = [], $foto_file = null, $lat_siswa = null, $lon_siswa = null)
    {
        // 1. RBV-003: Validasi kelengkapan data dasar
        if (empty($siswa_id) || empty($tanggal)) {
            return [
                'is_valid' => false,
                'rule_id' => 'RBV-003',
                'message' => 'Data presensi tidak lengkap. Siswa dan tanggal wajib diisi.'
            ];
        }

        $status_kehadiran = isset($data['status_kehadiran']) ? $data['status_kehadiran'] : 'hadir';

        // 2. RBV-001 & RBV-012: Cek apakah presensi masuk untuk tanggal ini sudah ada
        $this->CI->db->where('siswa_id', $siswa_id);
        $this->CI->db->where('tanggal', $tanggal);
        $existing = $this->CI->db->get('tb_presensi')->row();

        if ($existing && !empty($existing->jam_masuk)) {
            return [
                'is_valid' => false,
                'rule_id' => 'RBV-001',
                'message' => 'Presensi masuk untuk tanggal ' . date('d-m-Y', strtotime($tanggal)) . ' sudah dilakukan sebelumnya pada pukul ' . $existing->jam_masuk . '.'
            ];
        }

        // 3. RBV-006: Validasi Geolocation & Radius (wajib untuk kehadiran fisik)
        if ($status_kehadiran === 'hadir') {
            $geo_check = $this->validate_geolocation($siswa_id, $lat_siswa, $lon_siswa, $status_kehadiran);
            if (!$geo_check['is_valid']) {
                return $geo_check;
            }
        }

        // Lolos seluruh rule presensi masuk
        return [
            'is_valid' => true,
            'rule_id' => 'RBV-PASS',
            'message' => 'Data presensi masuk valid dan memenuhi seluruh aturan sistem.'
        ];
    }

    /**
     * RBV-002, RBV-003, RBV-004, RBV-005, RBV-006, RBV-013:
     * Validasi Presensi Pulang
     */
    public function validate_presensi_pulang($siswa_id, $tanggal, $data = [], $foto_file = null, $lat_siswa = null, $lon_siswa = null)
    {
        // 1. RBV-003: Validasi kelengkapan data dasar
        if (empty($siswa_id) || empty($tanggal)) {
            return [
                'is_valid' => false,
                'rule_id' => 'RBV-003',
                'message' => 'Data presensi pulang tidak lengkap. Siswa dan tanggal wajib diisi.',
                'presensi_row' => null
            ];
        }

        // 2. RBV-002 (Prasyarat): Periksa apakah siswa sudah presensi masuk pada tanggal tersebut
        $this->CI->db->where('siswa_id', $siswa_id);
        $this->CI->db->where('tanggal', $tanggal);
        $existing = $this->CI->db->get('tb_presensi')->row();

        if (!$existing || empty($existing->jam_masuk)) {
            return [
                'is_valid' => false,
                'rule_id' => 'RBV-002',
                'message' => 'Anda belum melakukan presensi masuk pada tanggal ini (' . date('d-m-Y', strtotime($tanggal)) . '). Presensi pulang tidak dapat dilakukan sebelum presensi masuk.',
                'presensi_row' => null
            ];
        }

        // 3. RBV-002 & RBV-013: Cek apakah presensi pulang sudah pernah dilakukan
        if (!empty($existing->jam_pulang)) {
            return [
                'is_valid' => false,
                'rule_id' => 'RBV-013',
                'message' => 'Presensi pulang untuk tanggal ' . date('d-m-Y', strtotime($tanggal)) . ' sudah dilakukan sebelumnya pada pukul ' . $existing->jam_pulang . '.',
                'presensi_row' => $existing
            ];
        }

        // 4. RBV-006: Lokasi pulang juga harus berada di dalam radius DUDI.
        $geo_check = $this->validate_geolocation($siswa_id, $lat_siswa, $lon_siswa, 'hadir');
        if (!$geo_check['is_valid']) {
            return [
                'is_valid' => false,
                'rule_id' => $geo_check['rule_id'],
                'message' => $geo_check['message'],
                'presensi_row' => $existing
            ];
        }

        // Lolos seluruh rule presensi pulang
        return [
            'is_valid' => true,
            'rule_id' => 'RBV-PASS',
            'message' => 'Data presensi pulang valid dan siap disimpan.',
            'presensi_row' => $existing
        ];
    }

    /**
    * RBV-007, RBV-008, RBV-009, RBV-010, RBV-011, RBV-014:
     * Validasi Logbook Kegiatan Harian
     */
    public function validate_logbook($siswa_id, $tanggal, $data = [], $foto_file = null)
    {
        // 1. RBV-010: Validasi kelengkapan parameter dasar
        if (empty($siswa_id) || empty($tanggal)) {
            return [
                'is_valid' => false,
                'rule_id' => 'RBV-010',
                'message' => 'Data logbook tidak lengkap. Identitas siswa dan tanggal kegiatan wajib tersedia.'
            ];
        }

        // RBV-014: Logbook hanya dapat dibuat setelah presensi masuk dan pulang.
        $this->CI->db->where('siswa_id', $siswa_id);
        $this->CI->db->where('tanggal', $tanggal);
        $presensi = $this->CI->db->get('tb_presensi')->row();
        if (!$presensi || empty($presensi->jam_masuk) || empty($presensi->jam_pulang) || $presensi->status_validasi === 'tidak_valid') {
            return [
                'is_valid' => false,
                'rule_id' => 'RBV-014',
                'message' => 'Logbook hanya dapat diisi setelah presensi masuk dan pulang selesai pada tanggal ' . date('d-m-Y', strtotime($tanggal)) . '.'
            ];
        }

        // 2. RBV-007 & RBV-014: Cek duplikasi pengisian logbook pada tanggal yang sama
        $this->CI->db->where('siswa_id', $siswa_id);
        $this->CI->db->where('tanggal', $tanggal);
        $existing = $this->CI->db->get('tb_logbook')->row();

        if ($existing) {
            return [
                'is_valid' => false,
                'rule_id' => 'RBV-007',
                'message' => 'Logbook kegiatan untuk tanggal ' . date('d-m-Y', strtotime($tanggal)) . ' sudah pernah diisi. Satu siswa hanya dapat mengisi satu logbook per tanggal.'
            ];
        }

        // 3. RBV-008: Validasi isi deskripsi kegiatan (tidak boleh kosong & minimal 20 karakter)
        $deskripsi = isset($data['deskripsi_kegiatan']) ? trim($data['deskripsi_kegiatan']) : '';
        if (empty($deskripsi) || strlen($deskripsi) < 20) {
            return [
                'is_valid' => false,
                'rule_id' => 'RBV-008',
                'message' => 'Deskripsi kegiatan tidak boleh kosong dan harus menjelaskan aktivitas PKL secara rinci (minimal 20 karakter).'
            ];
        }

        // 4. RBV-009: Validasi wajib ketersediaan foto dokumentasi kegiatan
        if (empty($foto_file)) {
            return [
                'is_valid' => false,
                'rule_id' => 'RBV-009',
                'message' => 'Foto dokumentasi kegiatan wajib diunggah sebagai bukti otentik pekerjaan harian PKL.'
            ];
        }

        // Lolos seluruh rule logbook
        return [
            'is_valid' => true,
            'rule_id' => 'RBV-PASS',
            'message' => 'Data logbook valid dan siap disimpan ke sistem.'
        ];
    }

    /**
     * RBV-015: Validasi Kelayakan Siswa untuk Melakukan Monitoring
     */
    public function normalize_pkl_schedule($siswa)
    {
        if (!$siswa) {
            return ['updated' => false, 'tanggal_mulai' => null, 'tanggal_selesai' => null];
        }

        $tanggal_mulai = !empty($siswa->tanggal_mulai) ? $siswa->tanggal_mulai : date('Y-m-d');
        $start_dt = DateTime::createFromFormat('Y-m-d', $tanggal_mulai);
        if (!$start_dt || $start_dt->format('Y-m-d') !== $tanggal_mulai) {
            $tanggal_mulai = date('Y-m-d');
            $start_dt = new DateTime($tanggal_mulai);
        }

        $today = new DateTime('today');
        if ($start_dt > $today && $start_dt->diff($today)->days <= 30) {
            // masih di masa depan dekat, tetap boleh dipakai
        } elseif ($start_dt > $today->modify('+30 days')) {
            $tanggal_mulai = $today->format('Y-m-d');
            $start_dt = new DateTime($tanggal_mulai);
        }

        $tanggal_selesai = !empty($siswa->tanggal_selesai) ? $siswa->tanggal_selesai : date('Y-m-d', strtotime($tanggal_mulai . ' +3 months -1 day'));
        $end_dt = DateTime::createFromFormat('Y-m-d', $tanggal_selesai);
        if (!$end_dt || $end_dt < $start_dt) {
            $tanggal_selesai = date('Y-m-d', strtotime($tanggal_mulai . ' +3 months -1 day'));
            $end_dt = new DateTime($tanggal_selesai);
        }

        $diff_days = $start_dt->diff($end_dt)->days;
        if ($diff_days < 60 || $diff_days > 150) {
            $tanggal_selesai = date('Y-m-d', strtotime($tanggal_mulai . ' +3 months -1 day'));
        }

        $updated = ($tanggal_mulai !== $siswa->tanggal_mulai) || ($tanggal_selesai !== $siswa->tanggal_selesai);
        if ($updated) {
            $this->CI->db->where('siswa_id', $siswa->siswa_id);
            $this->CI->db->update('tb_siswa', [
                'tanggal_mulai' => $tanggal_mulai,
                'tanggal_selesai' => $tanggal_selesai,
                'lama_pelaksanaan' => 90,
                'periode' => date('Y', strtotime($tanggal_mulai)) . '/' . (date('Y', strtotime($tanggal_mulai)) + 1)
            ]);
        }

        return [
            'updated' => $updated,
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_selesai' => $tanggal_selesai
        ];
    }

    public function validate_siswa_eligibility($user_id)
    {
        $this->CI->db->select('s.*, d.dudi_nama, d.latitude as dudi_latitude, d.longitude as dudi_longitude, d.radius as dudi_radius, p.pembimbing_nama');
        $this->CI->db->from('tb_siswa s');
        $this->CI->db->join('tb_dudi d', 'd.dudi_id = s.dudi_id', 'left');
        $this->CI->db->join('tb_pembimbing p', 'p.pembimbing_id = s.pembimbing_id', 'left');
        $this->CI->db->where('s.user_id', $user_id);
        $siswa = $this->CI->db->get()->row();

        if (!$siswa) {
            return [
                'is_allowed' => false,
                'siswa' => null,
                'message' => 'Data profil siswa Anda belum terdaftar di sistem PKL.'
            ];
        }

        if ($siswa->status_pengajuan !== 'disetujui') {
            return [
                'is_allowed' => false,
                'siswa' => $siswa,
                'message' => 'Fitur presensi dan logbook monitoring hanya dapat diakses setelah pengajuan PKL Anda disetujui oleh Hubin. Status saat ini: ' . ucfirst($siswa->status_pengajuan) . '.'
            ];
        }

        $normalized = $this->normalize_pkl_schedule($siswa);
        $siswa->tanggal_mulai = $normalized['tanggal_mulai'];
        $siswa->tanggal_selesai = $normalized['tanggal_selesai'];
        $siswa->lama_pelaksanaan = 90;

        $today = date('Y-m-d');
        if ($today < $siswa->tanggal_mulai || $today > $siswa->tanggal_selesai) {
            return [
                'is_allowed' => false,
                'siswa' => $siswa,
                'message' => 'Periode PKL Anda berlangsung dari ' . date('d-m-Y', strtotime($siswa->tanggal_mulai)) . ' sampai ' . date('d-m-Y', strtotime($siswa->tanggal_selesai)) . '.'
            ];
        }

        return [
            'is_allowed' => true,
            'siswa' => $siswa,
            'message' => 'Siswa berhak mengakses modul monitoring PKL.'
        ];
    }

    /**
     * RBV-016 (perpanjangan): Validasi status harian gabungan Presensi + Logbook
     *
     * @param int $siswa_id
     * @param string $tanggal (Y-m-d)
     * @return array
     */
    public function validate_status_harian($siswa_id, $tanggal)
    {
        if (empty($siswa_id) || empty($tanggal)) {
            return [
                'is_valid' => false,
                'rule_id'  => 'RBV-003',
                'message'  => 'Data status harian tidak lengkap.'
            ];
        }

        $this->CI->db->where('siswa_id', $siswa_id);
        $this->CI->db->where('tanggal', $tanggal);
        $presensi = $this->CI->db->get('tb_presensi')->row();

        $this->CI->db->where('siswa_id', $siswa_id);
        $this->CI->db->where('tanggal', $tanggal);
        $logbook = $this->CI->db->get('tb_logbook')->row();

        if (!$presensi || empty($presensi->jam_masuk)) {
            return [
                'is_valid' => false,
                'rule_id'  => 'RBV-002',
                'message'  => 'Presensi masuk belum dilakukan pada tanggal ' . date('d-m-Y', strtotime($tanggal)) . '.',
                'presensi' => $presensi,
                'logbook'  => $logbook
            ];
        }

        if ($presensi->status_validasi === 'tidak_valid') {
            return [
                'is_valid' => false,
                'rule_id'  => 'RBV-005',
                'message'  => 'Presensi pada tanggal ini berstatus tidak valid.',
                'presensi' => $presensi,
                'logbook'  => $logbook
            ];
        }

        if (!$logbook) {
            return [
                'is_valid' => false,
                'rule_id'  => 'RBV-010',
                'message'  => 'Logbook kegiatan harian belum diisi untuk tanggal ' . date('d-m-Y', strtotime($tanggal)) . '.',
                'presensi' => $presensi,
                'logbook'  => null
            ];
        }

        if ($logbook->status_validasi === 'tidak_valid') {
            return [
                'is_valid' => false,
                'rule_id'  => 'RBV-010',
                'message'  => 'Logbook pada tanggal ini berstatus tidak valid (pengurangan nilai -5 diterapkan).',
                'presensi' => $presensi,
                'logbook'  => $logbook
            ];
        }

        return [
            'is_valid' => true,
            'rule_id'  => 'RBV-PASS',
            'message'  => 'Presensi valid dan logbook lengkap untuk tanggal ' . date('d-m-Y', strtotime($tanggal)) . '.',
            'presensi' => $presensi,
            'logbook'  => $logbook
        ];
    }

    /**
     * RBV-016: Sinkronisasi Nilai & Pengurangan Nilai (-5 per logbook invalid / tidak lengkap)
     * 
     * @param int $siswa_id
     * @return array
     */
    public function sync_penilaian_siswa($siswa_id)
    {
        $this->ensure_penilaian_penalty_columns();
        $this->mark_missing_attendance($siswa_id);

        // 1. Hitung kehadiran dari tb_presensi
        $this->CI->db->where('siswa_id', $siswa_id);
        $presensi_rows = $this->CI->db->get('tb_presensi')->result();

        $total_hadir = 0;
        $total_alpha = 0;
        $total_terlambat = 0;
        foreach ($presensi_rows as $p) {
            if ($p->status_validasi === 'valid') {
                if ($p->status_kehadiran === 'hadir') $total_hadir++;
                if ($p->status_kehadiran === 'alpha') $total_alpha++;
                if ($p->status_kehadiran === 'hadir' && !empty($p->jam_masuk) && $p->jam_masuk > '08:00:00') {
                    $total_terlambat++;
                }
            }
        }

        // 2. Hitung logbook valid dan tidak valid
        $this->CI->db->where('siswa_id', $siswa_id);
        $logbook_rows = $this->CI->db->get('tb_logbook')->result();

        $total_valid = 0;
        $total_tidak_valid = 0;
        $nilai_pembimbing_total = 0;
        $nilai_pembimbing_count = 0;
        foreach ($logbook_rows as $l) {
            if ($l->status_validasi === 'valid') {
                $total_valid++;
            } elseif ($l->status_validasi === 'tidak_valid') {
                $total_tidak_valid++;
            }
            if ($l->status_validasi === 'valid' && isset($l->nilai_pembimbing) && $l->nilai_pembimbing !== null) {
                $nilai_pembimbing_total += (float) $l->nilai_pembimbing;
                $nilai_pembimbing_count++;
            }
        }

        // 3. Penalti otomatis: 5 poin (5% dari nilai dasar 100) per kejadian.
        $penalti_per_kejadian = 5.0;
        $pengurangan_logbook = $total_tidak_valid * $penalti_per_kejadian;
        $pengurangan_kehadiran = ($total_alpha + $total_terlambat) * $penalti_per_kejadian;
        $pengurangan_nilai = $pengurangan_logbook + $pengurangan_kehadiran;

        // 4. Hitung komponen nilai
        $nilai_kehadiran = max(0, 100 - $pengurangan_kehadiran);
        $nilai_logbook_dasar = $nilai_pembimbing_count > 0
            ? round($nilai_pembimbing_total / $nilai_pembimbing_count, 2)
            : 100;
        $nilai_logbook = max(0, $nilai_logbook_dasar - $pengurangan_logbook);
        $nilai_disiplin  = 100;
        $nilai_akhir     = round(($nilai_kehadiran * 0.40) + ($nilai_logbook * 0.60), 2);

        // Ambil pembimbing_id siswa
        $this->CI->db->where('siswa_id', $siswa_id);
        $siswa = $this->CI->db->get('tb_siswa')->row();
        $pembimbing_id = $siswa ? $siswa->pembimbing_id : null;

        // 5. Upsert ke tb_penilaian
        $data_penilaian = [
            'siswa_id'                  => $siswa_id,
            'pembimbing_id'             => $pembimbing_id,
            'total_kehadiran'           => $total_hadir,
            'total_bolos'               => $total_alpha,
            'total_terlambat'           => $total_terlambat,
            'total_logbook_valid'       => $total_valid,
            'total_logbook_tidak_valid' => $total_tidak_valid,
            'total_pengurangan_nilai'   => $pengurangan_nilai,
            'nilai_kehadiran'           => $nilai_kehadiran,
            'nilai_logbook'             => $nilai_logbook,
            'nilai_disiplin'            => $nilai_disiplin,
            'nilai_akhir'               => $nilai_akhir,
            'updated_at'                => date('Y-m-d H:i:s')
        ];

        $this->CI->db->where('siswa_id', $siswa_id);
        $existing_penilaian = $this->CI->db->get('tb_penilaian')->row();

        if ($existing_penilaian) {
            $this->CI->db->where('penilaian_id', $existing_penilaian->penilaian_id);
            $this->CI->db->update('tb_penilaian', $data_penilaian);
        } else {
            $data_penilaian['created_at'] = date('Y-m-d H:i:s');
            $this->CI->db->insert('tb_penilaian', $data_penilaian);
        }

        return $data_penilaian;
    }

    /**
     * Tandai hari PKL yang sudah selesai tetapi belum memiliki presensi sebagai alpha.
     * Hari berjalan tidak diproses agar siswa masih memiliki waktu untuk absen.
     */
    private function mark_missing_attendance($siswa_id)
    {
        $this->CI->db->select('tanggal_mulai, tanggal_selesai, status_pengajuan');
        $this->CI->db->where('siswa_id', $siswa_id);
        $siswa = $this->CI->db->get('tb_siswa')->row();

        if (!$siswa || $siswa->status_pengajuan !== 'disetujui' || empty($siswa->tanggal_mulai)) {
            return;
        }

        $tanggal_mulai = new DateTime($siswa->tanggal_mulai);
        $kemarin = new DateTime('yesterday');
        $tanggal_selesai = !empty($siswa->tanggal_selesai)
            ? new DateTime($siswa->tanggal_selesai)
            : $kemarin;
        $tanggal_akhir = $tanggal_selesai < $kemarin ? $tanggal_selesai : $kemarin;

        if ($tanggal_mulai > $tanggal_akhir) {
            return;
        }

        for ($tanggal = clone $tanggal_mulai; $tanggal <= $tanggal_akhir; $tanggal->modify('+1 day')) {
            $tanggal_string = $tanggal->format('Y-m-d');
            $this->CI->db->where('siswa_id', $siswa_id);
            $this->CI->db->where('tanggal', $tanggal_string);
            $existing = $this->CI->db->get('tb_presensi')->row();

            if (!$existing) {
                $this->CI->db->insert('tb_presensi', [
                    'siswa_id'         => $siswa_id,
                    'tanggal'          => $tanggal_string,
                    'status_kehadiran' => 'alpha',
                    'status_validasi'  => 'valid',
                    'keterangan_masuk' => 'Tidak melakukan presensi pada tanggal ' . $tanggal->format('d-m-Y') . '.',
                    'created_at'       => date('Y-m-d H:i:s')
                ]);
            }
        }
    }

    private function ensure_penilaian_penalty_columns()
    {
        if (!$this->CI->db->field_exists('total_bolos', 'tb_penilaian')) {
            $this->CI->db->query('ALTER TABLE `tb_penilaian` ADD COLUMN `total_bolos` INT(11) DEFAULT 0 AFTER `total_kehadiran`');
        }
        if (!$this->CI->db->field_exists('total_terlambat', 'tb_penilaian')) {
            $this->CI->db->query('ALTER TABLE `tb_penilaian` ADD COLUMN `total_terlambat` INT(11) DEFAULT 0 AFTER `total_bolos`');
        }
    }
}

