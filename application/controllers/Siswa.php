<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Siswa extends CI_Controller {

    public function __construct()
    {
        date_default_timezone_set('Asia/Jakarta');
        parent::__construct();
        if(!$this->session->userdata('userdata')){
            redirect('auth/login_siswa');
        } elseif($this->session->userdata('userdata')['level'] != 3){
            // Hanya level 3 (Siswa) yang boleh mengakses controller ini
            // Level 1 (Hubin) dan Level 2 (Pembimbing) TIDAK boleh masuk ke sini
            $this->session->set_flashdata('error_message', 'Akses ditolak. Halaman ini hanya untuk siswa.');
            redirect('auth/index');
        }

        // Validasi keberadaan user_id di database untuk mencegah sesi usang / foreign key 1452
        $userdata = $this->session->userdata('userdata');
        if($userdata && isset($userdata['id'])){
            $this->db->where('id', $userdata['id']);
            $this->db->where('is_active', 1);
            $user_exists = $this->db->get('tb_user')->row();
            if(!$user_exists){
                $this->session->unset_userdata('userdata');
                $this->session->set_flashdata('message', '<div class="alert alert-warning">Sesi Anda telah kedaluwarsa atau akun tidak ditemukan di database. Silakan login kembali.</div>');
                redirect('auth/login_siswa');
            }
        }
        // Load essential models
        $this->load->model('M_user');
        $this->load->model('M_siswa');
        $this->load->model('M_presensi');
        $this->load->model('M_logbook');
        $this->load->model('M_pengelompokan');
        $this->load->model('M_penilaian');
        $this->load->model('M_pembimbing');

        // Sinkronisasi harian menandai hari PKL yang terlewat sebagai alpha.
        $this->load->library('rule_based_validation');
        $siswa_harian = get_siswa_by_user_id($userdata['id']);
        if ($siswa_harian) {
            $this->rule_based_validation->sync_penilaian_siswa($siswa_harian->siswa_id);
        }
    }
    
    // Calculate distance between two coordinates (Haversine formula)
    private function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        $R = 6371000; // Earth radius in meters
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = 
            sin($dLat/2) * sin($dLat/2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $R * $c;
    }
    
    function view($page = 'home')
    {
        // Handle detail-pengumuman dengan parameter
        if($page == 'detail-pengumuman'){
            $pengumuman_id = $this->uri->segment(4);
            if($pengumuman_id){
                $data['content'] = 'siswa/detail-pengumuman';
            } else {
                $data['content'] = '404';
            }
        } else if($page == 'detail-perusahaan') {
            $data['content'] = 'siswa/detail-perusahaan';
        } else if($page == 'pengajuan') {
            $data['content'] = 'siswa/pengajuan';
        } else if($page == 'pengajuan-sukses') {
            $data['content'] = 'siswa/pengajuan-sukses';
        } else if($page == 'buat-pengajuan') {
            // Check if student already has pending application
            $userdata = $this->session->userdata('userdata');
            $this->db->where('user_id', $userdata['id']);
            $siswa = $this->db->get('tb_siswa')->row();
            
            // If student already has a pending application (not rejected or draft), redirect to pengajuan-sukses
            if($siswa && $siswa->status_pengajuan == 'menunggu'){
                redirect('siswa/view/pengajuan-sukses');
                return;
            }
            // Allow access if student has been rejected or has draft, or no record exists yet
            $data['content'] = 'siswa/buat-pengajuan';
        } else if($page == 'presensi' || $page == 'logbook') {
            // Validasi eligibilitas siswa (RBV-015)
            $this->load->library('rule_based_validation');
            $userdata = $this->session->userdata('userdata');
            $check = $this->rule_based_validation->validate_siswa_eligibility($userdata['id']);
            
            if(!$check['is_allowed']) {
                $this->session->set_flashdata('error_message', $check['message']);
                redirect('siswa/view/home');
                return;
            }
            if($page == 'logbook') {
                $presensi_hari_ini = $this->db
                    ->where('siswa_id', $check['siswa']->siswa_id)
                    ->where('tanggal', date('Y-m-d'))
                    ->get('tb_presensi')->row();
                if(!$presensi_hari_ini || empty($presensi_hari_ini->jam_masuk) || empty($presensi_hari_ini->jam_pulang)) {
                    $this->session->set_flashdata('error_message', 'Logbook harian dapat diakses setelah presensi masuk dan pulang selesai.');
                    redirect('siswa/view/home');
                    return;
                }
            }
            $data['siswa'] = $check['siswa'];
            $data['content'] = 'siswa/' . $page;

        } else if($page == 'riwayat-presensi') {
            // Riwayat presensi — hanya milik siswa sendiri (anti-IDOR)
            $userdata = $this->session->userdata('userdata');
            $siswa = get_siswa_by_user_id($userdata['id']);
            if(!$siswa) {
                $this->session->set_flashdata('error_message', 'Data siswa tidak ditemukan.');
                redirect('siswa/view/home');
                return;
            }
            if($siswa->status_pengajuan != 'selesai') {
                $this->session->set_flashdata('error_message', 'Nilai dan rekapitulasi hanya dapat dilihat setelah PKL selesai.');
                redirect('siswa/view/home');
                return;
            }
            $this->load->model('M_presensi');
            $data['siswa']           = $siswa;
            $data['riwayat_presensi'] = $this->M_presensi->get_riwayat_presensi_siswa($siswa->siswa_id, 100);
            $data['statistik']       = $this->M_presensi->get_statistik_presensi_siswa($siswa->siswa_id);
            $data['content']         = 'siswa/riwayat-presensi';

        } else if($page == 'riwayat-logbook') {
            // Riwayat logbook — hanya milik siswa sendiri (anti-IDOR)
            $userdata = $this->session->userdata('userdata');
            $siswa = get_siswa_by_user_id($userdata['id']);
            if(!$siswa) {
                $this->session->set_flashdata('error_message', 'Data siswa tidak ditemukan.');
                redirect('siswa/view/home');
                return;
            }
            $this->load->model('M_logbook');
            $data['siswa']           = $siswa;
            $data['riwayat_logbook'] = $this->M_logbook->get_riwayat_logbook_siswa($siswa->siswa_id, 100);
            $data['statistik_lb']    = $this->M_logbook->get_statistik_logbook_siswa($siswa->siswa_id);
            $data['content']         = 'siswa/riwayat-logbook';

        } else if($page == 'catatan-pembimbing') {
            // Catatan pembimbing — hanya milik siswa sendiri (anti-IDOR)
            $userdata = $this->session->userdata('userdata');
            $siswa = get_siswa_by_user_id($userdata['id']);
            if(!$siswa) {
                $this->session->set_flashdata('error_message', 'Data siswa tidak ditemukan.');
                redirect('siswa/view/home');
                return;
            }
            $this->load->model('M_logbook');
            $this->load->model('M_pengelompokan');
            $data['siswa']            = $siswa;
            $data['logbook_list']     = $this->M_logbook->get_riwayat_logbook_siswa($siswa->siswa_id, 100);
            $data['pembimbing_info']  = $this->M_pengelompokan->get_pembimbing_by_siswa($siswa->siswa_id);
            $data['content']          = 'siswa/catatan-pembimbing';

        } else if($page == 'rekap' || $page == 'nilai' || $page == 'statistik-nilai') {
            // Rekap & Nilai — hanya data siswa sendiri (anti-IDOR)
            $userdata = $this->session->userdata('userdata');
            $siswa = get_siswa_by_user_id($userdata['id']);
            if(!$siswa) {
                $this->session->set_flashdata('error_message', 'Data siswa tidak ditemukan.');
                redirect('siswa/view/home');
                return;
            }
            if($siswa->status_pengajuan != 'selesai') {
                $this->session->set_flashdata('error_message', 'Nilai dan rekapitulasi hanya dapat dilihat setelah PKL selesai.');
                redirect('siswa/view/home');
                return;
            }
            $this->load->model('M_presensi');
            $this->load->model('M_logbook');
            $this->load->model('M_penilaian');
            $this->load->model('M_pengelompokan');
            $this->load->library('rule_based_validation');

            // Sync nilai terbaru
            $this->rule_based_validation->sync_penilaian_siswa($siswa->siswa_id);

            // Ambil data DUDI
            $dudi = null;
            if($siswa->dudi_id) {
                $this->db->where('dudi_id', $siswa->dudi_id);
                $dudi = $this->db->get('tb_dudi')->row();
            }

            // Ambil data Pembimbing
            $pembimbing_info = $this->M_pengelompokan->get_pembimbing_by_siswa($siswa->siswa_id);

            $data['siswa']            = $siswa;
            $data['dudi']             = $dudi;
            $data['pembimbing_info']  = $pembimbing_info;
            $data['statistik_pre']    = $this->M_presensi->get_statistik_presensi_siswa($siswa->siswa_id);
            $data['statistik_lb']     = $this->M_logbook->get_statistik_logbook_siswa($siswa->siswa_id);
            $data['penilaian']        = $this->M_penilaian->get_penilaian_by_siswa($siswa->siswa_id);
            $data['riwayat_presensi'] = $this->M_presensi->get_riwayat_presensi_siswa($siswa->siswa_id, 20);
            $data['riwayat_logbook']  = $this->M_logbook->get_riwayat_logbook_siswa($siswa->siswa_id, 20);
            $data['content']          = 'siswa/rekap';


        } else if($page == 'penempatan') {
            // Data penempatan — hanya milik siswa sendiri (anti-IDOR)
            $userdata = $this->session->userdata('userdata');
            $siswa = get_siswa_by_user_id($userdata['id']);
            if(!$siswa) {
                $this->session->set_flashdata('error_message', 'Data siswa tidak ditemukan.');
                redirect('siswa/view/home');
                return;
            }
            $this->load->model('M_pengelompokan');
            $dudi = null;
            if($siswa->dudi_id) {
                $this->db->where('dudi_id', $siswa->dudi_id);
                $dudi = $this->db->get('tb_dudi')->row();
            }
            $data['siswa']           = $siswa;
            $data['dudi']            = $dudi;
            $data['pembimbing_info'] = $this->M_pengelompokan->get_pembimbing_by_siswa($siswa->siswa_id);
            $data['content']         = 'siswa/penempatan';
        }else{
            // Use simplified home page
            if($page == 'home'){
                $data['content'] = 'siswa/home';
            } else {
                $data['content'] = 'siswa/' . $page;
            }
        }
        
        // Load helpers
        $this->load->helper('text');
        
        // Load pagination library for data tables
        // (pengajuan page removed - pagination no longer needed)
        
        // Load user data for profile page
        if($page == 'profile'){
            $this->load->model('M_user');
            $userdata = $this->session->userdata('userdata');
            $data['user'] = $this->M_user->get_user_by_id($userdata['id']);
            
            // Load student data
            $this->db->where('user_id', $userdata['id']);
            $data['siswa'] = $this->db->get('tb_siswa')->row();
        }
        
        // Load QR generator for ID card page
        if($page == 'id-card'){
            $this->load->library('qr_generator');
            
            // Fetch required data for the view
            $userdata = $this->session->userdata('userdata');
            $this->db->select('s.*, u.nama_lengkap, u.foto_profil, d.dudi_nama, d.dudi_id');
            $this->db->from('tb_siswa s');
            $this->db->join('tb_user u', 'u.id = s.user_id', 'left');
            $this->db->join('tb_dudi d', 'd.dudi_id = s.dudi_id', 'left');
            $this->db->where('s.user_id', $userdata['id']);
            $siswa = $this->db->get()->row();
            
            // Generate QR code after student data is fetched
            $data['qr_code_url'] = $this->qr_generator->generate_student_qr($siswa);
            
            if($siswa){
                // Pre-process data for faster view rendering
                $siswa->foto_profil_url = null;
                if($siswa->user_id && $siswa->foto_profil && file_exists('./uploads/profil/'.$siswa->foto_profil)){
                    $siswa->foto_profil_url = base_url('uploads/profil/'.$siswa->foto_profil);
                }
                $data['siswa'] = $siswa;
            }
            
            // Load ID card as standalone page without template wrapper
            $this->load->view('siswa/id-card', $data, FALSE);
            return; // Exit early to prevent loading template
        }
        
        // Load data for kontak pembimbing page
        if($page == 'kontak-pembimbing'){
            $this->load->model('M_pengelompokan');
            $this->load->model('M_pembimbing');
            $userdata = $this->session->userdata('userdata');
            
            // Get student data
            $this->db->where('user_id', $userdata['id']);
            $siswa = $this->db->get('tb_siswa')->row();
            
            if($siswa){
                // Get pembimbing assigned to this student
                $this->db->select('tb_pengelompokan.pembimbing_id');
                $this->db->from('tb_pengelompokan');
                $this->db->where('tb_pengelompokan.siswa_id', $siswa->siswa_id);
                $pengelompokan = $this->db->get()->row();
                
                if($pengelompokan){
                    // Get pembimbing data (no longer joined with user table)
                    $pembimbing = $this->M_pembimbing->get_pembimbing_by_id($pengelompokan->pembimbing_id);
                    
                    if($pembimbing){
                        $data['pembimbing'] = $pembimbing;
                    } else {
                        $data['pembimbing'] = null;
                    }
                } else {
                    $data['pembimbing'] = null;
                }
            } else {
                $data['pembimbing'] = null;
            }
        }
        
        // Load data for home page (supervisor information)
        if($page == 'home'){
            $this->load->model('M_pengelompokan');
            
            // Check if mentor assignment notification has already been shown
            $userdata = $this->session->userdata('userdata');
            $this->db->where('user_id', $userdata['id']);
            $siswa = $this->db->get('tb_siswa')->row();
            
            if($siswa && $siswa->status_pengajuan == 'disetujui') {
                $pembimbing_info = $this->M_pengelompokan->get_pembimbing_by_siswa($siswa->siswa_id);
                
                // If mentor is assigned and hasn't been notified yet ever (not just this session)
                // Check if the property exists to avoid undefined property error
                $mentorNotified = isset($siswa->mentor_assignment_notified) ? $siswa->mentor_assignment_notified : 0;
                if($pembimbing_info && !$mentorNotified) {
                    // Check if the column exists before updating to avoid database errors
                    $columns_query = $this->db->query("SHOW COLUMNS FROM tb_siswa LIKE 'mentor_assignment_notified'");
                    if($columns_query->num_rows() > 0) {
                        // Update the database to mark that the notification has been shown
                        $this->db->where('siswa_id', $siswa->siswa_id);
                        $this->db->update('tb_siswa', array('mentor_assignment_notified' => 1));
                    }
                    
                    // Set message to be displayed in the view
                    $pembimbing_nama = isset($pembimbing_info->user_nama) && $pembimbing_info->user_nama ? 
                                      $pembimbing_info->user_nama : 
                                      (isset($pembimbing_info->pembimbing_nama) ? $pembimbing_info->pembimbing_nama : 'Pembimbing');
                    
                    $notification_html = '
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>Selamat!</strong> Anda telah ditugaskan pembimbing. 
                        Guru pembimbing Anda adalah <strong>'.$pembimbing_nama.'</strong>.
                        <a href="'.base_url('siswa/view/kontak-pembimbing').'" class="alert-link ms-2">Lihat kontak pembimbing</a>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
                    
                    $data['mentor_assignment_notification'] = $notification_html;
                }
            }
        }
        
        // Load data for penempatan page
        if($page == 'penempatan'){
            $this->load->model('M_pengelompokan');
        }
        

        
        // Load template khusus untuk siswa (mobile-friendly)
        $this->load->view('template_siswa', $data, FALSE);
    }
    
    // Tampilkan form detail perusahaan untuk siswa yang memilih "other"
    function detail_perusahaan()
    {
        $data['content'] = 'siswa/detail-perusahaan';
        $this->load->view('template_siswa', $data, FALSE);
    }
    
    // Simpan detail perusahaan dari siswa
    function simpan_detail_perusahaan()
    {
        $status = true;
        $userdata = $this->session->userdata('userdata');
        
        // Validasi input
        $this->form_validation->set_rules('dudi_alamat', 'Alamat', 'required');
        
        if ($this->form_validation->run() == FALSE) {
            $status = false;
        }
        
        if($status){
            // Get student data
            $this->db->where('user_id', $userdata['id']);
            $siswa = $this->db->get('tb_siswa')->row();
            
            if(!$siswa || !$siswa->other_dudi_nama){
                $this->session->set_flashdata('error_message', 'Data tidak valid');
                redirect('siswa/view/home');
            }
            
            // Prepare company data
            $dudi_data = array(
                'dudi_nama' => $siswa->other_dudi_nama,
                'dudi_telepon' => $this->input->post('dudi_telepon'),
                'dudi_alamat' => $this->input->post('dudi_alamat'),
                'dudi_email' => $this->input->post('dudi_email'),
                'dudi_pic' => $this->input->post('dudi_pic'),
                'dudi_nip_pic' => $this->input->post('dudi_nip_pic'),
                'dudi_instruktur' => $this->input->post('dudi_instruktur'),
                'dudi_nip_instruktur' => $this->input->post('dudi_nip_instruktur'),
                'dudi_code' => 'dudi_' . time(),
                'is_mitra' => 0,  // Student-added companies are non-mitra by default
                'status_kerjasama' => 'non_mitra',  // Consistent status
                'sumber_data' => 'siswa'  // Mark as student-submitted
            );
            
            // Insert company data
            $this->db->insert('tb_dudi', $dudi_data);
            $dudi_id = $this->db->insert_id();
            
            // Update student with new company ID
            $siswa_update = array(
                'dudi_id' => $dudi_id,
                'other_dudi_nama' => null
            );
            
            $this->db->where('siswa_id', $siswa->siswa_id);
            $this->db->update('tb_siswa', $siswa_update);
            
            $this->session->set_flashdata('message', 'Detail perusahaan berhasil disimpan');
            redirect('siswa/view/home');
        } else {
            $this->detail_perusahaan();
        }
    }
    
    // Pengajuan PKL
    function buat_pengajuan()
    {
        $status = true;
        $userdata = $this->session->userdata('userdata');

        // Support existing installations that have not run the schema update yet.
        if(!$this->db->field_exists('other_dudi_latitude', 'tb_siswa')) {
            $this->db->query('ALTER TABLE `tb_siswa` ADD COLUMN `other_dudi_latitude` DECIMAL(10,8) DEFAULT NULL');
        }
        if(!$this->db->field_exists('other_dudi_longitude', 'tb_siswa')) {
            $this->db->query('ALTER TABLE `tb_siswa` ADD COLUMN `other_dudi_longitude` DECIMAL(11,8) DEFAULT NULL');
        }
        
        $this->form_validation->set_rules('siswa_kelas', 'Kelas', 'required');
        $this->form_validation->set_rules('siswa_telepon', 'Telepon', 'required|callback_valid_phone_number');
        $this->form_validation->set_rules('siswa_alamat', 'Alamat', 'required');
        $this->form_validation->set_rules('dudi_id', 'Perusahaan', 'required');
        $this->form_validation->set_rules('periode', 'Periode PKL', 'required');
        
        // Validasi tambahan untuk perusahaan lainnya
        $dudi_id = $this->input->post('dudi_id');
        if($dudi_id == 'other'){
            $this->form_validation->set_rules('other_dudi_nama', 'Nama Perusahaan Lainnya', 'required');
            $this->form_validation->set_rules('other_dudi_latitude', 'Latitude Perusahaan', 'required|decimal|callback_valid_latitude');
            $this->form_validation->set_rules('other_dudi_longitude', 'Longitude Perusahaan', 'required|decimal|callback_valid_longitude');
        }
        
        if ($this->form_validation->run() == FALSE) {
            $status = false;
        }

        if($status){
            $this->load->model('M_siswa');
            $code = $this->M_siswa->generateRandomString(30);
            
            // Cek apakah siswa sudah punya data
            $this->db->where('user_id', $userdata['id']);
            $cek_siswa = $this->db->get('tb_siswa')->row();
            
            // Logging untuk debugging
            log_message('debug', 'Pengajuan PKL: cek_siswa=' . print_r($cek_siswa, true));
            log_message('debug', 'Pengajuan PKL: status_pengajuan=' . ($cek_siswa ? $cek_siswa->status_pengajuan : 'null'));
            
            // Persiapkan data
            $data = array(
                'siswa_kelas' => $this->input->post('siswa_kelas'),
                'siswa_telepon' => $this->input->post('siswa_telepon'),
                'siswa_alamat' => $this->input->post('siswa_alamat'),
                'periode' => $this->input->post('periode'),
                'status_pengajuan' => 'menunggu',
                'updated_at' => date('Y-m-d H:i:s')
            );
            
            // Tangani pilihan perusahaan
            if($dudi_id == 'other'){
                // Jika memilih "Lainnya", simpan nama perusahaan di field khusus
                $data['other_dudi_nama'] = $this->input->post('other_dudi_nama');
                $data['dudi_id'] = NULL; // Set dudi_id ke NULL karena belum ada di database
                
                // Simpan data lengkap perusahaan yang tidak terdaftar
                $data['other_dudi_alamat'] = $this->input->post('other_dudi_alamat');
                $data['other_dudi_telepon'] = $this->input->post('other_dudi_telepon');
                $data['other_dudi_email'] = $this->input->post('other_dudi_email');
                $data['other_dudi_pic'] = $this->input->post('other_dudi_pic');
                $data['other_dudi_nip_pic'] = $this->input->post('other_dudi_nip_pic');
                $data['other_dudi_instruktur'] = $this->input->post('other_dudi_instruktur');
                $data['other_dudi_nip_instruktur'] = $this->input->post('other_dudi_nip_instruktur');
                $data['other_dudi_latitude'] = $this->input->post('other_dudi_latitude');
                $data['other_dudi_longitude'] = $this->input->post('other_dudi_longitude');
            } else {
                // Jika memilih perusahaan yang sudah ada
                $data['dudi_id'] = $dudi_id;
            }
            
            // Handle upload surat permohonan/pernyataan
            if(!empty($_FILES['surat_permohonan']['name'])){
                $config['upload_path'] = './uploads/pengajuan/';
                $config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|doc|docx|xls|xlsx|ppt|pptx';
                $config['max_size'] = 5120; // 5MB
                $config['file_name'] = 'surat_permohonan_'.$userdata['id'].'_'.time();
                
                // Buat folder jika belum ada
                if(!is_dir($config['upload_path'])){
                    mkdir($config['upload_path'], 0777, true);
                }
                
                $this->load->library('upload', $config);
                
                if($this->upload->do_upload('surat_permohonan')){
                    $upload_data = $this->upload->data();
                    // Hapus file lama jika ada
                    if($cek_siswa && $cek_siswa->surat_permohonan && file_exists('./uploads/pengajuan/'.$cek_siswa->surat_permohonan)){
                        unlink('./uploads/pengajuan/'.$cek_siswa->surat_permohonan);
                    }
                    $data['surat_permohonan'] = $upload_data['file_name'];
                } else {
                    $this->session->set_flashdata('error_message', 'Gagal upload surat permohonan: ' . $this->upload->display_errors('', ''));
                    redirect('siswa/view/buat-pengajuan');
                }
            }
            
            // Sinkronkan juga telepon dan alamat ke tb_user
            $this->M_user->update_user_by_id(array(
                'telepon'    => $this->input->post('siswa_telepon'),
                'alamat'     => $this->input->post('siswa_alamat'),
                'updated_at' => date('Y-m-d H:i:s')
            ), $userdata['id']);

            if($cek_siswa && $cek_siswa->status_pengajuan != 'disetujui'){
                // Update existing - bisa ajukan ulang jika ditolak atau draft
                log_message('debug', 'Pengajuan PKL: Updating existing record');
                $this->M_siswa->update_siswa($data, $cek_siswa->siswa_code);
                $this->session->set_flashdata('message', 'Pengajuan PKL berhasil diperbarui dan dikirim');
            } else {
                // Create new - untuk pengajuan pertama atau ajukan ulang setelah disetujui
                // Validasi bahwa user_id benar-benar ada di tb_user
                $this->db->where('id', $userdata['id']);
                $user_db = $this->db->get('tb_user')->row();
                if(!$user_db){
                    $this->session->unset_userdata('userdata');
                    $this->session->set_flashdata('message', '<div class="alert alert-warning">Akun pengguna tidak ditemukan. Silakan login kembali.</div>');
                    redirect('auth/login_siswa');
                    return;
                }
                
                $data['user_id'] = $userdata['id'];
                $data['siswa_nama'] = $user_db->nama_lengkap ?: $userdata['nama_lengkap'];
                $data['siswa_code'] = $code;
                $data['siswa_nis'] = !empty($user_db->nip_nim) ? $user_db->nip_nim : (isset($userdata['nip_nim']) ? $userdata['nip_nim'] : null);
                unset($data['updated_at']); // Remove updated_at for new record
                $this->M_siswa->tambah_data_siswa($data);
                $this->session->set_flashdata('message', 'Pengajuan PKL baru berhasil dibuat dan dikirim');
            }
            
            redirect('siswa/view/pengajuan-sukses');
        } else {
            $this->view('buat-pengajuan');
        }
    }
    
    // Callback function to validate phone number
    public function valid_phone_number($phone)
    {
        // Allow digits, spaces, hyphens, parentheses, plus signs, and dots
        if (preg_match('/^[0-9\s\-\+\(\)\.]+$/', $phone)) {
            return TRUE;
        } else {
            $this->form_validation->set_message('valid_phone_number', 'Format {field} tidak valid. Gunakan hanya angka dan karakter seperti spasi, tanda hubung, kurung, plus, atau titik.');
            return FALSE;
        }
    }

    public function valid_latitude($latitude)
    {
        if(is_numeric($latitude) && $latitude >= -90 && $latitude <= 90) {
            return TRUE;
        }

        $this->form_validation->set_message('valid_latitude', 'Latitude harus berada di antara -90 dan 90.');
        return FALSE;
    }

    public function valid_longitude($longitude)
    {
        if(is_numeric($longitude) && $longitude >= -180 && $longitude <= 180) {
            return TRUE;
        }

        $this->form_validation->set_message('valid_longitude', 'Longitude harus berada di antara -180 dan 180.');
        return FALSE;
    }

    // Test method for file upload debugging
    function test_upload()
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            log_message('debug', 'Test upload POST data: ' . print_r($_POST, true));
            log_message('debug', 'Test upload FILES data: ' . print_r($_FILES, true));
            
            if(!empty($_FILES['test_file']['name'])) {
                $config['upload_path'] = './uploads/profil/';
                $config['allowed_types'] = 'jpg|jpeg|png|gif';
                $config['max_size'] = 2048;
                $config['file_name'] = 'test_' . time();
                
                $this->load->library('upload', $config);
                
                if($this->upload->do_upload('test_file')) {
                    $upload_data = $this->upload->data();
                    echo "Upload successful: " . $upload_data['file_name'];
                } else {
                    echo "Upload failed: " . $this->upload->display_errors();
                }
            } else {
                echo "No file uploaded";
            }
            return;
        }
        
        // Display test form
        echo '
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="test_file" accept="image/*">
            <button type="submit">Upload Test</button>
        </form>
        ';
    }
    
    function update_profile()
    {
        // Debug session data
        $userdata = $this->session->userdata('userdata');
        log_message('debug', 'Session userdata: ' . print_r($userdata, true));
        
        $status = true;
        $user_code = $userdata['id'];

        $this->load->model('M_user');
        $this->load->model('M_siswa');
        $user = $this->M_user->get_user_by_id($user_code);
        
        // Debug user data
        log_message('debug', 'User data: ' . print_r($user, true));
        
        if(!$user){
            $status = false;
            $this->session->set_flashdata('error_message', 'User tidak ditemukan');
        }

        // Validate required fields
        $this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'required');
        $this->form_validation->set_rules('siswa_kelas', 'Kelas', 'required');
        
        // Validate NIS/NISN uniqueness for student data
        $nis = $this->input->post('siswa_nis');
        $nisn = $this->input->post('siswa_nisn');
        
        if($nis) {
            // Get the current student record to exclude from uniqueness check
            $this->db->where('user_id', $user_code);
            $current_student = $this->db->get('tb_siswa')->row();
            if($current_student && isset($current_student->siswa_code)) {
                $this->form_validation->set_rules('siswa_nis', 'NIS', 'callback_nis_check['.$current_student->siswa_code.']');
            } else {
                // If no current record exists, just check for general uniqueness
                $this->form_validation->set_rules('siswa_nis', 'NIS', 'is_unique[tb_siswa.siswa_nis]');
            }
        }
        
        if($nisn) {
            // Get the current student record to exclude from uniqueness check
            $this->db->where('user_id', $user_code);
            $current_student = $this->db->get('tb_siswa')->row();
            if($current_student && isset($current_student->siswa_code)) {
                $this->form_validation->set_rules('siswa_nisn', 'NISN', 'callback_nisn_check['.$current_student->siswa_code.']');
            } else {
                // If no current record exists, just check for general uniqueness
                $this->form_validation->set_rules('siswa_nisn', 'NISN', 'is_unique[tb_siswa.siswa_nisn]');
            }
        }
        
        // Run validation but don't fail if only photo upload fails
        if ($this->form_validation->run() == FALSE) {
            log_message('debug', 'Form validation failed: ' . validation_errors());
            $status = false;
        } else {
            log_message('debug', 'Form validation passed');
        }
        
        // Additional validation for file upload
        // Debug: Check if file is received
        log_message('debug', 'FILES array: ' . print_r($_FILES, true));
        log_message('debug', 'POST data: ' . print_r($this->input->post(), true));
        
        if(!empty($_FILES['foto_profil']['name'])){
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            $file_type = $_FILES['foto_profil']['type'];
            $file_size = $_FILES['foto_profil']['size'];
            
            if(!in_array($file_type, $allowed_types)){
                $this->session->set_flashdata('error_message', 'Format file tidak didukung. Gunakan JPG, PNG, atau GIF.');
                $status = false;
            }
            
            if($file_size > 2097152){ // 2MB in bytes
                $this->session->set_flashdata('error_message', 'Ukuran file terlalu besar. Maksimal 2MB.');
                $status = false;
            }
        }

         if($status){
            // Update user data
            $jenis_kelamin = $this->input->post('jenis_kelamin');
            
            // Convert 'L'/'P' to 'Laki-laki'/'Perempuan' to match database ENUM in tb_user
            if ($jenis_kelamin === 'L') {
                $jenis_kelamin = 'Laki-laki';
            } elseif ($jenis_kelamin === 'P') {
                $jenis_kelamin = 'Perempuan';
            }
            
            $user_update = array(
                'nama_lengkap'  => $this->input->post('nama_lengkap'),
                'tempat_lahir'  => $this->input->post('tempat_lahir'),
                'tanggal_lahir' => $this->input->post('tanggal_lahir') ? $this->input->post('tanggal_lahir') : NULL,
                'jenis_kelamin' => $jenis_kelamin,
                'alamat'        => $this->input->post('alamat'),
                'email'         => $this->input->post('email'),
                'telepon'       => $this->input->post('telepon'),
                'nip_nim'       => $this->input->post('siswa_nis') ? $this->input->post('siswa_nis') : NULL,
                'updated_at'    => date('Y-m-d H:i:s')
            );

            // Handle upload foto
            if(!empty($_FILES['foto_profil']['name'])){
                log_message('debug', 'Starting file upload process');
                $config['upload_path'] = './uploads/profil/';
                $config['allowed_types'] = 'jpg|jpeg|png|gif';
                $config['max_size'] = 2048; // 2MB
                $config['file_name'] = 'profil_'.$user_code.'_'.time();
                $config['overwrite'] = FALSE;
                
                // Buat folder jika belum ada
                if(!is_dir($config['upload_path'])){
                    mkdir($config['upload_path'], 0777, true);
                }
                
                $this->load->library('upload', $config);
                
                if($this->upload->do_upload('foto_profil')){
                    $upload_data = $this->upload->data();
                    // Hapus foto lama jika ada
                    if(isset($user->foto_profil) && $user->foto_profil && file_exists('./uploads/profil/'.$user->foto_profil)){
                        unlink('./uploads/profil/'.$user->foto_profil);
                    }
                    $user_update['foto_profil'] = $upload_data['file_name'];
                } else {
                    $error = $this->upload->display_errors('', '');
                    log_message('debug', 'File upload failed: ' . $error);
                    $this->session->set_flashdata('error_message', 'Gagal upload foto: '.$error);
                }
            }

            // Update user table
            $this->M_user->update_user_by_id($user_update, $user_code);
            
            // Format JK untuk tb_siswa ('L' atau 'P')
            $siswa_jk = NULL;
            if ($jenis_kelamin === 'Laki-laki' || $jenis_kelamin === 'L') {
                $siswa_jk = 'L';
            } elseif ($jenis_kelamin === 'Perempuan' || $jenis_kelamin === 'P') {
                $siswa_jk = 'P';
            }

            // Get student data
            $this->db->where('user_id', $user_code);
            $siswa = $this->db->get('tb_siswa')->row();
            
            $siswa_payload = array(
                'siswa_nama'          => $this->input->post('nama_lengkap'),
                'siswa_nis'           => $this->input->post('siswa_nis') ? $this->input->post('siswa_nis') : NULL,
                'siswa_nisn'          => $this->input->post('siswa_nisn') ? $this->input->post('siswa_nisn') : NULL,
                'siswa_kelas'         => $this->input->post('siswa_kelas'),
                'siswa_jurusan'       => $this->input->post('siswa_jurusan'),
                'siswa_tempat_lahir'  => $this->input->post('tempat_lahir'),
                'siswa_tanggal_lahir' => $this->input->post('tanggal_lahir') ? $this->input->post('tanggal_lahir') : NULL,
                'siswa_jk'            => $siswa_jk,
                'siswa_telepon'       => $this->input->post('telepon'),
                'siswa_alamat'        => $this->input->post('alamat'),
                'updated_at'          => date('Y-m-d H:i:s')
            );

            if($siswa){
                $siswa_result = $this->M_siswa->update_siswa_by_user_id($siswa_payload, $user_code);
                if($siswa_result === false) {
                    $this->session->set_flashdata('error_message', 'Gagal mengupdate data siswa. Pastikan NIS/NISN tidak duplikat.');
                    redirect('siswa/view/profile');
                    return;
                }
            } else {
                $siswa_payload['user_id'] = $user_code;
                $siswa_payload['siswa_code'] = 'siswa_' . time() . '_' . rand(1000, 9999);
                $siswa_payload['status_pengajuan'] = 'draft';
                $siswa_payload['is_active'] = 1;
                $siswa_payload['created_at'] = date('Y-m-d H:i:s');
                
                $siswa_create_result = $this->M_siswa->tambah_data_siswa($siswa_payload);
                if($siswa_create_result === false) {
                    $this->session->set_flashdata('error_message', 'Gagal membuat data siswa. Pastikan NIS/NISN tidak duplikat.');
                    redirect('siswa/view/profile');
                    return;
                }
            }

            // Get updated user data
            $updated_user = $this->M_user->get_user_by_id($user_code);
            
            // Update session
            $user_login = array(
                'userdata' => array(
                    'id' => $userdata['id'],
                    'username' => $userdata['username'],
                    'nama_lengkap' => $user_update['nama_lengkap'],
                    'level' => $userdata['level'],
                    'group_name' => $userdata['group_name'],
                    'foto_profil' => isset($user_update['foto_profil']) ? $user_update['foto_profil'] : (isset($updated_user->foto_profil) ? $updated_user->foto_profil : null)
                ),
            );
            $this->session->set_userdata($user_login);
            
            $this->session->set_flashdata('message', 'Biodata profil berhasil diperbarui');
            redirect('siswa/view/profile');
        } else {
            $data['user'] = $user;
            $this->db->where('user_id', $user_code);
            $data['siswa'] = $this->db->get('tb_siswa')->row();
            $data['content'] = 'siswa/profile';
            $this->load->view('template_siswa', $data, FALSE);
        }
    }

    function change_password()
    {
        $status = true;
        $userdata = $this->session->userdata('userdata');
        $user_code = $userdata['id'];

        $this->load->model('M_user');
        $user = $this->M_user->get_user_by_id($user_code);
        
        if(!$user){
            $status = false;
            $this->session->set_flashdata('error_message', 'User tidak ditemukan');
        }

        $this->form_validation->set_rules('password_lama', 'Password Lama', 'required');
        $this->form_validation->set_rules('password_baru', 'Password Baru', 'required|min_length[6]');
        $this->form_validation->set_rules('password_konfirmasi', 'Konfirmasi Password', 'required|matches[password_baru]');
        
        if ($this->form_validation->run() == FALSE) {
            $status = false;
        }

        if($status){
            if(!password_verify($this->input->post('password_lama'), $user->password)){
                $this->session->set_flashdata('error_message', 'Password lama tidak sesuai');
                $status = false;
            }

            if($status){
                $hash = password_hash($this->input->post('password_baru'), PASSWORD_DEFAULT);
                $update = array('password' => $hash);
                $this->M_user->update_user_by_id($update, $user_code);
                
                $this->session->set_flashdata('message', 'Password berhasil diubah');
                redirect('siswa/view/change-password');
            }
        }

        $data['content'] = 'siswa/change-password';
        $this->load->view('template_siswa', $data, FALSE);
    }

    
    // Callback function to validate NIS uniqueness (excluding current record)
    public function nis_check($nis, $current_siswa_code)
    {
        if (empty($nis)) {
            return TRUE; // Skip validation if empty
        }
        
        $this->db->where('siswa_nis', $nis);
        $this->db->where('siswa_code !=', $current_siswa_code);
        $query = $this->db->get('tb_siswa');
        
        if ($query->num_rows() > 0) {
            $this->form_validation->set_message('nis_check', 'NIS sudah digunakan oleh siswa lain');
            return FALSE;
        }
        
        return TRUE;
    }
    
    // Callback function to validate NISN uniqueness (excluding current record)
    public function nisn_check($nisn, $current_siswa_code)
    {
        if (empty($nisn)) {
            return TRUE; // Skip validation if empty
        }
        
        $this->db->where('siswa_nisn', $nisn);
        $this->db->where('siswa_code !=', $current_siswa_code);
        $query = $this->db->get('tb_siswa');
        
        if ($query->num_rows() > 0) {
            $this->form_validation->set_message('nisn_check', 'NISN sudah digunakan oleh siswa lain');
            return FALSE;
        }
        
        return TRUE;
    }
    
    // API endpoint to check NIS availability
    function check_nis_availability()
    {
        // Only allow AJAX requests
        if(!$this->input->is_ajax_request()) {
            show_404();
            return;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $nis = isset($input['nis']) ? trim($input['nis']) : '';
        $current_nis = isset($input['current_nis']) ? trim($input['current_nis']) : '';
        
        $response = array(
            'available' => false,
            'message' => ''
        );
        
        // Validate input
        if(empty($nis)) {
            $response['message'] = 'NIS tidak boleh kosong';
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
            return;
        }
        
        // Validate NIS format
        if(!preg_match('/^[0-9]{10,15}$/', $nis)) {
            $response['message'] = 'NIS harus berupa angka 10-15 digit';
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
            return;
        }
        
        // Check if NIS is the same as current user's NIS
        if($nis === $current_nis) {
            $response['available'] = true;
            $response['message'] = 'NIS ini milik Anda';
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
            return;
        }
        
        // Check if NIS already exists in database
        $this->db->where('siswa_nis', $nis);
        $existing_siswa = $this->db->get('tb_siswa')->row();
        
        if($existing_siswa) {
            $response['message'] = 'NIS sudah digunakan oleh siswa lain (' . $existing_siswa->siswa_nama . ' dari kelas ' . $existing_siswa->siswa_kelas . ')';
        } else {
            $response['available'] = true;
            $response['message'] = 'NIS tersedia';
        }
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }
    
    // API endpoint to check NISN availability
    function check_nisn_availability()
    {
        // Only allow AJAX requests
        if(!$this->input->is_ajax_request()) {
            show_404();
            return;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $nisn = isset($input['nisn']) ? trim($input['nisn']) : '';
        $current_nisn = isset($input['current_nisn']) ? trim($input['current_nisn']) : '';
        
        $response = array(
            'available' => false,
            'message' => ''
        );
        
        // Validate input
        if(empty($nisn)) {
            $response['message'] = 'NISN tidak boleh kosong';
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
            return;
        }
        
        // Validate NISN format
        if(!preg_match('/^[0-9]{10}$/', $nisn)) {
            $response['message'] = 'NISN harus berupa angka 10 digit';
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
            return;
        }
        
        // Check if NISN is the same as current user's NISN
        if($nisn === $current_nisn) {
            $response['available'] = true;
            $response['message'] = 'NISN ini milik Anda';
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
            return;
        }
        
        // Check if NISN already exists in database
        $this->db->where('siswa_nisn', $nisn);
        $existing_siswa = $this->db->get('tb_siswa')->row();
        
        if($existing_siswa) {
            $response['message'] = 'NISN sudah digunakan oleh siswa lain (' . $existing_siswa->siswa_nama . ' dari kelas ' . $existing_siswa->siswa_kelas . ')';
        } else {
            $response['available'] = true;
            $response['message'] = 'NISN tersedia';
        }
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }
    
    // ==========================================
    // MODUL PRESENSI SISWA (RULE-BASED VALIDATION)
    // ==========================================

    /**
     * Submit Presensi Masuk Siswa
     * Menerapkan Rule: RBV-001, RBV-003, RBV-004, RBV-005, RBV-006, RBV-012
     */
    public function do_presensi_masuk()
    {
        $userdata = $this->session->userdata('userdata');
        $this->load->library('rule_based_validation');
        $this->load->model('M_presensi');

        // Validasi hak akses & status siswa (RBV-015)
        $eligibility = $this->rule_based_validation->validate_siswa_eligibility($userdata['id']);
        if(!$eligibility['is_allowed']) {
            $this->session->set_flashdata('error_message', $eligibility['message']);
            redirect('siswa/view/home');
            return;
        }

        $siswa = $eligibility['siswa'];
        $tanggal = $this->input->post('tanggal') ? $this->input->post('tanggal') : date('Y-m-d');
        if($tanggal !== date('Y-m-d')) {
            $this->session->set_flashdata('error_message', 'Presensi masuk hanya dapat dilakukan untuk tanggal hari ini.');
            redirect('siswa/view/home');
            return;
        }
        $status_kehadiran = $this->input->post('status_kehadiran') ? $this->input->post('status_kehadiran') : 'hadir';
        $keterangan_masuk = $this->input->post('keterangan_masuk');
        $jam_masuk = date('H:i:s');
        if (trim((string) $keterangan_masuk) === '') {
            $keterangan_masuk = 'Presensi masuk ' . $siswa->siswa_nama . ' pada pukul ' . $jam_masuk . ' di tempat PKL.';
        }

        // Koordinat Geolocation dari peramban client
        $latitude_siswa  = $this->input->post('latitude_siswa') !== '' ? $this->input->post('latitude_siswa') : null;
        $longitude_siswa = $this->input->post('longitude_siswa') !== '' ? $this->input->post('longitude_siswa') : null;

        // Pre-validation awal (duplikasi, parameter dasar, geolokasi & radius) sebelum I/O upload file
        $pre_rule = $this->rule_based_validation->validate_presensi_masuk(
            $siswa->siswa_id,
            $tanggal,
            ['status_kehadiran' => $status_kehadiran, 'jam_masuk' => $jam_masuk, 'keterangan_masuk' => $keterangan_masuk],
            null,
            $latitude_siswa,
            $longitude_siswa
        );

        if (!$pre_rule['is_valid'] && $pre_rule['rule_id'] !== 'RBV-004') {
            $this->session->set_flashdata('error_message', '[' . $pre_rule['rule_id'] . '] ' . $pre_rule['message']);
            redirect('siswa/view/home');
            return;
        }

        // Handle upload foto bukti presensi masuk
        $foto_masuk_name = null;
        $upload_path = FCPATH . 'uploads/presensi/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0777, true);
        }

        if (!empty($_FILES['foto_masuk']['name'])) {
            $config['upload_path']   = $upload_path;
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['max_size']      = 2048; // 2MB
            $config['file_name']     = 'masuk_' . $siswa->siswa_id . '_' . date('Ymd_His');
            $config['overwrite']     = false;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            if ($this->upload->do_upload('foto_masuk')) {
                $upload_data = $this->upload->data();
                $foto_masuk_name = $upload_data['file_name'];
            } else {
                $this->session->set_flashdata('error_message', 'Gagal upload foto masuk: ' . $this->upload->display_errors('', ''));
                redirect('siswa/view/home');
                return;
            }
        }

        // Eksekusi Validasi Lengkap Rule-Based Validation
        $rule_check = $this->rule_based_validation->validate_presensi_masuk(
            $siswa->siswa_id,
            $tanggal,
            ['status_kehadiran' => $status_kehadiran, 'jam_masuk' => $jam_masuk, 'keterangan_masuk' => $keterangan_masuk],
            $foto_masuk_name,
            $latitude_siswa,
            $longitude_siswa
        );

        if (!$rule_check['is_valid']) {
            if ($foto_masuk_name && file_exists($upload_path . $foto_masuk_name)) {
                unlink($upload_path . $foto_masuk_name);
            }
            $this->session->set_flashdata('error_message', '[' . $rule_check['rule_id'] . '] ' . $rule_check['message']);
            redirect('siswa/view/home');
            return;
        }

        // Ambil data audit radius geolokasi
        $geo_audit = $this->rule_based_validation->validate_geolocation($siswa->siswa_id, $latitude_siswa, $longitude_siswa, $status_kehadiran);

        // Simpan Data Presensi Masuk beserta Audit Geolocation
        $insert_data = [
            'siswa_id'         => $siswa->siswa_id,
            'tanggal'          => $tanggal,
            'jam_masuk'        => $jam_masuk,
            'foto_masuk'       => $foto_masuk_name,
            'keterangan_masuk' => $keterangan_masuk,
            'status_kehadiran' => $status_kehadiran,
            'status_validasi'  => 'valid',
            'alasan_penolakan' => null,
            'latitude_siswa'   => $latitude_siswa,
            'longitude_siswa'  => $longitude_siswa,
            'latitude_dudi'    => isset($geo_audit['latitude_dudi']) ? $geo_audit['latitude_dudi'] : null,
            'longitude_dudi'   => isset($geo_audit['longitude_dudi']) ? $geo_audit['longitude_dudi'] : null,
            'jarak'            => isset($geo_audit['jarak']) ? $geo_audit['jarak'] : null,
            'radius'           => isset($geo_audit['radius']) ? $geo_audit['radius'] : null,
            'status_lokasi'    => isset($geo_audit['status_lokasi']) ? $geo_audit['status_lokasi'] : 'valid',
            'created_at'       => date('Y-m-d H:i:s')
        ];

        $saved = $this->M_presensi->simpan_presensi_masuk($insert_data);
        if ($saved) {
            // Sinkronisasi modul penilaian & evaluasi siswa
            $this->rule_based_validation->sync_penilaian_siswa($siswa->siswa_id);

            $pesan_sukses = 'Presensi masuk berhasil dicatat pada pukul ' . $jam_masuk . ' (Status: Valid).';
            if (isset($geo_audit['jarak']) && $geo_audit['jarak'] !== null) {
                $pesan_sukses .= ' Lokasi terverifikasi dalam radius ' . $geo_audit['radius'] . 'm (Jarak aktual: ' . round($geo_audit['jarak']) . 'm).';
            }
            $this->session->set_flashdata('message', $pesan_sukses);
        } else {
            $this->session->set_flashdata('error_message', 'Terjadi kesalahan sistem saat menyimpan presensi masuk.');
        }

        redirect('siswa/view/home');
    }

    /**
     * Submit Presensi Pulang Siswa
     * Menerapkan Rule: RBV-002, RBV-003, RBV-004, RBV-005, RBV-006, RBV-013
     */
    public function do_presensi_pulang()
    {
        $userdata = $this->session->userdata('userdata');
        $this->load->library('rule_based_validation');
        $this->load->model('M_presensi');

        // Validasi hak akses & status siswa (RBV-015)
        $eligibility = $this->rule_based_validation->validate_siswa_eligibility($userdata['id']);
        if(!$eligibility['is_allowed']) {
            $this->session->set_flashdata('error_message', $eligibility['message']);
            redirect('siswa/view/home');
            return;
        }

        $siswa = $eligibility['siswa'];
        $tanggal = $this->input->post('tanggal') ? $this->input->post('tanggal') : date('Y-m-d');
        if($tanggal !== date('Y-m-d')) {
            $this->session->set_flashdata('error_message', 'Presensi pulang hanya dapat dilakukan untuk tanggal hari ini.');
            redirect('siswa/view/home');
            return;
        }
        $keterangan_pulang = $this->input->post('keterangan_pulang');
        $jam_pulang = date('H:i:s');
        if (trim((string) $keterangan_pulang) === '') {
            $keterangan_pulang = 'Presensi pulang ' . $siswa->siswa_nama . ' pada pukul ' . $jam_pulang . ' setelah kegiatan PKL selesai.';
        }

        $latitude_siswa  = $this->input->post('latitude_siswa') !== '' ? $this->input->post('latitude_siswa') : null;
        $longitude_siswa = $this->input->post('longitude_siswa') !== '' ? $this->input->post('longitude_siswa') : null;

        // Pre-validation presensi pulang (RBV-002, RBV-006, RBV-013) sebelum upload
        $pre_rule = $this->rule_based_validation->validate_presensi_pulang(
            $siswa->siswa_id,
            $tanggal,
            ['jam_pulang' => $jam_pulang, 'keterangan_pulang' => $keterangan_pulang],
            null,
            $latitude_siswa,
            $longitude_siswa
        );

        if (!$pre_rule['is_valid'] && $pre_rule['rule_id'] !== 'RBV-004') {
            $this->session->set_flashdata('error_message', '[' . $pre_rule['rule_id'] . '] ' . $pre_rule['message']);
            redirect('siswa/view/home');
            return;
        }

        if ($pre_rule['is_valid'] && !empty($pre_rule['presensi_row'])) {
            $baseline = $pre_rule['presensi_row'];
            $integrity = $this->rule_based_validation->validate_location_integrity(
                $latitude_siswa,
                $longitude_siswa,
                $baseline->latitude_siswa,
                $baseline->longitude_siswa,
                $baseline->updated_at
            );
            if (!$integrity['is_valid']) {
                $this->db->where('presensi_id', $baseline->presensi_id)->update('tb_presensi', [
                    'status_validasi' => 'tidak_valid',
                    'status_lokasi' => 'tidak_valid',
                    'alasan_penolakan' => $integrity['message'],
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                $this->rule_based_validation->sync_penilaian_siswa($siswa->siswa_id);
                $this->session->set_flashdata('error_message', '[' . $integrity['rule_id'] . '] ' . $integrity['message']);
                redirect('siswa/view/home');
                return;
            }
        }

        // Upload Foto Pulang
        $foto_pulang_name = null;
        $upload_path = FCPATH . 'uploads/presensi/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0777, true);
        }

        if (!empty($_FILES['foto_pulang']['name'])) {
            $config['upload_path']   = $upload_path;
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['max_size']      = 2048; // 2MB
            $config['file_name']     = 'pulang_' . $siswa->siswa_id . '_' . date('Ymd_His');
            $config['overwrite']     = false;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            if ($this->upload->do_upload('foto_pulang')) {
                $upload_data = $this->upload->data();
                $foto_pulang_name = $upload_data['file_name'];
            } else {
                $this->session->set_flashdata('error_message', 'Gagal upload foto pulang: ' . $this->upload->display_errors('', ''));
                redirect('siswa/view/home');
                return;
            }
        }

        // Eksekusi Algoritma Rule-Based Validation untuk Presensi Pulang
        $rule_check = $this->rule_based_validation->validate_presensi_pulang(
            $siswa->siswa_id,
            $tanggal,
            ['jam_pulang' => $jam_pulang, 'keterangan_pulang' => $keterangan_pulang],
            $foto_pulang_name,
            $latitude_siswa,
            $longitude_siswa
        );

        if (!$rule_check['is_valid']) {
            if ($foto_pulang_name && file_exists($upload_path . $foto_pulang_name)) {
                unlink($upload_path . $foto_pulang_name);
            }
            $this->session->set_flashdata('error_message', '[' . $rule_check['rule_id'] . '] ' . $rule_check['message']);
            redirect('siswa/view/home');
            return;
        }

        $presensi_row = $rule_check['presensi_row'];

        // Update Data Presensi Pulang
        $update_data = [
            'jam_pulang'        => $jam_pulang,
            'foto_pulang'       => $foto_pulang_name,
            'keterangan_pulang' => $keterangan_pulang,
            'updated_at'        => date('Y-m-d H:i:s')
        ];

        $updated = $this->M_presensi->update_presensi_pulang($presensi_row->presensi_id, $update_data);
        if ($updated) {
            // Sinkronisasi nilai
            $this->rule_based_validation->sync_penilaian_siswa($siswa->siswa_id);
            $this->session->set_flashdata('message', 'Presensi pulang berhasil dicatat pada pukul ' . $jam_pulang . ' (Presensi Harian Lengkap).');
            $this->session->set_flashdata('show_logbook_link', true);
        } else {
            $this->session->set_flashdata('error_message', 'Presensi pulang untuk hari ini sudah dicatat dan tidak dapat diulang.');
        }

        redirect('siswa/view/home');
    }

    public function update_lokasi_realtime()
    {
        $this->output->set_content_type('application/json');
        $userdata = $this->session->userdata('userdata');
        $this->load->library('rule_based_validation');

        $eligibility = $this->rule_based_validation->validate_siswa_eligibility($userdata['id']);
        if (!$eligibility['is_allowed']) {
            $this->output->set_output(json_encode(['success' => false, 'message' => $eligibility['message']]));
            return;
        }

        $latitude = $this->input->post('latitude');
        $longitude = $this->input->post('longitude');
        if (!is_numeric($latitude) || !is_numeric($longitude) || $latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
            $this->output->set_output(json_encode(['success' => false, 'message' => 'Koordinat lokasi tidak valid.']));
            return;
        }

        $siswa = $eligibility['siswa'];
        $presensi = $this->db
            ->where('siswa_id', $siswa->siswa_id)
            ->where('tanggal', date('Y-m-d'))
            ->where('jam_masuk IS NOT NULL', null, false)
            ->where('jam_pulang IS NULL', null, false)
            ->get('tb_presensi')->row();

        if (!$presensi) {
            $this->output->set_output(json_encode(['success' => false, 'message' => 'Sesi lokasi berakhir setelah presensi pulang.']));
            return;
        }

        if ($presensi->status_validasi !== 'valid') {
            $this->output->set_output(json_encode([
                'success' => false,
                'fake_gps' => $presensi->status_lokasi === 'tidak_valid',
                'attendance_invalid' => true,
                'message' => $presensi->alasan_penolakan ?: 'Presensi ini tidak sah. Hubungi guru pembimbing untuk klarifikasi.'
            ]));
            return;
        }

        $integrity = $this->rule_based_validation->validate_location_integrity(
            $latitude,
            $longitude,
            $presensi->latitude_siswa,
            $presensi->longitude_siswa,
            $presensi->updated_at
        );
        if (!$integrity['is_valid']) {
            $this->db->where('presensi_id', $presensi->presensi_id)->update('tb_presensi', [
                'status_validasi' => 'tidak_valid',
                'status_lokasi' => 'tidak_valid',
                'alasan_penolakan' => $integrity['message'],
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            $this->rule_based_validation->sync_penilaian_siswa($siswa->siswa_id);
            $this->output->set_output(json_encode([
                'success' => false,
                'fake_gps' => $integrity['fake_gps'],
                'attendance_invalid' => true,
                'rule_id' => $integrity['rule_id'],
                'message' => $integrity['message']
            ]));
            return;
        }

        $geo_audit = $this->rule_based_validation->validate_geolocation($siswa->siswa_id, $latitude, $longitude, 'hadir');
        $this->db->where('presensi_id', $presensi->presensi_id)->update('tb_presensi', [
            'latitude_siswa' => $latitude,
            'longitude_siswa' => $longitude,
            'latitude_dudi' => $geo_audit['latitude_dudi'],
            'longitude_dudi' => $geo_audit['longitude_dudi'],
            'jarak' => $geo_audit['jarak'],
            'radius' => $geo_audit['radius'],
            'status_lokasi' => $geo_audit['status_lokasi'],
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $this->output->set_output(json_encode([
            'success' => true,
            'within_radius' => $geo_audit['is_valid'],
            'distance' => $geo_audit['jarak'],
            'radius' => $geo_audit['radius'],
            'message' => $geo_audit['message']
        ]));
    }

    // ==========================================
    // MODUL LOGBOOK SISWA (RULE-BASED VALIDATION)
    // ==========================================

    /**
     * Submit Logbook Kegiatan Harian Siswa
     * Menerapkan Rule: RBV-007, RBV-008, RBV-009, RBV-010, RBV-011, RBV-014, RBV-016
     */
    public function do_tambah_logbook()
    {
        $userdata = $this->session->userdata('userdata');
        $this->load->library('rule_based_validation');
        $this->load->model('M_logbook');

        // Validasi hak akses & status siswa (RBV-015)
        $eligibility = $this->rule_based_validation->validate_siswa_eligibility($userdata['id']);
        if(!$eligibility['is_allowed']) {
            $this->session->set_flashdata('error_message', $eligibility['message']);
            redirect('siswa/view/home');
            return;
        }

        $siswa = $eligibility['siswa'];
        $tanggal = $this->input->post('tanggal') ? $this->input->post('tanggal') : date('Y-m-d');
        if($tanggal !== date('Y-m-d')) {
            $this->session->set_flashdata('error_message', 'Logbook hanya dapat diisi untuk tanggal hari ini.');
            redirect('siswa/view/home');
            return;
        }
        $presensi_hari_ini = $this->db
            ->where('siswa_id', $siswa->siswa_id)
            ->where('tanggal', $tanggal)
            ->get('tb_presensi')->row();
        if(!$presensi_hari_ini || empty($presensi_hari_ini->jam_masuk) || empty($presensi_hari_ini->jam_pulang)) {
            $this->session->set_flashdata('error_message', 'Logbook hanya dapat diisi setelah presensi masuk dan pulang selesai.');
            redirect('siswa/view/home');
            return;
        }

        $latitude_siswa = $this->input->post('latitude_siswa');
        $longitude_siswa = $this->input->post('longitude_siswa');
        if ($latitude_siswa === '' || $longitude_siswa === '' || $latitude_siswa === null || $longitude_siswa === null) {
            $this->session->set_flashdata('error_message', 'Lokasi perangkat belum tersedia. Izinkan akses GPS lalu kirim ulang logbook.');
            redirect('siswa/view/logbook');
            return;
        }
        $geo_check = $this->rule_based_validation->validate_geolocation($siswa->siswa_id, $latitude_siswa, $longitude_siswa, 'hadir');
        if (!$geo_check['is_valid']) {
            $this->session->set_flashdata('error_message', 'Logbook hanya dapat diunggah saat berada di dalam radius tempat PKL. ' . $geo_check['message']);
            redirect('siswa/view/logbook');
            return;
        }
        $deskripsi_kegiatan = $this->input->post('deskripsi_kegiatan');
        $kendala_pembelajaran = $this->input->post('kendala_pembelajaran');

        // Pre-validation logbook (RBV-007, RBV-008, RBV-010) sebelum upload
        $pre_rule = $this->rule_based_validation->validate_logbook(
            $siswa->siswa_id,
            $tanggal,
            ['deskripsi_kegiatan' => $deskripsi_kegiatan, 'kendala_pembelajaran' => $kendala_pembelajaran],
            null
        );

        if (!$pre_rule['is_valid'] && $pre_rule['rule_id'] !== 'RBV-009') {
            $this->session->set_flashdata('error_message', '[' . $pre_rule['rule_id'] . '] ' . $pre_rule['message']);
            redirect('siswa/view/logbook');
            return;
        }

        // Upload Foto Dokumentasi Kegiatan (RBV-009)
        $foto_kegiatan_name = null;
        $upload_path = FCPATH . 'uploads/logbook/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0777, true);
        }

        if (!empty($_FILES['foto_kegiatan']['name'])) {
            $config['upload_path']   = $upload_path;
            $config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|doc|docx|xls|xlsx|ppt|pptx';
            $config['max_size']      = 5120; // 5MB
            $config['file_name']     = 'logbook_' . $siswa->siswa_id . '_' . date('Ymd_His');
            $config['overwrite']     = false;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            if ($this->upload->do_upload('foto_kegiatan')) {
                $upload_data = $this->upload->data();
                $foto_kegiatan_name = $upload_data['file_name'];
            } else {
                $this->session->set_flashdata('error_message', 'Gagal upload dokumentasi kegiatan: ' . $this->upload->display_errors('', ''));
                redirect('siswa/view/logbook');
                return;
            }
        }

        // Eksekusi Algoritma Rule-Based Validation untuk Logbook
        $rule_check = $this->rule_based_validation->validate_logbook(
            $siswa->siswa_id,
            $tanggal,
            [
                'deskripsi_kegiatan'   => $deskripsi_kegiatan,
                'kendala_pembelajaran' => $kendala_pembelajaran
            ],
            $foto_kegiatan_name
        );

        if (!$rule_check['is_valid']) {
            if ($foto_kegiatan_name && file_exists($upload_path . $foto_kegiatan_name)) {
                unlink($upload_path . $foto_kegiatan_name);
            }
            $this->session->set_flashdata('error_message', '[' . $rule_check['rule_id'] . '] ' . $rule_check['message']);
            redirect('siswa/view/logbook');
            return;
        }

        // Simpan Data Logbook
        $insert_data = [
            'siswa_id'             => $siswa->siswa_id,
            'tanggal'              => $tanggal,
            'deskripsi_kegiatan'   => $deskripsi_kegiatan,
            'kendala_pembelajaran' => $kendala_pembelajaran,
            'foto_kegiatan'        => $foto_kegiatan_name,
            'catatan_pembimbing'   => null,
            'status_validasi'      => 'menunggu',
            'alasan_penolakan'     => null,
            'created_at'           => date('Y-m-d H:i:s')
        ];

        $saved = $this->M_logbook->simpan_logbook($insert_data);
        if ($saved) {
            // Sinkronisasi nilai & rekapitulasi evaluasi siswa
            $this->rule_based_validation->sync_penilaian_siswa($siswa->siswa_id);
            $this->session->set_flashdata('message', 'Logbook berhasil dikirim dan sedang menunggu validasi guru pembimbing.');
        } else {
            $this->session->set_flashdata('error_message', 'Terjadi kesalahan sistem saat menyimpan logbook.');
        }

        redirect('siswa/view/logbook');
    }
}

