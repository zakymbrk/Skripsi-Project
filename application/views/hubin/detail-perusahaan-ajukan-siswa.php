<?php if(!isset($siswa) || !isset($proposed_company_name)): ?>
<div class="alert alert-danger">
    <i class="fas fa-exclamation-circle me-2"></i>
    Data perusahaan tidak ditemukan.
</div>
<?php else: ?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo base_url('hubin/view') ?>"><i class="fas fa-home"></i> Home</a></li>
        <li class="breadcrumb-item"><a href="<?php echo base_url('hubin/view/pengajuan') ?>">Pengajuan PKL</a></li>
        <li class="breadcrumb-item active">Detail Perusahaan Ajukan Siswa</li>
    </ol>
</nav>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="fas fa-building me-2"></i>Detail Perusahaan Ajukan Siswa</h2>
</div>

<div class="row">
    <!-- Student Info -->
    <div class="col-md-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-user-graduate me-2"></i>Informasi Siswa</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless mb-0">
                        <tbody>
                            <tr>
                                <td class="fw-bold bg-light" width="40%">Nama Siswa</td>
                                <td><?php echo $siswa->siswa_nama ?></td>
                            </tr>
                            <tr>
                                <td class="fw-bold bg-light">Kelas</td>
                                <td><?php echo $siswa->siswa_kelas ?></td>
                            </tr>
                            <tr>
                                <td class="fw-bold bg-light">Jurusan</td>
                                <td><?php echo $siswa->siswa_jurusan ? $siswa->siswa_jurusan : '-' ?></td>
                            </tr>
                            <tr>
                                <td class="fw-bold bg-light">Telepon</td>
                                <td><?php echo $siswa->siswa_telepon ?></td>
                            </tr>
                            <tr>
                                <td class="fw-bold bg-light">Alamat</td>
                                <td><?php echo isset($siswa->alamat) ? $siswa->alamat : (isset($siswa->siswa_alamat) ? $siswa->siswa_alamat : '-') ?></td>
                            </tr>
                            <tr>
                                <td class="fw-bold bg-light">Status Pengajuan</td>
                                <td>
                                    <?php 
                                    $badge_class = 'bg-info';
                                    if($siswa->status_pengajuan == 'disetujui') $badge_class = 'bg-success';
                                    if($siswa->status_pengajuan == 'ditolak') $badge_class = 'bg-danger';
                                    if($siswa->status_pengajuan == 'menunggu') $badge_class = 'bg-warning';
                                    ?>
                                    <span class="badge <?php echo $badge_class; ?>">
                                        <?php echo ucfirst($siswa->status_pengajuan); ?>
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Proposed Company Info -->
    <div class="col-md-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="fas fa-building me-2"></i>Perusahaan yang Diajukan</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless mb-0">
                        <tbody>
                            <tr>
                                <td class="fw-bold bg-light" width="40%">Nama Perusahaan</td>
                                <td>
                                    <span class="text-primary fw-bold">[PERUSAHAAN AJUKAN SISWA]</span>
                                    <br><?php echo $proposed_company_name ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold bg-light">Status</td>
                                <td>
                                    <span class="badge bg-warning">Belum Ada dalam Database</span>
                                    <br><small class="text-muted">Perlu ditambahkan ke sistem</small>
                                </td>
                            </tr>
                            
                            <?php if($siswa->other_dudi_alamat): ?>
                            <tr>
                                <td class="fw-bold bg-light">Alamat</td>
                                <td><?php echo $siswa->other_dudi_alamat ?></td>
                            </tr>
                            <?php endif; ?>
                            
                            <?php if($siswa->other_dudi_telepon): ?>
                            <tr>
                                <td class="fw-bold bg-light">Telepon</td>
                                <td><?php echo $siswa->other_dudi_telepon ?></td>
                            </tr>
                            <?php endif; ?>
                            
                            <?php if($siswa->other_dudi_email): ?>
                            <tr>
                                <td class="fw-bold bg-light">Email</td>
                                <td><?php echo $siswa->other_dudi_email ?></td>
                            </tr>
                            <?php endif; ?>

                            <?php if($siswa->other_dudi_latitude !== null && $siswa->other_dudi_longitude !== null): ?>
                            <tr>
                                <td class="fw-bold bg-light">Koordinat GPS</td>
                                <td>
                                    Latitude: <?php echo $siswa->other_dudi_latitude ?><br>
                                    Longitude: <?php echo $siswa->other_dudi_longitude ?>
                                </td>
                            </tr>
                            <?php endif; ?>
                            
                            <?php if($siswa->other_dudi_pic): ?>
                            <tr>
                                <td class="fw-bold bg-light">PIC</td>
                                <td>
                                    <?php echo $siswa->other_dudi_pic ?>
                                    <?php if($siswa->other_dudi_nip_pic): ?>
                                    <br><small class="text-muted">NIP: <?php echo $siswa->other_dudi_nip_pic ?></small>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endif; ?>
                            
                            <?php if($siswa->other_dudi_instruktur): ?>
                            <tr>
                                <td class="fw-bold bg-light">Instruktur</td>
                                <td>
                                    <?php echo $siswa->other_dudi_instruktur ?>
                                    <?php if($siswa->other_dudi_nip_instruktur): ?>
                                    <br><small class="text-muted">NIP: <?php echo $siswa->other_dudi_nip_instruktur ?></small>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endif; ?>
                            
                            
                            <tr>
                                <td class="fw-bold bg-light">Tanggal Pengajuan</td>
                                <td><?php echo $siswa->created_at ? date('d F Y H:i', strtotime($siswa->created_at)) : '-' ?></td>
                            </tr>
                            <?php if($siswa->updated_at): ?>
                            <tr>
                                <td class="fw-bold bg-light">Terakhir Update</td>
                                <td><?php echo date('d F Y H:i', strtotime($siswa->updated_at)) ?></td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="card shadow-sm">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0"><i class="fas fa-tasks me-2"></i>Aksi yang Tersedia</h5>
    </div>
    <div class="card-body">
        <div class="d-flex flex-wrap gap-2">
            <?php if($siswa->status_pengajuan == 'menunggu'): ?>
                <!-- Approve as Mitra -->
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveMitraModal">
                    <i class="fas fa-check-circle me-2"></i>Setujui & Tambah sebagai Mitra
                </button>
                
                <!-- Approve as Non-Mitra -->
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#approveNonMitraModal">
                    <i class="fas fa-check me-2"></i>Setujui & Tambah sebagai Non-Mitra
                </button>
                
                <!-- Reject -->
                <a href="<?php echo base_url('hubin/tolak_pengajuan/'.$siswa->siswa_code) ?>" 
                   onclick="return confirm('Apakah Anda yakin ingin menolak pengajuan ini?\nPerusahaan <?php echo $proposed_company_name; ?> tidak akan ditambahkan ke database.')" 
                   class="btn btn-danger">
                    <i class="fas fa-times-circle me-2"></i>Tolak Pengajuan
                </a>
            <?php else: ?>
                <div class="alert alert-info w-100">
                    <i class="fas fa-info-circle me-2"></i>
                    Pengajuan ini sudah <?php echo $siswa->status_pengajuan == 'disetujui' ? 'disetujui' : 'ditolak'; ?>.
                </div>
            <?php endif; ?>
            
            <!-- Back Button -->

        </div>
    </div>
