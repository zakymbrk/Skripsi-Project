<?php
$CI = & get_instance();
$userdata = $CI->session->userdata('userdata');

// Nilai default jika data belum terkomputasi
$nilai_akhir = isset($penilaian->nilai_akhir) ? (float)$penilaian->nilai_akhir : 100.00;
$nilai_kehadiran = isset($penilaian->nilai_kehadiran) ? (float)$penilaian->nilai_kehadiran : 100.00;
$nilai_logbook = isset($penilaian->nilai_logbook) ? (float)$penilaian->nilai_logbook : 100.00;
$nilai_disiplin = isset($penilaian->nilai_disiplin) ? (float)$penilaian->nilai_disiplin : 100.00;
$pengurangan_nilai = isset($penilaian->total_pengurangan_nilai) ? (float)$penilaian->total_pengurangan_nilai : 0.00;
$total_hadir = isset($penilaian->total_kehadiran) ? (int)$penilaian->total_kehadiran : (isset($statistik_pre['hadir']) ? $statistik_pre['hadir'] : 0);
$total_logbook_valid = isset($penilaian->total_logbook_valid) ? (int)$penilaian->total_logbook_valid : (isset($statistik_lb['valid']) ? $statistik_lb['valid'] : 0);
$total_logbook_invalid = isset($penilaian->total_logbook_tidak_valid) ? (int)$penilaian->total_logbook_tidak_valid : (isset($statistik_lb['tidak_valid']) ? $statistik_lb['tidak_valid'] : 0);
$catatan_evaluasi = isset($penilaian->catatan_evaluasi) ? $penilaian->catatan_evaluasi : null;

// Tentukan Predikat & Grade
if ($nilai_akhir >= 90) {
    $grade = 'A';
    $predikat = 'Sangat Baik (Amat Memuaskan)';
    $grade_badge_class = 'bg-success';
    $grade_card_border = 'border-success';
} elseif ($nilai_akhir >= 80) {
    $grade = 'B';
    $predikat = 'Baik (Memuaskan)';
    $grade_badge_class = 'bg-primary';
    $grade_card_border = 'border-primary';
} elseif ($nilai_akhir >= 70) {
    $grade = 'C';
    $predikat = 'Cukup';
    $grade_badge_class = 'bg-warning text-dark';
    $grade_card_border = 'border-warning';
} else {
    $grade = 'D';
    $predikat = 'Kurang';
    $grade_badge_class = 'bg-danger';
    $grade_card_border = 'border-danger';
}

$pembimbing_nama = '-';
$pembimbing_nip = '-';
if (!empty($pembimbing_info)) {
    $pembimbing_nama = !empty($pembimbing_info->user_nama) ? $pembimbing_info->user_nama : (!empty($pembimbing_info->pembimbing_nama) ? $pembimbing_info->pembimbing_nama : '-');
    $pembimbing_nip = !empty($pembimbing_info->pembimbing_nip) ? $pembimbing_info->pembimbing_nip : '-';
}

$dudi_nama = '-';
if (!empty($dudi)) {
    $dudi_nama = $dudi->dudi_nama;
} elseif (!empty($siswa->other_dudi_nama)) {
    $dudi_nama = $siswa->other_dudi_nama;
}
?>

<style>
@media print {
    body {
        background: #fff !important;
        font-size: 12pt;
    }
    .no-print, .header-siswa, .back-btn, nav, .btn, .dropdown {
        display: none !important;
    }
    .content-wrapper {
        padding: 0 !important;
        margin: 0 !important;
    }
    .card {
        border: 1px solid #ddd !important;
        box-shadow: none !important;
        break-inside: avoid;
    }
    .print-header {
        display: block !important;
    }
    .print-signature {
        display: block !important;
    }
}

.print-header {
    display: none;
}

.print-signature {
    display: none;
}

.hero-score-card {
    background: #563b82;
    color: white;
    border-radius: 8px;
}

.hero-score-card::after {
    content: none;
}

.score-badge-circle {
    width: 110px;
    height: 110px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(5px);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border: 3px solid rgba(255, 255, 255, 0.4);
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
}

.stat-metric-card {
    background: white;
    border-radius: 6px;
    padding: 20px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.04);
    transition: none;
    border-left: 4px solid transparent;
}

.stat-metric-card:hover {
    transform: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.04);
}
</style>

