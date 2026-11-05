<?php
$CI =& get_instance();
$CI->load->model('M_presensi');
$CI->load->model('M_logbook');
$CI->load->library('rule_based_validation');

$presensi_list  = $CI->M_presensi->get_all_presensi_global(null, null, 200);
$logbook_list   = $CI->M_logbook->get_all_logbook_global(null, 200);

// Ambil data evaluasi & penilaian seluruh siswa
$all_students = $CI->db->get('tb_siswa')->result();
foreach ($all_students as $st) {
    $CI->rule_based_validation->sync_penilaian_siswa($st->siswa_id);
}

$CI->db->select('p.*, s.siswa_nama, s.siswa_nis, s.siswa_kelas, d.dudi_nama, pb.pembimbing_nama');
$CI->db->from('tb_penilaian p');
$CI->db->join('tb_siswa s', 's.siswa_id = p.siswa_id');
$CI->db->join('tb_dudi d', 'd.dudi_id = s.dudi_id', 'left');
$CI->db->join('tb_pembimbing pb', 'pb.pembimbing_id = s.pembimbing_id', 'left');
$penilaian_global = $CI->db->get()->result();
?>

<!-- Header Laporan Monitoring Hubin -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="mb-0 fs-4 fw-bold text-dark"><i class="fas fa-file-invoice text-primary me-2"></i>Laporan Rekapitulasi Monitoring PKL</h1>
        <p class="text-muted mb-0 small">Rekap data kehadiran, aktivitas logbook, dan evaluasi nilai PKL seluruh siswa SMK ITIKURIH HIBARNA</p>
    </div>
    <div>
        <button type="button" class="btn btn-primary" onclick="printDiv('area-cetak-hubin')">
            <i class="fas fa-print me-1"></i> Cetak Laporan Terpilih
        </button>
    </div>
</div>

<div class="alert alert-info d-flex align-items-center gap-2 mb-4">
    <i class="fas fa-info-circle"></i>
    <span>Centang siswa pada kolom paling kiri tabel, kemudian klik <strong>Cetak Laporan Terpilih</strong>.</span>
</div>

<div class="card border-0 rounded-4 shadow-sm mb-4">
    <div class="card-body p-3">
        <label for="cari-nama-siswa" class="form-label small fw-semibold mb-1">Cari Nama Siswa</label>
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
            <input type="search" id="cari-nama-siswa" class="form-control" placeholder="Ketik nama siswa..." autocomplete="off">
        </div>
    </div>
</div>

