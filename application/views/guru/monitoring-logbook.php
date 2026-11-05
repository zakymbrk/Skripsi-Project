<!-- Header Monitoring Logbook -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="mb-0 fs-4 fw-bold text-dark"><i class="fas fa-book-reader text-primary me-2"></i>Monitoring Logbook Siswa Bimbingan</h1>
        <p class="text-muted mb-0 small">Review aktivitas kerja harian, dokumentasi foto, dan berikan feedback/catatan pembimbing</p>
    </div>
    <div>
        <a href="<?php echo base_url('guru/view/laporan-bimbingan'); ?>" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-print me-1"></i> Cetak Rekapitulasi
        </a>
    </div>
</div>

<!-- Alert Notifikasi -->
<?php if($this->session->flashdata('message')): ?>
<div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
    <i class="fas fa-check-circle me-2"></i> <?php echo $this->session->flashdata('message'); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if($this->session->flashdata('error_message')): ?>
<div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
    <i class="fas fa-exclamation-triangle me-2"></i> <?php echo $this->session->flashdata('error_message'); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Filter Logbook -->
<div class="card border-0 rounded-4 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="get" action="<?php echo base_url('guru/view/monitoring-logbook'); ?>" class="row g-2 align-items-center">
            <div class="col-md-4">
                <label class="form-label small fw-semibold mb-1">Filter Siswa Bimbingan</label>
                <select name="siswa_id" class="form-select form-select-sm">
                    <option value="">-- Semua Siswa Bimbingan --</option>
                    <?php foreach($siswa_list as $s): ?>
                        <option value="<?php echo $s->siswa_id; ?>" <?php echo ($filter_siswa == $s->siswa_id) ? 'selected' : ''; ?>>
                            <?php echo $s->siswa_nama; ?> (<?php echo $s->siswa_kelas; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Filter Tanggal Kegiatan</label>
                <input type="date" name="tanggal" value="<?php echo $filter_tanggal; ?>" class="form-control form-control-sm">
            </div>
            <div class="col-md-3 d-flex align-items-end gap-2 mt-auto">
                <button type="submit" class="btn btn-primary btn-sm px-3">
                    <i class="fas fa-filter me-1"></i> Filter Data
                </button>
                <a href="<?php echo base_url('guru/view/monitoring-logbook'); ?>" class="btn btn-light btn-sm border">
                    Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Tabel Monitoring Logbook -->
<div class="card border-0 rounded-4 shadow-sm mb-4">
    <div class="card-header bg-white py-3 px-4 border-0 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fs-6 fw-bold text-dark"><i class="fas fa-clipboard-check me-2"></i>Daftar Logbook Kegiatan Siswa</h5>
        <span class="badge bg-light text-dark border"><?php echo count($logbook_list); ?> Catatan Logbook</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">No</th>
                        <th>Nama Siswa</th>
                        <th>Tanggal</th>
                        <th>Deskripsi Aktivitas</th>
                        <th>Dokumentasi</th>
                        <th>Catatan Pembimbing</th>
                        <th>Nilai</th>
                        <th>Validasi</th>
                        <th class="pe-4 text-center">Review</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($logbook_list)): ?>
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">Belum ada catatan logbook yang sesuai dengan filter.</td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach($logbook_list as $l): ?>
                            <tr>
                                <td class="ps-4 text-muted small"><?php echo $no++; ?></td>
                                <td>
                                    <strong class="text-dark"><?php echo $l->siswa_nama; ?></strong><br>
                                    <small class="text-muted"><?php echo $l->siswa_kelas; ?> | <?php echo $l->dudi_nama ?: '-'; ?></small>
                                </td>
                                <td>
                                    <strong><?php echo date('d/m/Y', strtotime($l->tanggal)); ?></strong><br>
                                    <small class="text-muted"><?php echo date('l', strtotime($l->tanggal)); ?></small>
                                </td>
                                <td>
                                    <p class="mb-0 small text-truncate" style="max-width: 250px;">
                                        <?php echo htmlspecialchars($l->deskripsi_kegiatan); ?>
                                    </p>
                                </td>
                                <td>
                                    <?php if(!empty($l->foto_kegiatan) && file_exists('./uploads/logbook/'.$l->foto_kegiatan)): ?>
                                        <a href="<?php echo base_url('uploads/logbook/'.$l->foto_kegiatan); ?>" target="_blank" class="btn btn-outline-primary btn-sm py-0 px-2">
                                            <i class="fas fa-image me-1"></i> Foto Bukti
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if(!empty($l->catatan_pembimbing)): ?>
                                        <span class="badge bg-info text-dark text-truncate" style="max-width: 130px;" title="<?php echo htmlspecialchars($l->catatan_pembimbing); ?>">
                                            <i class="fas fa-comment me-1"></i><?php echo htmlspecialchars($l->catatan_pembimbing); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-muted border">Belum Ada</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if(isset($l->nilai_pembimbing) && $l->nilai_pembimbing !== null): ?>
                                        <span class="badge bg-warning text-dark"><?php echo number_format((float)$l->nilai_pembimbing, 2); ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-muted border">Belum Dinilai</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($l->status_validasi == 'valid'): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success"><i class="fas fa-check-circle me-1"></i>Disetujui</span>
                                    <?php elseif($l->status_validasi == 'menunggu'): ?>
                                        <span class="badge bg-warning bg-opacity-10 text-dark border border-warning"><i class="fas fa-hourglass-half me-1"></i>Menunggu review</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger"><i class="fas fa-circle-xmark me-1"></i>Ditolak</span>
                                        <?php if(!empty($l->alasan_penolakan)): ?><small class="d-block text-muted mt-1" title="<?php echo htmlspecialchars($l->alasan_penolakan); ?>"><?php echo htmlspecialchars($l->alasan_penolakan); ?></small><?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td class="pe-4 text-center">
                                    <button type="button" class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalReviewLogbook<?php echo $l->logbook_id; ?>">
                                        <i class="fas fa-edit me-1"></i> Review
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Review Logbook Guru (Ditaruh di luar table & card agar bebas dari stacking context CSS / backdrop) -->
<?php if(!empty($logbook_list)): ?>
    <?php foreach($logbook_list as $l): ?>
        <div class="modal fade" id="modalReviewLogbook<?php echo $l->logbook_id; ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow">
                    <div class="modal-header bg-dark text-white py-3">
                        <h6 class="modal-title fw-bold"><i class="fas fa-edit text-primary me-2"></i>Review Logbook: <?php echo htmlspecialchars($l->siswa_nama); ?> - <?php echo date('d F Y', strtotime($l->tanggal)); ?></h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-4">
                            <div class="col-md-5 text-center">
                                <p class="small text-muted fw-bold mb-2"><i class="fas fa-camera me-1"></i>Foto Dokumentasi Kegiatan</p>
                                <?php if(!empty($l->foto_kegiatan) && file_exists('./uploads/logbook/'.$l->foto_kegiatan)): ?>
                                    <a href="<?php echo base_url('uploads/logbook/'.$l->foto_kegiatan); ?>" target="_blank">
                                        <img src="<?php echo base_url('uploads/logbook/'.$l->foto_kegiatan); ?>" class="img-fluid rounded-3 shadow-sm border" style="max-height: 260px; object-fit: cover;" alt="Foto Kegiatan">
                                    </a>
                                <?php else: ?>
                                    <div class="bg-light p-4 rounded text-muted"><i class="fas fa-image me-1"></i>Foto tidak tersedia</div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-7">
                                <h6 class="fw-bold small text-muted mb-1"><i class="fas fa-file-alt me-1"></i>Deskripsi Kegiatan Siswa:</h6>
                                <p class="p-3 bg-light rounded-3 small text-secondary" style="white-space: pre-line; max-height: 180px; overflow-y: auto;"><?php echo htmlspecialchars($l->deskripsi_kegiatan); ?></p>

                                <?php if(!empty($l->kendala_pembelajaran)): ?>
                                    <h6 class="fw-bold small text-muted mb-1"><i class="fas fa-exclamation-circle me-1"></i>Kendala & Pembelajaran:</h6>
                                    <p class="small text-muted mb-3 p-2 bg-warning bg-opacity-10 rounded border border-warning"><?php echo htmlspecialchars($l->kendala_pembelajaran); ?></p>
                                <?php endif; ?>

                                <!-- Form Beri Catatan Guru -->
                                <form action="<?php echo base_url('guru/simpan_catatan_logbook'); ?>" method="post" class="logbook-review-form">
                                    <input type="hidden" name="logbook_id" value="<?php echo $l->logbook_id; ?>">
                                    <label class="form-label fw-bold small text-dark mb-1" for="keputusan-logbook-<?php echo $l->logbook_id; ?>">Keputusan Validasi:</label>
                                    <select name="keputusan_logbook" id="keputusan-logbook-<?php echo $l->logbook_id; ?>" class="form-select form-select-sm mb-3 logbook-review-decision" required>
                                        <?php if($l->status_validasi == 'menunggu'): ?><option value="" selected disabled>Pilih keputusan</option><?php endif; ?>
                                        <option value="valid" <?php echo $l->status_validasi == 'valid' ? 'selected' : ''; ?>>Setujui logbook</option>
                                        <option value="tidak_valid" <?php echo $l->status_validasi == 'tidak_valid' ? 'selected' : ''; ?>>Tolak logbook</option>
                                    </select>
                                    <label class="form-label fw-bold small text-dark mb-1"><i class="fas fa-comment-dots text-primary me-1"></i>Beri Catatan / Evaluasi Pembimbing:</label>
                                    <textarea name="catatan_pembimbing" class="form-control form-control-sm mb-3" rows="3" placeholder="Tuliskan apresiasi, koreksi, atau saran pembimbingan di sini..." required><?php echo htmlspecialchars($l->catatan_pembimbing ?: ''); ?></textarea>
                                    <div class="logbook-rejection-reason mb-3" <?php echo $l->status_validasi == 'tidak_valid' ? '' : 'hidden'; ?>>
                                        <label class="form-label fw-bold small text-dark mb-1">Alasan Penolakan:</label>
                                        <textarea name="alasan_penolakan" class="form-control form-control-sm" rows="2" maxlength="255" placeholder="Jelaskan bagian yang harus diperbaiki..."><?php echo htmlspecialchars($l->alasan_penolakan ?: ''); ?></textarea>
                                    </div>
                                    <label class="form-label fw-bold small text-dark mb-1"><i class="fas fa-star text-warning me-1"></i>Beri Penilaian (0-100):</label>
                                    <input type="number" name="nilai_pembimbing" class="form-control form-control-sm mb-3" min="0" max="100" step="0.01" value="<?php echo isset($l->nilai_pembimbing) && $l->nilai_pembimbing !== null ? $l->nilai_pembimbing : ''; ?>" required>
                                    <div class="text-end">
                                        <button type="button" class="btn btn-light btn-sm me-2" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-success btn-sm px-4">
                                            <i class="fas fa-save me-1"></i> Simpan Catatan Pembimbing
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<script>
document.querySelectorAll('.logbook-review-form').forEach(function (form) {
    var decision = form.querySelector('.logbook-review-decision');
    var rejection = form.querySelector('.logbook-rejection-reason');
    var reason = rejection.querySelector('textarea');

    function updateRejectionField() {
        var isRejected = decision.value === 'tidak_valid';
        rejection.hidden = !isRejected;
        reason.required = isRejected;
    }

    decision.addEventListener('change', updateRejectionField);
    updateRejectionField();
});
</script>

