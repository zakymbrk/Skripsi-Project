<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo base_url('hubin/view') ?>"><i class="fas fa-home"></i> Home</a></li>
        <li class="breadcrumb-item"><a href="<?php echo base_url('hubin/view/data-dudi') ?>">Data DUDI</a></li>
        <li class="breadcrumb-item active">Tambah DUDI</li>
    </ol>
</nav>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="fas fa-building me-2"></i>Tambah DUDI</h2>
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

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h6 class="mb-1 fw-bold"><i class="fas fa-file-import me-2 text-success"></i>Import DUDI (CSV / Excel)</h6>
                <small class="text-muted">Jika Anda ingin mengimport banyak data sekaligus.</small>
            </div>
            <div>
                <a href="<?php echo base_url('uploads/imports'); ?>" class="btn btn-outline-success btn-sm me-2" target="_blank">
                    <i class="fas fa-folder-open me-1"></i>Folder Import
                </a>
            </div>
        </div>

        <form action="<?php echo site_url('hubin/import_dudi') ?>" method="post" enctype="multipart/form-data" class="mt-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-8">
                    <label for="dudi_file" class="form-label small fw-semibold">Pilih File</label>
                    <input type="file" class="form-control" id="dudi_file" name="dudi_file" accept=".csv,.xlsx,.xls" required>
                    <div class="form-text">Format header: Nama DUDI, Alamat, Telepon, Email, Penanggung Jawab (PIC), NIP PIC, Instruktur, NIP Instruktur, Status Mitra, Latitude, Longitude, Radius Toleransi (Meter)</div>
                </div>
                <div class="col-md-4 text-md-end">
                    <button type="submit" class="btn btn-success w-100">
                        <i class="fas fa-upload me-2"></i>Import DUDI
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Card -->
<div class="card fade-in">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-plus me-2"></i>Tambah Data DUDI
        </h5>
    </div>
    <div class="card-body">
        <form action="<?php echo site_url('hubin/tambah_dudi') ?>" method="post">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="dudi_nama" class="form-label">Nama DUDI <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="dudi_nama" name="dudi_nama" 
                               value="<?php echo set_value('dudi_nama') ?>" required>
                        <?php echo form_error('dudi_nama', '<small class="text-danger">', '</small>'); ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="dudi_alamat" class="form-label">Alamat <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="dudi_alamat" name="dudi_alamat" rows="3" required><?php echo set_value('dudi_alamat') ?></textarea>
                        <?php echo form_error('dudi_alamat', '<small class="text-danger">', '</small>'); ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="dudi_telepon" class="form-label">Telepon</label>
                        <input type="text" class="form-control" id="dudi_telepon" name="dudi_telepon" 
                               value="<?php echo set_value('dudi_telepon') ?>">
                        <?php echo form_error('dudi_telepon', '<small class="text-danger">', '</small>'); ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="dudi_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="dudi_email" name="dudi_email" 
                               value="<?php echo set_value('dudi_email') ?>">
                        <?php echo form_error('dudi_email', '<small class="text-danger">', '</small>'); ?>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="dudi_pic" class="form-label">Penanggung Jawab (PIC)</label>
                        <input type="text" class="form-control" id="dudi_pic" name="dudi_pic" 
                               value="<?php echo set_value('dudi_pic') ?>">
                        <?php echo form_error('dudi_pic', '<small class="text-danger">', '</small>'); ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="dudi_nip_pic" class="form-label">NIP PIC</label>
                        <input type="text" class="form-control" id="dudi_nip_pic" name="dudi_nip_pic" 
                               value="<?php echo set_value('dudi_nip_pic') ?>">
                        <?php echo form_error('dudi_nip_pic', '<small class="text-danger">', '</small>'); ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="dudi_instruktur" class="form-label">Instruktur</label>
                        <input type="text" class="form-control" id="dudi_instruktur" name="dudi_instruktur" 
                               value="<?php echo set_value('dudi_instruktur') ?>">
                        <?php echo form_error('dudi_instruktur', '<small class="text-danger">', '</small>'); ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="dudi_nip_instruktur" class="form-label">NIP Instruktur</label>
                        <input type="text" class="form-control" id="dudi_nip_instruktur" name="dudi_nip_instruktur" 
                               value="<?php echo set_value('dudi_nip_instruktur') ?>">
                        <?php echo form_error('dudi_nip_instruktur', '<small class="text-danger">', '</small>'); ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="is_mitra" class="form-label">Status Mitra</label>
                        <select class="form-select" id="is_mitra" name="is_mitra">
                            <option value="1" <?php echo set_value('is_mitra', 1) == '1' ? 'selected' : ''; ?>>Mitra</option>
                            <option value="0" <?php echo set_value('is_mitra') == '0' ? 'selected' : ''; ?>>Non-Mitra</option>
                        </select>
                        <?php echo form_error('is_mitra', '<small class="text-danger">', '</small>'); ?>
                    </div>
                </div>
            </div>

            <!-- Konfigurasi Geolocation & Radius Presensi (Rule-Based Validation) -->
            <div class="card bg-light border-0 rounded-4 p-4 mt-3 mb-2">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <h6 class="fw-bold text-dark mb-0"><i class="fas fa-map-marked-alt text-primary me-2"></i>Konfigurasi Titik Lokasi & Radius Presensi (GPS)</h6>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="btn-get-location">
                        <i class="fas fa-location-crosshairs me-1"></i>Ambil Lokasi Saat Ini
                    </button>
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="latitude" class="form-label small fw-semibold">Latitude DUDI</label>
                        <input type="text" class="form-control form-control-sm" id="latitude" name="latitude" 
                               value="<?php echo set_value('latitude') ?>" placeholder="Contoh: -7.032145">
                        <div class="form-text text-muted" style="font-size: 0.75rem;">Titik lintang lokasi kantor DUDI. Bisa diambil otomatis atau diisi manual.</div>
                        <?php echo form_error('latitude', '<small class="text-danger">', '</small>'); ?>
                    </div>
                    <div class="col-md-4">
                        <label for="longitude" class="form-label small fw-semibold">Longitude DUDI</label>
                        <input type="text" class="form-control form-control-sm" id="longitude" name="longitude" 
                               value="<?php echo set_value('longitude') ?>" placeholder="Contoh: 107.712345">
                        <div class="form-text text-muted" style="font-size: 0.75rem;">Titik bujur lokasi kantor DUDI. Bisa diambil otomatis atau diisi manual.</div>
                        <?php echo form_error('longitude', '<small class="text-danger">', '</small>'); ?>
                    </div>
                    <div class="col-md-4">
                        <label for="radius" class="form-label small fw-semibold">Radius Toleransi (Meter) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control form-control-sm" id="radius" name="radius" 
                               value="<?php echo set_value('radius', 100) ?>" min="10" max="5000" required>
                        <div class="form-text text-muted" style="font-size: 0.75rem;">Batas jarak maksimal presensi (default: 100 meter).</div>
                        <?php echo form_error('radius', '<small class="text-danger">', '</small>'); ?>
                    </div>
                </div>
            </div>
            
            <hr>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4 pt-3 border-top">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Simpan Data
                </button>
                <a href="<?php echo base_url('hubin/view/data-dudi') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-2"></i>Batalkan
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const btnGetLocation = document.getElementById('btn-get-location');
        const latitudeInput = document.getElementById('latitude');
        const longitudeInput = document.getElementById('longitude');

        if (btnGetLocation) {
            btnGetLocation.addEventListener('click', function () {
                if (!navigator.geolocation) {
                    alert('Browser Anda tidak mendukung fitur geolocation. Silakan isi Latitude dan Longitude secara manual.');
                    return;
                }

                btnGetLocation.disabled = true;
                btnGetLocation.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Mengambil Lokasi...';

                navigator.geolocation.getCurrentPosition(function (position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    latitudeInput.value = lat.toFixed(8);
                    longitudeInput.value = lng.toFixed(8);

                    btnGetLocation.disabled = false;
                    btnGetLocation.innerHTML = '<i class="fas fa-location-crosshairs me-1"></i>Ambil Lokasi Saat Ini';
                }, function () {
                    btnGetLocation.disabled = false;
                    btnGetLocation.innerHTML = '<i class="fas fa-location-crosshairs me-1"></i>Ambil Lokasi Saat Ini';
                    alert('Gagal mengambil lokasi browser. Silakan isi Latitude dan Longitude secara manual.');
                }, {
                    enableHighAccuracy: true,
                    timeout: 20000,
                    maximumAge: 0
                });
            });
        }
    });
</script>