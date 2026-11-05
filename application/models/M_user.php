<?php
defined('BASEPATH')OR exit('No direct script access allowed');

class M_user extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        //Do your magic here
    }

    private $table = 'tb_user'; 
    private $status= true; 

    function generateRandomString($length = 20) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    function normalize_identifier_value($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        if (preg_match('/^[-+]?(?:\d+(?:\.\d*)?|\.\d+)(?:[Ee][-+]?\d+)$/', $value)) {
            $numeric = (float) $value;
            if (is_finite($numeric)) {
                $normalized = preg_replace('/\.0+$/', '', sprintf('%.0f', $numeric));
                if ($normalized !== '' && preg_match('/^\d+$/', $normalized)) {
                    return $normalized;
                }
            }
        }

        return $value;
    }

    function tambah_data_user($data)
    {
        if (isset($data['nip_nim'])) {
            $data['nip_nim'] = $this->normalize_identifier_value($data['nip_nim']);
        }

        /*===========================
        =    INSERT KE DATABASE     =
        ===========================*/
        $insert = $this->db->insert($this->table, $data);
        if(!$insert){
            $this->status = false;
        }
        return $this->status;        
    }

    function get_data_user($user_code)
    {
        $result = array();

        $this->db->where('user_code', $user_code);
        $this->db->join('tb_group', 'tb_group.group_id = tb_user.level');
        $cekuser = $this->db->get($this->table);
        if($cekuser->num_rows() != 0){
            $result = $cekuser->row();
        }

        return $result;
    }

    function update_user($data, $user_code)
    {
        if (isset($data['nip_nim'])) {
            $data['nip_nim'] = $this->normalize_identifier_value($data['nip_nim']);
        }

        $this->db->where('user_code', $user_code);
        $result = $this->db->update('tb_user', $data);
        
        // Log the query for debugging
        log_message('debug', 'User update query: ' . $this->db->last_query());
        log_message('debug', 'User update affected rows: ' . $this->db->affected_rows());
        
        return $result;
    }

    function delete_user($user_code)
    {
        $this->db->where('user_code', $user_code);
        $user = $this->db->get('tb_user')->row();

        if (!$user) {
            return false;
        }

        $user_id = (int) $user->id;
        $this->db->trans_start();

        $this->db->select('siswa_id');
        $this->db->where('user_id', $user_id);
        $siswa_rows = $this->db->get('tb_siswa')->result();

        $siswa_ids = [];
        foreach ($siswa_rows as $siswa) {
            $siswa_ids[] = (int) $siswa->siswa_id;
        }

        if (!empty($siswa_ids)) {
            $this->db->where_in('siswa_id', $siswa_ids)->delete('tb_pengelompokan');
            $this->db->where_in('siswa_id', $siswa_ids)->delete('tb_presensi');
            $this->db->where_in('siswa_id', $siswa_ids)->delete('tb_logbook');
            $this->db->where_in('siswa_id', $siswa_ids)->delete('tb_penilaian');
        }

        $this->db->where('user_id', $user_id);
        $this->db->delete('tb_siswa');

        $this->db->select('pembimbing_id');
        $this->db->where('user_id', $user_id);
        $pembimbing_rows = $this->db->get('tb_pembimbing')->result();

        $pembimbing_ids = [];
        foreach ($pembimbing_rows as $pembimbing) {
            $pembimbing_ids[] = (int) $pembimbing->pembimbing_id;
        }

        if (!empty($pembimbing_ids)) {
            $this->db->where_in('pembimbing_id', $pembimbing_ids)->delete('tb_pengelompokan');
            $this->db->where_in('pembimbing_id', $pembimbing_ids)->delete('tb_penilaian');
            $this->db->where_in('pembimbing_id', $pembimbing_ids)->update('tb_siswa', ['pembimbing_id' => null]);
        }

        $this->db->where('user_id', $user_id);
        $this->db->delete('tb_pembimbing');

        $this->db->where('user_code', $user_code);
        $result = $this->db->delete('tb_user');

        $this->db->trans_complete();

        return $this->db->trans_status() && $result;
    }

    function get_user_by_id($id)
    {
        $this->db->select('tb_user.*, tb_group.group_name');
        $this->db->where('tb_user.id', $id);
        $this->db->join('tb_group', 'tb_group.group_id = tb_user.level');
        $query = $this->db->get($this->table);
        return $query->row();
    }

    function update_user_by_id($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update('tb_user', $data);
        return true;
    }

    function get_all_users()
    {
        $this->db->join('tb_group', 'tb_group.group_id = tb_user.level');
        $this->db->order_by('tb_user.id', 'DESC');
        return $this->db->get($this->table);
    }
    
    function get_all_users_count()
    {
        return $this->db->count_all_results($this->table);
    }
    
    function get_all_users_paginated($limit, $offset)
    {
        $this->db->select('tb_user.*, tb_group.group_name');
        $this->db->join('tb_group', 'tb_group.group_id = tb_user.level');
        $this->db->order_by('tb_user.id', 'DESC');
        $this->db->limit($limit, $offset);
        return $this->db->get($this->table);
    }
    
    function get_all_users_count_search($search = '')
    {
        if ($search) {
            $this->db->group_start();
            $this->db->like('tb_user.username', $search);
            $this->db->or_like('tb_user.nama_lengkap', $search);
            $this->db->group_end();
        }
        return $this->db->count_all_results($this->table);
    }
    
    function get_all_users_paginated_search($limit, $offset, $search = '')
    {
        $this->db->select('tb_user.*, tb_group.group_name');
        $this->db->join('tb_group', 'tb_group.group_id = tb_user.level');
        if ($search) {
            $this->db->group_start();
            $this->db->like('tb_user.username', $search);
            $this->db->or_like('tb_user.nama_lengkap', $search);
            $this->db->group_end();
        }
        $this->db->order_by('tb_user.id', 'DESC');
        $this->db->limit($limit, $offset);
        return $this->db->get($this->table);
    }

    /**
     * Mendapatkan data user dengan identitas lengkap
     * @param string $user_code
     * @return object
     */
    function get_user_with_identity($user_code)
    {
        $this->db->select('tb_user.*, tb_group.group_name');
        $this->db->where('tb_user.user_code', $user_code);
        $this->db->join('tb_group', 'tb_group.group_id = tb_user.level');
        $query = $this->db->get($this->table);
        return $query->row();
    }

    /**
     * Update identitas lengkap user
     * @param array $identity_data
     * @param string $user_code
     * @return bool
     */
    function update_user_identity($identity_data, $user_code)
    {
        // Filter hanya field identitas yang valid
        $valid_fields = ['email', 'telepon', 'alamat', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin'];
        $filtered_data = array_intersect_key($identity_data, array_flip($valid_fields));
        
        // Tambahkan timestamp update
        $filtered_data['updated_at'] = date('Y-m-d H:i:s');
        
        $this->db->where('user_code', $user_code);
        return $this->db->update($this->table, $filtered_data);
    }

    /**
     * Validasi format email
     * @param string $email
     * @return bool
     */
    function validate_email($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validasi format nomor telepon
     * @param string $phone
     * @return bool
     */
    function validate_phone($phone)
    {
        // Hapus spasi dan karakter non-digit
        $clean_phone = preg_replace('/[^0-9]/', '', $phone);
        // Minimal 10 digit, maksimal 15 digit
        return strlen($clean_phone) >= 10 && strlen($clean_phone) <= 15;
    }

    /**
     * Mendapatkan daftar user dengan identitas lengkap untuk reporting
     * @return object
     */
    function get_users_with_complete_identity()
    {
        $this->db->select('tb_user.username, tb_user.nama_lengkap, tb_user.email, tb_user.telepon, 
                          tb_user.alamat, tb_user.tempat_lahir, tb_user.tanggal_lahir, 
                          tb_user.jenis_kelamin, tb_user.nip_nim, tb_group.group_name');
        $this->db->join('tb_group', 'tb_group.group_id = tb_user.level');
        $this->db->order_by('tb_user.level', 'ASC');
        $this->db->order_by('tb_user.nama_lengkap', 'ASC');
        return $this->db->get($this->table);
    }
}