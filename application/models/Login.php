<?php
defined('BASEPATH')OR exit('No direct script access allowed');

class Login extends CI_Model {

    
    public function __construct()
    {
        parent::__construct();
        //Do your magic here
    }

    function cek_login($data_login)
    {
        $result = null;

        $this->db->select('tb_user.id, tb_user.user_code, tb_user.username, tb_user.password, tb_user.nama_lengkap, tb_user.email, tb_user.telepon, tb_user.alamat, tb_user.tempat_lahir, tb_user.tanggal_lahir, tb_user.jenis_kelamin, tb_user.nip_nim, tb_user.foto_profil, tb_user.level, tb_user.is_active, tb_group.group_name');
        $this->db->from('tb_user');
        $this->db->join('tb_group', 'tb_group.group_id = tb_user.level');
        $this->db->where('tb_user.username', $data_login['username']);
        $this->db->where('tb_user.is_active', 1);
        $cekuser = $this->db->get();

        if($cekuser->num_rows() > 0){
            $row = $cekuser->row();
            $stored_password = $row->password;
            $is_valid = password_verify($data_login['password'], $stored_password);
            if($is_valid){
                $result = $row;
            }
        }
        return $result;
    }
}