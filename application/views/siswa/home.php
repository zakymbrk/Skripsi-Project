<?php 

$userdata = $this->session->userdata('userdata');
$this->db->where('user_id', $userdata['id']);
$siswa = $this->db->get('tb_siswa')->row();
$this->load->model('M_presensi');
$presensi_hari_ini = $siswa ? $this->M_presensi->get_presensi_hari_ini($siswa->siswa_id, date('Y-m-d')) : null;

// Get pengumuman terbaru
$pengumuman = $this->db->order_by('created_at', 'DESC')->limit(3)->get('tb_pengumuman')->result();

// Load model for pengelompokan
$this->load->model('M_pengelompokan');

// Get pembimbing information
$pembimbing_info = null;
if($siswa && $siswa->siswa_id) {
    $pembimbing_info = $this->M_pengelompokan->get_pembimbing_by_siswa($siswa->siswa_id);
}

// Get data DUDI jika siswa sudah punya penempatan
$dudi = null;
if($siswa && $siswa->dudi_id){
    $this->db->where('dudi_id', $siswa->dudi_id);
    $dudi = $this->db->get('tb_dudi')->row();
} elseif($siswa && $siswa->other_dudi_nama) {
    // For students with other/unregistered companies
    $dudi = (object) [
        'dudi_nama' => $siswa->other_dudi_nama
    ];
}
$dudi_lat = ($dudi && !empty($dudi->latitude)) ? (float) $dudi->latitude : null;
$dudi_lon = ($dudi && !empty($dudi->longitude)) ? (float) $dudi->longitude : null;
$dudi_radius = ($dudi && !empty($dudi->radius)) ? (int) $dudi->radius : 100;



// Get pembimbing information if application is approved
$pembimbing = null;
if($siswa && $siswa->status_pengajuan == 'disetujui'){
    $pembimbing = $this->M_pengelompokan->get_pembimbing_by_siswa($siswa->siswa_id);
}
?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<style>
    .student-location-map {
        width: 100%;
        height: 280px;
        border: 1px solid #e5e1eb;
        border-radius: 6px;
        overflow: hidden;
        background: #f0ebf7;
    }

    .student-map-marker span {
        display: block;
        width: 16px;
        height: 16px;
        margin: 3px;
        border: 3px solid #fff;
        border-radius: 50%;
        background: #563b82;
        box-shadow: 0 1px 4px rgba(32, 24, 45, .35);
    }
</style>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="mb-0 fs-4"><i class="fas fa-tachometer-alt me-2"></i>Dashboard Siswa</h1>
        <p class="text-muted mb-0 small" id="current-date"><?php echo date("l, d F Y"); ?></p>
    </div>
    <div class="d-flex align-items-center">
        <div class="me-2">
            <span class="badge bg-primary small">
                <i class="fas fa-clock me-1"></i><span id="current-time"><?php echo date("H:i:s"); ?></span>
            </span>
        </div>
    </div>
</div>

<?php if($this->session->flashdata('message')): ?>
<div class="alert alert-success alert-dismissible fade show shadow-sm rounded-4" role="alert">
    <i class="fas fa-check-circle me-2"></i><?php echo $this->session->flashdata('message'); ?>
    <?php if($this->session->flashdata('show_logbook_link')): ?>
        <a href="<?php echo base_url('siswa/view/logbook'); ?>" class="btn btn-success btn-sm ms-2">
            <i class="fas fa-book-open me-1"></i>Buka Logbook Hari Ini
        </a>
    <?php endif; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>
<?php if($this->session->flashdata('error_message')): ?>
<div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-4" role="alert">
    <i class="fas fa-exclamation-triangle me-2"></i>
    <strong>Validasi Gagal!</strong> <?php echo $this->session->flashdata('error_message'); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<!-- Notification for Pembimbing Assignment -->
<?php if(isset($mentor_assignment_notification)): ?>
    <?php echo $mentor_assignment_notification; ?>
