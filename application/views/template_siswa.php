<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>SiPKL - <?php echo ucfirst($content); ?></title>
    <link rel="icon" type="image/png" href="<?php echo base_url('assets/img/logo.png') ?>">
    <!-- Force CSS Reload -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,400,0,0" rel="stylesheet">
    
    <style>
        /* CSS Version: 3.0 - Enhanced Menu */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f7fa;
            padding-bottom: 20px;
        }
        
        .header-siswa {
            background: #563b82;
            color: white;
                color: #563b82;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
                color: #563b82;
            font-size: 1.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .header-siswa .user-info {
            font-size: 0.9rem;
            margin-top: 5px;
                    gap: 10px;
                    margin: 0;
                    padding: 0;
                    background: transparent;
                    border-radius: 0;
            margin-top: 2px;
        }
        
        .content-wrapper {
            padding: 20px;
            max-width: 100%;
        }
        
        .card-mobile {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin: 0;
            padding: 0;
            background: transparent;
            border-radius: 0;
        }
        
        .menu-item {
            text-align: center;
            padding: 25px 20px;
            background: #fff;
                    transform: none;
                    border-color: #563b82;
            text-decoration: none;
            color: #292633;
            transition: background-color .15s ease, border-color .15s ease;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 112px;
            border: 1px solid #e5e1eb;
        }
        
        .menu-item::before {
            content: none;
        }
        
        .menu-item:hover::before {
            opacity: 1;
        }
        
        .menu-item:hover {
            transform: none;
            box-shadow: 0 1px 3px rgba(33, 27, 42, .05);
            color: #563b82;
            text-decoration: none;
            border-color: #563b82;
        }
        
        .menu-item:active {
            transform: none;
            box-shadow: 0 1px 3px rgba(33, 27, 42, .05);
            background: #f0ebf7;
        }
        
        .menu-item i {
            font-size: 2.5rem;
            color: #563b82;
            margin-bottom: 15px;
            text-shadow: none;
            transition: color .15s ease;
        }
        
        .menu-item:hover i {
            transform: none;
            color: #432e67;
        }
        
        .menu-item span {
            font-size: 1rem;
            font-weight: 700;
            color: #292633;
            line-height: 1.4;
            text-shadow: none;
            transition: color .15s ease;
        }
        
        .menu-item:hover span {
            color: #563b82;
            transform: none;
        }
        
        .menu-item span {
            display: block;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .btn-mobile {
            width: 100%;
            padding: 15px;
            border-radius: 12px;
            font-weight: 600;
            border: none;
            margin-top: 10px;
        }
        
        .info-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        
        .info-card .info-title {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 5px;
        }
        
        .info-card .info-value {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
        }
        
        .badge-status {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        .badge-info { background: #d1ecf1; color: #0c5460; }
        
        .back-btn {
            color: white;
            text-decoration: none;
            font-size: 1.2rem;
            margin-right: 15px;
        }
        
        .profile-avatar-header {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            overflow: hidden;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .profile-img-header {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .profile-placeholder-header {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }
        
        .user-name-header {
            font-size: 1rem;
            font-weight: 600;
            color: white;
            line-height: 1.2;
        }
        
        .user-class-header {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.9);
            margin-top: 2px;
        }
        
        .header-menu-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .header-menu-icon {
            color: white;
            font-size: 1.2rem;
            text-decoration: none;
            position: relative;
            padding: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s;
        }
        
        .header-menu-icon:hover {
            color: rgba(255,255,255,0.8);
            background: rgba(255,255,255,0.1);
        }
        
        .header-menu-icon.dropdown-toggle::after {
            display: none;
        }
        
        .notification-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            background: #e74c3c;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        .dropdown-menu-header {
            border-radius: 10px;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-top: 10px;
        }
        
        .dropdown-item-header {
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .dropdown-item-header i {
            width: 20px;
            color: #667eea;
        }
        
        .dropdown-item-header:hover {
            background: #f8f9fa;
            color: #667eea;
        }
        
        /* Mobile Optimization */
        @media (max-width: 576px) {
            .header-siswa {
                padding: 12px 15px;
            }
            
            .menu-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
                margin: 20px;
                padding: 12px;
                background: #f1f3f5;
                border-radius: 15px;
            }
            
            .menu-item {
                padding: 22px 16px;
                min-height: 120px;
                background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
                border: 2px solid #e9ecef;
            }
            
            .menu-item i {
                font-size: 2rem;
                margin-bottom: 12px;
            }
            
            .menu-item:hover {
                transform: translateY(-8px) scale(1.02);
                border-color: #667eea;
            }
            
            .menu-item span {
                font-size: 0.85rem;
            }
            
            .header-siswa .logo {
                font-size: 1.3rem;
            }
            
            .content-wrapper {
                padding: 15px;
            }
            
            .menu-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 12px;
            }
            
            .menu-item {
                padding: 15px 8px;
                min-height: 90px;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
            }
            
            .menu-item i {
                font-size: 1.8rem;
                margin-bottom: 8px;
            }
            
            .menu-item span {
                font-size: 0.8rem;
                line-height: 1.3;
            }
            
            .card-mobile {
                padding: 15px;
                margin-bottom: 15px;
            }
            
            .btn-mobile {
                padding: 12px;
                font-size: 1rem;
            }
            
            .info-card {
                padding: 15px;
                margin-bottom: 15px;
            }
            
            .profile-avatar-header {
                width: 40px;
                height: 40px;
            }
            
            .user-name-header {
                font-size: 0.9rem;
            }
            
            .user-class-header {
                font-size: 0.75rem;
            }
            
            .header-menu-icon {
                font-size: 1.1rem;
                padding: 6px;
            }
        }
        
        /* Extra Small Devices (iPhone SE, etc) */
        @media (max-width: 375px) {
            .menu-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
                margin: 12px;
                padding: 6px;
            }
            
            .menu-item {
                padding: 15px 10px;
                min-height: 90px;
            }
            
            .menu-item i {
                font-size: 1.6rem;
                margin-bottom: 8px;
            }
            
            .menu-item span {
                font-size: 0.8rem;
            }
            
            .menu-item {
                padding: 12px 5px;
                min-height: 80px;
            }
            
            .menu-item i {
                font-size: 1.6rem;
            }
            
            .menu-item span {
                font-size: 0.75rem;
            }
            
            .content-wrapper {
                padding: 12px;
            }
        }
        
        /* Touch-friendly enhancements */
        .menu-item {
            -webkit-tap-highlight-color: transparent;
            tap-highlight-color: transparent;
            user-select: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            position: relative;
            overflow: hidden;
        }
        
        .menu-item:active {
            transform: scale(0.95);
            background: #f8f9fa;
        }
        
        .menu-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
            border-radius: 15px;
        }
        
        .menu-item:hover::before {
            opacity: 1;
        }
        
        .menu-item:active {
            transform: scale(0.95);
            background: #f0f0f0;
        }
        
        /* Prevent zoom on input focus */
        input, textarea, select {
            font-size: 16px;
        }
        
        /* Animation for menu items loading */
        @keyframes menuAppear {
            0% {
                opacity: 0;
                transform: translateY(20px) scale(0.9);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        .menu-item {
            animation: menuAppear 0.6s ease-out forwards;
        }
        
        .menu-item:nth-child(1) { animation-delay: 0.1s; }
        .menu-item:nth-child(2) { animation-delay: 0.2s; }
        .menu-item:nth-child(3) { animation-delay: 0.3s; }
        .menu-item:nth-child(4) { animation-delay: 0.4s; }
        
        /* Better scrollbar for mobile */
        ::-webkit-scrollbar {
            width: 6px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        .student-app {
            --student-purple: #563b82;
            --student-purple-dark: #432e67;
            --student-purple-soft: #f0ebf7;
            --student-ink: #292633;
            --student-muted: #716d79;
            --student-line: #e5e1eb;
            --student-canvas: #f5f4f7;
            background: var(--student-canvas);
            color: var(--student-ink);
        }

        .student-app .header-siswa {
            background: var(--student-purple);
            padding: 12px 20px;
            box-shadow: 0 1px 4px rgba(37, 25, 56, .18);
        }

        .student-app .content-wrapper {
            max-width: 1280px;
            margin: 0 auto;
            padding: 24px;
        }

        .student-app .card,
        .student-app .card-mobile,
        .student-app .info-card {
            border: 1px solid var(--student-line) !important;
            border-radius: 8px !important;
            box-shadow: 0 1px 3px rgba(33, 27, 42, .05) !important;
        }

        .student-app .card-header {
            border-bottom: 1px solid var(--student-line) !important;
        }

        .student-app .btn-primary,
        .student-app .bg-primary {
            background-color: var(--student-purple) !important;
            border-color: var(--student-purple) !important;
        }

        .student-app .btn-primary:hover,
        .student-app .btn-primary:focus {
            background-color: var(--student-purple-dark) !important;
            border-color: var(--student-purple-dark) !important;
        }

        .student-app .bg-primary.bg-opacity-10 {
            background-color: var(--student-purple-soft) !important;
        }

        .student-app .btn-outline-primary {
            color: var(--student-purple) !important;
            border-color: var(--student-purple) !important;
        }

        .student-app .btn-outline-primary:hover {
            color: #fff !important;
            background: var(--student-purple) !important;
        }

        .student-app .text-primary,
        .student-app a:not(.btn):not(.dropdown-item) {
            color: var(--student-purple) !important;
        }

        .student-app .form-control:focus,
        .student-app .form-select:focus {
            border-color: #8a72ad;
            box-shadow: 0 0 0 .2rem rgba(86, 59, 130, .14);
        }

        .student-app .menu-grid {
            gap: 12px;
            margin: 0;
            padding: 0;
            background: transparent;
        }

        .student-app .menu-item,
        .student-app .menu-item:hover,
        .student-app .menu-item:active {
            min-height: 112px;
            padding: 18px 14px;
            border: 1px solid var(--student-line) !important;
            border-radius: 8px;
            background: #fff !important;
            box-shadow: 0 1px 3px rgba(33, 27, 42, .05);
            color: var(--student-ink) !important;
            transform: none;
            animation: none;
            overflow: visible;
        }

        .student-app .menu-item::before {
            content: none;
        }

        .student-app .menu-item i {
            color: var(--student-purple) !important;
            font-size: 1.65rem;
            margin-bottom: 10px;
            text-shadow: none;
            transform: none;
        }

        .student-app .menu-item span,
        .student-app .menu-item:hover span {
            color: var(--student-ink) !important;
            font-size: .9rem;
            font-weight: 600;
            text-shadow: none;
            transform: none;
        }

        .student-app .header-menu-icon {
            width: 40px;
            height: 40px;
            padding: 0;
            border-radius: 8px;
            font-size: 1.2rem;
        }

        .student-app .header-menu-icon:hover {
            color: #fff;
            background: rgba(255, 255, 255, .12);
        }

        .student-app .dropdown-menu-header {
            border: 1px solid var(--student-line);
            border-radius: 8px;
            box-shadow: 0 8px 22px rgba(33, 27, 42, .12);
        }

        .student-app .dropdown-item-header i,
        .student-app .dropdown-item-header:hover {
            color: var(--student-purple) !important;
        }

        .student-app .student-icon {
            display: inline-flex;
            width: 24px;
            height: 24px;
            align-items: center;
            justify-content: center;
            font-family: 'Material Symbols Rounded';
            font-size: 22px;
            font-weight: normal;
            font-style: normal;
            line-height: 1;
            vertical-align: middle;
            white-space: nowrap;
            direction: ltr;
            -webkit-font-feature-settings: 'liga';
            -webkit-font-smoothing: antialiased;
        }

        .student-app .content-wrapper .fas,
        .student-app .content-wrapper .far,
        .student-app .content-wrapper .fa-solid {
            text-shadow: none;
        }

        .student-app .content-wrapper i[class*="fa-"]:not(.text-success):not(.text-danger):not(.text-warning):not(.text-white):not(.text-muted):not(.text-secondary):not(.text-info) {
            color: var(--student-purple) !important;
        }

        .student-app .student-icon {
            color: var(--student-purple);
        }

        .student-app .header-menu-icon .student-icon,
        .student-app .profile-placeholder-header .material-symbols-rounded,
        .student-app .back-btn .student-icon {
            color: #fff;
        }

        .student-app .dropdown-item-header .student-icon {
            color: var(--student-purple);
        }

        @media (max-width: 576px) {
            .student-app .content-wrapper {
                padding: 16px 12px;
            }

            .student-app .menu-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 10px;
            }

            .student-app .menu-item,
            .student-app .menu-item:hover {
                min-height: 96px;
                padding: 14px 8px;
            }

            .student-app .header-menu-right {
                gap: 4px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .student-app *,
            .student-app *::before,
            .student-app *::after {
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                scroll-behavior: auto !important;
                transition-duration: .01ms !important;
            }
        }
    </style>
</head>
<body class="student-app">
    <!-- Header -->
    <div class="header-siswa">
        <?php 
        $userdata = $this->session->userdata('userdata');
        $this->db->where('user_id', $userdata['id']);
        $siswa = $this->db->get('tb_siswa')->row();
        $foto_profil = isset($userdata['foto_profil']) ? $userdata['foto_profil'] : null;
        
        // Get jumlah notifikasi (pengumuman baru dalam 7 hari terakhir)
        $this->db->where('created_at >=', date('Y-m-d H:i:s', strtotime('-7 days')));
        $notifikasi_count = $this->db->get('tb_pengumuman')->num_rows();
        ?>
        <div class="d-flex align-items-center">
            <?php if(uri_string() != 'siswa/view' && uri_string() != 'siswa/view/home'){ ?>
            <a href="<?php echo base_url('siswa/view') ?>" class="back-btn">
                <span class="material-symbols-rounded student-icon" aria-hidden="true">arrow_back</span>
            </a>
            <?php } ?>
            <div class="d-flex align-items-center flex-grow-1">
                <div class="profile-avatar-header me-3">
                    <?php if($foto_profil && file_exists('./uploads/profil/'.$foto_profil)){ ?>
                        <img src="<?php echo base_url('uploads/profil/'.$foto_profil) ?>" 
                             alt="Foto Profil" 
                             class="profile-img-header">
                    <?php } else { ?>
                        <div class="profile-placeholder-header">
                            <span class="material-symbols-rounded" aria-hidden="true">person</span>
                        </div>
                    <?php } ?>
                </div>
                <div class="flex-grow-1">
                    <div class="user-name-header"><?php echo $userdata['nama_lengkap']; ?></div>
                    <?php if($siswa){ ?>
                    <div class="user-class-header">
                        <?php echo $siswa->siswa_kelas ? $siswa->siswa_kelas : 'Kelas'; ?> | 
                        <?php echo $siswa->siswa_jurusan ? $siswa->siswa_jurusan : 'Jurusan'; ?>
                </div>
                    <?php } ?>
                </div>
            </div>
            
            <!-- Menu Kanan -->
            <div class="header-menu-right">
                <!-- Profil -->
                <a href="<?php echo base_url('siswa/view/profile') ?>" class="header-menu-icon" title="Profil">
                    <span class="material-symbols-rounded student-icon" aria-hidden="true">person</span>
                </a>
                
                <!-- Beranda -->
                <a href="<?php echo base_url('siswa/view') ?>" class="header-menu-icon" title="Beranda">
                    <span class="material-symbols-rounded student-icon" aria-hidden="true">home</span>
                </a>
                
                <!-- Notifikasi -->
                <div class="dropdown">
                    <a href="#" class="header-menu-icon dropdown-toggle" data-bs-toggle="dropdown" title="Notifikasi">
                        <span class="material-symbols-rounded student-icon" aria-hidden="true">notifications</span>
                        <?php if($notifikasi_count > 0){ ?>
                        <span class="notification-badge"><?php echo $notifikasi_count > 9 ? '9+' : $notifikasi_count; ?></span>
                        <?php } ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-header">
                        <li><h6 class="dropdown-header">Notifikasi</h6></li>
                        <li><hr class="dropdown-divider"></li>
                        <?php 
                        // Pengumuman baru
                        $this->db->where('created_at >=', date('Y-m-d H:i:s', strtotime('-7 days')));
                        $this->db->order_by('created_at', 'DESC');
                        $this->db->limit(5);
                        $notif_pengumuman = $this->db->get('tb_pengumuman')->result();
                        
                        $total_notif = count($notif_pengumuman);
                        
                        if($total_notif > 0){
                            foreach($notif_pengumuman as $notif){ ?>
                            <li>
                                <a class="dropdown-item dropdown-item-header" href="<?php echo base_url('siswa/view/detail-pengumuman/'.$notif->pengumuman_id) ?>">
                                    <span class="material-symbols-rounded student-icon" aria-hidden="true">campaign</span>
                                    <div>
                                        <div style="font-weight: 600;"><?php echo $notif->judul; ?></div>
                                        <small class="text-muted"><?php echo date('d M Y', strtotime($notif->created_at)); ?></small>
                                    </div>
                                </a>
                            </li>
                            <?php } 
                        } else { ?>
                        <li>
                            <a class="dropdown-item dropdown-item-header" href="#">
                                <span class="material-symbols-rounded student-icon" aria-hidden="true">task_alt</span>
                                <div>Tidak ada notifikasi baru</div>
                            </a>
                        </li>
                        <?php } ?>
                        <li><hr class="dropdown-divider"></li>

                    </ul>
                </div>
                
                <!-- Menu Lainnya -->
                <div class="dropdown">
                    <a href="#" class="header-menu-icon dropdown-toggle" data-bs-toggle="dropdown" title="Menu">
                        <span class="material-symbols-rounded student-icon" aria-hidden="true">more_vert</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-header">
                        <li>
                            <a class="dropdown-item dropdown-item-header" href="<?php echo base_url('siswa/view/profile') ?>">
                                <span class="material-symbols-rounded student-icon" aria-hidden="true">person</span>
                                <div>Profil</div>
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item dropdown-item-header" href="<?php echo base_url('siswa/view') ?>">
                                <span class="material-symbols-rounded student-icon" aria-hidden="true">home</span>
                                <div>Beranda</div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item dropdown-item-header" href="<?php echo base_url('siswa/view/presensi') ?>">
                                <span class="material-symbols-rounded student-icon" aria-hidden="true">event_available</span>
                                <div>Presensi PKL</div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item dropdown-item-header" href="<?php echo base_url('siswa/view/logbook') ?>">
                                <span class="material-symbols-rounded student-icon" aria-hidden="true">menu_book</span>
                                <div>Logbook PKL</div>
                            </a>
                        </li>


                        <li>
                            <a class="dropdown-item dropdown-item-header" href="<?php echo base_url('siswa/view/kontak-pembimbing') ?>">
                                <span class="material-symbols-rounded student-icon" aria-hidden="true">contact_page</span>
                                <div>Kontak Pembimbing</div>
                            </a>
                        </li>


                        <li>
                            <a class="dropdown-item dropdown-item-header" href="<?php echo base_url('siswa/view/change-password') ?>">
                                <span class="material-symbols-rounded student-icon" aria-hidden="true">key</span>
                                <div>Ubah Password</div>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item dropdown-item-header text-danger" href="<?php echo base_url('auth/logout') ?>" onclick="return confirm('Yakin ingin logout?')">
                                <span class="material-symbols-rounded student-icon" aria-hidden="true">logout</span>
                                <div>Logout</div>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="content-wrapper">
        <?php $this->load->view($content);?>
    </div>


    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto dismiss alerts
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
    <?php
    $tracking_userdata = $this->session->userdata('userdata');
    $tracking_siswa = null;
    $tracking_presensi = null;
    if ($tracking_userdata && isset($tracking_userdata['id'])) {
        $this->db->where('user_id', $tracking_userdata['id']);
        $tracking_siswa = $this->db->get('tb_siswa')->row();
        if ($tracking_siswa && $tracking_siswa->status_pengajuan === 'disetujui') {
            $tracking_presensi = $this->db
                ->where('siswa_id', $tracking_siswa->siswa_id)
                ->where('tanggal', date('Y-m-d'))
                ->where('jam_masuk IS NOT NULL', null, false)
                ->where('jam_pulang IS NULL', null, false)
                ->where('status_validasi', 'valid')
                ->get('tb_presensi')->row();
        }
    }
    ?>
    <?php if ($tracking_presensi || $content === 'siswa/logbook'): ?>
    <script>
    (function () {
        var endpoint = '<?php echo base_url('siswa/update_lokasi_realtime'); ?>';
        var logbookForm = document.getElementById('form-logbook');
        var trackingEnabled = <?php echo $tracking_presensi ? 'true' : 'false'; ?>;
        var trackingWatchId = null;
        var invalidAttendanceNoticeShown = false;

        function sendLocation(position) {
            var latitude = position.coords.latitude;
            var longitude = position.coords.longitude;
            if (logbookForm) {
                document.getElementById('logbook-latitude').value = latitude;
                document.getElementById('logbook-longitude').value = longitude;
            }
            fetch(endpoint, {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded', 'Accept': 'application/json'},
                credentials: 'same-origin',
                body: 'latitude=' + encodeURIComponent(latitude) + '&longitude=' + encodeURIComponent(longitude)
            }).then(function (response) {
                return response.json();
            }).then(function (result) {
                if (result.attendance_invalid) {
                    trackingEnabled = false;
                    if (trackingWatchId !== null && navigator.geolocation) {
                        navigator.geolocation.clearWatch(trackingWatchId);
                        trackingWatchId = null;
                    }
                    if (!invalidAttendanceNoticeShown) {
                        invalidAttendanceNoticeShown = true;
                        window.alert(result.message || 'Presensi tidak sah. Hubungi guru pembimbing untuk klarifikasi.');
                    }
                }
            }).catch(function () {});
        }

        if (trackingEnabled && navigator.geolocation) {
            trackingWatchId = navigator.geolocation.watchPosition(sendLocation, function () {}, {enableHighAccuracy: true, maximumAge: 15000, timeout: 15000});
        }

        if (logbookForm) {
            logbookForm.addEventListener('submit', function (event) {
                if (logbookForm.dataset.locationReady === '1') return;
                event.preventDefault();
                if (!navigator.geolocation) {
                    window.alert('Browser tidak mendukung GPS.');
                    return;
                }
                navigator.geolocation.getCurrentPosition(function (position) {
                    document.getElementById('logbook-latitude').value = position.coords.latitude;
                    document.getElementById('logbook-longitude').value = position.coords.longitude;
                    logbookForm.dataset.locationReady = '1';
                    logbookForm.submit();
                }, function () {
                    window.alert('Lokasi tidak dapat diambil. Izinkan akses GPS lalu coba lagi.');
                }, {enableHighAccuracy: true, maximumAge: 0, timeout: 15000});
            });
        }
    })();
    </script>
    <?php endif; ?>
</body>
</html>

