<?php
$CI = & get_instance();
$userdata = $CI->session->userdata('userdata');
$CI->load->model('M_presensi');

$tanggal_hari_ini = date('Y-m-d');
$presensi_hari_ini = $CI->M_presensi->get_presensi_hari_ini($siswa->siswa_id, $tanggal_hari_ini);
$riwayat_presensi = $CI->M_presensi->get_riwayat_presensi_siswa($siswa->siswa_id, 30);
$statistik = $CI->M_presensi->get_statistik_presensi_siswa($siswa->siswa_id);

$dudi_lat = !empty($siswa->dudi_latitude) ? (float)$siswa->dudi_latitude : null;
$dudi_lon = !empty($siswa->dudi_longitude) ? (float)$siswa->dudi_longitude : null;
$dudi_rad = !empty($siswa->dudi_radius) ? (int)$siswa->dudi_radius : 100;
?>

<style>
/* Presensi dilakukan dari dashboard siswa; halaman ini khusus riwayat. */
.presensi-action-section {
    display: none !important;
}

@media (max-width: 576px) {
    .presensi-history-table {
        font-size: 0.78rem;
    }

    .presensi-history-table th,
    .presensi-history-table td {
        padding: 0.65rem 0.5rem;
        white-space: nowrap;
    }

    .presensi-history-table td:first-child {
        padding-left: 0.75rem;
    }

}
</style>

<!-- Header Halaman -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="mb-0 fs-4 fw-bold text-dark"><i class="fas fa-map-marker-alt text-primary me-2"></i>Presensi Kehadiran & Geolocation</h1>
        <p class="text-muted mb-0 small">Verifikasi kehadiran di tempat PKL berbasis <strong>Rule-Based Validation & Geolocation Radius</strong></p>
    </div>
    <div>
        <span class="badge bg-primary px-3 py-2 fs-6">
            <i class="fas fa-clock me-1"></i> <span id="clock-display"><?php echo date('H:i:s'); ?></span> WIB
        </span>
    </div>
</div>

<div class="presensi-action-section">
<!-- Info Instansi PKL & Koordinat Radius -->
<div class="card border-0 rounded-4 shadow-sm bg-white mb-4">
    <div class="card-body p-3">
        <div class="row align-items-center">
            <div class="col-md-7">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 me-3">
                        <i class="fas fa-building fa-lg"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-5 mt-3 mt-md-0 text-md-end">
                <span class="badge bg-light text-dark border px-3 py-2">
                    <i class="fas fa-crosshairs text-danger me-1"></i> Radius Toleransi: <strong><?php echo $dudi_rad; ?> Meter</strong>
                </span>
                <?php if($dudi_lat && $dudi_lon): ?>
                    <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2 ms-1">
                        <i class="fas fa-check-circle me-1"></i> Koordinat Aktif
                    </span>
                <?php else: ?>
                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning px-3 py-2 ms-1">
                        <i class="fas fa-exclamation-circle me-1"></i> Koordinat Belum Disetel
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Ringkasan Statistik Kehadiran -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 rounded-4 shadow-sm bg-white p-3 h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold">Total Hadir</span>
                    <h3 class="fw-bold text-success mb-0 mt-1"><?php echo $statistik['hadir']; ?></h3>
                </div>
                <div class="bg-success bg-opacity-10 text-success rounded-circle p-3">
                    <i class="fas fa-user-check fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 rounded-4 shadow-sm bg-white p-3 h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold">Izin / Sakit</span>
                    <h3 class="fw-bold text-warning mb-0 mt-1"><?php echo $statistik['izin'] + $statistik['sakit']; ?></h3>
                </div>
                <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-3">
                    <i class="fas fa-notes-medical fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 rounded-4 shadow-sm bg-white p-3 h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold">Presensi Valid</span>
                    <h3 class="fw-bold text-primary mb-0 mt-1"><?php echo $statistik['valid']; ?></h3>
                </div>
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3">
                    <i class="fas fa-shield-alt fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 rounded-4 shadow-sm bg-white p-3 h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold">Total Hari</span>
                    <h3 class="fw-bold text-dark mb-0 mt-1"><?php echo $statistik['total']; ?></h3>
                </div>
                <div class="bg-secondary bg-opacity-10 text-secondary rounded-circle p-3">
                    <i class="fas fa-calendar-day fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Card Status & Aksi Presensi Hari Ini -->