<?php else: ?>
    <?php if($siswa && $siswa->status_pengajuan == 'disetujui'): ?>
        <?php if($pembimbing_info): ?>
        <!-- Mentor assignment notification already shown in this session, so we don't show it again -->
        <?php else: ?>
        <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-clock me-2"></i>
            <strong>Menunggu Penugasan Pembimbing</strong> 
            Pengajuan PKL Anda sudah disetujui. Guru pembimbing akan segera ditugaskan kepada Anda.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
    <?php endif; ?>
<?php endif; ?>

<?php if($siswa && $siswa->status_pengajuan == 'disetujui'): ?>
<div class="card border-0 rounded-4 shadow-sm mb-4">
    <div class="card-body p-3 p-md-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="mb-1 fw-bold"><i class="fas fa-fingerprint text-primary me-2"></i>Presensi Hari Ini</h5>
                <small class="text-muted">Klik tombol sesuai aktivitas Anda. Lokasi GPS akan diverifikasi otomatis.</small>
            </div>
            <?php if($presensi_hari_ini && $presensi_hari_ini->status_validasi === 'tidak_valid'): ?>
                <span class="badge bg-danger">Tidak Sah</span>
            <?php elseif($presensi_hari_ini && $presensi_hari_ini->status_validasi === 'valid' && $presensi_hari_ini->jam_pulang): ?>
                <span class="badge bg-success">Lengkap</span>
            <?php elseif($presensi_hari_ini && $presensi_hari_ini->status_validasi === 'valid' && $presensi_hari_ini->jam_masuk): ?>
                <span class="badge bg-warning text-dark">Sudah Masuk</span>
            <?php else: ?>
                <span class="badge bg-secondary">Belum Presensi</span>
            <?php endif; ?>
        </div>
        <div id="home-presensi-status" class="small mb-3 text-muted"></div>
        <section class="mb-3" aria-labelledby="student-location-title">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                <div>
                    <h6 class="mb-0 fw-semibold" id="student-location-title"><span class="material-symbols-rounded student-icon" aria-hidden="true">location_on</span> Lokasi GPS</h6>
                    <small class="text-muted">Periksa posisi perangkat dan radius tempat PKL.</small>
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm" id="student-location-check">
                    <span class="material-symbols-rounded student-icon" aria-hidden="true">my_location</span> Periksa lokasi
                </button>
            </div>
            <div id="student-location-status" class="small text-muted mb-2" aria-live="polite">Lokasi belum diperiksa.</div>
            <div id="student-location-map" class="student-location-map" <?php echo ($presensi_hari_ini && !empty($presensi_hari_ini->latitude_siswa) && !empty($presensi_hari_ini->longitude_siswa)) ? '' : 'hidden'; ?> aria-label="Peta lokasi perangkat dan tempat PKL"></div>
            <div class="d-flex flex-wrap gap-3 mt-2 small" id="student-location-legend" <?php echo ($presensi_hari_ini && !empty($presensi_hari_ini->latitude_siswa) && !empty($presensi_hari_ini->longitude_siswa)) ? '' : 'hidden'; ?>>
                <span><i class="fas fa-location-dot me-1"></i>Lokasi perangkat</span>
                <?php if($dudi_lat && $dudi_lon): ?><span><i class="fas fa-building me-1"></i>Tempat PKL dan radius</span><?php endif; ?>
            </div>
        </section>
        <?php if($presensi_hari_ini && $presensi_hari_ini->status_validasi === 'tidak_valid'): ?>
            <div class="alert alert-danger py-2 px-3 mb-3" role="alert">
                <strong>Presensi tidak sah.</strong>
                <?php echo htmlspecialchars($presensi_hari_ini->alasan_penolakan ?: 'Sinyal lokasi tidak memenuhi validasi. Hubungi guru pembimbing untuk klarifikasi.'); ?>
            </div>
        <?php endif; ?>
        <div class="row g-2">
            <?php if($presensi_hari_ini && $presensi_hari_ini->status_validasi === 'tidak_valid'): ?>
                <div class="col-12"><p class="small text-danger mb-0">Fitur presensi hari ini dinonaktifkan karena data lokasi ditandai tidak valid.</p></div>
            <?php else: ?>
            <div class="col-12 col-md-6">
                <?php if(!$presensi_hari_ini || !$presensi_hari_ini->jam_masuk): ?>
                <form method="post" action="<?php echo base_url('siswa/do_presensi_masuk'); ?>" class="home-presensi-form">
                    <input type="hidden" name="tanggal" value="<?php echo date('Y-m-d'); ?>">
                    <input type="hidden" name="status_kehadiran" value="hadir">
                    <input type="hidden" name="keterangan_masuk" value="Presensi masuk <?php echo htmlspecialchars($siswa->siswa_nama); ?> pada pukul <?php echo date('H:i:s'); ?> di tempat PKL.">
                    <input type="hidden" name="latitude_siswa" class="home-latitude">
                    <input type="hidden" name="longitude_siswa" class="home-longitude">
                    <button type="submit" class="btn btn-primary w-100 py-3 fw-semibold"><i class="fas fa-sign-in-alt me-2"></i>Klik Presensi Masuk</button>
                </form>
                <?php else: ?>
                    <button type="button" class="btn btn-light border w-100 py-3" disabled><i class="fas fa-check text-success me-2"></i>Masuk: <?php echo $presensi_hari_ini->jam_masuk; ?></button>
                <?php endif; ?>
            </div>
            <div class="col-12 col-md-6">
                <?php if($presensi_hari_ini && $presensi_hari_ini->jam_masuk && !$presensi_hari_ini->jam_pulang): ?>
                <div class="alert alert-warning py-2 px-3 small mb-2">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Pastikan seluruh kegiatan hari ini sudah selesai. Setelah presensi pulang, logbook hari ini baru dapat diisi dan presensi pulang tidak dapat diulang.
                </div>
                <form method="post" action="<?php echo base_url('siswa/do_presensi_pulang'); ?>" class="home-presensi-form" onsubmit="return confirmPresensiPulang();">
                    <input type="hidden" name="tanggal" value="<?php echo date('Y-m-d'); ?>">
                    <input type="hidden" name="keterangan_pulang" value="Presensi pulang <?php echo htmlspecialchars($siswa->siswa_nama); ?> setelah kegiatan PKL selesai.">
                    <input type="hidden" name="latitude_siswa" class="home-latitude">
                    <input type="hidden" name="longitude_siswa" class="home-longitude">
                    <button type="submit" class="btn btn-warning text-dark w-100 py-3 fw-semibold"><i class="fas fa-sign-out-alt me-2"></i>Klik Presensi Pulang</button>
                </form>
                <?php elseif($presensi_hari_ini && $presensi_hari_ini->jam_pulang): ?>
                    <button type="button" class="btn btn-light border w-100 py-3" disabled><i class="fas fa-check text-success me-2"></i>Pulang: <?php echo $presensi_hari_ini->jam_pulang; ?></button>
                <?php else: ?>
                    <button type="button" class="btn btn-light border w-100 py-3" disabled><i class="fas fa-lock text-muted me-2"></i>Pulang setelah presensi masuk</button>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script>
