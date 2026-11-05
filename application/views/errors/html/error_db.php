<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>Layanan Sedang Terganggu - SiPKL</title>
<style>
    * { box-sizing: border-box; }
    body { min-height: 100vh; margin: 0; display: grid; place-items: center; padding: 24px; background: #f2f4f0; color: #263b33; font: 16px/1.6 'Segoe UI', Arial, sans-serif; }
    main { width: min(100%, 520px); padding: 32px; background: #fff; border: 1px solid #dfe5df; border-top: 4px solid #c7953e; }
    h1 { margin: 0 0 12px; font-size: 1.35rem; }
    p { margin: 0 0 12px; color: #52645a; }
    a { display: inline-block; margin-top: 8px; padding: 9px 15px; background: #24483b; color: #fff; text-decoration: none; border-radius: 4px; }
</style>
</head>
<body>
    <main role="alert">
        <h1>Layanan sedang mengalami gangguan</h1>
        <p>Permintaan Anda belum dapat diproses. Silakan coba kembali beberapa saat lagi.</p>
        <p>Jika masalah berlanjut, hubungi administrator sekolah.</p>
        <a href="<?php echo function_exists('base_url') ? base_url() : '/'; ?>">Kembali ke halaman utama</a>
    </main>
</body>
</html>