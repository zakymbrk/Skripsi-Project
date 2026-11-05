<?php 
// Get student data passed from controller
$siswa = $siswa ?? null;

// QR code URL is passed from controller
$qr_code_url = isset($qr_code_url) ? $qr_code_url : '';

// Debug: Tampilkan informasi untuk troubleshooting
// echo "<!-- DEBUG: SISWA DATA: " . print_r($siswa, true) . " -->";
// echo "<!-- DEBUG: QR CODE URL: " . $qr_code_url . " -->";
// echo "<!-- DEBUG: FOTO PROFIL: " . $foto_profil . " -->";

// Check if student data exists
if (!$siswa) {
    echo "<!DOCTYPE html>
    <html lang='id'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>ID Card Siswa</title>
        <style>body{font-family:Arial,sans-serif;margin:0;padding:20px;background-color:#f5f5f5}.alert{padding:20px;margin:20px;background-color:#f8d7da;color:#721c24;border:1px solid #f5c6cb;border-radius:10px;text-align:center}.alert i{font-size:24px;margin-bottom:10px}</style>
    </head>
    <body>
        <div class='alert'>
            <span style=\"font-size:24px;margin-bottom:10px\">&#9888;</span>
            <p>Data siswa tidak ditemukan.</p>
        </div>
    </body>
    </html>";
    exit;
}

// Pre-fetched data from controller - no additional DB queries needed
$dudi = isset($siswa->dudi_nama) ? (object)['dudi_nama' => $siswa->dudi_nama] : null;
$foto_profil = isset($siswa->foto_profil_url) ? $siswa->foto_profil_url : base_url('assets/img/default-avatar.png');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ID Card Siswa - <?php echo $siswa->siswa_nama ?></title>
    <!-- Preconnect for faster font loading -->
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <!-- Critical CSS embedded for immediate rendering -->
    <style>
        /* Critical rendering styles */
        body{font-family:Arial,sans-serif;margin:0;padding:20px;background-color:#f5f5f5}
        .id-card-container{display:flex;gap:20px;justify-content:center;flex-wrap:wrap}
        .id-card,.id-card-back{width:350px;height:580px;background-color:#563b82;color:white;border-radius:8px;padding:20px;box-shadow:0 4px 12px rgba(0,0,0,0.16);position:relative;overflow:hidden}
        .id-card-back{background-color:#432e67}
        .header{text-align:center;padding:15px 0;background-color:rgba(255,255,255,0.1);border-radius:10px;margin-bottom:20px}
        .photo-section{text-align:center;margin:20px 0}
        .photo{width:120px;height:150px;border:3px solid white;border-radius:10px;overflow:hidden;margin:0 auto;background-color:white}
        .photo img{width:100%;height:100%;object-fit:cover}
        .info-section{margin:20px 0;background-color:rgba(255,255,255,0.1);padding:15px;border-radius:10px}
        .label{font-size:14px;color:#e1bee7;margin-bottom:5px;display:block}
        .value{font-size:16px;font-weight:bold;margin-bottom:15px;color:white}
        .back-info{margin-top:30px}
        .qr-code-container{width:160px;height:160px;background-color:white;margin:20px auto;position:relative;border-radius:8px;padding:10px}.qr-code-img{width:100%;height:100%;border-radius:6px}.qr-overlay{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:50px;height:50px;border-radius:50%;overflow:hidden;border:3px solid white;box-shadow:0 0 0 2px #563b82}.profile-overlay{width:100%;height:100%;object-fit:cover}
        .contact-info{margin-top:20px;font-size:12px;text-align:center}
        .print-btn{display:block;margin:20px auto;padding:12px 30px;background-color:#563b82;color:white;border:none;border-radius:6px;font-size:16px;font-weight:600;cursor:pointer;text-decoration:none;text-align:center;width:200px}
        .print-btn:hover{background-color:#432e67}
        @media print {
            body {
                background-color: white;
                padding: 0;
                margin: 0;
            }
            
            .id-card-container {
                display: flex !important;
                flex-direction: row !important;
                gap: 15px !important;
                justify-content: center !important;
                align-items: flex-start !important;
                margin: 10px auto !important;
                width: 100% !important;
                max-width: none !important;
                page-break-inside: avoid !important;
            }
            
            .print-btn, .alert {
                display: none !important;
            }
            
            .id-card, .id-card-back {
                box-shadow: none !important;
                margin: 0 !important;
                flex: none !important;
            }
            
            @page {
                size: A4 portrait !important;
                margin: 10mm !important;
            }
            
            /* Ensure both cards appear side by side on the same page */
            .id-card {
                float: left !important;
                clear: none !important;
            }
            
            .id-card-back {
                float: right !important;
                clear: none !important;
            }
        }
    </style>
    <!-- Non-critical font awesome loaded asynchronously -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"></noscript>
</head>
<body style="font-family: Arial, sans-serif; margin: 0; padding: 20px; background-color: #f5f5f5;">
    <style>
        .id-card-container {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .id-card, .id-card-back {
            width: 72mm;
            height: 107mm;
            background-color: #563b82;
            color: white;
            border-radius: 8px;
            padding: 10px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
            position: relative;
            overflow: hidden;
            font-size: 14px;
        }
        
        .id-card-back {
            background-color: #432e67;
        }
        
        .header {
            text-align: center;
            padding: 15px 0;
            background-color: rgba(255,255,255,0.1);
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .photo-section {
            text-align: center;
            margin: 20px 0;
        }
        
        .photo {
            width: 100px;
            height: 120px;
            border: 3px solid white;
            border-radius: 10px;
            overflow: hidden;
            margin: 0 auto;
            background-color: white;
        }
        
        .photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .info-section {
            margin: 20px 0;
            background-color: rgba(255,255,255,0.1);
            padding: 15px;
            border-radius: 10px;
        }
        
        .label {
            font-size: 12px;
            color: #e1bee7;
            margin-bottom: 5px;
            display: block;
        }
        
        .value {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 15px;
            color: white;
        }
        
        .id-number {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin: 15px 0;
            color: #ffd54f;
        }
        
        .back-info {
            margin-top: 30px;
        }
        
        .qr-code-container {
            width: 130px;
            height: 130px;
            background-color: white;
            margin: 20px auto;
            position: relative;
            border-radius: 10px;
            padding: 10px;
        }
        
        .qr-code-img {
            width: 100%;
            height: 100%;
            border-radius: 8px;
        }
        

        .contact-info {
            margin-top: 20px;
            font-size: 11px;
            text-align: center;
        }
        
        .print-btn {
            display: block;
            margin: 20px auto;
            padding: 12px 30px;
            background-color: #563b82;
            color: white;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            width: 200px;
        }
        
        .print-btn:hover {
            background-color: #432e67;
        }
        
    </style>

    <div class="id-card-container">
        <div class="id-card">
            <div class="header">
                <h3 style="margin: 0; font-size: 18px;">SMK ITIKURIH HIBARNA</h3>
                <p style="margin: 5px 0 0 0; font-size: 14px; color: #ffd54f;">SEKOLAH MENENGAH KEJURUAN</p>
                <p style="margin: 5px 0 0 0; font-size: 12px; color: #e1bee7;">ID CARD SISWA PKL</p>
            </div>
            
            <div class="photo-section">
                <div class="photo">
                    <img src="<?php echo $foto_profil; ?>" alt="Foto Siswa" loading="lazy" decoding="async" onerror="this.src='<?php echo base_url('assets/img/default-avatar.png'); ?>'">
                </div>
            </div>
            
            <div class="info-section">
                <span class="label">Nama Siswa:</span>
                <div class="value"><?php echo !empty($siswa->siswa_nama) ? $siswa->siswa_nama : $siswa->nama_lengkap ?></div>
                
                <span class="label">NIS/NISN:</span>
                <div class="value"><?php echo !empty($siswa->siswa_nis) ? $siswa->siswa_nis : 'Belum Diisi' ?></div>
                
                <span class="label">Kelas:</span>
                <div class="value"><?php echo !empty($siswa->siswa_kelas) ? $siswa->siswa_kelas : 'Belum Ditentukan' ?></div>
                
                <span class="label">Jurusan:</span>
                <div class="value"><?php echo !empty($siswa->siswa_jurusan) ? $siswa->siswa_jurusan : 'Belum Ditentukan' ?></div>
                
                <span class="label">Perusahaan:</span>
                <div class="value" style="font-size: 16px; color: #ffd54f;"><?php echo $dudi && !empty($dudi->dudi_nama) ? $dudi->dudi_nama : 'Belum Ditentukan' ?></div>
            </div>
        </div>
        
        <div class="id-card-back">
            <div class="header">
                <h3 style="margin: 0; font-size: 18px;">SMK ITIKURIH HIBARNA</h3>
                <p style="margin: 5px 0 0 0; font-size: 14px; color: #ffd54f;">SEKOLAH MENENGAH KEJURUAN</p>
                <p style="margin: 5px 0 0 0; font-size: 12px; color: #e1bee7;">VERIFIKASI KEAMANAN</p>
            </div>
            
            <div class="back-info">
                <h4 style="text-align: center; color: #ffd54f; margin: 30px 0 20px 0;">KODE KEAMANAN</h4>
                <p style="text-align: center; font-size: 14px; margin-bottom: 20px;">Scan QR untuk verifikasi</p>
                
                <div class="qr-code-container">
                    <?php if(!empty($qr_code_url)): ?>
                        <img src="<?php echo $qr_code_url; ?>" alt="QR Code Verifikasi" class="qr-code-img" onerror="this.src='<?php echo base_url('assets/img/default-qr.png'); ?>'; this.alt='QR Code tidak tersedia';">
                    <?php else: ?>
                        <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background-color:#f0f0f0;color:#666;font-size:12px;text-align:center;border-radius:8px;">
                            QR Code tidak tersedia<br>Silakan coba lagi
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="contact-info">
                    <p>ID Card milik sekolah</p>
                    <p>Jika hilang hubungi sekolah</p>
                </div>
            </div>
        </div>
    </div>

    <button class="print-btn" onclick="window.print()">🖨️ Cetak ID Card</button>
</body>
</html>