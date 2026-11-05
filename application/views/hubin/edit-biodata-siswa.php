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

<?php if($this->session->flashdata('error_message')) { ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <?php echo $this->session->flashdata('error_message'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php } ?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo base_url('hubin/view') ?>"><i class="fas fa-home"></i> Home</a></li>
        <li class="breadcrumb-item"><a href="<?php echo base_url('hubin/view/data-user') ?>">Data User</a></li>
        <li class="breadcrumb-item active">Edit Biodata Siswa</li>
    </ol>
</nav>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="fas fa-user-graduate me-2"></i>Edit Biodata Siswa</h2>
</div>

<form action="<?php echo site_url('hubin/update_biodata'); ?>" method="post" enctype="multipart/form-data" id="editBiodataForm">
    <div class="card fade-in">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-edit me-2"></i>Form Edit Biodata Siswa
            </h5>
        </div>
        <div class="card-body">
            <input type="hidden" name="user_code" value="<?php echo $user->user_code ?>">
            <input type="hidden" name="tipe_user" value="siswa">
            
            <!-- Student Data Section -->
            <h5 class="mb-3"><i class="fas fa-user-graduate me-2"></i>Data Siswa</h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="siswa_nama" class="form-label">Nama Siswa *</label>
                        <input type="text" class="form-control" id="siswa_nama" name="siswa_nama" 
                               value="<?php echo isset($biodata->siswa_nama) ? $biodata->siswa_nama : $user->nama_lengkap ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="siswa_nis" class="form-label">NIS (Nomor Induk Siswa)</label>
                        <input type="text" class="form-control" id="siswa_nis" name="siswa_nis" 
                               placeholder="Masukkan NIS" value="<?php echo isset($biodata->siswa_nis) ? $biodata->siswa_nis : ''; ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="siswa_nisn" class="form-label">NISN (Nomor Induk Siswa Nasional)</label>
                        <input type="text" class="form-control" id="siswa_nisn" name="siswa_nisn" 
                               placeholder="Masukkan NISN" value="<?php echo isset($biodata->siswa_nisn) ? $biodata->siswa_nisn : ''; ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="siswa_jk" class="form-label">Jenis Kelamin *</label>
                        <select class="form-select" id="siswa_jk" name="siswa_jk">
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="L" <?php echo (isset($biodata->siswa_jk) && ($biodata->siswa_jk == 'L' || $biodata->siswa_jk == 'Laki-laki')) ? 'selected' : ''; ?>>Laki-laki</option>
                            <option value="P" <?php echo (isset($biodata->siswa_jk) && ($biodata->siswa_jk == 'P' || $biodata->siswa_jk == 'Perempuan')) ? 'selected' : ''; ?>>Perempuan</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="siswa_tempat_lahir" class="form-label">Tempat Lahir</label>
                        <input type="text" class="form-control" id="siswa_tempat_lahir" name="siswa_tempat_lahir" 
                               placeholder="Masukkan Tempat Lahir" value="<?php echo isset($biodata->siswa_tempat_lahir) ? $biodata->siswa_tempat_lahir : ''; ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="siswa_tanggal_lahir" class="form-label">Tanggal Lahir</label>
                        <input type="date" class="form-control" id="siswa_tanggal_lahir" name="siswa_tanggal_lahir" 
                               value="<?php echo isset($biodata->siswa_tanggal_lahir) ? $biodata->siswa_tanggal_lahir : ''; ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="siswa_kelas" class="form-label">Kelas *</label>
                        <input type="text" class="form-control" id="siswa_kelas" name="siswa_kelas" 
                               placeholder="Masukkan Kelas" value="<?php echo isset($biodata->siswa_kelas) ? $biodata->siswa_kelas : ''; ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="siswa_jurusan" class="form-label">Jurusan</label>
                        <input type="text" class="form-control" id="siswa_jurusan" name="siswa_jurusan" 
                               placeholder="Masukkan Jurusan" value="<?php echo isset($biodata->siswa_jurusan) ? $biodata->siswa_jurusan : ''; ?>">
                    </div>
                    

                    <div class="mb-3">
                        <label for="siswa_telepon" class="form-label">Telepon Siswa</label>
                        <input type="text" class="form-control" id="siswa_telepon" name="siswa_telepon" 
                               placeholder="Masukkan Telepon Siswa" value="<?php echo isset($biodata->siswa_telepon) ? $biodata->siswa_telepon : ''; ?>">
                    </div>
                </div>
                
                <div class="col-md-6">
                    <!-- User Account Section -->
                    <h5 class="mb-3"><i class="fas fa-user me-2"></i>Akun Login Siswa</h5>
                    
                    <div class="mb-3">
                        <label for="username" class="form-label">Username *</label>
                        <input type="text" class="form-control" id="username" name="username" 
                               value="<?php echo $user->username ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               placeholder="Masukkan Email" value="<?php echo isset($user->email) && $user->email ? $user->email : (isset($biodata->user_email) && $biodata->user_email ? $biodata->user_email : ''); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat Lengkap</label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="3" 
                                  placeholder="Masukkan Alamat"><?php echo isset($biodata->alamat) ? $biodata->alamat : ''; ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="dudi_id" class="form-label">Perusahaan (DUDI)</label>
                        <select class="form-select" id="dudi_id" name="dudi_id">
                            <option value="">-- Pilih Perusahaan --</option>
                            <?php
                            $this->db->order_by('dudi_nama', 'ASC');
                            $dudi_list = $this->db->get('tb_dudi')->result();
                            $current_dudi_id = isset($biodata->dudi_id) ? $biodata->dudi_id : '';
                            foreach($dudi_list as $dudi){
                                $selected = $current_dudi_id == $dudi->dudi_id ? 'selected' : '';
                                $mitra_label = $dudi->is_mitra ? '(Mitra)' : '(Non-Mitra)';
                                echo '<option value="'.$dudi->dudi_id.'" '.$selected.'>'.$dudi->dudi_nama.' '.$mitra_label.'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tanggal_mulai" class="form-label">Tanggal Mulai PKL</label>
                        <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" 
                               value="<?php echo isset($biodata->tanggal_mulai) ? $biodata->tanggal_mulai : ''; ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="tanggal_selesai" class="form-label">Tanggal Selesai PKL</label>
                        <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" 
                               value="<?php echo isset($biodata->tanggal_selesai) ? $biodata->tanggal_selesai : ''; ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="lama_pelaksanaan" class="form-label">Lama Pelaksanaan (hari)</label>
                        <input type="number" class="form-control" id="lama_pelaksanaan" name="lama_pelaksanaan" 
                               value="<?php echo isset($biodata->lama_pelaksanaan) ? $biodata->lama_pelaksanaan : ''; ?>" min="1">
                    </div>
                    
                    <div class="mb-3">
                        <label for="status_pengajuan" class="form-label">Status Pengajuan</label>
                        <select class="form-select" id="status_pengajuan" name="status_pengajuan">
                            <option value="draft" <?php echo (isset($biodata->status_pengajuan) && $biodata->status_pengajuan == 'draft') ? 'selected' : ''; ?>>Draft</option>
                            <option value="menunggu" <?php echo (isset($biodata->status_pengajuan) && $biodata->status_pengajuan == 'menunggu') ? 'selected' : ''; ?>>Menunggu</option>
                            <option value="disetujui" <?php echo (isset($biodata->status_pengajuan) && $biodata->status_pengajuan == 'disetujui') ? 'selected' : ''; ?>>Disetujui</option>
                            <option value="ditolak" <?php echo (isset($biodata->status_pengajuan) && $biodata->status_pengajuan == 'ditolak') ? 'selected' : ''; ?>>Ditolak</option>
                            <option value="selesai" <?php echo (isset($biodata->status_pengajuan) && $biodata->status_pengajuan == 'selesai') ? 'selected' : ''; ?>>Selesai</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="periode" class="form-label">Periode PKL</label>
                        <input type="text" class="form-control" id="periode" name="periode" 
                               value="<?php echo isset($biodata->periode) ? $biodata->periode : ''; ?>"
                               placeholder="Contoh: Januari 2024 - Juni 2024">
                    </div>
                    
                    <div class="mb-3">
                        <label for="foto_profil" class="form-label">Foto Profil</label>
                        <?php if($user->foto_profil): ?>
                            <div class="mb-3">
                                <label class="form-label">Foto Saat Ini:</label><br>
                                  <img id="foto-profil-preview" src="<?php echo base_url('uploads/profil/'.$user->foto_profil); ?>" 
                                     alt="Foto Profil" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                            </div>
                        <?php endif; ?>
                        
                        <input type="file" class="form-control" id="foto_profil" name="foto_profil" 
                               accept="image/*">
                        <?php if(!$user->foto_profil): ?>
                            <img id="foto-profil-preview" src="" alt="Pratinjau Foto Profil" class="img-thumbnail mt-2 d-none" style="max-width: 200px; max-height: 200px;">
                        <?php endif; ?>
                        <div class="form-text">Format: JPG, PNG, GIF. Ukuran maksimal: 2MB</div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save me-2"></i>Update Biodata
                </button>
            </div>
        </div>
    </div>
</form>

<script>
(function () {
    var input = document.getElementById('foto_profil');
    var preview = document.getElementById('foto-profil-preview');
    if (!input || !preview) return;

    input.addEventListener('change', function () {
        var file = this.files && this.files[0];
        if (!file) return;
        if (!file.type.match(/^image\//)) {
            this.value = '';
            window.alert('Pilih file gambar yang valid.');
            return;
        }
        if (file.size > 2 * 1024 * 1024) {
            this.value = '';
            window.alert('Ukuran foto maksimal 2 MB.');
            return;
        }
        var reader = new FileReader();
        reader.onload = function (event) {
            preview.src = event.target.result;
            preview.classList.remove('d-none');
        };
        reader.readAsDataURL(file);
    });
})();
</script>