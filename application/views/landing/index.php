<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Pendataan Siswa Prakerin - SiPKL SMK ITIKURIH HIBARNA</title>
    <link rel="icon" type="image/png" href="<?php echo base_url('assets/img/logo.png') ?>">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4cc9f0;
            --light-color:rgb(183, 187, 192);
            --dark-color: #212529;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
            background-color: #fafafa;
        }
        
        /* Navbar */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
            padding: 1rem 0;
            transition: all 0.3s;
        }
        
        .navbar.scrolled {
            padding: 0.5rem 0;
        }
        
        .navbar-brand {
            font-weight: 800;
            font-size: 1.8rem;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .nav-link {
            font-weight: 500;
            color: #333 !important;
            margin: 0 0.5rem;
            transition: all 0.3s;
            position: relative;
        }
        
        .nav-link:hover {
            color: var(--primary-color) !important;
        }
        
        .nav-link:hover::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--primary-color);
        }
        
        /* Hero Section */
        .hero-section {
            min-height: 100vh;
            background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            padding: 100px 0;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }
        
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .hero-content {
            position: relative;
            z-index: 1;
            color: white;
        }
        
        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            line-height: 1.2;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        
        .hero-subtitle {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            opacity: 0.95;
            line-height: 1.6;
        }
        
        .btn-hero {
            padding: 1rem 2.5rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            border: none;
            transition: all 0.3s;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            margin: 0.5rem;
        }
        
        .btn-hero:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
        }
        
        .btn-primary-hero {
            background: white;
            color: var(--primary-color);
        }
        
        .btn-outline-hero {
            background: transparent;
            color: white;
            border: 2px solid white;
        }
        
        /* Statistics Section */
        .stats-section {
            padding: 100px 0;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }
        
        .section-title {
            font-size: 2.5rem;
            font-weight: 800;
            text-align: center;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .section-subtitle {
            text-align: center;
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 60px;
        }
        
        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s;
            border: none;
            height: 100%;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
        }
        
        .stat-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.15);
        }
        
        .stat-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2rem;
            color: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .stat-icon.primary { background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%); }
        .stat-icon.success { background: linear-gradient(135deg, #2ec4b6 0%, #1a936f 100%); }
        .stat-icon.warning { background: linear-gradient(135deg, #ff9f1c 0%, #e76f51 100%); }
        .stat-icon.info { background: linear-gradient(135deg, #4cc9f0 0%, #4895ef 100%); }
        
        .stat-number {
            font-size: 3rem;
            font-weight: 800;
            color: #333;
            margin-bottom: 10px;
        }
        
        .stat-label {
            font-size: 1.1rem;
            color: #666;
            font-weight: 500;
        }
        
        .stat-desc {
            font-size: 0.9rem;
            color: #999;
            margin-top: 8px;
            font-style: italic;
        }
        
        .feature-title {
            font-weight: 700;
            color: #333;
        }
        
        .feature-desc {
            color: #666;
            line-height: 1.6;
        }
        
        /* FAQ Section */
        .faq-section {
            padding: 100px 0;
            background: white;
        }
        
        .accordion-item {
            border: none;
            margin-bottom: 20px;
            border-radius: 15px !important;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .accordion-button {
            background: white;
            font-weight: 600;
            color: #333;
            border: none;
            padding: 20px 25px;
        }
        
        .accordion-body ul, .accordion-body ol {
            padding-left: 20px;
            margin-bottom: 0;
        }
        
        .accordion-body li {
            margin-bottom: 5px;
        }
        
        .accordion-button:not(.collapsed) {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
        }
        
        .accordion-button:focus {
            box-shadow: none;
        }
        
        /* Contact Section */
        .contact-section {
            padding: 100px 0;
            background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
            color: white;
        }
        
        .contact-card {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 50px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .contact-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            font-size: 2rem;
        }
        
        .contact-info {
            text-align: left;
            margin-bottom: 20px;
        }
        
        .contact-info i {
            color: var(--accent-color);
            margin-right: 15px;
            width: 20px;
        }
        
        /* Footer */
        .footer {
            background: #1a1a1a;
            color: white;
            padding: 60px 0 30px;
        }
        
        .footer-title {
            font-weight: 700;
            margin-bottom: 20px;
            font-size: 1.3rem;
            position: relative;
            padding-bottom: 10px;
        }
        
        .footer-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background: var(--accent-color);
        }
        
        .footer-link {
            color: #ccc;
            text-decoration: none;
            display: block;
            margin-bottom: 10px;
            transition: all 0.3s;
        }
        
        .footer-link:hover {
            color: white;
            padding-left: 5px;
        }
        
        .social-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 1.2rem;
            transition: all 0.3s;
            text-decoration: none;
        }
        
        .social-icon:hover {
            background: var(--accent-color);
            transform: translateY(-3px);
        }
        
        .footer-bottom {
            border-top: 1px solid #333;
            padding-top: 30px;
            margin-top: 40px;
            text-align: center;
            color: #999;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1.1rem;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .btn-hero {
                display: block;
                width: 80%;
                margin: 10px auto;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top" id="navbar">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-graduation-cap me-2"></i><strong>Si</strong>PKL
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#beranda">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#fitur">Fitur</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#statistik">Statistik</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#faq">FAQ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#kontak">Kontak</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-primary-hero btn-sm px-3 py-2 ms-2" href="<?php echo base_url('login/siswa') ?>">
                            <i class="fas fa-sign-in-alt me-1"></i>Login
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section" id="beranda">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content" data-aos="fade-right">
                    <h1 class="hero-title">Manajemen Praktik Kerja Lapangan Kini Lebih Mudah dan Terintegrasi</h1>
                    <p class="hero-subtitle">
                        SIPKL SMK ITIKUTIH HIBARNA hadir untuk menyerderhanakan administrasi PKL, mulai dari pengajuan hingga evaluasi.
                    </p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="<?php echo base_url('login/siswa') ?>" class="btn btn-hero btn-primary-hero">
                            <i class="fas fa-sign-in-alt me-2"></i>Masuk ke Sistem
                        </a>
                        <a href="#statistik" class="btn btn-hero btn-outline-hero">
                            <i class="fas fa-chart-line me-2"></i>Lihat Statistik
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 text-center" data-aos="fade-left">
                    <div class="mt-5 mt-lg-0">
                        <img src="<?php echo base_url('assets/img/logo-sekolah.png') ?>" 
                             alt="Logo SMK ITIKURIH HIBARNA" 
                             style="max-width: 100%; height: auto; max-height: 400px; filter: drop-shadow(0 0 20px rgba(255,255,255,0.5));"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                        <i class="fas fa-school" style="font-size: 20rem; opacity: 0.3; color: white; display: none;"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Statistics Section -->
    <section class="stats-section" id="statistik">
        <div class="container">
            <div class="row mb-5">
                <div class="col-12 text-center">
                    <h2 class="section-title">Statistik</h2>
                    <p class="section-subtitle">Data terkini sistem SiPKL SMK ITIKURIH HIBARNA</p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="100">
                    <div class="stat-card">
                        <div class="stat-icon primary">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($total_siswa); ?>+</div>
                        <div class="stat-label">Total Siswa</div>
                        <div class="stat-desc">Siswa terdaftar dalam sistem</div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="200">
                    <div class="stat-card">
                        <div class="stat-icon success">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($total_pembimbing); ?>+</div>
                        <div class="stat-label">Guru Pembimbing</div>
                        <div class="stat-desc">Pembimbing aktif PKL</div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
                    <div class="stat-card">
                        <div class="stat-icon warning">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($siswa_prakerin); ?>+</div>
                        <div class="stat-label">Siswa PKL Aktif</div>
                        <div class="stat-desc">Sedang menjalani PKL</div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="400">
                    <div class="stat-card">
                        <div class="stat-icon info">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($this->db->get('tb_dudi')->num_rows() ?: 0); ?>+</div>
                        <div class="stat-label">Mitra Industri</div>
                        <div class="stat-desc">Perusahaan mitra PKL</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pengumuman Section -->
    <?php if(isset($pengumuman) && $pengumuman){ ?>
    <section class="pengumuman-section" id="pengumuman" style="padding: 80px 0; background: white;">
        <div class="container">
            <div class="row mb-5">
                <div class="col-12 text-center">
                    <h2 class="section-title">Pengumuman Terkini</h2>
                    <p class="section-subtitle">Informasi dan pengumuman terbaru dari SMK ITIKURIH HIBARNA</p>
                </div>
            </div>
            <div class="row g-4 justify-content-center">
                <?php if(isset($pengumuman) && is_array($pengumuman) && !empty($pengumuman)): ?>
                    <?php foreach($pengumuman as $p): ?>
                    <div class="col-md-6 col-lg-4" data-aos="fade-up">
                        <div class="stat-card">
                            <div class="stat-icon info" style="width: 60px; height: 60px; font-size: 1.5rem; margin-bottom: 20px;">
                                <i class="fas fa-bullhorn"></i>
                            </div>
                            <h3 class="feature-title" style="font-size: 1.2rem;"><?php echo isset($p->judul) ? $p->judul : 'Pengumuman'; ?></h3>
                            <p class="feature-desc" style="font-size: 0.9rem; margin-bottom: 10px;">
                                <?php echo isset($p->created_at) ? date('d F Y', strtotime($p->created_at)) : date('d F Y'); ?>
                            </p>
                            <p class="feature-desc">
                                <?php echo isset($p->isi) ? substr($p->isi, 0, 100) . '...' : 'Tidak ada deskripsi'; ?>
                            </p>
                            <div class="mt-3">
                                <a href="<?php echo base_url('home/detail_pengumuman/' . (isset($p->pengumuman_id) ? $p->pengumuman_id : '1')); ?>" 
                                   class="btn btn-primary btn-sm w-100">
                                    <i class="fas fa-eye me-1"></i>Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <div class="stat-card">
                            <div class="stat-icon info" style="width: 60px; height: 60px; font-size: 1.5rem; margin-bottom: 20px;">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <h3 class="feature-title">Belum Ada Pengumuman</h3>
                            <p class="feature-desc">Saat ini belum ada pengumuman yang tersedia.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php } ?>

    <!-- FAQ Section -->
    <section class="faq-section" id="faq">
        <div class="container">
            <div class="row mb-5">
                <div class="col-12 text-center">
                    <h2 class="section-title">Pertanyaan yang Sering Diajukan</h2>
                    <p class="section-subtitle">Temukan jawaban untuk pertanyaan umum tentang SiPKL</p>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item" data-aos="fade-up">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    Apa itu SiPKL SMK ITIKURIH HIBARNA?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    SiPKL SMK ITIKURIH HIBARNA adalah sistem informasi digital yang dirancang untuk mengelola seluruh proses Praktik Kerja Lapangan secara terintegrasi. Landing page berfungsi sebagai halaman publik yang memperkenalkan sistem kepada pengguna sebelum memasuki area aplikasi internal, tanpa memerlukan autentikasi.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item" data-aos="fade-up" data-aos-delay="100">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    Apa saja fitur utama yang ada di SiPKL?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Fitur utama SiPKL meliputi:
                                    <ul class="mb-0">
                                        <li>Manajemen Data User dan Siswa</li>
                                        <li>Dashboard dengan Statistik Real-time</li>
                                        <li>Export ke Excel dan Print Laporan</li>
                                        <li>Pencarian dan Filter Data</li>
                                        <li>Pagination untuk Data Besar</li>
                                        <li>Profile Management</li>
                                        <li>Multi Level User (Hubin, Pembimbing & Siswa)</li>
                                        <li>Manajemen Pengajuan PKL</li>
                                        <li>Manajemen Penempatan</li>
                                        <li>QR Code untuk Verifikasi</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item" data-aos="fade-up" data-aos-delay="200">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    Siapa saja yang dapat menggunakan SiPKL?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    SiPKL dapat digunakan oleh tiga level user:
                                    <ul class="mb-0">
                                        <li><strong>Siswa</strong>: Mengajukan PKL, mengelola data pribadi, dan melihat status pengajuan</li>
                                        <li><strong>Pembimbing</strong>: Memberikan bimbingan kepada siswa</li>
                                        <li><strong>Hubin</strong>: Mengelola seluruh administrasi PKL, penempatan siswa, dan koordinasi dengan industri</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item" data-aos="fade-up" data-aos-delay="300">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                    Bagaimana cara login ke sistem SiPKL?
                                </button>
                            </h2>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <ol class="mb-0">
                                        <li>Kunjungi halaman beranda SiPKL</li>
                                        <li>Klik tombol "Masuk ke Sistem" di navbar atau hero section</li>
                                        <li>Masukkan username dan password yang telah diberikan oleh administrator</li>
                                        <li>Pilih level user sesuai dengan peran Anda (Siswa/Pembimbing/Hubin)</li>
                                        <li>Klik login untuk mengakses dashboard</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item" data-aos="fade-up" data-aos-delay="400">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                                    Apakah data yang diinput aman?
                                </button>
                            </h2>
                            <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Ya, sistem menggunakan berbagai lapisan keamanan:
                                    <ul class="mb-0">
                                        <li>Autentikasi berbasis session yang aman</li>
                                        <li>Password di-hash dengan algoritma bcrypt</li>
                                        <li>Validasi input data untuk mencegah SQL injection</li>
                                        <li>Authorization berdasarkan level user</li>
                                        <li>Backup data berkala untuk keamanan</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item" data-aos="fade-up" data-aos-delay="500">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq6">
                                    Bagaimana sistem membantu proses PKL?
                                </button>
                            </h2>
                            <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Sistem membantu dengan:
                                    <ul class="mb-0">
                                        <li>Otomatisasi proses pengajuan dan penempatan</li>
                                        <li>Monitoring real-time progres siswa</li>
                                        <li>Dokumentasi digital semua aktivitas PKL</li>
                                        <li>Evaluasi terstruktur dan transparan</li>
                                        <li>Koordinasi yang lebih efektif antar stakeholder</li>
                                        <li>Laporan otomatis dalam format Excel/PDF</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item" data-aos="fade-up" data-aos-delay="600">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq7">
                                    Apa fungsi dari landing page SiPKL?
                                </button>
                            </h2>
                            <div id="faq7" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Landing page berfungsi sebagai halaman publik yang memperkenalkan sistem SiPKL kepada pengguna sebelum memasuki area aplikasi internal. Halaman ini bersifat informatif dan tidak memerlukan autentikasi, peran nya tidak hanya sebagai gerbang awal, tetapi juga sebagai media penyampaian profil yayasan dan gambaran umum sistem.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section" id="kontak">
        <div class="container">
            <div class="row mb-5">
                <div class="col-12 text-center">
                    <h2 class="section-title" style="color: white !important; -webkit-text-fill-color: white !important;">Kontak</h2>
                    <p class="section-subtitle" style="color: white !important;">Kami siap membantu Anda!</p>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="contact-card" data-aos="zoom-in">
                        <div class="contact-icon">
                            <i class="fas fa-school"></i>
                        </div>
                        <h3 class="mb-4" style="color: white;">SMK ITIKURIH HIBARNA</h3>
                        <p class="mb-4" style="color: white;">Sistem Pendataan Siswa Prakerin</p>
                        <div class="row text-start">
                            <div class="col-md-6 mb-3">
                                <div class="contact-info">
                                    <p style="color: white;"><i class="fas fa-map-marker-alt me-2"></i> <strong>Alamat:</strong><br>
                                    Jalan Raya Laswi No. 782 Ciparay<br>Kab. Bandung [40381]</p>
                                </div>
                                <div class="contact-info">
                                    <p style="color: white;"><i class="fas fa-phone me-2"></i> <strong>Telpon:</strong><br>
                                    022-5957900</p>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="contact-info">
                                    <p style="color: white;"><i class="fas fa-envelope me-2"></i> <strong>Email:</strong><br>
                                    smk@itikurih-hibarna.sch.id</p>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-4">
                            <a href="<?php echo base_url('login/siswa') ?>" class="btn btn-hero btn-primary-hero">
                                <i class="fas fa-sign-in-alt me-2"></i>Masuk ke Sistem
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 class="footer-title" style="color: white !important;">
                        <i class="fas fa-graduation-cap me-2"></i>SiPKL
                    </h5>
                    <p class="text-muted" style="color: white !important;">
                        Sistem Pendataan Siswa Prakerin SMK ITIKURIH HIBARNA - Manajemen PKL yang lebih mudah dan terintegrasi.
                    </p>
                    <div class="mt-3">
                        <a href="https://www.instagram.com/smk_itikurih_hibarna" target="_blank" class="social-icon me-3">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="https://www.facebook.com/smk.itikurih.hibarna" target="_blank" class="social-icon me-3">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <a href="https://www.youtube.com/@smkitikurihhibarna" target="_blank" class="social-icon">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <h5 class="footer-title" style="color: white;">Link Cepat</h5>
                    <a href="#beranda" class="footer-link" style="color: white;">Beranda</a>
                    <a href="#fitur" class="footer-link" style="color: white;">Fitur</a>
                    <a href="#statistik" class="footer-link" style="color: white;">Statistik</a>
                    <a href="#faq" class="footer-link" style="color: white;">FAQ</a>
                    <a href="#kontak" class="footer-link" style="color: white;">Kontak</a>
                </div>
                <div class="col-md-4 mb-4">
                    <h5 class="footer-title" style="color: white !important;">Tautan Penting</h5>
                    <a href="<?php echo base_url('login/siswa'); ?>" class="footer-link" style="color: white !important;">Login Siswa</a>
                    <a href="<?php echo base_url('login/pembimbing'); ?>" class="footer-link" style="color: white !important;">Login Pembimbing</a>
                    <a href="<?php echo base_url('login/hubin'); ?>" class="footer-link" style="color: white !important;">Login Hubin</a>
                    <a href="https://smk.itikurih-hibarna.sch.id/" target="_blank" class="footer-link" style="color: white !important;">Website Sekolah</a>
                    <a href="mailto:smk@itikurih-hibarna.sch.id" class="footer-link" style="color: white !important;">Email Sekolah</a>
                    <a href="tel:0225957900" class="footer-link" style="color: white !important;">Telepon Sekolah</a>
                </div>
            </div>
            <div class="footer-bottom">
                <p class="mb-0">
                    © <?php echo date('Y'); ?> <strong>SMK ITIKURIH HIBARNA</strong> | SiPKL v2.0 - All Rights Reserved
                </p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AOS Animation -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true
        });
        
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
        
        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>