var studentLocationMap = null;
var studentDudiLatitude = <?php echo json_encode($dudi_lat); ?>;
var studentDudiLongitude = <?php echo json_encode($dudi_lon); ?>;
var studentDudiRadius = <?php echo json_encode($dudi_radius); ?>;

function showStudentLocationMap(latitude, longitude) {
    var mapElement = document.getElementById('student-location-map');
    var legend = document.getElementById('student-location-legend');
    if (!mapElement || !window.L) return;

    mapElement.hidden = false;
    legend.hidden = false;
    if (!studentLocationMap) {
        studentLocationMap = L.map(mapElement, {scrollWheelZoom: false});
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(studentLocationMap);
    }

    studentLocationMap.eachLayer(function (layer) {
        if (!(layer instanceof L.TileLayer)) studentLocationMap.removeLayer(layer);
    });

    var devicePoint = [latitude, longitude];
    var icon = L.divIcon({className: 'student-map-marker', html: '<span></span>', iconSize: [22, 22], iconAnchor: [11, 11]});
    var points = [devicePoint];
    L.marker(devicePoint, {icon: icon}).addTo(studentLocationMap).bindPopup('Lokasi perangkat');

    if (studentDudiLatitude && studentDudiLongitude) {
        var dudiPoint = [studentDudiLatitude, studentDudiLongitude];
        L.marker(dudiPoint).addTo(studentLocationMap).bindPopup('Tempat PKL');
        L.circle(dudiPoint, {
            radius: studentDudiRadius,
            color: '#563b82',
            weight: 2,
            fillColor: '#8064a8',
            fillOpacity: 0.12
        }).addTo(studentLocationMap);
        points.push(dudiPoint);
    }

    if (points.length > 1) {
        studentLocationMap.fitBounds(points, {padding: [24, 24], maxZoom: 16});
    } else {
        studentLocationMap.setView(devicePoint, 16);
    }
    window.setTimeout(function () { studentLocationMap.invalidateSize(); }, 0);
}

