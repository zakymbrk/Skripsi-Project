<?php
defined('BASEPATH')OR exit('No direct script access allowed');

class M_pembimbing extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    private $table = 'tb_pembimbing'; 
    private $status = true; 

    function generateRandomString($length = 20) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    function tambah_data_pembimbing($data)
    {
        // Ensure required fields are set with defaults if not provided
        if(!isset($data['pembimbing_nip'])) {
            $data['pembimbing_nip'] = NULL;
        }
        if(!isset($data['pembimbing_telepon'])) {
            $data['pembimbing_telepon'] = NULL;
        }
        if(!isset($data['pembimbing_email'])) {
            $data['pembimbing_email'] = NULL;
        }

        
        $insert = $this->db->insert($this->table, $data);
        if(!$insert){
            $this->status = false;
        }
        return $this->status;        
    }

    function get_data_pembimbing($pembimbing_code)
    {
        $result = array();

        $this->db->where('pembimbing_code', $pembimbing_code);
        // Tidak lagi join dengan tb_user karena pembimbing tidak terhubung dengan user
        $cek = $this->db->get($this->table);
        if($cek->num_rows() != 0){
            $result = $cek->row();
        }

        return $result;
    }

    // Fungsi get_pembimbing_by_user_id telah dihapus karena pembimbing tidak terhubung dengan user

    function update_pembimbing($data, $pembimbing_code)
    {
        $result = true;

        $this->db->where('pembimbing_code', $pembimbing_code);
        $this->db->update('tb_pembimbing', $data);

        return $result;
    }

    function delete_pembimbing($pembimbing_code)
    {
        $this->db->where('pembimbing_code', $pembimbing_code);
        $pembimbing = $this->db->get('tb_pembimbing')->row();

        if (!$pembimbing) {
            return false;
        }

        $pembimbing_id = (int) $pembimbing->pembimbing_id;
        $user_id = isset($pembimbing->user_id) ? (int) $pembimbing->user_id : null;

        $this->db->trans_start();

        $this->db->where('pembimbing_id', $pembimbing_id);
        $this->db->delete('tb_pengelompokan');

        $this->db->where('pembimbing_id', $pembimbing_id);
        $this->db->delete('tb_penilaian');

        $this->db->where('pembimbing_id', $pembimbing_id);
        $this->db->update('tb_siswa', ['pembimbing_id' => null]);

        $this->db->where('pembimbing_code', $pembimbing_code);
        $this->db->delete('tb_pembimbing');

        if ($user_id) {
            $this->db->where('id', $user_id);
            $this->db->delete('tb_user');
        }

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    function get_all_pembimbing()
    {
        // Tidak lagi join dengan tb_user karena pembimbing tidak terhubung dengan user
        $this->db->order_by('pembimbing_id', 'DESC');
        return $this->db->get($this->table);
    }

    function get_pembimbing_with_siswa_count()
    {
        $this->db->select('tb_pembimbing.*, COUNT(tb_pengelompokan.siswa_id) as jumlah_siswa');
        $this->db->from('tb_pembimbing');
        // Tidak lagi join dengan tb_user karena pembimbing tidak terhubung dengan user
        $this->db->join('tb_pengelompokan', 'tb_pengelompokan.pembimbing_id = tb_pembimbing.pembimbing_id', 'left');
        $this->db->group_by('tb_pembimbing.pembimbing_id');
        $this->db->order_by('tb_pembimbing.pembimbing_id', 'DESC');
        return $this->db->get();
    }
    
    // New function to get pembimbing with full biodata (tanpa user)
    function get_pembimbing_with_biodata($pembimbing_code)
    {
        $this->db->select('tb_pembimbing.*, tb_user.username, tb_user.password, tb_user.nama_lengkap, tb_user.email as user_email, tb_user.telepon as user_telepon, tb_user.alamat as user_alamat, tb_user.tempat_lahir as user_tempat_lahir, tb_user.tanggal_lahir as user_tanggal_lahir, tb_user.jenis_kelamin as user_jenis_kelamin, tb_user.nip_nim as user_nip_nim');
        $this->db->from('tb_pembimbing');
        $this->db->join('tb_user', 'tb_user.id = tb_pembimbing.user_id AND tb_user.level = 2', 'left');
        $this->db->where('tb_pembimbing.pembimbing_code', $pembimbing_code);
        return $this->db->get()->row();
    }
    
    /**
     * Update biodata lengkap pembimbing
     * @param array $biodata_data
     * @param string $pembimbing_code
     * @return bool
     */
    function update_biodata_pembimbing($biodata_data, $pembimbing_code)
    {
        // Filter only valid fields for pembimbing
        $valid_fields = [
            'pendidikan_terakhir', 'jabatan', 'jurusan_keahlian', 
            'tahun_masuk', 'status_kepegawaian', 'tempat_tugas',
            'pembimbing_nama', 'pembimbing_nip', 'pembimbing_telepon',
            'pembimbing_email', 'pembimbing_alamat'
        ];
        $filtered_data = array_intersect_key($biodata_data, array_flip($valid_fields));
        
        // Add update timestamp
        $filtered_data['updated_at'] = date('Y-m-d H:i:s');
        
        $this->db->where('pembimbing_code', $pembimbing_code);
        return $this->db->update($this->table, $filtered_data);
    }
    
    /**
     * Validasi format tahun masuk
     * @param string $tahun
     * @return bool
     */
    function validate_tahun_masuk($tahun)
    {
        return preg_match('/^\d{4}$/', $tahun) && $tahun >= 1950 && $tahun <= date('Y');
    }
    
    /**
     * Mendapatkan daftar pembimbing dengan biodata lengkap untuk reporting (tanpa user)
     * @return object
     */
    function get_pembimbing_with_complete_biodata()
    {
        $this->db->select('tb_pembimbing.pembimbing_nama, tb_pembimbing.pembimbing_nip, tb_pembimbing.pembimbing_telepon,
                          tb_pembimbing.pembimbing_email, tb_pembimbing.pembimbing_alamat,
                          tb_pembimbing.pendidikan_terakhir, tb_pembimbing.jabatan, tb_pembimbing.jurusan_keahlian,
                          tb_pembimbing.tahun_masuk, tb_pembimbing.status_kepegawaian,
                          tb_pembimbing.tempat_tugas');
        $this->db->from('tb_pembimbing');
        // Tidak lagi join dengan tb_user karena pembimbing tidak terhubung dengan user
        $this->db->order_by('tb_pembimbing.pembimbing_nama', 'ASC');
        return $this->db->get();
    }
    
    /**
     * Mendapatkan pembimbing dengan biodata lengkap berdasarkan pembimbing_id (bukan user_id)
     * @param int $pembimbing_id
     * @return object
     */
    function get_pembimbing_with_complete_biodata_by_pembimbing_id($pembimbing_id)
    {
        $this->db->select('tb_pembimbing.*, tb_user.username, tb_user.password, tb_user.nama_lengkap, tb_user.email as user_email, tb_user.telepon as user_telepon, tb_user.alamat as user_alamat, tb_user.tempat_lahir as user_tempat_lahir, tb_user.tanggal_lahir as user_tanggal_lahir, tb_user.jenis_kelamin as user_jenis_kelamin, tb_user.nip_nim as user_nip_nim');
        $this->db->from('tb_pembimbing');
        $this->db->join('tb_user', 'tb_user.id = tb_pembimbing.user_id AND tb_user.level = 2', 'left');
        $this->db->where('tb_pembimbing.pembimbing_id', $pembimbing_id);
        return $this->db->get()->row();
    }
    
    /**
     * Update pembimbing berdasarkan pembimbing_id (bukan user_id)
     * @param array $data
     * @param int $pembimbing_id
     * @return bool
     */
    function update_pembimbing_by_pembimbing_id($data, $pembimbing_id)
    {
        $this->db->where('pembimbing_id', $pembimbing_id);
        return $this->db->update('tb_pembimbing', $data);
    }
    
    /**
     * Mendapatkan jumlah pembimbing berdasarkan status kepegawaian
     * @return object
     */
    function get_pembimbing_count_by_status()
    {
        $this->db->select('status_kepegawaian, COUNT(*) as jumlah');
        $this->db->from('tb_pembimbing');
        $this->db->where('status_kepegawaian IS NOT NULL');
        $this->db->group_by('status_kepegawaian');
        return $this->db->get();
    }
    
    /**
     * Get pembimbing by pembimbing_id
     * @param int $pembimbing_id
     * @return object
     */
    function get_pembimbing_by_id($pembimbing_id)
    {
        $this->db->select('tb_pembimbing.*');
        $this->db->from('tb_pembimbing');
        $this->db->where('tb_pembimbing.pembimbing_id', $pembimbing_id);
        return $this->db->get()->row();
    }
    
    /**
     * Get pembimbing dengan pagination dan search
     * @param int $limit
     * @param int $offset
     * @param string $search
     * @return object
     */
    function get_pembimbing_paginated($limit, $offset, $search = '')
    {
        $this->db->select('tb_pembimbing.*, tb_user.username, tb_user.nama_lengkap, tb_user.email as user_email, tb_user.telepon as user_telepon, tb_user.alamat as user_alamat');
        $this->db->from('tb_pembimbing');
        $this->db->join('tb_user', 'tb_user.id = tb_pembimbing.user_id AND tb_user.level = 2', 'left');
        
        if($search) {
            $this->db->like('tb_pembimbing.pembimbing_nama', $search);
        }
        
        $this->db->order_by('tb_pembimbing.pembimbing_id', 'DESC');
        $this->db->limit($limit, $offset);
        return $this->db->get();
    }
    
    /**
     * Count total pembimbing dengan search
     * @param string $search
     * @return int
     */
    function count_pembimbing($search = '')
    {
        $this->db->from('tb_pembimbing');
        
        if($search) {
            $this->db->like('tb_pembimbing.pembimbing_nama', $search);
        }
        
        return $this->db->count_all_results();
    }
}