<div class="card border-0 rounded-4 shadow-sm mb-4 overflow-hidden">
    <div class="card-header bg-dark text-white py-3 px-4 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fs-6 fw-bold"><i class="fas fa-business-time me-2"></i>Status Presensi Hari Ini (<?php echo date('l, d F Y'); ?>)</h5>
        <?php if(!$presensi_hari_ini): ?>
            <span class="badge bg-danger">Belum Presensi</span>
        <?php elseif(empty($presensi_hari_ini->jam_pulang)): ?>
            <span class="badge bg-warning text-dark">Sudah Masuk (Belum Pulang)</span>
        <?php else: ?>
            <span class="badge bg-success">Presensi Lengkap</span>
        <?php endif; ?>
    </div>
    <div class="card-body p-4">
        <div class="row g-4">
            <!-- Box Presensi Masuk -->
            <div class="col-md-6">
                <div class="border rounded-4 p-4 h-100 <?php echo ($presensi_hari_ini && !empty($presensi_hari_ini->jam_masuk)) ? 'bg-light border-success' : 'border-2 border-primary bg-white'; ?>">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0 text-primary"><i class="fas fa-sign-in-alt me-2"></i>1. Presensi Masuk</h6>
                        <?php if($presensi_hari_ini && !empty($presensi_hari_ini->jam_masuk)): ?>
                            <span class="badge bg-success"><i class="fas fa-check me-1"></i>Tercatat: <?php echo $presensi_hari_ini->jam_masuk; ?></span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Menunggu Input</span>
                        <?php endif; ?>
                    </div>

                    <?php if($presensi_hari_ini && !empty($presensi_hari_ini->jam_masuk)): ?>
                        <div class="text-center my-3">
                            <p class="mt-2 mb-1 text-muted small"><strong>Keterangan:</strong> <?php echo $presensi_hari_ini->keterangan_masuk ? $presensi_hari_ini->keterangan_masuk : '-'; ?></p>
                            <div class="d-flex justify-content-center gap-2 mt-2">
                                <span class="badge bg-info text-dark">Status: <?php echo ucfirst($presensi_hari_ini->status_kehadiran); ?></span>
                                <?php if($presensi_hari_ini->status_lokasi == 'valid'): ?>
                                    <span class="badge bg-success"><i class="fas fa-map-marker-alt me-1"></i>Radius Valid (<?php echo round($presensi_hari_ini->jarak); ?>m)</span>
                                <?php elseif($presensi_hari_ini->status_lokasi == 'tidak_valid'): ?>
                                    <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Luar Radius (<?php echo round($presensi_hari_ini->jarak); ?>m)</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><i class="fas fa-info-circle me-1"></i>Tanpa Geolokasi</span>
                                <?php endif; ?>
                            </div>
                            <div class="alert alert-success py-2 px-3 mt-3 mb-0 small text-start">
                                <i class="fas fa-check-double me-1"></i> Presensi masuk telah divalidasi oleh sistem (<strong>RBV-001 & RBV-006 Pass</strong>).
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Widget Deteksi Geolocation GPS -->
                        <div class="card bg-light border-0 rounded-3 p-3 mb-3" id="gps-box">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold small text-dark"><i class="fas fa-satellite text-primary me-1"></i> Deteksi Lokasi GPS</span>
                                <button type="button" class="btn btn-outline-primary btn-xs py-0 px-2 small" onclick="detectGPSLocation()">
                                    <i class="fas fa-sync-alt me-1"></i> Refresh GPS
                                </button>
                            </div>
                            <div id="gps-status-content" class="small">
                                <span class="text-muted"><i class="fas fa-spinner fa-spin me-1"></i> Mengambil koordinat perangkat...</span>
                            </div>
                        </div>

                        <!-- Form Presensi Masuk -->
                        <form action="<?php echo base_url('siswa/do_presensi_masuk'); ?>" method="post" id="form-presensi-masuk">
                            <input type="hidden" name="tanggal" value="<?php echo $tanggal_hari_ini; ?>">
                            <input type="hidden" name="latitude_siswa" id="latitude_siswa" value="">
                            <input type="hidden" name="longitude_siswa" id="longitude_siswa" value="">
                            
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Status Kehadiran <span class="text-danger">*</span></label>
                                <select name="status_kehadiran" id="status_kehadiran" class="form-select form-select-sm" required onchange="handleStatusKehadiranChange(this.value)">
                                    <option value="hadir" selected>Hadir di Tempat PKL</option>
                                    <option value="izin">Izin</option>
                                    <option value="sakit">Sakit</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Keterangan / Catatan Masuk</label>
                                <textarea name="keterangan_masuk" class="form-control form-control-sm" rows="2" placeholder="Contoh: Tiba di kantor instansi PKL tepat waktu..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 btn-sm py-2 fw-semibold" id="btn-submit-masuk">
                                <i class="fas fa-sign-in-alt me-1"></i> Klik Presensi Masuk
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Box Presensi Pulang -->
            <div class="col-md-6">
                <div class="border rounded-4 p-4 h-100 <?php echo ($presensi_hari_ini && !empty($presensi_hari_ini->jam_pulang)) ? 'bg-light border-success' : 'border-2 border-warning bg-white'; ?>">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0 text-warning text-dark"><i class="fas fa-sign-out-alt me-2"></i>2. Presensi Pulang</h6>
                        <?php if($presensi_hari_ini && !empty($presensi_hari_ini->jam_pulang)): ?>
                            <span class="badge bg-success"><i class="fas fa-check me-1"></i>Tercatat: <?php echo $presensi_hari_ini->jam_pulang; ?></span>
                        <?php elseif($presensi_hari_ini && !empty($presensi_hari_ini->jam_masuk)): ?>
                            <span class="badge bg-warning text-dark">Siap Diisi Saat Jam Pulang</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Terkunci (Perlu Masuk Dahulu)</span>
                        <?php endif; ?>
                    </div>

                    <?php if($presensi_hari_ini && !empty($presensi_hari_ini->jam_pulang)): ?>
                        <div class="text-center my-3">
                            <p class="mt-2 mb-1 text-muted small"><strong>Ringkasan Pulang:</strong> <?php echo $presensi_hari_ini->keterangan_pulang ? $presensi_hari_ini->keterangan_pulang : '-'; ?></p>
                            <div class="alert alert-success py-2 px-3 mt-3 mb-0 small text-start">
                                <i class="fas fa-check-double me-1"></i> Presensi pulang telah divalidasi oleh sistem (<strong>RBV-002 Pass</strong>).
                            </div>
                        </div>
                    <?php elseif($presensi_hari_ini && !empty($presensi_hari_ini->jam_masuk)): ?>
                        <!-- Form Presensi Pulang -->
                        <form action="<?php echo base_url('siswa/do_presensi_pulang'); ?>" method="post">
                            <input type="hidden" name="tanggal" value="<?php echo $tanggal_hari_ini; ?>">
                            
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Ringkasan Aktivitas Selesai</label>
                                <textarea name="keterangan_pulang" class="form-control form-control-sm" rows="3" placeholder="Contoh: Pekerjaan maintenance server dan penyusunan berkas hari ini selesai..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-warning w-100 btn-sm py-2 fw-semibold text-dark">
                                <i class="fas fa-sign-out-alt me-1"></i> Klik Presensi Pulang
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-lock fa-3x mb-3 text-secondary opacity-50"></i>
                            <p class="mb-0 small">Selesaikan <strong>Presensi Masuk</strong> terlebih dahulu untuk membuka formulir Presensi Pulang sesuai aturan <strong>RBV-002</strong>.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Riwayat Presensi -->