<?php if($presensi_hari_ini && !empty($presensi_hari_ini->latitude_siswa) && !empty($presensi_hari_ini->longitude_siswa)): ?>
showStudentLocationMap(<?php echo json_encode((float) $presensi_hari_ini->latitude_siswa); ?>, <?php echo json_encode((float) $presensi_hari_ini->longitude_siswa); ?>);
<?php endif; ?>

document.getElementById('student-location-check').addEventListener('click', function () {
    var status = document.getElementById('student-location-status');
    if (!navigator.geolocation) {
        status.textContent = 'Perangkat atau browser ini tidak mendukung pemeriksaan lokasi.';
        status.className = 'small text-danger mb-2';
        return;
    }
    status.textContent = 'Meminta lokasi perangkat...';
    status.className = 'small text-muted mb-2';
    navigator.geolocation.getCurrentPosition(function (position) {
        var latitude = position.coords.latitude;
        var longitude = position.coords.longitude;
        var accuracy = Math.round(position.coords.accuracy);
        showStudentLocationMap(latitude, longitude);
        status.textContent = 'Lokasi diperoleh. Akurasi perangkat sekitar ±' + accuracy + ' meter.';
        status.className = 'small text-success mb-2';
    }, function (error) {
        var message = error.code === error.PERMISSION_DENIED
            ? 'Izin lokasi ditolak. Aktifkan izin lokasi pada browser/perangkat.'
            : 'Lokasi belum tersedia. Pastikan GPS aktif lalu coba kembali.';
        status.textContent = message;
        status.className = 'small text-danger mb-2';
    }, {enableHighAccuracy: true, timeout: 15000, maximumAge: 0});
});

