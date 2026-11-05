<?php
defined('BASEPATH')OR exit('No direct script access allowed');

class Hubin extends CI_Controller {
    
    public function __construct()
    {
        parent::__construct();
        if(!$this->session->userdata('userdata')){
            redirect('auth');
        }elseif($this->session->userdata('userdata')['level'] != 1){
            redirect('auth');
        }
        
        // Load cache library
        $this->load->driver('cache');
    }

    private function normalize_pkl_start_date($tanggal_mulai)
    {
        if (empty($tanggal_mulai)) {
            return date('Y-m-d');
        }

        $date = DateTime::createFromFormat('Y-m-d', $tanggal_mulai);
        if (!$date || $date->format('Y-m-d') !== $tanggal_mulai) {
            return date('Y-m-d');
        }

        $today = new DateTime('today');
        $start = new DateTime($tanggal_mulai);
        if ($start > $today && $start->diff($today)->days <= 30) {
            return $tanggal_mulai;
        }
        if ($start > $today->modify('+30 days')) {
            return $today->format('Y-m-d');
        }

        return $tanggal_mulai;
    }

    private function get_pkl_period_data($siswa)
    {
        $tanggal_mulai = $this->normalize_pkl_start_date(!empty($siswa->tanggal_mulai) ? $siswa->tanggal_mulai : date('Y-m-d'));
        $tanggal_selesai = !empty($siswa->tanggal_selesai) ? $siswa->tanggal_selesai : date('Y-m-d', strtotime($tanggal_mulai . ' +3 months -1 day'));

        $start_dt = DateTime::createFromFormat('Y-m-d', $tanggal_mulai);
        $end_dt = DateTime::createFromFormat('Y-m-d', $tanggal_selesai);
        if (!$start_dt || !$end_dt || $end_dt < $start_dt) {
            $tanggal_mulai = date('Y-m-d');
            $tanggal_selesai = date('Y-m-d', strtotime($tanggal_mulai . ' +3 months -1 day'));
        }

        $diff_days = $start_dt ? $start_dt->diff($end_dt ?: new DateTime($tanggal_mulai))->days : 90;
        if ($diff_days < 60 || $diff_days > 150) {
            $tanggal_selesai = date('Y-m-d', strtotime($tanggal_mulai . ' +3 months -1 day'));
        }

        return array(
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_selesai' => $tanggal_selesai,
            'lama_pelaksanaan' => 90,
            'periode' => date('Y', strtotime($tanggal_mulai)) . '/' . (date('Y', strtotime($tanggal_mulai)) + 1)
        );
    }

    // Method to download import template
    public function download_template()
    {
        $template_type = $this->input->get('type') ? $this->input->get('type') : 'excel';
        
        if($template_type == 'pembimbing') {
            redirect('export_excel/export_data_pembimbing_template');
        } elseif($template_type == 'excel') {
            redirect('export_excel/export_data_siswa_template');
        } else {
            $this->generate_csv_template();
        }
    }
    
    private function generate_csv_template()
    {
        $template_data = [
            [
                'Nama Siswa', 'NIS', 'NISN', 'Jenis Kelamin', 'Tempat Lahir', 'Tanggal Lahir',
                'Kelas', 'Jurusan', 'Sekolah', 'Telepon Siswa', 'Username', 'Password',
                'Email', 'Alamat Lengkap', 'Perusahaan (DUDI)', 'Tanggal Mulai PKL',
                'Tanggal Selesai PKL', 'Lama Pelaksanaan (hari)', 'Status Pengajuan', 'Periode PKL'
            ],
            [
                'Andi Setiawan', '1234567890', '0987654321', 'L', 'Bandung', '2006-05-15',
                'XI TKJ 1', 'Teknik Komputer dan Jaringan', 'SMK ITIKURIH HIBARNA', '081234567890',
                'andi_tkj', 'password123', 'andi@example.com', 'Jl. Raya Laswi No. 12, Ciparay',
                'PT. Telekomunikasi Indonesia', '2026-07-01', '2026-09-30', '90', 'disetujui', '2026/2027'
            ],
            [
                'Budi Santoso', '1234567891', '0987654322', 'L', 'Bandung', '2006-03-20',
                'XI RPL 1', 'Rekayasa Perangkat Lunak', 'SMK ITIKURIH HIBARNA', '081234567891',
                'budi_rpl', 'password123', 'budi@example.com', 'Jl. Contoh No. 2, Ciparay',
                'PT Teknologi Indonesia', '2026-07-01', '2026-09-30', '90', 'disetujui', '2026/2027'
            ]
        ];
        
        $filename = 'template_import_siswa_' . date('Y-m-d') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        
        $output = fopen('php://output', 'w');
        
        // Add UTF-8 BOM for proper Excel display
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        foreach($template_data as $row) {
            fputcsv($output, $row);
        }
        
        fclose($output);
        exit;
    }
    
