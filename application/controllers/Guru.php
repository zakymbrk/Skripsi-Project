<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Guru extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        if(!$this->session->userdata('userdata')){
            redirect('auth/');
        }elseif($this->session->userdata('userdata')['level'] != 2){
            redirect('auth/');
        }

        $this->load->model('M_presensi');
        $this->load->model('M_logbook');
        $this->load->model('M_pengelompokan');
        $this->load->model('M_pembimbing');
        $this->load->model('M_siswa');
        $this->load->model('M_user');
    }

    /**
     * Mendapatkan record data pembimbing dari user yang sedang login
     */
    private function get_current_pembimbing()
    {
        $userdata = $this->session->userdata('userdata');
        $user_id = $userdata['id'];

        $this->db->where('user_id', $user_id);
        $pembimbing = $this->db->get('tb_pembimbing')->row();

        if (!$pembimbing && !empty($userdata['nip_nim'])) {
            $this->db->where('pembimbing_nip', $userdata['nip_nim']);
            $pembimbing = $this->db->get('tb_pembimbing')->row();
        }

        return $pembimbing;
    }

    /**
     * Pastikan pembimbing terhubung ke akun login; tolak jika tidak
     */
    private function require_pembimbing_session()
    {
        $pembimbing = $this->get_current_pembimbing();
        if (!$pembimbing) {
            $this->session->set_flashdata('error_message', 'Profil pembimbing tidak ditemukan atau belum terhubung ke akun Anda. Hubungi Hubin.');
            redirect('guru/view/home');
            exit;
        }
        return $pembimbing;
    }

    /**
     * Verifikasi siswa_id milik pembimbing (cegah IDOR)
     */
    private function verify_siswa_bimbingan($siswa_id, $pembimbing_id, $redirect_page = 'guru/view/home')
    {
        if (empty($siswa_id)) {
            return;
        }
        deny_pembimbing_siswa_access($siswa_id, $pembimbing_id, $redirect_page);
    }

    public function view($page = 'home')
    {
        $pembimbing = $this->get_current_pembimbing();
        $pembimbing_id = $pembimbing ? $pembimbing->pembimbing_id : 0;
        $data['pembimbing'] = $pembimbing;

        if ($page == 'home') {
            // Data untuk Dashboard Guru
            $tanggal_hari_ini = date('Y-m-d');
            $data['siswa_bimbingan'] = $this->M_pengelompokan->get_siswa_by_pembimbing($pembimbing_id)->result();
            $data['total_siswa']     = count($data['siswa_bimbingan']);
            
            // Presensi hari ini siswa bimbingan
            $data['presensi_hari_ini'] = $this->M_presensi->get_presensi_by_pembimbing($pembimbing_id, $tanggal_hari_ini);
            $data['total_hadir_hari_ini'] = 0;
            foreach ($data['presensi_hari_ini'] as $pr) {
                if ($pr->status_kehadiran == 'hadir') {
                    $data['total_hadir_hari_ini']++;
                }
            }

            // Logbook terbaru bimbingan
            $data['logbook_terbaru'] = $this->M_logbook->get_logbook_by_pembimbing($pembimbing_id, null, null);
            $data['total_logbook']   = count($data['logbook_terbaru']);
            $data['total_logbook_valid'] = 0;
            $data['total_logbook_tidak_valid'] = 0;
            foreach ($data['logbook_terbaru'] as $lb) {
                if ($lb->status_validasi === 'valid') {
                    $data['total_logbook_valid']++;
                } elseif ($lb->status_validasi === 'tidak_valid') {
                    $data['total_logbook_tidak_valid']++;
                }
            }
            $data['total_tidak_hadir_hari_ini'] = max(0, $data['total_siswa'] - $data['total_hadir_hari_ini']);

            // Pengumuman
            $this->load->model('M_pengumuman');
            $data['pengumuman'] = $this->M_pengumuman->get_recent_pengumuman(3);

            $data['content'] = 'guru/dashboard';

        } elseif ($page == 'monitoring-presensi') {
            // Filter tanggal & siswa
            $filter_tanggal = $this->input->get('tanggal');
            $filter_siswa   = $this->input->get('siswa_id');

            $this->verify_siswa_bimbingan($filter_siswa, $pembimbing_id, 'guru/view/monitoring-presensi');

            $data['filter_tanggal']  = $filter_tanggal;
            $data['filter_siswa']    = $filter_siswa;
            $data['siswa_list']      = $this->M_pengelompokan->get_siswa_by_pembimbing($pembimbing_id)->result();
            $data['presensi_list']   = $this->M_presensi->get_presensi_by_pembimbing($pembimbing_id, $filter_tanggal, $filter_siswa);
            $data['content']         = 'guru/monitoring-presensi';

        } elseif ($page == 'monitoring-logbook') {
            // Filter tanggal & siswa
            $filter_tanggal = $this->input->get('tanggal');
            $filter_siswa   = $this->input->get('siswa_id');

            $this->verify_siswa_bimbingan($filter_siswa, $pembimbing_id, 'guru/view/monitoring-logbook');

            $data['filter_tanggal']  = $filter_tanggal;
            $data['filter_siswa']    = $filter_siswa;
            $data['siswa_list']      = $this->M_pengelompokan->get_siswa_by_pembimbing($pembimbing_id)->result();
            $data['logbook_list']    = $this->M_logbook->get_logbook_by_pembimbing($pembimbing_id, $filter_tanggal, $filter_siswa);
            $data['content']         = 'guru/monitoring-logbook';

        } elseif ($page == 'siswa-bimbingan') {
            $data['siswa_bimbingan'] = $this->M_pengelompokan->get_siswa_by_pembimbing($pembimbing_id)->result();
            $data['content']         = 'guru/siswa-bimbingan';

        } elseif ($page == 'laporan-bimbingan') {
            $filter_siswa = $this->input->get('siswa_id');

            $this->verify_siswa_bimbingan($filter_siswa, $pembimbing_id, 'guru/view/laporan-bimbingan');

            $data['filter_siswa']    = $filter_siswa;
            $data['siswa_list']      = $this->M_pengelompokan->get_siswa_by_pembimbing($pembimbing_id)->result();
            $data['presensi_list']   = $this->M_presensi->get_presensi_by_pembimbing($pembimbing_id, null, $filter_siswa);
            $data['logbook_list']    = $this->M_logbook->get_logbook_by_pembimbing($pembimbing_id, null, $filter_siswa);

            // Sinkronisasi dan ambil data penilaian evaluasi siswa bimbingan
            $this->load->library('rule_based_validation');
            foreach ($data['siswa_list'] as $sb) {
                $this->rule_based_validation->sync_penilaian_siswa($sb->siswa_id);
            }

            $this->db->select('p.*, s.siswa_nama, s.siswa_nis, s.siswa_kelas, d.dudi_nama');
            $this->db->from('tb_penilaian p');
            $this->db->join('tb_siswa s', 's.siswa_id = p.siswa_id');
            $this->db->join('tb_dudi d', 'd.dudi_id = s.dudi_id', 'left');
            $this->db->join('tb_pengelompokan peng', 'peng.siswa_id = s.siswa_id');
            $this->db->where('peng.pembimbing_id', $pembimbing_id);
            if ($filter_siswa) {
                $this->db->where('p.siswa_id', $filter_siswa);
            }
            $data['penilaian_list'] = $this->db->get()->result();

            $data['content']         = 'guru/laporan-bimbingan';

        } elseif ($page == 'profile') {
            $userdata = $this->session->userdata('userdata');
            $data['user'] = $this->M_user->get_user_by_id($userdata['id']);
            $data['content'] = 'guru/profile';

        } elseif ($page == 'change-password') {
            $data['content'] = 'guru/change-password';

        } else {
            if (file_exists(APPPATH . 'views/guru/' . $page . '.php')) {
                $data['content'] = 'guru/' . $page;
            } else {
                $data['content'] = '404';
            }
        }

        $this->load->view('template', $data, FALSE);
    }

    /**
     * Guru Pembimbing memberikan catatan review pada logbook siswa
     */
    public function simpan_catatan_logbook()
    {
        $pembimbing = $this->require_pembimbing_session();
        $pembimbing_id = $pembimbing->pembimbing_id;
        $this->load->library('rule_based_validation');

        if(!$this->db->field_exists('nilai_pembimbing', 'tb_logbook')) {
            $this->db->query('ALTER TABLE `tb_logbook` ADD COLUMN `nilai_pembimbing` DECIMAL(5,2) DEFAULT NULL AFTER `catatan_pembimbing`');
        }

        $logbook_id = $this->input->post('logbook_id');
        $catatan    = $this->input->post('catatan_pembimbing');
        $nilai      = $this->input->post('nilai_pembimbing');
        $keputusan  = $this->input->post('keputusan_logbook');
        $alasan     = $this->input->post('alasan_penolakan');

        $review_rule = $this->rule_based_validation->validate_logbook_review($keputusan, $catatan, $nilai, $alasan);
        if (empty($logbook_id) || !$review_rule['is_valid']) {
            $message = empty($logbook_id) ? 'Data logbook tidak ditemukan.' : '[' . $review_rule['rule_id'] . '] ' . $review_rule['message'];
            $this->session->set_flashdata('error_message', $message);
            redirect('guru/view/monitoring-logbook');
            return;
        }

        $updated = $this->M_logbook->update_catatan_pembimbing(
            $logbook_id,
            trim($catatan),
            $pembimbing_id,
            $nilai,
            $review_rule['status_validasi'],
            $review_rule['alasan_penolakan']
        );
        if ($updated) {
            $this->rule_based_validation->sync_penilaian_siswa($this->M_logbook->get_logbook_by_id($logbook_id)->siswa_id);
            $this->session->set_flashdata('message', $review_rule['message'] . ' Catatan dan penilaian pembimbing tersimpan.');
        } else {
            $this->session->set_flashdata('error_message', 'Gagal memperbarui catatan logbook. Anda tidak memiliki akses ke logbook siswa ini.');
        }

        redirect('guru/view/monitoring-logbook');
    }

    public function api_lokasi_siswa()
    {
        $this->output->set_content_type('application/json');
        $pembimbing = $this->get_current_pembimbing();
        if (!$pembimbing) {
            $this->output->set_status_header(403)->set_output(json_encode(['success' => false, 'message' => 'Profil pembimbing tidak ditemukan.']));
            return;
        }

        $this->db->select('s.siswa_id, s.siswa_nama, s.siswa_kelas, s.user_id, u.foto_profil, p.latitude_siswa, p.longitude_siswa, p.jarak, p.radius, p.status_lokasi, p.jam_masuk, p.jam_pulang, p.updated_at as last_seen, d.dudi_nama, d.latitude as dudi_latitude, d.longitude as dudi_longitude');
        $this->db->from('tb_pengelompokan k');
        $this->db->join('tb_siswa s', 's.siswa_id = k.siswa_id');
        $this->db->join('tb_user u', 'u.id = s.user_id', 'left');
        $this->db->join('tb_presensi p', "p.siswa_id = s.siswa_id AND p.tanggal = '" . $this->db->escape_str(date('Y-m-d')) . "'", 'inner');
        $this->db->join('tb_dudi d', 'd.dudi_id = s.dudi_id', 'left');
        $this->db->where('k.pembimbing_id', $pembimbing->pembimbing_id);
        $this->db->where('s.status_pengajuan', 'disetujui');
        $this->db->where('p.jam_masuk IS NOT NULL', null, false);
        $this->db->where('p.jam_pulang IS NULL', null, false);
        $this->db->where('p.latitude_siswa IS NOT NULL', null, false);
        $this->db->where('p.longitude_siswa IS NOT NULL', null, false);
        $this->db->order_by('s.siswa_nama', 'ASC');
        $locations = $this->db->get()->result();

        foreach ($locations as $location) {
            $location->foto_profil_url = base_url('assets/img/default-avatar.png');
            if (!empty($location->foto_profil)) {
                $photo_path = FCPATH . 'uploads/profil/' . $location->foto_profil;
                if (file_exists($photo_path)) {
                    $location->foto_profil_url = base_url('uploads/profil/' . $location->foto_profil);
                } elseif (file_exists(FCPATH . 'uploads/profil/' . basename($location->foto_profil))) {
                    $location->foto_profil_url = base_url('uploads/profil/' . basename($location->foto_profil));
                }
            }
            $location->is_online = strtotime($location->last_seen) >= (time() - 120);
        }

        $this->output->set_output(json_encode([
            'success' => true,
            'server_time' => date('c'),
            'locations' => $locations
        ]));
    }

    /**
     * Update Profile Guru
     */
    public function update_profile()
    {
        $status = true;
        $userdata = $this->session->userdata('userdata');
        $user_id = $userdata['id'];

        $user = $this->M_user->get_user_by_id($user_id);
        if(!$user){
            $status = false;
            $this->session->set_flashdata('error_message', 'User tidak ditemukan');
        }

        $this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'required');
        
        if ($this->form_validation->run() == FALSE) {
            $status = false;
        }

        if($status){
            $update = array(
                'nama_lengkap' => $this->input->post('nama_lengkap'),
                'telepon'      => $this->input->post('telepon'),
                'alamat'       => $this->input->post('alamat'),
                'email'        => $this->input->post('email')
            );

            $this->M_user->update_user_by_id($update, $user_id);
            
            // Update session
            $user_login = array(
                'userdata' => array(
                    'id'          => $userdata['id'],
                    'username'    => $userdata['username'],
                    'nama_lengkap'=> $update['nama_lengkap'],
                    'level'       => $userdata['level'],
                    'group_name'  => $userdata['group_name'],
                    'foto_profil' => isset($userdata['foto_profil']) ? $userdata['foto_profil'] : null
                ),
            );
            $this->session->set_userdata($user_login);
            
            $this->session->set_flashdata('message', 'Profile guru berhasil diupdate');
            redirect('guru/view/profile');
        } else {
            $data['user'] = $user;
            $data['content'] = 'guru/profile';
            $this->load->view('template', $data, FALSE);
        }
    }

    /**
     * Ubah Password Guru
     */
    public function change_password()
    {
        $status = true;
        $userdata = $this->session->userdata('userdata');
        $user_id = $userdata['id'];

        $user = $this->M_user->get_user_by_id($user_id);
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
                $this->M_user->update_user_by_id($update, $user_id);
                
                $this->session->set_flashdata('message', 'Password berhasil diubah');
                redirect('guru/view/change-password');
            }
        }

        $data['content'] = 'guru/change-password';
        $this->load->view('template', $data, FALSE);
    }
}