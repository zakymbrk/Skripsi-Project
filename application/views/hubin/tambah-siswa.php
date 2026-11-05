<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo base_url('hubin/view') ?>"><i class="fas fa-home"></i> Home</a></li>
        <li class="breadcrumb-item"><a href="<?php echo base_url('hubin/view/daftar-siswa') ?>">Daftar Siswa</a></li>
        <li class="breadcrumb-item active">Tambah Siswa</li>
    </ol>
</nav>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Tambah Siswa</h2>
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
            <i class="fas fa-plus me-2"></i>Tambah Data Siswa
        </h5>
    </div>
    <div class="card-body">
        <div class="alert alert-info mb-4">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Perhatian:</strong> Sistem akan memeriksa duplikasi data berdasarkan NIS, NISN, dan kombinasi Nama+Kelas.
            Pastikan data yang dimasukkan sudah benar sebelum menyimpan.
        </div>
        
        <form action="<?php echo site_url('hubin/tambah_siswa') ?>" method="post" id="tambahSiswaForm">
            <div class="row">
                <div class="col-md-6">
                    <!-- Student Data Section -->
                    <h5 class="mb-3"><i class="fas fa-user-graduate me-2"></i>Data Siswa</h5>
                    
                    <div class="mb-3">
                        <label for="siswa_nama" class="form-label">Nama Siswa <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="siswa_nama" name="siswa_nama" 
                               value="<?php echo set_value('siswa_nama') ?>" required autocomplete="off">
                        <div class="invalid-feedback" id="nama-error"></div>
                        <?php echo form_error('siswa_nama', '<small class="text-danger">', '</small>'); ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="siswa_nis" class="form-label">NIS (Nomor Induk Siswa)</label>
                        <input type="text" class="form-control" id="siswa_nis" name="siswa_nis" 
                               value="<?php echo set_value('siswa_nis') ?>" autocomplete="off">
                        <div class="invalid-feedback" id="nis-error"></div>
                        <?php echo form_error('siswa_nis', '<small class="text-danger">', '</small>'); ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="siswa_nisn" class="form-label">NISN (Nomor Induk Siswa Nasional)</label>
                        <input type="text" class="form-control" id="siswa_nisn" name="siswa_nisn" 
                               value="<?php echo set_value('siswa_nisn') ?>" autocomplete="off">
                        <div class="invalid-feedback" id="nisn-error"></div>
                        <?php echo form_error('siswa_nisn', '<small class="text-danger">', '</small>'); ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="siswa_jk" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                        <select class="form-select" id="siswa_jk" name="siswa_jk" required>
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="L" <?php echo set_value('siswa_jk') == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                            <option value="P" <?php echo set_value('siswa_jk') == 'P' ? 'selected' : '' ?>>Perempuan</option>
                        </select>
                        <?php echo form_error('siswa_jk', '<small class="text-danger">', '</small>'); ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="siswa_tempat_lahir" class="form-label">Tempat Lahir</label>
                        <input type="text" class="form-control" id="siswa_tempat_lahir" name="siswa_tempat_lahir" 
                               value="<?php echo set_value('siswa_tempat_lahir') ?>">
                        <?php echo form_error('siswa_tempat_lahir', '<small class="text-danger">', '</small>'); ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="siswa_tanggal_lahir" class="form-label">Tanggal Lahir</label>
                        <input type="date" class="form-control" id="siswa_tanggal_lahir" name="siswa_tanggal_lahir" 
                               value="<?php echo set_value('siswa_tanggal_lahir') ?>">
                        <?php echo form_error('siswa_tanggal_lahir', '<small class="text-danger">', '</small>'); ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="siswa_kelas" class="form-label">Kelas <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="siswa_kelas" name="siswa_kelas" 
                               value="<?php echo set_value('siswa_kelas') ?>" required>
                        <?php echo form_error('siswa_kelas', '<small class="text-danger">', '</small>'); ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="siswa_jurusan" class="form-label">Jurusan</label>
                        <input type="text" class="form-control" id="siswa_jurusan" name="siswa_jurusan" 
                               value="<?php echo set_value('siswa_jurusan') ?>">
                        <?php echo form_error('siswa_jurusan', '<small class="text-danger">', '</small>'); ?>
                    </div>
                    

                    <div class="mb-3">
                        <label for="siswa_telepon" class="form-label">Telepon Siswa</label>
                        <input type="text" class="form-control" id="siswa_telepon" name="siswa_telepon" 
                               value="<?php echo set_value('siswa_telepon') ?>">
                        <?php echo form_error('siswa_telepon', '<small class="text-danger">', '</small>'); ?>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <!-- User Account Section -->
                    <h5 class="mb-3"><i class="fas fa-user me-2"></i>Akun Login Siswa</h5>
                    
                    <div class="mb-3">
                        <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="username" name="username" 
                               value="<?php echo set_value('username') ?>" required>
                        <?php echo form_error('username', '<small class="text-danger">', '</small>'); ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password" name="password" 
                               value="<?php echo set_value('password') ?>" required>
                        <?php echo form_error('password', '<small class="text-danger">', '</small>'); ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo set_value('email') ?>">
                        <?php echo form_error('email', '<small class="text-danger">', '</small>'); ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="siswa_alamat" class="form-label">Alamat Lengkap</label>
                        <textarea class="form-control" id="siswa_alamat" name="siswa_alamat" rows="3"><?php echo set_value('siswa_alamat') ?></textarea>
                        <?php echo form_error('siswa_alamat', '<small class="text-danger">', '</small>'); ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="dudi_id" class="form-label">Perusahaan (DUDI)</label>
                        <select class="form-select" id="dudi_id" name="dudi_id">
                            <option value="">-- Pilih Perusahaan --</option>
                            <?php
                            $this->db->order_by('dudi_nama', 'ASC');
                            $dudi_list = $this->db->get('tb_dudi')->result();
                            foreach($dudi_list as $dudi){
                                $selected = set_value('dudi_id') == $dudi->dudi_id ? 'selected' : '';
                                $mitra_label = $dudi->is_mitra ? '(Mitra)' : '(Non-Mitra)';
                                echo '<option value="'.$dudi->dudi_id.'" '.$selected.'>'.$dudi->dudi_nama.' '.$mitra_label.'</option>';
                            }
                            ?>
                        </select>
                        <?php echo form_error('dudi_id', '<small class="text-danger">', '</small>'); ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tanggal_mulai" class="form-label">Tanggal Mulai PKL</label>
                        <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" 
                               value="<?php echo set_value('tanggal_mulai') ?>">
                        <?php echo form_error('tanggal_mulai', '<small class="text-danger">', '</small>'); ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tanggal_selesai" class="form-label">Tanggal Selesai PKL</label>
                        <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" 
                               value="<?php echo set_value('tanggal_selesai') ?>">
                        <?php echo form_error('tanggal_selesai', '<small class="text-danger">', '</small>'); ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="lama_pelaksanaan" class="form-label">Lama Pelaksanaan (hari)</label>
                        <input type="number" class="form-control" id="lama_pelaksanaan" name="lama_pelaksanaan" 
                               value="<?php echo set_value('lama_pelaksanaan') ?>" min="1">
                        <?php echo form_error('lama_pelaksanaan', '<small class="text-danger">', '</small>'); ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="status_pengajuan" class="form-label">Status Pengajuan</label>
                        <select class="form-select" id="status_pengajuan" name="status_pengajuan">
                            <option value="draft" <?php echo set_value('status_pengajuan', 'draft') == 'draft' ? 'selected' : ''; ?>>Draft</option>
                            <option value="menunggu" <?php echo set_value('status_pengajuan') == 'menunggu' ? 'selected' : ''; ?>>Menunggu</option>
                            <option value="disetujui" <?php echo set_value('status_pengajuan') == 'disetujui' ? 'selected' : ''; ?>>Disetujui</option>
                            <option value="ditolak" <?php echo set_value('status_pengajuan') == 'ditolak' ? 'selected' : ''; ?>>Ditolak</option>
                            <option value="selesai" <?php echo set_value('status_pengajuan') == 'selesai' ? 'selected' : ''; ?>>Selesai</option>
                        </select>
                        <?php echo form_error('status_pengajuan', '<small class="text-danger">', '</small>'); ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="periode" class="form-label">Periode PKL</label>
                        <input type="text" class="form-control" id="periode" name="periode" 
                               value="<?php echo set_value('periode') ?>">
                        <?php echo form_error('periode', '<small class="text-danger">', '</small>'); ?>
                    </div>
                </div>
            </div>
            
            <div class="card-footer d-flex flex-wrap gap-3 justify-content-between">
                <div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        <span>Simpan Data</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Data synchronization features