<!-- KOP SURAT CETAK (Hanya Muncul Saat Print) -->
<div class="print-header text-center mb-4 pb-3 border-bottom border-2 border-dark">
    <h4 class="fw-bold mb-0">SMK ITIKURIH HIBARNA CIPARAY</h4>
    <p class="mb-0 small">Sistem Informasi Praktek Kerja Lapangan (SiPKL)</p>
    <p class="mb-1 small">Jl. Raya Laswi No. 123, Ciparay, Kab. Bandung, Jawa Barat</p>
    <h5 class="fw-bold mt-3 mb-1 text-uppercase text-decoration-underline">NILAI PKL</h5>
    <p class="small mb-0">Tahun Ajaran <?php echo date('Y'); ?>/<?php echo date('Y')+1; ?></p>
</div>

<!-- Header Halaman (Screen View) -->
<div class="d-flex justify-content-between align-items-center mb-4 no-print flex-wrap gap-2">
    <div>
        <h1 class="mb-0 fs-4 fw-bold text-dark">
            <i class="fas fa-award text-warning me-2"></i>Nilai PKL
        </h1>
        <p class="text-muted mb-0 small">
            Statistik kehadiran, logbook tervalidasi, dan transparansi nilai berbasis <strong>Rule-Based Validation (RBV-016)</strong>
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo base_url('siswa/view/home'); ?>" class="btn btn-outline-secondary btn-sm px-3 rounded-pill">
            <i class="fas fa-arrow-left me-1"></i> Dashboard
        </a>
        <button onclick="window.print()" class="btn btn-primary btn-sm px-3 rounded-pill shadow-sm">
            <i class="fas fa-print me-1"></i> Cetak / Simpan Rekap
        </button>
    </div>
</div>

<!-- Flash Message -->
<?php if($CI->session->flashdata('message')): ?>
<div class="alert alert-success alert-dismissible fade show shadow-sm rounded-4 no-print" role="alert">
    <i class="fas fa-check-circle me-2 fs-5"></i>
    <?php echo $CI->session->flashdata('message'); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<!-- Identitas Siswa Card -->
<div class="card border-0 rounded-4 shadow-sm bg-white mb-4">
    <div class="card-body p-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 me-3 flex-shrink-0">
                        <i class="fas fa-user-graduate fa-2x"></i>
                    </div>
                    <div>
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-pill mb-1">
                            NIS: <?php echo !empty($siswa->siswa_nis) ? $siswa->siswa_nis : '-'; ?>
                        </span>
                        <h5 class="fw-bold text-dark mb-1"><?php echo $siswa->siswa_nama; ?></h5>
                        <p class="text-muted small mb-0">
                            <strong>Kelas:</strong> <?php echo !empty($siswa->siswa_kelas) ? $siswa->siswa_kelas : '-'; ?> &bull; 
                            <strong>Jurusan:</strong> <?php echo !empty($siswa->siswa_jurusan) ? $siswa->siswa_jurusan : '-'; ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mt-3 mt-md-0 text-md-end border-start-md ps-md-4">
                <div class="mb-1">
                    <small class="text-muted d-block">Tempat PKL / DUDI:</small>
                    <span class="fw-bold text-dark"><i class="fas fa-building text-primary me-1"></i><?php echo $dudi_nama; ?></span>
                </div>
                <div>
                    <small class="text-muted d-block">Guru Pembimbing:</small>
                    <span class="fw-semibold text-secondary"><i class="fas fa-user-tie text-success me-1"></i><?php echo $pembimbing_nama; ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hero Card: Nilai Akhir & Predikat -->
<div class="hero-score-card p-4 p-md-5 mb-4 shadow">
    <div class="row align-items-center">
        <div class="col-md-8">
            <span class="badge bg-light text-dark px-3 py-2 rounded-pill fw-bold mb-2">
                <i class="fas fa-certificate text-warning me-1"></i> HASIL PENILAIAN AKHIR PKL
            </span>
            <h2 class="fw-bold text-white mb-2">Nilai Akhir: <?php echo number_format($nilai_akhir, 2); ?> / 100</h2>
            <p class="text-white-50 mb-3" style="font-size: 0.95rem;">
                Predikat: <strong class="text-white"><?php echo $predikat; ?> (Grade <?php echo $grade; ?>)</strong>. 
                Nilai dihitung secara otomatis dan transparan melalui sistem validasi terpadu SiPKL.
            </p>
            
            <?php if(!empty($catatan_evaluasi)): ?>
            <div class="bg-white bg-opacity-10 border border-white border-opacity-25 rounded-3 p-3 mt-2">
                <small class="text-warning fw-bold d-block mb-1"><i class="fas fa-comment-dots me-1"></i> Catatan Evaluasi Pembimbing:</small>
                <p class="text-white mb-0 small fst-italic">"<?php echo htmlspecialchars($catatan_evaluasi); ?>"</p>
            </div>
            <?php else: ?>
            <div class="bg-white bg-opacity-10 border border-white border-opacity-25 rounded-3 p-2 px-3 mt-2 small text-white-50">
                <i class="fas fa-info-circle me-1"></i> Pembimbing belum menambahkan catatan evaluasi khusus.
            </div>
            <?php endif; ?>
        </div>
        <div class="col-md-4 text-center mt-4 mt-md-0">
            <div class="d-inline-flex flex-column align-items-center">
                <div class="score-badge-circle mb-2">
                    <span class="fs-1 fw-bold text-white"><?php echo $grade; ?></span>
                    <small class="text-white-50" style="font-size: 0.75rem;">GRADE</small>
                </div>
                <span class="badge <?php echo $grade_badge_class; ?> px-3 py-2 rounded-pill fs-6 shadow-sm">
                    Skor: <?php echo number_format($nilai_akhir, 1); ?>
                </span>
            </div>
        </div>
    </div>
