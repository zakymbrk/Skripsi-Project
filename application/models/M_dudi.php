<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_dudi extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    private $table = 'tb_dudi';

    function generateRandomString($length = 20) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    function tambah_dudi($data)
    {
        if(!isset($data['dudi_code'])){
            $data['dudi_code'] = $this->generateRandomString(30);
        }
        // Set default is_mitra to 1 (mitra) if not provided
        if(!isset($data['is_mitra'])){
            $data['is_mitra'] = 1; // Default as mitra
        }
        // Set default status_kerjasama based on is_mitra if not provided
        if(!isset($data['status_kerjasama'])){
            $data['status_kerjasama'] = ($data['is_mitra'] == 1) ? 'mitra' : 'non_mitra';
        }
        // Set default sumber_data if not provided
        if(!isset($data['sumber_data'])){
            $data['sumber_data'] = 'sekolah';
        }
        return $this->db->insert($this->table, $data);
    }

    function normalisasi_status_mitra($value)
    {
        $status = strtolower(trim((string) $value));
        if ($status === 'mitra' || $status === '1' || $status === 'ya' || $status === 'true' || $status === 'y') {
            return ['is_mitra' => 1, 'status_kerjasama' => 'mitra'];
        }

        return ['is_mitra' => 0, 'status_kerjasama' => 'non_mitra'];
    }

    function import_dudi_batch($rows)
    {
        $inserted = 0;
        $updated = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            if (empty($row)) {
                continue;
            }

            $nama = isset($row['dudi_nama']) ? trim((string) $row['dudi_nama']) : '';
            if ($nama === '') {
                $errors[] = 'Baris ' . ($index + 2) . ': Nama DUDI kosong.';
                continue;
            }

            $alamat = isset($row['dudi_alamat']) ? trim((string) $row['dudi_alamat']) : '';
            $telepon = isset($row['dudi_telepon']) ? trim((string) $row['dudi_telepon']) : null;
            $email = isset($row['dudi_email']) ? trim((string) $row['dudi_email']) : null;
            $pic = isset($row['dudi_pic']) ? trim((string) $row['dudi_pic']) : null;
            $nip_pic = isset($row['dudi_nip_pic']) ? trim((string) $row['dudi_nip_pic']) : null;
            $instruktur = isset($row['dudi_instruktur']) ? trim((string) $row['dudi_instruktur']) : null;
            $nip_instruktur = isset($row['dudi_nip_instruktur']) ? trim((string) $row['dudi_nip_instruktur']) : null;
            $latitude = isset($row['latitude']) ? trim((string) $row['latitude']) : null;
            $longitude = isset($row['longitude']) ? trim((string) $row['longitude']) : null;
            $radius = isset($row['radius']) ? trim((string) $row['radius']) : 100;
            $status = isset($row['status_kerjasama']) ? trim((string) $row['status_kerjasama']) : (isset($row['is_mitra']) ? $row['is_mitra'] : 'mitra');

            $status_data = $this->normalisasi_status_mitra($status);

            $data = [
                'dudi_nama' => $nama,
                'dudi_alamat' => $alamat,
                'dudi_telepon' => $telepon !== '' ? $telepon : null,
                'dudi_email' => $email !== '' ? $email : null,
                'dudi_pic' => $pic !== '' ? $pic : null,
                'dudi_nip_pic' => $nip_pic !== '' ? $nip_pic : null,
                'dudi_instruktur' => $instruktur !== '' ? $instruktur : null,
                'dudi_nip_instruktur' => $nip_instruktur !== '' ? $nip_instruktur : null,
                'latitude' => $latitude !== '' && $latitude !== null ? (float) $latitude : null,
                'longitude' => $longitude !== '' && $longitude !== null ? (float) $longitude : null,
                'radius' => is_numeric($radius) ? (int) $radius : 100,
                'is_mitra' => $status_data['is_mitra'],
                'status_kerjasama' => $status_data['status_kerjasama'],
                'sumber_data' => 'sekolah',
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $existing = $this->get_dudi_by_nama($nama);
            if ($existing) {
                $this->db->where('dudi_code', $existing->dudi_code);
                $this->db->update($this->table, $data);
                $updated++;
                continue;
            }

            $data['dudi_code'] = $this->generateRandomString(30);
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert($this->table, $data);
            $inserted++;
        }

        return [
            'inserted' => $inserted,
            'updated' => $updated,
            'errors' => $errors
        ];
    }

    function get_all_dudi()
    {
        $this->db->order_by('dudi_nama', 'ASC');
        return $this->db->get($this->table);
    }

    function get_dudi($dudi_code)
    {
        $this->db->where('dudi_code', $dudi_code);
        $query = $this->db->get($this->table);
        return $query->row();
    }

    function update_dudi($data, $dudi_code)
    {
        // Ensure consistency between is_mitra and status_kerjasama
        if(isset($data['is_mitra']) && !isset($data['status_kerjasama'])){
            $data['status_kerjasama'] = ($data['is_mitra'] == 1) ? 'mitra' : 'non_mitra';
        } elseif(isset($data['status_kerjasama']) && !isset($data['is_mitra'])){
            $data['is_mitra'] = ($data['status_kerjasama'] == 'mitra') ? 1 : 0;
        }
        
        $this->db->where('dudi_code', $dudi_code);
        return $this->db->update($this->table, $data);
    }

    function delete_dudi($dudi_code)
    {
        $this->db->where('dudi_code', $dudi_code);
        return $this->db->delete($this->table);
    }
    
    // New function to get mitra DUDI only
    function get_mitra_dudi()
    {
        $this->db->where('is_mitra', 1);
        $this->db->order_by('dudi_nama', 'ASC');
        return $this->db->get($this->table);
    }
    
    // New function to get non-mitra DUDI only
    function get_nonmitra_dudi()
    {
        $this->db->where('is_mitra', 0);
        $this->db->order_by('dudi_nama', 'ASC');
        return $this->db->get($this->table);
    }
    
    // New function to get DUDI by name
    function get_dudi_by_nama($nama)
    {
        $this->db->where('dudi_nama', $nama);
        return $this->db->get($this->table)->row();
    }
    
    // New function to get DUDI by name and status
    function get_dudi_by_nama_and_status($nama, $status)
    {
        $this->db->where('dudi_nama', $nama);
        $this->db->where('status_kerjasama', $status);
        return $this->db->get($this->table)->row();
    }
    
    // Fungsi untuk mendapatkan DUDI berdasarkan status kerjasama
    function get_dudi_by_status($status)
    {
        $this->db->where('status_kerjasama', $status);
        $this->db->order_by('dudi_nama', 'ASC');
        return $this->db->get($this->table);
    }
    
    // Fungsi untuk mendapatkan DUDI mitra
    function get_dudi_mitra()
    {
        return $this->get_dudi_by_status('mitra');
    }
    
    // Fungsi untuk mendapatkan DUDI non-mitra
    function get_dudi_non_mitra()
    {
        return $this->get_dudi_by_status('non_mitra');
    }
    
    // Fungsi untuk mendapatkan DUDI pengajuan
    function get_dudi_pengajuan()
    {
        return $this->get_dudi_by_status('pengajuan');
    }
    
    // Fungsi untuk mendapatkan DUDI berdasarkan sumber data
    function get_dudi_by_sumber($sumber)
    {
        $this->db->where('sumber_data', $sumber);
        $this->db->order_by('dudi_nama', 'ASC');
        return $this->db->get($this->table);
    }
    
    // Fungsi untuk mendapatkan DUDI dari sekolah
    function get_dudi_sekolah()
    {
        return $this->get_dudi_by_sumber('sekolah');
    }
    
    // Fungsi untuk mendapatkan DUDI dari siswa
    function get_dudi_siswa()
    {
        return $this->get_dudi_by_sumber('siswa');
    }
    
    // Fungsi untuk menghitung jumlah DUDI berdasarkan kriteria
    function count_dudi_by_criteria($criteria = array())
    {
        if(!empty($criteria)) {
            foreach($criteria as $key => $value) {
                $this->db->where($key, $value);
            }
        }
        return $this->db->count_all_results($this->table);
    }
    
    // Fungsi untuk mendapatkan statistik DUDI
    function get_dudi_statistics()
    {
        $stats = array();
        
        // Hitung berdasarkan status kerjasama
        $stats['mitra'] = $this->count_dudi_by_criteria(array('status_kerjasama' => 'mitra'));
        $stats['non_mitra'] = $this->count_dudi_by_criteria(array('status_kerjasama' => 'non_mitra'));
        $stats['pengajuan'] = $this->count_dudi_by_criteria(array('status_kerjasama' => 'pengajuan'));
        
        // Hitung berdasarkan sumber data
        $stats['dari_sekolah'] = $this->count_dudi_by_criteria(array('sumber_data' => 'sekolah'));
        $stats['dari_siswa'] = $this->count_dudi_by_criteria(array('sumber_data' => 'siswa'));
        
        // Total
        $stats['total'] = $stats['mitra'] + $stats['non_mitra'] + $stats['pengajuan'];
        
        return $stats;
    }
    
    // Fungsi untuk merekomendasikan DUDI berdasarkan nama
    function rekomendasikan_dudi($nama_dudi, $data_siswa = array())
    {
        $data = array(
            'dudi_nama' => $nama_dudi,
            'dudi_alamat' => isset($data_siswa['alamat']) ? $data_siswa['alamat'] : '',
            'dudi_telepon' => isset($data_siswa['telepon']) ? $data_siswa['telepon'] : '',
            'dudi_email' => isset($data_siswa['email']) ? $data_siswa['email'] : '',
            'status_kerjasama' => 'pengajuan',
            'sumber_data' => 'siswa',
            'dudi_code' => $this->generateRandomString(30),
            'created_at' => date('Y-m-d H:i:s')
        );
        
        return $this->db->insert($this->table, $data);
    }
    
    // Get all companies with coordinates for mapping
    public function get_all_with_coordinates()
    {
        $this->db->select('d.*, s.siswa_jurusan as jurusan');
        $this->db->from('tb_dudi d');
        $this->db->join('tb_siswa s', 's.dudi_id = d.dudi_id', 'left');
        $this->db->group_by('d.dudi_id');
        $this->db->order_by('d.dudi_nama', 'ASC');
        return $this->db->get()->result();
    }
}