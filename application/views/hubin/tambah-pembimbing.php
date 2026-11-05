<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo base_url('hubin/view') ?>"><i class="fas fa-home"></i> Home</a></li>
        <li class="breadcrumb-item"><a href="<?php echo base_url('hubin/view/data-pembimbing') ?>">Data Pembimbing</a></li>
        <li class="breadcrumb-item active">Tambah Pembimbing</li>
    </ol>
</nav>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="fas fa-user-tie me-2"></i>Tambah Pembimbing</h2>
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

<?php if(validation_errors()){ ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <?php echo validation_errors(); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php } ?>

<!-- Card Form Tambah Pembimbing -->
<div class="card fade-in">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-plus me-2"></i>Tambah Data Pembimbing & Akun Login
        </h5>
    </div>
    <div class="card-body">
        <form action="<?php echo site_url('hubin/tambah_pembimbing') ?>" method="post">
            <div class="row">
                <!-- Kolom Kiri: Biodata & Data Kepegawaian -->
                <div class="col-lg-7">
                    <h5 class="mb-3 text-primary border-bottom pb-2">
                        <i class="fas fa-id-card me-2"></i>Data Pribadi & Kepegawaian
                    </h5>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Pembimbing <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="pembimbing_nama" 
                               value="<?php echo set_value('pembimbing_nama') ?>" placeholder="Nama lengkap beserta gelar" required>
                        <?php echo form_error('pembimbing_nama', '<small class="text-danger">', '</small>'); ?>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">NIP <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="pembimbing_nip" 
                                   value="<?php echo set_value('pembimbing_nip') ?>" placeholder="Nomor Induk Pegawai" required>
                            <?php echo form_error('pembimbing_nip', '<small class="text-danger">', '</small>'); ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tempat Tugas</label>
                            <select class="form-select" name="tempat_tugas">
                                <option value="">-- Pilih Tempat Tugas --</option>
                                <option value="TKJ" <?php echo set_value('tempat_tugas') == 'TKJ' ? 'selected' : '' ?>>Teknik Komputer Jaringan</option>
                                <option value="Perbankan" <?php echo set_value('tempat_tugas') == 'Perbankan' ? 'selected' : '' ?>>Perbankan</option>
                            </select>
                            <?php echo form_error('tempat_tugas', '<small class="text-danger">', '</small>'); ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tempat Lahir</label>
                            <input type="text" class="form-control" name="tempat_lahir" 
                                   value="<?php echo set_value('tempat_lahir') ?>" placeholder="Contoh: Jakarta">
                            <?php echo form_error('tempat_lahir', '<small class="text-danger">', '</small>'); ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tanggal Lahir</label>
                            <input type="date" class="form-control" name="tanggal_lahir" 
                                   value="<?php echo set_value('tanggal_lahir') ?>">
                            <?php echo form_error('tanggal_lahir', '<small class="text-danger">', '</small>'); ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Jenis Kelamin</label>
                            <select class="form-select" name="jenis_kelamin">
                                <option value="">-- Pilih Jenis Kelamin --</option>
                                <option value="Laki-laki" <?php echo set_value('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                                <option value="Perempuan" <?php echo set_value('jenis_kelamin') == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                            </select>
                            <?php echo form_error('jenis_kelamin', '<small class="text-danger">', '</small>'); ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Pendidikan Terakhir</label>
                            <select class="form-select" name="pendidikan_terakhir">
                                <option value="">-- Pilih Pendidikan --</option>
                                <option value="SMA/SMK" <?php echo set_value('pendidikan_terakhir') == 'SMA/SMK' ? 'selected' : '' ?>>SMA/SMK</option>
                                <option value="D3" <?php echo set_value('pendidikan_terakhir') == 'D3' ? 'selected' : '' ?>>D3</option>
                                <option value="S1" <?php echo set_value('pendidikan_terakhir') == 'S1' ? 'selected' : '' ?>>S1</option>
                                <option value="S2" <?php echo set_value('pendidikan_terakhir') == 'S2' ? 'selected' : '' ?>>S2</option>
                                <option value="S3" <?php echo set_value('pendidikan_terakhir') == 'S3' ? 'selected' : '' ?>>S3</option>
                            </select>
                            <?php echo form_error('pendidikan_terakhir', '<small class="text-danger">', '</small>'); ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Jurusan Keahlian</label>
                            <input type="text" class="form-control" name="jurusan_keahlian" 
                                   value="<?php echo set_value('jurusan_keahlian') ?>" placeholder="Contoh: Teknik Informatika, Akuntansi, dll">
                            <?php echo form_error('jurusan_keahlian', '<small class="text-danger">', '</small>'); ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Jabatan</label>
                            <input type="text" class="form-control" name="jabatan" 
                                   value="<?php echo set_value('jabatan') ?>" placeholder="Contoh: Kepala Seksi, Koordinator, dll">
                            <?php echo form_error('jabatan', '<small class="text-danger">', '</small>'); ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tahun Masuk</label>
                            <input type="number" class="form-control" name="tahun_masuk" 
                                   value="<?php echo set_value('tahun_masuk') ?>" min="1950" max="<?php echo date('Y') ?>" placeholder="Contoh: 2010">
                            <?php echo form_error('tahun_masuk', '<small class="text-danger">', '</small>'); ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Status Kepegawaian</label>
                            <select class="form-select" name="status_kepegawaian">
                                <option value="">-- Pilih Status --</option>
                                <option value="PNS" <?php echo set_value('status_kepegawaian') == 'PNS' ? 'selected' : '' ?>>PNS</option>
                                <option value="CPNS" <?php echo set_value('status_kepegawaian') == 'CPNS' ? 'selected' : '' ?>>CPNS</option>
                                <option value="Honorer" <?php echo set_value('status_kepegawaian') == 'Honorer' ? 'selected' : '' ?>>Honorer</option>
                                <option value="Kontrak" <?php echo set_value('status_kepegawaian') == 'Kontrak' ? 'selected' : '' ?>>Kontrak</option>
                            </select>
                            <?php echo form_error('status_kepegawaian', '<small class="text-danger">', '</small>'); ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Telepon</label>
                            <input type="text" class="form-control" name="pembimbing_telepon" 
                                   value="<?php echo set_value('pembimbing_telepon') ?>" placeholder="Nomor telepon/handphone">
                            <?php echo form_error('pembimbing_telepon', '<small class="text-danger">', '</small>'); ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" class="form-control" name="pembimbing_email" 
                                   value="<?php echo set_value('pembimbing_email') ?>" placeholder="email@domain.com">
                            <?php echo form_error('pembimbing_email', '<small class="text-danger">', '</small>'); ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Alamat</label>
                        <textarea class="form-control" name="pembimbing_alamat" rows="3" placeholder="Alamat lengkap tempat tinggal"><?php echo set_value('pembimbing_alamat') ?></textarea>
                        <?php echo form_error('pembimbing_alamat', '<small class="text-danger">', '</small>'); ?>
                    </div>
                </div>

                <!-- Kolom Kanan: Akun Login Pembimbing -->
                <div class="col-lg-5">
                    <h5 class="mb-3 text-primary border-bottom pb-2">
                        <i class="fas fa-key me-2"></i>Akun Login Pembimbing
                    </h5>

                    <div class="alert alert-info small mb-3">
                        <i class="fas fa-info-circle me-1"></i>
                        Akun login ini digunakan oleh Guru Pembimbing untuk masuk ke <strong>Portal Pembimbing</strong> (monitoring presensi GPS, validasi logbook harian, dan evaluasi siswa).
                    </div>

                    <div class="mb-3">
                        <label for="username" class="form-label fw-bold">Username <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?php echo set_value('username') ?>" placeholder="Masukkan username login" required autocomplete="off">
                        </div>
                        <?php echo form_error('username', '<small class="text-danger">', '</small>'); ?>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-bold">Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" 
                                   value="<?php echo set_value('password') ?>" placeholder="Masukkan password login" required autocomplete="new-password">
                        </div>
                        <?php echo form_error('password', '<small class="text-danger">', '</small>'); ?>
                    </div>

                    <div class="card bg-light border mt-4">
                        <div class="card-body">
                            <h6 class="card-title fw-bold text-dark"><i class="fas fa-shield-alt text-primary me-2"></i>Informasi Akun</h6>
                            <ul class="small text-muted mb-0 ps-3">
                                <li>Role / Level: <strong>Guru Pembimbing (Level 2)</strong></li>
                                <li>Username dan Password dapat diserahkan langsung kepada guru pembimbing.</li>
                                <li>Pembimbing dapat login melalui halaman <code>/auth/login_pembimbing</code>.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr class="my-4">

            <div class="d-flex justify-content-end gap-2">
                <a href="<?php echo base_url('hubin/view/data-pembimbing') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i> Simpan Data & Buat Akun
                </button>
            </div>
        </form>
    </div>
</div>