</div>
<div class="card border-0 rounded-4 shadow-sm mb-4">
    <div class="card-header bg-white py-3 px-4 border-0 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fs-6 fw-bold text-dark"><i class="fas fa-history me-2"></i>Riwayat Presensi PKL (30 Hari Terakhir)</h5>
        <span class="badge bg-light text-dark border"><?php echo count($riwayat_presensi); ?> Catatan</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 presensi-history-table">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Tanggal</th>
                        <th>Status</th>
                        <th>Jam Masuk</th>
                        <th>Jam Pulang</th>
                        <th>Verifikasi Lokasi (GPS)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($riwayat_presensi)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada riwayat data presensi.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($riwayat_presensi as $item): ?>
                            <tr>
                                <td class="ps-4 fw-semibold">
                                    <?php echo date('d/m/Y', strtotime($item->tanggal)); ?><br>
                                    <small class="text-muted fw-normal"><?php echo date('l', strtotime($item->tanggal)); ?></small>
                                </td>
                                <td>
                                    <?php if($item->status_validasi == 'tidak_valid'): ?>
                                        <span class="badge bg-danger">Tidak Sah</span>
                                    <?php elseif($item->status_kehadiran == 'hadir'): ?>
                                        <span class="badge bg-success">Hadir</span>
                                    <?php elseif($item->status_kehadiran == 'izin'): ?>
                                        <span class="badge bg-warning text-dark">Izin</span>
                                    <?php elseif($item->status_kehadiran == 'sakit'): ?>
                                        <span class="badge bg-info text-dark">Sakit</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Alpha</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo $item->jam_masuk ? '<strong>'.$item->jam_masuk.'</strong>' : '<span class="text-muted">-</span>'; ?>
                                </td>
                                <td>
                                    <?php echo $item->jam_pulang ? '<strong>'.$item->jam_pulang.'</strong>' : '<span class="badge bg-secondary">Belum Pulang</span>'; ?>
                                </td>
                                </td>
                                <td>
                                    <?php if($item->status_validasi == 'valid'): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success"><i class="fas fa-check-circle me-1"></i>Valid (RBV)</span>
                                    <?php elseif($item->status_validasi == 'tidak_valid'): ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger"><i class="fas fa-times-circle me-1"></i>Tidak Valid</span>
                                        <?php if(!empty($item->alasan_penolakan)): ?><small class="d-block text-danger mt-1"><?php echo htmlspecialchars($item->alasan_penolakan); ?></small><?php endif; ?>
                                    <?php else: ?>
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning">Menunggu</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
<script>
    // DUDI Coordinate & Radius Config from Server
    var dudiLat = <?php echo json_encode($dudi_lat); ?>;
    var dudiLon = <?php echo json_encode($dudi_lon); ?>;
    var dudiRad = <?php echo json_encode($dudi_rad); ?>;

    // Haversine Distance in JS (meters)
    function calculateHaversineDistance(lat1, lon1, lat2, lon2) {
        var R = 6371000;
        var dLat = (lat2 - lat1) * Math.PI / 180;
        var dLon = (lon2 - lon1) * Math.PI / 180;
        var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon/2) * Math.sin(dLon/2);
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return Math.round(R * c);
    }

    // Geolocation Detection
    function detectGPSLocation() {
        var statusEl = document.getElementById('gps-status-content');
        if (!statusEl) return;

        if (!navigator.geolocation) {
            statusEl.innerHTML = '<span class="text-danger"><i class="fas fa-exclamation-triangle me-1"></i> Peramban tidak mendukung Geolocation API.</span>';
            return;
        }

        statusEl.innerHTML = '<span class="text-muted"><i class="fas fa-spinner fa-spin me-1"></i> Mengambil posisi GPS perangkat...</span>';

        navigator.geolocation.getCurrentPosition(
            function(position) {
                var lat = position.coords.latitude;
                var lon = position.coords.longitude;
                var acc = Math.round(position.coords.accuracy);

                var latInput = document.getElementById('latitude_siswa');
                var lonInput = document.getElementById('longitude_siswa');
                if (latInput) latInput.value = lat;
                if (lonInput) lonInput.value = lon;
                var html = '<div class="text-success"><i class="fas fa-check-circle me-1"></i> <strong>GPS Terkunci:</strong> ' + lat.toFixed(6) + ', ' + lon.toFixed(6) + ' (Akurasi: ±' + acc + 'm)</div>';

                if (dudiLat && dudiLon) {
                    var dist = calculateHaversineDistance(lat, lon, dudiLat, dudiLon);
                    if (dist <= dudiRad) {
                        html += '<div class="mt-1 text-success small"><i class="fas fa-map-pin me-1"></i> <strong>Status Radius:</strong> Valid (Jarak ke DUDI: ' + dist + 'm, Batas: ' + dudiRad + 'm)</div>';
                    } else {
                        html += '<div class="mt-1 text-danger small"><i class="fas fa-exclamation-triangle me-1"></i> <strong>Peringatan:</strong> Lokasi Anda berada di luar radius instansi (' + dist + 'm dari lokasi PKL, toleransi: ' + dudiRad + 'm). Presensi akan ditolak oleh sistem.</div>';
                    }
                } else {
                    html += '<div class="mt-1 text-muted small"><i class="fas fa-info-circle me-1"></i> DUDI belum mengatur koordinat GPS di sistem.</div>';
                }

                statusEl.innerHTML = html;
            },
            function(error) {
                var msg = 'Gagal mendeteksi lokasi.';
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        msg = 'Izin lokasi (GPS) ditolak oleh pengguna/browser. Harap izinkan akses lokasi.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        msg = 'Informasi lokasi perangkat tidak tersedia.';
                        break;
                    case error.TIMEOUT:
                        msg = 'Waktu permintaan lokasi habis (Timeout). Coba lagi.';
                        break;
                }
                statusEl.innerHTML = '<span class="text-danger"><i class="fas fa-times-circle me-1"></i> ' + msg + '</span>';
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    }

    // Toggle photo requirement for izin / sakit
    function handleStatusKehadiranChange(val) {
        var fotoGroup = document.getElementById('foto-masuk-group');
        var fotoInput = document.getElementById('foto_masuk');
        if (val === 'hadir') {
            if (fotoGroup) fotoGroup.classList.remove('d-none');
            if (fotoInput) fotoInput.setAttribute('required', 'required');
        } else {
            if (fotoGroup) fotoGroup.classList.add('d-none');
            if (fotoInput) fotoInput.removeAttribute('required');
        }
    }

    // Preview uploaded image
    function previewImage(input, previewId) {
        var previewContainer = document.getElementById(previewId);
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                if (previewContainer) {
                    previewContainer.classList.remove('d-none');
                    previewContainer.querySelector('img').src = e.target.result;
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Initialize GPS on load
    document.addEventListener('DOMContentLoaded', function() {
        detectGPSLocation();
    });

    // Live clock updater
    setInterval(function() {
        var now = new Date();
        var h = String(now.getHours()).padStart(2, '0');
        var m = String(now.getMinutes()).padStart(2, '0');
        var s = String(now.getSeconds()).padStart(2, '0');
        var el = document.getElementById('clock-display');
        if(el) el.textContent = h + ':' + m + ':' + s;
    }, 1000);
</script>

