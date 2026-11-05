<!-- Header Siswa Bimbingan -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="mb-0 fs-4 fw-bold text-dark"><i class="fas fa-users text-primary me-2"></i>Daftar Siswa Bimbingan</h1>
        <p class="text-muted mb-0 small">Daftar siswa peserta PKL yang berada di bawah pengawasan Anda</p>
    </div>
    <div>
        <span class="badge bg-primary px-3 py-2 fs-6">
            <i class="fas fa-user-graduate me-1"></i> Total: <?php echo count($siswa_bimbingan); ?> Siswa
        </span>
    </div>
</div>

<div class="card border-0 rounded-4 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">No</th>
                        <th>NIS / NISN</th>
                        <th>Nama Siswa</th>
                        <th>Kelas & Jurusan</th>
                        <th>Kontak Telepon</th>
                        <th>Tempat PKL (DUDI)</th>
                        <th>Periode PKL</th>
                        <th class="pe-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($siswa_bimbingan)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Belum ada siswa yang ditugaskan kepada Anda oleh Hubin.</td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach($siswa_bimbingan as $s): ?>
                            <tr>
                                <td class="ps-4 text-muted small"><?php echo $no++; ?></td>
                                <td>
                                    <strong><?php echo $s->siswa_nis ?: '-'; ?></strong><br>
                                    <small class="text-muted"><?php echo $s->siswa_nisn ?: '-'; ?></small>
                                </td>
                                <td>
                                    <strong class="text-dark"><?php echo $s->siswa_nama; ?></strong>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border"><?php echo $s->siswa_kelas; ?></span><br>
                                    <small class="text-muted"><?php echo $s->siswa_jurusan; ?></small>
                                </td>
                                <td>
                                    <small><i class="fas fa-phone text-muted me-1"></i><?php echo $s->siswa_telepon ?: '-'; ?></small>
                                </td>
                                <td>
                                    <strong class="text-primary"><?php echo $s->dudi_nama ?: ($s->other_dudi_nama ?: '-'); ?></strong>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?php if($s->tanggal_mulai || $s->tanggal_selesai): ?>
                                            <?php echo $s->tanggal_mulai ? date('d/m/Y', strtotime($s->tanggal_mulai)) : '-'; ?> s/d
                                            <?php echo $s->tanggal_selesai ? date('d/m/Y', strtotime($s->tanggal_selesai)) : '-'; ?>
                                        <?php else: ?>
                                            <?php echo $s->periode ?: '-'; ?>
                                        <?php endif; ?>
                                    </small>
                                </td>
                                <td class="pe-4 text-center">
                                    <a href="<?php echo base_url('guru/view/monitoring-presensi?siswa_id='.$s->siswa_id); ?>" class="btn btn-outline-primary btn-sm rounded-pill px-2 py-1" title="Monitoring Presensi">
                                        <i class="fas fa-calendar-check"></i>
                                    </a>
                                    <a href="<?php echo base_url('guru/view/monitoring-logbook?siswa_id='.$s->siswa_id); ?>" class="btn btn-outline-info btn-sm rounded-pill px-2 py-1" title="Monitoring Logbook">
                                        <i class="fas fa-book"></i>
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