document.querySelectorAll('.home-presensi-form').forEach(function(form) {
    form.addEventListener('submit', function(event) {
        var isMasuk = form.action.indexOf('do_presensi_masuk') !== -1;
        var isPulang = form.action.indexOf('do_presensi_pulang') !== -1;
        if (form.dataset.locationReady === '1') return;
        if (!isMasuk && !isPulang) return;

        event.preventDefault();
        var status = document.getElementById('home-presensi-status');
        if(!navigator.geolocation) {
            status.textContent = 'Browser tidak mendukung GPS.';
            status.className = 'small mb-3 text-danger';
            return;
        }
        status.textContent = 'Mengambil lokasi untuk memvalidasi presensi ' + (isMasuk ? 'masuk' : 'pulang') + '...';
        navigator.geolocation.getCurrentPosition(function(position) {
            form.querySelector('.home-latitude').value = position.coords.latitude;
            form.querySelector('.home-longitude').value = position.coords.longitude;
            showStudentLocationMap(position.coords.latitude, position.coords.longitude);
            var locationStatus = document.getElementById('student-location-status');
            locationStatus.textContent = 'Lokasi didapat untuk validasi presensi. Akurasi ±' + Math.round(position.coords.accuracy) + ' meter.';
            locationStatus.className = 'small text-success mb-2';
            form.dataset.locationReady = '1';
            form.submit();
        }, function() {
            status.textContent = 'Lokasi tidak dapat diambil. Izinkan akses GPS lalu klik kembali.';
            status.className = 'small mb-3 text-danger';
        }, {enableHighAccuracy: true, timeout: 15000, maximumAge: 0});
    });
});

function confirmPresensiPulang() {
    return window.confirm('Apakah kegiatan PKL hari ini benar-benar sudah selesai? Setelah presensi pulang dicatat, logbook hari ini akan dibuka dan presensi pulang tidak dapat diulang.');
}
</script>
<?php endif; ?>




        
        
        
        
        



<!-- Stats Overview -->
<div class="row g-3 mb-4">

    <?php 
    // Get student data for status display
    $this->db->where('user_id', $userdata['id']);
    $student_data = $this->db->get('tb_siswa')->row();
    
    // Get DUDI info if available
    $dudi_info = null;
    if($student_data && $student_data->dudi_id) {
        $this->db->where('dudi_id', $student_data->dudi_id);
        $dudi_info = $this->db->get('tb_dudi')->row();
    }
    
    // Get pembimbing info if available
    $pembimbing_info = null;
    if($student_data && $student_data->status_pengajuan == 'disetujui') {
        $this->load->model('M_pengelompokan');
        $pembimbing_info = $this->M_pengelompokan->get_pembimbing_by_siswa($student_data->siswa_id);
    }
    ?>

    <div class="col-md-4">
        <div class="card border-0 rounded-lg shadow-sm h-100 stat-card-modern">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1 small">Status Pengajuan</h6>
                        <h3 class="mb-1 fw-bold fs-5">
                            <?php 
                            if($student_data && $student_data->status_pengajuan) {
                                $status = $student_data->status_pengajuan;
                                if($status == 'disetujui') {
                                    echo '<span class="text-success">Disetujui</span>';
                                } elseif($status == 'ditolak') {
                                    echo '<span class="text-danger">Ditolak</span>';
                                } elseif($status == 'menunggu') {
                                    echo '<span class="text-warning">Menunggu</span>';
                                } else {
                                    echo '<span class="text-secondary">Belum</span>';
                                }
                            } else {
                                echo '<span class="text-secondary">-</span>';
                            }
                            ?>
                        </h3>
                        <small class="text-muted">Status permohonan PKL</small>
                    </div>
                    <div class="icon-wrapper bg-primary bg-opacity-10 text-primary rounded-circle p-2">
                        <i class="fas fa-file-alt fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php if($student_data && $student_data->status_pengajuan == 'disetujui' && $dudi_info) { ?>
    <div class="col-md-4">
        <div class="card border-0 rounded-lg shadow-sm h-100 stat-card-modern">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1 small">Penempatan</h6>
                        <h3 class="mb-1 fw-bold fs-5">
                            <?php 
                            if($dudi_info) {
                                echo '<span>' . (strlen($dudi_info->dudi_nama) > 12 ? substr($dudi_info->dudi_nama, 0, 12) . '...' : $dudi_info->dudi_nama) . '</span>';
                            } else {
                                echo '<span class="text-secondary">-</span>';
                            }
                            ?>
                        </h3>
                        <small class="text-muted">Tempat PKL</small>
                    </div>
                    <div class="icon-wrapper bg-success bg-opacity-10 text-success rounded-circle p-2">
                        <i class="fas fa-building fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php } else { ?>
    <div class="col-md-4">
        <div class="card border-0 rounded-lg shadow-sm h-100 stat-card-modern">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1 small">Penempatan</h6>
                        <h3 class="mb-1 fw-bold fs-5">
                            <span class="text-secondary">-</span>
                        </h3>
                        <small class="text-muted">Tempat PKL</small>
                    </div>
                    <div class="icon-wrapper bg-secondary bg-opacity-10 text-secondary rounded-circle p-2">
                        <i class="fas fa-building fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
    
    <div class="col-md-4">
        <div class="card border-0 rounded-lg shadow-sm h-100 stat-card-modern">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1 small">Guru Pembimbing</h6>
                        <h3 class="mb-1 fw-bold fs-5">
                            <?php 
                            if($pembimbing_info) {
                                $pembimbing_nama = isset($pembimbing_info->user_nama) && $pembimbing_info->user_nama ? 
                                                  $pembimbing_info->user_nama : 
                                                  (isset($pembimbing_info->pembimbing_nama) ? $pembimbing_info->pembimbing_nama : '-');
                                echo '<span class="text-success">' . (strlen($pembimbing_nama) > 12 ? substr($pembimbing_nama, 0, 12) . '...' : $pembimbing_nama) . '</span>';
                            } else {
                                echo '<span class="text-secondary">-</span>';
                            }
                            ?>
                        </h3>
                        <small class="text-muted">
                            <?php if($pembimbing_info): ?>
                                <i class="fas fa-check-circle text-success me-1"></i>Pembimbing sudah ditugaskan
                            <?php else: ?>
                                <i class="fas fa-clock text-warning me-1"></i>Menunggu penugasan
                            <?php endif; ?>
                        </small>
                    </div>
                    <div class="icon-wrapper <?php echo $pembimbing_info ? 'bg-success' : 'bg-warning'; ?> bg-opacity-10 <?php echo $pembimbing_info ? 'text-success' : 'text-warning'; ?> rounded-circle p-2">
                        <i class="fas fa-user-tie fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Menu Grid -->