let syncTimeout;

// Initialize synchronization
function initSynchronization() {
    // Set up real-time sync for form changes
    const form = document.getElementById('tambahSiswaForm');
    if (form) {
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('change', function() {
                queueSyncUpdate(this.name, this.value);
            });
        });
    }
}

// Queue synchronization update
function queueSyncUpdate(fieldName, fieldValue) {
    clearTimeout(syncTimeout);
    syncTimeout = setTimeout(() => {
        sendSyncUpdate(fieldName, fieldValue);
    }, 1000); // Debounce for 1 second
}

// Send synchronization update
function sendSyncUpdate(fieldName, fieldValue) {
    const data = {
        field: fieldName,
        value: fieldValue,
        timestamp: new Date().toISOString()
    };
    
    // Store in localStorage for cross-tab communication
    localStorage.setItem('siswa_data_update', JSON.stringify(data));
    
    // Notify other tabs/pages
    localStorage.setItem('siswa_data_update_timestamp', Date.now().toString());
}

// Listen for updates from other tabs
window.addEventListener('storage', function(e) {
    if (e.key === 'siswa_data_update' && e.newValue) {
        const updateData = JSON.parse(e.newValue);
        updateFormField(updateData.field, updateData.value);
    }
});

// Update form field
function updateFormField(fieldName, value) {
    const field = document.querySelector(`[name="${fieldName}"]`);
    if (field && field.value !== value) {
        field.value = value;
        // Add visual indicator
        field.classList.add('updated-highlight');
        setTimeout(() => {
            field.classList.remove('updated-highlight');
        }, 2000);
    }
}