</div>

<!-- 4 Metric Cards (Komponen Nilai & Statistik) -->
<div class="row g-3 mb-4">
    <!-- Kehadiran -->
    <div class="col-6 col-md-3">
        <div class="stat-metric-card h-100" style="border-left-color: #28a745;">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <span class="text-muted small fw-semibold d-block">Nilai Kehadiran</span>
                    <h3 class="fw-bold text-success mb-0"><?php echo number_format($nilai_kehadiran, 1); ?></h3>
                </div>
                <div class="bg-success bg-opacity-10 text-success rounded-circle p-2 px-3">
                    <i class="fas fa-calendar-check"></i>
                </div>
            </div>
            <div class="small text-muted border-top pt-2 mt-2">
                <i class="fas fa-check text-success me-1"></i> Total Hadir: <strong><?php echo $total_hadir; ?> Hari</strong><br>
                <i class="fas fa-times-circle text-danger me-1"></i> Alpha: <strong><?php echo isset($statistik_pre['alpha']) ? $statistik_pre['alpha'] : 0; ?> Hari</strong>
            </div>
        </div>
    </div>

    <!-- Logbook Valid -->
    <div class="col-6 col-md-3">
        <div class="stat-metric-card h-100" style="border-left-color: #17a2b8;">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <span class="text-muted small fw-semibold d-block">Nilai Logbook</span>
                    <h3 class="fw-bold text-info mb-0"><?php echo number_format($nilai_logbook, 1); ?></h3>
                </div>
                <div class="bg-info bg-opacity-10 text-info rounded-circle p-2 px-3">
                    <i class="fas fa-book-open"></i>
                </div>
            </div>
            <div class="small text-muted border-top pt-2 mt-2">
                <i class="fas fa-check-double text-info me-1"></i> Valid: <strong><?php echo $total_logbook_valid; ?> Kegiatan</strong><br>
                <i class="fas fa-exclamation-triangle text-warning me-1"></i> Ditolak: <strong><?php echo $total_logbook_invalid; ?></strong>
            </div>
        </div>
    </div>

    <!-- Penalti Nilai RBV-016 -->
    <div class="col-6 col-md-3">
        <div class="stat-metric-card h-100" style="border-left-color: #dc3545;">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <span class="text-muted small fw-semibold d-block">Penalti Nilai</span>
                    <h3 class="fw-bold text-danger mb-0">-<?php echo number_format($pengurangan_nilai, 0); ?> Poin</h3>
                </div>
                <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-2 px-3">
                    <i class="fas fa-minus-circle"></i>
                </div>
            </div>
            <div class="small text-muted border-top pt-2 mt-2">
                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle">
                    Rule RBV-016
                </span>
                <span class="d-block mt-1" style="font-size: 0.75rem;">-5 poin per logbook tidak valid</span>
            </div>
        </div>
    </div>

    <!-- Sikap & Disiplin -->
    <div class="col-6 col-md-3">
        <div class="stat-metric-card h-100" style="border-left-color: #ffc107;">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <span class="text-muted small fw-semibold d-block">Nilai Disiplin</span>
                    <h3 class="fw-bold text-dark mb-0"><?php echo number_format($nilai_disiplin, 1); ?></h3>
                </div>
                <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-2 px-3">
                    <i class="fas fa-star"></i>
                </div>
            </div>
            <div class="small text-muted border-top pt-2 mt-2">
                <i class="fas fa-shield-alt text-warning me-1"></i> Kepatuhan SOP & Radius<br>
                <span class="text-success fw-semibold">&bull; Memenuhi Syarat</span>
            </div>
        </div>
    </div>
