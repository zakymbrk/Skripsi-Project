<?php
$current_user = $this->session->userdata('userdata');
$is_admin = isset($current_user['level']) && (int) $current_user['level'] === 1;
$admin_notices = array();

if ($is_admin) {
    foreach (array('error_message' => 'danger', 'message' => 'success') as $flash_key => $alert_type) {
        $flash_message = $this->session->flashdata($flash_key);
        if (!empty($flash_message)) {
            $plain_message = trim(strip_tags((string) $flash_message));
            if ($flash_key === 'message' && preg_match('/\b(gagal|error|tidak dapat|belum berhasil)\b/i', $plain_message)) {
                $alert_type = 'danger';
            }
            $admin_notices[] = array('type' => $alert_type, 'message' => $plain_message);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SiPKL - <?php echo ucfirst($content); ?></title>
    <link rel="icon" type="image/png" href="<?php echo base_url('assets/logo.png') ?>">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,400,0,0" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo base_url('assets/css/bootstrap-custom.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/dashboard-custom.css') ?>">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fa;
        }
        
        /* Modern Sidebar */
        .sidebar {
            background: #1a1a1a;
            min-height: calc(100vh - 56px);
            padding: 0;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 15px 20px;
            border-left: 3px solid transparent;
            transition: all 0.3s;
            font-weight: 500;
        }
        
        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: #fff;
            border-left-color: #fff;
            padding-left: 25px;
        }
        
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.15);
            color: #fff;
            border-left-color: #fff;
            font-weight: 600;
        }
        
        .sidebar .nav-link i {
            width: 24px;
            margin-right: 12px;
            text-align: center;
        }
        
        /* Modern Navbar */
        .navbar-custom {
            background: #1a1a1a !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 12px 0;
        }
        
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            color: #fff !important;
        }
        
        /* Modern Cards */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
            transition: all 0.3s;
            margin-bottom: 25px;
        }
        
        .card:hover {
            box-shadow: 0 5px 25px rgba(0,0,0,0.12);
            transform: translateY(-2px);
        }
        
        /* Ensure main content area is clickable */
        main {
            pointer-events: auto;
        }
        
        .card-header {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            color: white;
            border-radius: 12px 12px 0 0 !important;
            padding: 18px 25px;
            font-weight: 600;
            border: none;
        }
        
        /* Modern Buttons */
        .btn {
            border-radius: 8px;
            padding: 10px 24px;
            font-weight: 500;
            transition: all 0.3s;
            border: none;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #2d2d2d 0%, #1a1a1a 100%);
        }
        
        /* Modern Tables */
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        
        .table thead {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            color: white;
        }
        
        .table thead th {
            border: none;
            padding: 18px 20px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }
        
        .table tbody tr {
            transition: all 0.2s;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9fa;
            transform: scale(1.01);
        }
        
        /* Profile Section */
        .sidebar-profile {
            padding: 25px 20px;
            background: rgba(255,255,255,0.05);
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }
        
        .sidebar-profile img {
            border: 3px solid rgba(255,255,255,0.3);
        }
        
        /* Main Content */
        main {
            background: #f5f7fa;
            padding: 30px;
            min-height: calc(100vh - 56px);
        }
        
        /* Stat Cards */
        .stat-card-modern {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
            transition: all 0.3s;
            height: 100%;
            border-left: 4px solid #1a1a1a;
        }
        
        .stat-card-modern:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 25px rgba(0,0,0,0.12);
        }
        
        .stat-card-modern .stat-icon {
            font-size: 2.5rem;
            opacity: 0.2;
            float: right;
            margin-top: -10px;
            color: #1a1a1a;
        }
        
        .stat-card-modern .stat-number {
            font-size: 2.2rem;
            font-weight: 700;
            color: #1a1a1a;
            margin: 10px 0;
        }
        
        .stat-card-modern .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .admin-panel {
            --admin-ink: #263b33;
            --admin-green: #24483b;
            --admin-green-hover: #1c3a30;
            --admin-accent: #c7953e;
            --admin-line: #dfe5df;
            --admin-paper: #ffffff;
            --admin-canvas: #f2f4f0;
            font-family: 'IBM Plex Sans', sans-serif;
            color: var(--admin-ink);
            background: var(--admin-canvas);
        }

        .admin-panel main {
            background: var(--admin-canvas);
            padding-top: 24px;
        }

        .admin-panel .navbar-custom,
        .admin-panel .sidebar {
            background: var(--admin-green) !important;
            box-shadow: none;
        }

        .admin-panel .navbar-custom {
            border-bottom: 1px solid rgba(255, 255, 255, .12);
        }

        .admin-panel .navbar-brand {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .admin-panel .sidebar {
            min-height: calc(100vh - 56px);
        }

        .admin-panel .sidebar-profile {
            padding: 20px 16px;
            margin-bottom: 8px;
            background: transparent;
            border-bottom-color: rgba(255, 255, 255, .14);
        }

        .admin-panel .sidebar .nav-link {
            margin: 2px 0;
            padding: 10px 12px;
            border-left: 2px solid transparent;
            border-radius: 3px;
            font-size: .94rem;
            transition: background-color .15s ease, color .15s ease;
        }

        .admin-panel .sidebar .nav-link:hover,
        .admin-panel .sidebar .nav-link.active {
            padding-left: 12px;
            background: rgba(255, 255, 255, .11);
            border-left-color: var(--admin-accent);
        }

        .admin-panel .sidebar .nav-link i {
            width: 20px;
            margin-right: 8px;
            color: #d7c18f;
        }

        .admin-panel .hubin-icon {
            display: inline-flex;
            width: 22px;
            height: 22px;
            align-items: center;
            justify-content: center;
            margin-right: 9px;
            color: #d7c18f;
            font-family: 'Material Symbols Rounded';
            font-size: 21px;
            font-weight: normal;
            font-style: normal;
            line-height: 1;
            vertical-align: middle;
            white-space: nowrap;
            direction: ltr;
            -webkit-font-feature-settings: 'liga';
            -webkit-font-smoothing: antialiased;
        }

        .admin-panel .sidebar .nav-link.active .hubin-icon {
            color: #f0d28f;
        }

        .admin-panel .hubin-icon-danger {
            color: #e6a59b;
        }

        .admin-panel .card,
        .admin-panel .stat-card-modern {
            margin-bottom: 18px;
            border: 1px solid var(--admin-line);
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(25, 45, 35, .04);
            transition: none;
            overflow: visible;
        }

        .admin-panel .card:hover,
        .admin-panel .stat-card-modern:hover {
            transform: none;
            box-shadow: 0 1px 3px rgba(25, 45, 35, .04);
        }

        .admin-panel .card-header {
            padding: 14px 18px;
            color: var(--admin-ink) !important;
            background: var(--admin-paper) !important;
            border-bottom: 1px solid var(--admin-line);
            border-radius: 4px 4px 0 0 !important;
        }

        .admin-panel .card-header h1,
        .admin-panel .card-header h2,
        .admin-panel .card-header h3,
        .admin-panel .card-header h4,
        .admin-panel .card-header h5,
        .admin-panel .card-header h6 {
            color: var(--admin-ink) !important;
        }

        .admin-panel .table {
            margin-bottom: 0;
            border-radius: 0;
            overflow: visible;
        }

        .admin-panel .table thead,
        .admin-panel .table-light th {
            color: #42564c;
            background: #eef1ed !important;
        }

        .admin-panel .table thead th {
            padding: 12px 14px;
            border-bottom: 1px solid var(--admin-line);
            font-size: .78rem;
            text-transform: none;
            letter-spacing: 0;
        }

        .admin-panel .table tbody tr,
        .admin-panel .table tbody tr:hover {
            transform: none;
            background-color: transparent;
        }

        .admin-panel .table tbody tr:hover {
            background-color: #f7f8f6;
        }

        .admin-panel .table td {
            border-color: #e8ece8;
        }

        .admin-panel .btn {
            padding: 8px 14px;
            border-radius: 4px;
            font-weight: 500;
            transition: background-color .15s ease, border-color .15s ease;
        }

        .admin-panel .btn:hover,
        .admin-panel .btn:active {
            transform: none;
            box-shadow: none;
        }

        .admin-panel .btn-primary {
            color: #fff;
            background: var(--admin-green);
            border-color: var(--admin-green);
        }

        .admin-panel .btn-primary:hover,
        .admin-panel .btn-primary:focus {
            background: var(--admin-green-hover);
            border-color: var(--admin-green-hover);
        }

        .admin-panel .text-primary {
            color: #326c56 !important;
        }

        .admin-panel .form-control,
        .admin-panel .form-select {
            border-color: #cfd8d0;
            border-radius: 4px;
        }

        .admin-panel .form-control:focus,
        .admin-panel .form-select:focus {
            border-color: #6d9a83;
            box-shadow: 0 0 0 .2rem rgba(50, 108, 86, .13);
        }

        .admin-panel .badge {
            border-radius: 4px;
            font-weight: 600;
        }

        .admin-panel .admin-notice {
            border-radius: 4px;
            border-width: 1px;
            margin-bottom: 18px;
        }

        @media (max-width: 767.98px) {
            .admin-panel main {
                padding: 18px 14px;
            }

            .admin-panel .sidebar {
                min-height: 0;
            }

            .admin-panel .table thead th {
                white-space: nowrap;
            }
        }
    </style>
</head>
<body class="<?php echo $is_admin ? 'admin-panel' : ''; ?>">
    <?php 
// Load helper and calculate pending submissions for Hubin
$this->load->helper('view');
$pending_count = 0;
if($this->session->userdata('userdata')['level'] == 1){ // Hubin 
    $pending_count = $this->db->where('status_pengajuan', 'menunggu')->get('tb_siswa')->num_rows();
}
?>

<!-- Notification Banner for Hubin -->
<?php if($this->session->userdata('userdata')['level'] == 1 && $pending_count > 0): ?>
<div class="alert alert-warning alert-dismissible fade show text-center py-2 mb-0 rounded-0" role="alert">
    <?php if($is_admin): ?><span class="material-symbols-rounded hubin-icon" aria-hidden="true">pending_actions</span><?php else: ?><i class="fas fa-exclamation-triangle me-2"></i><?php endif; ?>
    <strong>Ada <?php echo $pending_count ?> pengajuan PKL menunggu!</strong> Silakan tinjau dan proses pengajuan dari siswa.
    <a href="<?php echo base_url('hubin/view/pengajuan') ?>" class="alert-link fw-bold ms-2">Lihat Pengajuan</a>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="margin-top: -0.25rem;"></button>
</div>
<?php endif; ?>

<!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php 
                $level = $this->session->userdata('userdata')['level'];
                if($level == 1) echo base_url('hubin/view');
                elseif($level == 2) echo base_url('guru/view');
                elseif($level == 3) echo base_url('siswa/view');
            ?>">
                <?php if($is_admin): ?><span class="material-symbols-rounded hubin-icon" aria-hidden="true">school</span><?php else: ?><i class="fas fa-graduation-cap me-2"></i><?php endif; ?><strong>Si</strong>PKL
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="<?php echo $is_admin ? '#adminSidebar' : '#navbarNav'; ?>" aria-label="Buka navigasi">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Menu removed as per user request -->
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav id="adminSidebar" class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky">
                    <div class="sidebar-profile text-center" style="cursor: pointer;" onclick="window.location.href='<?php 
                        $level = $this->session->userdata('userdata')['level'];
                        if($level == 1) echo base_url('hubin/view/profile');
                        elseif($level == 2) echo base_url('guru/view/profile');
                        elseif($level == 3) echo base_url('siswa/view/profile');
                    ?>'">
                        <?php 
                        $foto_profil = isset($this->session->userdata('userdata')['foto_profil']) ? $this->session->userdata('userdata')['foto_profil'] : null;
                        if($foto_profil && file_exists('./uploads/profil/'.$foto_profil)){ ?>
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center overflow-hidden" style="width: 70px; height: 70px; border: 3px solid rgba(255,255,255,0.3); transition: all 0.3s;" onmouseover="this.style.transform='scale(1.1)'; this.style.border='3px solid rgba(255,255,255,0.6)';" onmouseout="this.style.transform='scale(1)'; this.style.border='3px solid rgba(255,255,255,0.3)';">
                                <img src="<?php echo base_url('uploads/profil/'.$foto_profil) ?>" 
                                     alt="Foto Profil" 
                                     style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        <?php } else { ?>
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 70px; height: 70px; background: rgba(255,255,255,0.1); border: 3px solid rgba(255,255,255,0.3); transition: all 0.3s;" onmouseover="this.style.transform='scale(1.1)'; this.style.border='3px solid rgba(255,255,255,0.6)';" onmouseout="this.style.transform='scale(1)'; this.style.border='3px solid rgba(255,255,255,0.3)';">
                                <?php if($is_admin): ?><span class="material-symbols-rounded text-white" style="font-size: 34px;" aria-hidden="true">person</span><?php else: ?><i class="fas fa-user text-white fa-2x"></i><?php endif; ?>
                            </div>
                        <?php } ?>
                        <p class="text-white mt-3 mb-1 fw-bold" style="font-size: 1rem;"><?php echo $this->session->userdata('userdata')['nama_lengkap'] ?></p>
                        <small class="text-white-50 d-block mb-2">
                            <?php echo $this->session->userdata('userdata')['group_name']; ?>
                        </small>
                        
                    </div>
                    
                    <ul class="nav flex-column" style="padding: 0 10px;">
                        <li class="nav-item">
                            <a class="nav-link <?php echo (uri_string() == 'hubin/view' || uri_string() == 'guru/view' || uri_string() == 'hubin/view/home' || uri_string() == 'guru/view/home') ? 'active' : ''; ?>" 
                               href="<?php 
                                $level = $this->session->userdata('userdata')['level'];
                                if($level == 1) echo base_url('hubin/view');
                                elseif($level == 2) echo base_url('guru/view');
                                elseif($level == 3) echo base_url('siswa/view');
                            ?>">
                                <?php if($is_admin): ?><span class="material-symbols-rounded hubin-icon" aria-hidden="true">dashboard</span><?php else: ?><i class="fas fa-tachometer-alt me-2"></i><?php endif; ?> Dashboard
                            </a>
                        </li>

                        <?php if($this->session->userdata('userdata')['level'] == 1){ // Hubin ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos(uri_string(), 'daftar-siswa') !== false) ? 'active' : ''; ?>" 
                                href="<?php echo base_url('hubin/view/daftar-siswa') ?>">
                                <span class="material-symbols-rounded hubin-icon" aria-hidden="true">school</span> Data Siswa
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos(uri_string(), 'data-pembimbing') !== false) ? 'active' : ''; ?>" 
                                href="<?php echo base_url('hubin/view/data-pembimbing') ?>">
                                <span class="material-symbols-rounded hubin-icon" aria-hidden="true">co_present</span> Data Pembimbing
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos(uri_string(), 'assign-pembimbing') !== false) ? 'active' : ''; ?>" 
                                href="<?php echo base_url('hubin/view/assign-pembimbing') ?>">
                                <span class="material-symbols-rounded hubin-icon" aria-hidden="true">assignment_ind</span> Assign Pembimbing
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos(uri_string(), 'data-dudi') !== false) ? 'active' : ''; ?>" 
                                href="<?php echo base_url('hubin/view/data-dudi') ?>">
                                <span class="material-symbols-rounded hubin-icon" aria-hidden="true">business</span> Data DUDI
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos(uri_string(), 'pengajuan') !== false) ? 'active' : ''; ?>" 
                                href="<?php echo base_url('hubin/view/pengajuan') ?>">
                                <span class="material-symbols-rounded hubin-icon" aria-hidden="true">description</span> Pengajuan PKL
                                <?php if(isset($pending_count) && $pending_count > 0): ?>
                                    <span class="badge bg-warning text-dark ms-2" style="font-size: 0.7em;">
                                        <?php echo $pending_count ?>
                                    </span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <!-- Modul Monitoring PKL Hubin -->
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos(uri_string(), 'monitoring-presensi') !== false) ? 'active' : ''; ?>" 
                                href="<?php echo base_url('hubin/view/monitoring-presensi') ?>">
                                <span class="material-symbols-rounded hubin-icon" aria-hidden="true">fact_check</span> Monitoring Presensi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos(uri_string(), 'monitoring-logbook') !== false) ? 'active' : ''; ?>" 
                                href="<?php echo base_url('hubin/view/monitoring-logbook') ?>">
                                <span class="material-symbols-rounded hubin-icon" aria-hidden="true">menu_book</span> Monitoring Logbook
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos(uri_string(), 'laporan-monitoring') !== false) ? 'active' : ''; ?>" 
                                href="<?php echo base_url('hubin/view/laporan-monitoring') ?>">
                                <span class="material-symbols-rounded hubin-icon" aria-hidden="true">summarize</span> Laporan Monitoring
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos(uri_string(), 'pengumuman') !== false) ? 'active' : ''; ?>" 
                                href="<?php echo base_url('hubin/view/pengumuman') ?>">
                                <span class="material-symbols-rounded hubin-icon" aria-hidden="true">campaign</span> Informasi & Pengumuman
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo base_url('auth/logout') ?>" style="color: #f0c1b9;">
                                <span class="material-symbols-rounded hubin-icon hubin-icon-danger" aria-hidden="true">logout</span> Logout
                            </a>
                        </li>

                        <?php } elseif($this->session->userdata('userdata')['level'] == 2){ // Guru Pembimbing ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos(uri_string(), 'siswa-bimbingan') !== false) ? 'active' : ''; ?>" 
                                href="<?php echo base_url('guru/view/siswa-bimbingan') ?>">
                                <i class="fas fa-user-graduate me-2"></i> Siswa Bimbingan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos(uri_string(), 'monitoring-presensi') !== false) ? 'active' : ''; ?>" 
                                href="<?php echo base_url('guru/view/monitoring-presensi') ?>">
                                <i class="fas fa-map-location-dot me-2 text-info"></i> Monitoring Lokasi Siswa
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos(uri_string(), 'monitoring-logbook') !== false) ? 'active' : ''; ?>" 
                                href="<?php echo base_url('guru/view/monitoring-logbook') ?>">
                                <i class="fas fa-book-reader me-2 text-info"></i> Monitoring Logbook
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos(uri_string(), 'laporan-bimbingan') !== false) ? 'active' : ''; ?>" 
                                href="<?php echo base_url('guru/view/laporan-bimbingan') ?>">
                                <i class="fas fa-print me-2 text-info"></i> Rekapitulasi & Cetak
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos(uri_string(), 'profile') !== false) ? 'active' : ''; ?>" 
                                href="<?php echo base_url('guru/view/profile') ?>">
                                <i class="fas fa-user-circle me-2"></i> Profil Saya
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos(uri_string(), 'change-password') !== false) ? 'active' : ''; ?>" 
                                href="<?php echo base_url('guru/view/change-password') ?>">
                                <i class="fas fa-key me-2"></i> Ubah Password
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo base_url('auth/logout') ?>" style="color: #ff6b6b;">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </a>
                        </li>
                        <?php } ?>
                        

                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <?php foreach ($admin_notices as $notice): ?>
                    <div class="alert alert-<?php echo $notice['type']; ?> alert-dismissible fade show admin-notice" role="alert">
                        <i class="fas <?php echo $notice['type'] === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation'; ?> me-2" aria-hidden="true"></i>
                        <?php echo html_escape($notice['message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                    </div>
                <?php endforeach; ?>
                <?php $this->load->view($content);?>
            </main>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-light text-center py-3 mt-5">
        <div class="container">
            <p class="mb-0 text-muted">
                <strong>SiPKL</strong> v2.0 - Sistem Pendataan Siswa Prakerin SMK ITIKURIH HIBARNA
                <span class="ms-2">© <?php echo date('Y'); ?></span>
            </p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <!-- Custom JS -->
    <script>
        // Print function for targeted printing of content
        function printDiv(divName) {
            var printContents = document.getElementById(divName).innerHTML;
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;
            
            // Add print styles programmatically
            var style = document.createElement('style');
            style.type = 'text/css';
            style.innerHTML = `
                @page {
                    size: A4;
                    margin: 15mm;
                }
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 15mm;
                    background: white !important;
                    color: black !important;
                    font-size: 12pt;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 0;
                }
                td, th {
                    border: 1px solid #000;
                    padding: 8px;
                    background: white !important;
                    color: black !important;
                }
                .fw-bold {
                    font-weight: bold;
                }
            `;
            document.head.appendChild(style);
            
            window.print();
            
            document.body.innerHTML = originalContents;
            location.reload(); // Reload to restore original content
        }

        // Auto dismiss alerts after 5 seconds
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
        
        // Ensure no modals are stuck open with backdrops
        document.addEventListener('DOMContentLoaded', function() {
            // Close any open modals on page load
            const openModals = document.querySelectorAll('.modal.show');
            openModals.forEach(function(modal) {
                modal.classList.remove('show');
                modal.setAttribute('aria-hidden', 'true');
            });
            
            // Remove any lingering modal backdrops
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(function(backdrop) {
                backdrop.remove();
            });
            
            // Ensure body doesn't have modal-open class
            document.body.classList.remove('modal-open');
        });
        
        // Add click event listener to detect if clicks are being blocked
        document.addEventListener('click', function(e) {
            // Check if click is being prevented by overlays
            const overlays = document.querySelectorAll('.modal-backdrop, .loading-overlay, .preloader');
            if (overlays.length > 0) {
                console.log('Potential click-blocking elements detected:', overlays);
            }
            
            // Debug semua klik
            console.log('Click detected on:', e.target);
            console.log('Click path:', e.composedPath());
        }, true); // Use capture phase to detect events early
        
        // Ensure sidebar collapse works properly to prevent layout issues
        document.addEventListener('DOMContentLoaded', function() {
            // Make sure sidebar is properly initialized
            const sidebar = document.querySelector('.sidebar');
            if (sidebar) {
                // Ensure no unintended display:block is applied incorrectly
                sidebar.style.overflow = 'auto';
            }
            
            // Check for any elements that might be blocking clicks
            const body = document.body;
            body.style.pointerEvents = 'auto';
        });
        
        // Final check to ensure page is interactive
        window.addEventListener('load', function() {
            // Remove any potential loading classes
            document.body.classList.remove('loading');
            document.body.style.cursor = 'default';
        });
    </script>
    <!-- Floating Action Button -->
    <button type="button" class="btn-float btn btn-primary" onclick="scrollToTop()" style="z-index: 1020;">
        <i class="fas fa-arrow-up"></i>
    </button>
    
    <script>
    // Scroll to top function
    function scrollToTop() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }
    
    // Show/hide floating button based on scroll position
    window.addEventListener('scroll', function() {
        const floatBtn = document.querySelector('.btn-float');
        if (window.pageYOffset > 300) {
            floatBtn.style.opacity = '1';
            floatBtn.style.transform = 'scale(1)';
        } else {
            floatBtn.style.opacity = '0';
            floatBtn.style.transform = 'scale(0.8)';
        }
    });
    
    // Initialize button state
    document.addEventListener('DOMContentLoaded', function() {
        const floatBtn = document.querySelector('.btn-float');
        floatBtn.style.opacity = '0';
        floatBtn.style.transform = 'scale(0.8)';
        floatBtn.style.transition = 'all 0.3s ease';
    });
    </script>
</body>
</html>
