<?php 
// Load helper
$this->load->helper('view');

// Get pengumuman terbaru
$pengumuman = $this->db->order_by('created_at', 'DESC')->limit(3)->get('tb_pengumuman')->result();

// Hitung total berkas yang diunggah oleh semua siswa
$total_berkas = 0;
$all_students = $this->db->get('tb_siswa')->result();
foreach($all_students as $student) {
    if($student->surat_permohonan) $total_berkas++;
    if($student->surat_balasan) $total_berkas++;

}
?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="mb-0"><i class="fas fa-tachometer-alt me-3"></i>Dashboard Koordinator PKL</h1>
        <p class="text-muted mb-0" id="current-date"><?php echo date("l, d F Y"); ?></p>
    </div>
    <div class="d-flex align-items-center">
        <div class="me-3">
            <span class="badge bg-primary">
                <i class="fas fa-clock me-1"></i><span id="current-time"><?php echo date("H:i:s"); ?></span>
            </span>
        </div>
    </div>
</div>

<div class="card border-warning shadow-sm mb-4">
    <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h5 class="fw-bold mb-1"><i class="fas fa-rotate text-warning me-2"></i>Periode PKL</h5>
            <p class="text-muted mb-0 small">Arsipkan seluruh monitoring dan buka pengajuan untuk periode PKL berikutnya.</p>
        </div>
        <form method="post" action="<?php echo base_url('hubin/reset_periode_pkl'); ?>" onsubmit="return confirm('Reset periode PKL? Data presensi, logbook, penilaian, dan assignment akan diarsipkan.');">
            <button type="submit" class="btn btn-outline-warning"><i class="fas fa-rotate me-1"></i> Reset Periode PKL</button>
        </form>
    </div>
</div>

