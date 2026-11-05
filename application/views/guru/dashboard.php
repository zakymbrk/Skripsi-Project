<!-- Header Dashboard Guru -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="mb-0 fs-4 fw-bold text-dark"><i class="fas fa-chalkboard-teacher text-primary me-2"></i>Dashboard Guru Pembimbing</h1>
        <p class="text-muted mb-0 small">Selamat datang, <strong><?php echo $this->session->userdata('userdata')['nama_lengkap']; ?></strong> | Portal Monitoring PKL SMK ITIKURIH HIBARNA</p>
    </div>
    <div>
        <span class="badge bg-primary px-3 py-2 fs-6">
            <i class="fas fa-calendar-alt me-1"></i> <?php echo date('d F Y'); ?>
        </span>
    </div>
</div>

<!-- Alert Notifikasi -->
<?php if($this->session->flashdata('message')): ?>
<div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
    <i class="fas fa-check-circle me-2"></i> <?php echo $this->session->flashdata('message'); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 rounded-4 shadow-sm bg-white p-3 stat-card-modern h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold">Siswa Dibimbing</span>
                    <h2 class="fw-bold text-dark mb-0 mt-1"><?php echo $total_siswa; ?></h2>
                    <small class="text-muted">Total anak bimbingan</small>
                </div>
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3">
                    <i class="fas fa-user-graduate fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 rounded-4 shadow-sm bg-white p-3 stat-card-modern h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold">Hadir Hari Ini</span>
                    <h2 class="fw-bold text-success mb-0 mt-1"><?php echo $total_hadir_hari_ini; ?></h2>
                    <small class="text-muted">Dari <?php echo $total_siswa; ?> siswa</small>
                </div>
                <div class="bg-success bg-opacity-10 text-success rounded-circle p-3">
                    <i class="fas fa-calendar-check fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 rounded-4 shadow-sm bg-white p-3 stat-card-modern h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold">Logbook Tercatat</span>
                    <h2 class="fw-bold text-info mb-0 mt-1"><?php echo $total_logbook; ?></h2>
                    <small class="text-muted">Total catatan aktivitas</small>
                </div>
                <div class="bg-info bg-opacity-10 text-info rounded-circle p-3">
                    <i class="fas fa-book-reader fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 rounded-4 shadow-sm bg-white p-3 stat-card-modern h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold">Logbook Valid</span>
                    <h2 class="fw-bold text-success mb-0 mt-1"><?php echo isset($total_logbook_valid) ? $total_logbook_valid : 0; ?></h2>
                    <small class="text-muted">Tidak valid: <?php echo isset($total_logbook_tidak_valid) ? $total_logbook_tidak_valid : 0; ?></small>
                </div>
                <div class="bg-success bg-opacity-10 text-success rounded-circle p-3">
                    <i class="fas fa-check-double fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Section Presensi Hari Ini & Logbook Terbaru -->
<div class="row g-4 mb-4">
    <!-- Presensi Hari Ini -->
    <div class="col-md-6">
        <div class="card border-0 rounded-4 shadow-sm h-100">
            <div class="card-header bg-white py-3 px-4 border-0 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fs-6 fw-bold text-white"><i class="fas fa-user-clock text-primary me-2"></i>Presensi Hari Ini (<?php echo date('d M Y'); ?>)</h5>
                <a href="<?php echo base_url('guru/view/monitoring-presensi'); ?>" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Siswa</th>
                                <th>Masuk</th>
                                <th>Pulang</th>
                                <th>Status</th>
                                <th class="pe-4 text-center">Foto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($presensi_hari_ini)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Belum ada siswa bimbingan yang presensi hari ini.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($presensi_hari_ini as $p): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <strong><?php echo $p->siswa_nama; ?></strong><br>
                                            <small class="text-muted"><?php echo $p->siswa_kelas; ?> | <?php echo $p->dudi_nama ?: '-'; ?></small>
                                        </td>
                                        <td><span class="badge bg-light text-dark border"><?php echo $p->jam_masuk ?: '-'; ?></span></td>
                                        <td><span class="badge bg-light text-dark border"><?php echo $p->jam_pulang ?: 'Belum'; ?></span></td>
                                        <td>
                                            <?php if($p->status_kehadiran == 'hadir'): ?>
                                                <span class="badge bg-success">Hadir</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark"><?php echo ucfirst($p->status_kehadiran); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="pe-4 text-center">
                                            <?php if(!empty($p->foto_masuk) && file_exists('./uploads/presensi/'.$p->foto_masuk)): ?>
                                                <a href="<?php echo base_url('uploads/presensi/'.$p->foto_masuk); ?>" target="_blank" class="btn btn-sm btn-light rounded-circle">
                                                    <i class="fas fa-image text-primary"></i>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Logbook Terbaru -->
    <div class="col-md-6">
        <div class="card border-0 rounded-4 shadow-sm h-100">
            <div class="card-header bg-white py-3 px-4 border-0 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fs-6 fw-bold text-white"><i class="fas fa-clipboard-list text-primary me-2"></i>Logbook Aktivitas Terbaru</h5>
                <a href="<?php echo base_url('guru/view/monitoring-logbook'); ?>" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Siswa</th>
                                <th>Tanggal</th>
                                <th>Deskripsi</th>
                                <th class="pe-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($logbook_terbaru)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Belum ada catatan logbook dari siswa bimbingan.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach(array_slice($logbook_terbaru, 0, 5) as $l): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <strong><?php echo $l->siswa_nama; ?></strong><br>
                                            <small class="text-muted"><?php echo $l->siswa_kelas; ?></small>
                                        </td>
                                        <td><small class="text-muted"><?php echo date('d/m/Y', strtotime($l->tanggal)); ?></small></td>
                                        <td>
                                            <p class="mb-0 small text-truncate" style="max-width: 200px;">
                                                <?php echo htmlspecialchars($l->deskripsi_kegiatan); ?>
                                            </p>
                                        </td>
                                        <td class="pe-4 text-center">
                                            <a href="<?php echo base_url('guru/view/monitoring-logbook'); ?>" class="btn btn-sm btn-light rounded-circle" title="Review Logbook">
                                                <i class="fas fa-eye text-primary"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