<div class="card border-0 rounded-lg shadow-sm mb-4">
    <div class="card-header bg-white border-0 py-3">
        <h5 class="mb-0 fs-6"><i class="fas fa-th-large me-2"></i>Menu Utama</h5>
    </div>
    <div class="card-body">
        <div class="menu-grid">
            <?php if($student_data && in_array($student_data->status_pengajuan, array('disetujui', 'selesai'))): ?>
            <a href="<?php echo base_url('siswa/view/presensi') ?>" class="menu-item">
                <i class="fas fa-calendar-check text-primary"></i>
                <span class="fw-bold text-primary">Presensi PKL</span>
            </a>
            <a href="<?php echo base_url('siswa/view/logbook') ?>" class="menu-item">
                <i class="fas fa-book-open text-success"></i>
                <span class="fw-bold text-success">Logbook PKL</span>
            </a>
            <?php endif; ?>
            <a href="<?php echo base_url('siswa/view/id-card') ?>" class="menu-item">
                <i class="fas fa-id-card"></i>
                <span>ID Card</span>
            </a>
            <a href="<?php echo base_url('siswa/view/kontak-pembimbing') ?>" class="menu-item">
                <i class="fas fa-address-book"></i>
                <span>Info Pembimbing</span>
            </a>
            <?php 
            // Check if student already has a record
            $this->db->where('user_id', $userdata['id']);
            $existing_siswa = $this->db->get('tb_siswa')->row();
                    
            if($existing_siswa && $existing_siswa->status_pengajuan == 'menunggu') {
                // If waiting for approval, show pengajuan status page
            ?>
            <a href="<?php echo base_url('siswa/view/pengajuan') ?>" class="menu-item">
                <i class="fas fa-file-alt"></i>
                <span>Status Pengajuan</span>
            </a>
            <?php 
            } else {
                // If no application yet, rejected, draft, or approved, show buat pengajuan
                // Students with approved applications can also re-apply
            ?>
            <a href="<?php echo base_url('siswa/view/buat-pengajuan') ?>" class="menu-item">
                <i class="fas fa-plus-circle"></i>
                <span>
                    <?php echo ($existing_siswa && $existing_siswa->status_pengajuan == 'disetujui') ? 'Ajukan Ulang' : 'Buat Pengajuan'; ?>
                </span>
            </a>
            <?php } ?>
            <a href="<?php echo base_url('siswa/view/penempatan') ?>" class="menu-item">
                <i class="fas fa-building"></i>
                <span>Penempatan</span>
            </a>
        </div>
    </div>
