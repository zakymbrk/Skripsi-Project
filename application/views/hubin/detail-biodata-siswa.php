<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo base_url('hubin/view') ?>"><i class="fas fa-home"></i> Home</a></li>
        <li class="breadcrumb-item"><a href="<?php echo base_url('hubin/view/daftar-siswa') ?>">Daftar Siswa</a></li>
        <li class="breadcrumb-item active">Detail Biodata Siswa</li>
    </ol>
</nav>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Detail Biodata Siswa</h2>
</div>

<div class="card fade-in">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-info-circle me-2"></i>Informasi Siswa
        </h5>
    </div>
    <div class="card-body">
        <!-- Student Data Section -->
        <h5 class="mb-3"><i class="fas fa-user-graduate me-2"></i>Data Siswa</h5>
        <div class="table-responsive">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <td width="30%" class="fw-bold bg-light">Nama Siswa</td>
                        <td><?php echo isset($biodata->siswa_nama) && $biodata->siswa_nama ? $biodata->siswa_nama : (isset($biodata->user_nama) && $biodata->user_nama ? $biodata->user_nama : (isset($biodata->nama_lengkap) ? $biodata->nama_lengkap : '-')) ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">NIS (Nomor Induk Siswa)</td>
                        <td><?php echo isset($biodata->siswa_nis) && $biodata->siswa_nis ? $biodata->siswa_nis : (isset($biodata->user_nip_nim) && $biodata->user_nip_nim ? $biodata->user_nip_nim : (isset($biodata->nip_nim) ? $biodata->nip_nim : '-')) ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">NISN (Nomor Induk Siswa Nasional)</td>
                        <td><?php echo isset($biodata->siswa_nisn) && $biodata->siswa_nisn ? $biodata->siswa_nisn : '-' ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">Jenis Kelamin</td>
                        <td>
                            <?php 
                            $jenis_kelamin = isset($biodata->siswa_jk) && !empty($biodata->siswa_jk) ? $biodata->siswa_jk : (isset($biodata->user_jenis_kelamin) && !empty($biodata->user_jenis_kelamin) ? $biodata->user_jenis_kelamin : (isset($biodata->jenis_kelamin) ? $biodata->jenis_kelamin : ''));
                            
                            if(!empty($jenis_kelamin)) {
                                if ($jenis_kelamin == 'L' || $jenis_kelamin == 'Laki-laki') {
                                    echo 'Laki-laki';
                                } elseif ($jenis_kelamin == 'P' || $jenis_kelamin == 'Perempuan') {
                                    echo 'Perempuan';
                                } else {
                                    echo $jenis_kelamin;
                                }
                            } else {
                                echo '-';
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">Tempat Lahir</td>
                        <td><?php echo isset($biodata->siswa_tempat_lahir) && $biodata->siswa_tempat_lahir ? $biodata->siswa_tempat_lahir : (isset($biodata->user_tempat_lahir) && $biodata->user_tempat_lahir ? $biodata->user_tempat_lahir : (isset($biodata->tempat_lahir) ? $biodata->tempat_lahir : '-')) ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">Tanggal Lahir</td>
                        <td><?php 
                            $tgl_lahir = isset($biodata->siswa_tanggal_lahir) && $biodata->siswa_tanggal_lahir ? $biodata->siswa_tanggal_lahir : (isset($biodata->user_tanggal_lahir) && $biodata->user_tanggal_lahir ? $biodata->user_tanggal_lahir : (isset($biodata->tanggal_lahir) ? $biodata->tanggal_lahir : ''));
                            echo ($tgl_lahir && $tgl_lahir != '0000-00-00') ? date('d F Y', strtotime($tgl_lahir)) : '-';
                        ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">Kelas</td>
                        <td><?php echo isset($biodata->siswa_kelas) && $biodata->siswa_kelas ? $biodata->siswa_kelas : '-' ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">Jurusan</td>
                        <td><?php echo isset($biodata->siswa_jurusan) && $biodata->siswa_jurusan ? $biodata->siswa_jurusan : '-' ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">Sekolah</td>
                        <td><?php echo isset($biodata->siswa_asal_sekolah) && $biodata->siswa_asal_sekolah ? $biodata->siswa_asal_sekolah : 'SMK ITIKURIH HIBARNA' ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">Telepon Siswa</td>
                        <td><?php echo isset($biodata->siswa_telepon) && $biodata->siswa_telepon ? $biodata->siswa_telepon : (isset($biodata->user_telepon) && $biodata->user_telepon ? $biodata->user_telepon : (isset($biodata->telepon) ? $biodata->telepon : '-')) ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">Alamat Lengkap</td>
                        <td><?php echo isset($biodata->siswa_alamat) && $biodata->siswa_alamat ? $biodata->siswa_alamat : (isset($biodata->user_alamat) && $biodata->user_alamat ? $biodata->user_alamat : (isset($biodata->alamat) ? $biodata->alamat : '-')) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- User Account Section -->
        <h5 class="mb-3 mt-4"><i class="fas fa-user me-2"></i>Akun Login Siswa</h5>
        <div class="table-responsive">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <td width="30%" class="fw-bold bg-light">Username</td>
                        <td><?php echo $password_info['username'] ? $password_info['username'] : '-' ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">Email</td>
                        <td><?php echo isset($biodata->user_email) && $biodata->user_email ? $biodata->user_email : (isset($biodata->email) && $biodata->email ? $biodata->email : '-') ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">Level User</td>
                        <td>
                            <span class="badge bg-primary"><?php echo $password_info['group_name'] ? $password_info['group_name'] : '-' ?></span>
                            <?php if(isset($password_info['is_active']) && $password_info['is_active'] == 1): ?>
                                <span class="badge bg-success ms-2">Aktif</span>
                            <?php else: ?>
                                <span class="badge bg-danger ms-2">Non-Aktif</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- PKL Status Section -->
        <h5 class="mb-3 mt-4"><i class="fas fa-file-alt me-2"></i>Status Pengajuan PKL</h5>
        <div class="table-responsive">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <td width="30%" class="fw-bold bg-light">Status Pengajuan PKL</td>
                        <td>
                            <?php 
                            $badge_class = 'bg-info';
                            if(isset($biodata->status_pengajuan) && $biodata->status_pengajuan == 'disetujui') $badge_class = 'bg-success';
                            if(isset($biodata->status_pengajuan) && $biodata->status_pengajuan == 'ditolak') $badge_class = 'bg-danger';
                            if(isset($biodata->status_pengajuan) && $biodata->status_pengajuan == 'menunggu') $badge_class = 'bg-warning';
                            if(isset($biodata->status_pengajuan) && $biodata->status_pengajuan == 'draft') $badge_class = 'bg-secondary';
                            ?>
                            <span class="badge <?php echo $badge_class; ?>">
                                <?php echo isset($biodata->status_pengajuan) ? ucfirst($biodata->status_pengajuan) : '-' ?>
                            </span>
                        </td>
                    </tr>
                    <?php if(isset($biodata->dudi_nama) && $biodata->dudi_nama): ?>
                    <tr>
                        <td class="fw-bold bg-light">Perusahaan (DUDI)</td>
                        <td>
                            <?php echo $biodata->dudi_nama; ?>
                            <?php if(isset($biodata->dudi_is_mitra)): ?>
                                <?php if($biodata->dudi_is_mitra == 1): ?>
                                    <span class="badge bg-success ms-2">Mitra</span>
                                <?php else: ?>
                                    <span class="badge bg-warning ms-2">Non-Mitra</span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php if(isset($biodata->lama_pelaksanaan) && $biodata->lama_pelaksanaan): ?>
                    <tr>
                        <td class="fw-bold bg-light">Lama Pelaksanaan</td>
                        <td><?php echo $biodata->lama_pelaksanaan ?> hari</td>
                    </tr>
                    <?php endif; ?>
                    <?php if(isset($biodata->periode) && $biodata->periode){ ?>
                    <tr>
                        <td class="fw-bold bg-light">Periode PKL</td>
                        <td><?php echo $biodata->periode ?></td>
                    </tr>
                    <?php } ?>
                    <?php if(isset($biodata->tanggal_mulai) && $biodata->tanggal_mulai): ?>
                    <tr>
                        <td class="fw-bold bg-light">Tanggal Mulai PKL</td>
                        <td><?php echo date('d F Y', strtotime($biodata->tanggal_mulai)) ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if(isset($biodata->tanggal_selesai) && $biodata->tanggal_selesai): ?>
                    <tr>
                        <td class="fw-bold bg-light">Tanggal Selesai PKL</td>
                        <td><?php echo date('d F Y', strtotime($biodata->tanggal_selesai)) ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if(isset($biodata->updated_at) && $biodata->updated_at): ?>
                    <tr>
                        <td class="fw-bold bg-light">Terakhir Diubah</td>
                        <td><?php echo isset($biodata->updated_at) ? date('d F Y H:i:s', strtotime($biodata->updated_at)) : '-' ?></td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
                
        <div class="mt-4">
            <div class="btn-group" role="group">
                <?php if($biodata->siswa_id): ?>
                <a href="<?php echo base_url('hubin/view/id-card-siswa/' . $biodata->siswa_id) ?>" class="btn btn-success">
                    <i class="fas fa-id-card me-2"></i>Cetak ID Card
                </a>
                <?php endif; ?>
                <a href="<?php echo base_url('hubin/print_biodata_siswa/' . (isset($biodata->user_code) ? $biodata->user_code : $user->user_code)) ?>" target="_blank" class="btn btn-info">
                    <i class="fas fa-print me-2"></i>Cetak Biodata
                </a>
                <a href="<?php echo base_url('hubin/edit_biodata_siswa/' . (isset($biodata->user_code) ? $biodata->user_code : $user->user_code)) ?>" class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i>Edit Biodata
                </a>
                
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                    <i class="fas fa-lock me-2"></i>Ubah Password
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo base_url('hubin/change_password_from_detail/' . $user->user_code) ?>" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">
                        <i class="fas fa-lock me-2"></i>Ubah Password
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="password_baru" class="form-label">Password Baru <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password_baru" name="password_baru" required minlength="6">
                        <div class="form-text">Minimal 6 karakter</div>
                    </div>
                    <div class="mb-3">
                        <label for="password_konfirmasi" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password_konfirmasi" name="password_konfirmasi" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Ubah Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Data synchronization for detail biodata page
(function() {
    const userCode = '<?php echo $user->user_code; ?>';
    let syncCheckInterval;
    
    // Initialize synchronization
    function initSync() {
        // Listen for localStorage changes
        window.addEventListener('storage', function(e) {
            if (e.key === 'siswa_data_update' && e.newValue) {
                const updateData = JSON.parse(e.newValue);
                updateBiodataField(updateData.field, updateData.value);
            }
        });
        
        // Periodic sync check
        syncCheckInterval = setInterval(checkForUpdates, 5000);
        
        // Show sync indicator
        document.getElementById('syncIndicator').style.display = 'block';
    }
    
    // Update biodata field
    function updateBiodataField(fieldName, value) {
        // Map field names to display elements
        const fieldMap = {
            'siswa_nama': 'Nama Siswa',
            'siswa_nis': 'NIS (Nomor Induk Siswa)',
            'siswa_nisn': 'NISN (Nomor Induk Siswa Nasional)',
            'nama_lengkap': 'Nama Siswa'
        };
        
        if (fieldMap[fieldName]) {
            // Find and update the corresponding table cell
            const table = document.querySelector('.table-bordered');
            if (table) {
                const rows = table.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    const headerCell = row.querySelector('td:first-child');
                    const valueCell = row.querySelector('td:last-child');
                    
                    if (headerCell && headerCell.textContent.includes(fieldMap[fieldName]) && valueCell) {
                        // Update value with animation
                        valueCell.classList.add('updated-highlight');
                        valueCell.innerHTML = value || '-';
                        setTimeout(() => {
                            valueCell.classList.remove('updated-highlight');
                        }, 2000);
                    }
                });
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
                    updateBiodataField(update.updated_fields.field, update.updated_fields.value);
                });
            }
        })
        .catch(error => {
            console.error('Sync check error:', error);
        });
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

<style>
.updated-highlight {
    background-color: #d4edda !important;
    transition: background-color 0.5s ease;
}

.sync-indicator {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}
</style>