</div>

<?php endif; ?>

<!-- Modal Approve as Mitra -->
<div class="modal fade" id="approveMitraModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo base_url('hubin/process_proposed_company') ?>" method="post">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fas fa-check-circle me-2"></i>Setujui sebagai Mitra</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="siswa_code" value="<?php echo $siswa->siswa_code ?>">
                    <input type="hidden" name="proposed_company_name" value="<?php echo $proposed_company_name ?>">
                    <input type="hidden" name="action" value="add_as_mitra">
                    
                    <div class="alert alert-success">
                        <i class="fas fa-info-circle me-2"></i>
                        Anda akan menyetujui pengajuan dan menambahkan <strong><?php echo $proposed_company_name ?></strong> sebagai perusahaan mitra resmi.
                    </div>
                    
                    <p class="text-muted">Perusahaan akan ditambahkan ke sistem sebagai mitra dan pengajuan siswa akan disetujui.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check-circle me-2"></i>Setujui & Tambah sebagai Mitra
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Approve as Non-Mitra -->
<div class="modal fade" id="approveNonMitraModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo base_url('hubin/process_proposed_company') ?>" method="post">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-check me-2"></i>Setujui sebagai Non-Mitra</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="siswa_code" value="<?php echo $siswa->siswa_code ?>">
                    <input type="hidden" name="proposed_company_name" value="<?php echo $proposed_company_name ?>">
                    <input type="hidden" name="action" value="add_as_nonmitra">
                    
                    <div class="alert alert-primary">
                        <i class="fas fa-info-circle me-2"></i>
                        Anda akan menyetujui pengajuan dan menambahkan <strong><?php echo $proposed_company_name ?></strong> sebagai perusahaan non-mitra.
                    </div>
                    
                    <p class="text-muted">Perusahaan akan ditambahkan ke sistem sebagai non-mitra dan pengajuan siswa akan disetujui.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check me-2"></i>Setujui & Tambah sebagai Non-Mitra
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>