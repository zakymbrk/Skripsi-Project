<?php 
$userdata = $this->session->userdata('userdata');
?>

<style>
    .import-pembimbing-page { position: fixed; inset: 0; z-index: 1055; min-height: 100%; padding: 22px 10px 30px; overflow-y: auto; background: rgba(0,0,0,.78); }
    .import-pembimbing-page > nav,
    .import-pembimbing-page > .page-title { display: none; }
    .import-pembimbing-page .import-header { display: none; }
    .import-pembimbing-page > .import-form-card { max-width: 614px; margin: 0 auto 20px; border-radius: 6px; overflow: hidden; box-shadow: 0 8px 24px rgba(0,0,0,.22); }
    .import-pembimbing-page > .import-form-card .card-header { background: #12c4df !important; color: #fff !important; padding: 13px 14px; border-radius: 0 !important; }
    .import-pembimbing-page > .import-form-card .card-header h5 { font-size: 1rem; }
    .import-pembimbing-page > .import-form-card .card-header { display: flex; align-items: center; justify-content: space-between; }
    .import-pembimbing-page .dialog-close { color: rgba(255,255,255,.8); text-decoration: none; font-size: 1.4rem; line-height: 1; }
    .import-pembimbing-page > .import-form-card .card-body { padding: 16px 13px 13px; }
    .import-pembimbing-page > .import-form-card .alert-info,
    .import-pembimbing-page > .import-form-card .btn-secondary { display: none; }
    .import-pembimbing-page > .import-form-card .btn-primary { width: 100%; background: #222 !important; color: #fff; border-radius: 6px; }
    .import-pembimbing-page .download-template-button { width: 100%; margin-top: 6px; background: linear-gradient(90deg, #42b9e8, #4215b8); color: #fff; border-radius: 6px; }
    .import-pembimbing-page > .import-form-card ~ .card { display: none; }
    .import-pembimbing-page .alert { max-width: 614px; margin-left: auto; margin-right: auto; }
    .import-pembimbing-page .form-control-lg { font-size: .95rem; }
</style>

<div class="import-pembimbing-page">

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo base_url('hubin/view') ?>"><i class="fas fa-home"></i> Home</a></li>
        <li class="breadcrumb-item"><a href="<?php echo base_url('hubin/view/data-pembimbing') ?>">Data Pembimbing</a></li>
        <li class="breadcrumb-item active">Import Data Pembimbing</li>
    </ol>
</nav>

<!-- Page Header -->
<div class="page-title d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="fas fa-file-import me-2"></i>Import Data Pembimbing</h2>
    <a href="<?php echo base_url('hubin/view/data-pembimbing') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Kembali
    </a>
</div>

<?php if(!empty($this->session->flashdata('message'))){ ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <?php echo $this->session->flashdata('message'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php } ?>

<?php if(!empty($this->session->flashdata('error_message'))){ ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <?php echo $this->session->flashdata('error_message'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php } ?>

<?php if(!empty($this->session->flashdata('import_errors'))): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Detail Kesalahan Import:</h6>
        <ul class="mb-0">
            <?php foreach($this->session->flashdata('import_errors') as $err): ?>
                <li><?php echo htmlspecialchars($err); ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Import Form Card -->
<div class="card fade-in import-form-card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-upload me-2"></i>Import Data Pembimbing</h5>
        <a class="dialog-close" href="<?php echo base_url('hubin/view/data-pembimbing'); ?>" aria-label="Tutup">&times;</a>
    </div>
    <div class="card-body">
        <form action="<?php echo base_url('export_excel/import_daftar_pembimbing') ?>" method="post" enctype="multipart/form-data">
            <div class="mb-4">
                <label for="file_excel" class="form-label fw-bold">Pilih File Excel/CSV</label>
                <input type="file" class="form-control form-control-lg" id="file_excel" name="file_excel" accept=".xlsx,.xls,.csv" required>
                <div class="form-text">Format file yang didukung: .xlsx, .xls, atau .csv (Maksimal 5MB)</div>
            </div>
            
            <div class="alert alert-info">
                <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Ketentuan & Aturan Import Pembimbing:</h6>
                <ul class="mb-0">
                    <li>Gunakan template resmi yang telah disediakan agar struktur kolom terbaca sempurna.</li>
                    <li>Kolom yang <strong>wajib diisi</strong>: <strong>Nama Pembimbing</strong>, <strong>NIP</strong>, <strong>Username</strong>, dan <strong>Password</strong>.</li>
                    <li><strong>Username</strong> dan <strong>NIP</strong> harus unik (belum pernah terdaftar di sistem).</li>
                    <li>Password minimal 6 karakter (akan dienkripsi secara otomatis untuk akun login pembimbing).</li>
                    <li>Jenis Kelamin: isi <code>L</code> (Laki-laki) atau <code>P</code> (Perempuan).</li>
                    <li>Format tanggal lahir: <code>YYYY-MM-DD</code> (contoh: <code>1980-05-20</code>).</li>
                </ul>
            </div>
            
            <div class="d-grid gap-2 d-md-block">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-upload me-2"></i>Import Data
                </button>
                <button type="reset" class="btn btn-secondary btn-lg">
                    <i class="fas fa-redo me-2"></i>Reset
                </button>
                <a href="<?php echo base_url('export_excel/export_data_pembimbing_template') ?>" class="btn download-template-button btn-lg">
                    <i class="fas fa-download me-2"></i>Download Template Excel
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Template Download Card -->
<div class="card fade-in mt-4">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="fas fa-download me-2"></i>Download Template Import Pembimbing</h5>
    </div>
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h6><i class="fas fa-file-excel me-2"></i>Template File Excel (.xlsx)</h6>
                <p class="text-muted mb-2">Unduh template resmi berikut untuk memudahkan pengisian data guru pembimbing secara massal. Template sudah dilengkapi baris contoh dan petunjuk pengisian.</p>
                <p class="small text-muted mb-0"><i class="fas fa-lightbulb me-1 text-warning"></i>Tips: Pastikan baris contoh dihapus sebelum file diunggah ke sistem.</p>
            </div>
            <div class="col-md-4 text-center">
                <div class="card border-success shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-file-excel fa-3x text-success mb-3"></i>
                        <h6>Template Pembimbing</h6>
                        <a href="<?php echo base_url('export_excel/export_data_pembimbing_template') ?>" class="btn btn-success">
                            <i class="fas fa-download me-2"></i>Download Template (.xlsx)
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Format Details Card -->
<div class="card fade-in mt-4">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0"><i class="fas fa-table me-2"></i>Spesifikasi Kolom Template</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="5%">No</th>
                        <th width="18%">Nama Kolom</th>
                        <th width="12%">Status</th>
                        <th width="35%">Keterangan</th>
                        <th width="30%">Contoh Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td><strong>Nama Pembimbing</strong></td>
                        <td><span class="badge bg-danger">Wajib</span></td>
                        <td>Nama lengkap beserta gelar pembimbing</td>
                        <td>Drs. H. Maman Suryaman, M.Pd.</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td><strong>NIP</strong></td>
                        <td><span class="badge bg-danger">Wajib</span></td>
                        <td>Nomor Induk Pegawai (harus unik)</td>
                        <td>197508122000031001</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Jenis Kelamin</td>
                        <td><span class="badge bg-secondary">Opsional</span></td>
                        <td>L untuk Laki-laki, P untuk Perempuan</td>
                        <td>L atau P</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>Email</td>
                        <td><span class="badge bg-secondary">Opsional</span></td>
                        <td>Alamat email aktif pembimbing</td>
                        <td>maman@example.com</td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td>No Telepon</td>
                        <td><span class="badge bg-secondary">Opsional</span></td>
                        <td>Nomor WhatsApp / kontak aktif</td>
                        <td>081234567890</td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td>Alamat</td>
                        <td><span class="badge bg-secondary">Opsional</span></td>
                        <td>Alamat domisili lengkap</td>
                        <td>Jl. Raya Laswi No. 100, Ciparay</td>
                    </tr>
                    <tr>
                        <td>7</td>
                        <td>Tempat Lahir</td>
                        <td><span class="badge bg-secondary">Opsional</span></td>
                        <td>Kota/Kabupaten tempat lahir</td>
                        <td>Bandung</td>
                    </tr>
                    <tr>
                        <td>8</td>
                        <td>Tanggal Lahir</td>
                        <td><span class="badge bg-secondary">Opsional</span></td>
                        <td>Format standar: YYYY-MM-DD</td>
                        <td>1975-08-12</td>
                    </tr>
                    <tr>
                        <td>9</td>
                        <td>Pendidikan Terakhir</td>
                        <td><span class="badge bg-secondary">Opsional</span></td>
                        <td>Jenjang pendidikan terakhir</td>
                        <td>S2</td>
                    </tr>
                    <tr>
                        <td>10</td>
                        <td>Jabatan</td>
                        <td><span class="badge bg-secondary">Opsional</span></td>
                        <td>Jabatan fungsional / struktural</td>
                        <td>Guru Pembimbing</td>
                    </tr>
                    <tr>
                        <td>11</td>
                        <td>Jurusan Keahlian</td>
                        <td><span class="badge bg-secondary">Opsional</span></td>
                        <td>Bidang keahlian kompetensi</td>
                        <td>Rekayasa Perangkat Lunak</td>
                    </tr>
                    <tr>
                        <td>12</td>
                        <td>Tahun Masuk</td>
                        <td><span class="badge bg-secondary">Opsional</span></td>
                        <td>Tahun mulai bertugas</td>
                        <td>2010</td>
                    </tr>
                    <tr>
                        <td>13</td>
                        <td>Status Kepegawaian</td>
                        <td><span class="badge bg-secondary">Opsional</span></td>
                        <td>Status pegawai (PNS / GTY / Honorer)</td>
                        <td>PNS</td>
                    </tr>
                    <tr>
                        <td>14</td>
                        <td>Tempat Tugas</td>
                        <td><span class="badge bg-secondary">Opsional</span></td>
                        <td>Unit kerja / nama sekolah</td>
                        <td>SMK ITIKURIH HIBARNA</td>
                    </tr>
                    <tr>
                        <td>15</td>
                        <td><strong>Username</strong></td>
                        <td><span class="badge bg-danger">Wajib</span></td>
                        <td>Username untuk login Portal Pembimbing (unik)</td>
                        <td>pembimbing_maman</td>
                    </tr>
                    <tr>
                        <td>16</td>
                        <td><strong>Password</strong></td>
                        <td><span class="badge bg-danger">Wajib</span></td>
                        <td>Password akun login (minimal 6 karakter)</td>
                        <td>password123</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

</div>
