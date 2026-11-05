<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo base_url('guru/view') ?>"><i class="fas fa-home"></i> Home</a></li>
        <li class="breadcrumb-item active">Ubah Password</li>
    </ol>
</nav>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0 fs-4 fw-bold"><i class="fas fa-key me-2 text-primary"></i>Ubah Kata Sandi</h2>
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

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 rounded-4 shadow-sm p-4">
            <form action="<?php echo base_url('guru/change_password'); ?>" method="post">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Password Saat Ini (Lama) <span class="text-danger">*</span></label>
                    <input type="password" name="password_lama" class="form-control" placeholder="Masukkan password lama Anda..." required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Password Baru <span class="text-danger">*</span></label>
                    <input type="password" name="password_baru" class="form-control" placeholder="Minimal 6 karakter..." required minlength="6">
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-semibold">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                    <input type="password" name="password_konfirmasi" class="form-control" placeholder="Ulangi password baru..." required minlength="6">
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
                        <i class="fas fa-lock me-1"></i> Perbarui Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
