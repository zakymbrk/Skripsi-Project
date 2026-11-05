<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/userguide3/general/urls.html
	 */

	
	public function __construct()
	{
		parent::__construct();
		//Cek apapkah sudah memiliki Session
		
		//Do your magic here
	}
	
	public function index()
	{
		if($this->session->userdata('userdata')){
			$userdata = $this->session->userdata('userdata');
			//Cek level user dan redirect dengan fungsi aman
			switch($userdata['level']){
				case 1: // Hubin
					safe_redirect('hubin', 'view', 'home');
					break;
				case 2: // Pembimbing
					safe_redirect('guru', 'view', 'home');
					break;
				case 3: // Siswa
					safe_redirect('siswa', 'view', 'home');
					break;
				default:
					safe_redirect('auth', 'logout');
					break;
			}
		}
		// Default to landing page for students
		redirect('');
	}
	
	public function login_siswa()
	{
		if($this->session->userdata('userdata')){
			$userdata = $this->session->userdata('userdata');
			if($userdata['level'] == 3){
				safe_redirect('siswa', 'view', 'home');
			} else {
				$this->session->unset_userdata('userdata');
			}
		}
		$this->load->view('authentication/login-siswa');
	}
	
	public function login_pembimbing()
	{
		if($this->session->userdata('userdata')){
			$userdata = $this->session->userdata('userdata');
			if($userdata['level'] == 2){
				safe_redirect('guru', 'view', 'home');
			} else {
				$this->session->unset_userdata('userdata');
			}
		}
		$this->load->view('authentication/login-pembimbing');
	}

	public function login_hubin()
	{
		if($this->session->userdata('userdata')){
			$userdata = $this->session->userdata('userdata');
			if($userdata['level'] == 1){
				safe_redirect('hubin', 'view', 'home');
			} else {
				$this->session->unset_userdata('userdata');
			}
		}
		$this->load->view('authentication/login-hubin');
	}

	public function do_login()
	{
		$status = true;
		$this->form_validation->set_rules('username', 'Username', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');
		if ($this->form_validation->run() == FALSE) {
			$status = false;
		}
		
		if($status){
			//Load Models
			$this->load->model('Login');
			$data_login = array(
				'username'	=> $this->input->post('username'),
				'password'	=> $this->input->post('password')
			);
			
			$cekk = $this->Login->cek_login($data_login);
	
			if($cekk){
				$user_login = array(
					'userdata'	=> array(
						'id'			=> $cekk->id,
						'username'		=> $cekk->username,
						'nama_lengkap'	=> $cekk->nama_lengkap,
						'level'			=> $cekk->level,
						'group_name'	=> $cekk->group_name,
						'foto_profil'	=> isset($cekk->foto_profil) ? $cekk->foto_profil : null
					),
				);

				$this->session->sess_regenerate(TRUE);
				$this->session->set_userdata($user_login);
				
				// Redirect langsung ke dashboard sesuai level dengan fungsi aman
				switch($cekk->level){
					case 1: // Hubin
						safe_redirect('hubin', 'view', 'home');
						break;
					case 2: // Pembimbing
						safe_redirect('guru', 'view', 'home');
						break;
					case 3: // Siswa
						safe_redirect('siswa', 'view', 'home');
						break;
					default:
						$this->session->set_flashdata('message', '<div class="alert alert-danger">Level user tidak valid</div>');
						$this->load->view('authentication/login');
						return;
				}
				
			}else{
				$this->session->set_flashdata('message', '<div class="alert alert-warning">Username atau Password salah</div>');
				$this->load->view('authentication/login');
			}
		
		}else{
			$this->load->view('authentication/login');
		}
	}
	
	public function do_login_siswa()
	{
		$status = true;
		$this->form_validation->set_rules('username', 'Username', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');
		if ($this->form_validation->run() == FALSE) {
			$status = false;
		}
		
		if($status){
			$this->load->model('Login');
			$data_login = array(
				'username'	=> $this->input->post('username'),
				'password'	=> $this->input->post('password')
			);
			
			$cekk = $this->Login->cek_login($data_login);
	
			if($cekk && $cekk->level == 3){ // Hanya siswa (level 3)
				$user_login = array(
					'userdata'	=> array(
						'id'			=> $cekk->id,
						'username'		=> $cekk->username,
						'nama_lengkap'	=> $cekk->nama_lengkap,
						'level'			=> $cekk->level,
						'group_name'	=> $cekk->group_name,
						'foto_profil'	=> isset($cekk->foto_profil) ? $cekk->foto_profil : null
					),
				);

				$this->session->sess_regenerate(TRUE);
				$this->session->set_userdata($user_login);
				safe_redirect('siswa', 'view', 'home');
				
			}else{
				$this->session->set_flashdata('message', '<div class="alert alert-warning">Username atau Password salah, atau Anda bukan siswa</div>');
				$this->load->view('authentication/login-siswa');
			}
		
		}else{
			$this->load->view('authentication/login-siswa');
		}
	}

	public function do_login_pembimbing()
	{
		$status = true;
		$this->form_validation->set_rules('username', 'Username', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');
		if ($this->form_validation->run() == FALSE) {
			$status = false;
		}

		if($status){
			$this->load->model('Login');
			$data_login = array(
				'username'	=> $this->input->post('username'),
				'password'	=> $this->input->post('password')
			);

			$cekk = $this->Login->cek_login($data_login);

			if($cekk && $cekk->level == 2){
				$user_login = array(
					'userdata'	=> array(
						'id'			=> $cekk->id,
						'username'		=> $cekk->username,
						'nama_lengkap'	=> $cekk->nama_lengkap,
						'level'			=> $cekk->level,
						'group_name'	=> $cekk->group_name,
						'foto_profil'	=> isset($cekk->foto_profil) ? $cekk->foto_profil : null
					),
				);

				$this->session->sess_regenerate(TRUE);
				$this->session->set_userdata($user_login);
				safe_redirect('guru', 'view', 'home');

			}else{
				$this->session->set_flashdata('message', '<div class="alert alert-warning">Username atau Password salah, atau Anda bukan guru pembimbing</div>');
				$this->load->view('authentication/login-pembimbing');
			}

		}else{
			$this->load->view('authentication/login-pembimbing');
		}
	}

	public function do_login_hubin()
	{
		$status = true;
		$this->form_validation->set_rules('username', 'Username', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');
		if ($this->form_validation->run() == FALSE) {
			$status = false;
		}
		
		if($status){
			$this->load->model('Login');
			$data_login = array(
				'username'	=> $this->input->post('username'),
				'password'	=> $this->input->post('password')
			);
			
			$cekk = $this->Login->cek_login($data_login);
	
			if($cekk && $cekk->level == 1){ // Only for hubin
				$user_login = array(
					'userdata'	=> array(
						'id'			=> $cekk->id,
						'username'		=> $cekk->username,
						'nama_lengkap'	=> $cekk->nama_lengkap,
						'level'			=> $cekk->level,
						'group_name'	=> $cekk->group_name,
						'foto_profil'	=> isset($cekk->foto_profil) ? $cekk->foto_profil : null
					),
				);

				$this->session->sess_regenerate(TRUE);
				$this->session->set_userdata($user_login);
				safe_redirect('hubin', 'view', 'home');
				
			}else{
				$this->session->set_flashdata('message', '<div class="alert alert-warning">Username atau Password salah, atau Anda bukan hubin</div>');
				$this->load->view('authentication/login-hubin');
			}
		
		}else{
			$this->load->view('authentication/login-hubin');
		}
	}

	function logout()
	{
		// Hancurkan seluruh session agar tombol Back tidak bisa mengakses dashboard
		$this->session->sess_destroy();
		redirect('auth/index');
	}

	function information()
	{
		$data['content']	= 'information';
		$this->load->view('template', $data);
	}
}
