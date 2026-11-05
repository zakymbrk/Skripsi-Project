<?php
$CI = & get_instance();
$userdata = $CI->session->userdata('userdata');
$CI->load->model('M_logbook');

$tanggal_hari_ini = date('Y-m-d');
$logbook_hari_ini = $CI->M_logbook->get_logbook_by_date($siswa->siswa_id, $tanggal_hari_ini);
$riwayat_logbook  = $CI->M_logbook->get_riwayat_logbook_siswa($siswa->siswa_id, 30);
$total_logbook    = $CI->M_logbook->count_logbook_siswa($siswa->siswa_id);
?>

<!-- Header Halaman -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="mb-0 fs-4 fw-bold text-dark"><i class="fas fa-book-open text-primary me-2"></i>Logbook Kegiatan PKL</h1>
        <p class="text-muted mb-0 small">Pencatatan aktivitas harian dan dokumentasi kerja berbasis <strong>Rule-Based Validation</strong></p>
    </div>
    <div>
        <span class="badge bg-light text-dark border px-3 py-2 fs-6">
            <i class="fas fa-tasks me-1 text-primary"></i> Total Logbook: <strong><?php echo $total_logbook; ?></strong>
        </span>
    </div>
</div>

<!-- Flash Messages / Feedback Rule -->
<?php if($CI->session->flashdata('message')): ?>
<div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
    <i class="fas fa-check-circle me-2 fs-5"></i>
    <?php echo $CI->session->flashdata('message'); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<?php if($CI->session->flashdata('error_message')): ?>
<div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
    <i class="fas fa-exclamation-triangle me-2 fs-5"></i>
    <strong>Validasi Gagal!</strong> <?php echo $CI->session->flashdata('error_message'); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<!-- Form / Status Logbook Hari Ini -->
