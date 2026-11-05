<?php 
$userdata = $this->session->userdata('userdata');
?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo base_url('hubin/view') ?>"><i class="fas fa-home"></i> Home</a></li>
        <li class="breadcrumb-item"><a href="<?php echo base_url('hubin/view/daftar-siswa') ?>">Data Siswa</a></li>
        <li class="breadcrumb-item active">Import Data Siswa</li>
    </ol>
</nav>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="fas fa-upload me-2"></i>Import Data Siswa</h2>
    <a href="<?php echo base_url('hubin/view/daftar-siswa') ?>" class="btn btn-outline-secondary">
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

<!-- Import Form Card -->
<div class="card fade-in">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0"><i class="fas fa-file-upload me-2"></i>Upload File Excel/CSV</h5>
    </div>
    <div class="card-body">
        <form action="<?php echo base_url('export_excel/import_daftar_siswa') ?>" method="post" enctype="multipart/form-data">
            <div class="mb-4">
                <label for="file_excel" class="form-label fw-bold">Pilih File Excel/XLSX/CSV</label>
                <input type="file" class="form-control form-control-lg" id="file_excel" name="file_excel" accept=".xlsx,.xls,.csv" required>
                <div class="form-text">Format file yang didukung: .xlsx, .xls, atau .csv (Max 5MB)</div>
            </div>
            
            <div class="alert alert-warning">
                <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Perhatian:</h6>
                <ul class="mb-0">
                    <li>Pastikan format file sesuai dengan template yang disediakan</li>
                    <li>Data wajib diisi: Nama Siswa*, Kelas*, Username*, Password*</li>
                    <li>Username, NIS, NISN, dan Email harus unik (tidak boleh sama dengan data yang sudah ada)</li>
                    <li>Password minimal 6 karakter</li>
                    <li>Format tanggal: YYYY-MM-DD (contoh: 2024-01-15)</li>
                    <li>Jenis Kelamin: L untuk Laki-laki, P untuk Perempuan</li>
                </ul>
            </div>
            
            <div class="d-grid gap-2 d-md-block">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-upload me-2"></i>Import Data
                </button>
                <button type="reset" class="btn btn-secondary btn-lg">
                    <i class="fas fa-redo me-2"></i>Reset
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Template Download Card -->
<div class="card fade-in mt-4">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="fas fa-download me-2"></i>Download Template</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <h6><i class="fas fa-file-excel me-2"></i>Template Import Siswa</h6>
                <p class="text-muted">Download template Excel yang dapat Anda edit untuk import data siswa. Template ini berisi struktur kolom yang benar dengan contoh data.</p>
                <ul class="mb-3">
                    <li><strong>Nama Siswa *</strong> - Wajib diisi</li>
                    <li>NIS - Nomor Induk Siswa (opsional)</li>
                    <li>NISN - Nomor Induk Siswa Nasional (opsional)</li>
                    <li>Jenis Kelamin - L atau P (opsional)</li>
                    <li><strong>Kelas *</strong> - Wajib diisi</li>
                    <li>Jurusan (opsional)</li>
                    <li><strong>Username *</strong> - Harus unik, wajib diisi</li>
                    <li><strong>Password *</strong> - Wajib diisi</li>
                </ul>
            </div>
            <div class="col-md-4 text-center">
                <div class="card border-success">
                    <div class="card-body">
                        <i class="fas fa-file-excel fa-3x text-success mb-3"></i>
                        <h6>Template XLSX</h6>
                        <a href="<?php echo base_url('export_excel/export_data_siswa_template') ?>" class="btn btn-success">
                            <i class="fas fa-download me-2"></i>Download
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Format Details Card -->
<div class="card fade-in mt-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-table me-2"></i>Detail Format Kolom</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="5%">No</th>
                        <th width="20%">Nama Kolom</th>
                        <th width="15%">Wajib Diisi</th>
                        <th width="30%">Keterangan</th>
                        <th width="30%">Contoh</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td><strong>Nama Siswa</strong></td>
                        <td><span class="badge bg-danger">Wajib</span></td>
                        <td>Nama lengkap siswa</td>
                        <td>Andi Setiawan</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>NIS</td>
                        <td><span class="badge bg-secondary">Opsional</span></td>
                        <td>Nomor Induk Siswa</td>
                        <td>2024001</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>NISN</td>
                        <td><span class="badge bg-secondary">Opsional</span></td>
                        <td>Nomor Induk Siswa Nasional</td>
                        <td>0001234567890</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>Jenis Kelamin</td>
                        <td><span class="badge bg-secondary">Opsional</span></td>
                        <td>L untuk Laki-laki, P untuk Perempuan</td>
                        <td>L atau P</td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td><strong>Kelas</strong></td>
                        <td><span class="badge bg-danger">Wajib</span></td>
                        <td>Kelas siswa saat ini</td>
                        <td>XI TKJ 1</td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td>Jurusan</td>
                        <td><span class="badge bg-secondary">Opsional</span></td>
                        <td>Jurusan siswa</td>
                        <td>Teknik Komputer dan Jaringan</td>
                    </tr>
                    <tr>
                        <td>7</td>
                        <td><strong>Username</strong></td>
                        <td><span class="badge bg-danger">Wajib</span></td>
                        <td>Username untuk login (harus unik)</td>
                        <td>andi_tkj</td>
                    </tr>
                    <tr>
                        <td>8</td>
                        <td><strong>Password</strong></td>
                        <td><span class="badge bg-danger">Wajib</span></td>
                        <td>Password untuk login siswa</td>
                        <td>password123</td>
                    </tr>
            </table>
        </div>
    </div>
</div>
