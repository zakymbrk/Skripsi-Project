<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Export_excel extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        require_role(1);
    }
    
    // Helper method untuk export data biodata lengkap siswa
    private function _export_siswa_engine($selected_raw = null, $filename_prefix = 'data-siswa-lengkap')
    {
        if (!class_exists('\\PhpOffice\\PhpSpreadsheet\\Spreadsheet')) {
            $autoload_path = FCPATH . 'vendor/autoload.php';
            if (file_exists($autoload_path)) {
                require_once $autoload_path;
            } else {
                die('Fitur Export Excel memerlukan library PhpSpreadsheet. Silakan install dependencies terlebih dahulu.');
            }
        }

        $this->load->model('M_siswa');
        $this->load->model('M_user');
        $this->load->model('M_dudi');

        $this->db->select('
            tb_user.id as user_id,
            tb_user.username,
            tb_user.nama_lengkap as user_nama,
            tb_user.email as user_email,
            tb_user.telepon as user_telepon,
            tb_user.alamat as user_alamat,
            tb_user.tempat_lahir as user_tempat_lahir,
            tb_user.tanggal_lahir as user_tanggal_lahir,
            tb_user.jenis_kelamin as user_jenis_kelamin,
            tb_user.nip_nim,
            tb_user.is_active as user_is_active,
            tb_user.created_at as user_created_at,
            tb_siswa.siswa_id,
            tb_siswa.siswa_code,
            tb_siswa.siswa_nama,
            tb_siswa.siswa_nis,
            tb_siswa.siswa_nisn,
            tb_siswa.siswa_jk,
            tb_siswa.siswa_tempat_lahir,
            tb_siswa.siswa_tanggal_lahir,
            tb_siswa.siswa_kelas,
            tb_siswa.siswa_jurusan,
            tb_siswa.siswa_asal_sekolah,
            tb_siswa.siswa_telepon,
            tb_siswa.siswa_alamat,
            tb_siswa.status_pengajuan,
            tb_siswa.tanggal_mulai,
            tb_siswa.tanggal_selesai,
            tb_siswa.lama_pelaksanaan,
            tb_siswa.periode,
            tb_siswa.other_dudi_nama,
            tb_siswa.other_dudi_alamat,
            tb_siswa.other_dudi_telepon,
            tb_siswa.other_dudi_email,
            tb_siswa.other_dudi_pic,
            tb_siswa.created_at as siswa_created_at,
            tb_dudi.dudi_nama,
            tb_dudi.dudi_alamat,
            tb_dudi.dudi_telepon,
            tb_dudi.dudi_email,
            tb_dudi.dudi_pic,
            tb_dudi.is_mitra as dudi_is_mitra,
            tb_pembimbing.pembimbing_nama,
            tb_pembimbing.pembimbing_nip,
            tb_pembimbing.tempat_tugas as pembimbing_tempat_tugas
        ');
        $this->db->from('tb_user');
        $this->db->join('tb_siswa', 'tb_siswa.user_id = tb_user.id', 'left');
        $this->db->join('tb_dudi', 'tb_dudi.dudi_id = tb_siswa.dudi_id', 'left');
        $this->db->join('tb_pengelompokan', 'tb_pengelompokan.siswa_id = tb_siswa.siswa_id', 'left');
        $this->db->join('tb_pembimbing', 'tb_pembimbing.pembimbing_id = tb_pengelompokan.pembimbing_id', 'left');
        $this->db->where('tb_user.level', 3);

        if (!empty($selected_raw)) {
            $selected_array = is_array($selected_raw) ? $selected_raw : explode(',', $selected_raw);
            $selected_array = array_map('trim', $selected_array);
            
            $this->db->group_start();
            $this->db->where_in('tb_siswa.siswa_id', $selected_array);
            $this->db->or_where_in('tb_siswa.siswa_code', $selected_array);
            $this->db->or_where_in('tb_user.id', $selected_array);
            $this->db->or_where_in('tb_user.user_code', $selected_array);
            $this->db->or_where_in('tb_user.username', $selected_array);
            $this->db->group_end();
        }

        $this->db->order_by('tb_user.id', 'ASC');
        $siswa = $this->db->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Biodata Siswa');

        // Header tabel lengkap (30 kolom)
        $headers = [
            'No',
            'Nama Siswa',
            'Username',
            'NIS',
            'NISN',
            'Jenis Kelamin',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Kelas',
            'Jurusan',
            'Asal Sekolah',
            'Telepon Siswa',
            'Email Siswa',
            'Alamat Lengkap Siswa',
            'Tempat PKL / Perusahaan (DUDI)',
            'Kategori DUDI',
            'Alamat Perusahaan',
            'Telepon Perusahaan',
            'Email Perusahaan',
            'PIC Perusahaan',
            'Guru Pembimbing',
            'NIP Pembimbing',
            'Tempat Tugas Pembimbing',
            'Tanggal Mulai PKL',
            'Tanggal Selesai PKL',
            'Lama Pelaksanaan (Hari)',
            'Status Pengajuan PKL',
            'Periode PKL',
            'Status Akun',
            'Tanggal Terdaftar'
        ];

        $header_style = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '1565C0']],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
        ];

        $col = 'A';
        foreach($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->applyFromArray($header_style);
            $col++;
        }
        $sheet->getRowDimension(1)->setRowHeight(28);

        $row_num = 2;
        $no = 1;
        foreach($siswa->result() as $data) {
            // Resolusi data nama
            $nama_siswa = !empty($data->siswa_nama) ? $data->siswa_nama : (!empty($data->user_nama) ? $data->user_nama : '-');
            
            // Resolusi NIS & NISN
            $nis = !empty($data->siswa_nis) ? $data->siswa_nis : (!empty($data->nip_nim) ? $data->nip_nim : '');
            $nisn = !empty($data->siswa_nisn) ? $data->siswa_nisn : '';

            // Resolusi Jenis Kelamin
            $raw_jk = !empty($data->siswa_jk) ? $data->siswa_jk : (!empty($data->user_jenis_kelamin) ? $data->user_jenis_kelamin : '');
            if ($raw_jk == 'L' || $raw_jk == 'Laki-laki') {
                $jenis_kelamin = 'Laki-laki';
            } elseif ($raw_jk == 'P' || $raw_jk == 'Perempuan') {
                $jenis_kelamin = 'Perempuan';
            } else {
                $jenis_kelamin = $raw_jk;
            }

            // Resolusi Tempat & Tanggal Lahir
            $tempat_lahir = !empty($data->siswa_tempat_lahir) ? $data->siswa_tempat_lahir : (!empty($data->user_tempat_lahir) ? $data->user_tempat_lahir : '');
            $raw_tgl_lahir = !empty($data->siswa_tanggal_lahir) ? $data->siswa_tanggal_lahir : (!empty($data->user_tanggal_lahir) ? $data->user_tanggal_lahir : '');
            $tanggal_lahir = (!empty($raw_tgl_lahir) && $raw_tgl_lahir != '0000-00-00') ? date('d-m-Y', strtotime($raw_tgl_lahir)) : '';

            // Kelas & Jurusan
            $kelas = !empty($data->siswa_kelas) ? $data->siswa_kelas : '';
            $jurusan = !empty($data->siswa_jurusan) ? $data->siswa_jurusan : '';
            $asal_sekolah = !empty($data->siswa_asal_sekolah) ? $data->siswa_asal_sekolah : 'SMK ITIKURIH HIBARNA';

            // Kontak Siswa
            $telepon = !empty($data->siswa_telepon) ? $data->siswa_telepon : (!empty($data->user_telepon) ? $data->user_telepon : '');
            $email = !empty($data->user_email) ? $data->user_email : '';
            $alamat = !empty($data->siswa_alamat) ? $data->siswa_alamat : (!empty($data->user_alamat) ? $data->user_alamat : '');

            // DUDI info
            $dudi_nama = !empty($data->dudi_nama) ? $data->dudi_nama : (!empty($data->other_dudi_nama) ? $data->other_dudi_nama : '-');
            $kategori_dudi = (isset($data->dudi_is_mitra) && $data->dudi_is_mitra == 1) ? 'Mitra' : 'Non-Mitra';
            $dudi_alamat = !empty($data->dudi_alamat) ? $data->dudi_alamat : (!empty($data->other_dudi_alamat) ? $data->other_dudi_alamat : '');
            $dudi_telepon = !empty($data->dudi_telepon) ? $data->dudi_telepon : (!empty($data->other_dudi_telepon) ? $data->other_dudi_telepon : '');
            $dudi_email = !empty($data->dudi_email) ? $data->dudi_email : (!empty($data->other_dudi_email) ? $data->other_dudi_email : '');
            $dudi_pic = !empty($data->dudi_pic) ? $data->dudi_pic : (!empty($data->other_dudi_pic) ? $data->other_dudi_pic : '');

            // Pembimbing info
            $pembimbing_nama = !empty($data->pembimbing_nama) ? $data->pembimbing_nama : 'Belum Di-Assign';
            $pembimbing_nip = !empty($data->pembimbing_nip) ? $data->pembimbing_nip : '-';
            $tempat_tugas = !empty($data->pembimbing_tempat_tugas) ? $data->pembimbing_tempat_tugas : '-';

            // PKL Info
            $tgl_mulai = (!empty($data->tanggal_mulai) && $data->tanggal_mulai != '0000-00-00') ? date('d-m-Y', strtotime($data->tanggal_mulai)) : '';
            $tgl_selesai = (!empty($data->tanggal_selesai) && $data->tanggal_selesai != '0000-00-00') ? date('d-m-Y', strtotime($data->tanggal_selesai)) : '';

            if(!empty($data->lama_pelaksanaan)) {
                $lama_hari = $data->lama_pelaksanaan;
            } elseif(!empty($data->tanggal_mulai) && !empty($data->tanggal_selesai)) {
                $s = new DateTime($data->tanggal_mulai);
                $e = new DateTime($data->tanggal_selesai);
                $lama_hari = $s->diff($e)->days + 1;
            } else {
                $lama_hari = '';
            }

            $status_pengajuan = ucfirst(!empty($data->status_pengajuan) ? $data->status_pengajuan : 'Draft');
            $periode = !empty($data->periode) ? $data->periode : '';
            $status_akun = ($data->user_is_active == 1) ? 'Aktif' : 'Non-Aktif';
            
            $raw_tgl_daftar = !empty($data->siswa_created_at) ? $data->siswa_created_at : (!empty($data->user_created_at) ? $data->user_created_at : '');
            $tgl_daftar = !empty($raw_tgl_daftar) ? date('d-m-Y H:i', strtotime($raw_tgl_daftar)) : '';

            // Tulis data per cell
            $c = 'A';
            $sheet->setCellValue($c++ . $row_num, $no++);
            $sheet->setCellValue($c++ . $row_num, $nama_siswa);
            $sheet->setCellValue($c++ . $row_num, $data->username);
            $sheet->setCellValueExplicit($c++ . $row_num, (string)$nis, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit($c++ . $row_num, (string)$nisn, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue($c++ . $row_num, $jenis_kelamin);
            $sheet->setCellValue($c++ . $row_num, $tempat_lahir);
            $sheet->setCellValue($c++ . $row_num, $tanggal_lahir);
            $sheet->setCellValue($c++ . $row_num, $kelas);
            $sheet->setCellValue($c++ . $row_num, $jurusan);
            $sheet->setCellValue($c++ . $row_num, $asal_sekolah);
            $sheet->setCellValueExplicit($c++ . $row_num, (string)$telepon, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue($c++ . $row_num, $email);
            $sheet->setCellValue($c++ . $row_num, $alamat);
            $sheet->setCellValue($c++ . $row_num, $dudi_nama);
            $sheet->setCellValue($c++ . $row_num, $kategori_dudi);
            $sheet->setCellValue($c++ . $row_num, $dudi_alamat);
            $sheet->setCellValueExplicit($c++ . $row_num, (string)$dudi_telepon, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue($c++ . $row_num, $dudi_email);
            $sheet->setCellValue($c++ . $row_num, $dudi_pic);
            $sheet->setCellValue($c++ . $row_num, $pembimbing_nama);
            $sheet->setCellValueExplicit($c++ . $row_num, (string)$pembimbing_nip, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue($c++ . $row_num, $tempat_tugas);
            $sheet->setCellValue($c++ . $row_num, $tgl_mulai);
            $sheet->setCellValue($c++ . $row_num, $tgl_selesai);
            $sheet->setCellValue($c++ . $row_num, $lama_hari);
            $sheet->setCellValue($c++ . $row_num, $status_pengajuan);
            $sheet->setCellValue($c++ . $row_num, $periode);
            $sheet->setCellValue($c++ . $row_num, $status_akun);
            $sheet->setCellValue($c++ . $row_num, $tgl_daftar);

            $row_num++;
        }

        // Auto-fit kolom
        $last_col = 'AD'; // 30 kolom -> Kolom AD
        foreach(range('A', 'Z') as $col_char) {
            $sheet->getColumnDimension($col_char)->setAutoSize(true);
        }
        $sheet->getColumnDimension('AA')->setAutoSize(true);
        $sheet->getColumnDimension('AB')->setAutoSize(true);
        $sheet->getColumnDimension('AC')->setAutoSize(true);
        $sheet->getColumnDimension('AD')->setAutoSize(true);

        $filename = $filename_prefix . '-' . date('Ymd_His') . '.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    // Export data siswa dalam format XLSX (dari menu daftar siswa)
    function export_daftar_siswa()
    {
        $selected_ids = $this->input->get('selected');
        $this->_export_siswa_engine($selected_ids, 'data-siswa-lengkap');
    }

    // Export data siswa (alias & dipanggil tombol di daftar-siswa)
    function export_data_siswa()
    {
        $selected_ids = $this->input->get('selected');
        $this->_export_siswa_engine($selected_ids, 'data-siswa-lengkap');
    }

    // Download template import siswa - HANYA 8 kolom wajib
    function export_data_siswa_template()
    {
        if (!class_exists('\\PhpOffice\\PhpSpreadsheet\\Spreadsheet')) {
            $autoload_path = FCPATH . 'vendor/autoload.php';
            if (file_exists($autoload_path)) {
                require_once $autoload_path;
            } else {
                die('Fitur Export Excel memerlukan library PhpSpreadsheet. Silakan install dependencies terlebih dahulu.');
            }
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import Siswa');

        // 8 kolom saja
        $headers = [
            'A' => 'Nama Siswa',
            'B' => 'NIS',
            'C' => 'NISN',
            'D' => 'Jenis Kelamin (L/P)',
            'E' => 'Kelas',
            'F' => 'Jurusan',
            'G' => 'Username',
            'H' => 'Password'
        ];

        $header_style = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '2E7D32']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
        ];

        foreach($headers as $col => $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->applyFromArray($header_style);
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Baris contoh
        $sample = [
            ['Andi Setiawan', '2024001', '0001234567890', 'L', 'XI TKJ 1', 'Teknik Komputer dan Jaringan', 'andi_tkj', 'password123'],
            ['Budi Santoso',  '2024002', '0001234567891', 'L', 'XI RPL 1', 'Rekayasa Perangkat Lunak',     'budi_rpl', 'password123'],
            ['Citra Dewi',    '2024003', '0001234567892', 'P', 'XI MM 1',  'Multimedia',                   'citra_mm', 'password123'],
        ];

        $row = 2;
        foreach($sample as $data) {
            $col = 'A';
            foreach($data as $val) {
                $sheet->setCellValue($col . $row, $val);
                $col++;
            }
            $row++;
        }

        // Instruksi
        $instruksi_row = $row + 1;
        $sheet->setCellValue('A' . $instruksi_row, 'PETUNJUK PENGISIAN:');
        $sheet->getStyle('A' . $instruksi_row)->getFont()->setBold(true);
        $sheet->setCellValue('A' . ($instruksi_row+1), '* Kolom Nama Siswa, Kelas, Username, dan Password wajib diisi');
        $sheet->setCellValue('A' . ($instruksi_row+2), '* Jenis Kelamin: isi L (Laki-laki) atau P (Perempuan)');
        $sheet->setCellValue('A' . ($instruksi_row+3), '* Username harus unik, tidak boleh sama dengan yang sudah ada');
        $sheet->setCellValue('A' . ($instruksi_row+4), '* Hapus baris contoh sebelum mengupload');

        $filename = 'template-import-siswa-' . date('Ymd') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    // Download template import pembimbing
    function export_data_pembimbing_template()
    {
        if (!class_exists('\\PhpOffice\\PhpSpreadsheet\\Spreadsheet')) {
            $autoload_path = FCPATH . 'vendor/autoload.php';
            if (file_exists($autoload_path)) {
                require_once $autoload_path;
            } else {
                die('Fitur Export Excel memerlukan library PhpSpreadsheet. Silakan install dependencies terlebih dahulu.');
            }
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import Pembimbing');

        $headers = [
            'A' => 'Nama Pembimbing',
            'B' => 'NIP',
            'C' => 'Jenis Kelamin (L/P)',
            'D' => 'Email',
            'E' => 'No Telepon',
            'F' => 'Alamat',
            'G' => 'Tempat Lahir',
            'H' => 'Tanggal Lahir (YYYY-MM-DD)',
            'I' => 'Pendidikan Terakhir',
            'J' => 'Jabatan',
            'K' => 'Jurusan Keahlian',
            'L' => 'Tahun Masuk',
            'M' => 'Status Kepegawaian',
            'N' => 'Tempat Tugas',
            'O' => 'Username',
            'P' => 'Password'
        ];

        $header_style = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '1565C0']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
        ];

        foreach($headers as $col => $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->applyFromArray($header_style);
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Baris contoh
        $sample = [
            [
                'Drs. H. Maman Suryaman, M.Pd.', '197508122000031001', 'L', 'maman@example.com', '081234567890',
                'Jl. Raya Laswi No. 100, Ciparay', 'Bandung', '1975-08-12', 'S2', 'Guru Pembimbing',
                'Rekayasa Perangkat Lunak', '2010', 'PNS', 'SMK ITIKURIH HIBARNA', 'pembimbing_maman', 'password123'
            ],
            [
                'Siti Nurhaliza, S.Kom.', '198503152010012005', 'P', 'siti@example.com', '081298765432',
                'Jl. Contoh No. 45, Bandung', 'Bandung', '1985-03-15', 'S1', 'Guru Produktif',
                'Teknik Komputer dan Jaringan', '2012', 'GTY', 'SMK ITIKURIH HIBARNA', 'pembimbing_siti', 'password123'
            ]
        ];

        $row = 2;
        foreach($sample as $data) {
            $col = 'A';
            foreach($data as $val) {
                $sheet->setCellValue($col . $row, $val);
                $col++;
            }
            $row++;
        }

        // Instruksi
        $instruksi_row = $row + 1;
        $sheet->setCellValue('A' . $instruksi_row, 'PETUNJUK PENGISIAN:');
        $sheet->getStyle('A' . $instruksi_row)->getFont()->setBold(true);
        $sheet->setCellValue('A' . ($instruksi_row+1), '* Kolom Nama Pembimbing, NIP, Username, dan Password wajib diisi');
        $sheet->setCellValue('A' . ($instruksi_row+2), '* Jenis Kelamin: isi L (Laki-laki) atau P (Perempuan)');
        $sheet->setCellValue('A' . ($instruksi_row+3), '* Tanggal Lahir berformat: YYYY-MM-DD (contoh: 1980-05-20)');
        $sheet->setCellValue('A' . ($instruksi_row+4), '* Username dan NIP harus unik, tidak boleh sama dengan yang sudah terdaftar');
        $sheet->setCellValue('A' . ($instruksi_row+5), '* Password minimal 6 karakter');
        $sheet->setCellValue('A' . ($instruksi_row+6), '* Hapus baris contoh sebelum mengupload file');

        $filename = 'template-import-pembimbing-' . date('Ymd') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    // Export data biodata lengkap pembimbing dalam format XLSX
    function export_data_pembimbing()
    {
        if (!class_exists('\\PhpOffice\\PhpSpreadsheet\\Spreadsheet')) {
            $autoload_path = FCPATH . 'vendor/autoload.php';
            if (file_exists($autoload_path)) {
                require_once $autoload_path;
            } else {
                die('Fitur Export Excel memerlukan library PhpSpreadsheet. Silakan install dependencies terlebih dahulu.');
            }
        }
        
        $this->load->model('M_pembimbing');
        $selected_ids = $this->input->get('selected');
        
        $this->db->select('
            tb_pembimbing.*,
            tb_user.username,
            tb_user.email as user_email,
            tb_user.telepon as user_telepon,
            tb_user.alamat as user_alamat,
            tb_user.tempat_lahir as user_tempat_lahir,
            tb_user.tanggal_lahir as user_tanggal_lahir,
            tb_user.jenis_kelamin as user_jenis_kelamin,
            tb_user.nip_nim as user_nip_nim,
            tb_user.is_active as user_is_active,
            (SELECT COUNT(*) FROM tb_pengelompokan WHERE tb_pengelompokan.pembimbing_id = tb_pembimbing.pembimbing_id) as total_siswa_bimbingan
        ');
        $this->db->from('tb_pembimbing');
        $this->db->join('tb_user', 'tb_user.id = tb_pembimbing.user_id', 'left');
        
        if ($selected_ids) {
            $pembimbing_ids_array = explode(',', $selected_ids);
            $this->db->group_start();
            $this->db->where_in('tb_pembimbing.pembimbing_id', $pembimbing_ids_array);
            $this->db->or_where_in('tb_pembimbing.pembimbing_code', $pembimbing_ids_array);
            $this->db->group_end();
        }
        
        $this->db->order_by('tb_pembimbing.pembimbing_nama', 'ASC');
        $pembimbing = $this->db->get();
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Pembimbing');
        
        $headers = [
            'Nama Pembimbing',
            'NIP',
            'Jenis Kelamin (L/P)',
            'Email',
            'No Telepon',
            'Alamat',
            'Tempat Lahir',
            'Tanggal Lahir (YYYY-MM-DD)',
            'Pendidikan Terakhir',
            'Jabatan',
            'Jurusan Keahlian',
            'Tahun Masuk',
            'Status Kepegawaian',
            'Tempat Tugas',
            'Username',
            'Password'
        ];
        
        $header_style = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '00796B']],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
        ];

        $col = 'A';
        foreach($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->applyFromArray($header_style);
            $col++;
        }
        $sheet->getRowDimension(1)->setRowHeight(28);
        
        $row_num = 2;
        foreach($pembimbing->result() as $data){
            $tanggal_lahir = $data->tanggal_lahir ?: $data->user_tanggal_lahir;
            $tgl_lahir = (!empty($tanggal_lahir) && $tanggal_lahir != '0000-00-00') ? date('Y-m-d', strtotime($tanggal_lahir)) : '';
            $nama = $data->pembimbing_nama ?: $data->nama_lengkap;
            $email = $data->pembimbing_email ?: $data->user_email;
            $telepon = $data->pembimbing_telepon ?: $data->user_telepon;
            $alamat = $data->pembimbing_alamat ?: $data->user_alamat;

            $c = 'A';
            $sheet->setCellValue($c++ . $row_num, $nama ?: '');
            $sheet->setCellValueExplicit($c++ . $row_num, (string)($data->pembimbing_nip ?: $data->user_nip_nim ?: ''), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue($c++ . $row_num, $data->jenis_kelamin ?: $data->user_jenis_kelamin ?: '');
            $sheet->setCellValue($c++ . $row_num, $email ?: '');
            $sheet->setCellValueExplicit($c++ . $row_num, (string)($telepon ?: ''), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue($c++ . $row_num, $alamat ?: '');
            $sheet->setCellValue($c++ . $row_num, $data->tempat_lahir ?: $data->user_tempat_lahir ?: '');
            $sheet->setCellValue($c++ . $row_num, $tgl_lahir);
            $sheet->setCellValue($c++ . $row_num, $data->pendidikan_terakhir ?: '');
            $sheet->setCellValue($c++ . $row_num, $data->jabatan ?: '');
            $sheet->setCellValue($c++ . $row_num, $data->jurusan_keahlian ?: '');
            $sheet->setCellValue($c++ . $row_num, $data->tahun_masuk ?: '');
            $sheet->setCellValue($c++ . $row_num, $data->status_kepegawaian ?: '');
            $sheet->setCellValue($c++ . $row_num, $data->tempat_tugas ?: '');
            $sheet->setCellValue($c++ . $row_num, $data->username ?: '');
            $sheet->setCellValue($c++ . $row_num, '');
            
            $row_num++;
        }
        
        foreach(range('A', 'P') as $col_char) {
            $sheet->getColumnDimension($col_char)->setAutoSize(true);
        }
        
        $filename = 'data-pembimbing-lengkap-' . date("Ymd_His") . '.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    // Alias export daftar pembimbing
    function export_daftar_pembimbing()
    {
        $this->export_data_pembimbing();
    }

    // Export data DUDI lengkap dalam format XLSX
    function export_data_dudi()
    {
        if (!class_exists('\\PhpOffice\\PhpSpreadsheet\\Spreadsheet')) {
            $autoload_path = FCPATH . 'vendor/autoload.php';
            if (file_exists($autoload_path)) {
                require_once $autoload_path;
            } else {
                die('Fitur Export Excel memerlukan library PhpSpreadsheet. Silakan install dependencies terlebih dahulu.');
            }
        }
        
        $this->load->model('M_dudi');
        $selected_codes = $this->input->get('selected');
        
        $this->db->select('
            tb_dudi.*,
            (SELECT COUNT(*) FROM tb_siswa WHERE tb_siswa.dudi_id = tb_dudi.dudi_id) as total_siswa_pkl
        ');
        $this->db->from('tb_dudi');
        
        if ($selected_codes) {
            $dudi_codes_array = explode(',', $selected_codes);
            $this->db->group_start();
            $this->db->where_in('tb_dudi.dudi_code', $dudi_codes_array);
            $this->db->or_where_in('tb_dudi.dudi_id', $dudi_codes_array);
            $this->db->group_end();
        }
        
        $this->db->order_by('tb_dudi.dudi_nama', 'ASC');
        $dudi = $this->db->get();
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data DUDI');
        
        $headers = [
            'No',
            'Nama Perusahaan (DUDI)',
            'Kategori (Mitra/Non-Mitra)',
            'Status Kerjasama',
            'Alamat Lengkap Perusahaan',
            'Telepon',
            'Email',
            'Nama PIC',
            'NIP PIC',
            'Nama Instruktur Lapangan',
            'NIP Instruktur',
            'Latitude GPS',
            'Longitude GPS',
            'Radius Presensi (Meter)',
            'Sumber Data',
            'Jumlah Siswa PKL',
            'Tanggal Terdaftar'
        ];
        
        $header_style = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E65100']],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
        ];

        $col = 'A';
        foreach($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->applyFromArray($header_style);
            $col++;
        }
        $sheet->getRowDimension(1)->setRowHeight(28);
        
        $row_num = 2;
        $no = 1;
        foreach($dudi->result() as $data){
            $kategori = ($data->is_mitra == 1) ? 'Mitra' : 'Non-Mitra';
            $tgl_dibuat = !empty($data->created_at) ? date('d-m-Y H:i', strtotime($data->created_at)) : '';

            $c = 'A';
            $sheet->setCellValue($c++ . $row_num, $no++);
            $sheet->setCellValue($c++ . $row_num, $data->dudi_nama);
            $sheet->setCellValue($c++ . $row_num, $kategori);
            $sheet->setCellValue($c++ . $row_num, ucfirst($data->status_kerjasama ?: ''));
            $sheet->setCellValue($c++ . $row_num, $data->dudi_alamat ?: '');
            $sheet->setCellValueExplicit($c++ . $row_num, (string)($data->dudi_telepon ?: ''), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue($c++ . $row_num, $data->dudi_email ?: '');
            $sheet->setCellValue($c++ . $row_num, $data->dudi_pic ?: '');
            $sheet->setCellValueExplicit($c++ . $row_num, (string)($data->dudi_nip_pic ?: ''), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue($c++ . $row_num, $data->dudi_instruktur ?: '');
            $sheet->setCellValueExplicit($c++ . $row_num, (string)($data->dudi_nip_instruktur ?: ''), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue($c++ . $row_num, $data->latitude ?: '');
            $sheet->setCellValue($c++ . $row_num, $data->longitude ?: '');
            $sheet->setCellValue($c++ . $row_num, $data->radius ?: 100);
            $sheet->setCellValue($c++ . $row_num, ucfirst($data->sumber_data ?: 'sekolah'));
            $sheet->setCellValue($c++ . $row_num, (int)$data->total_siswa_pkl);
            $sheet->setCellValue($c++ . $row_num, $tgl_dibuat);
            
            $row_num++;
        }
        
        foreach(range('A', 'Q') as $col_char) {
            $sheet->getColumnDimension($col_char)->setAutoSize(true);
        }
        
        $filename = 'data-dudi-lengkap-' . date("Ymd_His") . '.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    // Import data siswa dengan template yang sesuai
    function import_daftar_siswa()
    {
        // Debug: Log the start of the import process
        log_message('debug', 'Import process started');
        
        // Check if user is logged in and has permission (Hubin level)
        $userdata = $this->session->userdata('userdata');
        log_message('debug', 'User data: ' . json_encode($userdata));
        
        if(!$userdata || $userdata['level'] != 1){
            log_message('error', 'User not authorized or not logged in');
            $this->session->set_flashdata('error_message', 'Anda tidak memiliki izin untuk melakukan import data.');
            redirect('hubin/view/daftar-siswa');
            return;
        }

        $config['upload_path'] = './uploads/import/';
        $config['allowed_types'] = 'xlsx|xls|csv';
        $config['max_size'] = 5120; // 5MB
        $config['file_name'] = 'import_daftar_siswa_' . time();

        // Create directory if not exists
        if(!is_dir($config['upload_path'])){
            mkdir($config['upload_path'], 0777, true);
        }

        $this->load->library('upload', $config);
        log_message('debug', 'Upload library loaded');

        if (!$this->upload->do_upload('file_excel')) {
            $error_message = $this->upload->display_errors('', '');
            log_message('error', 'Upload failed: ' . $error_message);
            $this->session->set_flashdata('error_message', $error_message);
            redirect('hubin/view/daftar-siswa');
            return;
        }

        $upload_data = $this->upload->data();
        $file_path = $config['upload_path'] . $upload_data['file_name'];
        log_message('debug', 'File uploaded successfully: ' . $file_path);

        try {
            $this->load->model('M_user');
            $this->load->model('M_siswa');
            log_message('debug', 'Models loaded successfully');
            
            $success_count = 0;
            $error_count = 0;
            $errors = array();

            // Determine file type and parse accordingly
            $file_extension = pathinfo($file_path, PATHINFO_EXTENSION);
            log_message('debug', 'File extension: ' . $file_extension);
            
            if(strtolower($file_extension) === 'csv') {
                // Handle CSV file
                log_message('debug', 'Processing CSV file');
                if (($handle = fopen($file_path, "r")) !== FALSE) {
                    $row_index = 0;
                    $header_check = null;
                    
                    while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $row_index++;
                        log_message('debug', 'Processing row ' . $row_index . ': ' . json_encode($row));
                        
                        // Check header row
                        if($row_index == 1) {
                            if (count($row) < 8) {
                                fclose($handle);
                                throw new Exception('File CSV harus memiliki minimal 8 kolom sesuai template. Ditemukan hanya ' . count($row) . ' kolom.');
                            }
                            $header_check = $row;
                            log_message('debug', 'Header row validated');
                            continue;
                        }

                        // Skip empty rows
                        if(empty($row[0])) {
                            log_message('debug', 'Skipping empty row ' . $row_index);
                            continue;
                        }

                        // Extract data dari 8 kolom template
                        $nama_siswa = trim($row[0]);
                        $nis = isset($row[1]) ? trim($row[1]) : '';
                        $nisn = isset($row[2]) ? trim($row[2]) : '';
                        $jenis_kelamin = isset($row[3]) ? trim($row[3]) : '';
                        $kelas = isset($row[4]) ? trim($row[4]) : '';
                        $jurusan = isset($row[5]) ? trim($row[5]) : '';
                        $username = isset($row[6]) ? trim($row[6]) : '';
                        $password = isset($row[7]) ? trim($row[7]) : '';

                        log_message('debug', 'Extracted data - Nama: ' . $nama_siswa . ', Username: ' . $username . ', Kelas: ' . $kelas);

                        // Validasi data wajib
                        if(empty($nama_siswa) || empty($kelas) || empty($username) || empty($password)) {
                            $error_msg = 'Baris ' . $row_index . ': Data tidak lengkap (Nama Siswa, Kelas, Username, dan Password wajib diisi)';
                            log_message('error', $error_msg);
                            $errors[] = $error_msg;
                            $error_count++;
                            continue;
                        }
                        
                        // Cek duplikasi username
                        $this->db->where('username', $username);
                        if($this->db->get('tb_user')->num_rows() > 0) {
                            $error_msg = 'Baris ' . $row_index . ': Username "' . $username . '" sudah ada dalam database';
                            log_message('error', $error_msg);
                            $errors[] = $error_msg;
                            $error_count++;
                            continue;
                        }
                        
                        log_message('debug', 'Data validation passed for row ' . $row_index);
                        
                        // Hash password
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        log_message('debug', 'Password hashed');

                        // Simpan user - Level 3 Siswa
                        $user_data = array(
                            'username'     => $username,
                            'password'     => $hashed_password,
                            'nama_lengkap' => $nama_siswa,
                            'jenis_kelamin'=> !empty($jenis_kelamin) ? (strtoupper($jenis_kelamin) == 'L' ? 'Laki-laki' : 'Perempuan') : NULL,
                            'nip_nim'      => !empty($nis) ? $nis : NULL,
                            'level'        => 3,
                            'is_active'    => 1,
                            'user_code'    => $this->M_user->generateRandomString(30)
                        );

                        $this->db->insert('tb_user', $user_data);
                        log_message('debug', 'User insert query executed');
                        
                        if($this->db->affected_rows() > 0) {
                            $success_count++;
                            log_message('debug', 'User inserted successfully');
                            
                            // Auto-create data siswa
                            $user_id = $this->db->insert_id();
                            
                            $dudi_id = NULL;
                            
                            // Cek apakah DUDI sudah ada
                            if(!empty($perusahaan_dudi)) {
                                $this->db->where('dudi_nama', $perusahaan_dudi);
                                $existing_dudi = $this->db->get('tb_dudi')->row();
                                
                                if($existing_dudi) {
                                    $dudi_id = $existing_dudi->dudi_id;
                                } else {
                                    // Buat DUDI baru jika belum ada
                                    $dudi_code = $this->M_siswa->generateRandomString(30);
                                    $dudi_data = array(
                                        'dudi_nama' => $perusahaan_dudi,
                                        'dudi_code' => $dudi_code,
                                        'status_kerjasama' => 'non_mitra',
                                        'is_mitra' => 0,
                                        'sumber_data' => 'sekolah',
                                        'created_at' => date('Y-m-d H:i:s')
                                    );
                                    
                                    $this->db->insert('tb_dudi', $dudi_data);
                                    if($this->db->affected_rows() > 0) {
                                        $dudi_id = $this->db->insert_id();
                                    }
                                }
                            }
                            
                            $siswa_data = array(
                                'user_id'         => $user_id,
                                'siswa_nama'      => $nama_siswa,
                                'siswa_nis'       => !empty($nis) ? $nis : NULL,
                                'siswa_nisn'      => !empty($nisn) ? $nisn : NULL,
                                'siswa_jk'        => !empty($jenis_kelamin) ? (strtoupper($jenis_kelamin) == 'L' ? 'Laki-laki' : 'Perempuan') : NULL,
                                'siswa_kelas'     => $kelas,
                                'siswa_jurusan'   => !empty($jurusan) ? $jurusan : NULL,
                                'status_pengajuan'=> 'draft',
                                'is_active'       => 1,
                                'created_at'      => date('Y-m-d H:i:s'),
                                'updated_at'      => date('Y-m-d H:i:s'),
                                'siswa_code'      => $this->M_siswa->generateRandomString(30)
                            );
                            
                            $this->db->insert('tb_siswa', $siswa_data);
                            log_message('debug', 'Siswa data inserted');
                        } else {
                            $error_msg = 'Baris ' . $row_index . ': Gagal menyimpan data user';
                            log_message('error', $error_msg);
                            $errors[] = $error_msg;
                            $error_count++;
                        }
                    }
                    fclose($handle);
                }
            } else {
                // Handle Excel file
                log_message('debug', 'Processing Excel file');
                if (!class_exists('\\PhpOffice\\PhpSpreadsheet\\IOFactory')) {
                    $autoload_path = FCPATH . 'vendor/autoload.php';
                    if (!file_exists($autoload_path)) {
                        throw new Exception('Fitur Import Excel memerlukan library PhpSpreadsheet. Silakan install dependencies terlebih dahulu.');
                    }
                    require_once $autoload_path;
                }
                
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file_path);
                $worksheet = $spreadsheet->getActiveSheet();
                $rows = $worksheet->toArray();

                // Check if file has data
                if (empty($rows)) {
                    throw new Exception('File Excel tidak memiliki data apapun.');
                }

                // Check if there are headers
                if (count($rows[0]) < 8) {
                    throw new Exception('File Excel harus memiliki minimal 8 kolom sesuai template. Ditemukan hanya ' . count($rows[0]) . ' kolom.');
                }

                // Skip header row
                array_shift($rows);
                log_message('debug', 'Excel file processed, ' . count($rows) . ' data rows found');

                foreach($rows as $index => $row){
                    // Skip empty rows
                    if(empty($row[0])) {
                        log_message('debug', 'Skipping empty row ' . ($index + 2));
                        continue;
                    }

                    // Extract data dari 8 kolom template (XLSX)
                    $nama_siswa = trim($row[0]);
                    $nis = isset($row[1]) ? trim($row[1]) : '';
                    $nisn = isset($row[2]) ? trim($row[2]) : '';
                    $jenis_kelamin = isset($row[3]) ? trim($row[3]) : '';
                    $kelas = isset($row[4]) ? trim($row[4]) : '';
                    $jurusan = isset($row[5]) ? trim($row[5]) : '';
                    $username = isset($row[6]) ? trim($row[6]) : '';
                    $password = isset($row[7]) ? trim($row[7]) : '';

                    log_message('debug', 'Processing Excel row ' . ($index + 2) . ' - Nama: ' . $nama_siswa . ', Username: ' . $username);

                    // Validasi data wajib (same as CSV)
                    if(empty($nama_siswa) || empty($kelas) || empty($username) || empty($password)) {
                        $error_msg = 'Baris ' . ($index + 2) . ': Data tidak lengkap (Nama Siswa, Kelas, Username, dan Password wajib diisi)';
                        log_message('error', $error_msg);
                        $errors[] = $error_msg;
                        $error_count++;
                        continue;
                    }
                    
                    // Cek duplikasi username
                    $this->db->where('username', $username);
                    if($this->db->get('tb_user')->num_rows() > 0) {
                        $error_msg = 'Baris ' . ($index + 2) . ': Username "' . $username . '" sudah ada dalam database';
                        log_message('error', $error_msg);
                        $errors[] = $error_msg;
                        $error_count++;
                        continue;
                    }
                    
                    log_message('debug', 'Data validation passed for Excel row ' . ($index + 2));
                    
                    // Hash password
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    // Simpan user (Excel) - Level 3 Siswa
                    $user_data = array(
                        'username'     => $username,
                        'password'     => $hashed_password,
                        'nama_lengkap' => $nama_siswa,
                        'jenis_kelamin'=> !empty($jenis_kelamin) ? (strtoupper($jenis_kelamin) == 'L' ? 'Laki-laki' : 'Perempuan') : NULL,
                        'nip_nim'      => !empty($nis) ? $nis : NULL,
                        'level'        => 3,
                        'is_active'    => 1,
                        'user_code'    => $this->M_user->generateRandomString(30)
                    );

                    $this->db->insert('tb_user', $user_data);
                    
                    if($this->db->affected_rows() > 0) {
                        $success_count++;
                        log_message('debug', 'User inserted successfully from Excel');
                        
                        // Auto-create data siswa (same as CSV)
                        $user_id = $this->db->insert_id();
                        
                        $dudi_id = NULL;
                        
                        // Cek apakah DUDI sudah ada
                        if(!empty($perusahaan_dudi)) {
                            $this->db->where('dudi_nama', $perusahaan_dudi);
                            $existing_dudi = $this->db->get('tb_dudi')->row();
                            
                            if($existing_dudi) {
                                $dudi_id = $existing_dudi->dudi_id;
                            } else {
                                // Buat DUDI baru jika belum ada
                                $dudi_code = $this->M_siswa->generateRandomString(30);
                                $dudi_data = array(
                                    'dudi_nama' => $perusahaan_dudi,
                                    'dudi_code' => $dudi_code,
                                    'status_kerjasama' => 'non_mitra',
                                    'is_mitra' => 0,
                                    'sumber_data' => 'sekolah',
                                    'created_at' => date('Y-m-d H:i:s')
                                );
                                
                                $this->db->insert('tb_dudi', $dudi_data);
                                if($this->db->affected_rows() > 0) {
                                    $dudi_id = $this->db->insert_id();
                                }
                            }
                        }
                        
                        $siswa_data = array(
                            'user_id'         => $user_id,
                            'siswa_nama'      => $nama_siswa,
                            'siswa_nis'       => !empty($nis) ? $nis : NULL,
                            'siswa_nisn'      => !empty($nisn) ? $nisn : NULL,
                            'siswa_jk'        => !empty($jenis_kelamin) ? (strtoupper($jenis_kelamin) == 'L' ? 'Laki-laki' : 'Perempuan') : NULL,
                            'siswa_kelas'     => $kelas,
                            'siswa_jurusan'   => !empty($jurusan) ? $jurusan : NULL,
                            'status_pengajuan'=> 'draft',
                            'is_active'       => 1,
                            'created_at'      => date('Y-m-d H:i:s'),
                            'updated_at'      => date('Y-m-d H:i:s'),
                            'siswa_code'      => $this->M_siswa->generateRandomString(30)
                        );
                        
                        $this->db->insert('tb_siswa', $siswa_data);
                        log_message('debug', 'Siswa data inserted from Excel');
                    } else {
                        $error_msg = 'Baris ' . ($index + 2) . ': Gagal menyimpan data user';
                        log_message('error', $error_msg);
                        $errors[] = $error_msg;
                        $error_count++;
                    }
                }
            }

            // Hapus file setelah diproses
            unlink($file_path);
            log_message('debug', 'Temporary file deleted');

            // Set flash message
            if($success_count > 0) {
                $this->session->set_flashdata('message', $success_count . ' data siswa berhasil diimport.');
                log_message('debug', $success_count . ' records imported successfully');
            } else {
                // Even if no successful imports, show a message to indicate processing happened
                if($error_count > 0) {
                    $this->session->set_flashdata('error_message', 'Tidak ada data yang berhasil diimport. Terdapat ' . $error_count . ' error.');
                    log_message('error', 'No records imported, ' . $error_count . ' errors occurred');
                } else {
                    $this->session->set_flashdata('message', 'File berhasil diproses tetapi tidak ada data baru yang ditambahkan.');
                    log_message('debug', 'File processed but no new data added');
                }
            }
            
            if(!empty($errors)) {
                $error_message = 'Terdapat ' . $error_count . ' error:\n' . implode("\n", array_slice($errors, 0, 10));
                $this->session->set_flashdata('error_message', $error_message);
                log_message('error', 'Import completed with errors: ' . json_encode($errors));
            }

        } catch (Exception $e) {
            log_message('error', 'Import exception: ' . $e->getMessage());
            $this->session->set_flashdata('error_message', 'Error: ' . $e->getMessage());
            // Delete uploaded file on error
            if(file_exists($file_path)){
                unlink($file_path);
            }
        }

        log_message('debug', 'Import process completed');
        redirect('hubin/view/daftar-siswa');
    }

    // Import data pembimbing dengan template Excel/CSV
    function import_daftar_pembimbing()
    {
        log_message('debug', 'Import pembimbing process started');
        
        $userdata = $this->session->userdata('userdata');
        if(!$userdata || $userdata['level'] != 1){
            log_message('error', 'User not authorized or not logged in for import pembimbing');
            $this->session->set_flashdata('error_message', 'Anda tidak memiliki izin untuk melakukan import data pembimbing.');
            redirect('hubin/view/data-pembimbing');
            return;
        }

        $config['upload_path'] = './uploads/import/';
        $config['allowed_types'] = 'xlsx|xls|csv';
        $config['max_size'] = 5120; // 5MB
        $config['file_name'] = 'import_daftar_pembimbing_' . time();

        if(!is_dir($config['upload_path'])){
            mkdir($config['upload_path'], 0777, true);
        }

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('file_excel')) {
            $error_message = $this->upload->display_errors('', '');
            log_message('error', 'Upload failed: ' . $error_message);
            $this->session->set_flashdata('error_message', $error_message);
            redirect('hubin/view/data-pembimbing');
            return;
        }

        $upload_data = $this->upload->data();
        $file_path = $config['upload_path'] . $upload_data['file_name'];

        try {
            $this->load->model('M_user');
            $this->load->model('M_pembimbing');
            
            $success_count = 0;
            $error_count = 0;
            $errors = array();

            $file_extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));

            if ($file_extension === 'csv') {
                if (($handle = fopen($file_path, "r")) !== FALSE) {
                    $row_index = 0;
                    $headers = array();
                    while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $row_index++;
                        if ($row_index == 1) {
                            if (count($row) < 4) {
                                fclose($handle);
                                throw new Exception('File CSV harus memiliki kolom sesuai template pembimbing.');
                            }
                            $headers = $row;
                            continue;
                        }

                        $row = $this->_map_pembimbing_row_by_header($headers, $row);
                        if (empty($row[0])) {
                            continue;
                        }

                        $res = $this->_process_pembimbing_row($row, $row_index);
                        if ($res['status']) {
                            $success_count++;
                        } else {
                            $error_count++;
                            $errors[] = $res['message'];
                        }
                    }
                    fclose($handle);
                }
            } else {
                if (!class_exists('\\PhpOffice\\PhpSpreadsheet\\IOFactory')) {
                    $autoload_path = FCPATH . 'vendor/autoload.php';
                    if (!file_exists($autoload_path)) {
                        throw new Exception('Fitur Import Excel memerlukan library PhpSpreadsheet. Silakan install dependencies terlebih dahulu.');
                    }
                    require_once $autoload_path;
                }

                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file_path);
                $worksheet = $spreadsheet->getActiveSheet();
                $rows = $worksheet->toArray();

                if (empty($rows)) {
                    throw new Exception('File Excel tidak memiliki data apapun.');
                }

                // Skip header row
                $headers = array_shift($rows);

                foreach ($rows as $index => $row) {
                    $row_number = $index + 2;
                    $row = $this->_map_pembimbing_row_by_header($headers, $row);
                    if (empty($row[0])) {
                        continue;
                    }

                    $res = $this->_process_pembimbing_row($row, $row_number);
                    if ($res['status']) {
                        $success_count++;
                    } else {
                        $error_count++;
                        $errors[] = $res['message'];
                    }
                }
            }

            if (file_exists($file_path)) {
                unlink($file_path);
            }

            if ($success_count > 0) {
                $msg = $success_count . ' data guru pembimbing berhasil diimport beserta akun login.';
                if ($error_count > 0) {
                    $msg .= ' (' . $error_count . ' data dilewati karena error)';
                }
                $this->session->set_flashdata('message', $msg);
            } else {
                $this->session->set_flashdata('error_message', 'Tidak ada data yang berhasil diimport. Ditemukan ' . $error_count . ' kesalahan.');
            }

            if (!empty($errors)) {
                $this->session->set_flashdata('import_errors', array_slice($errors, 0, 10));
            }

        } catch (Exception $e) {
            log_message('error', 'Import pembimbing exception: ' . $e->getMessage());
            $this->session->set_flashdata('error_message', 'Error: ' . $e->getMessage());
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }

        redirect('hubin/view/data-pembimbing');
    }

    /**
     * Helper to process single row of pembimbing data
     */
    private function _map_pembimbing_row_by_header($headers, $row)
    {
        $aliases = array(
            'nama' => 0,
            'nip' => 1,
            'jenis kelamin' => 2,
            'email' => 3,
            'telepon' => 4,
            'no telepon' => 4,
            'alamat' => 5,
            'tempat lahir' => 6,
            'tanggal lahir' => 7,
            'pendidikan' => 8,
            'jabatan' => 9,
            'jurusan' => 10,
            'tahun masuk' => 11,
            'status kepegawaian' => 12,
            'tempat tugas' => 13,
            'username' => 14,
            'password' => 15
        );
        $mapped = array_fill(0, 16, '');
        foreach ($headers as $index => $header) {
            $normalized = strtolower(trim(preg_replace('/\s+/', ' ', preg_replace('/\([^)]*\)/', '', (string) $header))));
            foreach ($aliases as $alias => $target) {
                if ($normalized === $alias || strpos($normalized, $alias) !== false) {
                    $mapped[$target] = isset($row[$index]) ? $row[$index] : '';
                    break;
                }
            }
        }
        return $mapped;
    }

    private function _process_pembimbing_row($row, $row_number)
    {
        $nama               = isset($row[0]) ? trim($row[0]) : '';
        $nip                = isset($row[1]) ? trim($row[1]) : '';
        $jk_raw             = isset($row[2]) ? trim($row[2]) : '';
        $email              = isset($row[3]) ? trim($row[3]) : '';
        $telepon            = isset($row[4]) ? trim($row[4]) : '';
        $alamat             = isset($row[5]) ? trim($row[5]) : '';
        $tempat_lahir       = isset($row[6]) ? trim($row[6]) : '';
        $tgl_lahir_raw      = isset($row[7]) ? trim($row[7]) : '';
        $pendidikan_terakhir= isset($row[8]) ? trim($row[8]) : '';
        $jabatan            = isset($row[9]) ? trim($row[9]) : '';
        $jurusan_keahlian   = isset($row[10]) ? trim($row[10]) : '';
        $tahun_masuk        = isset($row[11]) ? trim($row[11]) : '';
        $status_kepegawaian = isset($row[12]) ? trim($row[12]) : '';
        $tempat_tugas       = isset($row[13]) ? trim($row[13]) : '';
        $username           = isset($row[14]) ? trim($row[14]) : '';
        $password           = isset($row[15]) ? trim($row[15]) : '';

        if (!empty($nip)) {
            $nip = $this->M_user->normalize_identifier_value($nip);
        }

        $this->db->where('username', $username);
        $existing_user = $this->db->get('tb_user')->row();

        if ($existing_user && $existing_user->username !== $username) {
            return [
                'status' => false,
                'message' => 'Baris ' . $row_number . ': Username "' . $username . '" sudah digunakan akun lain'
            ];
        }

        // Validasi wajib
        if (empty($nama) || empty($nip) || empty($username) || (empty($password) && !$existing_user)) {
            return [
                'status' => false,
                'message' => 'Baris ' . $row_number . ': Data tidak lengkap (Nama Pembimbing, NIP, Username, dan Password wajib diisi)'
            ];
        }

        if (!empty($password) && strlen($password) < 6) {
            return [
                'status' => false,
                'message' => 'Baris ' . $row_number . ': Password untuk ' . $nama . ' minimal 6 karakter'
            ];
        }

        if (!empty($email)) {
            $this->db->where('email', $email);
            if ($existing_user) {
                $this->db->where('id !=', $existing_user->id);
            }
            if ($this->db->get('tb_user')->num_rows() > 0) {
                return [
                    'status' => false,
                    'message' => 'Baris ' . $row_number . ': Email "' . $email . '" sudah digunakan akun lain'
                ];
            }
        }

        if (!empty($nip)) {
            $this->db->where('nip_nim', $nip);
            if ($existing_user) {
                $this->db->where('id !=', $existing_user->id);
            }
            if ($this->db->get('tb_user')->num_rows() > 0) {
                return [
                    'status' => false,
                    'message' => 'Baris ' . $row_number . ': NIP/NIM "' . $nip . '" sudah digunakan akun lain'
                ];
            }
        }

        // Cek duplikasi NIP di tb_pembimbing
        $this->db->where('pembimbing_nip', $nip);
        if ($existing_user) {
            $this->db->where('user_id !=', $existing_user->id);
        }
        if ($this->db->get('tb_pembimbing')->num_rows() > 0) {
            return [
                'status' => false,
                'message' => 'Baris ' . $row_number . ': NIP "' . $nip . '" sudah terdaftar pada pembimbing lain'
            ];
        }

        // Format Jenis Kelamin
        $jenis_kelamin = null;
        if (!empty($jk_raw)) {
            $jk_upper = strtoupper($jk_raw);
            if ($jk_upper === 'L' || strpos($jk_upper, 'LAKI') !== false) {
                $jenis_kelamin = 'Laki-laki';
            } elseif ($jk_upper === 'P' || strpos($jk_upper, 'PEREMPUAN') !== false) {
                $jenis_kelamin = 'Perempuan';
            }
        }

        // Format Tanggal Lahir
        $tanggal_lahir = null;
        if (!empty($tgl_lahir_raw)) {
            if (is_numeric($tgl_lahir_raw) && (float) $tgl_lahir_raw > 25569) {
                $tanggal_lahir = date('Y-m-d', ((int) $tgl_lahir_raw - 25569) * 86400);
            } else {
                $tgl_time = strtotime($tgl_lahir_raw);
                if ($tgl_time !== false) {
                    $tanggal_lahir = date('Y-m-d', $tgl_time);
                }
            }
        }

        if ($existing_user && empty($password)) {
            $this->db->where('user_id', $existing_user->id);
            $existing_pembimbing = $this->db->get('tb_pembimbing')->row();
            if (!$existing_pembimbing) {
                return ['status' => false, 'message' => 'Baris ' . $row_number . ': Username sudah ada tetapi profil pembimbing tidak ditemukan.'];
            }
            $this->db->where('id', $existing_user->id)->update('tb_user', [
                'nama_lengkap' => $nama,
                'email' => $email ?: null,
                'telepon' => $telepon ?: null,
                'alamat' => $alamat ?: null,
                'tempat_lahir' => $tempat_lahir ?: null,
                'tanggal_lahir' => $tanggal_lahir,
                'jenis_kelamin' => $jenis_kelamin,
                'nip_nim' => $nip,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            $this->db->where('pembimbing_id', $existing_pembimbing->pembimbing_id)->update('tb_pembimbing', [
                'pembimbing_nama' => $nama,
                'pembimbing_nip' => $nip,
                'pembimbing_telepon' => $telepon ?: null,
                'pembimbing_email' => $email ?: null,
                'pembimbing_alamat' => $alamat ?: null,
                'tempat_lahir' => $tempat_lahir ?: null,
                'tanggal_lahir' => $tanggal_lahir,
                'jenis_kelamin' => $jenis_kelamin,
                'pendidikan_terakhir' => $pendidikan_terakhir ?: null,
                'jabatan' => $jabatan ?: null,
                'jurusan_keahlian' => $jurusan_keahlian ?: null,
                'tahun_masuk' => $tahun_masuk ?: null,
                'status_kepegawaian' => $status_kepegawaian ?: null,
                'tempat_tugas' => $tempat_tugas ?: null,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            return ['status' => true, 'message' => 'Baris ' . $row_number . ': ' . $nama . ' berhasil diperbarui'];
        }

        if ($existing_user && !empty($password)) {
            $this->db->where('id', $existing_user->id)->update('tb_user', [
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'nama_lengkap' => $nama,
                'email' => $email ?: null,
                'telepon' => $telepon ?: null,
                'alamat' => $alamat ?: null,
                'tempat_lahir' => $tempat_lahir ?: null,
                'tanggal_lahir' => $tanggal_lahir,
                'jenis_kelamin' => $jenis_kelamin,
                'nip_nim' => $nip,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $this->db->where('user_id', $existing_user->id);
            $existing_pembimbing = $this->db->get('tb_pembimbing')->row();

            if ($existing_pembimbing) {
                $this->db->where('pembimbing_id', $existing_pembimbing->pembimbing_id)->update('tb_pembimbing', [
                    'pembimbing_nama' => $nama,
                    'pembimbing_nip' => $nip,
                    'pembimbing_telepon' => $telepon ?: null,
                    'pembimbing_email' => $email ?: null,
                    'pembimbing_alamat' => $alamat ?: null,
                    'tempat_lahir' => $tempat_lahir ?: null,
                    'tanggal_lahir' => $tanggal_lahir,
                    'jenis_kelamin' => $jenis_kelamin,
                    'pendidikan_terakhir' => $pendidikan_terakhir ?: null,
                    'jabatan' => $jabatan ?: null,
                    'jurusan_keahlian' => $jurusan_keahlian ?: null,
                    'tahun_masuk' => $tahun_masuk ?: null,
                    'status_kepegawaian' => $status_kepegawaian ?: null,
                    'tempat_tugas' => $tempat_tugas ?: null,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }

            return ['status' => true, 'message' => 'Baris ' . $row_number . ': ' . $nama . ' berhasil diperbarui dan password diubah'];
        }

        // 1. Buat User Account (Level 2 = Pembimbing)
        $user_code = $this->M_user->generateRandomString(30);
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $user_data = [
            'user_code'     => $user_code,
            'username'      => $username,
            'password'      => $hashed_password,
            'nama_lengkap'  => $nama,
            'email'         => !empty($email) ? $email : null,
            'telepon'       => !empty($telepon) ? $telepon : null,
            'alamat'        => !empty($alamat) ? $alamat : null,
            'tempat_lahir'  => !empty($tempat_lahir) ? $tempat_lahir : null,
            'tanggal_lahir' => $tanggal_lahir,
            'jenis_kelamin' => $jenis_kelamin,
            'nip_nim'       => $nip,
            'level'         => 2, // Pembimbing
            'is_active'     => 1,
            'created_at'    => date('Y-m-d H:i:s')
        ];

        if (!$this->db->insert('tb_user', $user_data)) {
            $error = $this->db->error();
            return [
                'status' => false,
                'message' => 'Baris ' . $row_number . ': Gagal membuat akun ' . $nama . ' (' . $error['message'] . ')'
            ];
        }
        $user_id = $this->db->insert_id();

        if (!$user_id) {
            return [
                'status' => false,
                'message' => 'Baris ' . $row_number . ': Gagal membuat akun pengguna untuk ' . $nama
            ];
        }

        // 2. Buat Record di tb_pembimbing
        $pembimbing_code = $this->M_pembimbing->generateRandomString(30);
        $pembimbing_data = [
            'user_id'             => $user_id,
            'pembimbing_nama'     => $nama,
            'pembimbing_nip'      => $nip,
            'pembimbing_telepon'  => !empty($telepon) ? $telepon : null,
            'pembimbing_email'    => !empty($email) ? $email : null,
            'pembimbing_alamat'   => !empty($alamat) ? $alamat : null,
            'tempat_lahir'        => !empty($tempat_lahir) ? $tempat_lahir : null,
            'tanggal_lahir'       => $tanggal_lahir,
            'jenis_kelamin'       => $jenis_kelamin,
            'pendidikan_terakhir' => !empty($pendidikan_terakhir) ? $pendidikan_terakhir : null,
            'jabatan'             => !empty($jabatan) ? $jabatan : null,
            'jurusan_keahlian'    => !empty($jurusan_keahlian) ? $jurusan_keahlian : null,
            'tahun_masuk'         => !empty($tahun_masuk) ? $tahun_masuk : null,
            'status_kepegawaian'  => !empty($status_kepegawaian) ? $status_kepegawaian : null,
            'tempat_tugas'        => !empty($tempat_tugas) ? $tempat_tugas : null,
            'pembimbing_code'     => $pembimbing_code,
            'is_active'           => 1,
            'created_at'          => date('Y-m-d H:i:s'),
            'updated_at'          => date('Y-m-d H:i:s')
        ];

        if (!$this->db->insert('tb_pembimbing', $pembimbing_data)) {
            $error = $this->db->error();
            $this->db->where('id', $user_id)->delete('tb_user');
            return [
                'status' => false,
                'message' => 'Baris ' . $row_number . ': Gagal menyimpan profil ' . $nama . ' (' . $error['message'] . ')'
            ];
        }

        return [
            'status' => true,
            'message' => 'Baris ' . $row_number . ': ' . $nama . ' berhasil diimport'
        ];
    }

    // Export data pengajuan PKL dalam format XLSX
    function export_data_pengajuan_pkl()
    {
        if (!class_exists('\\PhpOffice\\PhpSpreadsheet\\Spreadsheet')) {
            $autoload_path = FCPATH . 'vendor/autoload.php';
            if (file_exists($autoload_path)) {
                require_once $autoload_path;
            } else {
                die('Fitur Export Excel memerlukan library PhpSpreadsheet. Silakan install dependencies terlebih dahulu.');
            }
        }
        
        $selected_ids = $this->input->get('selected');
        
        $this->db->select('
            tb_siswa.*, 
            tb_dudi.dudi_nama, 
            tb_dudi.dudi_alamat,
            tb_dudi.dudi_telepon,
            tb_dudi.dudi_email,
            tb_dudi.dudi_pic,
            tb_dudi.is_mitra as dudi_is_mitra,
            tb_user.nama_lengkap as user_nama,
            tb_user.telepon as user_telepon,
            tb_user.email as user_email,
            tb_user.alamat as user_alamat,
            tb_user.tempat_lahir as user_tempat_lahir,
            tb_user.tanggal_lahir as user_tanggal_lahir,
            tb_user.jenis_kelamin as user_jenis_kelamin
        ');
        $this->db->from('tb_siswa');
        $this->db->join('tb_dudi', 'tb_dudi.dudi_id = tb_siswa.dudi_id', 'left');
        $this->db->join('tb_user', 'tb_user.id = tb_siswa.user_id', 'left');
        $this->db->where('tb_siswa.status_pengajuan !=', 'draft');
        
        if ($selected_ids) {
            $siswa_ids_array = explode(',', $selected_ids);
            $this->db->group_start();
            $this->db->where_in('tb_siswa.siswa_id', $siswa_ids_array);
            $this->db->or_where_in('tb_siswa.siswa_code', $siswa_ids_array);
            $this->db->group_end();
        }
        
        $this->db->order_by('tb_siswa.created_at', 'DESC');
        $pengajuan = $this->db->get();
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Pengajuan PKL');
        
        $headers = [
            'No',
            'Nama Siswa',
            'NIS',
            'NISN',
            'Jenis Kelamin',
            'Kelas',
            'Jurusan',
            'Telepon Siswa',
            'Email Siswa',
            'Alamat Siswa',
            'Perusahaan (DUDI)',
            'Kategori Perusahaan',
            'Alamat Perusahaan',
            'Telepon Perusahaan',
            'Email Perusahaan',
            'PIC Perusahaan',
            'Tanggal Mulai PKL',
            'Tanggal Selesai PKL',
            'Lama Pelaksanaan (Hari)',
            'Periode PKL',
            'Status Pengajuan',
            'Tanggal Pengajuan',
            'Surat Permohonan',
            'Surat Balasan'
        ];
        
        $header_style = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '6A1B9A']],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
        ];

        $col = 'A';
        foreach($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->applyFromArray($header_style);
            $col++;
        }
        $sheet->getRowDimension(1)->setRowHeight(28);
        
        $row_num = 2;
        $no = 1;
        foreach($pengajuan->result() as $data){
            $nama_siswa = $data->siswa_nama ? $data->siswa_nama : ($data->user_nama ? $data->user_nama : '-');
            $nis = $data->siswa_nis ? $data->siswa_nis : '';
            $nisn = $data->siswa_nisn ? $data->siswa_nisn : '';
            
            $raw_jk = !empty($data->siswa_jk) ? $data->siswa_jk : (!empty($data->user_jenis_kelamin) ? $data->user_jenis_kelamin : '');
            $jenis_kelamin = ($raw_jk == 'L' || $raw_jk == 'Laki-laki') ? 'Laki-laki' : (($raw_jk == 'P' || $raw_jk == 'Perempuan') ? 'Perempuan' : $raw_jk);

            $telepon = !empty($data->siswa_telepon) ? $data->siswa_telepon : (!empty($data->user_telepon) ? $data->user_telepon : '');
            $email = !empty($data->user_email) ? $data->user_email : '';
            $alamat = !empty($data->siswa_alamat) ? $data->siswa_alamat : (!empty($data->user_alamat) ? $data->user_alamat : '');

            $dudi_nama = !empty($data->dudi_nama) ? $data->dudi_nama : (!empty($data->other_dudi_nama) ? $data->other_dudi_nama : '-');
            $kategori_dudi = (isset($data->dudi_is_mitra) && $data->dudi_is_mitra == 1) ? 'Mitra' : 'Non-Mitra';
            $dudi_alamat = !empty($data->dudi_alamat) ? $data->dudi_alamat : (!empty($data->other_dudi_alamat) ? $data->other_dudi_alamat : '');
            $dudi_telepon = !empty($data->dudi_telepon) ? $data->dudi_telepon : (!empty($data->other_dudi_telepon) ? $data->other_dudi_telepon : '');
            $dudi_email = !empty($data->dudi_email) ? $data->dudi_email : (!empty($data->other_dudi_email) ? $data->other_dudi_email : '');
            $dudi_pic = !empty($data->dudi_pic) ? $data->dudi_pic : (!empty($data->other_dudi_pic) ? $data->other_dudi_pic : '');

            $tgl_mulai = (!empty($data->tanggal_mulai) && $data->tanggal_mulai != '0000-00-00') ? date('d-m-Y', strtotime($data->tanggal_mulai)) : '';
            $tgl_selesai = (!empty($data->tanggal_selesai) && $data->tanggal_selesai != '0000-00-00') ? date('d-m-Y', strtotime($data->tanggal_selesai)) : '';

            if(!empty($data->lama_pelaksanaan)) {
                $lama_hari = $data->lama_pelaksanaan;
            } elseif(!empty($data->tanggal_mulai) && !empty($data->tanggal_selesai)) {
                $s = new DateTime($data->tanggal_mulai);
                $e = new DateTime($data->tanggal_selesai);
                $lama_hari = $s->diff($e)->days + 1;
            } else {
                $lama_hari = '';
            }

            $tgl_pengajuan = !empty($data->created_at) ? date('d-m-Y H:i', strtotime($data->created_at)) : '';
            $surat_permohonan = !empty($data->surat_permohonan) ? 'Ada (' . $data->surat_permohonan . ')' : 'Tidak Ada';
            $surat_balasan = !empty($data->surat_balasan) ? 'Ada (' . $data->surat_balasan . ')' : 'Tidak Ada';

            $c = 'A';
            $sheet->setCellValue($c++ . $row_num, $no++);
            $sheet->setCellValue($c++ . $row_num, $nama_siswa);
            $sheet->setCellValueExplicit($c++ . $row_num, (string)$nis, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit($c++ . $row_num, (string)$nisn, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue($c++ . $row_num, $jenis_kelamin);
            $sheet->setCellValue($c++ . $row_num, $data->siswa_kelas ?: '');
            $sheet->setCellValue($c++ . $row_num, $data->siswa_jurusan ?: '');
            $sheet->setCellValueExplicit($c++ . $row_num, (string)$telepon, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue($c++ . $row_num, $email);
            $sheet->setCellValue($c++ . $row_num, $alamat);
            $sheet->setCellValue($c++ . $row_num, $dudi_nama);
            $sheet->setCellValue($c++ . $row_num, $kategori_dudi);
            $sheet->setCellValue($c++ . $row_num, $dudi_alamat);
            $sheet->setCellValueExplicit($c++ . $row_num, (string)$dudi_telepon, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue($c++ . $row_num, $dudi_email);
            $sheet->setCellValue($c++ . $row_num, $dudi_pic);
            $sheet->setCellValue($c++ . $row_num, $tgl_mulai);
            $sheet->setCellValue($c++ . $row_num, $tgl_selesai);
            $sheet->setCellValue($c++ . $row_num, $lama_hari);
            $sheet->setCellValue($c++ . $row_num, $data->periode ?: '');
            $sheet->setCellValue($c++ . $row_num, ucfirst($data->status_pengajuan ?: ''));
            $sheet->setCellValue($c++ . $row_num, $tgl_pengajuan);
            $sheet->setCellValue($c++ . $row_num, $surat_permohonan);
            $sheet->setCellValue($c++ . $row_num, $surat_balasan);
            
            $row_num++;
        }
        
        foreach(range('A', 'X') as $col_char) {
            $sheet->getColumnDimension($col_char)->setAutoSize(true);
        }
        
        $filename = 'data-pengajuan-pkl-lengkap-' . date("Ymd_His") . '.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    // Alias export data pengajuan PKL
    function export_pengajuan_pkl()
    {
        $this->export_data_pengajuan_pkl();
    }

    // Export data assign pembimbing dalam format XLSX
    function export_assign_pembimbing()
    {
        if (!class_exists('\\PhpOffice\\PhpSpreadsheet\\Spreadsheet')) {
            $autoload_path = FCPATH . 'vendor/autoload.php';
            if (file_exists($autoload_path)) {
                require_once $autoload_path;
            } else {
                die('Fitur Export Excel memerlukan library PhpSpreadsheet. Silakan install dependencies terlebih dahulu.');
            }
        }
        
        $selected_ids = $this->input->get('selected');
        
        $this->db->select('
            tb_siswa.siswa_id,
            tb_siswa.siswa_code,
            tb_siswa.siswa_nama,
            tb_siswa.siswa_kelas,
            tb_siswa.siswa_jurusan,
            tb_siswa.siswa_nis,
            tb_siswa.siswa_nisn,
            tb_siswa.siswa_jk,
            tb_siswa.siswa_telepon,
            tb_siswa.status_pengajuan,
            tb_siswa.periode,
            tb_siswa.tanggal_mulai,
            tb_siswa.tanggal_selesai,
            tb_siswa.other_dudi_nama,
            tb_siswa.other_dudi_alamat,
            tb_dudi.dudi_nama,
            tb_dudi.dudi_alamat,
            tb_pembimbing.pembimbing_nama,
            tb_pembimbing.pembimbing_nip,
            tb_pembimbing.tempat_tugas,
            tb_pembimbing.pembimbing_telepon,
            tb_pengelompokan.id as kelompok_id,
            tb_pengelompokan.created_at as assigned_date
        ');
        $this->db->from('tb_siswa');
        $this->db->join('tb_dudi', 'tb_dudi.dudi_id = tb_siswa.dudi_id', 'left');
        $this->db->join('tb_pengelompokan', 'tb_pengelompokan.siswa_id = tb_siswa.siswa_id', 'left');
        $this->db->join('tb_pembimbing', 'tb_pembimbing.pembimbing_id = tb_pengelompokan.pembimbing_id', 'left');
        $this->db->where('tb_siswa.status_pengajuan', 'disetujui');
        
        if ($selected_ids) {
            $selected_array = explode(',', $selected_ids);
            $this->db->group_start();
            $this->db->where_in('tb_pengelompokan.id', $selected_array);
            $this->db->or_where_in('tb_siswa.siswa_code', $selected_array);
            $this->db->or_where_in('tb_siswa.siswa_id', $selected_array);
            $this->db->group_end();
        }
        
        $this->db->order_by('CASE WHEN tb_pengelompokan.pembimbing_id IS NULL THEN 0 ELSE 1 END', '', FALSE);
        $this->db->order_by('tb_pembimbing.pembimbing_nama', 'ASC');
        $this->db->order_by('tb_siswa.siswa_nama', 'ASC');
        $assignments = $this->db->get();
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Penempatan Pembimbing');
        
        $headers = [
            'No',
            'Nama Siswa',
            'NIS',
            'NISN',
            'Jenis Kelamin',
            'Kelas',
            'Jurusan',
            'Telepon Siswa',
            'Perusahaan (DUDI)',
            'Alamat Perusahaan',
            'Periode PKL',
            'Status Pengajuan',
            'Guru Pembimbing',
            'NIP Pembimbing',
            'Tempat Tugas Pembimbing',
            'Telepon Pembimbing',
            'Status Assignment',
            'Tanggal Penempatan'
        ];
        
        $header_style = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '0D47A1']],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
        ];

        $col = 'A';
        foreach($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->applyFromArray($header_style);
            $col++;
        }
        $sheet->getRowDimension(1)->setRowHeight(28);
        
        $row_num = 2;
        $no = 1;
        foreach($assignments->result() as $data){
            $raw_jk = !empty($data->siswa_jk) ? $data->siswa_jk : '';
            $jenis_kelamin = ($raw_jk == 'L' || $raw_jk == 'Laki-laki') ? 'Laki-laki' : (($raw_jk == 'P' || $raw_jk == 'Perempuan') ? 'Perempuan' : $raw_jk);

            $dudi_nama = !empty($data->dudi_nama) ? $data->dudi_nama : (!empty($data->other_dudi_nama) ? $data->other_dudi_nama : '-');
            $dudi_alamat = !empty($data->dudi_alamat) ? $data->dudi_alamat : (!empty($data->other_dudi_alamat) ? $data->other_dudi_alamat : '');

            $is_assigned = !empty($data->kelompok_id);
            $pembimbing_nama = $is_assigned ? ($data->pembimbing_nama ?: '-') : 'Belum Di-Assign';
            $pembimbing_nip = $is_assigned ? ($data->pembimbing_nip ?: '-') : '-';
            $tempat_tugas = $is_assigned ? ($data->tempat_tugas ?: '-') : '-';
            $pembimbing_telepon = $is_assigned ? ($data->pembimbing_telepon ?: '-') : '-';
            $status_assign = $is_assigned ? 'Sudah Di-Assign' : 'Belum Di-Assign';
            $tgl_assign = ($is_assigned && !empty($data->assigned_date)) ? date('d-m-Y H:i', strtotime($data->assigned_date)) : '-';

            $c = 'A';
            $sheet->setCellValue($c++ . $row_num, $no++);
            $sheet->setCellValue($c++ . $row_num, $data->siswa_nama ?: '');
            $sheet->setCellValueExplicit($c++ . $row_num, (string)($data->siswa_nis ?: ''), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit($c++ . $row_num, (string)($data->siswa_nisn ?: ''), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue($c++ . $row_num, $jenis_kelamin);
            $sheet->setCellValue($c++ . $row_num, $data->siswa_kelas ?: '');
            $sheet->setCellValue($c++ . $row_num, $data->siswa_jurusan ?: '');
            $sheet->setCellValueExplicit($c++ . $row_num, (string)($data->siswa_telepon ?: ''), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue($c++ . $row_num, $dudi_nama);
            $sheet->setCellValue($c++ . $row_num, $dudi_alamat);
            $sheet->setCellValue($c++ . $row_num, $data->periode ?: '');
            $sheet->setCellValue($c++ . $row_num, ucfirst($data->status_pengajuan ?: ''));
            $sheet->setCellValue($c++ . $row_num, $pembimbing_nama);
            $sheet->setCellValueExplicit($c++ . $row_num, (string)$pembimbing_nip, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue($c++ . $row_num, $tempat_tugas);
            $sheet->setCellValueExplicit($c++ . $row_num, (string)$pembimbing_telepon, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue($c++ . $row_num, $status_assign);
            $sheet->setCellValue($c++ . $row_num, $tgl_assign);
            
            $row_num++;
        }
        
        foreach(range('A', 'R') as $col_char) {
            $sheet->getColumnDimension($col_char)->setAutoSize(true);
        }
        
        $filename = 'data-assign-pembimbing-lengkap-' . date("Ymd_His") . '.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

}