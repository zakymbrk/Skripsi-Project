<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        // Jika sudah login, redirect ke dashboard sesuai level
        if($this->session->userdata('userdata')){
            $userdata = $this->session->userdata('userdata');
            if($userdata['level'] == 1){
                // Hubin
                redirect('hubin/view/home');
            } elseif($userdata['level'] == 2){
                // Guru Pembimbing
                redirect('guru/view/home');
            } elseif($userdata['level'] == 3){
                // Siswa
                redirect('siswa/view/home');
            } else {
                // Level tidak valid → logout
                $this->session->sess_destroy();
                redirect('auth/index');
            }
        }
        
        // Get statistics
        $data['total_siswa'] = $this->db->get('tb_siswa')->num_rows() ?: 0;
        $data['total_user'] = $this->db->get('tb_user')->num_rows() ?: 0;
        $data['total_pembimbing'] = $this->db->get('tb_pembimbing')->num_rows() ?: 0;
        $data['siswa_prakerin'] = $this->db->where('status_pengajuan', 'disetujui')->get('tb_siswa')->num_rows() ?: 0;
        $data['total_dudi'] = $this->db->get('tb_dudi')->num_rows() ?: 0;
        
        // Get pengumuman with creator info
        $this->load->model('M_pengumuman');
        $data['pengumuman'] = $this->M_pengumuman->get_recent_pengumuman(5);
        
        $this->load->view('landing/index', $data);
    }
    
    public function show_404()
    {
        $this->load->view('404');
    }
    
    public function detail_pengumuman($pengumuman_id = null)
    {
        if(!$pengumuman_id) {
            redirect('home');
        }
        
        // Load model
        $this->load->model('M_pengumuman');
        
        // Get pengumuman detail
        $data['pengumuman'] = $this->M_pengumuman->get_pengumuman($pengumuman_id);
        
        if(!$data['pengumuman']) {
            redirect('home');
        }
        
        $this->load->view('landing/detail-pengumuman', $data);
    }
}

