<?php
$CI = & get_instance();
$CI->load->model('M_presensi');
$filter_tanggal = $CI->input->get('tanggal');
$filter_status  = $CI->input->get('status_kehadiran');

$presensi_global = $CI->M_presensi->get_all_presensi_global($filter_tanggal, $filter_status, 200);
?>

<!-- Header Monitoring Presensi Hubin -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="mb-0 fs-4 fw-bold text-dark"><i class="fas fa-user-check text-primary me-2"></i>Monitoring Presensi PKL (Admin/Hubin)</h1>
        <p class="text-muted mb-0 small">Pemantauan kehadiran, verifikasi GPS/radius, dan status validasi presensi seluruh siswa PKL</p>
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
        <form method="get" action="<?php echo base_url('hubin/view/monitoring-presensi'); ?>" class="row g-2 align-items-center">
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Filter Tanggal</label>
                <input type="date" name="tanggal" value="<?php echo $filter_tanggal; ?>" class="form-control form-control-sm">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Status Kehadiran</label>
                <select name="status_kehadiran" class="form-select form-select-sm">
                    <option value="">-- Semua Status --</option>
                    <option value="hadir" <?php echo ($filter_status == 'hadir') ? 'selected' : ''; ?>>Hadir</option>
                    <option value="izin" <?php echo ($filter_status == 'izin') ? 'selected' : ''; ?>>Izin</option>
                    <option value="sakit" <?php echo ($filter_status == 'sakit') ? 'selected' : ''; ?>>Sakit</option>
                    <option value="alpha" <?php echo ($filter_status == 'alpha') ? 'selected' : ''; ?>>Alpha</option>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end gap-2 mt-auto">
                <button type="submit" class="btn btn-primary btn-sm px-3">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                <a href="<?php echo base_url('hubin/view/monitoring-presensi'); ?>" class="btn btn-light btn-sm border">
                    Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Tabel Monitoring Presensi Hubin -->
<div class="card border-0 rounded-4 shadow-sm mb-4">
    <div class="card-header bg-white py-3 px-4 border-0 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fs-6 fw-bold text-white"><i class="fas fa-list-check me-2"></i>Rekap Presensi Seluruh Siswa PKL</h5>
        <span class="badge bg-light text-dark border"><?php echo count($presensi_global); ?> Catatan Presensi</span>
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
                        <th>Jam Masuk</th>
                        <th>Jam Pulang</th>
                        <th>Status Kehadiran</th>
                        <th>Verifikasi Lokasi (GPS)</th>
                        <th>Validasi</th>
                        <th class="pe-4 text-center">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($presensi_global)): ?>
                        <tr>
                            <td colspan="10" class="text-center py-4 text-muted">Belum ada data presensi yang tercatat.</td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach($presensi_global as $p): ?>
                            <tr>
                                <td class="ps-4 text-muted small"><?php echo $no++; ?></td>
                                <td>
                                    <strong class="text-dark"><?php echo $p->siswa_nama; ?></strong><br>
                                    <small class="text-muted"><?php echo $p->siswa_kelas; ?> | NIS: <?php echo $p->siswa_nis ?: '-'; ?></small>
                                </td>
                                <td>
                                    <strong class="text-primary small"><?php echo $p->dudi_nama ?: '-'; ?></strong><br>
                                    <small class="text-muted"><i class="fas fa-user-tie me-1"></i><?php echo isset($p->pembimbing_nama) ? $p->pembimbing_nama : 'Belum di-assign'; ?></small>
                                </td>
                                <td>
                                    <strong><?php echo date('d/m/Y', strtotime($p->tanggal)); ?></strong>
                                </td>
                                <td><?php echo $p->jam_masuk ? '<span class="badge bg-light text-dark border">'.$p->jam_masuk.'</span>' : '<span class="text-muted">-</span>'; ?></td>
                                <td><?php echo $p->jam_pulang ? '<span class="badge bg-light text-dark border">'.$p->jam_pulang.'</span>' : '<span class="badge bg-secondary">Belum Pulang</span>'; ?></td>
                                <td>
                                    <?php if($p->status_validasi == 'tidak_valid'): ?>
                                        <span class="badge bg-danger">Tidak Sah</span>
                                    <?php elseif($p->status_kehadiran == 'hadir'): ?>
                                        <span class="badge bg-success">Hadir</span>
                                    <?php elseif($p->status_kehadiran == 'izin'): ?>
                                        <span class="badge bg-warning text-dark">Izin</span>
                                    <?php elseif($p->status_kehadiran == 'sakit'): ?>
                                        <span class="badge bg-info text-dark">Sakit</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Alpha</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if(isset($p->status_lokasi) && $p->status_lokasi == 'valid'): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success">
                                            <i class="fas fa-check-circle me-1"></i> Dalam Radius (<?php echo round($p->jarak); ?>m)
                                        </span>
                                    <?php elseif(isset($p->status_lokasi) && $p->status_lokasi == 'tidak_valid'): ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">
                                            <i class="fas fa-times-circle me-1"></i> Lokasi Tidak Valid
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border">
                                            <i class="fas fa-minus-circle me-1"></i> Non-GPS
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($p->status_validasi == 'valid'): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success"><i class="fas fa-check-circle me-1"></i>Valid</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">Tidak Valid</span>
                                        <?php if(!empty($p->alasan_penolakan)): ?><small class="d-block text-danger mt-1"><?php echo htmlspecialchars($p->alasan_penolakan); ?></small><?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td class="pe-4 text-center">
                                    <button type="button" class="btn btn-light btn-sm rounded-circle" data-bs-toggle="modal" data-bs-target="#modalHubinPresensi<?php echo $p->presensi_id; ?>">
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

