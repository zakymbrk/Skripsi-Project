<?php
$CI = & get_instance();
$CI->load->model('M_logbook');
$filter_tanggal = $CI->input->get('tanggal');

$logbook_global = $CI->M_logbook->get_all_logbook_global($filter_tanggal, 100);
?>

<!-- Header Monitoring Logbook Hubin -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="mb-0 fs-4 fw-bold text-dark"><i class="fas fa-book-reader text-primary me-2"></i>Monitoring Logbook PKL (Admin/Hubin)</h1>
        <p class="text-muted mb-0 small">Pemantauan aktivitas kerja harian, dokumentasi foto, dan catatan evaluasi pembimbing seluruh siswa</p>
    </div>
    <div>
        <a href="<?php echo base_url('hubin/view/laporan-monitoring'); ?>" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-print me-1"></i> Laporan Rekapitulasi
        </a>
    </div>
</div>

<!-- Filter Global -->
<div class="card border-0 rounded-4 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="get" action="<?php echo base_url('hubin/view/monitoring-logbook'); ?>" class="row g-2 align-items-center">
            <div class="col-md-4">
                <label class="form-label small fw-semibold mb-1">Filter Tanggal Kegiatan</label>
                <input type="date" name="tanggal" value="<?php echo $filter_tanggal; ?>" class="form-control form-control-sm">
            </div>
            <div class="col-md-4 d-flex align-items-end gap-2 mt-auto">
                <button type="submit" class="btn btn-primary btn-sm px-3">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                <a href="<?php echo base_url('hubin/view/monitoring-logbook'); ?>" class="btn btn-light btn-sm border">
                    Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Tabel Monitoring Logbook Hubin -->
<div class="card border-0 rounded-4 shadow-sm mb-4">
    <div class="card-header bg-white py-3 px-4 border-0 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fs-6 fw-bold text-white"><i class="fas fa-table me-2"></i>Rekap Logbook Aktivitas Seluruh Siswa</h5>
        <span class="badge bg-light text-dark border"><?php echo count($logbook_global); ?> Data Logbook</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">No</th>
                        <th>Siswa / Kelas</th>
                        <th>Tempat PKL & Pembimbing</th>
                        <th>Tanggal</th>
                        <th>Uraian Aktivitas</th>
                        <th>Dokumentasi</th>
                        <th>Catatan Guru</th>
                        <th>Validasi</th>
                        <th class="pe-4 text-center">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($logbook_global)): ?>
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">Belum ada data logbook yang tercatat.</td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach($logbook_global as $l): ?>
                            <tr>
                                <td class="ps-4 text-muted small"><?php echo $no++; ?></td>
                                <td>
                                    <strong class="text-dark"><?php echo $l->siswa_nama; ?></strong><br>
                                    <small class="text-muted"><?php echo $l->siswa_kelas; ?> | NIS: <?php echo $l->siswa_nis ?: '-'; ?></small>
                                </td>
                                <td>
                                    <strong class="text-primary small"><?php echo $l->dudi_nama ?: '-'; ?></strong><br>
                                    <small class="text-muted"><i class="fas fa-user-tie me-1"></i><?php echo $l->pembimbing_nama ?: 'Belum di-assign'; ?></small>
                                </td>
                                <td>
                                    <strong><?php echo date('d/m/Y', strtotime($l->tanggal)); ?></strong><br>
                                    <small class="text-muted"><?php echo date('l', strtotime($l->tanggal)); ?></small>
                                </td>
                                <td>
                                    <p class="mb-0 small text-truncate" style="max-width: 220px;">
                                        <?php echo htmlspecialchars($l->deskripsi_kegiatan); ?>
                                    </p>
                                </td>
                                <td>
                                    <?php if(!empty($l->foto_kegiatan) && file_exists('./uploads/logbook/'.$l->foto_kegiatan)): ?>
                                        <a href="<?php echo base_url('uploads/logbook/'.$l->foto_kegiatan); ?>" target="_blank" class="btn btn-outline-primary btn-sm py-0 px-2">
                                            <i class="fas fa-image me-1"></i> Foto
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if(!empty($l->catatan_pembimbing)): ?>
                                        <span class="badge bg-info text-dark text-truncate" style="max-width: 120px;" title="<?php echo htmlspecialchars($l->catatan_pembimbing); ?>">
                                            <i class="fas fa-comment me-1"></i><?php echo htmlspecialchars($l->catatan_pembimbing); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-muted border">Belum Ada</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success"><i class="fas fa-shield-alt me-1"></i>Valid (RBV)</span>
                                </td>
                                <td class="pe-4 text-center">
                                    <button type="button" class="btn btn-light btn-sm rounded-circle" data-bs-toggle="modal" data-bs-target="#modalHubinLogbook<?php echo $l->logbook_id; ?>">
                                        <i class="fas fa-eye text-primary"></i>
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

<!-- Modal Detail Logbook Hubin (Ditaruh di luar table & card agar bebas stacking context CSS) -->
<?php if(!empty($logbook_global)): ?>
    <?php foreach($logbook_global as $l): ?>
        <div class="modal fade" id="modalHubinLogbook<?php echo $l->logbook_id; ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow">
                    <div class="modal-header bg-dark text-white py-3">
                        <h6 class="modal-title fw-bold"><i class="fas fa-book-open text-primary me-2"></i>Detail Logbook: <?php echo htmlspecialchars($l->siswa_nama); ?> - <?php echo date('d F Y', strtotime($l->tanggal)); ?></h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-4">
                            <div class="col-md-5 text-center">
                                <p class="small text-muted fw-bold mb-2">Foto Dokumentasi Kegiatan</p>
                                <?php if(!empty($l->foto_kegiatan) && file_exists('./uploads/logbook/'.$l->foto_kegiatan)): ?>
                                    <a href="<?php echo base_url('uploads/logbook/'.$l->foto_kegiatan); ?>" target="_blank">
                                        <img src="<?php echo base_url('uploads/logbook/'.$l->foto_kegiatan); ?>" class="img-fluid rounded-3 shadow-sm border" style="max-height: 240px; object-fit: cover;" alt="Foto Kegiatan">
                                    </a>
                                <?php else: ?>
                                    <div class="bg-light p-4 rounded text-muted">Foto tidak tersedia</div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-7">
                                <div class="mb-3 text-muted small">
                                    <strong>Tempat PKL:</strong> <?php echo $l->dudi_nama ?: '-'; ?><br>
                                    <strong>Guru Pembimbing:</strong> <?php echo $l->pembimbing_nama ?: '-'; ?>
                                </div>
                                <h6 class="fw-bold small text-muted mb-1">Deskripsi Kegiatan Siswa:</h6>
                                <p class="p-3 bg-light rounded-3 small text-secondary" style="white-space: pre-line;"><?php echo htmlspecialchars($l->deskripsi_kegiatan); ?></p>

                                <?php if(!empty($l->kendala_pembelajaran)): ?>
                                    <h6 class="fw-bold small text-muted mb-1">Kendala & Pembelajaran:</h6>
                                    <p class="small text-muted mb-3 p-2 bg-warning bg-opacity-10 rounded border border-warning"><?php echo htmlspecialchars($l->kendala_pembelajaran); ?></p>
                                <?php endif; ?>

                                <?php if(!empty($l->catatan_pembimbing)): ?>
                                    <div class="alert alert-info py-2 px-3 small mb-0">
                                        <strong>Catatan Evaluasi Guru Pembimbing:</strong><br>
                                        <?php echo htmlspecialchars($l->catatan_pembimbing); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