// Real-time duplicate checking
let nisTimeout;
let nisnTimeout;

// Check NIS duplicate
document.getElementById('siswa_nis').addEventListener('input', function() {
    const nis = this.value.trim();
    const errorDiv = document.getElementById('nis-error');
    
    if(nis.length > 0) {
        clearTimeout(nisTimeout);
        nisTimeout = setTimeout(() => {
            checkDuplicate('nis', nis, errorDiv);
        }, 500);
    } else {
        errorDiv.textContent = '';
        this.classList.remove('is-invalid');
    }
});

// Check NISN duplicate
document.getElementById('siswa_nisn').addEventListener('input', function() {
    const nisn = this.value.trim();
    const errorDiv = document.getElementById('nisn-error');
    
    if(nisn.length > 0) {
        clearTimeout(nisnTimeout);
        nisnTimeout = setTimeout(() => {
            checkDuplicate('nisn', nisn, errorDiv);
        }, 500);
    } else {
        errorDiv.textContent = '';
        this.classList.remove('is-invalid');
    }
});

// Check name + class combination
document.getElementById('siswa_nama').addEventListener('input', function() {
    checkNameClassDuplicate();
});

document.getElementById('siswa_kelas').addEventListener('input', function() {
    checkNameClassDuplicate();
});

function checkDuplicate(type, value, errorDiv) {
    fetch('<?php echo base_url("hubin/check_duplicate/"); ?>' + type + '/' + encodeURIComponent(value))
        .then(response => response.json())
        .then(data => {
            const input = document.getElementById('siswa_' + type);
            if(data.exists) {
                errorDiv.textContent = data.message;
                input.classList.add('is-invalid');
            } else {
                errorDiv.textContent = '';
                input.classList.remove('is-invalid');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

function checkNameClassDuplicate() {
    const nama = document.getElementById('siswa_nama').value.trim();
    const kelas = document.getElementById('siswa_kelas').value.trim();
    const errorDiv = document.getElementById('nama-error');
    
    if(nama.length > 0 && kelas.length > 0) {
        fetch('<?php echo base_url("hubin/check_duplicate_name_class/"); ?>' + encodeURIComponent(nama) + '/' + encodeURIComponent(kelas))
            .then(response => response.json())
            .then(data => {
                const input = document.getElementById('siswa_nama');
                if(data.exists) {
                    errorDiv.textContent = data.message;
                    input.classList.add('is-invalid');
                } else {
                    errorDiv.textContent = '';
                    input.classList.remove('is-invalid');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }
}

// Form submission validation
document.getElementById('tambahSiswaForm').addEventListener('submit', function(e) {
    const nisInput = document.getElementById('siswa_nis');
    const nisnInput = document.getElementById('siswa_nisn');
    const namaInput = document.getElementById('siswa_nama');
    
    let hasErrors = false;
    
    // Check if there are any validation errors
    if(nisInput.classList.contains('is-invalid') || 
       nisnInput.classList.contains('is-invalid') || 
       namaInput.classList.contains('is-invalid')) {
        hasErrors = true;
    }
    
    if(hasErrors) {
        e.preventDefault();
        alert('Mohon perbaiki kesalahan pada form sebelum menyimpan.');
        return false;
    }
});

// Initialize synchronization when page loads
document.addEventListener('DOMContentLoaded', function() {
    initSynchronization();
    
    // Add sync status indicator
    const syncIndicator = document.createElement('div');
    syncIndicator.id = 'sync-status';
    syncIndicator.className = 'sync-status';
    document.body.appendChild(syncIndicator);
});
</script>

<style>
    /* Synchronization highlight */
    .updated-highlight {
        background-color: #fff3cd !important;
        border-color: #ffc107 !important;
        box-shadow: 0 0 5px rgba(255, 193, 7, 0.5);
        transition: all 0.3s ease;
    }
    
    /* Sync status indicator */
    .sync-status {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 10px 15px;
        border-radius: 5px;
        color: white;
        font-weight: bold;
        z-index: 9999;
        display: none;
    }
    
    .sync-status.syncing {
        background-color: #17a2b8;
        display: block;
    }
    
    .sync-status.synced {
        background-color: #28a745;
        display: block;
    }
    
    .sync-status.error {
        background-color: #dc3545;
        display: block;
    }
</style>