</div>

<!-- Transparansi Formula Perhitungan Nilai (RBV-016) -->
<div class="card border-0 rounded-4 shadow-sm bg-white mb-4">
    <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fs-6 fw-bold text-dark">
            <i class="fas fa-calculator text-primary me-2"></i>Rincian & Transparansi Komputasi Nilai (Rule-Based Validation)
        </h5>
        <span class="badge bg-primary bg-opacity-10 text-primary">Algoritma RBV-016</span>
    </div>
    <div class="card-body p-4 pt-0">
        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 30%;">Komponen Penilaian</th>
                        <th style="width: 20%;" class="text-center">Standar / Acuan</th>
                        <th style="width: 25%;" class="text-center">Kondisi Siswa</th>
                        <th style="width: 25%;" class="text-center">Pengaruh Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <strong>Nilai Dasar Awal</strong>
                            <small class="text-muted d-block">Skor maksimal sebelum kalkulasi penalti</small>
                        </td>
                        <td class="text-center">100.00 Poin</td>
                        <td class="text-center text-muted">-</td>
                        <td class="text-center fw-bold text-success">+100.00</td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Penalti Logbook Tidak Valid</strong>
                            <small class="text-muted d-block">Logbook ditolak pembimbing / tidak memenuhi kriteria RBV-008 &amp; RBV-009</small>
                        </td>
                        <td class="text-center text-danger">-5.00 Poin / Kegiatan</td>
                        <td class="text-center">
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle px-2 py-1">
                                <?php echo $total_logbook_invalid; ?> Logbook Invalid
                            </span>
                        </td>
                        <td class="text-center fw-bold text-danger">
                            -<?php echo number_format($pengurangan_nilai, 2); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Penalti Ketidakhadiran (Alpha)</strong>
                            <small class="text-muted d-block">Presensi tanpa keterangan / tidak melakukan check-in</small>
                        </td>
                        <td class="text-center text-danger">-10.00 Poin / Hari</td>
                        <td class="text-center">
                            <?php $alpha_count = isset($statistik_pre['alpha']) ? $statistik_pre['alpha'] : 0; ?>
                            <span class="badge bg-<?php echo $alpha_count > 0 ? 'danger' : 'success'; ?> bg-opacity-10 text-<?php echo $alpha_count > 0 ? 'danger' : 'success'; ?> border px-2 py-1">
                                <?php echo $alpha_count; ?> Hari Alpha
                            </span>
                        </td>
                        <td class="text-center fw-bold text-<?php echo $alpha_count > 0 ? 'danger' : 'secondary'; ?>">
                                -<?php echo number_format($alpha_count * 5, 2); ?>
                        </td>
                    </tr>
                    <tr class="table-primary fw-bold">
                        <td colspan="3" class="text-end">NILAI AKHIR PEROLEHAN PKL:</td>
                        <td class="text-center fs-5 text-primary">
                            <?php echo number_format($nilai_akhir, 2); ?> (<?php echo $grade; ?>)
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Nav Tabs & Ringkasan Presensi + Logbook Terbaru -->
<div class="card border-0 rounded-4 shadow-sm bg-white mb-4">
    <div class="card-header bg-white border-0 pt-3 px-4">
        <ul class="nav nav-tabs card-header-tabs" id="rekapTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-semibold" id="presensi-tab" data-bs-toggle="tab" data-bs-target="#presensi-pane" type="button" role="tab" aria-selected="true">
                    <i class="fas fa-calendar-check text-primary me-1"></i> Ringkasan Presensi (Terakhir)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="logbook-tab" data-bs-toggle="tab" data-bs-target="#logbook-pane" type="button" role="tab" aria-selected="false">
                    <i class="fas fa-book-open text-success me-1"></i> Ringkasan Logbook (Terakhir)
                </button>
            </li>
        </ul>
    </div>
    <div class="card-body p-4 tab-content" id="rekapTabContent">
        <!-- Tab 1: Presensi -->
        <div class="tab-pane fade show active" id="presensi-pane" role="tabpanel" aria-labelledby="presensi-tab">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 15%;">Tanggal</th>
                            <th style="width: 15%;">Jam Masuk</th>
                            <th style="width: 15%;">Jam Pulang</th>
                            <th style="width: 20%;">Status Kehadiran</th>
                            <th style="width: 20%;">Validasi Lokasi</th>
                            <th style="width: 15%;">Status Validasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($riwayat_presensi)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada riwayat data presensi.</td>
                        </tr>
                        <?php else: ?>
                            <?php foreach($riwayat_presensi as $rp): ?>
                            <tr>
                                <td class="fw-semibold"><?php echo date('d/m/Y', strtotime($rp->tanggal)); ?></td>
                                <td><?php echo !empty($rp->jam_masuk) ? date('H:i', strtotime($rp->jam_masuk)).' WIB' : '-'; ?></td>
                                <td><?php echo !empty($rp->jam_pulang) ? date('H:i', strtotime($rp->jam_pulang)).' WIB' : '-'; ?></td>
                                <td>
                                    <?php 
                                    if ($rp->status_kehadiran == 'hadir') echo '<span class="badge bg-success">Hadir</span>';
                                    elseif ($rp->status_kehadiran == 'izin') echo '<span class="badge bg-warning text-dark">Izin</span>';
                                    elseif ($rp->status_kehadiran == 'sakit') echo '<span class="badge bg-info">Sakit</span>';
                                    else echo '<span class="badge bg-danger">Alpha</span>';
                                    ?>
                                </td>
                                <td>
                                    <?php if($rp->status_lokasi == 'valid'): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success"><i class="fas fa-check-circle me-1"></i>Dalam Radius</span>
                                    <?php elseif($rp->status_lokasi == 'tidak_valid'): ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger"><i class="fas fa-times-circle me-1"></i>Luar Radius</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border">Tanpa GPS</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($rp->status_validasi == 'valid'): ?>
                                        <span class="badge bg-success"><i class="fas fa-check me-1"></i>Valid</span>
                                    <?php elseif($rp->status_validasi == 'tidak_valid'): ?>
                                        <span class="badge bg-danger"><i class="fas fa-times me-1"></i>Ditolak</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Menunggu</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab 2: Logbook -->
        <div class="tab-pane fade" id="logbook-pane" role="tabpanel" aria-labelledby="logbook-tab">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 15%;">Tanggal</th>
                            <th style="width: 40%;">Aktivitas / Kegiatan</th>
                            <th style="width: 25%;">Catatan Pembimbing</th>
                            <th style="width: 15%;">Nilai Pembimbing</th>
                            <th style="width: 20%;">Status Validasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($riwayat_logbook)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada riwayat data logbook kegiatan.</td>
                        </tr>
                        <?php else: ?>
                            <?php foreach($riwayat_logbook as $rl): ?>
                            <tr>
                                <td class="fw-semibold"><?php echo date('d/m/Y', strtotime($rl->tanggal)); ?></td>
                                <td>
                                    <div class="small fw-semibold text-dark"><?php echo htmlspecialchars(substr($rl->deskripsi_kegiatan, 0, 90)); ?><?php echo strlen($rl->deskripsi_kegiatan) > 90 ? '...' : ''; ?></div>
                                </td>
                                <td>
                                    <?php if(!empty($rl->catatan_pembimbing)): ?>
                                        <small class="text-muted fst-italic"><?php echo htmlspecialchars($rl->catatan_pembimbing); ?></small>
                                    <?php else: ?>
                                        <small class="text-muted">-</small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if(isset($rl->nilai_pembimbing) && $rl->nilai_pembimbing !== null): ?>
                                        <strong class="text-primary"><?php echo number_format((float)$rl->nilai_pembimbing, 2); ?></strong>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($rl->status_validasi == 'valid'): ?>
                                        <span class="badge bg-success"><i class="fas fa-check me-1"></i>Valid (Lengkap)</span>
                                    <?php elseif($rl->status_validasi == 'tidak_valid'): ?>
                                        <span class="badge bg-danger"><i class="fas fa-times me-1"></i>Ditolak (-5 poin)</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Menunggu Review</span>
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

<!-- Bagian Tanda Tangan Cetak (Hanya Muncul Saat Print) -->
<div class="print-signature mt-5 pt-4">
    <div class="row">
        <div class="col-6 text-center">
            <p class="small mb-5">Mengetahui,<br><strong>Koordinator Hubungan Industri (Hubin)</strong></p>
            <br><br>
            <p class="fw-bold mb-0"><u>Staff Hubin</u></p>
            <small class="text-muted">SMK ITIKURIH HIBARNA</small>
        </div>
        <div class="col-6 text-center">
            <p class="small mb-5">Ciparay, <?php echo date('d F Y'); ?><br><strong>Guru Pembimbing PKL</strong></p>
            <br><br>
            <p class="fw-bold mb-0"><u><?php echo $pembimbing_nama; ?></u></p>
            <small class="text-muted">NIP: <?php echo $pembimbing_nip; ?></small>
        </div>
    </div>
</div>
