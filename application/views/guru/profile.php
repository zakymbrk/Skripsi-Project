<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo base_url('guru/view') ?>"><i class="fas fa-home"></i> Home</a></li>
        <li class="breadcrumb-item active">Profile Guru</li>
    </ol>
</nav>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0 fs-4 fw-bold"><i class="fas fa-user-circle me-2 text-primary"></i>Profil Guru Pembimbing</h2>
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

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card border-0 rounded-4 shadow-sm text-center p-4">
            <div class="mb-3 mx-auto rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary" style="width: 100px; height: 100px;">
                <i class="fas fa-user-tie fa-3x"></i>
            </div>
            <h5 class="fw-bold mb-1"><?php echo $user->nama_lengkap ?></h5>
            <p class="text-muted small mb-2"><?php echo $user->group_name ?></p>
            <span class="badge bg-success px-3 py-1 rounded-pill mx-auto">
                <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i>Aktif
            </span>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card border-0 rounded-4 shadow-sm p-4">
            <h5 class="fw-bold mb-3"><i class="fas fa-edit me-2 text-primary"></i>Edit Biodata Pembimbing</h5>
            <form action="<?php echo base_url('guru/update_profile'); ?>" method="post">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Nama Lengkap</label>
                        <input type="text" class="form-control" name="nama_lengkap" value="<?php echo htmlspecialchars($user->nama_lengkap); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Username Login</label>
                        <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($user->username); ?>" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Nomor Telepon / WhatsApp</label>
                        <input type="text" class="form-control" name="telepon" value="<?php echo htmlspecialchars($user->telepon ?: ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Email</label>
                        <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user->email ?: ''); ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-semibold">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="3"><?php echo htmlspecialchars($user->alamat ?: ''); ?></textarea>
                    </div>
                    <div class="col-12 text-end mt-4">
                        <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
                            <i class="fas fa-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