    private function generate_excel_template()
    {
        // Load PhpSpreadsheet library
        if (!class_exists('\\PhpOffice\\PhpSpreadsheet\\Spreadsheet')) {
            $autoload_path = FCPATH . 'vendor/autoload.php';
            if (file_exists($autoload_path)) {
                require_once $autoload_path;
            }
        }
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set column headers
        $headers = [
            'A' => 'Username',
            'B' => 'Nama Lengkap', 
            'C' => 'Password',
            'D' => 'NIS',
            'E' => 'NISN',
            'F' => 'Jenis Kelamin',
            'G' => 'Tempat Lahir',
            'H' => 'Tanggal Lahir (YYYY-MM-DD)',
            'I' => 'Kelas',
            'J' => 'Jurusan',
            'K' => 'Telepon',
            'L' => 'Alamat',
            'M' => 'Email',
            'N' => 'Periode',
            'O' => 'Tanggal Mulai (YYYY-MM-DD)',
            'P' => 'Tanggal Selesai (YYYY-MM-DD)',
            'Q' => 'Nama DUDI',
            'R' => 'Status Pengajuan'
        ];
        
        // Apply header styles
        $header_style = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4CAF50']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ]
            ]
        ];
        
        foreach($headers as $column => $header) {
            $sheet->setCellValue($column . '1', $header);
            $sheet->getStyle($column . '1')->applyFromArray($header_style);
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Add sample data
        $sample_data = [
            ['siswa001', 'Andi Setiawan', 'password123', '1234567890', '0987654321', 'L', 'Jakarta', '2005-01-15', 'XI RPL 1', 'Rekayasa Perangkat Lunak', '08123456789', 'Jl. Contoh No. 1', 'andi@example.com', '2026/2027', '2024-06-01', '2024-08-30', 'PT Contoh Perusahaan', 'menunggu'],
            ['siswa002', 'Budi Santoso', 'password123', '1234567891', '0987654322', 'L', 'Bandung', '2005-03-20', 'XI TKJ 1', 'Teknik Komputer dan Jaringan', '08123456790', 'Jl. Contoh No. 2', 'budi@example.com', '2026/2027', '2024-06-01', '2024-08-30', 'PT Teknologi Indonesia', 'menunggu'],
            ['siswa003', 'Citra Dewi', 'password123', '1234567892', '0987654323', 'P', 'Semarang', '2005-05-10', 'XI MM 1', 'Multimedia', '08123456791', 'Jl. Contoh No. 3', 'citra@example.com', '2026/2027', '2024-06-01', '2024-08-30', 'PT Media Kreatif', 'menunggu']
        ];
        
        $row = 2;
        foreach($sample_data as $data) {
            $col = 'A';
            foreach($data as $value) {
                $sheet->setCellValue($col . $row, $value);
                $col++;
            }
            $row++;
        }
        
        // Add data validation instructions
        $sheet->setCellValue('A' . ($row + 2), 'INSTRUKSI PENGISIAN:');
        $sheet->setCellValue('A' . ($row + 3), '1. Kolom Username, Nama Lengkap, dan Password wajib diisi');
        $sheet->setCellValue('A' . ($row + 4), '2. Jenis Kelamin: L (Laki-laki) atau P (Perempuan)');
        $sheet->setCellValue('A' . ($row + 5), '3. Format tanggal: YYYY-MM-DD (contoh: 2005-01-15)');
        $sheet->setCellValue('A' . ($row + 6), '4. Status Pengajuan: draft, menunggu, disetujui, ditolak, selesai');
        $sheet->setCellValue('A' . ($row + 7), '5. Hapus baris contoh sebelum mengupload');
        
        $filename = 'template_import_siswa_' . date('Y-m-d') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    
    public function view($page = 'home')
    {
        // Handle detail-pengumuman dengan parameter
        if($page == 'detail-pengumuman'){
            $pengumuman_id = $this->uri->segment(4);
            if($pengumuman_id){
                $this->load->model('M_pengumuman');
                $pengumuman = $this->M_pengumuman->get_pengumuman($pengumuman_id);
                if($pengumuman){
                    $data['pengumuman'] = $pengumuman;
                    $data['content'] = 'hubin/detail-pengumuman';
                } else {
                    $data['content'] = '404';
                }
            } else {
                $data['content'] = '404';
            }
        }
        // Handle edit-pengumuman dengan parameter
        else if($page == 'edit-pengumuman'){
            $pengumuman_id = $this->uri->segment(4);
            if($pengumuman_id){
                $this->load->model('M_pengumuman');
                $pengumuman = $this->M_pengumuman->get_pengumuman($pengumuman_id);
                if($pengumuman){
                    $data['pengumuman'] = $pengumuman;
                    $data['content'] = 'hubin/edit-pengumuman';
                } else {
                    $this->session->set_flashdata('error_message', 'Pengumuman tidak ditemukan');
                    redirect('hubin/view/pengumuman');
                }
            } else {
                $this->session->set_flashdata('error_message', 'ID pengumuman tidak ditemukan');
                redirect('hubin/view/pengumuman');
            }
        } else if(!file_exists(APPPATH . 'views/hubin/' . $page . '.php')){
            // Handle special case for 'dudi' - redirect to data-dudi
            if($page == 'dudi') {
                redirect('hubin/view/data-dudi');
            }
            $data['content'] = '404';
        }else{
            // Use simplified home page
            if($page == 'home'){
                $data['content'] = 'hubin/home';
                
                // Load models needed for home page statistics
                $this->load->model('M_siswa');
                $this->load->model('M_dudi');
                
                
                
                // Also load other stats that might be used in home page
                $data['pengajuan_hari_ini'] = $this->M_siswa->get_pengajuan_hari_ini_count();
                $data['siswa_belum_assign'] = $this->M_siswa->get_siswa_belum_assign_count();
            } else {
                $data['content'] = 'hubin/' . $page;
            }
        }
        
        // Handle detail-pengajuan page
        if($page == 'detail-pengajuan'){
            $siswa_code = $this->uri->segment(4);
            if($siswa_code){
                $data['siswa_code'] = $siswa_code;
            }
            
            // Load required models for pengajuan pages
            $this->load->model('M_siswa');
            $this->load->model('M_dudi');
        }
        
        // Handle id-card-siswa page with parameter
        if($page == 'id-card-siswa'){
            $siswa_id = $this->uri->segment(4);
            if($siswa_id){
                // Load QR generator library
                $this->load->library('qr_generator');
                
                // Optimized single query with all required data
                $this->db->select('s.*, u.nama_lengkap, u.foto_profil, d.dudi_nama, d.dudi_id as dudi_real_id');
                $this->db->from('tb_siswa s');
                $this->db->join('tb_user u', 'u.id = s.user_id', 'left');
                $this->db->join('tb_dudi d', 'd.dudi_id = s.dudi_id', 'left');
                $this->db->where('s.siswa_id', $siswa_id);
                $siswa = $this->db->get()->row();
                
                if($siswa){
                    // Generate QR code only after confirming student data exists
                    $data['qr_code_url'] = $this->qr_generator->generate_qr_code(
                        'https://smk.itikurih-hibarna.sch.id/?page_id=140&id=' . $siswa->siswa_id . 
                        '&nama=' . urlencode($siswa->siswa_nama) . '&kelas=' . urlencode($siswa->siswa_kelas),
                        160, 'M', 2
                    );
                    
                    // Pre-process data for faster view rendering
                    $siswa->foto_profil_url = null;
                    if($siswa->user_id && $siswa->foto_profil && file_exists('./uploads/profil/'.$siswa->foto_profil)){
                        $siswa->foto_profil_url = base_url('uploads/profil/'.$siswa->foto_profil);
                    }
                    $data['siswa'] = $siswa;
                    
                    // Load ID card as standalone page without template wrapper
                    $this->load->view('hubin/id-card', $data, FALSE);
                    return; // Exit early to prevent loading template
                } else {
                    $this->session->set_flashdata('error_message', 'Data siswa tidak ditemukan');
                    redirect('hubin/view/assign-pembimbing');
                }
            } else {
                $data['content'] = '404';
            }
        }
        
        
        
        if($page == 'data-user' || $page == 'assign-pembimbing' || $page == 'data-dudi' || $page == 'data-pembimbing' || $page == 'daftar-siswa' || $page == 'pengajuan'){
            $this->load->library('pagination');
        }
        
        // Load model M_dudi untuk halaman data-dudi
        if($page == 'data-dudi'){
            $this->load->model('M_dudi');
            $this->load->model('M_siswa');
            
            // Get all DUDI data with student data integration for synchronization
            // For student-submitted DUDI, we need to check if the data was properly transferred during approval
            $sql = "SELECT d.*,
                CASE 
                    WHEN d.sumber_data = 'siswa' AND (d.dudi_alamat IS NULL OR d.dudi_alamat = '' OR d.dudi_alamat = 'Alamat belum diisi') THEN NULL
                    WHEN d.sumber_data = 'siswa' THEN d.dudi_alamat
                    ELSE d.dudi_alamat
                END as display_alamat,
                CASE 
                    WHEN d.sumber_data = 'siswa' AND (d.dudi_telepon IS NULL OR d.dudi_telepon = '') THEN NULL
                    WHEN d.sumber_data = 'siswa' THEN d.dudi_telepon
                    ELSE d.dudi_telepon
                END as display_telepon,
                CASE 
                    WHEN d.sumber_data = 'siswa' AND (d.dudi_email IS NULL OR d.dudi_email = '') THEN NULL
                    WHEN d.sumber_data = 'siswa' THEN d.dudi_email
                    ELSE d.dudi_email
                END as display_email,
                CASE 
                    WHEN d.sumber_data = 'siswa' AND (d.dudi_pic IS NULL OR d.dudi_pic = '') THEN NULL
                    WHEN d.sumber_data = 'siswa' THEN d.dudi_pic
                    ELSE d.dudi_pic
                END as display_pic,
                CASE 
                    WHEN d.sumber_data = 'siswa' AND (d.dudi_nip_pic IS NULL OR d.dudi_nip_pic = '') THEN NULL
                    WHEN d.sumber_data = 'siswa' THEN d.dudi_nip_pic
                    ELSE d.dudi_nip_pic
                END as display_nip_pic,
                CASE 
                    WHEN d.sumber_data = 'siswa' AND (d.dudi_instruktur IS NULL OR d.dudi_instruktur = '') THEN NULL
                    WHEN d.sumber_data = 'siswa' THEN d.dudi_instruktur
                    ELSE d.dudi_instruktur
                END as display_instruktur,
                CASE 
                    WHEN d.sumber_data = 'siswa' AND (d.dudi_nip_instruktur IS NULL OR d.dudi_nip_instruktur = '') THEN NULL
                    WHEN d.sumber_data = 'siswa' THEN d.dudi_nip_instruktur
                    ELSE d.dudi_nip_instruktur
                END as display_nip_instruktur
                FROM tb_dudi d 
                GROUP BY d.dudi_id
                ORDER BY d.dudi_nama ASC";
            
            $query = $this->db->query($sql);
            $all_dudi_with_student_data = $query->result();
            
            // Ensure all objects have the display_* properties to prevent undefined property errors
            foreach($all_dudi_with_student_data as $dudi) {
                if(!property_exists($dudi, 'display_alamat')) {
                    $dudi->display_alamat = $dudi->sumber_data == 'siswa' && (empty($dudi->dudi_alamat) || $dudi->dudi_alamat == 'Alamat belum diisi') ? null : $dudi->dudi_alamat;
                }
                if(!property_exists($dudi, 'display_telepon')) {
                    $dudi->display_telepon = $dudi->sumber_data == 'siswa' && empty($dudi->dudi_telepon) ? null : $dudi->dudi_telepon;
                }
                if(!property_exists($dudi, 'display_pic')) {
                    $dudi->display_pic = $dudi->sumber_data == 'siswa' && empty($dudi->dudi_pic) ? null : $dudi->dudi_pic;
                }
                if(!property_exists($dudi, 'display_email')) {
                    $dudi->display_email = $dudi->sumber_data == 'siswa' && empty($dudi->dudi_email) ? null : $dudi->dudi_email;
                }
                if(!property_exists($dudi, 'display_nip_pic')) {
                    $dudi->display_nip_pic = $dudi->sumber_data == 'siswa' && empty($dudi->dudi_nip_pic) ? null : $dudi->dudi_nip_pic;
                }
                if(!property_exists($dudi, 'display_instruktur')) {
                    $dudi->display_instruktur = $dudi->sumber_data == 'siswa' && empty($dudi->dudi_instruktur) ? null : $dudi->dudi_instruktur;
                }
                if(!property_exists($dudi, 'display_nip_instruktur')) {
                    $dudi->display_nip_instruktur = $dudi->sumber_data == 'siswa' && empty($dudi->dudi_nip_instruktur) ? null : $dudi->dudi_instruktur;
                }
            }
            
            // Filter data for each category based on the synchronized data
            $dudi_mitra = array();
            $dudi_non_mitra = array();
            $dudi_pengajuan = array(); // Also handle pengajuan
            
            foreach($all_dudi_with_student_data as $dudi) {
                if($dudi->status_kerjasama == 'mitra') {
                    $dudi_mitra[] = $dudi;
                } elseif($dudi->status_kerjasama == 'non_mitra') {
                    $dudi_non_mitra[] = $dudi;
                } elseif($dudi->status_kerjasama == 'pengajuan') {
                    $dudi_pengajuan[] = $dudi;
                }
            }
            
            // Pass the data directly as arrays since the SQL query already sets the display_* properties
            // Create a simple object with result method to maintain compatibility
            $data['dudi_mitra'] = (object)[
                'result' => function() use ($dudi_mitra) { return $dudi_mitra; },
                'num_rows' => function() use ($dudi_mitra) { return count($dudi_mitra); }
            ];
            
            $data['dudi_non_mitra'] = (object)[
                'result' => function() use ($dudi_non_mitra) { return $dudi_non_mitra; },
                'num_rows' => function() use ($dudi_non_mitra) { return count($dudi_non_mitra); }
            ];
            
            // Load statistik
            $data['stats'] = $this->M_dudi->get_dudi_statistics();
            
            // Store all synchronized data for the main table
            $data['all_dudi_with_student_data'] = $all_dudi_with_student_data;
        }
        
        // Load model M_pembimbing untuk halaman data-pembimbing
        if($page == 'data-pembimbing' || $page == 'daftar-pembimbing'){
            $this->load->model('M_pembimbing');
            
            // Load data untuk daftar-pembimbing
            if($page == 'daftar-pembimbing'){
                $search = $this->input->get('nama_pembimbing') ? $this->input->get('nama_pembimbing') : '';
                $data['search'] = $search;
                
                // Get all pembimbing with student count (no pagination - show all)
                $pembimbing_result = $this->M_pembimbing->get_pembimbing_with_siswa_count();
                $data['all_pembimbing'] = $pembimbing_result->result();
                $data['total_pembimbing'] = count($data['all_pembimbing']);
            }
        }
        
        // Load user data for profile page
        if($page == 'profile'){
            $this->load->model('M_user');
            $userdata = $this->session->userdata('userdata');
            $data['user'] = $this->M_user->get_user_by_id($userdata['id']);
        }
        
        // Load user data for data-user page with pagination
        if($page == 'data-user'){
            $this->load->model('M_user');
            
            // Get search parameter
            $search = $this->input->get('username') ? $this->input->get('username') : '';
            $data['search'] = $search;
            
            // Pagination configuration
            $config['base_url'] = base_url('hubin/view/daftar-siswa');
            
            // Count total rows with search condition
            $config['total_rows'] = $this->M_user->get_all_users_count_search($search);
            $config['per_page'] = 10;
            $config['uri_segment'] = 4;
            
            // Add search parameter to pagination
            if ($search) {
                $config['suffix'] = '?username=' . urlencode($search);
                $config['first_url'] = $config['base_url'] . $config['suffix'];
            }
            
            // Styling for pagination
            $config['full_tag_open'] = '<ul class="pagination">';
            $config['full_tag_close'] = '</ul>';
            $config['first_link'] = 'First';
            $config['last_link'] = 'Last';
            $config['first_tag_open'] = '<li class="page-item">';
            $config['first_tag_close'] = '</li>';
            $config['prev_link'] = '&laquo';
            $config['prev_tag_open'] = '<li class="page-item">';
            $config['prev_tag_close'] = '</li>';
            $config['next_link'] = '&raquo';
            $config['next_tag_open'] = '<li class="page-item">';
            $config['next_tag_close'] = '</li>';
            $config['last_tag_open'] = '<li class="page-item">';
            $config['last_tag_close'] = '</li>';
            $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
            $config['cur_tag_close'] = '</a></li>';
            $config['num_tag_open'] = '<li class="page-item">';
            $config['num_tag_close'] = '</li>';
            $config['attributes'] = array('class' => 'page-link');
            
            // Initialize pagination
            $this->pagination->initialize($config);
            
            // Get offset from URI segment
            $offset = $this->uri->segment(4) ? $this->uri->segment(4) : 0;
            
            // Get users with pagination and search
            $data['getuser'] = $this->M_user->get_all_users_paginated_search($config['per_page'], $offset, $search);
            $data['total_rows'] = $config['total_rows'];
            $data['config'] = $config;
            $data['offset'] = $offset;
        }
        
        // Load student data for daftar-siswa page (only for level 3 - siswa)
        if($page == 'daftar-siswa'){
            $this->load->model('M_user');
            
            // Get search parameter
            $search = $this->input->get('username') ? $this->input->get('username') : '';
            $data['search'] = $search;
            
            // Current user data to exclude from the list
            $userdata = $this->session->userdata('userdata');
            
            // Get students with complete biodata (no pagination - show all)
            $this->db->select('u.*, g.group_name, s.siswa_id, s.siswa_code, s.siswa_nama, s.siswa_nis, s.siswa_nisn, s.siswa_kelas, s.siswa_jurusan, u.alamat as siswa_alamat, s.status_pengajuan, s.dudi_id, d.dudi_nama, d.is_mitra as dudi_is_mitra');
            $this->db->from('tb_user u');
            $this->db->join('tb_group g', 'g.group_id = u.level');
            $this->db->join('tb_siswa s', 's.user_id = u.id', 'left');
            $this->db->join('tb_dudi d', 'd.dudi_id = s.dudi_id', 'left');
            $this->db->where('u.level', 3); // Only Siswa
            $this->db->where('u.id !=', $userdata['id']); // Exclude current user
            if ($search) {
                $this->db->group_start();
                $this->db->like('u.username', $search);
                $this->db->or_like('u.nama_lengkap', $search);
                $this->db->or_like('s.siswa_nama', $search);
                $this->db->or_like('s.siswa_nis', $search);
                $this->db->or_like('s.siswa_kelas', $search);
                $this->db->group_end();
            }
            $this->db->order_by('u.id', 'DESC');
            $data['getuser'] = $this->db->get();
            
            // Set total_rows to actual count but no pagination
            $this->db->where('level', 3); // Only Siswa
            $this->db->where('id !=', $userdata['id']); // Exclude current user
            if ($search) {
                $this->db->group_start();
                $this->db->like('username', $search);
                $this->db->or_like('nama_lengkap', $search);
                $this->db->group_end();
            }
            $data['total_rows'] = $this->db->count_all_results('tb_user');
            $data['config'] = null; // No pagination config
            $data['offset'] = 0;
        }
        
        $this->load->view('template', $data, FALSE);
    }
        
    // Method to handle single student assignment after approval
    function assign_pembimbing_single($siswa_code = null)
    {
        if(!$siswa_code) {
            $this->session->set_flashdata('error_message', 'Kode siswa tidak valid');
            redirect('hubin/view/pengajuan');
            return;
        }
            
        $this->load->model('M_siswa');
        $this->load->model('M_pembimbing');
            
        $siswa = $this->M_siswa->get_data_siswa($siswa_code);
        if(!$siswa) {
            $this->session->set_flashdata('error_message', 'Data siswa tidak ditemukan');
            redirect('hubin/view/pengajuan');
            return;
        }
            
        // Get all pembimbing for dropdown
        $pembimbing_list = $this->M_pembimbing->get_all_pembimbing()->result();
            
        $data['siswa'] = $siswa;
        $data['pembimbing_list'] = $pembimbing_list;
        $data['content'] = 'hubin/assign-pembimbing-single';
            
        $this->load->view('template', $data, FALSE);
    }
        
        
    // Handle assignment of a single student to a pembimbing
    function assign_single_student_pembimbing()
    {
        $siswa_code = $this->input->post('siswa_code');
        $pembimbing_id = $this->input->post('pembimbing_id');
            
        if (!$siswa_code || !$pembimbing_id) {
            $this->session->set_flashdata('error_message', 'Data tidak lengkap');
            redirect('hubin/view/pengajuan');
            return;
        }
            
        $this->load->model('M_siswa');
        $this->load->model('M_pengelompokan');
        $this->load->model('M_pembimbing');
            
        // Get student data
        $siswa = $this->M_siswa->get_data_siswa($siswa_code);
        if (!$siswa) {
            $this->session->set_flashdata('error_message', 'Data siswa tidak ditemukan');
            redirect('hubin/view/pengajuan');
            return;
        }
            
        // Check if student status is approved
        if ($siswa->status_pengajuan != 'disetujui') {
            $this->session->set_flashdata('error_message', 'Hanya siswa dengan status disetujui yang dapat diassign pembimbing');
            redirect('hubin/view/pengajuan');
            return;
        }
            
        // Get pembimbing data
        $pembimbing = $this->M_pembimbing->get_pembimbing_by_id($pembimbing_id);
        if (!$pembimbing) {
            $this->session->set_flashdata('error_message', 'Guru pembimbing tidak ditemukan');
            redirect('hubin/view/assign-pembimbing-single/' . $siswa_code);
            return;
        }
            
        // Check current assignment count for this pembimbing
        $this->db->where('pembimbing_id', $pembimbing_id);
        $current_count = $this->db->get('tb_pengelompokan')->num_rows();
        $max_limit = 20; // Maximum 20 students per pembimbing
            
        if ($current_count >= $max_limit) {
            $this->session->set_flashdata('error_message', 'Guru pembimbing ini telah mencapai batas maksimum (' . $max_limit . ') dalam mengajar siswa PKL');
            redirect('hubin/view/assign-pembimbing-single/' . $siswa_code);
            return;
        }
            
        // Check if student is already assigned to another pembimbing
        $this->db->where('siswa_id', $siswa->siswa_id);
        $existing_assignment = $this->db->get('tb_pengelompokan')->row();
            
        if ($existing_assignment) {
            // Update existing assignment
            $update_data = array(
                'pembimbing_id' => $pembimbing_id,
                'tanggal_assign' => date('Y-m-d H:i:s')
            );
            $this->db->where('siswa_id', $siswa->siswa_id);
            $this->db->update('tb_pengelompokan', $update_data);
        } else {
            // Create new assignment
            $assignment_data = array(
                'siswa_id' => $siswa->siswa_id,
                'pembimbing_id' => $pembimbing_id,
                'tanggal_assign' => date('Y-m-d H:i:s')
            );
            $this->db->insert('tb_pengelompokan', $assignment_data);
        }
            
        // Also update siswa table for backward compatibility
        $current_year = date('Y');
        $next_year = $current_year + 1;
        $periode = $current_year . '/' . $next_year;
            
        $this->db->where('siswa_id', $siswa->siswa_id);
        $this->db->update('tb_siswa', array(
            'pembimbing_id' => $pembimbing_id,
            'periode' => $periode
        ));
            
        $this->session->set_flashdata('message', 'Berhasil assign guru pembimbing "' . $pembimbing->pembimbing_nama . '" ke siswa "' . $siswa->siswa_nama . '".');
        redirect('hubin/view/assign-pembimbing');
    }
        
        
    function tambah_user()
    {
        $status = true;
        $this->load->model('M_user');

        if ($this->input->post('nip_nim') !== null && $this->input->post('nip_nim') !== '') {
            $_POST['nip_nim'] = $this->M_user->normalize_identifier_value($this->input->post('nip_nim'));
        }

        $this->form_validation->set_rules('username', 'Username', 'required|alpha_numeric|is_unique[tb_user.username]');
        $this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');
        $this->form_validation->set_rules('level', 'Level', 'required');
        
        // Validasi email jika diisi
        if($this->input->post('email')) {
            $this->form_validation->set_rules('email', 'Email', 'valid_email|is_unique[tb_user.email]');
        }
        
        // Validasi NIP/NIM jika diisi
        if($this->input->post('nip_nim')) {
            $this->form_validation->set_rules('nip_nim', 'NIP/NIM', 'is_unique[tb_user.nip_nim]');
        }
        if ($this->form_validation->run() == FALSE) {
            $status = false;
        }

        if($status){
            $password_plain = $this->input->post('password');
            $hash = password_hash($password_plain, PASSWORD_DEFAULT);
            $code = $this->M_user->generateRandomString(30);
            // Siapkan data identitas lengkap
            $identity_fields = [
                'email', 'telepon', 'alamat', 'tempat_lahir', 
                'tanggal_lahir', 'jenis_kelamin', 'nip_nim'
            ];
            
            $identity_data = [];
            foreach($identity_fields as $field) {
                $value = $this->input->post($field);
                if(!empty($value)) {
                    // Convert jenis_kelamin from 'L'/'P' to 'Laki-laki'/'Perempuan'
                    if($field === 'jenis_kelamin') {
                        if ($value === 'L') {
                            $value = 'Laki-laki';
                        } elseif ($value === 'P') {
                            $value = 'Perempuan';
                        }
                    }
                    $identity_data[$field] = $value;
                }
            }
            
            $data = array_merge([
                'username' => $this->input->post('username'),
                'password' => $hash,
                'nama_lengkap' => $this->input->post('nama_lengkap'),
                'level' => $this->input->post('level'),
                'user_code' => $code,
                'created_at' => date('Y-m-d H:i:s')
            ], $identity_data);
            $this->M_user->tambah_data_user($data);
            
            // Get the inserted user ID
            $user_id = $this->db->insert_id();
            
            // Jika level siswa (3), auto-create basic student data
            if($this->input->post('level') == 3){ // Create student data only for student level
                $this->load->model('M_siswa');
                
                // Check if student data already exists
                $this->db->where('user_id', $user_id);
                $existing_siswa = $this->db->get('tb_siswa')->row();
                
                if(!$existing_siswa){
                    // Auto-create basic student data
                    $siswa_code = $this->M_siswa->generateRandomString(30);
                    $siswa_data = array(
                        'user_id' => $user_id,
                        'siswa_nama' => $this->input->post('nama_lengkap'),
                        'siswa_code' => $siswa_code
                    );
                    $this->M_siswa->tambah_data_siswa($siswa_data);
                    
                    // Default status awal siswa harus konsisten dengan alur PKL.
                    // Null membuat daftar status tidak konsisten dan memicu redirect/logic yang salah.
                    $this->db->where('user_id', $user_id);
                    $this->db->update('tb_siswa', array('status_pengajuan' => 'draft'));
                }
                
                // Simpan username dan password untuk ditampilkan
                $this->session->set_flashdata('new_user_username', $this->input->post('username'));
                $this->session->set_flashdata('new_user_password', $password_plain);
                $this->session->set_flashdata('new_user_nama', $this->input->post('nama_lengkap'));
                $this->session->set_flashdata('message', 'Berhasil menambah user siswa. Data siswa otomatis dibuat. Username dan password telah disimpan untuk diberikan ke siswa.');
            } 
            
            else {
                $this->session->set_flashdata('message', 'Berhasil menambah user');
            }
            redirect('hubin/view/daftar-siswa');
        }else{
            $this->view('tambah-user');
        }
    }

    function read_user($user_code = null)
    {
        $this->load->model('M_user');
        $user = $this->M_user->get_data_user($user_code);
        if($user){
            $data['user'] = $user;
            $data['content'] = 'hubin/read-user';
        }else{
            $data['content'] = '404';
        }
        $this->load->view('template', $data, FALSE);
    }

    function edit_user($user_code = null)
    {
        $this->load->model('M_user');
        $user = $this->M_user->get_data_user($user_code);
        
        if($user){
            $data['user'] = $user;
            $data['content'] = 'hubin/edit-user';
        }else{
            $data['content'] = '404';
        }
        $this->load->view('template', $data, FALSE);
    }

    function update_data_user()
    {
        $status = true;
        $content = 'hubin/edit-user';
        $user_code = $this->input->post('user_code');
        $this->load->model('M_user');

        if ($this->input->post('nip_nim') !== null && $this->input->post('nip_nim') !== '') {
            $_POST['nip_nim'] = $this->M_user->normalize_identifier_value($this->input->post('nip_nim'));
        }

        $cekusers = $this->M_user->get_data_user($user_code);
        if(!$cekusers){
            $status = false;
            $content = '404';
        }else{
            $data['user'] = $cekusers;
        }
        
        $this->form_validation->set_rules('user_code', 'code', 'required');
        $this->form_validation->set_rules('username', 'Username', 'required|alpha_numeric|callback_username_check['.$user_code.']');
        $this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'required');
        $this->form_validation->set_rules('level', 'Level', 'required');
        
        // Validasi email jika diisi
        if($this->input->post('email')) {
            $this->form_validation->set_rules('email', 'Email', 'valid_email|callback_email_check['.$user_code.']');
        }
        
        // Validasi NIP/NIM jika diisi
        if($this->input->post('nip_nim')) {
            $this->form_validation->set_rules('nip_nim', 'NIP/NIM', 'callback_nip_nim_check['.$user_code.']');
        }
        if ($this->form_validation->run() == FALSE) {
            $status = false;
        }

        if($status){
            // Siapkan data identitas lengkap untuk update
            $identity_fields = [
                'username', 'nama_lengkap', 'level', 
                'email', 'telepon', 'alamat', 'tempat_lahir', 
                'tanggal_lahir', 'jenis_kelamin', 'nip_nim'
            ];
            
            $update = [];
            foreach($identity_fields as $field) {
                $value = $this->input->post($field);
                // Jangan update field dengan nilai kosong kecuali untuk level
                if($field == 'level' || !empty($value)) {
                    $update[$field] = $value;
                }
            }
            
            // Tambahkan timestamp update
            $update['updated_at'] = date('Y-m-d H:i:s');
            
            $this->M_user->update_user($update, $user_code);
            $this->session->set_flashdata('message', 'Berhasil update data user dengan identitas lengkap');
            redirect('hubin/view/daftar-siswa');
        }

        $data['content'] = $content; 
        $this->load->view('template', $data, FALSE);
    }

    function hapus_user($user_code = null)
    {
        $this->load->model('M_user');
        $cekusers = $this->M_user->get_data_user($user_code);
        if($cekusers){
            $this->M_user->delete_user($user_code);
            $this->session->set_flashdata('message', 'Berhasil menghapus user');
            // Check if request is AJAX
            if($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'success', 'message' => 'User deleted successfully']);
                return;
            }
            redirect('hubin/view/daftar-siswa');
        }else{
            if($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'error', 'message' => 'User not found']);
                return;
            }
            echo "<script>alert('User tidak ditemukan');</script>";
            redirect('hubin/view/daftar-siswa');
        }        
    }

    // Reset password user (untuk siswa/pembimbing yang lupa password)
    function reset_password_user($user_code = null)
    {
        $this->load->model('M_user');
        $user = $this->M_user->get_data_user($user_code);
        
        if(!$user){
            $this->session->set_flashdata('error_message', 'User tidak ditemukan');
            redirect('hubin/view/daftar-siswa');
        }
        
        // Generate password baru (default: username123)
        $new_password = $user->username . '123';
        $hash = password_hash($new_password, PASSWORD_DEFAULT);
        
        $update = array('password' => $hash);
        $this->M_user->update_user($update, $user_code);
        
        $this->session->set_flashdata('message', 'Password berhasil direset. Password baru: <strong>' . $new_password . '</strong>');
        $this->session->set_flashdata('reset_password_username', $user->username);
        $this->session->set_flashdata('reset_password_new', $new_password);
        
        redirect('hubin/view/daftar-siswa');
    }
        
    // Ubah password user (dengan form)
    function ubah_password_user($user_code = null)
    {
        $this->load->model('M_user');
        $user = $this->M_user->get_data_user($user_code);
            
        if(!$user){
            $this->session->set_flashdata('error_message', 'User tidak ditemukan');
            redirect('hubin/view/daftar-siswa');
        }
            
        $data['user'] = $user;
        $data['content'] = 'hubin/ubah-password-user';
        $this->load->view('template', $data, FALSE);
    }
        
    // Proses ubah password user
    function proses_ubah_password_user()
    {
        $status = true;
        $user_code = $this->input->post('user_code');
        $this->load->model('M_user');
        $user = $this->M_user->get_data_user($user_code);
        
        if(!$user){
            $status = false;
            $this->session->set_flashdata('error_message', 'User tidak ditemukan');
        }
        
        $this->form_validation->set_rules('password_baru', 'Password Baru', 'required|min_length[6]');
        $this->form_validation->set_rules('password_konfirmasi', 'Konfirmasi Password', 'required|matches[password_baru]');
        
        if ($this->form_validation->run() == FALSE) {
            $status = false;
        }
        
        if($status){
            $hash = password_hash($this->input->post('password_baru'), PASSWORD_DEFAULT);
            $update = array('password' => $hash);
            $this->M_user->update_user($update, $user_code);
            
            $this->session->set_flashdata('message', 'Password berhasil diubah untuk user: ' . $user->nama_lengkap);
            redirect('hubin/view/daftar-siswa');
        } else {
            $data['user'] = $user;
            $data['content'] = 'hubin/ubah-password-user';
            $this->load->view('template', $data, FALSE);
        }
    }

    // Approve Pengajuan PKL
    function approve_pengajuan($siswa_code)
    {
        $this->load->model('M_siswa');
        $siswa = $this->M_siswa->get_data_siswa($siswa_code);
        
        if($siswa){
            // Check if student selected 'other' company
            if($siswa->other_dudi_nama){
                // Show form to decide whether to add company as mitra or not
                $data['siswa'] = $siswa;
                $data['content'] = 'hubin/approve-pengajuan-new-company';
                $this->load->view('template', $data, FALSE);
                return;
            }
            
            // Update status to disetujui
            $update = array_merge(array('status_pengajuan' => 'disetujui'), $this->get_pkl_period_data($siswa));
            $this->M_siswa->update_siswa($update, $siswa_code);
            
            // Set success message
            $this->session->set_flashdata('message', 'Pengajuan PKL berhasil disetujui dan telah dipindahkan ke Data Siswa. Silakan assign pembimbing untuk siswa ini.');
            
            // Redirect to data-siswa page (approved submissions are now visible there)
            redirect('hubin/view/assign-pembimbing');
        } else {
            $this->session->set_flashdata('error_message', 'Data siswa tidak ditemukan');
            redirect('hubin/view/pengajuan');
        }
    }

    function tolak_pengajuan($siswa_code)
    {
        $this->load->model('M_siswa');
        $siswa = $this->M_siswa->get_data_siswa($siswa_code);
        if($siswa){
            $update = array('status_pengajuan' => 'ditolak');
            $this->M_siswa->update_siswa($update, $siswa_code);
            $this->session->set_flashdata('message', 'Pengajuan PKL ditolak dan telah dihapus dari daftar pengajuan');
        }
        redirect('hubin/view/pengajuan');
    }
    
    function hapus_pengajuan($siswa_code)
    {
        $this->load->model('M_siswa');
        $siswa = $this->M_siswa->get_data_siswa($siswa_code);
        
        if($siswa){
            // Delete uploaded files if they exist
            if($siswa->surat_permohonan) {
                $surat_permohonan_path = './uploads/pengajuan/' . $siswa->surat_permohonan;
                if(file_exists($surat_permohonan_path)) {
                    unlink($surat_permohonan_path);
                }
            }
            
            if($siswa->surat_balasan) {
                $surat_balasan_path = './uploads/pengajuan/' . $siswa->surat_balasan;
                if(file_exists($surat_balasan_path)) {
                    unlink($surat_balasan_path);
                }
            }
            
            // Delete the student record
            $result = $this->M_siswa->hapus_siswa($siswa_code);
            if($result) {
                $this->session->set_flashdata('message', 'Pengajuan PKL berhasil dihapus');
            } else {
                $this->session->set_flashdata('message', 'Gagal menghapus pengajuan PKL');
            }
        } else {
            $this->session->set_flashdata('message', 'Data pengajuan tidak ditemukan');
        }
        redirect('hubin/view/pengajuan');
    }
    
    // Detail Perusahaan yang Diajukan Siswa
    function detail_perusahaan_ajukan_siswa($siswa_code)
    {
        $this->load->model('M_siswa');
        $this->load->model('M_dudi');
        
        $siswa = $this->M_siswa->get_data_siswa($siswa_code);
        
        if(!$siswa || !$siswa->other_dudi_nama){
            $this->session->set_flashdata('error_message', 'Data perusahaan tidak ditemukan atau bukan perusahaan ajukan siswa');
            redirect('hubin/view/pengajuan');
            return;
        }
        
        // Get student data
        $this->db->select('tb_siswa.*');
        $this->db->from('tb_siswa');
        // Tidak lagi join dengan tb_user karena siswa tidak terhubung dengan user
        $this->db->where('tb_siswa.siswa_code', $siswa_code);
        $siswa_detail = $this->db->get()->row();
        
        $data['siswa'] = $siswa_detail;
        $data['proposed_company_name'] = $siswa->other_dudi_nama;
        $data['content'] = 'hubin/detail-perusahaan-ajukan-siswa';
        $this->load->view('template', $data, FALSE);
    }
    
    function process_proposed_company()
    {
        $this->load->model('M_siswa');
        $this->load->model('M_dudi');
        
        $siswa_code = $this->input->post('siswa_code');
        $proposed_company_name = $this->input->post('proposed_company_name');
        $action = $this->input->post('action');
        
        $siswa = $this->M_siswa->get_data_siswa($siswa_code);
        
        if(!$siswa) {
            $this->session->set_flashdata('error_message', 'Data siswa tidak ditemukan');
            redirect('hubin/view/pengajuan');
            return;
        }
        
        switch($action) {
            case 'add_as_mitra':
                // Add the proposed company as a mitra DUDI with student-entered information
                $dudi_data = array(
                    'dudi_nama' => $proposed_company_name,
                    'dudi_alamat' => $siswa->other_dudi_alamat ?: 'Alamat belum diisi',
                    'dudi_telepon' => $siswa->other_dudi_telepon,
                    'dudi_email' => $siswa->other_dudi_email,
                    'dudi_pic' => $siswa->other_dudi_pic,
                    'dudi_nip_pic' => $siswa->other_dudi_nip_pic,
                    'dudi_instruktur' => $siswa->other_dudi_instruktur,
                    'dudi_nip_instruktur' => $siswa->other_dudi_nip_instruktur,
                    'latitude' => $siswa->other_dudi_latitude,
                    'longitude' => $siswa->other_dudi_longitude,
                    'is_mitra' => 1,  // Mark as mitra
                    'status_kerjasama' => 'mitra',  // Consistent status
                    'sumber_data' => 'siswa'  // Mark as student-submitted
                );
                
                $this->M_dudi->tambah_dudi($dudi_data);
                
                // Get the newly created DUDI ID
                $this->db->where('dudi_nama', $proposed_company_name);
                $new_dudi = $this->db->get('tb_dudi')->row();
                
                if($new_dudi) {
                    // Update student record to link with the new DUDI
                    $update_siswa = array(
                        'dudi_id' => $new_dudi->dudi_id,
                        'status_pengajuan' => 'disetujui',
                        'other_dudi_nama' => null  // Clear the proposed company name
                    );
                    $update_siswa = array_merge($update_siswa, $this->get_pkl_period_data($siswa));
                    $this->M_siswa->update_siswa($update_siswa, $siswa_code);
                    
                    $this->session->set_flashdata('message', 'Pengajuan PKL berhasil disetujui dan telah dipindahkan ke Data Siswa. Silakan assign pembimbing untuk siswa ini.');
                    // Redirect to data-siswa page (approved submissions are now visible there)
                    redirect('hubin/view/assign-pembimbing');
                    return;
                } else {
                    $this->session->set_flashdata('error_message', 'Terjadi kesalahan saat menambahkan perusahaan');
                }
                break;
                
            case 'add_as_nonmitra':
                // Add the proposed company as a non-mitra DUDI with student-entered information
                $dudi_data = array(
                    'dudi_nama' => $proposed_company_name,
                    'dudi_alamat' => $siswa->other_dudi_alamat ?: 'Alamat belum diisi',
                    'dudi_telepon' => $siswa->other_dudi_telepon,
                    'dudi_email' => $siswa->other_dudi_email,
                    'dudi_pic' => $siswa->other_dudi_pic,
                    'dudi_nip_pic' => $siswa->other_dudi_nip_pic,
                    'dudi_instruktur' => $siswa->other_dudi_instruktur,
                    'dudi_nip_instruktur' => $siswa->other_dudi_nip_instruktur,
                    'latitude' => $siswa->other_dudi_latitude,
                    'longitude' => $siswa->other_dudi_longitude,
                    'is_mitra' => 0,  // Mark as non-mitra
                    'status_kerjasama' => 'non_mitra',  // Consistent status
                    'sumber_data' => 'siswa'  // Mark as student-submitted
                );
                
                $this->M_dudi->tambah_dudi($dudi_data);
                
                // Get the newly created DUDI ID
                $this->db->where('dudi_nama', $proposed_company_name);
                $new_dudi = $this->db->get('tb_dudi')->row();
                
                if($new_dudi) {
                    // Update student record to link with the new DUDI
                    $update_siswa = array(
                        'dudi_id' => $new_dudi->dudi_id,
                        'status_pengajuan' => 'disetujui',
                        'other_dudi_nama' => null  // Clear the proposed company name
                    );
                    $update_siswa = array_merge($update_siswa, $this->get_pkl_period_data($siswa));
                    $this->M_siswa->update_siswa($update_siswa, $siswa_code);
                    
                    $this->session->set_flashdata('message', 'Pengajuan PKL berhasil disetujui dan telah dipindahkan ke Data Siswa. Silakan assign pembimbing untuk siswa ini.');
                    // Redirect to data-siswa page (approved submissions are now visible there)
                    redirect('hubin/view/assign-pembimbing');
                    return;
                } else {
                    $this->session->set_flashdata('error_message', 'Terjadi kesalahan saat menambahkan perusahaan');
                }
                break;
                
            case 'reject':
                // Simply reject the application without adding the company
                $update_siswa = array(
                    'status_pengajuan' => 'ditolak',
                    'other_dudi_nama' => null  // Clear the proposed company name
                );
                $this->M_siswa->update_siswa($update_siswa, $siswa_code);
                $this->session->set_flashdata('message', 'Pengajuan PKL ditolak');
                break;
                
            default:
                $this->session->set_flashdata('error_message', 'Aksi tidak valid');
                break;
        }
        
        redirect('hubin/view/pengajuan');
    }

    function update_profile()
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

        $this->form_validation->set_rules('username', 'Username', 'required|alpha_numeric');
        $this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'required');
        
        if ($this->form_validation->run() == FALSE) {
            $status = false;
        }

        if($status){
            $update = array(
                'username' => $this->input->post('username'),
                'nama_lengkap' => $this->input->post('nama_lengkap'),
            );

            // Handle upload foto
            if(!empty($_FILES['foto_profil']['name'])){
                $config['upload_path'] = './uploads/profil/';
                $config['allowed_types'] = 'jpg|jpeg|png|gif';
                $config['max_size'] = 2048; // 2MB
                $config['file_name'] = 'profil_'.$user_code.'_'.time();
                
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
                    $update['foto_profil'] = $upload_data['file_name'];
                } else {
                    $this->session->set_flashdata('error_message', $this->upload->display_errors('', ''));
                }
            }
            
            $this->M_user->update_user_by_id($update, $user_code);
            
            // Get updated user data
            $updated_user = $this->M_user->get_user_by_id($user_code);
            
            $user_login = array(
                'userdata' => array(
                    'id' => $userdata['id'],
                    'username' => $update['username'],
                    'nama_lengkap' => $update['nama_lengkap'],
                    'level' => $userdata['level'],
                    'group_name' => $userdata['group_name'],
                    'foto_profil' => isset($updated_user->foto_profil) ? $updated_user->foto_profil : null
                ),
            );
            $this->session->set_userdata($user_login);
            $this->session->set_flashdata('message', 'Profile berhasil diupdate');
            redirect('hubin/view/profile');
        } else {
            $data['user'] = $user;
            $data['content'] = 'hubin/profile';
            $this->load->view('template', $data, FALSE);
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
                redirect('hubin/view/change-password');
            }
        }

        $data['content'] = 'hubin/change-password';
        $this->load->view('template', $data, FALSE);
    }

    // ========== DUDI Management ==========
    function tambah_dudi()
    {
        $status = true;
        $this->form_validation->set_rules('dudi_nama', 'Nama DUDI', 'required|is_unique[tb_dudi.dudi_nama]');
        $this->form_validation->set_rules('dudi_alamat', 'Alamat DUDI', 'required');
        
        // Validasi email jika diisi
        if($this->input->post('dudi_email')) {
            $this->form_validation->set_rules('dudi_email', 'Email DUDI', 'valid_email|is_unique[tb_dudi.dudi_email]');
        }
        
        if ($this->form_validation->run() == FALSE) {
            $status = false;
        }

        if($status){
            $this->load->model('M_dudi');
            $data = array(
                'dudi_nama' => $this->input->post('dudi_nama'),
                'dudi_alamat' => $this->input->post('dudi_alamat'),
                'dudi_telepon' => $this->input->post('dudi_telepon'),
                'dudi_email' => $this->input->post('dudi_email'),
                'dudi_pic' => $this->input->post('dudi_pic'),
                'dudi_nip_pic' => $this->input->post('dudi_nip_pic'),
                'dudi_instruktur' => $this->input->post('dudi_instruktur'),
                'dudi_nip_instruktur' => $this->input->post('dudi_nip_instruktur'),
                'latitude' => $this->input->post('latitude') !== '' ? $this->input->post('latitude') : null,
                'longitude' => $this->input->post('longitude') !== '' ? $this->input->post('longitude') : null,
                'radius' => $this->input->post('radius') !== '' ? (int)$this->input->post('radius') : 100,
                'is_mitra' => $this->input->post('is_mitra') ? 1 : 0,
                'status_kerjasama' => $this->input->post('is_mitra') ? 'mitra' : 'non_mitra'
            );
            $this->M_dudi->tambah_dudi($data);
            $this->session->set_flashdata('message', 'Berhasil menambah data DUDI beserta konfigurasi lokasi radius presensi');
            redirect('hubin/view/data-dudi');
        }else{
            $this->view('tambah-dudi');
        }
    }

    function import_dudi()
    {
        if (empty($_FILES['dudi_file']['name'])) {
            $this->session->set_flashdata('error_message', 'File import DUDI belum dipilih.');
            redirect('hubin/view/tambah-dudi');
        }

        $config['upload_path'] = './uploads/imports';
        $config['allowed_types'] = 'csv|xlsx|xls';
        $config['max_size'] = 2048;
        $config['encrypt_name'] = TRUE;

        if (!is_dir($config['upload_path'])) {
            mkdir($config['upload_path'], 0777, true);
        }

        $this->load->library('upload', $config);
        if (!$this->upload->do_upload('dudi_file')) {
            $this->session->set_flashdata('error_message', $this->upload->display_errors('', ''));
            redirect('hubin/view/tambah-dudi');
        }

        $upload_data = $this->upload->data();
        $file_path = $upload_data['full_path'];
        $file_ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
        $rows = [];

        if ($file_ext === 'csv') {
            $handle = fopen($file_path, 'r');
            if ($handle === false) {
                $this->session->set_flashdata('error_message', 'File CSV tidak dapat dibaca.');
                redirect('hubin/view/tambah-dudi');
            }

            $headers = fgetcsv($handle, 2000, ',');
            if ($headers === false) {
                fclose($handle);
                unlink($file_path);
                $this->session->set_flashdata('error_message', 'File CSV kosong atau tidak valid.');
                redirect('hubin/view/tambah-dudi');
            }

            $normalized_headers = [];
            foreach ($headers as $header) {
                $normalized_headers[] = strtolower(trim((string) $header));
            }

            while (($row = fgetcsv($handle, 2000, ',')) !== false) {
                if (count($row) < 1) {
                    continue;
                }

                $mapped = [];
                foreach ($row as $idx => $value) {
                    $key = $normalized_headers[$idx] ?? 'column_' . $idx;
                    $mapped[$key] = trim((string) $value);
                }

                $rows[] = [
                    'dudi_nama' => $mapped['nama dudi'] ?? $mapped['nama_dudi'] ?? $mapped['dudi_nama'] ?? '',
                    'dudi_alamat' => $mapped['alamat'] ?? $mapped['dudi_alamat'] ?? '',
                    'dudi_telepon' => $mapped['telepon'] ?? $mapped['dudi_telepon'] ?? '',
                    'dudi_email' => $mapped['email'] ?? $mapped['dudi_email'] ?? '',
                    'dudi_pic' => $mapped['penanggung jawab (pic)'] ?? $mapped['penanggung_jawab'] ?? $mapped['dudi_pic'] ?? '',
                    'dudi_nip_pic' => $mapped['nip pic'] ?? $mapped['nip_pic'] ?? $mapped['dudi_nip_pic'] ?? '',
                    'dudi_instruktur' => $mapped['instruktur'] ?? $mapped['dudi_instruktur'] ?? '',
                    'dudi_nip_instruktur' => $mapped['nip instruktur'] ?? $mapped['nip_instruktur'] ?? $mapped['dudi_nip_instruktur'] ?? '',
                    'status_kerjasama' => $mapped['status mitra'] ?? $mapped['status_kerjasama'] ?? $mapped['is_mitra'] ?? 'mitra',
                    'latitude' => $mapped['latitude'] ?? '',
                    'longitude' => $mapped['longitude'] ?? '',
                    'radius' => $mapped['radius toleransi (meter)'] ?? $mapped['radius'] ?? 100,
                ];
            }

            fclose($handle);
        } else {
            if (!class_exists('PhpOffice\\PhpSpreadsheet\\IOFactory')) {
                $this->session->set_flashdata('error_message', 'Library PhpSpreadsheet belum terinstall untuk import Excel.');
                unlink($file_path);
                redirect('hubin/view/tambah-dudi');
            }

            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file_path);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows_raw = $worksheet->toArray();

            if (empty($rows_raw)) {
                unlink($file_path);
                $this->session->set_flashdata('error_message', 'File Excel kosong.');
                redirect('hubin/view/tambah-dudi');
            }

            $headers = array_shift($rows_raw);
            $normalized_headers = [];
            foreach ($headers as $header) {
                $normalized_headers[] = strtolower(trim((string) $header));
            }

            foreach ($rows_raw as $row) {
                if (empty(array_filter($row, function($cell) { return $cell !== null && trim((string) $cell) !== ''; }))) {
                    continue;
                }

                $mapped = [];
                foreach ($row as $idx => $value) {
                    $key = $normalized_headers[$idx] ?? 'column_' . $idx;
                    $mapped[$key] = trim((string) $value);
                }

                $rows[] = [
                    'dudi_nama' => $mapped['nama dudi'] ?? $mapped['nama_dudi'] ?? $mapped['dudi_nama'] ?? '',
                    'dudi_alamat' => $mapped['alamat'] ?? $mapped['dudi_alamat'] ?? '',
                    'dudi_telepon' => $mapped['telepon'] ?? $mapped['dudi_telepon'] ?? '',
                    'dudi_email' => $mapped['email'] ?? $mapped['dudi_email'] ?? '',
                    'dudi_pic' => $mapped['penanggung jawab (pic)'] ?? $mapped['penanggung_jawab'] ?? $mapped['dudi_pic'] ?? '',
                    'dudi_nip_pic' => $mapped['nip pic'] ?? $mapped['nip_pic'] ?? $mapped['dudi_nip_pic'] ?? '',
                    'dudi_instruktur' => $mapped['instruktur'] ?? $mapped['dudi_instruktur'] ?? '',
                    'dudi_nip_instruktur' => $mapped['nip instruktur'] ?? $mapped['nip_instruktur'] ?? $mapped['dudi_nip_instruktur'] ?? '',
                    'status_kerjasama' => $mapped['status mitra'] ?? $mapped['status_kerjasama'] ?? $mapped['is_mitra'] ?? 'mitra',
                    'latitude' => $mapped['latitude'] ?? '',
                    'longitude' => $mapped['longitude'] ?? '',
                    'radius' => $mapped['radius toleransi (meter)'] ?? $mapped['radius'] ?? 100,
                ];
            }
        }

        unlink($file_path);

        $this->load->model('M_dudi');
        $result = $this->M_dudi->import_dudi_batch($rows);

        if ($result['inserted'] > 0 || $result['updated'] > 0) {
            $message = 'Import DUDI berhasil: ' . $result['inserted'] . ' ditambahkan, ' . $result['updated'] . ' diperbarui.';
            if (!empty($result['errors'])) {
                $message .= ' Beberapa baris dilewati: ' . count($result['errors']);
            }
            $this->session->set_flashdata('message', $message);
        } else {
            $this->session->set_flashdata('error_message', 'Tidak ada data DUDI yang diproses. ' . (count($result['errors']) > 0 ? implode(' | ', array_slice($result['errors'], 0, 5)) : 'Periksa format file anda.'));
        }

        redirect('hubin/view/tambah-dudi');
    }

    function edit_dudi($dudi_code = null)
    {
        $this->load->model('M_dudi');
        $dudi = $this->M_dudi->get_dudi($dudi_code);
        if($dudi){
            $data['dudi'] = $dudi;
            $data['content'] = 'hubin/edit-dudi';
        }else{
            $data['content'] = '404';
        }
        $this->load->view('template', $data, FALSE);
    }

    function update_dudi()
    {
        $status = true;
        $content = 'hubin/edit-dudi';
        $dudi_code = $this->input->post('dudi_code');
        $this->load->model('M_dudi');
        $cekdudi = $this->M_dudi->get_dudi($dudi_code);
        if(!$cekdudi){
            $status = false;
            $content = '404';
        }else{
            $data['dudi'] = $cekdudi;
        }
        
        $this->form_validation->set_rules('dudi_code', 'code', 'required');
        $this->form_validation->set_rules('dudi_nama', 'Nama DUDI', 'required|callback_dudi_nama_check['.$dudi_code.']');
        $this->form_validation->set_rules('dudi_alamat', 'Alamat DUDI', 'required');
        
        // Validasi email jika diisi
        if($this->input->post('dudi_email')) {
            $this->form_validation->set_rules('dudi_email', 'Email DUDI', 'valid_email|callback_dudi_email_check['.$dudi_code.']');
        }
        if ($this->form_validation->run() == FALSE) {
            $status = false;
        }

        if($status){
            $update = array(
                'dudi_nama' => $this->input->post('dudi_nama'),
                'dudi_alamat' => $this->input->post('dudi_alamat'),
                'dudi_telepon' => $this->input->post('dudi_telepon'),
                'dudi_email' => $this->input->post('dudi_email'),
                'dudi_pic' => $this->input->post('dudi_pic'),
                'dudi_nip_pic' => $this->input->post('dudi_nip_pic'),
                'dudi_instruktur' => $this->input->post('dudi_instruktur'),
                'dudi_nip_instruktur' => $this->input->post('dudi_nip_instruktur'),
                'latitude' => $this->input->post('latitude') !== '' ? $this->input->post('latitude') : null,
                'longitude' => $this->input->post('longitude') !== '' ? $this->input->post('longitude') : null,
                'radius' => $this->input->post('radius') !== '' ? (int)$this->input->post('radius') : 100,
                'is_mitra' => $this->input->post('is_mitra') ? 1 : 0  // Update is_mitra field
            );
            $this->M_dudi->update_dudi($update, $dudi_code);
            $this->session->set_flashdata('message', 'Berhasil update data DUDI');
            redirect('hubin/view/data-dudi');
        }

        $data['content'] = $content; 
        $this->load->view('template', $data, FALSE);
    }

    function hapus_dudi($dudi_code = null)
    {
        $this->load->model('M_dudi');
        $cekdudi = $this->M_dudi->get_dudi($dudi_code);
        if($cekdudi){
            $this->M_dudi->delete_dudi($dudi_code);
            $this->session->set_flashdata('message', 'Berhasil menghapus data DUDI');
            // Check if request is AJAX
            if($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'success', 'message' => 'DUDI deleted successfully']);
                return;
            }
            redirect('hubin/view/data-dudi');
        }else{
            if($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'error', 'message' => 'DUDI not found']);
                return;
            }
            echo "<script>alert('Data DUDI tidak ditemukan');</script>";
            redirect('hubin/view/data-dudi');
        }        
    }
    
    function batch_delete_dudi()
    {
        $this->load->model('M_dudi');
        
        $selected_dudi = $this->input->post('selected_dudi');
        
        if (!$selected_dudi || !is_array($selected_dudi)) {
            $this->session->set_flashdata('error_message', 'Tidak ada data DUDI yang dipilih');
            redirect('hubin/view/data-dudi');
            return;
        }
        
        $deleted_count = 0;
        $failed_count = 0;
        
        foreach($selected_dudi as $dudi_code) {
            $cekdudi = $this->M_dudi->get_dudi($dudi_code);
            if($cekdudi) {
                if($this->M_dudi->delete_dudi($dudi_code)) {
                    $deleted_count++;
                } else {
                    $failed_count++;
                }
            } else {
                $failed_count++;
            }
        }
        
        if ($deleted_count > 0) {
            $this->session->set_flashdata('message', 'Berhasil menghapus ' . $deleted_count . ' data DUDI' . ($failed_count > 0 ? ' (' . $failed_count . ' gagal)' : ''));
        } else {
            $this->session->set_flashdata('error_message', 'Tidak ada data DUDI yang berhasil dihapus' . ($failed_count > 0 ? ' (' . $failed_count . ' gagal)' : ''));
        }
        
        redirect('hubin/view/data-dudi');
    }

    // Detail DUDI dengan informasi siswa yang PKL di sana
    function detail_dudi($dudi_code = null)
    {
        $this->load->model('M_dudi');
        $this->load->model('M_siswa');
        
        // Get DUDI data
        $dudi = $this->M_dudi->get_dudi($dudi_code);
        if(!$dudi){
            $this->session->set_flashdata('error_message', 'Data DUDI tidak ditemukan');
            redirect('hubin/view/data-dudi');
        }
        
        // Get students who are doing PKL at this company or proposed this company
        $this->db->select('s.siswa_id, s.siswa_nama, s.siswa_kelas, s.siswa_jurusan, s.status_pengajuan, s.periode, u.nama_lengkap, s.other_dudi_alamat, s.other_dudi_telepon, s.other_dudi_email, s.other_dudi_pic, s.other_dudi_nip_pic, s.other_dudi_instruktur, s.other_dudi_nip_instruktur, s.other_dudi_nama');
        $this->db->from('tb_siswa s');
        $this->db->join('tb_user u', 'u.id = s.user_id', 'left');
        $escaped_nama = $this->db->escape_str($dudi->dudi_nama);
        $this->db->where("(s.dudi_id = {$dudi->dudi_id} OR s.other_dudi_nama = '$escaped_nama')");
        $this->db->order_by('s.siswa_nama', 'ASC');
        $students = $this->db->get()->result();
        
        // If this is a student-submitted company, use the data from the DUDI table itself
        // since the data was copied there during the approval process
        if($dudi->sumber_data == 'siswa') {
            // Add DUDI table data to dudi object for display (this was copied from student during approval)
            $dudi->student_filled_data = [
                'alamat' => $dudi->dudi_alamat,
                'telepon' => $dudi->dudi_telepon,
                'email' => $dudi->dudi_email,
                'pic' => $dudi->dudi_pic,
                'nip_pic' => $dudi->dudi_nip_pic,
                'instruktur' => $dudi->dudi_instruktur,
                'nip_instruktur' => $dudi->dudi_nip_instruktur
            ];
        }
        
        $data['dudi'] = $dudi;
        $data['students'] = $students;
        $data['content'] = 'hubin/detail-dudi';
        $this->load->view('template', $data, FALSE);
    }

    // ========== Siswa Management ==========
    function tambah_siswa()
    {
        $status = true;
        $this->load->model('M_siswa');

        $submitted_status = $this->M_siswa->normalize_status_pengajuan($this->input->post('status_pengajuan'));
        $_POST['status_pengajuan'] = $submitted_status;
        
        // Validate student data fields
        $this->form_validation->set_rules('siswa_nama', 'Nama Siswa', 'required');
        $this->form_validation->set_rules('siswa_kelas', 'Kelas Siswa', 'required');
        $this->form_validation->set_rules('siswa_jk', 'Jenis Kelamin', 'required|in_list[L,P]');
        $this->form_validation->set_rules('status_pengajuan', 'Status Pengajuan', 'required|in_list[draft,menunggu,disetujui,ditolak,selesai]');
        
        // Validate NIS/NISN uniqueness
        $nis = $this->input->post('siswa_nis');
        $nisn = $this->input->post('siswa_nisn');
        
        // More comprehensive duplicate checking
        if($nis) {
            $this->db->where('siswa_nis', $nis);
            $existing_nis = $this->db->get('tb_siswa')->row();
            if($existing_nis) {
                $this->form_validation->set_rules('siswa_nis', 'NIS', 'required|is_unique[tb_siswa.siswa_nis]');
            }
        }
        
        if($nisn) {
            $this->db->where('siswa_nisn', $nisn);
            $existing_nisn = $this->db->get('tb_siswa')->row();
            if($existing_nisn) {
                $this->form_validation->set_rules('siswa_nisn', 'NISN', 'required|is_unique[tb_siswa.siswa_nisn]');
            }
        }
        
        // Validate user account fields
        $this->form_validation->set_rules('username', 'Username', 'required|alpha_numeric|min_length[3]');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        
        if ($this->form_validation->run() == FALSE) {
            $status = false;
        }

        if($status){
            $this->load->model('M_user');

            // Pastikan kolom status_pengajuan di DB mendukung seluruh alur yang valid.
            $this->db->query("ALTER TABLE `tb_siswa` MODIFY `status_pengajuan` ENUM('draft','menunggu','disetujui','ditolak','selesai') NOT NULL DEFAULT 'draft'");
            
            // Check for duplicate data before creation
            $duplicate_check = $this->check_duplicate_student($nis, $nisn, $this->input->post('siswa_nama'), $this->input->post('siswa_kelas'));
            if($duplicate_check !== true) {
                $this->session->set_flashdata('error_message', $duplicate_check);
                $this->view('tambah-siswa');
                return;
            }
            
            // Create user account first
            $user_code = $this->M_user->generateRandomString(30);
            $hashed_password = password_hash($this->input->post('password'), PASSWORD_DEFAULT);
            
            $user_data = array(
                'user_code' => $user_code,
                'username' => $this->input->post('username'),
                'password' => $hashed_password,
                'nama_lengkap' => $this->input->post('siswa_nama'),
                'email' => $this->input->post('email'),
                'telepon' => $this->input->post('siswa_telepon'),
                'alamat' => $this->input->post('siswa_alamat'),
                'tempat_lahir' => $this->input->post('siswa_tempat_lahir'),
                'tanggal_lahir' => $this->input->post('siswa_tanggal_lahir') ? date('Y-m-d', strtotime($this->input->post('siswa_tanggal_lahir'))) : NULL,
                'jenis_kelamin' => $this->input->post('siswa_jk'), // Store gender in user table
                'level' => 3, // Student level
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            );
            
            // Check if user with same username or email already exists
            $existing_user = null;
            
            // First check by username
            $this->db->where('username', $this->input->post('username'));
            $existing_user = $this->db->get('tb_user')->row();
            
            if (!$existing_user) {
                // Check by email if username doesn't match
                $this->db->where('email', $this->input->post('email'));
                $existing_user = $this->db->get('tb_user')->row();
            }
            
            if ($existing_user) {
                // Use existing user
                $user = $existing_user;
            } else {
                // Create new user
                $this->M_user->tambah_data_user($user_data);
                
                // Get the created user ID
                $this->db->where('username', $this->input->post('username'));
                $user = $this->db->get('tb_user')->row();
            }
            
            if($user) {
                // Create student record with all fields from form
                $code = $this->M_siswa->generateRandomString(30);
                $data = array(
                    'siswa_nama' => $this->input->post('siswa_nama'),
                    'siswa_kelas' => $this->input->post('siswa_kelas'),
                    'siswa_jurusan' => $this->input->post('siswa_jurusan'),
                    'siswa_nis' => $nis,
                    'siswa_nisn' => $nisn,
                    'dudi_id' => $this->input->post('dudi_id') ? $this->input->post('dudi_id') : NULL,
                    'status_pengajuan' => $this->M_siswa->normalize_status_pengajuan($this->input->post('status_pengajuan')),
                    'periode' => $this->input->post('periode'),
                    'user_id' => $user->id,
                    'siswa_code' => $code,
                    'tanggal_mulai' => $this->input->post('tanggal_mulai'),
                    'tanggal_selesai' => $this->input->post('tanggal_selesai'),
                    'lama_pelaksanaan' => $this->input->post('lama_pelaksanaan')
                );
                
                // Add optional fields only if they exist in the database
                $fields_to_add = array(
                    'siswa_tempat_lahir' => $this->input->post('siswa_tempat_lahir'),
                    'siswa_tanggal_lahir' => $this->input->post('siswa_tanggal_lahir') ? date('Y-m-d', strtotime($this->input->post('siswa_tanggal_lahir'))) : NULL,
                    'siswa_alamat' => $this->input->post('siswa_alamat'),
                    'siswa_telepon' => $this->input->post('siswa_telepon'),
                    'siswa_asal_sekolah' => $this->input->post('siswa_asal_sekolah')
                );
                
                // Check which fields exist in the database
                foreach($fields_to_add as $field => $value) {
                    if($this->column_exists('tb_siswa', $field) && !empty($value)) {
                        $data[$field] = $value;
                    }
                }
                
                // Remove empty values
                $data = array_filter($data, function($value) {
                    return $value !== '' && $value !== null;
                });
                
                $this->M_siswa->tambah_data_siswa($data);
                
                // Set flashdata for success message with account details
                $this->session->set_flashdata('message', 'Berhasil menambah data siswa dan akun login');
                $this->session->set_flashdata('new_user_nama', $this->input->post('siswa_nama'));
                $this->session->set_flashdata('new_user_username', $this->input->post('username'));
                $this->session->set_flashdata('new_user_password', $this->input->post('password'));
                
                redirect('hubin/view/daftar-siswa');
            } else {
                $this->session->set_flashdata('error_message', 'Gagal membuat akun user');
                $this->view('tambah-siswa');
            }
        }else{
            $this->view('tambah-siswa');
        }
    }
    
    /**
     * Check for duplicate student data
     * @param string $nis
     * @param string $nisn
     * @param string $nama
     * @param string $kelas
     * @return bool|string True if no duplicates, error message if duplicates found
     */
    private function check_duplicate_student($nis = null, $nisn = null, $nama = null, $kelas = null)
    {
        // Comprehensive NIS duplicate check
        if($nis) {
            $this->db->where('siswa_nis', $nis);
            $existing_nis = $this->db->get('tb_siswa')->row();
            if($existing_nis) {
                return 'NIS sudah terdaftar di sistem dengan nama: ' . $existing_nis->siswa_nama . ' dari kelas ' . $existing_nis->siswa_kelas . '. Silakan gunakan NIS yang berbeda.';
            }
        }
        
        // Comprehensive NISN duplicate check
        if($nisn) {
            $this->db->where('siswa_nisn', $nisn);
            $existing_nisn = $this->db->get('tb_siswa')->row();
            if($existing_nisn) {
                return 'NISN sudah terdaftar di sistem dengan nama: ' . $existing_nisn->siswa_nama . ' dari kelas ' . $existing_nisn->siswa_kelas . '. Silakan gunakan NISN yang berbeda.';
            }
        }
        
        // Check name + class duplicate (potential duplicate student)
        if($nama && $kelas) {
            $this->db->where('siswa_nama', $nama);
            $this->db->where('siswa_kelas', $kelas);
            $existing_student = $this->db->get('tb_siswa')->row();
            if($existing_student) {
                $nis_info = $existing_student->siswa_nis ? ' (NIS: ' . $existing_student->siswa_nis . ')' : '';
                $nisn_info = $existing_student->siswa_nisn ? ' (NISN: ' . $existing_student->siswa_nisn . ')' : '';
                return 'Siswa dengan nama dan kelas yang sama sudah terdaftar' . $nis_info . $nisn_info . '. Silakan periksa kembali data atau gunakan data yang berbeda.';
            }
        }
        
        return true;
    }
    
    /**
     * AJAX endpoint for checking duplicate NIS/NISN
     */
    public function check_duplicate($type = null, $value = null)
    {
        header('Content-Type: application/json');
        
        if(!$type || !$value) {
            echo json_encode(['exists' => false]);
            return;
        }
        
        $this->db->where('siswa_' . $type, $value);
        $count = $this->db->count_all_results('tb_siswa');
        
        if($count > 0) {
            echo json_encode([
                'exists' => true,
                'message' => strtoupper($type) . ' sudah terdaftar di sistem. Silakan gunakan ' . strtoupper($type) . ' yang berbeda.'
            ]);
        } else {
            echo json_encode(['exists' => false]);
        }
    }
    
    /**
     * AJAX endpoint for checking duplicate name + class combination
     */
    public function check_duplicate_name_class($nama = null, $kelas = null)
    {
        header('Content-Type: application/json');
        
        if(!$nama || !$kelas) {
            echo json_encode(['exists' => false]);
            return;
        }
        
        $this->db->where('siswa_nama', urldecode($nama));
        $this->db->where('siswa_kelas', urldecode($kelas));
        $count = $this->db->count_all_results('tb_siswa');
        
        if($count > 0) {
            echo json_encode([
                'exists' => true,
                'message' => 'Siswa dengan nama dan kelas yang sama sudah terdaftar. Silakan periksa kembali data.'
            ]);
        } else {
            echo json_encode(['exists' => false]);
        }
    }

    function read_siswa($siswa_code = null)
    {
        // Handle case when no siswa_code is provided
        if(empty($siswa_code)) {
            $this->session->set_flashdata('error_message', 'Parameter siswa tidak ditemukan');
            redirect('hubin/view/assign-pembimbing');
        }
        
        $this->load->model('M_siswa');
        $siswa = $this->M_siswa->get_siswa_with_biodata($siswa_code); // Use the new function
        if($siswa){
            $data['siswa'] = $siswa;
            $data['content'] = 'hubin/detail-siswa';
        }else{
            $this->session->set_flashdata('error_message', 'Data siswa tidak ditemukan');
            redirect('hubin/view/assign-pembimbing');
        }
        $this->load->view('template', $data, FALSE);
    }
    
    function edit_siswa($siswa_code = null)
    {
        $this->load->model('M_siswa');
        
        $siswa = $this->M_siswa->get_data_siswa($siswa_code);
        if($siswa){
            $data['siswa'] = $siswa;
            $data['content'] = 'hubin/edit-siswa';
        }else{
            $data['content'] = '404';
        }
        
        $this->load->view('template', $data, FALSE);
    }

    // ========== CRUD Biodata Lengkap User ==========
    
    // View detail biodata lengkap user (siswa atau pembimbing)
    function detail_biodata($user_code = null)
    {
        $this->load->model('M_user');
        $this->load->model('M_siswa');
        $this->load->model('M_pembimbing');
        
        $user = $this->M_user->get_data_user($user_code);
        if(!$user){
            $data['content'] = '404';
            $this->load->view('template', $data, FALSE);
            return;
        }
        
        $data['user'] = $user;
        
        // Add password information for display
        $data['password_info'] = array(
            'username' => $user->username,
            'level' => $user->level,
            'group_name' => $user->group_name,
            'is_active' => $user->is_active
        );
        
        // Jika user adalah siswa (level 3)
        if($user->level == 3){
            $siswa = $this->M_siswa->get_siswa_with_biodata_by_user_id($user->id);
            
            // If student record doesn't exist in tb_siswa, create a basic object with user data
            if(!$siswa) {
                $siswa = (object) array(
                    'siswa_id' => null,
                    'user_id' => $user->id,
                    'user_code' => $user->user_code,
                    'siswa_nama' => $user->nama_lengkap,
                    'nama_lengkap' => $user->nama_lengkap,
                    'username' => $user->username,
                    'siswa_nis' => $user->nip_nim,
                    'siswa_nisn' => null,
                    'siswa_kelas' => null,
                    'siswa_jurusan' => null,
                    'siswa_asal_sekolah' => 'SMK ITIKURIH HIBARNA',
                    'siswa_telepon' => $user->telepon,
                    'telepon' => $user->telepon,
                    'user_telepon' => $user->telepon,
                    'email' => $user->email,
                    'user_email' => $user->email,
                    'alamat' => $user->alamat,
                    'siswa_alamat' => $user->alamat,
                    'user_alamat' => $user->alamat,
                    'tempat_lahir' => $user->tempat_lahir,
                    'siswa_tempat_lahir' => $user->tempat_lahir,
                    'user_tempat_lahir' => $user->tempat_lahir,
                    'tanggal_lahir' => $user->tanggal_lahir,
                    'siswa_tanggal_lahir' => $user->tanggal_lahir,
                    'user_tanggal_lahir' => $user->tanggal_lahir,
                    'jenis_kelamin' => $user->jenis_kelamin,
                    'siswa_jk' => $user->jenis_kelamin,
                    'user_jenis_kelamin' => $user->jenis_kelamin,
                    'nip_nim' => $user->nip_nim,
                    'user_nip_nim' => $user->nip_nim,
                    'dudi_nama' => null,
                    'dudi_is_mitra' => null,
                    'status_pengajuan' => 'draft',
                    'periode' => null,
                    'tanggal_mulai' => null,
                    'tanggal_selesai' => null,
                    'lama_pelaksanaan' => null,
                    'pembimbing_nama' => null,
                    'pembimbing_email' => null,
                    'pembimbing_telepon' => null,
                    'updated_at' => $user->updated_at
                );
            } else {
                // Pastikan fallback nilai dari user jika di siswa kosong
                if(empty($siswa->siswa_nama)) $siswa->siswa_nama = $user->nama_lengkap;
                if(empty($siswa->siswa_nis)) $siswa->siswa_nis = $user->nip_nim;
                if(empty($siswa->siswa_telepon)) $siswa->siswa_telepon = $user->telepon;
                if(empty($siswa->siswa_alamat)) $siswa->siswa_alamat = $user->alamat;
                if(empty($siswa->siswa_tempat_lahir)) $siswa->siswa_tempat_lahir = $user->tempat_lahir;
                if(empty($siswa->siswa_tanggal_lahir)) $siswa->siswa_tanggal_lahir = $user->tanggal_lahir;
                if(empty($siswa->siswa_jk)) $siswa->siswa_jk = $user->jenis_kelamin;
                if(empty($siswa->siswa_asal_sekolah)) $siswa->siswa_asal_sekolah = 'SMK ITIKURIH HIBARNA';
                $siswa->user_email = $user->email;
                $siswa->nama_lengkap = $user->nama_lengkap;
            }
            
            $data['biodata'] = $siswa;
            $data['tipe_user'] = 'siswa';
            $data['content'] = 'hubin/detail-biodata-siswa';
        }
        // Untuk user lain (admin, dll)
        else {
            $data['biodata'] = $user;
            $data['tipe_user'] = 'lainnya';
            $data['content'] = 'hubin/detail-biodata-lainnya';
        }
        
        $this->load->view('template', $data, FALSE);
    }
    
    // Print biodata siswa
    function print_biodata_siswa($user_code = null)
    {
        $this->load->model('M_user');
        $this->load->model('M_siswa');
        
        $user = $this->M_user->get_data_user($user_code);
        if(!$user){
            show_404();
            return;
        }
        
        // Jika user adalah siswa (level 3)
        if($user->level == 3){
            $siswa = $this->M_siswa->get_siswa_with_biodata_by_user_id($user->id);
            
            $data['biodata'] = $siswa;
            
            $this->load->view('hubin/print-biodata-siswa', $data);
        } else {
            show_404();
        }
    }
    
    // Print biodata pembimbing
    function print_biodata_pembimbing($pembimbing_code = null)
    {
        $this->load->model('M_pembimbing');
        $this->load->model('M_pengelompokan');
        
        $pembimbing = $this->M_pembimbing->get_pembimbing_with_biodata($pembimbing_code);
        if(!$pembimbing){
            show_404();
            return;
        }
        
        $data['pembimbing'] = $pembimbing;
        $data['pembimbing_code'] = $pembimbing_code;
        
        // Get jumlah siswa bimbingan
        $siswa_query = $this->M_pengelompokan->get_siswa_by_pembimbing($pembimbing->pembimbing_id);
        $data['jumlah_siswa'] = $siswa_query->num_rows();
        
        $this->load->view('hubin/print-pembimbing', $data);
    }
    
    // Edit biodata for student
    function edit_biodata_siswa($user_code = null)
    {
        $this->load->model('M_user');
        $this->load->model('M_siswa');
        
        $user = $this->M_user->get_data_user($user_code);
        if(!$user){
            $data['content'] = '404';
            $this->load->view('template', $data, FALSE);
            return;
        }
        
        // Ensure the user is a student
        if($user->level != 3){
            $this->session->set_flashdata('error_message', 'User bukan merupakan siswa');
            redirect('hubin/view/daftar-siswa');
            return;
        }
        
        // Get student data
        $siswa = $this->M_siswa->get_siswa_with_biodata_by_user_id($user->id);
        
        $data['user'] = $user;
        $data['biodata'] = $siswa;
        $data['tipe_user'] = 'siswa';
        $data['content'] = 'hubin/edit-biodata-siswa';
            
        $this->load->view('template', $data, FALSE);
    }
        
    // Update biodata lengkap user
    function update_biodata()
    {
        // Only process POST requests
        if($this->input->method() !== 'post') {
            // If accessed via GET, redirect to a safe page
            redirect('hubin/view/daftar-siswa');
            return;
        }
        
        $status = true;
        $user_code = $this->input->post('user_code');
        $tipe_user = $this->input->post('tipe_user');
        
        // Debug POST data
        log_message('debug', 'POST data received: ' . print_r($this->input->post(), true));
        log_message('debug', 'User code: ' . $user_code);
        log_message('debug', 'Tipe user: ' . $tipe_user);
        log_message('debug', 'Request method: ' . $this->input->method());
        
        $this->load->model('M_user');
        $this->load->model('M_siswa');
        $this->load->model('M_pembimbing');
        
        $user = $this->M_user->get_data_user($user_code);
        if(!$user){
            $status = false;
            $this->session->set_flashdata('error_message', 'User tidak ditemukan');
        }
        
        if($status){
            // Validasi berdasarkan tipe user
            // For hubin editing, don't enforce required validation on student fields
            // since hubin might only want to update specific fields
            $has_validation_rules = false;
            
            if($tipe_user == 'pembimbing'){
                $this->form_validation->set_rules('pembimbing_nama', 'Nama Pembimbing', 'required');
                $this->form_validation->set_rules('nip_nim', 'NIP', 'required');
                $has_validation_rules = true;
            }
            
            // Add validation for student NIS/NISN uniqueness if updating student data
            if($tipe_user == 'siswa') {
                $nis = $this->input->post('siswa_nis');
                $nisn = $this->input->post('siswa_nisn');
                
                if($nis) {
                    // Get the current student record to exclude from uniqueness check
                    $current_student = $this->M_siswa->get_siswa_with_biodata_by_user_id($user->id);
                    if($current_student) {
                        $this->form_validation->set_rules('siswa_nis', 'NIS', 'callback_nis_check['.$current_student->siswa_code.']');
                    } else {
                        // If no current record exists, just check for general uniqueness
                        $this->form_validation->set_rules('siswa_nis', 'NIS', 'is_unique[tb_siswa.siswa_nis]');
                    }
                }
                
                if($nisn) {
                    // Get the current student record to exclude from uniqueness check
                    $current_student = $this->M_siswa->get_siswa_with_biodata_by_user_id($user->id);
                    if($current_student) {
                        $this->form_validation->set_rules('siswa_nisn', 'NISN', 'callback_nisn_check['.$current_student->siswa_code.']');
                    } else {
                        // If no current record exists, just check for general uniqueness
                        $this->form_validation->set_rules('siswa_nisn', 'NISN', 'is_unique[tb_siswa.siswa_nisn]');
                    }
                }
                $has_validation_rules = true;
            }
            
            // Hanya jalankan validasi jika ada aturan validasi yang ditetapkan
            if($has_validation_rules) {
                log_message('debug', 'Running form validation with rules');
                if ($this->form_validation->run() == FALSE) {
                    $status = false;
                    $validation_errors = validation_errors();
                    log_message('debug', 'Form validation failed: ' . $validation_errors);
                    $this->session->set_flashdata('error_message', 'Validasi form gagal: ' . $validation_errors);
                } else {
                    log_message('debug', 'Form validation passed');
                }
            } else {
                log_message('debug', 'No validation rules, skipping validation');
            }
        }
        
        $new_profile_photo = null;
        if($status){
            if (!empty($_FILES['foto_profil']['name'])) {
                $upload_path = FCPATH . 'uploads/profil/';
                if (!is_dir($upload_path)) {
                    mkdir($upload_path, 0777, true);
                }
                $config = [
                    'upload_path' => $upload_path,
                    'allowed_types' => 'jpg|jpeg|png|gif',
                    'max_size' => 2048,
                    'file_name' => 'profil_' . $user->id . '_' . time(),
                    'overwrite' => false
                ];
                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('foto_profil')) {
                    $this->session->set_flashdata('error_message', 'Upload foto gagal: ' . $this->upload->display_errors('', ''));
                    $status = false;
                } else {
                    $new_profile_photo = $this->upload->data('file_name');
                }
            }

        }

        if($status){
            // Update data user dasar with student data synchronization
            // For student data, we should synchronize user data with student data
            $user_update = [
                'nama_lengkap' => $this->input->post('siswa_nama') ? $this->input->post('siswa_nama') : $this->input->post('nama_lengkap'),
                'email' => $this->input->post('email'),
                'telepon' => $this->input->post('siswa_telepon') ? $this->input->post('siswa_telepon') : $this->input->post('telepon'),
                'alamat' => $this->input->post('siswa_alamat') ? $this->input->post('siswa_alamat') : $this->input->post('alamat'),
                'tempat_lahir' => $this->input->post('siswa_tempat_lahir') ? $this->input->post('siswa_tempat_lahir') : $this->input->post('tempat_lahir'),
                'tanggal_lahir' => $this->input->post('siswa_tanggal_lahir') ? $this->input->post('siswa_tanggal_lahir') : $this->input->post('tanggal_lahir'),
                'nip_nim' => $this->input->post('nip_nim'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            if ($new_profile_photo) {
                $user_update['foto_profil'] = $new_profile_photo;
            }
            
            // Handle jenis_kelamin from student data
            $siswa_jk = $this->input->post('siswa_jk');
            $user_jk = $this->input->post('jenis_kelamin');
            
            // Use student gender if provided, otherwise use user gender
            $jenis_kelamin = $siswa_jk ? $siswa_jk : $user_jk;
            
            // Convert 'L'/'P' to 'Laki-laki'/'Perempuan' to match database ENUM
            if ($jenis_kelamin === 'L') {
                $jenis_kelamin = 'Laki-laki';
            } elseif ($jenis_kelamin === 'P') {
                $jenis_kelamin = 'Perempuan';
            }
            
            if ($jenis_kelamin) {
                $user_update['jenis_kelamin'] = $jenis_kelamin;
            }
            
            // Filter field kosong
            $user_update = array_filter($user_update, function($value) {
                return $value !== '' && $value !== null;
            });
            
            log_message('debug', 'User update data: ' . print_r($user_update, true));
            
            $update_result = $this->M_user->update_user($user_update, $user_code);
            log_message('debug', 'User update result: ' . ($update_result ? 'success' : 'failed'));
            
            // Update data spesifik berdasarkan tipe user
            if($tipe_user == 'siswa'){
                $siswa_update = [
                    'siswa_nama' => $this->input->post('siswa_nama'),
                    'siswa_kelas' => $this->input->post('siswa_kelas'),
                    'siswa_telepon' => $this->input->post('siswa_telepon'),
                    'siswa_jurusan' => $this->input->post('siswa_jurusan'),
                    'siswa_nis' => $this->input->post('siswa_nis'),
                    'siswa_nisn' => $this->input->post('siswa_nisn'),
                    'siswa_jk' => $this->input->post('siswa_jk'),
                    'siswa_tempat_lahir' => $this->input->post('siswa_tempat_lahir'),
                    'siswa_tanggal_lahir' => $this->input->post('siswa_tanggal_lahir') ? date('Y-m-d', strtotime($this->input->post('siswa_tanggal_lahir'))) : NULL,
                    'siswa_alamat' => $this->input->post('siswa_alamat'),
                    'siswa_asal_sekolah' => $this->input->post('siswa_asal_sekolah')
                ];
                
                // Also update user data with student information for consistency
                // This ensures that the user table has the most up-to-date student information
                $user_sync_data = [
                    'nama_lengkap' => $this->input->post('siswa_nama'),
                    'email' => $this->input->post('email'),
                    'telepon' => $this->input->post('siswa_telepon'),
                    'alamat' => $this->input->post('siswa_alamat'),
                    'tempat_lahir' => $this->input->post('siswa_tempat_lahir'),
                    'tanggal_lahir' => $this->input->post('siswa_tanggal_lahir') ? date('Y-m-d', strtotime($this->input->post('siswa_tanggal_lahir'))) : NULL,
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                
                // Handle gender synchronization
                $siswa_jk = $this->input->post('siswa_jk');
                if ($siswa_jk === 'L') {
                    $user_sync_data['jenis_kelamin'] = 'Laki-laki';
                } elseif ($siswa_jk === 'P') {
                    $user_sync_data['jenis_kelamin'] = 'Perempuan';
                } elseif ($siswa_jk) {
                    $user_sync_data['jenis_kelamin'] = $siswa_jk;
                }
                
                // Filter out empty values
                $user_sync_data = array_filter($user_sync_data, function($value) {
                    return $value !== '' && $value !== null;
                });
                
                // Update user data with student information
                if (!empty($user_sync_data)) {
                    $this->M_user->update_user($user_sync_data, $user_code);
                    log_message('debug', 'User synchronized with student data: ' . print_r($user_sync_data, true));
                }
                
                // Filter field kosong
                $siswa_update = array_filter($siswa_update, function($value) {
                    return $value !== '' && $value !== null;
                });
                
                log_message('debug', 'Siswa update data: ' . print_r($siswa_update, true));
                
                $siswa_update_result = $this->M_siswa->update_siswa_by_user_id($siswa_update, $user->id);
                log_message('debug', 'Siswa update result: ' . ($siswa_update_result ? 'success' : 'failed'));
                
            } elseif($tipe_user == 'pembimbing'){
                // Validasi format tahun masuk
                $tahun_masuk = $this->input->post('tahun_masuk');
                
                if(!empty($tahun_masuk) && !$this->M_pembimbing->validate_tahun_masuk($tahun_masuk)){
                    $this->session->set_flashdata('error_message', 'Format tahun masuk tidak valid (contoh: 2020)');
                    $status = false;
                }
                
                if($status){
                    $pembimbing_update = [
                        'pembimbing_nama' => $this->input->post('pembimbing_nama'),
                        'pembimbing_nip' => $this->input->post('pembimbing_nip'),
                        'pembimbing_telepon' => $this->input->post('pembimbing_telepon'),
                        'pembimbing_email' => $this->input->post('pembimbing_email'),
                        'pembimbing_alamat' => $this->input->post('pembimbing_alamat'),
                        'pendidikan_terakhir' => $this->input->post('pendidikan_terakhir'),
                        'jabatan' => $this->input->post('jabatan'),
                        'jurusan_keahlian' => $this->input->post('jurusan_keahlian'),
                        'tahun_masuk' => $tahun_masuk,
                        'status_kepegawaian' => $this->input->post('status_kepegawaian'),
                        'tempat_tugas' => $this->input->post('tempat_tugas')
                    ];
                    
                    // Filter field kosong
                    $pembimbing_update = array_filter($pembimbing_update, function($value) {
                        return $value !== '' && $value !== null;
                    });
                    
                    $this->M_pembimbing->update_biodata_pembimbing($pembimbing_update, $user->user_code);
                }
            }
            
            if($status){
                $this->session->set_flashdata('message', 'Biodata berhasil diperbarui');
                // Redirect konsisten ke halaman detail biodata
                redirect('hubin/detail_biodata/'.$user_code);
            }
        }
        
        // Jika ada error, kembali ke form edit
        $data['user'] = $user;
        
        // Jika ada data yang dikirim dari form, gunakan itu, jika tidak ambil dari database
        // Kita tetap gunakan $user sebagai basis utama tapi overwrite dengan data yang di-post
        $posted_data = $this->input->post();
        $combined_data = (object)array_merge((array)$user, (array)$posted_data);
        
        // Pastikan nilai jenis_kelamin tetap dalam format yang benar untuk ditampilkan di form
        if(isset($posted_data['jenis_kelamin'])) {
            $combined_data->jenis_kelamin = $posted_data['jenis_kelamin'];
        }
        
        $data['biodata'] = $combined_data;
        $data['tipe_user'] = $tipe_user;
        
        // Handle case where student edit view no longer exists
        if($tipe_user == 'siswa') {
            $data['content'] = 'hubin/edit-biodata-siswa';
            $this->load->view('template', $data, FALSE);
        } else {
            $data['content'] = 'hubin/edit-biodata-' . $tipe_user;
            $this->load->view('template', $data, FALSE);
        }
    }
    
    // Hapus biodata user (soft delete)
    function hapus_biodata($user_code = null)
    {
        $this->load->model('M_user');
        $this->load->model('M_siswa');
        $this->load->model('M_pembimbing');
        
        $user = $this->M_user->get_data_user($user_code);
        if(!$user){
            $this->session->set_flashdata('error_message', 'User tidak ditemukan');
            redirect('hubin/view/daftar-siswa');
        }
        
        // Konfirmasi sebelum hapus
        if($this->input->post('konfirmasi_hapus')){
            // Soft delete: update status menjadi 'tidak_aktif'
            $update = [
                'status' => 'tidak_aktif',
                'deleted_at' => date('Y-m-d H:i:s')
            ];
            
            $this->M_user->update_user($update, $user_code);
            
            $this->session->set_flashdata('message', 'Biodata user berhasil dihapus');
            redirect('hubin/view/daftar-siswa');
        }
        
        // Tampilkan halaman konfirmasi
        $data['user'] = $user;
        $data['content'] = 'hubin/konfirmasi-hapus-biodata';
        $this->load->view('template', $data, FALSE);
    }

    function hapus_siswa($siswa_code = null)
    {
        $this->load->model('M_siswa');
        $ceksiswa = $this->M_siswa->get_data_siswa($siswa_code);
        if($ceksiswa){
            $result = $this->M_siswa->hapus_siswa($siswa_code);
            if($result){
                // Check if request is AJAX
                if($this->input->is_ajax_request()) {
                    echo json_encode(['status' => 'success', 'message' => 'Siswa deleted successfully']);
                    return;
                }
                $this->session->set_flashdata('message', 'Berhasil menghapus data siswa');
            } else {
                if($this->input->is_ajax_request()) {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to delete siswa']);
                    return;
                }
                $this->session->set_flashdata('error_message', 'Gagal menghapus data siswa');
            }
        } else {
            if($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'error', 'message' => 'Siswa not found']);
                return;
            }
            $this->session->set_flashdata('error_message', 'Data siswa tidak ditemukan');
        }
        redirect('hubin/view/assign-pembimbing');
    }

    // Update siswa function to include biodata
    function update_siswa()
    {
        $status = true;
        $content = 'hubin/edit-siswa';
        $siswa_code = $this->input->post('siswa_code');
        $this->load->model('M_siswa');
        $ceksiswa = $this->M_siswa->get_data_siswa($siswa_code);
        if(!$ceksiswa){
            $status = false;
            $content = '404';
        }else{
            $data['siswa'] = $ceksiswa;
        }
        
        $this->form_validation->set_rules('siswa_code', 'code', 'required');
        $this->form_validation->set_rules('siswa_nama', 'Nama Siswa', 'required');
        $this->form_validation->set_rules('siswa_kelas', 'Kelas Siswa', 'required');
        $this->form_validation->set_rules('siswa_telepon', 'Telepon Siswa', 'required|numeric');
        
        // Validate NIS/NISN uniqueness - exclude current record
        $nis = $this->input->post('siswa_nis');
        $nisn = $this->input->post('siswa_nisn');
        
        if($nis) {
            $this->form_validation->set_rules('siswa_nis', 'NIS', 'callback_nis_check['.$siswa_code.']');
        }
        
        if($nisn) {
            $this->form_validation->set_rules('siswa_nisn', 'NISN', 'callback_nisn_check['.$siswa_code.']');
        }
        
        if ($this->form_validation->run() == FALSE) {
            $status = false;
        }

        if($status){
            $update = array(
                'siswa_nama' => $this->input->post('siswa_nama'),
                'siswa_kelas' => $this->input->post('siswa_kelas'),
                'siswa_telepon' => $this->input->post('siswa_telepon'),
                'siswa_alamat' => $this->input->post('siswa_alamat'),
                'siswa_jurusan' => $this->input->post('siswa_jurusan'),
                'siswa_nis' => $this->input->post('siswa_nis'),
                'siswa_nisn' => $this->input->post('siswa_nisn'),
                'siswa_jk' => $this->input->post('siswa_jk'),
                'siswa_tempat_lahir' => $this->input->post('siswa_tempat_lahir'),
                'siswa_tanggal_lahir' => $this->input->post('siswa_tanggal_lahir') ? date('Y-m-d', strtotime($this->input->post('siswa_tanggal_lahir'))) : NULL,
                'siswa_asal_sekolah' => $this->input->post('siswa_asal_sekolah'),
                'dudi_id' => $this->input->post('dudi_id') ? $this->input->post('dudi_id') : NULL,
                'status_pengajuan' => $this->M_siswa->normalize_status_pengajuan($this->input->post('status_pengajuan')),
                'periode' => $this->input->post('periode'),
                'user_id' => $this->input->post('user_id') ? $this->input->post('user_id') : NULL
            );
            $this->M_siswa->update_siswa($update, $siswa_code);
            $this->session->set_flashdata('message', 'Berhasil update data siswa');
            redirect('hubin/view/assign-pembimbing');
        }

        $data['content'] = $content; 
        $this->load->view('template', $data, FALSE);
    }

    // Assign Pembimbing ke Siswa
    function assign_pembimbing()
    {
        $pembimbing_id = $this->input->post('pembimbing_id');
        $siswa_codes = $this->input->post('siswa_codes');
        
        if(!$pembimbing_id || !$siswa_codes){
            $this->session->set_flashdata('error_message', 'Pilih guru pembimbing dan siswa terlebih dahulu');
            redirect('hubin/view/assign-pembimbing');
        }
        
        $this->load->model('M_siswa');
        $this->load->model('M_pengelompokan');
        $this->load->model('M_pembimbing');
        
        // Get pembimbing data
        $pembimbing = $this->db->where('pembimbing_id', $pembimbing_id)->get('tb_pembimbing')->row();
        if(!$pembimbing){
            $this->session->set_flashdata('error_message', 'Guru pembimbing tidak ditemukan');
            redirect('hubin/view/assign-pembimbing');
        }
        
        $siswa_codes_array = explode(',', $siswa_codes);
        $siswa_ids = array();
        $success_count = 0;
        $failed_count = 0;
        
        // Convert siswa_codes to siswa_ids and validate status
        foreach($siswa_codes_array as $siswa_code){
            $siswa = $this->M_siswa->get_data_siswa($siswa_code);
            // Only allow assigning students with approved PKL submission
            if($siswa && $siswa->status_pengajuan == 'disetujui'){
                $siswa_ids[] = $siswa->siswa_id;
            }
        }
        
        if(empty($siswa_ids)){
            $this->session->set_flashdata('error_message', 'Tidak ada siswa yang valid untuk di-assign. Pastikan siswa telah disetujui pengajuan PKL-nya.');
            redirect('hubin/view/assign-pembimbing');
        }
        
        // Check current assignment count
        $this->db->where('pembimbing_id', $pembimbing_id);
        $current_count = $this->db->get('tb_pengelompokan')->num_rows();
        $available_slots = 10 - $current_count;
        
        if(count($siswa_ids) > $available_slots && $available_slots > 0){
            // Only assign up to available slots
            $siswa_ids = array_slice($siswa_ids, 0, $available_slots);
            $this->session->set_flashdata('warning_message', 'Guru pembimbing ini hanya memiliki ' . $available_slots . ' slot tersedia. Hanya ' . count($siswa_ids) . ' siswa yang di-assign.');
        }
        
        // Assign siswa to pembimbing using pengelompokan table
        $result = $this->M_pengelompokan->assign_siswa_to_pembimbing($pembimbing_id, $siswa_ids);
        
        // Also update tb_siswa.pembimbing_id for backward compatibility
        // Auto-generate periode PKL (current academic year)
        $current_year = date('Y');
        $next_year = $current_year + 1;
        $periode = $current_year . '/' . $next_year;
        
        foreach($siswa_ids as $siswa_id){
            $this->db->where('siswa_id', $siswa_id);
            $this->db->update('tb_siswa', array(
                'pembimbing_id' => $pembimbing_id,
                'periode' => $periode
            ));
        }
        
        // Clear any cached data to ensure fresh information
        $this->cache->clean();
        
        redirect('hubin/view/assign-pembimbing');
    }
    
    // Get available pembimbing for assignment (AJAX)
    function get_available_pembimbing()
    {
        $this->load->model('M_pembimbing');
        
        // Get all pembimbing with their student count (include inactive for visibility)
        $this->db->select('tb_pembimbing.*, 
            COUNT(tb_pengelompokan.siswa_id) as jumlah_siswa');
        $this->db->from('tb_pembimbing');
        $this->db->join('tb_pengelompokan', 'tb_pengelompokan.pembimbing_id = tb_pembimbing.pembimbing_id', 'left');
        // Remove status filter to show all pembimbing, but we'll mark inactive ones in the UI
        // $this->db->where('tb_pembimbing.status_kepegawaian', 'Aktif');
        $this->db->group_by('tb_pembimbing.pembimbing_id');
        $this->db->order_by('tb_pembimbing.pembimbing_nama', 'ASC');
        $pembimbing = $this->db->get()->result();
        
        if(count($pembimbing) > 0) {
            echo json_encode(array(
                'success' => true,
                'pembimbing' => $pembimbing
            ));
        } else {
            echo json_encode(array(
                'success' => false,
                'pembimbing' => array(),
                'message' => 'Belum ada data guru pembimbing. Silakan tambahkan pembimbing terlebih dahulu.'
            ));
        }
    }
    
    // Get available students for assignment (AJAX)
    function get_available_students($pembimbing_code = null)
    {
        $this->load->model('M_siswa');
        $this->load->model('M_pembimbing');
        $this->load->model('M_pengelompokan');
        
        // Get pembimbing data
        $pembimbing = $this->M_pembimbing->get_pembimbing_with_biodata($pembimbing_code);
        if (!$pembimbing) {
            echo json_encode(array('success' => false, 'message' => 'Pembimbing tidak ditemukan'));
            return;
        }
        
        // Check current assignment count
        $this->db->where('pembimbing_id', $pembimbing->pembimbing_id);
        $current_count = $this->db->get('tb_pengelompokan')->num_rows();
        $max_limit = 20; // Maximum 20 students per pembimbing
        $available_slots = $max_limit - $current_count;
        
        if ($available_slots <= 0) {
            echo json_encode(array(
                'success' => true,
                'students' => array(),
                'message' => 'Pembimbing ini telah mencapai batas maksimum siswa'
            ));
            return;
        }
        
        // Get approved students who are not yet assigned to any pembimbing
        $this->db->select('tb_siswa.*, tb_dudi.dudi_nama');
        $this->db->from('tb_siswa');
        $this->db->join('tb_dudi', 'tb_dudi.dudi_id = tb_siswa.dudi_id', 'left');
        $this->db->where('tb_siswa.status_pengajuan', 'disetujui');
        
        // Exclude students already assigned to this pembimbing
        $this->db->not_like('tb_siswa.siswa_id', function($qb) use ($pembimbing) {
            $qb->select('siswa_id');
            $qb->from('tb_pengelompokan');
            $qb->where('pembimbing_id', $pembimbing->pembimbing_id);
        });
        
        // Also exclude students assigned to other pembimbing
        $assigned_siswa_ids = array();
        $this->db->select('siswa_id');
        $this->db->from('tb_pengelompokan');
        $assigned_query = $this->db->get();
        foreach ($assigned_query->result() as $row) {
            $assigned_siswa_ids[] = $row->siswa_id;
        }
        
        if (!empty($assigned_siswa_ids)) {
            $this->db->where_not_in('tb_siswa.siswa_id', $assigned_siswa_ids);
        }
        
        $this->db->order_by('tb_siswa.siswa_nama', 'ASC');
        $query = $this->db->get();
        
        // Limit to available slots
        $students = array_slice($query->result(), 0, $available_slots);
        
        echo json_encode(array(
            'success' => true,
            'students' => $students,
            'available_slots' => $available_slots
        ));
    }
    
    // Assign students to pembimbing (AJAX)
    function assign_students_to_pembimbing()
    {
        $pembimbing_code = $this->input->post('pembimbing_code');
        $siswa_codes = $this->input->post('siswa_codes');
        
        if (!$pembimbing_code || !$siswa_codes) {
            echo json_encode(array('success' => false, 'message' => 'Data tidak lengkap'));
            return;
        }
        
        $this->load->model('M_siswa');
        $this->load->model('M_pengelompokan');
        $this->load->model('M_pembimbing');
        
        // Get pembimbing data
        $pembimbing = $this->M_pembimbing->get_pembimbing_with_biodata($pembimbing_code);
        if (!$pembimbing) {
            echo json_encode(array('success' => false, 'message' => 'Pembimbing tidak ditemukan'));
            return;
        }
        
        $siswa_codes_array = explode(',', $siswa_codes);
        $siswa_ids = array();
        
        // Convert siswa_codes to siswa_ids and validate
        foreach ($siswa_codes_array as $siswa_code) {
            $siswa = $this->M_siswa->get_data_siswa($siswa_code);
            if ($siswa && $siswa->status_pengajuan == 'disetujui') {
                $siswa_ids[] = $siswa->siswa_id;
            }
        }
        
        if (empty($siswa_ids)) {
            echo json_encode(array('success' => false, 'message' => 'Tidak ada siswa yang valid untuk di-assign'));
            return;
        }
        
        // Check available slots
        $this->db->where('pembimbing_id', $pembimbing->pembimbing_id);
        $current_count = $this->db->get('tb_pengelompokan')->num_rows();
        $available_slots = 20 - $current_count;
        
        if (count($siswa_ids) > $available_slots) {
            echo json_encode(array(
                'success' => false,
                'message' => 'Slot pembimbing hanya tersedia ' . $available_slots . ' siswa'
            ));
            return;
        }
        
        // Assign students
        $result = $this->M_pengelompokan->assign_siswa_to_pembimbing($pembimbing->pembimbing_id, $siswa_ids);
        
        // Update tb_siswa.pembimbing_id for backward compatibility
        $current_year = date('Y');
        $next_year = $current_year + 1;
        $periode = $current_year . '/' . $next_year;
        
        foreach ($siswa_ids as $siswa_id) {
            $this->db->where('siswa_id', $siswa_id);
            $this->db->update('tb_siswa', array(
                'pembimbing_id' => $pembimbing->pembimbing_id,
                'periode' => $periode
            ));
        }
        
        if ($result['success'] > 0) {
            $this->session->set_flashdata('message', 'Berhasil assign ' . $result['success'] . ' siswa ke pembimbing');
            echo json_encode(array('success' => true, 'message' => 'Berhasil assign ' . $result['success'] . ' siswa'));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Gagal assign siswa'));
        }
    }
    
    // Unassign Pembimbing dari Siswa
    function unassign_pembimbing($siswa_code = null)
    {
        $this->load->model('M_pengelompokan');
        $this->load->model('M_siswa');
        
        if($siswa_code) {
            // Get student data
            $siswa = $this->M_siswa->get_data_siswa($siswa_code);
            if($siswa) {
                // Remove from pengelompokan table
                $this->db->where('siswa_id', $siswa->siswa_id);
                $this->db->delete('tb_pengelompokan');
                
                // Update siswa table
                $this->db->where('siswa_id', $siswa->siswa_id);
                $this->db->update('tb_siswa', array('pembimbing_id' => NULL));
                
                $this->session->set_flashdata('message', 'Berhasil unassign guru pembimbing dari siswa');
            } else {
                $this->session->set_flashdata('error_message', 'Siswa tidak ditemukan');
            }
        } else {
            $siswa_codes = $this->input->post('siswa_codes');
            if($siswa_codes) {
                $siswa_codes_array = explode(',', $siswa_codes);
                $count = 0;
                
                foreach($siswa_codes_array as $code) {
                    $siswa = $this->M_siswa->get_data_siswa($code);
                    if($siswa) {
                        // Remove from pengelompokan table
                        $this->db->where('siswa_id', $siswa->siswa_id);
                        $this->db->delete('tb_pengelompokan');
                        
                        // Update siswa table
                        $this->db->where('siswa_id', $siswa->siswa_id);
                        $this->db->update('tb_siswa', array('pembimbing_id' => NULL));
                        $count++;
                    }
                }
                
                $this->session->set_flashdata('message', 'Berhasil unassign guru pembimbing dari ' . $count . ' siswa');
            } else {
                $this->session->set_flashdata('error_message', 'Pilih siswa terlebih dahulu');
            }
        }
        
        redirect('hubin/view/assign-pembimbing');
    }
    
    // Change Pembimbing for Siswa
    function change_pembimbing()
    {
        $siswa_code = $this->input->post('siswa_code');
        $new_pembimbing_id = $this->input->post('new_pembimbing_id');
        
        if(!$siswa_code || !$new_pembimbing_id) {
            $this->session->set_flashdata('error_message', 'Pilih siswa dan pembimbing baru terlebih dahulu');
            redirect('hubin/view/assign-pembimbing');
        }
        
        $this->load->model('M_siswa');
        $this->load->model('M_pengelompokan');
        $this->load->model('M_pembimbing');
        
        $siswa = $this->M_siswa->get_data_siswa($siswa_code);
        $new_pembimbing = $this->db->where('pembimbing_id', $new_pembimbing_id)->get('tb_pembimbing')->row();
        
        if(!$siswa || !$new_pembimbing) {
            $this->session->set_flashdata('error_message', 'Siswa atau pembimbing tidak ditemukan');
            redirect('hubin/view/assign-pembimbing');
        }
        
        // Remove old assignment
        $this->db->where('siswa_id', $siswa->siswa_id);
        $this->db->delete('tb_pengelompokan');
        
        // Create new assignment
        $assignment_data = array(
            'siswa_id' => $siswa->siswa_id,
            'pembimbing_id' => $new_pembimbing_id,
            'tanggal_assign' => date('Y-m-d H:i:s')
        );
        $this->db->insert('tb_pengelompokan', $assignment_data);
        
        // Update siswa table with new pembimbing and auto-generate periode
        $current_year = date('Y');
        $next_year = $current_year + 1;
        $periode = $current_year . '/' . $next_year;
        
        $this->db->where('siswa_id', $siswa->siswa_id);
        $this->db->update('tb_siswa', array(
            'pembimbing_id' => $new_pembimbing_id,
            'periode' => $periode
        ));
        
        $this->session->set_flashdata('message', 'Berhasil mengganti pembimbing untuk siswa ' . $siswa->siswa_nama);
        redirect('hubin/view/assign-pembimbing');
    }



    // Tambah Pembimbing beserta Akun Login User (Level 2)
    function tambah_pembimbing()
    {
        $status = true;
        $this->form_validation->set_rules('pembimbing_nama', 'Nama Pembimbing', 'required|trim');
        $this->form_validation->set_rules('pembimbing_nip', 'NIP', 'required|trim');
        $this->form_validation->set_rules('username', 'Username', 'required|trim');
        $this->form_validation->set_rules('password', 'Password', 'required');
        
        // Validasi tambahan untuk biodata
        if($this->input->post('tahun_masuk')) {
            $this->form_validation->set_rules('tahun_masuk', 'Tahun Masuk', 'numeric|greater_than[1950]|less_than_equal_to['.date('Y').']');
        }
        
        if($this->input->post('pembimbing_email')) {
            $this->form_validation->set_rules('pembimbing_email', 'Email', 'valid_email');
        }
        
        if($this->input->post('pembimbing_telepon')) {
            $this->form_validation->set_rules('pembimbing_telepon', 'Telepon', 'numeric');
        }
        
        // Validasi untuk biodata pribadi
        if($this->input->post('tanggal_lahir')) {
            $this->form_validation->set_rules('tanggal_lahir', 'Tanggal Lahir', 'callback_validate_date_format');
        }
        
        if($this->input->post('jenis_kelamin')) {
            $this->form_validation->set_rules('jenis_kelamin', 'Jenis Kelamin', 'in_list[Laki-laki,Perempuan]');
        }
        
        if ($this->form_validation->run() == FALSE) {
            $status = false;
        }
        
        if($status){
            $this->load->model('M_pembimbing');
            $this->load->model('M_user');
            
            $username = trim($this->input->post('username'));
            $nip = trim($this->input->post('pembimbing_nip'));
            $email = trim($this->input->post('pembimbing_email'));
            
            // Check if username already exists in tb_user
            $this->db->where('username', $username);
            $check_user = $this->db->get('tb_user')->row();
            if($check_user){
                $this->session->set_flashdata('error_message', 'Username "'.$username.'" sudah digunakan. Silakan gunakan username yang berbeda.');
                $this->view('tambah-pembimbing');
                return;
            }
            
            // Check if NIP already exists in tb_pembimbing
            $this->db->where('pembimbing_nip', $nip);
            $check_nip = $this->db->get('tb_pembimbing')->row();
            if($check_nip){
                $this->session->set_flashdata('error_message', 'NIP "'.$nip.'" sudah digunakan oleh pembimbing lain.');
                $this->view('tambah-pembimbing');
                return;
            }
            
            $pembimbing_alamat = trim($this->input->post('pembimbing_alamat'));
            $tempat_lahir = trim($this->input->post('tempat_lahir'));
            $tanggal_lahir = $this->input->post('tanggal_lahir') ? trim($this->input->post('tanggal_lahir')) : null;
            $jenis_kelamin = trim($this->input->post('jenis_kelamin'));
            $telepon = trim($this->input->post('pembimbing_telepon'));
            $nama = trim($this->input->post('pembimbing_nama'));
            
            // 1. Buat user account di tb_user (Level 2 = Guru Pembimbing)
            $user_code = $this->M_user->generateRandomString(30);
            $hashed_password = password_hash($this->input->post('password'), PASSWORD_DEFAULT);
            
            $user_data = array(
                'user_code'     => $user_code,
                'username'      => $username,
                'password'      => $hashed_password,
                'nama_lengkap'  => $nama,
                'email'         => !empty($email) ? $email : null,
                'telepon'       => !empty($telepon) ? $telepon : null,
                'alamat'        => !empty($pembimbing_alamat) ? $pembimbing_alamat : null,
                'tempat_lahir'  => !empty($tempat_lahir) ? $tempat_lahir : null,
                'tanggal_lahir' => !empty($tanggal_lahir) ? date('Y-m-d', strtotime($tanggal_lahir)) : null,
                'jenis_kelamin' => !empty($jenis_kelamin) ? $jenis_kelamin : null,
                'nip_nim'       => !empty($nip) ? $nip : null,
                'level'         => 2, // Pembimbing
                'is_active'     => 1,
                'created_at'    => date('Y-m-d H:i:s')
            );
            
            $this->M_user->tambah_data_user($user_data);
            
            // Dapatkan ID user yang baru dibuat
            $this->db->where('username', $username);
            $new_user = $this->db->get('tb_user')->row();
            $user_id = $new_user ? $new_user->id : null;
            
            // 2. Buat data pembimbing di tb_pembimbing
            $pembimbing_code = $this->M_pembimbing->generateRandomString(30);
            $pembimbing_data = array(
                'user_id'             => $user_id,
                'pembimbing_nama'     => $nama,
                'pembimbing_nip'      => $nip,
                'pembimbing_telepon'  => !empty($telepon) ? $telepon : null,
                'pembimbing_email'    => !empty($email) ? $email : null,
                'pembimbing_code'     => $pembimbing_code,
                // Professional data fields
                'pendidikan_terakhir' => trim($this->input->post('pendidikan_terakhir')),
                'jabatan'             => trim($this->input->post('jabatan')),
                'jurusan_keahlian'    => trim($this->input->post('jurusan_keahlian')),
                'tahun_masuk'         => trim($this->input->post('tahun_masuk')),
                'status_kepegawaian'  => trim($this->input->post('status_kepegawaian')),
                'tempat_tugas'        => trim($this->input->post('tempat_tugas')),
                // Personal data fields
                'pembimbing_alamat'   => !empty($pembimbing_alamat) ? $pembimbing_alamat : null,
                'tempat_lahir'        => !empty($tempat_lahir) ? $tempat_lahir : null,
                'tanggal_lahir'       => !empty($tanggal_lahir) ? $tanggal_lahir : null,
                'jenis_kelamin'       => !empty($jenis_kelamin) ? $jenis_kelamin : null,
                'is_active'           => 1,
                'created_at'          => date('Y-m-d H:i:s'),
                'updated_at'          => date('Y-m-d H:i:s')
            );
            
            $this->M_pembimbing->tambah_data_pembimbing($pembimbing_data);
            
            // Set flashdata informasi akun baru untuk diserahkan ke pembimbing
            $this->session->set_flashdata('message', 'Berhasil menambah data pembimbing "'.$nama.'" dan akun login');
            $this->session->set_flashdata('new_user_nama', $nama);
            $this->session->set_flashdata('new_user_username', $username);
            $this->session->set_flashdata('new_user_password', $this->input->post('password'));
            
            redirect('hubin/view/data-pembimbing');
        }else{
            $this->view('tambah-pembimbing');
        }
    }
    
    /**
     * Callback function to validate date format
     */
    public function validate_date_format($date)
    {
        // Check if date matches the expected format (YYYY-MM-DD)
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $this->form_validation->set_message('validate_date_format', 'Tanggal Lahir harus dalam format yang benar (YYYY-MM-DD).');
            return FALSE;
        }
        
        // Verify that it's a valid calendar date
        $timestamp = strtotime($date);
        if (!$timestamp || date('Y-m-d', $timestamp) !== $date) {
            $this->form_validation->set_message('validate_date_format', 'Tanggal Lahir tidak valid.');
            return FALSE;
        }
        
        return TRUE;
    }
    
    // Update Pembimbing (Tanpa User)
    function update_pembimbing_lengkap()
    {
        $status = true;
        $content = 'hubin/edit-pembimbing';
        $pembimbing_code = $this->input->post('pembimbing_code');
        $this->load->model('M_pembimbing');
        
        $cekpembimbing = $this->M_pembimbing->get_pembimbing_with_biodata($pembimbing_code);
        if(!$cekpembimbing){
            $status = false;
            $content = '404';
        } else {
            // Validasi NIP jika diubah
            $new_nip = $this->input->post('pembimbing_nip');
            if($new_nip != $cekpembimbing->pembimbing_nip) {
                $this->db->where('pembimbing_nip', $new_nip);
                $this->db->where('pembimbing_code !=', $pembimbing_code);
                $check_nip = $this->db->get('tb_pembimbing')->row();
                if($check_nip){
                    $this->session->set_flashdata('error_message', 'NIP sudah digunakan oleh pembimbing lain');
                    redirect('hubin/edit_pembimbing/'.$pembimbing_code);
                }
            }
            
            $this->form_validation->set_rules('pembimbing_code', 'code', 'required');
            $this->form_validation->set_rules('pembimbing_nama', 'Nama Pembimbing', 'required');
            $this->form_validation->set_rules('pembimbing_nip', 'NIP', 'required');
            
            // Validasi tambahan untuk biodata
            if($this->input->post('tahun_masuk')) {
                $this->form_validation->set_rules('tahun_masuk', 'Tahun Masuk', 'numeric|greater_than[1950]|less_than_equal_to['.date('Y').']');
            }
            
            if ($this->form_validation->run() == FALSE) {
                $status = false;
            }
        }
        
        if($status){
            // Update user data (for personal information)
            $this->load->model('M_user');
            
            // Handle personal information fields separately to allow empty values
            $pembimbing_alamat = $this->input->post('pembimbing_alamat');
            $tempat_lahir = $this->input->post('tempat_lahir');
            $tanggal_lahir = $this->input->post('tanggal_lahir');
            $jenis_kelamin = $this->input->post('jenis_kelamin');
            $nip_nim = $this->input->post('pembimbing_nip');
            
            $user_update = array(
                'nama_lengkap' => $this->input->post('pembimbing_nama'),
                'email' => $this->input->post('pembimbing_email'),
                'telepon' => $this->input->post('pembimbing_telepon'),
                'updated_at' => date('Y-m-d H:i:s')
            );
            $username = trim((string) $this->input->post('username'));
            if ($username !== '' && $username !== $cekpembimbing->username) {
                $this->db->where('username', $username);
                $this->db->where('id !=', $cekpembimbing->user_id);
                if ($this->db->get('tb_user')->num_rows() > 0) {
                    $this->session->set_flashdata('error_message', 'Username sudah digunakan oleh akun lain.');
                    redirect('hubin/edit_pembimbing/' . $pembimbing_code);
                    return;
                }
                $user_update['username'] = $username;
            }
            
            // Add personal information fields to user update
            if (isset($_POST['pembimbing_alamat'])) {
                $user_update['alamat'] = $pembimbing_alamat !== '' ? $pembimbing_alamat : null;
            }
            if (isset($_POST['tempat_lahir'])) {
                $user_update['tempat_lahir'] = $tempat_lahir !== '' ? $tempat_lahir : null;
            }
            if (isset($_POST['tanggal_lahir'])) {
                $user_update['tanggal_lahir'] = $tanggal_lahir !== '' ? $tanggal_lahir : null;
            }
            if (isset($_POST['jenis_kelamin'])) {
                $user_update['jenis_kelamin'] = $jenis_kelamin !== '' ? $jenis_kelamin : null;
            }
            if (isset($_POST['pembimbing_nip'])) {
                $user_update['nip_nim'] = $nip_nim !== '' ? $nip_nim : $this->input->post('pembimbing_nip');
            }

            if (!empty($cekpembimbing->user_id)) {
                $this->db->where('id', $cekpembimbing->user_id);
                $this->db->update('tb_user', $user_update);
            }
            
            // Update pembimbing data directly (no longer connected to user account)
            // Store personal information directly in pembimbing table
            $personal_fields = array();
            // Always include alamat field to allow clearing it
            if (isset($_POST['pembimbing_alamat'])) {
                $personal_fields['pembimbing_alamat'] = $pembimbing_alamat !== '' ? $pembimbing_alamat : null;
            }
            // Always include jenis_kelamin field to allow clearing it
            if (isset($_POST['jenis_kelamin'])) {
                $personal_fields['jenis_kelamin'] = $jenis_kelamin !== '' ? $jenis_kelamin : null;
            }
            if (isset($user_update['tempat_lahir']) && !empty($user_update['tempat_lahir'])) {
                $personal_fields['tempat_lahir'] = $user_update['tempat_lahir'];
            }
            if (isset($user_update['tanggal_lahir']) && !empty($user_update['tanggal_lahir'])) {
                $personal_fields['tanggal_lahir'] = $user_update['tanggal_lahir'];
            }
            if (isset($user_update['nip_nim']) && !empty($user_update['nip_nim'])) {
                $personal_fields['pembimbing_nip'] = $user_update['nip_nim'];
            }
            if (isset($user_update['telepon']) && !empty($user_update['telepon'])) {
                $personal_fields['pembimbing_telepon'] = $user_update['telepon'];
            }
            if (isset($user_update['email']) && !empty($user_update['email'])) {
                $personal_fields['pembimbing_email'] = $user_update['email'];
            }
            if (isset($user_update['nama_lengkap']) && !empty($user_update['nama_lengkap'])) {
                $personal_fields['pembimbing_nama'] = $user_update['nama_lengkap'];
            }
            
            if (!empty($personal_fields)) {
                $this->db->where('pembimbing_code', $pembimbing_code);
                $this->db->update('tb_pembimbing', $personal_fields);
            }
            
            // Update pembimbing data (professional information)
            $pembimbing_update = array(
                'pembimbing_nama' => $this->input->post('pembimbing_nama'),
                'pembimbing_nip' => $this->input->post('pembimbing_nip'),
                'pembimbing_telepon' => $this->input->post('pembimbing_telepon'),
                'pembimbing_email' => $this->input->post('pembimbing_email'),
                'pendidikan_terakhir' => $this->input->post('pendidikan_terakhir'),
                'jabatan' => $this->input->post('jabatan'),
                'jurusan_keahlian' => $this->input->post('jurusan_keahlian'),
                'tahun_masuk' => $this->input->post('tahun_masuk'),
                'status_kepegawaian' => $this->input->post('status_kepegawaian'),
                'tempat_tugas' => $this->input->post('tempat_tugas'),
                'updated_at' => date('Y-m-d H:i:s')
            );
            
            // Filter empty values
            $pembimbing_update = array_filter($pembimbing_update, function($value) {
                return $value !== '' && $value !== null;
            });
            
            $this->M_pembimbing->update_pembimbing($pembimbing_update, $pembimbing_code);
            
            $this->session->set_flashdata('message', 'Berhasil update data pembimbing lengkap');
            redirect('hubin/view/data-pembimbing');
        }

        $data['content'] = $content; 
        $this->load->view('template', $data, FALSE);
    }
    
    // Advanced Pembimbing Management - Update with Full Biodata
    function update_pembimbing_full()
    {
        $status = true;
        $content = 'hubin/edit-pembimbing';
        $pembimbing_code = $this->input->post('pembimbing_code');
        $this->load->model('M_pembimbing');
        $this->load->model('M_user');
        
        $cekpembimbing = $this->M_pembimbing->get_pembimbing_with_biodata($pembimbing_code);
        if(!$cekpembimbing){
            $status = false;
            $content = '404';
        }else{
            $data['pembimbing'] = $cekpembimbing;
        }
        
        $this->form_validation->set_rules('pembimbing_code', 'code', 'required');
        $this->form_validation->set_rules('pembimbing_nama', 'Nama Pembimbing', 'required');
        if ($this->form_validation->run() == FALSE) {
            $status = false;
        }

        if($status){
            // Update user data
            $user_update = array(
                'nama_lengkap' => $this->input->post('pembimbing_nama'),
                'email' => $this->input->post('email'),
                'telepon' => $this->input->post('telepon'),
                'updated_at' => date('Y-m-d H:i:s')
            );
            
            // Handle personal information fields separately to allow empty values
            $pembimbing_alamat = $this->input->post('pembimbing_alamat');
            $tempat_lahir = $this->input->post('tempat_lahir');
            $tanggal_lahir = $this->input->post('tanggal_lahir');
            $jenis_kelamin = $this->input->post('jenis_kelamin');
            $nip_nim = $this->input->post('nip_nim');
            
            // Only add fields to update if they exist in POST data, allowing empty values
            if (isset($_POST['pembimbing_alamat'])) {
                $user_update['alamat'] = $pembimbing_alamat !== '' ? $pembimbing_alamat : null;
            }
            if (isset($_POST['tempat_lahir'])) {
                $user_update['tempat_lahir'] = $tempat_lahir !== '' ? $tempat_lahir : null;
            }
            if (isset($_POST['tanggal_lahir'])) {
                $user_update['tanggal_lahir'] = $tanggal_lahir !== '' ? $tanggal_lahir : null;
            }
            if (isset($_POST['jenis_kelamin'])) {
                $user_update['jenis_kelamin'] = $jenis_kelamin !== '' ? $jenis_kelamin : null;
            }
            if (isset($_POST['nip_nim'])) {
                $user_update['nip_nim'] = $nip_nim !== '' ? $nip_nim : $this->input->post('nip_nim');
            }
            
            // Update pembimbing data directly (no longer connected to user account)
            // Store personal information directly in pembimbing table
            $personal_fields = array();
            // Always include alamat field to allow clearing it
            if (isset($_POST['pembimbing_alamat'])) {
                $personal_fields['pembimbing_alamat'] = $pembimbing_alamat !== '' ? $pembimbing_alamat : null;
            }
            // Always include jenis_kelamin field to allow clearing it
            if (isset($_POST['jenis_kelamin'])) {
                $personal_fields['jenis_kelamin'] = $jenis_kelamin !== '' ? $jenis_kelamin : null;
            }
            if (isset($user_update['tempat_lahir']) && !empty($user_update['tempat_lahir'])) {
                $personal_fields['tempat_lahir'] = $user_update['tempat_lahir'];
            }
            if (isset($user_update['tanggal_lahir']) && !empty($user_update['tanggal_lahir'])) {
                $personal_fields['tanggal_lahir'] = $user_update['tanggal_lahir'];
            }
            if (isset($user_update['nip_nim']) && !empty($user_update['nip_nim'])) {
                $personal_fields['pembimbing_nip'] = $user_update['nip_nim'];
            }
            if (isset($user_update['telepon']) && !empty($user_update['telepon'])) {
                $personal_fields['pembimbing_telepon'] = $user_update['telepon'];
            }
            if (isset($user_update['email']) && !empty($user_update['email'])) {
                $personal_fields['pembimbing_email'] = $user_update['email'];
            }
            if (isset($user_update['nama_lengkap']) && !empty($user_update['nama_lengkap'])) {
                $personal_fields['pembimbing_nama'] = $user_update['nama_lengkap'];
            }
            
            if (!empty($personal_fields)) {
                $this->db->where('pembimbing_code', $pembimbing_code);
                $this->db->update('tb_pembimbing', $personal_fields);
            }
            
            // Update pembimbing data
            $pembimbing_update = array(
                'pembimbing_nama' => $this->input->post('pembimbing_nama'),
                'pembimbing_nip' => $this->input->post('pembimbing_nip'),
                'pembimbing_telepon' => $this->input->post('pembimbing_telepon'),
                'pembimbing_email' => $this->input->post('pembimbing_email'),
                'pembimbing_alamat' => $this->input->post('pembimbing_alamat'),
                'pendidikan_terakhir' => $this->input->post('pendidikan_terakhir'),
                'jabatan' => $this->input->post('jabatan'),
                'jurusan_keahlian' => $this->input->post('jurusan_keahlian'),
                'tahun_masuk' => $this->input->post('tahun_masuk'),
                'status_kepegawaian' => $this->input->post('status_kepegawaian'),
                'tempat_tugas' => $this->input->post('tempat_tugas'),
                'updated_at' => date('Y-m-d H:i:s')
            );
            
            // Filter empty values
            $pembimbing_update = array_filter($pembimbing_update, function($value) {
                return $value !== '' && $value !== null;
            });
            
            $this->M_pembimbing->update_pembimbing($pembimbing_update, $pembimbing_code);
            
            $this->session->set_flashdata('message', 'Berhasil update data pembimbing lengkap');
            redirect('hubin/view/data-pembimbing');
        }

        $data['content'] = $content; 
        $this->load->view('template', $data, FALSE);
    }
    
    // Bulk Update Pembimbing Status
    function bulk_update_pembimbing_status()
    {
        $pembimbing_ids = $this->input->post('pembimbing_ids');
        $new_status = $this->input->post('status');
        
        if(!$pembimbing_ids || !$new_status) {
            $this->session->set_flashdata('error_message', 'Pilih pembimbing dan status terlebih dahulu');
            redirect('hubin/view/data-pembimbing');
        }
        
        $this->load->model('M_pembimbing');
        $this->load->model('M_user');
        
        $count = 0;
        foreach($pembimbing_ids as $pembimbing_id) {
            $pembimbing = $this->M_pembimbing->get_pembimbing_by_id($pembimbing_id);
            if($pembimbing) {
                // Pembimbing is no longer connected to user accounts
                // Update pembimbing record directly if needed
                $this->db->where('pembimbing_id', $pembimbing->pembimbing_id);
                $this->db->update('tb_pembimbing', array('is_active' => $new_status == 'aktif' ? 1 : 0));
                $count++;
            }
        }
        
        $status_label = $new_status == 'aktif' ? 'aktif' : 'non-aktif';
        $this->session->set_flashdata('message', 'Berhasil memperbarui status ' . $count . ' pembimbing menjadi ' . $status_label);
        redirect('hubin/view/data-pembimbing');
    }
    
    // Advanced Siswa Management - Update with Full Biodata
    function update_siswa_full()
    {
        $status = true;
        $content = 'hubin/edit-siswa';
        $siswa_code = $this->input->post('siswa_code');
        $this->load->model('M_siswa');
        $this->load->model('M_user');
        
        $ceksiswa = $this->M_siswa->get_data_siswa($siswa_code);
        if(!$ceksiswa){
            $status = false;
            $content = '404';
        }else{
            $data['siswa'] = $ceksiswa;
        }
        
        $this->form_validation->set_rules('siswa_code', 'code', 'required');
        $this->form_validation->set_rules('siswa_nama', 'Nama Siswa', 'required');
        $this->form_validation->set_rules('siswa_kelas', 'Kelas Siswa', 'required');
        $this->form_validation->set_rules('siswa_telepon', 'Telepon Siswa', 'required|numeric');
        
        // Validate NIS/NISN uniqueness - exclude current record
        $nis = $this->input->post('siswa_nis');
        $nisn = $this->input->post('siswa_nisn');
        
        if($nis) {
            $this->form_validation->set_rules('siswa_nis', 'NIS', 'callback_nis_check['.$siswa_code.']');
        }
        
        if($nisn) {
            $this->form_validation->set_rules('siswa_nisn', 'NISN', 'callback_nisn_check['.$siswa_code.']');
        }
        
        if ($this->form_validation->run() == FALSE) {
            $status = false;
        }

        if($status){
            // Update user data
            $user_update = array(
                'nama_lengkap' => $this->input->post('siswa_nama'),
                'email' => $this->input->post('email'),
                'telepon' => $this->input->post('telepon'),
                'alamat' => $this->input->post('alamat'),
                'tempat_lahir' => $this->input->post('tempat_lahir'),
                'tanggal_lahir' => $this->input->post('tanggal_lahir'),
                'jenis_kelamin' => $this->input->post('jenis_kelamin'),
                'nip_nim' => $this->input->post('nip_nim'),
                'updated_at' => date('Y-m-d H:i:s')
            );
            
            // Filter empty values
            $user_update = array_filter($user_update, function($value) {
                return $value !== '' && $value !== null;
            });
            
            $this->M_user->update_user($user_update, $ceksiswa->user_code);
            
            // Update siswa data - pastikan email tidak disimpan di tabel tb_siswa karena seharusnya di tb_user
            $siswa_update = array(
                'siswa_nama' => $this->input->post('siswa_nama'),
                'siswa_kelas' => $this->input->post('siswa_kelas'),
                'siswa_telepon' => $this->input->post('siswa_telepon'),
                'siswa_alamat' => $this->input->post('siswa_alamat'),
                'siswa_jurusan' => $this->input->post('siswa_jurusan'),
                'siswa_nis' => $this->input->post('siswa_nis'),
                'siswa_nisn' => $this->input->post('siswa_nisn'),
                'siswa_jk' => $this->input->post('siswa_jk'),
                'siswa_tempat_lahir' => $this->input->post('siswa_tempat_lahir'),
                'siswa_tanggal_lahir' => $this->input->post('siswa_tanggal_lahir') ? date('Y-m-d', strtotime($this->input->post('siswa_tanggal_lahir'))) : NULL,
                'siswa_asal_sekolah' => $this->input->post('siswa_asal_sekolah'),
                'dudi_id' => $this->input->post('dudi_id') ? $this->input->post('dudi_id') : NULL,
                'status_pengajuan' => $this->M_siswa->normalize_status_pengajuan($this->input->post('status_pengajuan')),
                'periode' => $this->input->post('periode'),
                'user_id' => $this->input->post('user_id') ? $this->input->post('user_id') : NULL
            );
            
            // Filter empty values
            $siswa_update = array_filter($siswa_update, function($value) {
                return $value !== '' && $value !== null;
            });
            
            $this->M_siswa->update_siswa($siswa_update, $siswa_code);
            
            $this->session->set_flashdata('message', 'Berhasil update data siswa lengkap');
            redirect('hubin/view/assign-pembimbing');
        }

        $data['content'] = $content; 
        $this->load->view('template', $data, FALSE);
    }
    
    // Bulk Update Siswa Status
    function bulk_update_siswa_status()
    {
        $siswa_codes = $this->input->post('siswa_codes');
        $new_status = $this->input->post('status');
        
        if(!$siswa_codes || !$new_status) {
            $this->session->set_flashdata('error_message', 'Pilih siswa dan status terlebih dahulu');
            redirect('hubin/view/assign-pembimbing');
        }
        
        $this->load->model('M_siswa');
        $this->load->model('M_user');
        
        $count = 0;
        foreach($siswa_codes as $siswa_code) {
            $siswa = $this->M_siswa->get_data_siswa($siswa_code);
            if($siswa) {
                // Update user status
                $this->db->where('id', $siswa->user_id);
                $this->db->update('tb_user', array('status' => $new_status));
                $count++;
            }
        }
        
        $status_label = $new_status == 'aktif' ? 'aktif' : 'non-aktif';
        $this->session->set_flashdata('message', 'Berhasil memperbarui status ' . $count . ' siswa menjadi ' . $status_label);
        redirect('hubin/view/assign-pembimbing');
    }

    function detail_pembimbing_unified($pembimbing_code = null)
    {
        $this->load->model('M_pembimbing');
        $this->load->model('M_pengelompokan');
        
        // Get pembimbing data with personal information
        $pembimbing = $this->M_pembimbing->get_pembimbing_with_biodata($pembimbing_code);
        
        if($pembimbing){
            $data['pembimbing'] = $pembimbing;
            $data['pembimbing_code'] = $pembimbing_code;
            $data['content'] = 'hubin/detail-pembimbing-unified';
        }else{
            $data['content'] = '404';
        }
        $this->load->view('template', $data, FALSE);
    }
    
    function edit_pembimbing($pembimbing_code = null)
    {
        $this->load->model('M_pembimbing');
        $pembimbing = $this->M_pembimbing->get_pembimbing_with_biodata($pembimbing_code);
        if($pembimbing){
            $data['pembimbing'] = $pembimbing;
            $data['content'] = 'hubin/edit-pembimbing';
        }else{
            $data['content'] = '404';
        }
        $this->load->view('template', $data, FALSE);
    }

    function update_pembimbing()
    {
        // Redirect to the new complete method
        $this->update_pembimbing_lengkap();
    }

    function hapus_pembimbing($pembimbing_code = null)
    {
        $this->load->model('M_pembimbing');
        $this->load->model('M_pengelompokan');
        
        $cekpembimbing = $this->M_pembimbing->get_data_pembimbing($pembimbing_code);
        if($cekpembimbing){
            // Hapus semua relasi pembimbing dari tb_pengelompokan terlebih dahulu
            $this->db->where('pembimbing_id', $cekpembimbing->pembimbing_id);
            $this->db->delete('tb_pengelompokan');
            
            // Hapus dari tb_pembimbing
            $this->M_pembimbing->delete_pembimbing($pembimbing_code);
            
            // Check if request is AJAX
            if($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'success', 'message' => 'Pembimbing deleted successfully']);
                return;
            }
            
            $this->session->set_flashdata('message', 'Berhasil menghapus data pembimbing dan semua relasi yang terkait');
            redirect('hubin/view/data-pembimbing');
        }else{
            if($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'error', 'message' => 'Pembimbing not found']);
                return;
            }
            echo "<script>alert('Data pembimbing tidak ditemukan');</script>";
            redirect('hubin/view/data-pembimbing');
        }        
    }
    
    function batch_delete_pembimbing()
    {
        $this->load->model('M_pembimbing');
        
        $selected_pembimbing = $this->input->post('selected_pembimbing');
        
        if (!$selected_pembimbing || !is_array($selected_pembimbing)) {
            $this->session->set_flashdata('error_message', 'Tidak ada data pembimbing yang dipilih');
            redirect('hubin/view/data-pembimbing');
            return;
        }
        
        $deleted_count = 0;
        $failed_count = 0;
        
        foreach($selected_pembimbing as $pembimbing_code) {
            $cekpembimbing = $this->M_pembimbing->get_data_pembimbing($pembimbing_code);
            if($cekpembimbing) {
                // Hapus semua relasi pembimbing dari tb_pengelompokan terlebih dahulu
                $this->db->where('pembimbing_id', $cekpembimbing->pembimbing_id);
                $this->db->delete('tb_pengelompokan');
                
                if($this->M_pembimbing->delete_pembimbing($pembimbing_code)) {
                    $deleted_count++;
                } else {
                    $failed_count++;
                }
            } else {
                $failed_count++;
            }
        }
        
        if ($deleted_count > 0) {
            $this->session->set_flashdata('message', 'Berhasil menghapus ' . $deleted_count . ' data pembimbing' . ($failed_count > 0 ? ' (' . $failed_count . ' gagal)' : ''));
        } else {
            $this->session->set_flashdata('error_message', 'Tidak ada data pembimbing yang berhasil dihapus' . ($failed_count > 0 ? ' (' . $failed_count . ' gagal)' : ''));
        }
        
        redirect('hubin/view/data-pembimbing');
    }

    // ========== Pengumuman Management ==========
    function tambah_pengumuman()
    {
        $status = true;
        $this->form_validation->set_rules('judul', 'Judul Pengumuman', 'required');
        $this->form_validation->set_rules('isi', 'Isi Pengumuman', 'required');
        
        if ($this->form_validation->run() == FALSE) {
            $status = false;
        }

        if($status){
            $this->load->model('M_pengumuman');
            $userdata = $this->session->userdata('userdata');
            $data = array(
                'judul' => $this->input->post('judul'),
                'isi' => $this->input->post('isi'),
                'created_by' => $userdata['id']
            );
            $this->M_pengumuman->tambah_pengumuman($data);
            $this->session->set_flashdata('message', 'Berhasil menambah pengumuman');
            redirect('hubin/view/pengumuman');
        }else{
            $this->view('tambah-pengumuman');
        }
    }

    function edit_pengumuman($pengumuman_id = null)
    {
        $this->load->model('M_pengumuman');
        $pengumuman = $this->M_pengumuman->get_pengumuman($pengumuman_id);
        if($pengumuman){
            $data['pengumuman'] = $pengumuman;
            $data['content'] = 'hubin/edit-pengumuman';
        }else{
            $data['content'] = '404';
        }
        $this->load->view('template', $data, FALSE);
    }
    
    function update_pengumuman()
    {
        $status = true;
        $content = 'hubin/edit-pengumuman';
        $pengumuman_id = $this->input->post('pengumuman_id');
        $this->load->model('M_pengumuman');
        $cekpengumuman = $this->M_pengumuman->get_pengumuman($pengumuman_id);
        if(!$cekpengumuman){
            $this->session->set_flashdata('error_message', 'Pengumuman tidak ditemukan');
            redirect('hubin/view/pengumuman');
            return;
        }else{
            $data['pengumuman'] = $cekpengumuman;
        }
        
        $this->form_validation->set_rules('pengumuman_id', 'ID Pengumuman', 'required');
        $this->form_validation->set_rules('judul', 'Judul Pengumuman', 'required');
        $this->form_validation->set_rules('isi', 'Isi Pengumuman', 'required');
        
        if ($this->form_validation->run() == FALSE) {
            $status = false;
        }

        if($status){
            $update = array(
                'judul' => $this->input->post('judul'),
                'isi' => $this->input->post('isi'),
                'updated_at' => date('Y-m-d H:i:s')
            );
            $this->M_pengumuman->update_pengumuman($update, $pengumuman_id);
            $this->session->set_flashdata('message', 'Berhasil update pengumuman');
            redirect('hubin/view/pengumuman');
        }

        $data['content'] = $content; 
        $this->load->view('template', $data, FALSE);
    }    function hapus_pengumuman($pengumuman_id = null)
    {
        $this->load->model('M_pengumuman');
        $cekpengumuman = $this->M_pengumuman->get_pengumuman($pengumuman_id);
        if($cekpengumuman){
            $this->M_pengumuman->delete_pengumuman($pengumuman_id);
            $this->session->set_flashdata('message', 'Berhasil menghapus pengumuman');
            redirect('hubin/view/pengumuman');
        }else{
            echo "<script>alert('Pengumuman tidak ditemukan');</script>";
            redirect('hubin/view/pengumuman');
        }        
    }

    // ========== API Endpoints for Real-time Data ==========
    function api_realtime_data()
    {
        // Check if user is logged in
        if(!$this->session->userdata('userdata')){
            $this->output->set_status_header(401);
            echo json_encode(array('error' => 'Unauthorized'));
            return;
        }

        try {
            // Get Pengumuman Terkini
            $pengumuman = $this->db->order_by('created_at', 'DESC')->limit(3)->get('tb_pengumuman')->result();
            $pengumuman_list = array();
            foreach($pengumuman as $p){
                $pengumuman_list[] = array(
                    'pengumuman_id' => $p->pengumuman_id,
                    'judul' => $p->judul,
                    'isi' => substr($p->isi, 0, 150),
                    'created_at' => date('l, d F Y', strtotime($p->created_at)),
                    'url' => base_url('hubin/view/detail-pengumuman/'.$p->pengumuman_id)
                );
            }

            // Get Selamat Datang data
            $userdata = $this->session->userdata('userdata');
            $selamat_datang = array(
                'nama_lengkap' => $userdata['nama_lengkap'],
                'group_name' => $userdata['group_name'],
                'tanggal' => date("d F Y, l")
            );

            // Get real-time statistics
            $jumlah_siswa = $this->db->get('tb_siswa')->num_rows();
            $jumlah_user = $this->db->get('tb_user')->num_rows();
            $jumlah_pembimbing = $this->db->get('tb_pembimbing')->num_rows();
            $jumlah_dudi = $this->db->get('tb_dudi')->num_rows();

            // Statistik Pengajuan
            $pengajuan_menunggu = $this->db->where('status_pengajuan', 'menunggu')->get('tb_siswa')->num_rows();
            $pengajuan_disetujui = $this->db->where('status_pengajuan', 'disetujui')->get('tb_siswa')->num_rows();
            $pengajuan_ditolak = $this->db->where('status_pengajuan', 'ditolak')->get('tb_siswa')->num_rows();
            $pengajuan_draft = $this->db->where('status_pengajuan', 'draft')->get('tb_siswa')->num_rows();

            // Statistik Pembimbing
            $pembimbing_penuh = 0;
            $pembimbing_available = 0;
            $this->db->select('tb_pembimbing.pembimbing_id, COUNT(tb_pengelompokan.siswa_id) as jumlah_siswa');
            $this->db->from('tb_pembimbing');
            $this->db->join('tb_pengelompokan', 'tb_pengelompokan.pembimbing_id = tb_pembimbing.pembimbing_id', 'left');
            $this->db->group_by('tb_pembimbing.pembimbing_id');
            $pembimbing_stats = $this->db->get()->result();
            if($pembimbing_stats) {
                foreach($pembimbing_stats as $p){
                    if($p->jumlah_siswa >= 20){
                        $pembimbing_penuh++;
                    } else {
                        $pembimbing_available++;
                    }
                }
            }

            // Aktivitas Terkini (Pengajuan baru hari ini)
            $pengajuan_hari_ini = $this->db->where('DATE(created_at)', date('Y-m-d'))->get('tb_siswa')->num_rows();

            // Siswa belum di-assign pembimbing
            $this->db->select('tb_siswa.siswa_id');
            $this->db->from('tb_siswa');
            $this->db->join('tb_pengelompokan', 'tb_pengelompokan.siswa_id = tb_siswa.siswa_id', 'left');
            $this->db->where('tb_pengelompokan.siswa_id IS NULL');
            $siswa_belum_assign = $this->db->get()->num_rows();

            // Siswa dengan pembimbing aktif
            $this->db->select('tb_siswa.siswa_id');
            $this->db->from('tb_siswa');
            $this->db->join('tb_pengelompokan', 'tb_pengelompokan.siswa_id = tb_siswa.siswa_id', 'inner');
            $this->db->join('tb_pembimbing', 'tb_pembimbing.pembimbing_id = tb_pengelompokan.pembimbing_id', 'inner');
            $siswa_dengan_pembimbing = $this->db->get()->num_rows();

            // Statistik Monitoring PKL (presensi & logbook)
            $total_presensi = $this->db->table_exists('tb_presensi') ? $this->db->count_all('tb_presensi') : 0;
            $total_logbook = 0;
            $logbook_valid = 0;
            $logbook_tidak_valid = 0;
            if ($this->db->table_exists('tb_logbook')) {
                $total_logbook = $this->db->count_all('tb_logbook');
                $this->db->where('status_validasi', 'valid');
                $logbook_valid = $this->db->count_all_results('tb_logbook');
                $this->db->where('status_validasi', 'tidak_valid');
                $logbook_tidak_valid = $this->db->count_all_results('tb_logbook');
            }

            // Return JSON response
            $this->output->set_content_type('application/json');
            echo json_encode(array(
                'pengumuman_terkini' => $pengumuman_list,
                'selamat_datang' => $selamat_datang,
                'statistik' => array(
                    'jumlah_siswa' => $jumlah_siswa,
                    'jumlah_user' => $jumlah_user,
                    'jumlah_pembimbing' => $jumlah_pembimbing,
                    'jumlah_dudi' => $jumlah_dudi,
                    'pengajuan_menunggu' => $pengajuan_menunggu,
                    'pengajuan_disetujui' => $pengajuan_disetujui,
                    'pengajuan_ditolak' => $pengajuan_ditolak,
                    'pengajuan_draft' => $pengajuan_draft,
                    'pembimbing_penuh' => $pembimbing_penuh,
                    'pembimbing_available' => $pembimbing_available,
                    'pengajuan_hari_ini' => $pengajuan_hari_ini,
                    'siswa_belum_assign' => $siswa_belum_assign,
                    'siswa_dengan_pembimbing' => $siswa_dengan_pembimbing,
                    'total_presensi' => $total_presensi,
                    'total_logbook' => $total_logbook,
                    'logbook_valid' => $logbook_valid,
                    'logbook_tidak_valid' => $logbook_tidak_valid
                ),
                'timestamp' => date('Y-m-d H:i:s')
            ));
        } catch (Exception $e) {
            // Return error response instead of crashing
            $this->output->set_content_type('application/json');
            echo json_encode(array(
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ));
        }
    }

    public function reset_periode_pkl()
    {
        if ($this->input->method() !== 'post') {
            redirect('hubin/view/home');
            return;
        }

        $this->db->trans_start();
        $this->db->query('CREATE TABLE IF NOT EXISTS tb_presensi_arsip LIKE tb_presensi');
        $this->db->query('CREATE TABLE IF NOT EXISTS tb_logbook_arsip LIKE tb_logbook');
        $this->db->query('CREATE TABLE IF NOT EXISTS tb_penilaian_arsip LIKE tb_penilaian');
        foreach (['tb_presensi_arsip' => 'presensi_id', 'tb_logbook_arsip' => 'logbook_id', 'tb_penilaian_arsip' => 'penilaian_id'] as $table => $primary_key) {
            if (!$this->db->field_exists('archived_at', $table)) {
                $this->db->query("ALTER TABLE `{$table}` ADD COLUMN `archived_at` DATETIME NULL");
            }
            $this->db->query("INSERT INTO `{$table}` SELECT source.*, NOW() FROM `" . str_replace('_arsip', '', $table) . "` source");
        }
        $this->db->empty_table('tb_presensi');
        $this->db->empty_table('tb_logbook');
        $this->db->empty_table('tb_penilaian');
        $this->db->empty_table('tb_pengelompokan');
        $this->db->set([
            'dudi_id' => null,
            'pembimbing_id' => null,
            'status_pengajuan' => 'draft',
            'tanggal_mulai' => null,
            'tanggal_selesai' => null,
            'lama_pelaksanaan' => null,
            'periode' => null,
            'mentor_assignment_notified' => 0,
            'other_dudi_nama' => null,
            'other_dudi_alamat' => null,
            'other_dudi_telepon' => null,
            'other_dudi_email' => null,
            'other_dudi_pic' => null,
            'other_dudi_nip_pic' => null,
            'other_dudi_instruktur' => null,
            'other_dudi_nip_instruktur' => null,
            'other_dudi_latitude' => null,
            'other_dudi_longitude' => null
        ])->update('tb_siswa');
        $this->db->trans_complete();

        $this->session->set_flashdata($this->db->trans_status() ? 'message' : 'error_message', $this->db->trans_status() ? 'Periode PKL berhasil direset. Data lama telah diarsipkan dan siswa dapat mengajukan PKL untuk periode baru.' : 'Reset periode PKL gagal dan transaksi dibatalkan.');
        redirect('hubin/view/home');
    }
    
    // ========== Data DUDI Terpisah ==========
    
    // View data DUDI terpisah dengan tab
    function data_dudi_terpisah()
    {
        $this->load->model('M_dudi');
        
        // Get all DUDI data with student data integration for synchronization
        // For student-submitted DUDI, we need to check if the data was properly transferred during approval
        $sql = "SELECT d.*,
            CASE 
                WHEN d.sumber_data = 'siswa' AND (d.dudi_alamat IS NULL OR d.dudi_alamat = '' OR d.dudi_alamat = 'Alamat belum diisi') THEN NULL
                WHEN d.sumber_data = 'siswa' THEN d.dudi_alamat
                ELSE d.dudi_alamat
            END as display_alamat,
            CASE 
                WHEN d.sumber_data = 'siswa' AND (d.dudi_telepon IS NULL OR d.dudi_telepon = '') THEN NULL
                WHEN d.sumber_data = 'siswa' THEN d.dudi_telepon
                ELSE d.dudi_telepon
            END as display_telepon,
            CASE 
                WHEN d.sumber_data = 'siswa' AND (d.dudi_email IS NULL OR d.dudi_email = '') THEN NULL
                WHEN d.sumber_data = 'siswa' THEN d.dudi_email
                ELSE d.dudi_email
            END as display_email,
            CASE 
                WHEN d.sumber_data = 'siswa' AND (d.dudi_pic IS NULL OR d.dudi_pic = '') THEN NULL
                WHEN d.sumber_data = 'siswa' THEN d.dudi_pic
                ELSE d.dudi_pic
            END as display_pic,
            CASE 
                WHEN d.sumber_data = 'siswa' AND (d.dudi_nip_pic IS NULL OR d.dudi_nip_pic = '') THEN NULL
                WHEN d.sumber_data = 'siswa' THEN d.dudi_nip_pic
                ELSE d.dudi_nip_pic
            END as display_nip_pic,
            CASE 
                WHEN d.sumber_data = 'siswa' AND (d.dudi_instruktur IS NULL OR d.dudi_instruktur = '') THEN NULL
                WHEN d.sumber_data = 'siswa' THEN d.dudi_instruktur
                ELSE d.dudi_instruktur
            END as display_instruktur,
            CASE 
                WHEN d.sumber_data = 'siswa' AND (d.dudi_nip_instruktur IS NULL OR d.dudi_nip_instruktur = '') THEN NULL
                WHEN d.sumber_data = 'siswa' THEN d.dudi_nip_instruktur
                ELSE d.dudi_nip_instruktur
            END as display_nip_instruktur
            FROM tb_dudi d 
            GROUP BY d.dudi_id
            ORDER BY d.dudi_nama ASC";
        
        $query = $this->db->query($sql);
        $all_dudi_with_student_data = $query->result();
        
        // Filter data for each category based on the synchronized data
        $dudi_mitra = array();
        $dudi_non_mitra = array();
        $dudi_pengajuan = array();
        $dudi_siswa = array();
        
        foreach($all_dudi_with_student_data as $dudi) {
            if($dudi->status_kerjasama == 'mitra') {
                $dudi_mitra[] = $dudi;
            } elseif($dudi->status_kerjasama == 'non_mitra') {
                $dudi_non_mitra[] = $dudi;
            } elseif($dudi->status_kerjasama == 'pengajuan') {
                $dudi_pengajuan[] = $dudi;
            }
            
            if($dudi->sumber_data == 'siswa') {
                $dudi_siswa[] = $dudi;
            }
        }
        
        // Convert arrays to objects for compatibility
        $data['dudi_mitra'] = (object)[
            'result' => function() use ($dudi_mitra) { return $dudi_mitra; },
            'num_rows' => function() use ($dudi_mitra) { return count($dudi_mitra); }
        ];
        
        $data['dudi_non_mitra'] = (object)[
            'result' => function() use ($dudi_non_mitra) { return $dudi_non_mitra; },
            'num_rows' => function() use ($dudi_non_mitra) { return count($dudi_non_mitra); }
        ];
        
        $data['dudi_pengajuan'] = (object)[
            'result' => function() use ($dudi_pengajuan) { return $dudi_pengajuan; },
            'num_rows' => function() use ($dudi_pengajuan) { return count($dudi_pengajuan); }
        ];
        
        $data['dudi_siswa'] = (object)[
            'result' => function() use ($dudi_siswa) { return $dudi_siswa; },
            'num_rows' => function() use ($dudi_siswa) { return count($dudi_siswa); }
        ];
        
        // Load statistik
        $data['stats'] = $this->M_dudi->get_dudi_statistics();
        
        $data['content'] = 'hubin/data-dudi-terpisah';
        $this->load->view('template', $data, FALSE);
    }
    
    // Ubah status DUDI
    function ubah_status_dudi($dudi_code, $status_baru)
    {
        $this->load->model('M_dudi');
        $dudi = $this->M_dudi->get_dudi($dudi_code);
        
        if(!$dudi) {
            $this->session->set_flashdata('error_message', 'DUDI tidak ditemukan');
            redirect('hubin/data_dudi_terpisah');
        }
        
        // Validation: Check if DUDI with same name already exists as mitra when changing to mitra
        if($status_baru == 'mitra') {
            $existing_mitra = $this->M_dudi->get_dudi_by_nama_and_status($dudi->dudi_nama, 'mitra');
            if($existing_mitra && $existing_mitra->dudi_code != $dudi_code) {
                $this->session->set_flashdata('error_message', 'DUDI dengan nama \'' . $dudi->dudi_nama . '\' sudah ada sebagai mitra');
                redirect('hubin/data_dudi_terpisah');
            }
        }
        
        // Mapping status
        $status_mapping = [
            'mitra' => ['is_mitra' => '1', 'status_kerjasama' => 'mitra'],
            'non_mitra' => ['is_mitra' => '0', 'status_kerjasama' => 'non_mitra'],
            'pengajuan' => ['is_mitra' => '0', 'status_kerjasama' => 'pengajuan']
        ];
        
        if(!isset($status_mapping[$status_baru])) {
            $this->session->set_flashdata('error_message', 'Status tidak valid');
            redirect('hubin/data_dudi_terpisah');
        }
        
        $update_data = $status_mapping[$status_baru];
        $this->M_dudi->update_dudi($update_data, $dudi_code);
        
        $status_labels = [
            'mitra' => 'mitra',
            'non_mitra' => 'non-mitra',
            'pengajuan' => 'pengajuan'
        ];
        
        $this->session->set_flashdata('message', 'Status DUDI berhasil diubah menjadi ' . $status_labels[$status_baru]);
        redirect('hubin/data_dudi_terpisah');
    }
    
    // Rekomendasikan DUDI dari siswa
    function rekomendasikan_dudi()
    {
        $this->load->model('M_dudi');
        
        $this->form_validation->set_rules('dudi_nama', 'Nama DUDI', 'required');
        
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error_message', validation_errors());
            redirect('hubin/data_dudi_terpisah');
        }
        
        $nama_dudi = $this->input->post('dudi_nama');
        
        // Cek apakah DUDI sudah ada
        $existing_dudi = $this->M_dudi->get_dudi_by_nama($nama_dudi);
        if($existing_dudi) {
            $this->session->set_flashdata('error_message', 'DUDI dengan nama tersebut sudah ada dalam sistem');
            redirect('hubin/data_dudi_terpisah');
        }
        
        // Data siswa (bisa diambil dari session atau form)
        $data_siswa = [
            'alamat' => $this->input->post('dudi_alamat'),
            'telepon' => $this->input->post('dudi_telepon'),
            'email' => $this->input->post('dudi_email')
        ];
        
        $result = $this->M_dudi->rekomendasikan_dudi($nama_dudi, $data_siswa);
        
        if($result) {
            $this->session->set_flashdata('message', 'Rekomendasi DUDI berhasil diajukan dan menunggu verifikasi');
        } else {
            $this->session->set_flashdata('error_message', 'Gagal mengajukan rekomendasi DUDI');
        }
        
        redirect('hubin/data_dudi_terpisah');
    }
    
    // Tambah DUDI dengan status khusus
    function tambah_dudi_khusus()
    {
        $status = true;
        $this->form_validation->set_rules('dudi_nama', 'Nama DUDI', 'required');
        $this->form_validation->set_rules('dudi_alamat', 'Alamat DUDI', 'required');
        
        if ($this->form_validation->run() == FALSE) {
            $status = false;
        }
        
        if($status){
            $this->load->model('M_dudi');
            
            // Tentukan status berdasarkan sumber
            $sumber_data = $this->input->post('sumber_data') ? $this->input->post('sumber_data') : 'sekolah';
            $status_kerjasama = $this->input->post('status_kerjasama') ? $this->input->post('status_kerjasama') : 'mitra';
            $is_mitra = ($status_kerjasama == 'mitra') ? '1' : '0';
            
            $data = array(
                'dudi_nama' => $this->input->post('dudi_nama'),
                'dudi_alamat' => $this->input->post('dudi_alamat'),
                'dudi_telepon' => $this->input->post('dudi_telepon'),
                'dudi_email' => $this->input->post('dudi_email'),
                'dudi_pic' => $this->input->post('dudi_pic'),
                'dudi_nip_pic' => $this->input->post('dudi_nip_pic'),
                'dudi_instruktur' => $this->input->post('dudi_instruktur'),
                'dudi_nip_instruktur' => $this->input->post('dudi_nip_instruktur'),
                'is_mitra' => $is_mitra,
                'status_kerjasama' => $status_kerjasama,
                'sumber_data' => $sumber_data
            );
            
            $this->M_dudi->tambah_dudi($data);
            $this->session->set_flashdata('message', 'Berhasil menambah data DUDI dengan status khusus');
            redirect('hubin/data_dudi_terpisah');
        } else {
            $this->session->set_flashdata('error_message', validation_errors());
            redirect('hubin/data_dudi_terpisah');
        }
    }
    


    

    
    // Fungsi untuk menampilkan ID Card siswa
    function id_card_siswa($siswa_id = null)
    {
        if (!$siswa_id) {
            $this->session->set_flashdata('error_message', 'ID siswa tidak ditemukan');
            redirect('hubin/view/assign-pembimbing');
        }
        
        $this->load->model('M_siswa');
        $this->load->model('M_dudi');
        
        // Ambil data siswa
        $this->db->select('tb_siswa.*, tb_user.nama_lengkap, tb_user.foto_profil, tb_dudi.dudi_nama');
        $this->db->from('tb_siswa');
        $this->db->join('tb_user', 'tb_user.id = tb_siswa.user_id', 'left');
        $this->db->join('tb_dudi', 'tb_dudi.dudi_id = tb_siswa.dudi_id', 'left');
        $this->db->where('tb_siswa.siswa_id', $siswa_id);
        $siswa = $this->db->get()->row();
        
        if (!$siswa) {
            $this->session->set_flashdata('error_message', 'Data siswa tidak ditemukan');
            redirect('hubin/view/assign-pembimbing');
        }
        
        // Siapkan data untuk view
        $data['siswa'] = $siswa;
        
        // Load QR generator library
        $this->load->library('qr_generator');
        $data['qr_code_url'] = $this->qr_generator->generate_student_qr($siswa);
        
        // Load ID card as standalone page without template wrapper
        $this->load->view('hubin/id-card', $data, FALSE);
    }


    // Validate if student is already assigned to a specific pembimbing
    function validate_pembimbing_assignment()
    {
        $this->load->model('M_pengelompokan');
        
        $siswa_id = $this->input->post('siswa_id');
        $pembimbing_id = $this->input->post('pembimbing_id');
        
        if (!$siswa_id || !$pembimbing_id) {
            $response = array(
                'success' => false,
                'message' => 'Siswa ID dan Pembimbing ID harus disediakan'
            );
            
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
            return;
        }
        
        // Check if student is already assigned to this pembimbing using the correct table (tb_pengelompokan)
        // This prevents the "Unknown column 'siswa_id' in 'where clause'" error that occurs
        // when incorrectly querying tb_pembimbing table which doesn't have siswa_id column
        $is_assigned = $this->M_pengelompokan->validate_student_pembimbing_assignment($siswa_id, $pembimbing_id);
        
        if ($is_assigned) {
            $response = array(
                'success' => false,
                'message' => 'Siswa sudah diassign ke pembimbing ini.'
            );
        } else {
            $response = array(
                'success' => true,
                'message' => 'Valid untuk assignment.'
            );
        }
        
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }
    
    // Callback functions for unique validation during edit
    public function username_check($username, $user_code)
    {
        $this->db->where('username', $username);
        $this->db->where('user_code !=', $user_code);
        $query = $this->db->get('tb_user');
        
        if ($query->num_rows() > 0) {
            $this->form_validation->set_message('username_check', 'Username sudah digunakan oleh user lain.');
            return FALSE;
        } else {
            return TRUE;
        }
    }
    
    public function email_check($email, $user_code)
    {
        $this->db->where('email', $email);
        $this->db->where('user_code !=', $user_code);
        $query = $this->db->get('tb_user');
        
        if ($query->num_rows() > 0) {
            $this->form_validation->set_message('email_check', 'Email sudah digunakan oleh user lain.');
            return FALSE;
        } else {
            return TRUE;
        }
    }
    
    // Callback functions for DUDI validation
    public function dudi_nama_check($dudi_nama, $dudi_code)
    {
        $this->db->where('dudi_nama', $dudi_nama);
        $this->db->where('dudi_code !=', $dudi_code);
        $query = $this->db->get('tb_dudi');
        
        if ($query->num_rows() > 0) {
            $this->form_validation->set_message('dudi_nama_check', 'Nama DUDI sudah ada dalam database.');
            return FALSE;
        } else {
            return TRUE;
        }
    }
    
    public function dudi_email_check($dudi_email, $dudi_code)
    {
        $this->db->where('dudi_email', $dudi_email);
        $this->db->where('dudi_code !=', $dudi_code);
        $query = $this->db->get('tb_dudi');
        
        if ($query->num_rows() > 0) {
            $this->form_validation->set_message('dudi_email_check', 'Email DUDI sudah digunakan.');
            return FALSE;
        } else {
            return TRUE;
        }
    }
    
    // Handle password reset from detail biodata page
    function reset_password_from_detail($user_code = null)
    {
        if(!$user_code) {
            $this->session->set_flashdata('error_message', 'User code tidak ditemukan');
            redirect('hubin/view/data-user');
            return;
        }
        
        $this->load->model('M_user');
        $user = $this->M_user->get_data_user($user_code);
        
        if(!$user) {
            $this->session->set_flashdata('error_message', 'User tidak ditemukan');
            redirect('hubin/view/data-user');
            return;
        }
        
        // Generate new password (username + 123)
        $new_password = $user->username . '123';
        $hash = password_hash($new_password, PASSWORD_DEFAULT);
        
        $update = array('password' => $hash);
        $this->M_user->update_user($update, $user_code);
        
        $this->session->set_flashdata('message', 'Password berhasil direset. Password baru: <strong>' . $new_password . '</strong>');
        $this->session->set_flashdata('reset_password_username', $user->username);
        $this->session->set_flashdata('reset_password_new', $new_password);
        
        // Redirect back to detail page
        redirect('hubin/detail_biodata/' . $user_code);
    }
    
    // Handle password change from detail biodata page
    function change_password_from_detail($user_code = null)
    {
        if(!$user_code) {
            $this->session->set_flashdata('error_message', 'User code tidak ditemukan');
            redirect('hubin/view/data-user');
            return;
        }
        
        $this->load->model('M_user');
        $user = $this->M_user->get_data_user($user_code);
        
        if(!$user) {
            $this->session->set_flashdata('error_message', 'User tidak ditemukan');
            redirect('hubin/view/data-user');
            return;
        }
        
        $status = true;
        
        // Validate form data
        $this->form_validation->set_rules('password_baru', 'Password Baru', 'required|min_length[6]');
        $this->form_validation->set_rules('password_konfirmasi', 'Konfirmasi Password', 'required|matches[password_baru]');
        
        if ($this->form_validation->run() == FALSE) {
            $status = false;
            $this->session->set_flashdata('error_message', validation_errors());
        }
        
        if($status) {
            $hash = password_hash($this->input->post('password_baru'), PASSWORD_DEFAULT);
            $update = array('password' => $hash);
            $this->M_user->update_user($update, $user_code);
            
            $this->session->set_flashdata('message', 'Password berhasil diubah');
        }
        
        // Redirect back to detail page
        redirect('hubin/detail_biodata/' . $user_code);
    }
    
    private function column_exists($table, $column) {
        $query = $this->db->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
        return $query->num_rows() > 0;
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
}