<div class="card border-0 rounded-4 shadow-sm mb-4">
    <div class="card-header bg-dark text-white py-3 px-4 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fs-6 fw-bold"><i class="fas fa-pen-alt me-2"></i>Pengisian Logbook Hari Ini (<?php echo date('l, d F Y'); ?>)</h5>
        <?php if($logbook_hari_ini): ?>
            <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Sudah Diisi (Tervalidasi)</span>
        <?php else: ?>
            <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Belum Diisi</span>
        <?php endif; ?>
    </div>
    <div class="card-body p-4">
        <?php if($logbook_hari_ini): ?>
            <!-- Preview Logbook Hari Ini -->
            <div class="row g-4 align-items-center">
                <div class="col-md-4 text-center">
                    <?php if(!empty($logbook_hari_ini->foto_kegiatan) && file_exists('./uploads/logbook/'.$logbook_hari_ini->foto_kegiatan)): ?>
                        <img src="<?php echo base_url('uploads/logbook/'.$logbook_hari_ini->foto_kegiatan); ?>" class="img-fluid rounded-4 shadow-sm border" style="max-height: 220px; object-fit: cover;" alt="Foto Kegiatan">
                    <?php else: ?>
                        <div class="bg-light p-4 rounded-4 text-muted">Foto tidak tersedia</div>
                    <?php endif; ?>
                </div>
                <div class="col-md-8">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <?php if($logbook_hari_ini->status_validasi === 'valid'): ?>
                            <span class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1"><i class="fas fa-check me-1"></i>Disetujui pembimbing</span>
                        <?php elseif($logbook_hari_ini->status_validasi === 'tidak_valid'): ?>
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-2 py-1"><i class="fas fa-circle-xmark me-1"></i>Ditolak pembimbing</span>
                        <?php else: ?>
                            <span class="badge bg-warning bg-opacity-10 text-dark border border-warning px-2 py-1"><i class="fas fa-hourglass-half me-1"></i>Menunggu validasi</span>
                        <?php endif; ?>
                        <span class="text-muted small">Dicatat pada: <?php echo date('H:i', strtotime($logbook_hari_ini->created_at)); ?> WIB</span>
                    </div>
                    <?php if($logbook_hari_ini->status_validasi === 'tidak_valid' && !empty($logbook_hari_ini->alasan_penolakan)): ?>
                        <div class="alert alert-danger py-2 px-3 small mb-3"><strong>Alasan penolakan:</strong> <?php echo htmlspecialchars($logbook_hari_ini->alasan_penolakan); ?></div>
                    <?php endif; ?>
                    <h6 class="fw-bold text-dark mb-1">Deskripsi Pekerjaan / Aktivitas:</h6>
                    <p class="text-secondary bg-light p-3 rounded-3 mb-3 small" style="white-space: pre-line;"><?php echo htmlspecialchars($logbook_hari_ini->deskripsi_kegiatan); ?></p>
                    
                    <?php if(!empty($logbook_hari_ini->kendala_pembelajaran)): ?>
                        <h6 class="fw-bold text-dark mb-1 small">Kendala & Solusi / Pembelajaran:</h6>
                        <p class="text-muted small mb-3"><?php echo htmlspecialchars($logbook_hari_ini->kendala_pembelajaran); ?></p>
                    <?php endif; ?>

                    <?php if(!empty($logbook_hari_ini->catatan_pembimbing)): ?>
                        <div class="alert alert-info py-2 px-3 mb-0 small">
                            <strong><i class="fas fa-comment-dots me-1"></i>Catatan Pembimbing:</strong> <?php echo htmlspecialchars($logbook_hari_ini->catatan_pembimbing); ?>
                        </div>
                    <?php else: ?>
                        <span class="badge bg-secondary bg-opacity-10 text-secondary border small"><i class="fas fa-hourglass-half me-1"></i>Menunggu review guru pembimbing</span>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <!-- Form Input Logbook Baru -->
            <form action="<?php echo base_url('siswa/do_tambah_logbook'); ?>" method="post" enctype="multipart/form-data" id="form-logbook">
                <input type="hidden" name="latitude_siswa" id="logbook-latitude" value="">
                <input type="hidden" name="longitude_siswa" id="logbook-longitude" value="">
                <input type="hidden" name="tanggal" value="<?php echo $tanggal_hari_ini; ?>">
                
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label fw-semibold small">Deskripsi Kegiatan / Pekerjaan Harian <span class="text-danger">*</span></label>
                        <textarea name="deskripsi_kegiatan" id="deskripsi_kegiatan" class="form-control" rows="4" placeholder="Jelaskan secara detail aktivitas dan tugas yang Anda kerjakan hari ini di tempat PKL (minimal 20 karakter)..." required minlength="20"></textarea>
                        <div class="d-flex justify-content-between mt-1">
                            <span class="text-muted" style="font-size: 0.75rem;">Aturan <strong>RBV-008</strong>: Deskripsi wajib terisi minimal 20 karakter.</span>
                            <span id="char-count" class="badge bg-light text-secondary border" style="font-size: 0.75rem;">0 Karakter</span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Kendala & Solusi / Pembelajaran Baru (Opsional)</label>
                        <textarea name="kendala_pembelajaran" class="form-control" rows="3" placeholder="Tuliskan kendala teknis yang dihadapi dan cara mengatasinya..."></textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Foto Dokumentasi Kegiatan <span class="text-danger">*</span></label>
                        <input type="file" name="foto_kegiatan" class="form-control" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx" required>
                        <div class="form-text text-muted" style="font-size: 0.75rem;">
                            Aturan <strong>RBV-009</strong>: Wajib mengunggah dokumentasi kegiatan (gambar atau dokumen, maksimal 5MB).
                        </div>
                    </div>

                    <div class="col-12 text-end mt-4">
                        <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
                            <i class="fas fa-paper-plane me-1"></i> Simpan Logbook Kegiatan
                        </button>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<!-- Tabel Riwayat Logbook -->
<div class="card border-0 rounded-4 shadow-sm mb-4">
    <div class="card-header bg-white py-3 px-4 border-0 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fs-6 fw-bold text-dark"><i class="fas fa-history me-2"></i>Riwayat Logbook PKL</h5>
        <span class="badge bg-light text-dark border"><?php echo count($riwayat_logbook); ?> Catatan Tersimpan</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width: 15%;">Tanggal</th>
                        <th style="width: 40%;">Uraian Kegiatan</th>
                        <th style="width: 15%;">Dokumentasi</th>
                        <th style="width: 15%;">Catatan Guru</th>
                        <th style="width: 10%;">Validasi</th>
                        <th class="pe-4 text-center" style="width: 5%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($riwayat_logbook)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada riwayat logbook kegiatan.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($riwayat_logbook as $item): ?>
                            <tr>
                                <td class="ps-4 fw-semibold">
                                    <?php echo date('d/m/Y', strtotime($item->tanggal)); ?><br>
                                    <small class="text-muted fw-normal"><?php echo date('l', strtotime($item->tanggal)); ?></small>
                                </td>
                                <td>
                                    <p class="mb-0 text-dark small text-truncate" style="max-width: 320px;">
                                        <?php echo htmlspecialchars($item->deskripsi_kegiatan); ?>
                                    </p>
                                </td>
                                <td>
                                    <?php if(!empty($item->foto_kegiatan) && file_exists('./uploads/logbook/'.$item->foto_kegiatan)): ?>
                                        <a href="<?php echo base_url('uploads/logbook/'.$item->foto_kegiatan); ?>" target="_blank" class="btn btn-outline-primary btn-sm py-0 px-2">
                                            <i class="fas fa-image me-1"></i> Foto Bukti
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if(!empty($item->catatan_pembimbing)): ?>
                                        <span class="badge bg-info text-dark text-truncate" style="max-width: 120px;" title="<?php echo htmlspecialchars($item->catatan_pembimbing); ?>">
                                            <i class="fas fa-comment me-1"></i><?php echo htmlspecialchars($item->catatan_pembimbing); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted small">Belum ada</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($item->status_validasi == 'valid'): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success"><i class="fas fa-check-circle me-1"></i>Disetujui</span>
                                    <?php elseif($item->status_validasi == 'menunggu'): ?>
                                        <span class="badge bg-warning bg-opacity-10 text-dark border border-warning"><i class="fas fa-hourglass-half me-1"></i>Menunggu</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger"><i class="fas fa-circle-xmark me-1"></i>Ditolak</span>
                                        <?php if(!empty($item->alasan_penolakan)): ?><small class="d-block text-danger mt-1"><?php echo htmlspecialchars($item->alasan_penolakan); ?></small><?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td class="pe-4 text-center">
                                    <button type="button" class="btn btn-light btn-sm rounded-circle" data-bs-toggle="modal" data-bs-target="#modalLogbook<?php echo $item->logbook_id; ?>">
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