<!-- Notification Banner for Pending Submissions -->
<div class="alert alert-warning alert-dismissible fade show mb-4 d-none" role="alert" id="pending-submission-alert">
    <i class="fas fa-exclamation-triangle me-2"></i>
    <strong>Ada <span id="pending-count">0</span> pengajuan PKL menunggu!</strong> Silakan tinjau dan proses pengajuan dari siswa.
    <a href="<?php echo base_url('hubin/view/pengajuan') ?>" class="alert-link fw-bold">Lihat Pengajuan</a>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<!-- Quick Action Cards -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <a href="<?php echo base_url('hubin/view/pengajuan') ?>" class="text-decoration-none d-block">
            <div class="card border-0 rounded-lg shadow-sm h-100 text-center card-highlight-hover" style="cursor: pointer;">
                <div class="card-body p-4">
                    <div class="icon-wrapper bg-warning bg-opacity-10 text-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-3 mx-auto" style="width: 60px; height: 60px;">
                        <i class="fas fa-file-alt fa-lg"></i>
                    </div>
                    <h4 class="mb-1 fw-bold text-dark" id="card-pengajuan-menunggu">0</h4>
                    <p class="mb-0 text-muted">Pengajuan Menunggu</p>
                    <small class="text-success fw-semibold">Tinjau sekarang <i class="fas fa-arrow-right ms-1"></i></small>
                </div>
            </div>
        </a>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <a href="<?php echo base_url('hubin/view/daftar-siswa') ?>" class="text-decoration-none d-block">
            <div class="card border-0 rounded-lg shadow-sm h-100 text-center card-highlight-hover" style="cursor: pointer;">
                <div class="card-body p-4">
                    <div class="icon-wrapper bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3 mx-auto" style="width: 60px; height: 60px;">
                        <i class="fas fa-users fa-lg"></i>
                    </div>
                    <h4 class="mb-1 fw-bold text-dark" id="card-jumlah-siswa">0</h4>
                    <p class="mb-0 text-muted">Total Siswa</p>
                    <small class="text-success fw-semibold">Daftar siswa <i class="fas fa-arrow-right ms-1"></i></small>
                </div>
            </div>
        </a>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <a href="<?php echo base_url('hubin/view/pengajuan') ?>" class="text-decoration-none d-block">
            <div class="card border-0 rounded-lg shadow-sm h-100 text-center card-highlight-hover" style="cursor: pointer;">
                <div class="card-body p-4">
                    <div class="icon-wrapper bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3 mx-auto" style="width: 60px; height: 60px;">
                        <i class="fas fa-paperclip fa-lg"></i>
                    </div>
                    <h4 class="mb-1 fw-bold text-dark" id="card-total-berkas">0</h4>
                    <p class="mb-0 text-muted">Total Berkas</p>
                    <small class="text-success fw-semibold">Lihat berkas <i class="fas fa-arrow-right ms-1"></i></small>
                </div>
            </div>
        </a>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <a href="<?php echo base_url('hubin/view/data-dudi') ?>" class="text-decoration-none d-block">
            <div class="card border-0 rounded-lg shadow-sm h-100 text-center card-highlight-hover" style="cursor: pointer;">
                <div class="card-body p-4">
                    <div class="icon-wrapper bg-info bg-opacity-10 text-info rounded-circle d-inline-flex align-items-center justify-content-center mb-3 mx-auto" style="width: 60px; height: 60px;">
                        <i class="fas fa-building fa-lg"></i>
                    </div>
                    <h4 class="mb-1 fw-bold text-dark" id="card-jumlah-dudi">0</h4>
                    <p class="mb-0 text-muted">Perusahaan DUDI</p>
                    <small class="text-success fw-semibold">Lihat daftar <i class="fas fa-arrow-right ms-1"></i></small>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- Monitoring PKL Stats -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <a href="<?php echo base_url('hubin/view/monitoring-presensi') ?>" class="text-decoration-none d-block">
            <div class="card border-0 rounded-lg shadow-sm h-100 text-center card-highlight-hover" style="cursor: pointer;">
                <div class="card-body p-4">
                    <div class="icon-wrapper bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3 mx-auto" style="width: 60px; height: 60px;">
                        <i class="fas fa-calendar-check fa-lg"></i>
                    </div>
                    <h4 class="mb-1 fw-bold text-dark" id="card-total-presensi">0</h4>
                    <p class="mb-0 text-muted">Total Presensi</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-xl-3 col-md-6">
        <a href="<?php echo base_url('hubin/view/monitoring-logbook') ?>" class="text-decoration-none d-block">
            <div class="card border-0 rounded-lg shadow-sm h-100 text-center card-highlight-hover" style="cursor: pointer;">
                <div class="card-body p-4">
                    <div class="icon-wrapper bg-info bg-opacity-10 text-info rounded-circle d-inline-flex align-items-center justify-content-center mb-3 mx-auto" style="width: 60px; height: 60px;">
                        <i class="fas fa-book-reader fa-lg"></i>
                    </div>
                    <h4 class="mb-1 fw-bold text-dark" id="card-total-logbook">0</h4>
                    <p class="mb-0 text-muted">Total Logbook</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 rounded-lg shadow-sm h-100 text-center">
            <div class="card-body p-4">
                <div class="icon-wrapper bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3 mx-auto" style="width: 60px; height: 60px;">
                    <i class="fas fa-check-circle fa-lg"></i>
                </div>
                <h4 class="mb-1 fw-bold text-success" id="card-logbook-valid">0</h4>
                <p class="mb-0 text-muted">Logbook Valid</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 rounded-lg shadow-sm h-100 text-center">
            <div class="card-body p-4">
                <div class="icon-wrapper bg-danger bg-opacity-10 text-danger rounded-circle d-inline-flex align-items-center justify-content-center mb-3 mx-auto" style="width: 60px; height: 60px;">
                    <i class="fas fa-times-circle fa-lg"></i>
                </div>
                <h4 class="mb-1 fw-bold text-danger" id="card-logbook-tidak-valid">0</h4>
                <p class="mb-0 text-muted">Logbook Tidak Valid</p>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card border-0 rounded-lg shadow-sm h-100">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Status Pengajuan</h5>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="pengajuanChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card border-0 rounded-lg shadow-sm h-100">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Distribusi Siswa</h5>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="siswaChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Row -->
<div class="row g-4">

</div>

<!-- Pengumuman Terkini -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 rounded-lg shadow-sm h-100">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-bullhorn me-2"></i>Pengumuman Terkini</h5>
                <a href="<?php echo base_url('hubin/view/pengumuman') ?>" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body" id="pengumuman-container">
                <div class="d-flex justify-content-center align-items-center h-100">
                    <div class="text-center">
                        <i class="fas fa-sync-alt fa-spin text-primary fa-2x mb-3"></i>
                        <p class="mb-0 text-muted">Memuat pengumuman...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Real-time clock update
