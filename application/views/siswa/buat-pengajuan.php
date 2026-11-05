<?php 
$userdata = $this->session->userdata('userdata');

// Get student data
$this->db->where('user_id', $userdata['id']);
$siswa = $this->db->get('tb_siswa')->row();

// Get user profile data to sync phone and address
$this->db->where('id', $userdata['id']);
$user = $this->db->get('tb_user')->row();

// Get DUDI list
$dudi_list = $this->db->order_by('dudi_nama', 'ASC')->get('tb_dudi')->result();
?>

<div class="card-mobile">
    <h5 class="mb-3"><i class="fas fa-file-alt me-2 text-primary"></i>Form Pengajuan PKL</h5>
    
    <?php if(validation_errors()){ ?>
        <div class="alert alert-danger">
            <?php echo validation_errors(); ?>
        </div>
    <?php } ?>
    
    <?php if($siswa && ($siswa->status_pengajuan == 'ditolak' || $siswa->status_pengajuan == 'draft')): ?>
    <div class="alert alert-warning mb-3">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong><?php echo ($siswa->status_pengajuan == 'ditolak') ? 'Pengajuan Anda Ditolak' : 'Draft Pengajuan'; ?></strong><br>
        <?php echo ($siswa->status_pengajuan == 'ditolak') ? 'Anda dapat mengajukan ulang dengan memperbaiki data dan melampirkan dokumen yang diperlukan.' : 'Anda dapat mengedit dan melengkapi data pengajuan Anda.'; ?>
    </div>
<?php elseif($siswa && $siswa->status_pengajuan == 'disetujui'): ?>
    <div class="alert alert-info mb-3">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Anda Sudah Memiliki Pengajuan yang Disetujui</strong><br>
        Anda dapat mengajukan ulang untuk mengganti perusahaan atau periode PKL. Pengajuan baru akan dibuat sebagai record terpisah.
    </div>
<?php elseif($siswa && $siswa->status_pengajuan == 'menunggu'): ?>
<div class="alert alert-info mb-3">
    <i class="fas fa-clock me-2"></i>
    <strong>Pengajuan Sedang Diproses</strong><br>
    Pengajuan Anda sedang menunggu persetujuan dari Hubin. Anda dapat mengajukan ulang dengan data yang berbeda jika diperlukan.
