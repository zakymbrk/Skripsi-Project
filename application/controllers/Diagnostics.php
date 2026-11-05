<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Diagnostics extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        require_role(1);
    }

    public function check_template()
    {
        // Check if library exists before using it
        if (!class_exists('\\PhpOffice\\PhpSpreadsheet\\IOFactory')) {
            $autoload_path = FCPATH . 'vendor/autoload.php';
            if (file_exists($autoload_path)) {
                require_once $autoload_path;
            } else {
                die('Fitur Diagnostic memerlukan library PhpSpreadsheet. Silakan install dependencies terlebih dahulu dengan composer install.');
            }
        }
        
        $template_path = FCPATH . 'assets/Template.xlsx';
        
        if (!file_exists($template_path)) {
            echo "<h2>Template.xlsx tidak ditemukan di folder assets/</h2>";
            echo "<p>Mohon pastikan file Template.xlsx berada di folder: " . FCPATH . "assets/Template.xlsx</p>";
            return;
        }
        
        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($template_path);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            
            echo "<h2>Analisis Template.xlsx</h2>";
            
            if (empty($rows)) {
                echo "<p>File tidak memiliki data apapun.</p>";
                return;
            }
            
            echo "<h3>Header (Baris 1):</h3>";
            $header_row = $rows[0];
            echo "<ul>";
            foreach ($header_row as $index => $header) {
                echo "<li>Kolom " . ($index + 1) . ": \"" . htmlspecialchars($header) . "\"</li>";
            }
            echo "</ul>";
            
            echo "<p><strong>Jumlah kolom ditemukan:</strong> " . count($header_row) . "</p>";
            echo "<p><strong>Jumlah baris data:</strong> " . (count($rows) - 1) . " (tidak termasuk header)</p>";
            
            if (count($header_row) != 18) {
                echo "<div style='background-color: #f8d7da; color: #721c24; padding: 10px; margin: 10px 0; border-radius: 5px;'>";
                echo "<h4>⚠️ MASALAH TERDETEKSI:</h4>";
                echo "<p>Template harus memiliki <strong>18 kolom</strong> tetapi hanya menemukan <strong>" . count($header_row) . "</strong>.</p>";
                echo "<p>Urutan kolom yang benar:</p>";
                echo "<ol>";
                $expected_headers = [
                    'Username *',
                    'Nama Lengkap *', 
                    'Password *',
                    'NIS',
                    'NISN',
                    'Jenis Kelamin (L/P)',
                    'Tempat Lahir',
                    'Tanggal Lahir (YYYY-MM-DD)',
                    'Kelas *',
                    'Jurusan',
                    'Telepon',
                    'Alamat',
                    'Email',
                    'Periode',
                    'Tanggal Mulai (YYYY-MM-DD)',
                    'Tanggal Selesai (YYYY-MM-DD)',
                    'Nama DUDI',
                    'Status Pengajuan'
                ];
                foreach ($expected_headers as $header) {
                    echo "<li>" . htmlspecialchars($header) . "</li>";
                }
                echo "</ol>";
                echo "</div>";
            } else {
                echo "<div style='background-color: #d4edda; color: #155724; padding: 10px; margin: 10px 0; border-radius: 5px;'>";
                echo "<h4>✅ JUMLAH KOLOM BENAR:</h4>";
                echo "<p>Template memiliki jumlah kolom yang benar (18 kolom).</p>";
                echo "</div>";
            }
            
            // Check first data row if exists
            if (isset($rows[1])) {
                echo "<h3>Contoh Data (Baris 2):</h3>";
                echo "<ul>";
                foreach ($rows[1] as $index => $value) {
                    echo "<li>Kolom " . ($index + 1) . ": \"" . htmlspecialchars($value) . "\"</li>";
                }
                echo "</ul>";
            }
            
            echo "<h3>Saran:</h3>";
            echo "<ol>";
            echo "<li>Gunakan template yang di-generate dari sistem untuk memastikan format benar</li>";
            echo "<li>Periksa bahwa kolom berada dalam urutan yang benar</li>";
            echo "<li>Hapus baris kosong atau header tambahan</li>";
            echo "<li>Gunakan fungsi 'Export to XLSX' di halaman Daftar Siswa untuk mendapatkan template kosong yang benar</li>";
            echo "</ol>";
            
        } catch (Exception $e) {
            echo "<h2>Error membaca file Template.xlsx:</h2>";
            echo "<p>" . $e->getMessage() . "</p>";
        }
    }
}