function updateClock() {
    const now = new Date();
    const timeStr = now.toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit', second: '2-digit'});
    const dateStr = now.toLocaleDateString('id-ID', {weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'});
    
    const timeElement = document.getElementById('current-time');
    const dateElement = document.getElementById('current-date');
    
    if(timeElement) timeElement.textContent = timeStr;
    if(dateElement) dateElement.textContent = dateStr;
}

// Update clock every second
setInterval(updateClock, 1000);
updateClock(); // Initial call

// Function to update Pengumuman Terkini
function updatePengumuman(data) {
    const container = document.getElementById('pengumuman-container');
    
    if(data.length === 0) {
        container.innerHTML = `
            <div class="d-flex justify-content-center align-items-center h-100">
                <div class="text-center py-5">
                    <i class="fas fa-info-circle text-muted fa-3x mb-3"></i>
                    <h6 class="mb-2">Belum ada pengumuman</h6>
                    <p class="mb-0 text-muted small">Tidak ada pengumuman terbaru saat ini</p>
                </div>
            </div>`;
        return;
    }
    
    let html = '<div class="announcement-list">';
    data.forEach(function(p, index) {
        // Hanya tampilkan maksimal 5 pengumuman
        if(index < 5) {
            html += `
                <div class="announcement-item border-bottom pb-3 mb-3 ${index === data.length - 1 || index === 4 ? 'border-0 pb-0 mb-0' : ''}">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="mb-0">${p.judul}</h6>
                        <span class="badge bg-primary ms-2">Baru</span>
                    </div>
                    <p class="text-muted mb-2 small"><i class="fas fa-calendar me-1"></i>${p.created_at}</p>
                    <p class="mb-3">${p.isi.substring(0, 120)}${p.isi.length > 120 ? '...' : ''}</p>
                    <a href="${p.url}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye me-1"></i>Lihat Detail
                    </a>
                </div>`;
        }
    });
    html += '</div>';
    
    container.innerHTML = html;
}

// Function to fetch real-time data
function fetchRealtimeData() {
    // Only fetch if element exists
    const container = document.getElementById('pengumuman-container');
    if (!container) {
        return;
    }
    
    fetch('<?php echo base_url("hubin/api_realtime_data"); ?>')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            // Update pengumuman
            updatePengumuman(data.pengumuman_terkini);
        })
        .catch(error => {
            console.error('Error fetching real-time data:', error);
            // Don't break the UI if API fails
        });
}

// Initial load
fetchRealtimeData();

// Poll every 30 seconds
setInterval(fetchRealtimeData, 30000);