</div>
<?php endif; ?>
    
    <form action="<?php echo base_url('siswa/buat_pengajuan') ?>" method="post" enctype="multipart/form-data">
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
                   value="<?php echo $user ? $user->telepon : ($siswa ? $siswa->siswa_telepon : ''); ?>" required>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Alamat <span class="text-danger">*</span></label>
            <textarea name="siswa_alamat" class="form-control" rows="3" required><?php echo isset($user->alamat) ? $user->alamat : (isset($siswa->siswa_alamat) ? $siswa->siswa_alamat : (isset($siswa->alamat) ? $siswa->alamat : '')); ?></textarea>
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
            <select name="dudi_id" class="form-select" id="dudiSelect" required>
                <option value="">-- Pilih Perusahaan --</option>
                <?php foreach($dudi_list as $d){ ?>
                <option value="<?php echo $d->dudi_id ?>" 
                        <?php echo ($siswa && $siswa->dudi_id == $d->dudi_id) ? 'selected' : ''; ?>>
                    <?php echo $d->dudi_nama ?>
                </option>
                <?php } ?>
                <option value="other" <?php echo ($siswa && $siswa->other_dudi_nama) ? 'selected' : ''; ?>>Lainnya (Perusahaan tidak terdaftar)</option>
            </select>
        </div>
        
        <div class="mb-3" id="otherDudiContainer" style="display: none;">
            <label class="form-label">Nama Perusahaan Lainnya <span class="text-danger">*</span></label>
            <input type="text" name="other_dudi_nama" class="form-control" 
                     value="<?php echo set_value('other_dudi_nama', $siswa ? $siswa->other_dudi_nama : ''); ?>"
                   placeholder="Masukkan nama perusahaan tempat PKL" required>
            <small class="text-muted">Perusahaan ini akan diajukan untuk ditambahkan ke sistem</small>
            
            <!-- Additional company details for unregistered companies -->
            <div class="mt-3">
                <h6 class="mb-3">Data Lengkap Perusahaan</h6>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Alamat Perusahaan</label>
                        <textarea name="other_dudi_alamat" class="form-control" 
                                  placeholder="Alamat lengkap perusahaan"></textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Telepon Perusahaan</label>
                        <input type="text" name="other_dudi_telepon" class="form-control" 
                               placeholder="Nomor telepon perusahaan">
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email Perusahaan</label>
                        <input type="email" name="other_dudi_email" class="form-control" 
                               placeholder="Email perusahaan">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">PIC (Person In Charge)</label>
                        <input type="text" name="other_dudi_pic" class="form-control" 
                               placeholder="Nama PIC perusahaan">
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">NIP PIC</label>
                        <input type="text" name="other_dudi_nip_pic" class="form-control" 
                               placeholder="NIP PIC">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Instruktur</label>
                        <input type="text" name="other_dudi_instruktur" class="form-control" 
                               placeholder="Nama instruktur">
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">NIP Instruktur</label>
                        <input type="text" name="other_dudi_nip_instruktur" class="form-control" 
                               placeholder="NIP instruktur">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Lokasi Perusahaan</label>
                    <div class="row g-2">
                        <div class="col-md-5">
                            <label class="form-label small" for="otherDudiLatitude">Latitude</label>
                            <input type="text" name="other_dudi_latitude" id="otherDudiLatitude" class="form-control"
                                   value="<?php echo set_value('other_dudi_latitude', (!empty($siswa->other_dudi_latitude) ? $siswa->other_dudi_latitude : '')); ?>"
                                   placeholder="Contoh: -6.912345" step="any" min="-90" max="90">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small" for="otherDudiLongitude">Longitude</label>
                            <input type="text" name="other_dudi_longitude" id="otherDudiLongitude" class="form-control"
                                   value="<?php echo set_value('other_dudi_longitude', (!empty($siswa->other_dudi_longitude) ? $siswa->other_dudi_longitude : '')); ?>"
                                   placeholder="Contoh: 107.609122" step="any" min="-180" max="180">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" id="getOtherDudiLocation" class="btn btn-outline-primary w-100">
                                <i class="fas fa-location-crosshairs me-1"></i>Ambil Lokasi
                            </button>
                        </div>
                    </div>
                    <small id="otherDudiLocationStatus" class="text-muted">Bisa diisi manual atau klik tombol Ambil Lokasi untuk otomatis mengambil koordinat saat ini.</small>
                </div>
            </div>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Surat Permohonan/Pernyataan <span class="text-danger">*</span></label>
            <input type="file" name="surat_permohonan" class="form-control" 
                   accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx" required>
            <small class="text-muted">Format: JPG, PNG, GIF, PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX - Maksimal 5MB</small>
            <?php if($siswa && $siswa->surat_permohonan && file_exists('./uploads/pengajuan/'.$siswa->surat_permohonan)){ ?>
            <div class="mt-2">
                <small class="text-muted">File saat ini: </small>
                <a href="<?php echo base_url('uploads/pengajuan/'.$siswa->surat_permohonan) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-file me-1"></i>Lihat Dokumen
                </a>
            </div>
            <?php } ?>
        </div>
        
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary btn-mobile flex-grow-1">
                <i class="fas fa-paper-plane me-2"></i>
                <?php 
                if($siswa && $siswa->status_pengajuan == 'ditolak') {
                    echo 'Ajukan Ulang';
                } elseif($siswa && $siswa->status_pengajuan == 'draft') {
                    echo 'Simpan dan Kirim Pengajuan';
                } elseif($siswa && $siswa->status_pengajuan == 'disetujui') {
                    echo 'Ajukan PKL Baru';
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
const dudiSelect = document.getElementById('dudiSelect');
const otherDudiContainer = document.getElementById('otherDudiContainer');
const otherDudiInput = document.querySelector('input[name="other_dudi_nama"]');
const latitudeInput = document.getElementById('otherDudiLatitude');
const longitudeInput = document.getElementById('otherDudiLongitude');
const locationStatus = document.getElementById('otherDudiLocationStatus');

// Toggle tampilan field "Perusahaan Lainnya" berdasarkan pilihan dropdown
dudiSelect.addEventListener('change', function() {
    if(this.value === 'other') {
        otherDudiContainer.style.display = 'block';
        otherDudiInput.required = true;
        latitudeInput.required = true;
        longitudeInput.required = true;
        // Make all additional fields not required initially
        const additionalInputs = otherDudiContainer.querySelectorAll('input, textarea');
        additionalInputs.forEach(input => {
            if(input.name !== 'other_dudi_nama') {
                input.required = false;
            }
        });
    } else {
        otherDudiContainer.style.display = 'none';
        otherDudiInput.required = false;
        otherDudiInput.value = '';
        latitudeInput.required = false;
        longitudeInput.required = false;
        latitudeInput.value = '';
        longitudeInput.value = '';
        locationStatus.textContent = 'Koordinat wajib diisi untuk perusahaan yang tidak terdaftar.';
        // Clear all additional fields
        const additionalInputs = otherDudiContainer.querySelectorAll('input, textarea');
        additionalInputs.forEach(input => {
            input.value = '';
            input.required = false;
        });
    }
});

// Cek nilai awal saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    if(dudiSelect.value === 'other') {
        otherDudiContainer.style.display = 'block';
        otherDudiInput.required = true;
        latitudeInput.required = true;
        longitudeInput.required = true;
    }

    document.getElementById('getOtherDudiLocation').addEventListener('click', function() {
        if(!navigator.geolocation) {
            locationStatus.textContent = 'Browser tidak mendukung pengambilan lokasi.';
            locationStatus.className = 'text-danger';
            return;
        }

        locationStatus.textContent = 'Mengambil lokasi...';
        locationStatus.className = 'text-muted';
        navigator.geolocation.getCurrentPosition(function(position) {
            latitudeInput.value = position.coords.latitude.toFixed(8);
            longitudeInput.value = position.coords.longitude.toFixed(8);
            locationStatus.textContent = 'Lokasi berhasil diambil.';
            locationStatus.className = 'text-success';
        }, function() {
            locationStatus.textContent = 'Lokasi tidak dapat diambil. Izinkan akses lokasi lalu coba lagi.';
            locationStatus.className = 'text-danger';
        }, { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 });
    });
});
</script>

