<div class="card-mobile text-center" id="idCard">
    <h5 class="mb-3"><i class="fas fa-id-card me-2 text-primary"></i>ID Card Siswa PKL</h5>
    
    <?php 
    $userdata = $this->session->userdata('userdata');
    $this->db->where('user_id', $userdata['id']);
    $siswa = $this->db->get('tb_siswa')->row();

    // QR code URL is passed from controller
    $qr_code_url = isset($qr_code_url) ? $qr_code_url : '';

    if($siswa && $siswa->status_pengajuan == 'disetujui'){
        // If no QR code provided, generate one using the QR generator library
        if(empty($qr_code_url)) {
            $this->load->library('qr_generator');
            $qr_code_url = $this->qr_generator->generate_student_qr($siswa);
        } 
        // Get DUDI 
        $dudi = null; 
        if($siswa->dudi_id){ 
            $this->db->where('dudi_id', $siswa->dudi_id); 
            $dudi = $this->db->get('tb_dudi')->row(); 
        }
        
        // Get foto profil 
        $foto_profil = null; 
        if($siswa->user_id){ 
            $this->db->where('id', $siswa->user_id); 
            $user = $this->db->get('tb_user')->row(); 
            if($user && $user->foto_profil && file_exists('./uploads/profil/'.$user->foto_profil)){ 
                $foto_profil = base_url('uploads/profil/'.$user->foto_profil); 
            }
        }
    ?> 
    <!-- ID Card Design Following Your Template Exactly -->
    <style>
        :root {
            --primary-purple: #563b82;
            --light-purple: #f0ebf7;
            --accent-purple: #8064a8;
            --text-color: #432e67;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #e0e0e0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            gap: 20px;
            flex-wrap: wrap;
        }

        .id-card-container {
            display: flex;
            gap: 20px;
        }

        .id-card {
            width: 350px;
            height: 580px;
            background-color: var(--light-purple);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
            position: relative;
            text-align: center;
            display: flex;
            flex-direction: column;
        }

        .header-design {
            position: relative;
            height: 200px;
            background-color: var(--primary-purple);
            clip-path: polygon(0 0, 100% 0, 100% 70%, 50% 100%, 0 70%);
            z-index: 1;
        }

        .header-design::after {
            content: "";
            position: absolute;
            bottom: 5px;
            left: 0;
            right: 0;
            height: 100%;
            background-color: var(--accent-purple);
            clip-path: polygon(0 0, 100% 0, 100% 75%, 50% 100%, 0 75%);
            z-index: -1;
        }

        .photo-container {
            width: 160px;
            height: 160px;
            background: white;
            position: absolute;
            top: 80px;
            left: 50%;
            transform: translateX(-50%) rotate(45deg);
            z-index: 2;
            overflow: hidden;
            border: 4px solid white;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .photo-container img {
            width: 140%;
            height: 140%;
            object-fit: cover;
            transform: rotate(-45deg) translate(-20%, -10%);
        }

        .content {
            margin-top: 100px;
            padding: 20px;
            color: var(--text-color);
            flex-grow: 1;
        }

        .school-name {
            font-size: 1.2rem;
            font-weight: 500;
            margin-bottom: 30px;
        }

        .label {
            font-size: 0.9rem;
            margin-bottom: 5px;
            display: block;
            text-align: left;
        }

        .student-name {
            font-size: 1.8rem;
            font-weight: 900;
            margin: 5px 0 20px 0;
            color: var(--primary-purple);
            text-align: left;
        }

        .course {
            font-size: 1rem;
            line-height: 1.4;
            margin-bottom: 30px;
            text-align: left;
        }

        .info-badge {
            background-color: var(--primary-purple);
            color: white;
            padding: 12px 25px;
            border-radius: 20px;
            display: inline-block;
            margin-top: auto;
            margin-bottom: 30px;
        }

        .id-number {
            font-weight: bold;
            font-size: 1.1rem;
            display: block;
        }

        /* Bagian Belakang Kartu */
        .back-side .content {
            margin-top: 40px;
            font-size: 0.9rem;
            padding: 0 30px;
        }

        .contact-info {
            margin-top: 30px;
            text-align: left;
        }
    </style>

    <div class="id-card-container">
        <div class="id-card">
            <div class="header-design">
                <div style="color: white; padding-top: 20px; font-size: 1.1rem; font-weight: 600;">SMK ITIKURIH HIBARNA</div>
            </div>
            
            <div class="photo-container">
                <img src="<?php echo $foto_profil ?: base_url('assets/img/default-avatar.png'); ?>" alt="Student Photo" onerror="this.src='<?php echo base_url('assets/img/default-avatar.png'); ?>'">
            </div>

            <div class="content">
                <span class="label">Logo SMK ITIKURIH HIBARNA</span>
                
                <div style="text-align: left; margin: 15px 0;">
                    <div style="font-size: 1.1rem; font-weight: 600; margin-bottom: 5px;">SMK ITIKURIH</div>
                    <div style="font-size: 1rem; font-weight: 500; margin-bottom: 15px;">HIBARNA</div>
                    
                    <div style="background-color: var(--primary-purple); color: white; padding: 8px 20px; border-radius: 15px; display: inline-block; margin: 10px 0;">
                        <span style="font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">Prakerin</span>
                    </div>
                    
                    <div style="font-size: 1.1rem; font-weight: 700; margin: 15px 0; color: var(--primary-purple);">#<?php echo str_pad($siswa->siswa_id, 6, '0', STR_PAD_LEFT); ?></div>
                    
                    <span class="label">Nama Siswa:</span>
                    <h1 class="student-name"><?php echo $siswa->siswa_nama ?></h1>
                    
                    <span class="label">SISWA PRAKERIN</span>
                    <p class="course"><?php echo $siswa->siswa_kelas ?></p>

                    <span class="label">Perusahaan:</span>
                    <p class="course" style="font-size: 1.2rem; font-weight: bold; color: var(--primary-purple);"><?php echo $dudi ? $dudi->dudi_nama : 'Belum Ditentukan' ?></p>

                    <span class="label">ID:</span>
                    <p class="course">#<?php echo str_pad($siswa->siswa_id, 6, '0', STR_PAD_LEFT); ?></p>
                </div>

                <div class="info-badge" style="margin-left: 0; text-align: left;">
                    <span style="font-size: 0.8rem;">ID Card PKL</span>
                </div>
            </div>
        </div>

        <div class="id-card back-side">
            <div class="header-design">
                <div style="color: white; padding-top: 20px; font-size: 1.1rem; font-weight: 600;">SMK ITIKURIH HIBARNA</div>
            </div>
            
            <div class="content">
                <div style="text-align: center; font-weight: bold; font-size: 1.2rem; margin-bottom: 20px;">SMK ITIKURIH HIBARNA</div>
                <p style="text-align: center; font-weight: bold; font-size: 1rem; margin-bottom: 20px;">SEKOLAH MENENGAH KEJURUAN</p>
                
                <div style="text-align: center; margin: 30px 0;">
                    <h3 style="color: var(--primary-purple); font-size: 1.1rem; margin-bottom: 15px;">KODE KEAMANAN</h3>
                    <p style="font-size: 0.9rem; margin-bottom: 20px;">Scan QR untuk verifikasi</p>
                    <div class="qr-code-container" style="width: 160px; height: 160px; background-color: white; margin: 20px auto; position: relative; border-radius: 10px; padding: 10px;">
                        <img src="<?php echo $qr_code_url; ?>" 
                             alt="QR Code Verifikasi" 
                             style="width: 100%; height: 100%; border-radius: 8px;" class="qr-code-img">
                        <div class="qr-overlay" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 50px; height: 50px; border-radius: 50%; overflow: hidden; border: 3px solid white; box-shadow: 0 0 0 2px #6a3093;">
                            <img src="<?php echo $foto_profil ?: base_url('assets/img/default-avatar.png'); ?>" 
                                 alt="Foto Siswa" 
                                 style="width: 100%; height: 100%; object-fit: cover;" class="profile-overlay" onerror="this.src='<?php echo base_url('assets/img/default-avatar.png'); ?>'">
                        </div>
                    </div>
                </div>
                
                <div class="contact-info">
                    <p>ID Card milik sekolah</p>
                    <p>Jika hilang hubungi sekolah</p>
                    <p style="font-weight: bold;">#<?php echo str_pad($siswa->siswa_id, 6, '0', STR_PAD_LEFT); ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <button onclick="printIDCard()" class="btn btn-primary btn-mobile mt-3" style="background: #6a3093; border: none; padding: 12px 25px; font-weight: 600; font-size: 1rem;">
        <i class="fas fa-print me-2"></i>Cetak ID Card
    </button>
    
    <?php } else { ?>
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle me-2"></i>
        ID Card hanya dapat dicetak setelah pengajuan PKL Anda disetujui oleh Hubin.
    </div>
    <?php } ?>
</div>

<style>
@media print {
    body {
        display: block;
        background-color: white;
        margin: 0;
        padding: 10mm;
    }
    #idCard, #idCard * {
        visibility: visible;
    }
    #idCard {
        position: relative;
        width: 100%;
        max-width: 750px;
        height: auto;
        box-shadow: none;
        margin: 0 auto;
        padding: 0;
    }
    .id-card-container {
        display: flex !important;
        flex-direction: row !important;
        width: 100%;
        max-width: 750px;
        height: auto;
        margin: 0;
        padding: 0;
        gap: 15px;
        justify-content: space-between !important;
        align-items: flex-start !important;
        page-break-inside: avoid !important;
    }
    .id-card, .back-side {
        box-shadow: none !important;
        margin: 0 !important;
        width: 72mm !important;  /* Approximately half of A4 width minus margins */
        height: 107mm !important; /* Adjusted for proper fit */
        font-size: 14px !important;
        flex: none !important;
    }
    .id-card .header h3 {
        font-size: 16px !important;
    }
    .id-card .header p {
        font-size: 12px !important;
    }
    .photo {
        width: 100px !important;
        height: 120px !important;
    }
    .label {
        font-size: 12px !important;
    }
    .value {
        font-size: 14px !important;
    }
    .course {
        font-size: 14px !important;
    }
    .course[style*="font-size: 1.2rem"] {
        font-size: 1.1rem !important;
        font-weight: bold !important;
    }
    .back-side .header h3 {
        font-size: 16px !important;
    }
    .back-side .header p {
        font-size: 12px !important;
    }
    .qr-code-container {
        width: 130px !important;
        height: 130px !important;
        background: white !important;
        padding: 10px !important;
        border-radius: 10px !important;
        display: inline-block !important;
        margin: 20px auto !important;
    }
    
    .qr-code-container img {
        width: 100% !important;
        height: 100% !important;
        object-fit: contain !important;
        border-radius: 8px !important;
    }
    .contact-info {
        font-size: 11px;
    }
    .btn, h5 {
        display: none !important;
    }
    @page {
        size: A4 portrait !important;
        margin: 10mm !important;
    }
    .id-card {
        position: static;
        float: left !important;
        clear: none !important;
    }
    .back-side {
        position: static;
        float: right !important;
        clear: none !important;
    }
}
</style>

<script>
function printIDCard() {
    window.print();
}
</script>