// Chart.js implementation
document.addEventListener('DOMContentLoaded', function() {
    // Cache chart instances
    let pengajuanChartInstance = null;
    let siswaChartInstance = null;
    
    // Destroy existing charts if they exist
    const destroyChart = (chartInstance) => {
        if (chartInstance) {
            chartInstance.destroy();
        }
    };
    
    // Pengajuan Status Chart
    const initPengajuanChart = (stats = null) => {
        const pengajuanCtx = document.getElementById('pengajuanChart').getContext('2d');
        destroyChart(pengajuanChartInstance);
        
        // Use provided stats or defaults
        const pengajuanStats = stats || {
            pengajuan_menunggu: 0,
            pengajuan_disetujui: 0,
            pengajuan_ditolak: 0,
            pengajuan_draft: 0
        };
        
        pengajuanChartInstance = new Chart(pengajuanCtx, {
            type: 'doughnut',
            data: {
                labels: ['Menunggu', 'Disetujui', 'Ditolak', 'Draft'],
                datasets: [{
                    data: [
                        pengajuanStats.pengajuan_menunggu,
                        pengajuanStats.pengajuan_disetujui,
                        pengajuanStats.pengajuan_ditolak,
                        pengajuanStats.pengajuan_draft
                    ],
                    backgroundColor: [
                        '#ffc107',
                        '#28a745',
                        '#dc3545',
                        '#6c757d'
                    ],
                    borderColor: [
                        '#fff',
                        '#fff',
                        '#fff',
                        '#fff'
                    ],
                    borderWidth: 2,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    }
                }
            }
        });
    };
    
    // Siswa Distribution Chart
    const initSiswaChart = (stats = null) => {
        const siswaCtx = document.getElementById('siswaChart').getContext('2d');
        destroyChart(siswaChartInstance);
        
        // Use provided stats or defaults
        const siswaStats = stats || {
            jumlah_siswa: 0,
            siswa_belum_assign: 0,
            jumlah_pembimbing: 0
        };
        
        siswaChartInstance = new Chart(siswaCtx, {
            type: 'bar',
            data: {
                labels: ['Total Siswa', 'Sudah Assign', 'Belum Assign', 'Pembimbing'],
                datasets: [{
                    label: 'Jumlah',
                    data: [
                        siswaStats.jumlah_siswa,
                        (siswaStats.jumlah_siswa - siswaStats.siswa_belum_assign),
                        siswaStats.siswa_belum_assign,
                        siswaStats.jumlah_pembimbing
                    ],
                    backgroundColor: [
                        'rgba(0, 123, 255, 0.7)',
                        'rgba(40, 167, 69, 0.7)',
                        'rgba(220, 53, 69, 0.7)',
                        'rgba(108, 117, 125, 0.7)'
                    ],
                    borderColor: [
                        'rgba(0, 123, 255, 1)',
                        'rgba(40, 167, 69, 1)',
                        'rgba(220, 53, 69, 1)',
                        'rgba(108, 117, 125, 1)'
                    ],
                    borderWidth: 2,
                    borderRadius: 4,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    };
    
    // Function to fetch real-time data and update UI
    const updateRealTimeData = () => {
        // Check if required elements exist before trying to update them
        const elementsExist = 
            document.getElementById('card-pengajuan-menunggu') &&
            document.getElementById('card-jumlah-siswa') &&
            document.getElementById('card-total-berkas') &&
            document.getElementById('card-jumlah-dudi') &&
            document.getElementById('pending-count') &&
            document.getElementById('pending-submission-alert');
            
        if (!elementsExist) {
            return; // Don't proceed if elements don't exist
        }
        
        fetch('<?php echo base_url("hubin/api_realtime_data") ?>')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if(data.statistik) {
                // Update quick action cards
                const pengajuanMenungguEl = document.getElementById('card-pengajuan-menunggu');
                const jumlahSiswaEl = document.getElementById('card-jumlah-siswa');
                const totalBerkasEl = document.getElementById('card-total-berkas');
                const jumlahDudiEl = document.getElementById('card-jumlah-dudi');
                
                if (pengajuanMenungguEl) pengajuanMenungguEl.textContent = data.statistik.pengajuan_menunggu;
                if (jumlahSiswaEl) jumlahSiswaEl.textContent = data.statistik.jumlah_siswa;
                if (totalBerkasEl) totalBerkasEl.textContent = data.statistik.pengajuan_menunggu + data.statistik.pengajuan_disetujui + data.statistik.pengajuan_ditolak; // Approximation for total submissions
                if (jumlahDudiEl) jumlahDudiEl.textContent = data.statistik.jumlah_dudi;

                // Update monitoring PKL stats
                const presensiEl = document.getElementById('card-total-presensi');
                const logbookEl = document.getElementById('card-total-logbook');
                const logbookValidEl = document.getElementById('card-logbook-valid');
                const logbookInvalidEl = document.getElementById('card-logbook-tidak-valid');
                if (presensiEl && data.statistik.total_presensi !== undefined) presensiEl.textContent = data.statistik.total_presensi;
                if (logbookEl && data.statistik.total_logbook !== undefined) logbookEl.textContent = data.statistik.total_logbook;
                if (logbookValidEl && data.statistik.logbook_valid !== undefined) logbookValidEl.textContent = data.statistik.logbook_valid;
                if (logbookInvalidEl && data.statistik.logbook_tidak_valid !== undefined) logbookInvalidEl.textContent = data.statistik.logbook_tidak_valid;
                
                // Update notification banner
                const pendingCount = data.statistik.pengajuan_menunggu;
                const pendingCountEl = document.getElementById('pending-count');
                const alertElement = document.getElementById('pending-submission-alert');
                
                if (pendingCountEl) pendingCountEl.textContent = pendingCount;
                
                if(alertElement) {
                    if(pendingCount > 0) {
                        alertElement.classList.remove('d-none');
                    } else {
                        alertElement.classList.add('d-none');
                    }
                }
                
                // Update charts
                initPengajuanChart(data.statistik);
                initSiswaChart(data.statistik);
            }
        })
        .catch(error => {
            console.error('Error fetching real-time data:', error);
            // Don't break the UI if API fails
        });
    };
    
    // Initial load
    updateRealTimeData();
    
    // Refresh data every 30 seconds
    setInterval(updateRealTimeData, 30000);
    
    // Debounce function for window resize
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function() {
            updateRealTimeData();
        }, 250);
    });
});
</script>