<!-- Modal Detail Logbook Siswa (Ditaruh di luar table & card agar bebas stacking context CSS) -->
<?php if(!empty($riwayat_logbook)): ?>
    <?php foreach($riwayat_logbook as $item): ?>
        <div class="modal fade" id="modalLogbook<?php echo $item->logbook_id; ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow">
                    <div class="modal-header bg-dark text-white py-3">
                        <h6 class="modal-title fw-bold"><i class="fas fa-book-open text-primary me-2"></i>Detail Logbook - <?php echo date('d F Y', strtotime($item->tanggal)); ?></h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-4">
                            <div class="col-md-5 text-center">
                                <p class="small text-muted fw-bold mb-2">Dokumentasi Kegiatan</p>
                                <?php if(!empty($item->foto_kegiatan) && file_exists('./uploads/logbook/'.$item->foto_kegiatan)): ?>
                                    <a href="<?php echo base_url('uploads/logbook/'.$item->foto_kegiatan); ?>" target="_blank">
                                        <img src="<?php echo base_url('uploads/logbook/'.$item->foto_kegiatan); ?>" class="img-fluid rounded-3 shadow-sm border" style="max-height: 250px; object-fit: cover;" alt="Foto Kegiatan">
                                    </a>
                                <?php else: ?>
                                    <div class="bg-light p-4 rounded text-muted">Foto tidak tersedia</div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-7">
                                <h6 class="fw-bold small text-muted mb-1">Deskripsi Kegiatan:</h6>
                                <p class="p-3 bg-light rounded-3 small text-secondary" style="white-space: pre-line;"><?php echo htmlspecialchars($item->deskripsi_kegiatan); ?></p>

                                <?php if(!empty($item->kendala_pembelajaran)): ?>
                                    <h6 class="fw-bold small text-muted mb-1">Kendala & Solusi / Pembelajaran:</h6>
                                    <p class="small text-muted mb-3 p-2 bg-warning bg-opacity-10 rounded border border-warning"><?php echo htmlspecialchars($item->kendala_pembelajaran); ?></p>
                                <?php endif; ?>

                                <?php if(!empty($item->catatan_pembimbing)): ?>
                                    <div class="alert alert-info py-2 px-3 small mb-0">
                                        <strong><i class="fas fa-comment-dots me-1"></i>Catatan Guru Pembimbing:</strong><br>
                                        <?php echo htmlspecialchars($item->catatan_pembimbing); ?>
                                    </div>
                                <?php endif; ?>
                                <?php if($item->status_validasi === 'tidak_valid' && !empty($item->alasan_penolakan)): ?>
                                    <div class="alert alert-danger py-2 px-3 small mt-3 mb-0">
                                        <strong><i class="fas fa-circle-xmark me-1"></i>Alasan penolakan:</strong><br>
                                        <?php echo htmlspecialchars($item->alasan_penolakan); ?>
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

<script>
    // Live character counter for logbook description
    const desc = document.getElementById('deskripsi_kegiatan');
    const counter = document.getElementById('char-count');
    if(desc && counter) {
        desc.addEventListener('input', function() {
            const len = desc.value.trim().length;
            counter.textContent = len + ' Karakter';
            if(len >= 20) {
                counter.className = 'badge bg-success text-white border';
            } else {
                counter.className = 'badge bg-danger text-white border';
            }
        });
    }
</script>

