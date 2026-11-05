<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo base_url('hubin/view') ?>"><i class="fas fa-home"></i> Home</a></li>
        <li class="breadcrumb-item"><a href="<?php echo base_url('hubin/view/data-pembimbing') ?>">Data Pembimbing</a></li>
        <li class="breadcrumb-item active">Detail Biodata Pembimbing</li>
    </ol>
</nav>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="fas fa-chalkboard-teacher me-2"></i>Detail Biodata Pembimbing</h2>
</div>

<div class="card fade-in">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-info-circle me-2"></i>Informasi Pembimbing
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <td width="30%" class="fw-bold bg-light">Nama Pembimbing</td>
                        <td><?php echo $biodata->pembimbing_nama ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">Username Login</td>
                        <td><?php echo isset($biodata->username) && !empty($biodata->username) ? htmlspecialchars($biodata->username) : '-' ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">Password Login</td>
                        <td><code><?php echo isset($biodata->password) && !empty($biodata->password) ? htmlspecialchars($biodata->password) : '-' ?></code></td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">NIP</td>
                        <td><?php echo $biodata->pembimbing_nip ? $biodata->pembimbing_nip : '-' ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">Telepon</td>
                        <td><?php echo $biodata->pembimbing_telepon ? $biodata->pembimbing_telepon : '-' ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">Email</td>
                        <td><?php echo $biodata->pembimbing_email ? $biodata->pembimbing_email : '-' ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">Alamat</td>
                        <td><?php echo $biodata->pembimbing_alamat ? $biodata->pembimbing_alamat : '-' ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">Tempat Lahir</td>
                        <td><?php echo $biodata->pembimbing_tempat_lahir ? $biodata->pembimbing_tempat_lahir : '-' ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">Tanggal Lahir</td>
                        <td><?php echo $biodata->pembimbing_tanggal_lahir ? date('d F Y', strtotime($biodata->pembimbing_tanggal_lahir)) : '-' ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">Jenis Kelamin</td>
                        <td>
                            <?php 
                            if(isset($biodata->pembimbing_jk) && !empty($biodata->pembimbing_jk)) {
                                // Handle both old format ('L'/'P') and new format ('Laki-laki'/'Perempuan')
                                if ($biodata->pembimbing_jk == 'L' || $biodata->pembimbing_jk == 'Laki-laki') {
                                    echo 'Laki-laki';
                                } elseif ($biodata->pembimbing_jk == 'P' || $biodata->pembimbing_jk == 'Perempuan') {
                                    echo 'Perempuan';
                                } else {
                                    echo $biodata->pembimbing_jk; // Display as-is if it's something else
                                }
                            } else {
                                echo '-';
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">Pendidikan Terakhir</td>
                        <td><?php echo $biodata->pendidikan_terakhir ? $biodata->pendidikan_terakhir : '-' ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">Jabatan</td>
                        <td><?php echo $biodata->jabatan ? $biodata->jabatan : '-' ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">Jurusan Keahlian</td>
                        <td><?php echo $biodata->jurusan_keahlian ? $biodata->jurusan_keahlian : '-' ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">Tahun Masuk</td>
                        <td><?php echo $biodata->tahun_masuk ? $biodata->tahun_masuk : '-' ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">Status Kepegawaian</td>
                        <td><?php echo $biodata->status_kepegawaian ? $biodata->status_kepegawaian : '-' ?></td>
                    </tr>
                    
                    <tr>
                        <td class="fw-bold bg-light">Tempat Tugas</td>
                        <td><?php echo $biodata->tempat_tugas ? $biodata->tempat_tugas : '-' ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">Kode Pembimbing</td>
                        <td><?php echo $biodata->pembimbing_code ? $biodata->pembimbing_code : '-' ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">Tanggal Dibuat</td>
                        <td><?php echo $biodata->created_at ? date('d F Y H:i:s', strtotime($biodata->created_at)) : '-' ?></td>
                    </tr>
                    <?php if($biodata->updated_at): ?>
                    <tr>
                        <td class="fw-bold bg-light">Terakhir Diubah</td>
                        <td><?php echo date('d F Y H:i:s', strtotime($biodata->updated_at)) ?></td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Print Area for table content only -->
        <div id="printArea" class="d-none d-print-block">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td width="30%" class="fw-bold">Nama Pembimbing</td>
                            <td><?php echo $biodata->pembimbing_nama ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Username Login</td>
                            <td><?php echo isset($biodata->username) && !empty($biodata->username) ? htmlspecialchars($biodata->username) : '-' ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Password Login</td>
                            <td><?php echo isset($biodata->password) && !empty($biodata->password) ? htmlspecialchars($biodata->password) : '-' ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">NIP</td>
                            <td><?php echo $biodata->pembimbing_nip ? $biodata->pembimbing_nip : '-' ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Email</td>
                            <td><?php echo $biodata->pembimbing_email ? $biodata->pembimbing_email : '-' ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Jenis Kelamin</td>
                            <td><?php echo $biodata->pembimbing_jk ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Tempat Lahir</td>
                            <td><?php echo $biodata->pembimbing_tempat_lahir ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Tanggal Lahir</td>
                            <td><?php echo $biodata->pembimbing_tanggal_lahir ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Agama</td>
                            <td><?php echo $biodata->pembimbing_agama ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Alamat</td>
                            <td><?php echo $biodata->pembimbing_alamat ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Nomor Telepon</td>
                            <td><?php echo $biodata->pembimbing_telepon ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Jumlah Siswa Bimbingan</td>
                            <td><?php echo $jumlah_siswa ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Status</td>
                            <td>
                                <?php 
                                $jumlah_siswa = count($siswa_list);
                                if($jumlah_siswa >= 20) {
                                    echo 'Penuh';
                                } elseif($jumlah_siswa > 0) {
                                    echo 'Tersedia';
                                } else {
                                    echo 'Kosong';
                                }
                                ?>
                            </td>
                        </tr>
                        <?php if($biodata->created_at): ?>
                        <tr>
                            <td class="fw-bold">Tanggal Dibuat</td>
                            <td><?php echo date('d F Y H:i:s', strtotime($biodata->created_at)) ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if($biodata->updated_at): ?>
                        <tr>
                            <td class="fw-bold">Terakhir Diubah</td>
                            <td><?php echo date('d F Y H:i:s', strtotime($biodata->updated_at)) ?></td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            <button type="button" onclick="printDiv('printArea');" class="btn btn-primary">
                <i class="fas fa-print me-2"></i>Print
            </button>
        </div>
    </div>
</div>