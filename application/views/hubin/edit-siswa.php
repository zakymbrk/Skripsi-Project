<?php if(validation_errors()) { ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <?php echo validation_errors(); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php } ?>

<?php if($this->session->flashdata('message')) { ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <?php echo $this->session->flashdata('message'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php } ?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo base_url('hubin/view') ?>"><i class="fas fa-home"></i> Home</a></li>
        <li class="breadcrumb-item"><a href="<?php echo base_url('hubin/view/assign-pembimbing') ?>">Assign Pembimbing</a></li>
        <li class="breadcrumb-item active">Edit Siswa</li>
    </ol>
</nav>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="fas fa-user-edit me-2"></i>Edit Data Siswa</h2>
</div>

<form action="<?php echo site_url('hubin/update_siswa'); ?>" method="post">
    <div class="card fade-in">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-edit me-2"></i>Form Edit Siswa
            </h5>
        </div>
        <div class="card-body">
            <input type="hidden" name="siswa_code" value="<?php echo $siswa->siswa_code; ?>">
            
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td width="30%" class="fw-bold bg-light">Nama Siswa</td>
                            <td>
                                <input type="text" class="form-control" id="siswa_nama" name="siswa_nama" 
                                       value="<?php echo $siswa->siswa_nama ?>" required>
                            </td>
                        </tr>

                        <tr>
                            <td class="fw-bold bg-light">NIS</td>
                            <td>
                                <input type="text" class="form-control" id="siswa_nis" name="siswa_nis" 
                                       placeholder="Masukkan NIS" value="<?php echo $siswa->siswa_nis ? $siswa->siswa_nis : ''; ?>">
                            </td>
                        </tr>

                        <tr>
                            <td class="fw-bold bg-light">Kelas</td>
                            <td>
                                <input type="text" class="form-control" id="siswa_kelas" name="siswa_kelas" 
                                       placeholder="Masukkan Kelas" value="<?php echo $siswa->siswa_kelas ? $siswa->siswa_kelas : ''; ?>" required>
                            </td>
                        </tr>
                        
                        <tr>
                            <td class="fw-bold bg-light">Jurusan</td>
                            <td>
                                <input type="text" class="form-control" id="siswa_jurusan" name="siswa_jurusan" 
                                       placeholder="Masukkan Jurusan" value="<?php echo $siswa->siswa_jurusan ? $siswa->siswa_jurusan : ''; ?>">
                            </td>
                        </tr>
                        
                        <tr>
                            <td class="fw-bold bg-light">Telepon</td>
                            <td>
                                <input type="text" class="form-control" id="siswa_telepon" name="siswa_telepon" 
                                       placeholder="Masukkan Telepon Siswa" value="<?php echo $siswa->siswa_telepon ? $siswa->siswa_telepon : ''; ?>" required>
                            </td>
                        </tr>
                        
                        <tr>
                            <td class="fw-bold bg-light">Alamat</td>
                            <td>
                                <textarea class="form-control" id="siswa_alamat" name="siswa_alamat" rows="3" 
                                          placeholder="Masukkan Alamat"><?php echo $siswa->siswa_alamat ? $siswa->siswa_alamat : ''; ?></textarea>
                            </td>
                        </tr>
                        
                        <tr>
                            <td class="fw-bold bg-light">Periode PKL</td>
                            <td>
                                <input type="text" class="form-control" id="periode" name="periode" 
                                       placeholder="Contoh: Januari 2024 - Juni 2024" value="<?php echo $siswa->periode ? $siswa->periode : ''; ?>">
                            </td>
                        </tr>
                        
                        <tr>
                            <td class="fw-bold bg-light">Status Pengajuan</td>
                            <td>
                                <select class="form-select" id="status_pengajuan" name="status_pengajuan">
                                    <option value="draft" <?php echo ($siswa->status_pengajuan == 'draft') ? 'selected' : ''; ?>>Draft</option>
                                    <option value="menunggu" <?php echo ($siswa->status_pengajuan == 'menunggu') ? 'selected' : ''; ?>>Menunggu</option>
                                    <option value="disetujui" <?php echo ($siswa->status_pengajuan == 'disetujui') ? 'selected' : ''; ?>>Disetujui</option>
                                    <option value="ditolak" <?php echo ($siswa->status_pengajuan == 'ditolak') ? 'selected' : ''; ?>>Ditolak</option>
                                    <option value="selesai" <?php echo ($siswa->status_pengajuan == 'selesai') ? 'selected' : ''; ?>>Selesai PKL / Publikasikan Nilai</option>
                                </select>
                            </td>
                        </tr>
                        
                        <tr>
                            <td class="fw-bold bg-light">ID User</td>
                            <td>
                                <select class="form-select" id="user_id" name="user_id">
                                    <option value="">-- Pilih User --</option>
                                    <?php
                                    $this->db->where('level', 2); // Only students
                                    $users = $this->db->get('tb_user')->result();
                                    foreach($users as $user) {
                                        $selected = ($user->id == $siswa->user_id) ? 'selected' : '';
                                        echo '<option value="'.$user->id.'" '.$selected.'>'.$user->nama_lengkap.' ('.$user->username.')</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        
                        <tr>
                            <td class="fw-bold bg-light">ID DUDI</td>
                            <td>
                                <select class="form-select" id="dudi_id" name="dudi_id">
                                    <option value="">-- Pilih DUDI --</option>
                                    <?php
                                    $dudi = $this->db->get('tb_dudi')->result();
                                    foreach($dudi as $d) {
                                        $selected = ($d->dudi_id == $siswa->dudi_id) ? 'selected' : '';
                                        echo '<option value="'.$d->dudi_id.'" '.$selected.'>'.$d->dudi_nama.'</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save me-2"></i>Update Data Siswa
                </button>
            </div>
        </div>
    </div>
</form>