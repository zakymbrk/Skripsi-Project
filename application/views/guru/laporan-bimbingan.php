<!-- Header Laporan Bimbingan -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="mb-0 fs-4 fw-bold text-dark"><i class="fas fa-file-invoice text-primary me-2"></i>Laporan Rekapitulasi Monitoring PKL</h1>
        <p class="text-muted mb-0 small">Rekapitulasi data kehadiran, logbook, dan evaluasi siswa bimbingan</p>
    </div>
    <div>
        <button type="button" class="btn btn-primary" onclick="printDiv('area-cetak-guru')" <?php echo empty($filter_siswa) ? '' : 'title="Mencetak rekap siswa yang dipilih"'; ?>>
            <i class="fas fa-print me-1"></i>
            <?php echo empty($filter_siswa) ? 'Cetak Semua Rekap' : 'Cetak Rekap Siswa Terpilih'; ?>
        </button>
    </div>
</div>

<!-- Filter Laporan -->
<div class="card border-0 rounded-4 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="get" action="<?php echo base_url('guru/view/laporan-bimbingan'); ?>" class="row g-2 align-items-center">
            <div class="col-md-6">
                <label class="form-label small fw-semibold mb-1">Pilih Siswa</label>
                <select name="siswa_id" class="form-select form-select-sm">
                    <option value="">-- Semua Siswa Bimbingan --</option>
                    <?php foreach($siswa_list as $s): ?>
                        <option value="<?php echo $s->siswa_id; ?>" <?php echo ($filter_siswa == $s->siswa_id) ? 'selected' : ''; ?>>
                            <?php echo $s->siswa_nama; ?> (<?php echo $s->siswa_kelas; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end gap-2 mt-auto">
                <button type="submit" class="btn btn-primary btn-sm px-3">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                <a href="<?php echo base_url('guru/view/laporan-bimbingan'); ?>" class="btn btn-light btn-sm border">
                    Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Area Cetak Laporan -->
<div id="area-cetak-guru" class="card border-0 rounded-4 shadow-sm p-4 bg-white">
    <!-- Kop Laporan -->
    <div class="text-center pb-3 mb-4 border-bottom">
        <h4 class="fw-bold mb-1">SMK ITIKURIH HIBARNA CIPARAY</h4>
        <h5 class="fw-semibold text-secondary mb-1">REKAPITULASI MONITORING PRAKTIK KERJA LAPANGAN (PKL)</h5>
        <p class="text-muted small mb-0">
            Guru Pembimbing: <strong><?php echo $pembimbing ? $pembimbing->pembimbing_nama : $this->session->userdata('userdata')['nama_lengkap']; ?></strong>
            <?php if(!empty($filter_siswa)): ?>
                | Siswa: <strong><?php echo !empty($penilaian_list) ? $penilaian_list[0]->siswa_nama : 'Siswa terpilih'; ?></strong>
            <?php else: ?>
                | Siswa: <strong>Semua Siswa Bimbingan</strong>
            <?php endif; ?>
            | Tanggal Cetak: <?php echo date('d F Y'); ?>
        </p>
    </div>

    <!-- Bagian 1: Rekap Presensi -->
    <h6 class="fw-bold text-dark mb-3"><i class="fas fa-calendar-check text-primary me-2"></i>I. Rekapitulasi Presensi Siswa</h6>
    <div class="table-responsive mb-4">
        <table class="table table-bordered align-middle">
            <thead class="table-dark text-center">
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 25%;">Nama Siswa</th>
                    <th style="width: 15%;">Tanggal</th>
                    <th style="width: 15%;">Jam Masuk</th>
                    <th style="width: 15%;">Jam Pulang</th>
                    <th style="width: 10%;">Status</th>
                    <th style="width: 15%;">Validasi (RBV)</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($presensi_list)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-3 text-muted">Tidak ada data presensi.</td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach($presensi_list as $p): ?>
                        <tr>
                            <td class="text-center"><?php echo $no++; ?></td>
                            <td><strong><?php echo $p->siswa_nama; ?></strong> (<?php echo $p->siswa_kelas; ?>)</td>
                            <td class="text-center"><?php echo date('d/m/Y', strtotime($p->tanggal)); ?></td>
                            <td class="text-center"><?php echo $p->jam_masuk ?: '-'; ?></td>
                            <td class="text-center"><?php echo $p->jam_pulang ?: '-'; ?></td>
                            <td class="text-center"><?php echo ucfirst($p->status_kehadiran); ?></td>
                            <td class="text-center text-success fw-semibold"><?php echo strtoupper($p->status_validasi); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Bagian 2: Rekap Logbook -->
    <h6 class="fw-bold text-dark mb-3"><i class="fas fa-book-open text-primary me-2"></i>II. Rekapitulasi Logbook Aktivitas PKL</h6>
    <div class="table-responsive mb-4">
        <table class="table table-bordered align-middle">
            <thead class="table-dark text-center">
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 25%;">Nama Siswa</th>
                    <th style="width: 15%;">Tanggal</th>
                    <th style="width: 35%;">Uraian Pekerjaan</th>
                    <th style="width: 20%;">Catatan Pembimbing</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($logbook_list)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-3 text-muted">Tidak ada data logbook.</td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach($logbook_list as $l): ?>
                        <tr>
                            <td class="text-center"><?php echo $no++; ?></td>
                            <td><strong><?php echo $l->siswa_nama; ?></strong></td>
                            <td class="text-center"><?php echo date('d/m/Y', strtotime($l->tanggal)); ?></td>
                            <td><small><?php echo htmlspecialchars($l->deskripsi_kegiatan); ?></small></td>
                            <td><small class="text-primary"><?php echo htmlspecialchars($l->catatan_pembimbing ?: 'Belum dievaluasi'); ?></small></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Bagian 3: Rekap Evaluasi & Penilaian (Rule-Based Validation RBV-016) -->
    <h6 class="fw-bold text-dark mb-3"><i class="fas fa-award text-primary me-2"></i>III. Rekapitulasi Evaluasi & Penilaian Monitoring PKL</h6>
    <div class="table-responsive mb-4">
        <table class="table table-bordered align-middle">
            <thead class="table-dark text-center">
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 25%;">Nama Siswa</th>
                    <th style="width: 15%;">Tempat PKL</th>
                    <th style="width: 12%;">Nilai Kehadiran</th>
                    <th style="width: 12%;">Logbook Valid</th>
                    <th style="width: 15%;">Penalti Otomatis (5%)</th>
                    <th style="width: 16%;">Nilai Akhir PKL</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($penilaian_list)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-3 text-muted">Belum ada data evaluasi penilaian.</td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach($penilaian_list as $pn): ?>
                        <tr>
                            <td class="text-center"><?php echo $no++; ?></td>
                            <td>
                                <strong><?php echo $pn->siswa_nama; ?></strong><br>
                                <small class="text-muted"><?php echo $pn->siswa_kelas; ?> | NIS: <?php echo $pn->siswa_nis ?: '-'; ?></small>
                            </td>
                            <td><?php echo $pn->dudi_nama ?: '-'; ?></td>
                            <td class="text-center fw-semibold"><?php echo isset($pn->nilai_kehadiran) ? $pn->nilai_kehadiran : (isset($pn->nilai_presensi) ? $pn->nilai_presensi : 100); ?></td>
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

    <!-- Tanda Tangan Pembimbing -->
    <div class="row mt-5 pt-4">
        <div class="col-6 text-center">
            <p class="small mb-5">Mengetahui,<br><strong>Koordinator Hubin</strong></p>
            <br>
            <p class="fw-bold mb-0"><u>Staff Hubin</u></p>
            <small class="text-muted">SMK ITIKURIH HIBARNA</small>
        </div>
        <div class="col-6 text-center">
            <p class="small mb-5">Ciparay, <?php echo date('d F Y'); ?><br><strong>Guru Pembimbing</strong></p>
            <br>
            <p class="fw-bold mb-0"><u><?php echo $pembimbing ? $pembimbing->pembimbing_nama : $this->session->userdata('userdata')['nama_lengkap']; ?></u></p>
            <small class="text-muted">NIP: <?php echo ($pembimbing && !empty($pembimbing->pembimbing_nip)) ? $pembimbing->pembimbing_nip : '-'; ?></small>
        </div>
    </div>
</div>

<script>
    function printDiv(divId) {
        var printContents = document.getElementById(divId).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        window.location.reload();
    }
</script>
