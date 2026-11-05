<?php 
// Note: All data should be passed from controller
// student_data and user data are expected to be available from controller
?>

<div class="card-mobile">
    <h5 class="mb-3"><i class="fas fa-user-circle me-2 text-primary"></i>Profile Saya</h5>
    
    <?php if(!empty($this->session->flashdata('message'))){ ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>
            <?php echo $this->session->flashdata('message'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php } ?>
    
    <?php if(!empty($this->session->flashdata('error_message'))){ ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?php echo $this->session->flashdata('error_message'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php } ?>
    
    <?php if(validation_errors()){ ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?php echo validation_errors(); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php } ?>
    
    <div class="text-center mb-4">
        <div class="sync-indicator" id="syncIndicator" style="display: none; margin-bottom: 10px;">
            <span class="badge bg-success">Data Synchronized</span>
        </div>
        <div style="width: 120px; height: 120px; margin: 0 auto; border-radius: 50%; overflow: hidden; background-color: #563b82; display: flex; align-items: center; justify-content: center; cursor: pointer; position: relative;" id="profilePhotoContainer">
            <?php if(isset($user->foto_profil) && $user->foto_profil && file_exists('./uploads/profil/'.$user->foto_profil)){ ?>
                <img src="<?php echo base_url('uploads/profil/'.$user->foto_profil) ?>" 
                     alt="Foto Profil" 
                     style="width: 100%; height: 100%; object-fit: cover;" id="profilePhotoPreview">
            <?php } else { ?>
                <img src="<?php echo base_url('assets/img/logo-sekolah.png') ?>" 
                     alt="Logo Sekolah" 
                     style="width: 100%; height: 100%; object-fit: cover;" id="profilePhotoPreview">
            <?php } ?>
            <div style="position: absolute; bottom: 0; left: 0; right: 0; background: rgba(0,0,0,0.5); color: white; text-align: center; padding: 5px; font-size: 12px;">
                <i class="fas fa-camera"></i> Ganti Foto
            </div>
        </div>
        <h5 class="mt-3 mb-1"><?php echo isset($user->nama_lengkap) ? $user->nama_lengkap : (isset($siswa->nama_lengkap) ? $siswa->nama_lengkap : 'Nama Tidak Tersedia') ?></h5>
        <p class="text-muted mb-0"><?php echo isset($user->group_name) ? $user->group_name : (isset($siswa->group_name) ? $siswa->group_name : 'Group Tidak Tersedia') ?></p>
    </div>
    
    <style>
        /* Custom modal styles to match the theme */
        .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .modal-header {
            border-radius: 15px 15px 0 0;
            padding: 15px 20px;
        }
        
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        
        .table td {
            vertical-align: middle;
        }
        
        .table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
    </style>
    
    <script>
        document.getElementById('profilePhotoContainer').addEventListener('click', function() {
            document.getElementById('foto_profil').click();
        });
        
        document.getElementById('foto_profil').addEventListener('change', function() {
            if(this.files && this.files[0]) {
                // Validate file type
                var file = this.files[0];
                var allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                var maxSize = 2 * 1024 * 1024; // 2MB in bytes
                
                if(allowedTypes.indexOf(file.type) === -1) {
                    alert('Format file tidak didukung. Gunakan JPG, PNG, atau GIF.');
                    this.value = '';
                    return;
                }
                
                if(file.size > maxSize) {
                    alert('Ukuran file terlalu besar. Maksimal 2MB.');
                    this.value = '';
                    return;
                }
                
                // Preview the image immediately
                var reader = new FileReader();
                reader.onload = function(e) {
                    var img = document.getElementById('profilePhotoPreview');
                    img.src = e.target.result;
                    
                    // Add smooth transition effect
                    img.style.opacity = '0';
                    setTimeout(function() {
                        img.style.transition = 'opacity 0.3s ease-in-out';
                        img.style.opacity = '1';
                    }, 50);
                    
                    // Remove any existing success messages
                    var existingSuccess = document.querySelector('.alert.alert-success:not([style*="display: none"])');
                    if(existingSuccess) {
                        existingSuccess.remove();
                    }
                    
                    // Show success message with file info
                    var successDiv = document.createElement('div');
                    successDiv.className = 'alert alert-success alert-dismissible fade show mt-2';
                    successDiv.innerHTML = '<i class="fas fa-check-circle me-2"></i>Foto profil "' + file.name + '" siap disimpan<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
                    document.querySelector('.card-mobile').insertBefore(successDiv, document.querySelector('form'));
                    
                    // Log to console for debugging
                    console.log('File selected:', file.name, 'Size:', file.size, 'bytes');
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
        
        // Function to get original values for comparison
        function getOriginalValues() {
            var originalData = {};
            var inputs = document.querySelectorAll('#profileForm input, #profileForm select, #profileForm textarea');
            inputs.forEach(function(input) {
                if(input.name) {
                    originalData[input.name] = input.value;
                }
            });
            return originalData;
        }
        
        // Store original values when page loads
        var originalValues = getOriginalValues();
        
        // Function to get changed values
        function getChangedValues() {
            var changedValues = {};
            var inputs = document.querySelectorAll('#profileForm input, #profileForm select, #profileForm textarea');
            inputs.forEach(function(input) {
                if(input.name && originalValues[input.name] !== undefined && originalValues[input.name] !== input.value) {
                    changedValues[input.name] = {
                        oldValue: originalValues[input.name],
                        newValue: input.value
                    };
                }
            });
            
            // Check if a new photo has been selected
            var fileInput = document.getElementById('foto_profil');
            if(fileInput.files.length > 0) {
                changedValues['foto_profil'] = {
                    oldValue: 'Current Photo',
                    newValue: fileInput.files[0].name
                };
            }
            
            return changedValues;
        }
        
        // Function to show changes in a modal
        function showChangesModal() {
            var changedValues = getChangedValues();
            
            if(Object.keys(changedValues).length === 0) {
                // If no changes, proceed with submission
                return true;
            }
            
            // Create modal HTML
            var modalHtml = `
                <div class="modal fade" id="confirmChangesModal" tabindex="-1" aria-labelledby="confirmChangesModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title" id="confirmChangesModalLabel">
                                    <i class="fas fa-exclamation-circle me-2"></i>Konfirmasi Perubahan
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Anda akan menyimpan perubahan berikut:</p>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Field</th>
                                                <th>Nilai Sebelum</th>
                                                <th>Nilai Sesudah</th>
                                            </tr>
                                        </thead>
                                        <tbody>
            `;
            
            // Define field labels for better display
            var fieldLabels = {
                'nama_lengkap': 'Nama Lengkap',
                'siswa_nis': 'NIS',
                'siswa_kelas': 'Kelas',
                'siswa_jurusan': 'Jurusan',
                'tempat_lahir': 'Tempat Lahir',
                'tanggal_lahir': 'Tanggal Lahir',
                'jenis_kelamin': 'Jenis Kelamin',
                'email': 'Email',
                'telepon': 'Telepon',
                'alamat': 'Alamat',
                'foto_profil': 'Foto Profil'
            };
            
            for(var fieldName in changedValues) {
                var fieldLabel = fieldLabels[fieldName] || fieldName;
                var oldValue = changedValues[fieldName].oldValue || '(kosong)';
                var newValue = changedValues[fieldName].newValue || '(kosong)';
                
                // Format jenis_kelamin for better display
                if(fieldName === 'jenis_kelamin') {
                    if(oldValue === 'L') oldValue = 'Laki-laki';
                    else if(oldValue === 'P') oldValue = 'Perempuan';
                    if(newValue === 'L') newValue = 'Laki-laki';
                    else if(newValue === 'P') newValue = 'Perempuan';
                }
                
                // Format date fields for better display
                if(fieldName === 'tanggal_lahir' && oldValue !== '(kosong)') {
                    var oldDate = new Date(oldValue);
                    if(oldDate instanceof Date && !isNaN(oldDate)) {
                        oldValue = oldDate.toLocaleDateString('id-ID');
                    }
                }
                if(fieldName === 'tanggal_lahir' && newValue !== '(kosong)') {
                    var newDate = new Date(newValue);
                    if(newDate instanceof Date && !isNaN(newDate)) {
                        newValue = newDate.toLocaleDateString('id-ID');
                    }
                }
                
                modalHtml += `
                    <tr>
                        <td><strong>${fieldLabel}</strong></td>
                        <td>${oldValue}</td>
                        <td>${newValue}</td>
                    </tr>
                `;
            }
            
            modalHtml += `
                                        </tbody>
                                    </table>
                                </div>
                                <div class="alert alert-info mt-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Pastikan semua informasi sudah benar sebelum menyimpan.
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i class="fas fa-times me-2"></i>Batal
                                </button>
                                <button type="button" class="btn btn-primary" id="confirmSaveBtn">
                                    <i class="fas fa-check me-2"></i>Lanjutkan Penyimpanan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Add modal to the page
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            
            // Show the modal
            var modalElement = document.getElementById('confirmChangesModal');
            var modal = new bootstrap.Modal(modalElement);
            modal.show();
            
            // Handle confirmation button
            document.getElementById('confirmSaveBtn').addEventListener('click', function() {
                modal.hide();
                // Delay form submission to allow modal to close properly
                setTimeout(function() {
                    // Re-enable submit button if it was disabled
                    var submitBtn = document.querySelector('button[type="submit"]');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Simpan Perubahan';
                    
                    // Submit the form programmatically
                    document.getElementById('profileForm').dispatchEvent(new Event('submit', { cancelable: true }));
                }, 300);
            });
            
            // Remove modal when closed
            modalElement.addEventListener('hidden.bs.modal', function () {
                modalElement.remove();
            });
            
            return false;
        }
        
        // Real-time NIS validation
        let nisTimeout;
        const nisInput = document.getElementById('siswa_nis');
        const nisValidationMessage = document.getElementById('nis-validation-message');
        
        // Real-time NISN validation
        let nisnTimeout;
        const nisnInput = document.getElementById('siswa_nisn');
        const nisnValidationMessage = document.getElementById('nisn-validation-message');
        
        // Store current user's NIS and NISN to exclude from validation
        const currentNIS = '<?php echo isset($siswa->siswa_nis) ? $siswa->siswa_nis : '' ?>';
        const currentNISN = '<?php echo isset($siswa->siswa_nisn) ? $siswa->siswa_nisn : '' ?>';
        
        if(nisInput) {
            nisInput.addEventListener('input', function() {
                const nis = this.value.trim();
                
                // Clear previous timeout
                clearTimeout(nisTimeout);
                
                // Clear previous validation message
                nisValidationMessage.style.display = 'none';
                nisValidationMessage.className = 'form-text mt-1';
                nisValidationMessage.textContent = '';
                
                // Remove validation classes
                this.classList.remove('is-valid', 'is-invalid');
                
                // Skip validation for empty values
                if(nis === '') {
                    return;
                }
                
                // Skip validation if same as current NIS
                if(nis === currentNIS) {
                    this.classList.add('is-valid');
                    nisValidationMessage.style.display = 'block';
                    nisValidationMessage.className = 'form-text mt-1 text-success';
                    nisValidationMessage.innerHTML = '<i class="fas fa-check-circle me-1"></i>NIS ini milik Anda';
                    return;
                }
                
                // Validate NIS format (numeric, 10-15 digits)
                if(!/^[0-9]{10,15}$/.test(nis)) {
                    this.classList.add('is-invalid');
                    nisValidationMessage.style.display = 'block';
                    nisValidationMessage.className = 'form-text mt-1 text-danger';
                    nisValidationMessage.innerHTML = '<i class="fas fa-exclamation-circle me-1"></i>NIS harus berupa angka 10-15 digit';
                    return;
                }
                
                // Debounce the validation request
                nisTimeout = setTimeout(() => {
                    validateNIS(nis);
                }, 500);
            });
            
            // Function to validate NIS via AJAX
            function validateNIS(nis) {
                // Show loading state
                nisInput.classList.add('is-valid');
                nisValidationMessage.style.display = 'block';
                nisValidationMessage.className = 'form-text mt-1 text-info';
                nisValidationMessage.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Memeriksa ketersediaan NIS...';
                
                // Make AJAX request
                fetch('<?php echo base_url('siswa/check_nis_availability') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        nis: nis,
                        current_nis: currentNIS
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if(data.available) {
                        nisInput.classList.remove('is-valid', 'is-invalid');
                        nisInput.classList.add('is-valid');
                        nisValidationMessage.style.display = 'block';
                        nisValidationMessage.className = 'form-text mt-1 text-success';
                        nisValidationMessage.innerHTML = '<i class="fas fa-check-circle me-1"></i>NIS tersedia';
                    } else {
                        nisInput.classList.remove('is-valid', 'is-invalid');
                        nisInput.classList.add('is-invalid');
                        nisValidationMessage.style.display = 'block';
                        nisValidationMessage.className = 'form-text mt-1 text-danger';
                        nisValidationMessage.innerHTML = '<i class="fas fa-exclamation-circle me-1"></i>' + data.message;
                    }
                })
                .catch(error => {
                    console.error('Error validating NIS:', error);
                    nisInput.classList.remove('is-valid', 'is-invalid');
                    nisValidationMessage.style.display = 'block';
                    nisValidationMessage.className = 'form-text mt-1 text-warning';
                    nisValidationMessage.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i>Gagal memeriksa NIS';
                });
            }
        }
        
        // Real-time NISN validation
        if(nisnInput) {
            nisnInput.addEventListener('input', function() {
                const nisn = this.value.trim();
                
                // Clear previous timeout
                clearTimeout(nisnTimeout);
                
                // Clear previous validation message
                nisnValidationMessage.style.display = 'none';
                nisnValidationMessage.className = 'form-text mt-1';
                nisnValidationMessage.textContent = '';
                
                // Remove validation classes
                this.classList.remove('is-valid', 'is-invalid');
                
                // Skip validation for empty values (NISN is optional)
                if(nisn === '') {
                    return;
                }
                
                // Skip validation if same as current NISN
                if(nisn === currentNISN) {
                    this.classList.add('is-valid');
                    nisnValidationMessage.style.display = 'block';
                    nisnValidationMessage.className = 'form-text mt-1 text-success';
                    nisnValidationMessage.innerHTML = '<i class="fas fa-check-circle me-1"></i>NISN ini milik Anda';
                    return;
                }
                
                // Validate NISN format (numeric, 10 digits)
                if(!/^[0-9]{10}$/.test(nisn)) {
                    this.classList.add('is-invalid');
                    nisnValidationMessage.style.display = 'block';
                    nisnValidationMessage.className = 'form-text mt-1 text-danger';
                    nisnValidationMessage.innerHTML = '<i class="fas fa-exclamation-circle me-1"></i>NISN harus berupa angka 10 digit';
                    return;
                }
                
                // Debounce the validation request
                nisnTimeout = setTimeout(() => {
                    validateNISN(nisn);
                }, 500);
            });
            
            // Function to validate NISN via AJAX
            function validateNISN(nisn) {
                // Show loading state
                nisnInput.classList.add('is-valid');
                nisnValidationMessage.style.display = 'block';
                nisnValidationMessage.className = 'form-text mt-1 text-info';
                nisnValidationMessage.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Memeriksa ketersediaan NISN...';
                
                // Make AJAX request
                fetch('<?php echo base_url('siswa/check_nisn_availability') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        nisn: nisn,
                        current_nisn: currentNISN
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if(data.available) {
                        nisnInput.classList.remove('is-valid', 'is-invalid');
                        nisnInput.classList.add('is-valid');
                        nisnValidationMessage.style.display = 'block';
                        nisnValidationMessage.className = 'form-text mt-1 text-success';
                        nisnValidationMessage.innerHTML = '<i class="fas fa-check-circle me-1"></i>NISN tersedia';
                    } else {
                        nisnInput.classList.remove('is-valid', 'is-invalid');
                        nisnInput.classList.add('is-invalid');
                        nisnValidationMessage.style.display = 'block';
                        nisnValidationMessage.className = 'form-text mt-1 text-danger';
                        nisnValidationMessage.innerHTML = '<i class="fas fa-exclamation-circle me-1"></i>' + data.message;
                    }
                })
                .catch(error => {
                    console.error('Error validating NISN:', error);
                    nisnInput.classList.remove('is-valid', 'is-invalid');
                    nisnValidationMessage.style.display = 'block';
                    nisnValidationMessage.className = 'form-text mt-1 text-warning';
                    nisnValidationMessage.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i>Gagal memeriksa NISN';
                });
            }
        }
        
        // Add form submit handler for confirmation
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            // Check if NIS is valid before submission
            const nisInput = document.getElementById('siswa_nis');
            if(nisInput && nisInput.classList.contains('is-invalid')) {
                e.preventDefault();
                alert('Perbaiki kesalahan NIS sebelum menyimpan.');
                nisInput.focus();
                return false;
            }
            
            // Check if NISN is valid before submission (if NISN field exists and has value)
            const nisnInput = document.getElementById('siswa_nisn');
            if(nisnInput && nisnInput.value.trim() !== '' && nisnInput.classList.contains('is-invalid')) {
                e.preventDefault();
                alert('Perbaiki kesalahan NISN sebelum menyimpan.');
                nisnInput.focus();
                return false;
            }
            
            e.preventDefault(); // Prevent default submission
            
            // Show changes modal and return early
            showChangesModal();
            
            return false;
        });
        
        // Data synchronization for profile page
        (function() {
            const userCode = '<?php echo $user->user_code; ?>';
            let syncCheckInterval;
            
            // Initialize synchronization
            function initSync() {
                // Listen for localStorage changes
                window.addEventListener('storage', function(e) {
                    if (e.key === 'siswa_data_update' && e.newValue) {
                        const updateData = JSON.parse(e.newValue);
                        updateProfileField(updateData.field, updateData.value);
                    }
                });
                
                // Periodic sync check
                syncCheckInterval = setInterval(checkForUpdates, 3000);
                
                // Show sync indicator
                document.getElementById('syncIndicator').style.display = 'block';
            }
            
            // Update profile field
            function updateProfileField(fieldName, value) {
                // Map field names to form elements
                const fieldMap = {
                    'nama_lengkap': 'nama_lengkap',
                    'siswa_nis': 'siswa_nis',
                    'siswa_nisn': 'siswa_nisn',
                    'siswa_kelas': 'siswa_kelas',
                    'siswa_jurusan': 'siswa_jurusan',
                    'email': 'email',
                    'telepon': 'telepon',
                    'alamat': 'alamat',
                    'tempat_lahir': 'tempat_lahir',
                    'tanggal_lahir': 'tanggal_lahir',
                    'jenis_kelamin': 'jenis_kelamin'
                };
                
                if (fieldMap[fieldName]) {
                    const field = document.querySelector(`[name="${fieldMap[fieldName]}"]`);
                    if (field && field.value !== value) {
                        field.value = value;
                        // Add visual indicator
                        field.classList.add('updated-highlight');
                        setTimeout(() => {
                            field.classList.remove('updated-highlight');
                        }, 2000);
                    }
                }
            }
            
            // Check for updates from other pages
            function checkForUpdates() {
                fetch('<?php echo base_url('data_sync/check_updates'); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        user_code: userCode
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success' && data.updates.length > 0) {
                        data.updates.forEach(update => {
                            updateProfileField(update.updated_fields.field, update.updated_fields.value);
                        });
                        // Show sync success indicator
                        showSyncNotification('Data updated from other source');
                    }
                })
                .catch(error => {
                    console.error('Sync check error:', error);
                });
            }
            
            // Show sync notification
            function showSyncNotification(message) {
                const notification = document.createElement('div');
                notification.className = 'alert alert-info alert-dismissible fade show mt-2';
                notification.innerHTML = `
                    <i class="fas fa-sync me-2"></i>${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.querySelector('.card-mobile').insertBefore(notification, document.querySelector('form'));
                
                // Auto remove after 3 seconds
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.remove();
                    }
                }, 3000);
            }
            
            // Start synchronization when page loads
            document.addEventListener('DOMContentLoaded', initSync);
            
            // Cleanup on page unload
            window.addEventListener('beforeunload', function() {
                if (syncCheckInterval) {
                    clearInterval(syncCheckInterval);
                }
            });
        })();
    </script>
    

    
    <form action="<?php echo base_url('siswa/update_profile') ?>" method="post" enctype="multipart/form-data" id="profileForm">
        <!-- Hidden file input for photo upload -->
        <input type="file" name="foto_profil" id="foto_profil" class="form-control" accept="image/*" style="display: none;">
        
        <div class="card shadow mt-3">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" class="form-control" 
                           value="<?php echo isset($user->nama_lengkap) ? $user->nama_lengkap : (isset($siswa->nama_lengkap) ? $siswa->nama_lengkap : '') ?>" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">NIS</label>
                    <input type="text" name="siswa_nis" id="siswa_nis" class="form-control" 
                           value="<?php echo isset($siswa->siswa_nis) ? $siswa->siswa_nis : '' ?>"
                           placeholder="Masukkan NIS">
                    <div id="nis-validation-message" class="form-text mt-1" style="display: none;"></div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">NISN</label>
                    <input type="text" name="siswa_nisn" id="siswa_nisn" class="form-control" 
                           value="<?php echo isset($siswa->siswa_nisn) ? $siswa->siswa_nisn : '' ?>"
                           placeholder="Masukkan NISN (opsional)">
                    <div id="nisn-validation-message" class="form-text mt-1" style="display: none;"></div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Kelas</label>
                    <input type="text" name="siswa_kelas" class="form-control" 
                           value="<?php echo isset($siswa->siswa_kelas) ? $siswa->siswa_kelas : '' ?>" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Jurusan</label>
                    <input type="text" name="siswa_jurusan" class="form-control" 
                           value="<?php echo isset($siswa->siswa_jurusan) ? $siswa->siswa_jurusan : '' ?>">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" class="form-control" 
                           value="<?php echo isset($user->tempat_lahir) ? $user->tempat_lahir : (isset($siswa->tempat_lahir) ? $siswa->tempat_lahir : '') ?>">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" class="form-control" 
                           value="<?php echo isset($user->tanggal_lahir) ? $user->tanggal_lahir : (isset($siswa->tanggal_lahir) ? $siswa->tanggal_lahir : '') ?>">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-control">
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="L" <?php echo (isset($user->jenis_kelamin) && ($user->jenis_kelamin == 'L' || $user->jenis_kelamin == 'Laki-laki')) ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="P" <?php echo (isset($user->jenis_kelamin) && ($user->jenis_kelamin == 'P' || $user->jenis_kelamin == 'Perempuan')) ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" 
                           value="<?php echo isset($user->email) ? $user->email : (isset($siswa->email) ? $siswa->email : '') ?>">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Telepon</label>
                    <input type="text" name="telepon" class="form-control" 
                           value="<?php echo isset($user->telepon) ? $user->telepon : (isset($siswa->telepon) ? $siswa->telepon : '') ?>">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Alamat</label>
                    <textarea name="alamat" class="form-control" rows="3"><?php echo isset($user->alamat) ? $user->alamat : (isset($siswa->alamat) ? $siswa->alamat : '') ?></textarea>
                </div>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary btn-mobile">
            <i class="fas fa-save me-2"></i>Simpan Perubahan
        </button>
        <a href="<?php echo base_url('siswa/view/change-password') ?>" class="btn btn-warning btn-mobile">
            <i class="fas fa-key me-2"></i>Ubah Password
        </a>
    </form>
</div>