<!-- Modal Detail Presensi Hubin (Ditaruh di luar table & card agar bebas stacking context CSS) -->
<?php if(!empty($presensi_global)): ?>
    <?php foreach($presensi_global as $p): ?>
        <div class="modal fade" id="modalHubinPresensi<?php echo $p->presensi_id; ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content rounded-4 border-0 shadow">
                    <div class="modal-header bg-dark text-white py-3">
                        <h6 class="modal-title fw-bold"><i class="fas fa-info-circle text-primary me-2"></i>Detail Presensi: <?php echo htmlspecialchars($p->siswa_nama); ?></h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3 text-muted small">
                            <strong>Tempat PKL:</strong> <?php echo $p->dudi_nama ?: '-'; ?><br>
                            <strong>Pembimbing:</strong> <?php echo isset($p->pembimbing_nama) ? $p->pembimbing_nama : '-'; ?><br>
                            <strong>Tanggal:</strong> <?php echo date('d F Y', strtotime($p->tanggal)); ?>
                        </div>
                        <div class="small mb-2">
                            <strong>Audit Geolocation & Radius:</strong><br>
                            Status Lokasi: <span class="badge bg-<?php echo (isset($p->status_lokasi) && $p->status_lokasi == 'valid') ? 'success' : 'secondary'; ?>"><?php echo ucfirst(str_replace('_', ' ', isset($p->status_lokasi) ? $p->status_lokasi : 'N/A')); ?></span><br>
                            Jarak ke Kantor DUDI: <strong><?php echo isset($p->jarak) && $p->jarak !== null ? round($p->jarak) . ' meter' : '-'; ?></strong>
                            (Batas Radius: <?php echo isset($p->radius) && $p->radius ? $p->radius . 'm' : '-'; ?>)<br>
                            <?php if(!empty($p->latitude_siswa) && !empty($p->longitude_siswa)): ?>
                                Koordinat Siswa: <?php echo $p->latitude_siswa; ?>, <?php echo $p->longitude_siswa; ?>
                                <a href="https://maps.google.com/?q=<?php echo $p->latitude_siswa; ?>,<?php echo $p->longitude_siswa; ?>" target="_blank" class="btn btn-xs btn-outline-primary py-0 px-1 ms-1 small">
                                    <i class="fas fa-external-link-alt"></i> Peta
                                </a>
                            <?php endif; ?>
                        </div>
                        <hr>
                        <p class="mb-1 small"><strong>Keterangan Masuk:</strong> <?php echo htmlspecialchars($p->keterangan_masuk ?: '-'); ?></p>
                        <p class="mb-0 small"><strong>Keterangan Pulang:</strong> <?php echo htmlspecialchars($p->keterangan_pulang ?: '-'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

