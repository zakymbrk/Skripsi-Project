<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Guru Pembimbing - SiPKL</title>
    <link rel="icon" type="image/png" href="<?php echo base_url('assets/img/logo-sekolah.png'); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.85) 0%, rgba(234, 88, 12, 0.85) 100%),
                        url('<?php echo base_url('assets/img/sekolah.jpg') ?>') center center no-repeat;
            background-size: cover;
            padding: 20px;
        }
        .login-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 450px;
            width: 100%;
        }
        .logo-sekolah {
            width: 100px;
            height: 100px;
            margin: 0 auto 20px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            padding: 10px;
        }
        .logo-sekolah img { width: 100%; height: 100%; object-fit: contain; border-radius: 50%; }
        .login-title h2 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #ea580c;
            text-align: center;
        }
        .login-title p { text-align: center; color: #666; font-size: 0.9rem; }
        .form-label { font-weight: 500; color: #333; }
        .form-label i { color: #ea580c; margin-right: 6px; }
        .form-control { border-radius: 10px; padding: 12px 15px; }
        .form-control:focus { border-color: #ea580c; box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.1); }
        .btn-login {
            width: 100%;
            padding: 14px;
            border-radius: 10px;
            font-weight: 600;
            border: none;
            background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%);
            color: white;
        }
        .btn-login:hover { color: white; transform: translateY(-1px); }
        .info-box {
            background: #fff7ed;
            border-radius: 12px;
            padding: 16px;
            margin-top: 20px;
            border-left: 4px solid #ea580c;
            font-size: 0.85rem;
            color: #666;
        }
        .back-to-landing { text-align: center; margin-top: 16px; }
        .back-to-landing a { color: #ea580c; text-decoration: none; font-weight: 500; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="logo-sekolah">
            <img src="<?php echo base_url('assets/img/logo-sekolah.png') ?>" alt="Logo" onerror="this.style.display='none';">
        </div>
        <div class="login-title">
            <h2><i class="fas fa-chalkboard-teacher me-2"></i>Portal Pembimbing</h2>
            <p>Monitoring Presensi & Logbook PKL</p>
            <p class="text-muted small">SMK ITIKURIH HIBARNA CIPARAY</p>
        </div>

        <?php if(validation_errors()): ?>
        <div class="alert alert-danger"><?php echo validation_errors(); ?></div>
        <?php endif; ?>

        <?php if(!empty($this->session->flashdata('message'))): ?>
        <div class="alert alert-warning"><?php echo $this->session->flashdata('message'); ?></div>
        <?php endif; ?>

        <form action="<?php echo base_url('auth/do_login_pembimbing'); ?>" method="post">
            <div class="mb-3">
                <label for="username" class="form-label"><i class="fas fa-user"></i>Username</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Username pembimbing" required autofocus>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label"><i class="fas fa-lock"></i>Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-login">
                <i class="fas fa-sign-in-alt me-2"></i>Masuk sebagai Pembimbing
            </button>
        </form>

        <div class="info-box">
            Portal ini digunakan guru pembimbing untuk memantau kehadiran, logbook, dan evaluasi siswa bimbingan PKL.
        </div>

        <div class="back-to-landing">
            <a href="<?php echo base_url(); ?>"><i class="fas fa-arrow-left me-1"></i> Kembali ke Beranda</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
