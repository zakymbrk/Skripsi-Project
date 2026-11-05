<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    #monitoring-map { height: 520px; min-height: 360px; border-radius: 12px; z-index: 1; }
    .monitoring-location-header,
    .monitoring-location-header h5,
    .monitoring-location-header small { color: #212529 !important; }
    .monitoring-location-header { background: #ffffff !important; }
    .monitoring-location-page .card-header { background: #ffffff !important; color: #212529 !important; }
    .monitoring-location-page .table thead { background: #f8f9fa !important; color: #212529 !important; }
    .monitoring-location-page .table thead th { color: #212529 !important; }
    .student-marker { width: 34px; height: 34px; border: 3px solid #fff; border-radius: 50%; display: grid; place-items: center; color: #fff; box-shadow: 0 2px 8px rgba(0,0,0,.35); background-position: center; background-size: cover; background-repeat: no-repeat; overflow: hidden; }
    .student-marker.online { background-color: #198754; }
    .student-marker.stale { background-color: #6c757d; }
    .student-marker.has-photo { background-color: #ffffff; }
    .student-marker.has-photo i { display: none; }
    .student-marker:not(.has-photo) { border-radius: 50%; }
</style>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- Header Monitoring Lokasi -->
<div class="monitoring-location-page">
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="mb-0 fs-4 fw-bold text-dark"><i class="fas fa-map-location-dot text-primary me-2"></i>Monitoring Lokasi Siswa</h1>
        <p class="text-muted mb-0 small">Lokasi siswa bimbingan selama PKL sampai presensi pulang</p>
    </div>
    <div>
        <a href="<?php echo base_url('guru/view/laporan-bimbingan'); ?>" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-print me-1"></i> Cetak Rekapitulasi
        </a>
    </div>
</div>

<div class="card border-0 rounded-4 shadow-sm mb-4">
    <div class="card-header monitoring-location-header bg-white py-3 px-4 border-0 d-flex flex-wrap gap-2 justify-content-between align-items-center">
        <div>
            <h5 class="mb-1 fs-6 fw-bold text-dark"><i class="fas fa-location-crosshairs text-success me-2"></i>Siswa Sedang Aktif PKL</h5>
            <small class="text-muted" id="location-last-updated">Menunggu pembaruan lokasi...</small>
        </div>
        <div class="d-flex gap-2 small">
            <span class="badge bg-success"><i class="fas fa-circle me-1"></i>Online</span>
            <span class="badge bg-secondary"><i class="fas fa-circle me-1"></i>Terakhir terlihat</span>
        </div>
    </div>
    <div class="card-body p-3">
        <div id="monitoring-map"></div>
        <div id="location-message" class="small text-muted mt-3">Memuat lokasi siswa...</div>
        <div id="location-list" class="row g-2 mt-1"></div>
    </div>
</div>

<!-- Alert Notifikasi -->
<?php if($this->session->flashdata('message')): ?>
<div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
    <i class="fas fa-check-circle me-2"></i> <?php echo $this->session->flashdata('message'); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Filter Presensi -->
<div class="card border-0 rounded-4 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="get" action="<?php echo base_url('guru/view/monitoring-presensi'); ?>" class="row g-2 align-items-center">
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
                <label class="form-label small fw-semibold mb-1">Filter Tanggal</label>
                <input type="date" name="tanggal" value="<?php echo $filter_tanggal; ?>" class="form-control form-control-sm">
            </div>
            <div class="col-md-3 d-flex align-items-end gap-2 mt-auto">
                <button type="submit" class="btn btn-primary btn-sm px-3">
                    <i class="fas fa-filter me-1"></i> Filter Data
                </button>
                <a href="<?php echo base_url('guru/view/monitoring-presensi'); ?>" class="btn btn-light btn-sm border">
                    Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Riwayat Presensi -->
<div class="card border-0 rounded-4 shadow-sm mb-4">
    <div class="card-header bg-white py-3 px-4 border-0 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fs-6 fw-bold text-dark"><i class="fas fa-list-check me-2"></i>Riwayat Presensi Siswa Bimbingan</h5>
        <span class="badge bg-light text-dark border"><?php echo count($presensi_list); ?> Catatan Presensi</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">No</th>
                        <th>Nama Siswa</th>
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
                    <?php if(empty($presensi_list)): ?>
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">Tidak ada data presensi yang sesuai dengan filter.</td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach($presensi_list as $p): ?>
                            <tr>
                                <td class="ps-4 text-muted small"><?php echo $no++; ?></td>
                                <td>
                                    <strong class="text-dark"><?php echo $p->siswa_nama; ?></strong><br>
                                    <small class="text-muted">NIS: <?php echo $p->siswa_nis ?: '-'; ?> | <?php echo $p->siswa_kelas; ?></small>
                                </td>
                                <td>
                                    <strong><?php echo date('d/m/Y', strtotime($p->tanggal)); ?></strong><br>
                                    <small class="text-muted"><?php echo date('l', strtotime($p->tanggal)); ?></small>
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
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success"><i class="fas fa-check-circle me-1"></i>Valid (RBV)</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">Tidak Valid</span>
                                        <?php if(!empty($p->alasan_penolakan)): ?><small class="d-block text-danger mt-1"><?php echo htmlspecialchars($p->alasan_penolakan); ?></small><?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td class="pe-4 text-center">
                                    <button type="button" class="btn btn-light btn-sm rounded-circle" data-bs-toggle="modal" data-bs-target="#modalGuruPresensi<?php echo $p->presensi_id; ?>">
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

<!-- Modal Detail Presensi Guru (Ditaruh di luar table & card agar bebas stacking context CSS) -->
<?php if(!empty($presensi_list)): ?>
    <?php foreach($presensi_list as $p): ?>
        <div class="modal fade" id="modalGuruPresensi<?php echo $p->presensi_id; ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow">
                    <div class="modal-header bg-dark text-white py-3">
                        <h6 class="modal-title fw-bold"><i class="fas fa-info-circle text-primary me-2"></i>Detail Presensi: <?php echo htmlspecialchars($p->siswa_nama); ?></h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3 text-muted small">
                            <strong>Tempat PKL:</strong> <?php echo $p->dudi_nama ?: '-'; ?><br>
                            <strong>Tanggal:</strong> <?php echo date('d F Y', strtotime($p->tanggal)); ?>
                        </div>
                        <div class="small mb-2">
                            <strong>Audit Geolocation & Radius:</strong><br>
                            <span>Status Lokasi: <span class="badge bg-<?php echo (isset($p->status_lokasi) && $p->status_lokasi == 'valid') ? 'success' : 'secondary'; ?>"><?php echo ucfirst(str_replace('_', ' ', isset($p->status_lokasi) ? $p->status_lokasi : 'N/A')); ?></span></span><br>
                            <span>Jarak ke Kantor DUDI: <strong><?php echo isset($p->jarak) && $p->jarak !== null ? round($p->jarak) . ' meter' : '-'; ?></strong> (Batas Radius: <?php echo isset($p->radius) && $p->radius ? $p->radius . 'm' : '-'; ?>)</span><br>
                            <?php if(!empty($p->latitude_siswa) && !empty($p->longitude_siswa)): ?>
                                <span>Koordinat Siswa: <?php echo $p->latitude_siswa; ?>, <?php echo $p->longitude_siswa; ?></span>
                                <a href="https://maps.google.com/?q=<?php echo $p->latitude_siswa; ?>,<?php echo $p->longitude_siswa; ?>" target="_blank" class="btn btn-xs btn-outline-primary py-0 px-1 ms-1 small">
                                    <i class="fas fa-external-link-alt"></i> Peta
                                </a>
                            <?php endif; ?>
                        </div>
                        <hr>
                        <p class="mb-1 small"><strong>Keterangan Masuk:</strong> <?php echo htmlspecialchars($p->keterangan_masuk ?: 'Presensi masuk ' . $p->siswa_nama . ' pada pukul ' . ($p->jam_masuk ?: '-') . ' di tempat PKL.'); ?></p>
                        <p class="mb-0 small"><strong>Keterangan Pulang:</strong> <?php echo htmlspecialchars($p->keterangan_pulang ?: ($p->jam_pulang ? 'Presensi pulang ' . $p->siswa_nama . ' pada pukul ' . $p->jam_pulang . ' setelah kegiatan PKL selesai.' : 'Belum melakukan presensi pulang.')); ?></p>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

</div>

<script>
(function () {
    var mapElement = document.getElementById('monitoring-map');
    if (!mapElement || typeof L === 'undefined') return;

    var map = L.map(mapElement).setView([-6.9175, 107.6191], 11);
    L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        maxZoom: 19,
        attribution: 'Tiles &copy; Esri'
    }).addTo(map);

    var markers = {};
    var circles = {};
    var apiUrl = '<?php echo base_url('guru/api_lokasi_siswa'); ?>';
    var messageElement = document.getElementById('location-message');
    var updatedElement = document.getElementById('location-last-updated');
    var listElement = document.getElementById('location-list');

    window.setTimeout(function () { map.invalidateSize(); }, 250);

    function formatTime(value) {
        if (!value) return '-';
        return new Date(value.replace(' ', 'T')).toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'});
    }

    function refreshLocations() {
        fetch(apiUrl, {headers: {'Accept': 'application/json'}, credentials: 'same-origin'})
            .then(function (response) { return response.json(); })
            .then(function (payload) {
                if (!payload.success) throw new Error(payload.message || 'Lokasi tidak dapat dimuat.');
                var visible = {};
                var bounds = [];
                listElement.innerHTML = '';

                payload.locations.forEach(function (student) {
                    visible[student.siswa_id] = true;
                    var latLng = [Number(student.latitude_siswa), Number(student.longitude_siswa)];
                    bounds.push(latLng);
                    var markerClass = student.is_online ? 'online' : 'stale';
                    var photoUrl = student.foto_profil_url || '<?php echo base_url('assets/img/default-avatar.png'); ?>';
                    var backgroundStyle = photoUrl ? 'background-image:url(\'' + photoUrl + '\'); background-size:cover; background-position:center; background-repeat:no-repeat;' : '';
                    var iconHtml = '<div class="student-marker ' + markerClass + (photoUrl ? ' has-photo' : '') + '" style="' + backgroundStyle + ' border-radius:50%; overflow:hidden;">' + (photoUrl ? '' : '<i class="fas fa-user"></i>') + '</div>';
                    var icon = L.divIcon({className: '', html: iconHtml, iconSize: [34, 34], iconAnchor: [17, 17]});
                    if (markers[student.siswa_id]) {
                        markers[student.siswa_id].setLatLng(latLng).setIcon(icon);
                    } else {
                        markers[student.siswa_id] = L.marker(latLng, {icon: icon}).addTo(map);
                    }
                    markers[student.siswa_id].bindPopup('<strong>' + student.siswa_nama + '</strong><br>' + (student.dudi_nama || 'Tempat PKL belum diisi') + '<br>Jarak: ' + (student.jarak ? Math.round(student.jarak) + ' m' : '-') + ' / radius ' + (student.radius || '-') + ' m<br>Terakhir terlihat: ' + formatTime(student.last_seen));

                    if (student.dudi_latitude && student.dudi_longitude && !circles[student.siswa_id]) {
                        circles[student.siswa_id] = L.circle([Number(student.dudi_latitude), Number(student.dudi_longitude)], {radius: Number(student.radius) || 100, color: '#0d6efd', fillColor: '#0d6efd', fillOpacity: 0.08, weight: 1}).addTo(map);
                    }
                    var statusClass = student.is_online ? 'success' : 'secondary';
                    listElement.insertAdjacentHTML('beforeend', '<div class="col-md-6 col-xl-4"><div class="border rounded-3 p-2 small d-flex justify-content-between align-items-center"><span><strong>' + student.siswa_nama + '</strong><br><span class="text-muted">' + (student.dudi_nama || '-') + '</span></span><span class="text-end"><span class="badge bg-' + statusClass + '">' + (student.is_online ? 'Online' : 'Tidak aktif') + '</span><br><span class="text-muted">' + (student.jarak ? Math.round(student.jarak) + ' m' : '-') + '</span></span></div></div>');
                });

                Object.keys(markers).forEach(function (id) {
                    if (!visible[id]) { map.removeLayer(markers[id]); delete markers[id]; }
                });
                if (bounds.length) map.fitBounds(bounds, {padding: [35, 35], maxZoom: 16});
                messageElement.textContent = payload.locations.length ? payload.locations.length + ' siswa masih aktif dan belum presensi pulang.' : 'Belum ada siswa dengan lokasi aktif hari ini.';
                updatedElement.textContent = 'Diperbarui ' + new Date().toLocaleTimeString('id-ID') + ' • otomatis setiap 30 detik';
            })
            .catch(function (error) { messageElement.textContent = error.message; messageElement.className = 'small text-danger mt-3'; });
    }

    refreshLocations();
    window.setInterval(refreshLocations, 30000);
})();
</script>

