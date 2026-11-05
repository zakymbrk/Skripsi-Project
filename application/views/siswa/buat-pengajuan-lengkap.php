<?php 
$userdata = $this->session->userdata('userdata');
$this->db->where('user_id', $userdata['id']);
$siswa = $this->db->get('tb_siswa')->row();

// Get DUDI list
$dudi_list = $this->db->order_by('dudi_nama', 'ASC')->get('tb_dudi')->result();

// Check if this is an edit operation
$is_edit = ($siswa && $siswa->status_pengajuan != 'draft' && $siswa->status_pengajuan != 'disetujui');
$can_upload = !$siswa || $siswa->status_pengajuan == 'draft' || $siswa->status_pengajuan == 'ditolak';
?>

<div class="card-mobile">
    <h5 class="mb-3">
        <i class="fas fa-file-alt me-2 text-primary"></i>
        <?php if($is_edit): ?>
            Edit Pengajuan PKL
        <?php else: ?>
            Form Pengajuan PKL Lengkap
        <?php endif; ?>
    </h5>
    
    <?php if(validation_errors()){ ?>
        <div class="alert alert-danger">
            <?php echo validation_errors(); ?>
        </div>
    <?php } ?>
    
    <?php if($siswa && $siswa->status_pengajuan == 'ditolak'){ ?>
    <div class="alert alert-warning mb-3">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Pengajuan Anda Ditolak</strong><br>
        Anda dapat mengajukan ulang dengan data yang lebih lengkap dan dokumen yang diperlukan.
    </div>
    <?php } ?>
    
    <?php if($siswa && $siswa->status_pengajuan == 'menunggu'){ ?>
    <div class="alert alert-info mb-3">
        <i class="fas fa-clock me-2"></i>
        <strong>Pengajuan Sedang Diproses</strong><br>
        Pengajuan Anda sedang menunggu persetujuan. Anda dapat menambahkan dokumen pendukung jika diperlukan.
    </div>
    <?php } ?>
    
    <form action="<?php echo base_url('siswa/buat_pengajuan') ?>" method="post" enctype="multipart/form-data">
        <?php if($siswa): ?>
        <input type="hidden" name="siswa_code" value="<?php echo $siswa->siswa_code ?>">
        <?php endif; ?>
        
        <div class="mb-3">
            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
            <input type="text" name="siswa_nama" class="form-control" 
                   value="<?php echo $userdata['nama_lengkap']; ?>" readonly>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Kelas <span class="text-danger">*</span></label>
            <select name="siswa_kelas" class="form-select" required>
                <option value="">-- Pilih Kelas --</option>
                <option value="XI - TKJ1" <?php echo ($siswa && $siswa->siswa_kelas == 'XI - TKJ1') ? 'selected' : ''; ?>>XI - TKJ1</option>
                <option value="XI - TKJ2" <?php echo ($siswa && $siswa->siswa_kelas == 'XI - TKJ2') ? 'selected' : ''; ?>>XI - TKJ2</option>
                <option value="XI - TKJ3" <?php echo ($siswa && $siswa->siswa_kelas == 'XI - TKJ3') ? 'selected' : ''; ?>>XI - TKJ3</option>
                <option value="XI - Perbankan" <?php echo ($siswa && $siswa->siswa_kelas == 'XI - Perbankan') ? 'selected' : ''; ?>>XI - Perbankan</option>
            </select>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Telepon <span class="text-danger">*</span></label>
            <input type="text" name="siswa_telepon" class="form-control" 
                   value="<?php echo $siswa ? $siswa->siswa_telepon : ''; ?>" required>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Alamat <span class="text-danger">*</span></label>
            <textarea name="alamat" class="form-control" rows="3" required><?php echo isset($siswa->alamat) ? $siswa->alamat : (isset($siswa->siswa_alamat) ? $siswa->siswa_alamat : ''); ?></textarea>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Periode PKL <span class="text-danger">*</span></label>
            <input type="text" name="periode" class="form-control" 
                   value="<?php echo $siswa ? $siswa->periode : ''; ?>" 
                   placeholder="Contoh: 2023/2024" required>
            <small class="text-muted">Masukkan periode PKL (tahun/tahun)</small>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Perusahaan (DUDI) <span class="text-danger">*</span></label>
            <select name="dudi_id" class="form-select" required>
                <option value="">-- Pilih Perusahaan --</option>
                <?php foreach($dudi_list as $d){ ?>
                <option value="<?php echo $d->dudi_id ?>" 
                        <?php echo ($siswa && $siswa->dudi_id == $d->dudi_id) ? 'selected' : ''; ?>>
                    <?php echo $d->dudi_nama ?>
                </option>
                <?php } ?>
            </select>
        </div>
        
        <!-- File Upload Section -->
        <div class="mb-4">
            <h6 class="mb-3"><i class="fas fa-paperclip me-2 text-primary"></i>Dokumen Pendukung</h6>
            
            <?php if($can_upload): ?>
            <div class="mb-3">
                <label class="form-label">Surat Permohonan/Pernyataan</label>
                <input type="file" name="surat_permohonan" class="form-control" 
                      accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx">
                  <small class="text-muted">Format: JPG, PNG, GIF, PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX - Maksimal 5MB</small>
                <?php if($siswa && $siswa->surat_permohonan && file_exists('./uploads/pengajuan/'.$siswa->surat_permohonan)){ ?>
                <div class="mt-2">
                    <small class="text-muted">File saat ini: </small>
                    <a href="<?php echo base_url('uploads/pengajuan/'.$siswa->surat_permohonan) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-file me-1"></i>Lihat Dokumen
                    </a>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile('surat_permohonan')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <?php } ?>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Foto Dokumentasi (Opsional)</label>
                <input type="file" name="foto_dokumentasi" class="form-control" 
                      accept=".jpg,.jpeg,.png,.gif" multiple>
                  <small class="text-muted">Upload foto-foto dokumentasi (Format: JPG, PNG, GIF - Maksimal 5MB per file)</small>
                <?php if($siswa && $siswa->foto_dokumentasi): ?>
                    <?php 
                    $foto_files = json_decode($siswa->foto_dokumentasi, true);
                    if(is_array($foto_files) && !empty($foto_files)):
                    ?>
                    <div class="mt-2">
                        <small class="text-muted">Foto saat ini:</small>
                        <div class="d-flex flex-wrap gap-2 mt-1">
                            <?php foreach($foto_files as $foto): ?>
                                <?php if(file_exists('./uploads/pengajuan/'.$foto)): ?>
                                <div class="position-relative">
                                    <a href="<?php echo base_url('uploads/pengajuan/'.$foto) ?>" target="_blank">
                                        <img src="<?php echo base_url('uploads/pengajuan/'.$foto) ?>" 
                                             alt="Dokumentasi" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0" 
                                            onclick="removeFoto('<?php echo $foto; ?>')" style="padding: 2px 6px; font-size: 0.7rem;">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Upload dokumen hanya tersedia untuk pengajuan baru atau yang ditolak.
            </div>
            <?php endif; ?>
        </div>
        
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary btn-mobile flex-grow-1">
                <i class="fas fa-<?php echo $is_edit ? 'save' : 'paper-plane'; ?> me-2"></i>
                <?php 
                if($siswa && $siswa->status_pengajuan == 'ditolak') {
                    echo 'Ajukan Ulang';
                } elseif($is_edit) {
                    echo 'Update Pengajuan';
                } else {
                    echo 'Kirim Pengajuan';
                }
                ?>
            </button>
            <a href="<?php echo base_url('siswa/view/home') ?>" class="btn btn-secondary btn-mobile">
                <i class="fas fa-times me-2"></i>Batal
            </a>
        </div>
    </form>
</div>

<script>
function removeFile(fieldName) {
    if(confirm('Hapus file ini?')) {
        // Create hidden input to indicate file removal
        const form = document.querySelector('form');
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = fieldName + '_hapus';
        input.value = '1';
        form.appendChild(input);
        
        // Refresh page to show updated state
        location.reload();
    }
}

function removeFoto(filename) {
    if(confirm('Hapus foto ini?')) {
        // Create hidden input to indicate foto removal
        const form = document.querySelector('form');
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'foto_hapus[]';
        input.value = filename;
        form.appendChild(input);
        
        // Refresh page to show updated state
        location.reload();
    }
}
</script>