<!-- Area Cetak Laporan Hubin -->
<div id="area-cetak-hubin" class="card border-0 rounded-4 shadow-sm p-4 bg-white">
    <!-- Kop Laporan -->
    <div class="text-center pb-3 mb-4 border-bottom">
        <h4 class="fw-bold mb-1">SMK ITIKURIH HIBARNA CIPARAY</h4>
        <h5 class="fw-semibold text-secondary mb-1">LAPORAN MONITORING PRAKTIK KERJA LAPANGAN (PKL)</h5>
        <p class="text-muted small mb-0">DIVISI HUBUNGAN INDUSTRI (HUBIN) & MASYARAKAT | Tanggal Cetak: <?php echo date('d F Y'); ?></p>
    </div>

    <!-- Bagian 1: Rekap Presensi Global -->
    <h6 class="fw-bold text-dark mb-3"><i class="fas fa-calendar-check text-primary me-2"></i>I. Data Presensi Kehadiran Siswa PKL</h6>
    <div class="table-responsive mb-4">
        <table class="table table-bordered align-middle">
            <thead class="table-light text-center">
                <tr>
                    <th class="no-print" style="width: 5%;">Pilih</th>
                    <th style="width: 5%;">No</th>
                    <th style="width: 20%;">Nama Siswa</th>
                    <th style="width: 15%;">Kelas / NIS</th>
                    <th style="width: 20%;">Tempat PKL (DUDI)</th>
                    <th style="width: 12%;">Tanggal</th>
                    <th style="width: 10%;">Masuk</th>
                    <th style="width: 10%;">Pulang</th>
                    <th style="width: 8%;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($presensi_list)): ?>
                    <tr>
                        <td colspan="9" class="text-center py-3 text-muted">Tidak ada data presensi.</td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach($presensi_list as $p): ?>
                        <tr data-siswa-id="<?php echo (int)$p->siswa_id; ?>">
                            <td class="text-center no-print"><input type="checkbox" class="form-check-input student-print-check" data-siswa-id="<?php echo (int)$p->siswa_id; ?>" aria-label="Pilih <?php echo htmlspecialchars($p->siswa_nama); ?>"></td>
                            <td class="text-center"><?php echo $no++; ?></td>
                            <td><strong><?php echo $p->siswa_nama; ?></strong></td>
                            <td><?php echo $p->siswa_kelas; ?> / <?php echo $p->siswa_nis ?: '-'; ?></td>
                            <td><?php echo $p->dudi_nama ?: '-'; ?></td>
                            <td class="text-center"><?php echo date('d/m/Y', strtotime($p->tanggal)); ?></td>
                            <td class="text-center"><?php echo $p->jam_masuk ?: '-'; ?></td>
                            <td class="text-center"><?php echo $p->jam_pulang ?: '-'; ?></td>
                            <td class="text-center"><span class="badge bg-light text-dark border"><?php echo ucfirst($p->status_kehadiran); ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Bagian 2: Rekap Logbook Global -->
    <h6 class="fw-bold text-dark mb-3"><i class="fas fa-book-open text-primary me-2"></i>II. Data Logbook Aktivitas Kegiatan Siswa PKL</h6>
    <div class="table-responsive mb-4">
        <table class="table table-bordered align-middle">
            <thead class="table-light text-center">
                <tr>
                    <th class="no-print" style="width: 5%;">Pilih</th>
                    <th style="width: 5%;">No</th>
                    <th style="width: 20%;">Nama Siswa</th>
                    <th style="width: 12%;">Tanggal</th>
                    <th style="width: 35%;">Uraian Pekerjaan / Tugas</th>
                    <th style="width: 15%;">Guru Pembimbing</th>
                    <th style="width: 13%;">Catatan Evaluasi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($logbook_list)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-3 text-muted">Tidak ada data logbook.</td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach($logbook_list as $l): ?>
                        <tr data-siswa-id="<?php echo (int)$l->siswa_id; ?>">
                            <td class="text-center no-print"><input type="checkbox" class="form-check-input student-print-check" data-siswa-id="<?php echo (int)$l->siswa_id; ?>" aria-label="Pilih <?php echo htmlspecialchars($l->siswa_nama); ?>"></td>
                            <td class="text-center"><?php echo $no++; ?></td>
                            <td><strong><?php echo $l->siswa_nama; ?></strong> (<?php echo $l->siswa_kelas; ?>)</td>
                            <td class="text-center"><?php echo date('d/m/Y', strtotime($l->tanggal)); ?></td>
                            <td><small><?php echo htmlspecialchars($l->deskripsi_kegiatan); ?></small></td>
                            <td><small><?php echo $l->pembimbing_nama ?: '-'; ?></small></td>
                            <td><small class="text-primary"><?php echo htmlspecialchars($l->catatan_pembimbing ?: 'Belum dievaluasi'); ?></small></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Bagian 3: Rekap Evaluasi & Penilaian Monitoring PKL -->
    <h6 class="fw-bold text-dark mb-3"><i class="fas fa-award text-primary me-2"></i>III. Rekapitulasi Evaluasi & Penilaian Monitoring PKL</h6>
    <div class="table-responsive mb-4">
        <table class="table table-bordered align-middle">
            <thead class="table-light text-center">
                <tr>
                    <th class="no-print" style="width: 5%;">Pilih</th>
                    <th style="width: 5%;">No</th>
                    <th style="width: 20%;">Nama Siswa</th>
                    <th style="width: 15%;">Tempat PKL</th>
                    <th style="width: 15%;">Pembimbing</th>
                    <th style="width: 10%;">Nilai Hadir</th>
                    <th style="width: 10%;">Logbook Valid</th>
                    <th style="width: 12%;">Penalti Otomatis (5%)</th>
                    <th style="width: 13%;">Nilai Akhir</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($penilaian_global)): ?>
                    <tr>
                        <td colspan="9" class="text-center py-3 text-muted">Belum ada data penilaian.</td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach($penilaian_global as $pn): ?>
                        <tr data-siswa-id="<?php echo (int)$pn->siswa_id; ?>">
                            <td class="text-center no-print"><input type="checkbox" class="form-check-input student-print-check" data-siswa-id="<?php echo (int)$pn->siswa_id; ?>" aria-label="Pilih <?php echo htmlspecialchars($pn->siswa_nama); ?>"></td>
                            <td class="text-center"><?php echo $no++; ?></td>
                            <td>
                                <strong><?php echo $pn->siswa_nama; ?></strong><br>
                                <small class="text-muted"><?php echo $pn->siswa_kelas; ?> | NIS: <?php echo $pn->siswa_nis ?: '-'; ?></small>
                            </td>
                            <td><?php echo $pn->dudi_nama ?: '-'; ?></td>
                            <td><small><?php echo $pn->pembimbing_nama ?: '-'; ?></small></td>
                            <td class="text-center"><?php echo isset($pn->nilai_kehadiran) ? $pn->nilai_kehadiran : (isset($pn->nilai_presensi) ? $pn->nilai_presensi : 100); ?></td>
                            <td class="text-center text-success fw-bold">
                                <?php 
                                $valid = isset($pn->total_logbook_valid) ? (int)$pn->total_logbook_valid : 0;
                                $invalid = isset($pn->total_logbook_tidak_valid) ? (int)$pn->total_logbook_tidak_valid : 0;
                                $total = isset($pn->total_logbook) ? (int)$pn->total_logbook : ($valid + $invalid);
                                echo $valid . ' / ' . $total;
                                ?>
                            </td>
                            <td class="text-center text-danger fw-semibold">
                                <?php 
                                $penalti = isset($pn->total_pengurangan_nilai) ? $pn->total_pengurangan_nilai : (isset($pn->pengurangan_nilai) ? $pn->pengurangan_nilai : 0);
                                $bolos = isset($pn->total_bolos) ? (int)$pn->total_bolos : 0;
                                $terlambat = isset($pn->total_terlambat) ? (int)$pn->total_terlambat : 0;
                                echo $penalti > 0 ? '-'.$penalti.' Poin' : '0 Poin';
                                ?>
                                <br><small class="text-muted">Bolos: <?php echo $bolos; ?> | Telat: <?php echo $terlambat; ?><br>5% per kejadian</small>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary fs-6 px-3 py-2">
                                    <?php echo round(isset($pn->nilai_akhir) ? $pn->nilai_akhir : 100, 2); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Tanda Tangan Hubin -->
    <div class="row mt-5 pt-4">
        <div class="col-6 text-center">
            <p class="small mb-5">Mengetahui,<br><strong>Kepala Sekolah</strong></p>
            <br>
            <p class="fw-bold mb-0"><u>Kepala SMK ITIKURIH HIBARNA</u></p>
            <small class="text-muted">NIP. .....................................</small>
        </div>
        <div class="col-6 text-center">
            <p class="small mb-5">Ciparay, <?php echo date('d F Y'); ?><br><strong>Koordinator Hubin</strong></p>
            <br>
            <p class="fw-bold mb-0"><u><?php echo $this->session->userdata('userdata')['nama_lengkap']; ?></u></p>
            <small class="text-muted">Divisi Hubungan Industri & PKL</small>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.student-print-check').forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            document.querySelectorAll('.student-print-check[data-siswa-id="' + this.dataset.siswaId + '"]').forEach(function (sameStudentCheckbox) {
                sameStudentCheckbox.checked = checkbox.checked;
            });
        });
    });

    document.getElementById('cari-nama-siswa').addEventListener('input', function () {
        var keyword = this.value.trim().toLowerCase();
        document.querySelectorAll('tr[data-siswa-id]').forEach(function (row) {
            row.style.display = row.textContent.toLowerCase().indexOf(keyword) !== -1 ? '' : 'none';
        });
    });

    function printDiv(divId) {
        var selectedIds = Array.from(document.querySelectorAll('.student-print-check:checked'))
            .map(function (checkbox) { return checkbox.dataset.siswaId; });

        if (selectedIds.length === 0) {
            window.alert('Pilih minimal satu siswa yang ingin dicetak.');
            return;
        }

        var printArea = document.getElementById(divId).cloneNode(true);
        printArea.querySelectorAll('tr[data-siswa-id]').forEach(function (row) {
            if (selectedIds.indexOf(row.dataset.siswaId) === -1) {
                row.remove();
            } else {
                row.style.display = '';
            }
        });
        printArea.querySelectorAll('.no-print').forEach(function (element) {
            element.remove();
        });

        var printContents = printArea.innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        window.location.reload();
    }
</script>