</div>

<!-- Informasi Section -->
<?php if($pengumuman){ ?>
<div class="card border-0 rounded-lg shadow-sm">
    <div class="card-header bg-white border-0 py-3">
        <h5 class="mb-0 fs-6"><i class="fas fa-bullhorn me-2 text-primary"></i>Pengumuman Terkini</h5>
    </div>
    <div class="card-body">
        <?php foreach($pengumuman as $p){ ?>
        <div class="border-bottom pb-3 mb-3">
            <div class="d-flex align-items-start">
                <div class="flex-shrink-0 mt-1">
                    <i class="fas fa-info-circle text-primary fa-lg"></i>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h6 class="mb-1 fs-6 fw-bold"><?php echo $p->judul ?></h6>
                    <p class="small text-muted mb-2">
                        <?php echo date('d M Y', strtotime($p->created_at)); ?> | 
                        <?php 
                        $this->db->where('id', $p->created_by);
                        $creator = $this->db->get('tb_user')->row();
                        echo $creator ? $creator->nama_lengkap : 'Admin';
                        ?>
                    </p>
                    <p class="mb-2 small">
                        <?php echo substr($p->isi, 0, 100); ?>...
                    </p>
                    <a href="<?php echo base_url('siswa/view/detail-pengumuman/'.$p->pengumuman_id) ?>" class="btn btn-sm btn-outline-primary px-3 py-1">
                        <i class="fas fa-arrow-right me-1"></i>Selengkapnya
                    </a>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>
<?php } ?>


<!-- Warning Modal -->
<div class="modal fade" id="warningModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <i class="fas fa-exclamation-triangle text-warning" style="font-size: 4rem;"></i>
                <h5 class="mt-3 mb-2">Peringatan</h5>
                <p id="warning-message">Anda berada di luar radius kantor, perbarui lokasi Anda</p>
                <button type="button" class="btn btn-warning mt-3" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<script>


// Real-time clock update
function updateClock() {
    const now = new Date();
    const timeStr = now.toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit', second: '2-digit'});
    document.getElementById('current-time').textContent = timeStr;
    document.getElementById('current-time-display').textContent = timeStr;
}

// Update clock every second
setInterval(updateClock, 1000);
updateClock(); // Initial call

// Function to check for pembimbing assignment updates
function checkPembimbingAssignment() {
    // Only check if student is approved but doesn't have pembimbing info displayed
    <?php if($siswa && $siswa->status_pengajuan == 'disetujui' && !$pembimbing_info): ?>
    fetch('<?php echo base_url("siswa/check_pembimbing_assignment"); ?>')
        .then(response => response.json())
        .then(data => {
            if(data.has_pembimbing) {
                // Show notification that pembimbing has been assigned
                // Only show this notification if it hasn't been shown in the current session
                // We'll rely on the server-side notification instead to ensure it only shows once
            }
        })
        .catch(error => {
            console.log('Error checking pembimbing assignment:', error);
        });
    <?php endif; ?>
}

// Check for pembimbing assignment every 30 seconds
setInterval(checkPembimbingAssignment, 30000);


</script>

