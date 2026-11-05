<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo base_url('hubin/view') ?>"><i class="fas fa-home"></i> Home</a></li>
        <li class="breadcrumb-item"><a href="<?php echo base_url('hubin/view/data-pembimbing') ?>">Data Pembimbing</a></li>
        <li class="breadcrumb-item active">Edit Pembimbing</li>
    </ol>
</nav>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="fas fa-user-tie me-2"></i>Edit Pembimbing</h2>
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

<!-- Card -->
<div class="card fade-in">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-edit me-2"></i>Edit Data Pembimbing (Lengkap)
        </h5>
    </div>
    <div class="card-body">
        <form action="<?php echo site_url('hubin/update_pembimbing_lengkap') ?>" method="post">
            <input type="hidden" name="pembimbing_code" value="<?php echo $pembimbing->pembimbing_code ?>">
            
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tbody>

                        <tr>
                            <td width="30%" class="fw-bold bg-light">Nama Pembimbing <span class="text-danger">*</span></td>
                            <td>
                                <input type="text" class="form-control" name="pembimbing_nama" 
                                       value="<?php echo set_value('pembimbing_nama', $pembimbing->pembimbing_nama) ?>" required>
                                <?php echo form_error('pembimbing_nama', '<small class="text-danger">', '</small>'); ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold bg-light">NIP <span class="text-danger">*</span></td>
                            <td>
                                <input type="text" class="form-control" name="pembimbing_nip" 
                                       value="<?php echo set_value('pembimbing_nip', $pembimbing->pembimbing_nip) ?>" required>
                                <?php echo form_error('pembimbing_nip', '<small class="text-danger">', '</small>'); ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold bg-light">Username Login</td>
                            <td>
                                <input type="text" class="form-control" name="username" value="<?php echo set_value('username', isset($pembimbing->username) ? $pembimbing->username : '') ?>" pattern="[A-Za-z0-9_]+" placeholder="Username Portal Pembimbing">
                                <small class="text-muted">Password lama tetap digunakan. Untuk mengganti password, gunakan menu pengelolaan akun.</small>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold bg-light">Tempat Tugas</td>
                            <td>
                                <select class="form-control" name="tempat_tugas" required>
                                    <option value="">-- Pilih Tempat Tugas --</option>
                                    <option value="TKJ" <?php echo set_value('tempat_tugas', isset($pembimbing->tempat_tugas) ? $pembimbing->tempat_tugas : '') == 'TKJ' ? 'selected' : '' ?>>Teknik Komputer Jaringan</option>
                                    <option value="Perbankan" <?php echo set_value('tempat_tugas', isset($pembimbing->tempat_tugas) ? $pembimbing->tempat_tugas : '') == 'Perbankan' ? 'selected' : '' ?>>Perbankan</option>
                                </select>
                                <?php echo form_error('tempat_tugas', '<small class="text-danger">', '</small>'); ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold bg-light">Tempat Lahir</td>
                            <td>
                                <input type="text" class="form-control" name="tempat_lahir" 
                                       value="<?php echo set_value('tempat_lahir', isset($pembimbing->tempat_lahir) ? $pembimbing->tempat_lahir : '') ?>">
                                <?php echo form_error('tempat_lahir', '<small class="text-danger">', '</small>'); ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold bg-light">Tanggal Lahir</td>
                            <td>
                                <input type="date" class="form-control" name="tanggal_lahir" 
                                       value="<?php echo set_value('tanggal_lahir', isset($pembimbing->tanggal_lahir) ? $pembimbing->tanggal_lahir : '') ?>">
                                <?php echo form_error('tanggal_lahir', '<small class="text-danger">', '</small>'); ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold bg-light">Jenis Kelamin</td>
                            <td>
                                <select class="form-control" name="jenis_kelamin">
                                    <option value="">-- Pilih Jenis Kelamin --</option>
                                    <option value="Laki-laki" <?php echo set_value('jenis_kelamin', isset($pembimbing->jenis_kelamin) ? $pembimbing->jenis_kelamin : '') == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                                    <option value="Perempuan" <?php echo set_value('jenis_kelamin', isset($pembimbing->jenis_kelamin) ? $pembimbing->jenis_kelamin : '') == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                                </select>
                                <?php echo form_error('jenis_kelamin', '<small class="text-danger">', '</small>'); ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold bg-light">Alamat</td>
                            <td>
                                <textarea class="form-control" name="pembimbing_alamat" rows="3" placeholder="Alamat lengkap pembimbing"><?php echo set_value('pembimbing_alamat', isset($pembimbing->pembimbing_alamat) ? $pembimbing->pembimbing_alamat : (isset($pembimbing->alamat) ? $pembimbing->alamat : '')) ?></textarea>
                                <?php echo form_error('pembimbing_alamat', '<small class="text-danger">', '</small>'); ?>
                            </td>
                        </tr>
                        

                        <tr>
                            <td class="fw-bold bg-light">Pendidikan Terakhir</td>
                            <td>
                                <select class="form-control" name="pendidikan_terakhir">
                                    <option value="">-- Pilih Pendidikan --</option>
                                    <option value="SMA/SMK" <?php echo set_value('pendidikan_terakhir', isset($pembimbing->pendidikan_terakhir) ? $pembimbing->pendidikan_terakhir : '') == 'SMA/SMK' ? 'selected' : '' ?>>SMA/SMK</option>
                                    <option value="D3" <?php echo set_value('pendidikan_terakhir', isset($pembimbing->pendidikan_terakhir) ? $pembimbing->pendidikan_terakhir : '') == 'D3' ? 'selected' : '' ?>>D3</option>
                                    <option value="S1" <?php echo set_value('pendidikan_terakhir', isset($pembimbing->pendidikan_terakhir) ? $pembimbing->pendidikan_terakhir : '') == 'S1' ? 'selected' : '' ?>>S1</option>
                                    <option value="S2" <?php echo set_value('pendidikan_terakhir', isset($pembimbing->pendidikan_terakhir) ? $pembimbing->pendidikan_terakhir : '') == 'S2' ? 'selected' : '' ?>>S2</option>
                                    <option value="S3" <?php echo set_value('pendidikan_terakhir', isset($pembimbing->pendidikan_terakhir) ? $pembimbing->pendidikan_terakhir : '') == 'S3' ? 'selected' : '' ?>>S3</option>
                                </select>
                                <?php echo form_error('pendidikan_terakhir', '<small class="text-danger">', '</small>'); ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold bg-light">Jurusan Keahlian</td>
                            <td>
                                <input type="text" class="form-control" name="jurusan_keahlian" 
                                       value="<?php echo set_value('jurusan_keahlian', isset($pembimbing->jurusan_keahlian) ? $pembimbing->jurusan_keahlian : '') ?>" placeholder="Contoh: Teknik Informatika, Akuntansi, dll">
                                <?php echo form_error('jurusan_keahlian', '<small class="text-danger">', '</small>'); ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold bg-light">Jabatan</td>
                            <td>
                                <input type="text" class="form-control" name="jabatan" 
                                       value="<?php echo set_value('jabatan', isset($pembimbing->jabatan) ? $pembimbing->jabatan : '') ?>" placeholder="Contoh: Kepala Seksi, Koordinator, dll">
                                <?php echo form_error('jabatan', '<small class="text-danger">', '</small>'); ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold bg-light">Tahun Masuk</td>
                            <td>
                                <input type="number" class="form-control" name="tahun_masuk" 
                                       value="<?php echo set_value('tahun_masuk', isset($pembimbing->tahun_masuk) ? $pembimbing->tahun_masuk : '') ?>" min="1950" max="<?php echo date('Y') ?>" placeholder="Contoh: 2010">
                                <?php echo form_error('tahun_masuk', '<small class="text-danger">', '</small>'); ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold bg-light">Status Kepegawaian</td>
                            <td>
                                <select class="form-control" name="status_kepegawaian">
                                    <option value="">-- Pilih Status --</option>
                                    <option value="PNS" <?php echo set_value('status_kepegawaian', isset($pembimbing->status_kepegawaian) ? $pembimbing->status_kepegawaian : '') == 'PNS' ? 'selected' : '' ?>>PNS</option>
                                    <option value="CPNS" <?php echo set_value('status_kepegawaian', isset($pembimbing->status_kepegawaian) ? $pembimbing->status_kepegawaian : '') == 'CPNS' ? 'selected' : '' ?>>CPNS</option>
                                    <option value="Honorer" <?php echo set_value('status_kepegawaian', isset($pembimbing->status_kepegawaian) ? $pembimbing->status_kepegawaian : '') == 'Honorer' ? 'selected' : '' ?>>Honorer</option>
                                    <option value="Kontrak" <?php echo set_value('status_kepegawaian', isset($pembimbing->status_kepegawaian) ? $pembimbing->status_kepegawaian : '') == 'Kontrak' ? 'selected' : '' ?>>Kontrak</option>
                                </select>
                                <?php echo form_error('status_kepegawaian', '<small class="text-danger">', '</small>'); ?>
                            </td>
                        </tr>


                        <tr>
                            <td class="fw-bold bg-light">Telepon</td>
                            <td>
                                <input type="text" class="form-control" name="pembimbing_telepon" 
                                       value="<?php echo set_value('pembimbing_telepon', $pembimbing->pembimbing_telepon) ?>" placeholder="Nomor telepon/handphone">
                                <?php echo form_error('pembimbing_telepon', '<small class="text-danger">', '</small>'); ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold bg-light">Email</td>
                            <td>
                                <input type="email" class="form-control" name="pembimbing_email" 
                                       value="<?php echo set_value('pembimbing_email', $pembimbing->pembimbing_email) ?>" placeholder="email@domain.com">
                                <?php echo form_error('pembimbing_email', '<small class="text-danger">', '</small>'); ?>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
            
            <div class="card-footer d-flex flex-wrap gap-3 justify-content-between">
                <div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        <span>Update Data Pembimbing</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>