<?php
// Models are loaded in controller
// $this->load->model('M_pembimbing');
// $this->load->model('M_pengelompokan');

// Get pembimbing data by code
$pembimbing = null;
$siswa_list = array();

if(isset($pembimbing_code)){
    // Get pembimbing data (no longer joined with user table)
    $pembimbing = $this->M_pembimbing->get_pembimbing_with_biodata($pembimbing_code);
    
    if($pembimbing){
        // Get siswa assigned to this pembimbing
        $siswa_query = $this->M_pengelompokan->get_siswa_by_pembimbing($pembimbing->pembimbing_id);
        $siswa_list = $siswa_query->result();
    }
}
?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo base_url('hubin/view') ?>"><i class="fas fa-home"></i> Home</a></li>
        <li class="breadcrumb-item"><a href="<?php echo base_url('hubin/view/data-pembimbing') ?>">Data Pembimbing</a></li>
        <li class="breadcrumb-item active">Detail Pembimbing</li>
    </ol>
</nav>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="fas fa-user-tie me-2"></i>Detail Pembimbing</h2>
    <?php if($pembimbing): ?>
    <div>
        <a href="<?php echo base_url('hubin/print_biodata_pembimbing/'.$pembimbing->pembimbing_code) ?>" 
           target="_blank" class="btn btn-info">
            <i class="fas fa-print me-2"></i>Cetak Biodata
        </a>
        <a href="<?php echo base_url('hubin/edit_pembimbing/'.$pembimbing->pembimbing_code) ?>" 
           class="btn btn-warning">
            <i class="fas fa-edit me-2"></i>Edit Data
        </a>
    </div>
    <?php endif; ?>
</div>

<?php if(!$pembimbing): ?>
<div class="alert alert-danger">
    <i class="fas fa-exclamation-circle me-2"></i>
    Data pembimbing tidak ditemukan.
</div>
<?php else: ?>

<?php
$nama_pembimbing = $pembimbing->pembimbing_nama ?: $pembimbing->nama_lengkap;
$email_pembimbing = $pembimbing->pembimbing_email ?: $pembimbing->user_email;
$telepon_pembimbing = $pembimbing->pembimbing_telepon ?: $pembimbing->user_telepon;
$alamat_pembimbing = $pembimbing->pembimbing_alamat ?: $pembimbing->user_alamat;
?>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-info-circle me-2"></i>Detail Informasi Pembimbing
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <td width="30%" class="fw-bold bg-light">Nama Pembimbing</td>
                        <td><?php echo htmlspecialchars($nama_pembimbing ?: '-') ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">Username Login</td>
                        <td><?php echo htmlspecialchars($pembimbing->username ?: '-') ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">Password Login</td>
                        <td><code><?php echo htmlspecialchars($pembimbing->password ?: '-') ?></code></td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">NIP</td>
                        <td><?php echo isset($pembimbing->pembimbing_nip) && !empty($pembimbing->pembimbing_nip) ? $pembimbing->pembimbing_nip : '-' ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">Tempat Tugas</td>
                        <td><?php echo isset($pembimbing->tempat_tugas) && !empty($pembimbing->tempat_tugas) ? $pembimbing->tempat_tugas : '-' ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">Tempat Lahir</td>
                        <td><?php echo isset($pembimbing->tempat_lahir) && !empty($pembimbing->tempat_lahir) ? $pembimbing->tempat_lahir : '-' ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">Tanggal Lahir</td>
                        <td><?php echo isset($pembimbing->tanggal_lahir) && !empty($pembimbing->tanggal_lahir) ? date('d F Y', strtotime($pembimbing->tanggal_lahir)) : '-' ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">Jenis Kelamin</td>
                        <td>
                            <?php 
                            if(isset($pembimbing->jenis_kelamin) && !empty($pembimbing->jenis_kelamin)) {
                                // Handle both old format ('L'/'P') and new format ('Laki-laki'/'Perempuan')
                                if ($pembimbing->jenis_kelamin == 'L' || $pembimbing->jenis_kelamin == 'Laki-laki') {
                                    echo 'Laki-laki';
                                } elseif ($pembimbing->jenis_kelamin == 'P' || $pembimbing->jenis_kelamin == 'Perempuan') {
                                    echo 'Perempuan';
                                } else {
                                    echo $pembimbing->jenis_kelamin; // Display as-is if it's something else
                                }
                            } else {
                                echo '-';
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">Alamat</td>
                        <td><?php echo $alamat_pembimbing ? nl2br(htmlspecialchars($alamat_pembimbing)) : '-' ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">Pendidikan Terakhir</td>
                        <td><?php echo isset($pembimbing->pendidikan_terakhir) && !empty($pembimbing->pendidikan_terakhir) ? $pembimbing->pendidikan_terakhir : '-' ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">Jurusan Keahlian</td>
                        <td><?php echo isset($pembimbing->jurusan_keahlian) && !empty($pembimbing->jurusan_keahlian) ? $pembimbing->jurusan_keahlian : '-' ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">Jabatan</td>
                        <td><?php echo isset($pembimbing->jabatan) && !empty($pembimbing->jabatan) ? $pembimbing->jabatan : '-' ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">Tahun Masuk</td>
                        <td><?php echo isset($pembimbing->tahun_masuk) && !empty($pembimbing->tahun_masuk) ? $pembimbing->tahun_masuk : '-' ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">Status Kepegawaian</td>
                        <td>
                            <?php 
                            if(isset($pembimbing->status_kepegawaian) && !empty($pembimbing->status_kepegawaian)) {
                                $status_labels = [
                                    'PNS' => 'Pegawai Negeri Sipil',
                                    'CPNS' => 'Calon Pegawai Negeri Sipil',
                                    'Honorer' => 'Honorer',
                                    'Kontrak' => 'Kontrak'
                                ];
                                echo isset($status_labels[$pembimbing->status_kepegawaian]) ? $status_labels[$pembimbing->status_kepegawaian] : $pembimbing->status_kepegawaian;
                            } else {
                                echo '-';
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">Email</td>
                        <td>
                            <?php if(!empty($email_pembimbing)): ?>
                                <a href="mailto:<?php echo htmlspecialchars($email_pembimbing) ?>"><?php echo htmlspecialchars($email_pembimbing) ?></a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">Telepon</td>
                        <td>
                            <?php if(!empty($telepon_pembimbing)): ?>
                                <a href="tel:<?php echo htmlspecialchars($telepon_pembimbing) ?>"><?php echo htmlspecialchars($telepon_pembimbing) ?></a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>

                    <tr>
                        <td class="fw-bold bg-light">Jumlah Siswa Bimbingan</td>
                        <td><?php echo count($siswa_list); ?> Siswa</td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">Status Ketersediaan</td>
                        <td>
                            <?php 
                            $jumlah_siswa = count($siswa_list);
                            if($jumlah_siswa >= 20) {
                                echo '<span class="badge bg-success">Penuh</span>';
                            } elseif($jumlah_siswa > 0) {
                                echo '<span class="badge bg-warning">Tersedia</span>';
                            } else {
                                echo '<span class="badge bg-secondary">Kosong</span>';
                            }
                            ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
                
        <div class="mt-4">
        </div>
    </div>
</div>

<!-- Daftar Siswa Bimbingan -->
<div class="card mt-4">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Daftar Siswa Bimbingan</h5>
    </div>
    <div class="card-body">
        <?php if(!empty($siswa_list)): ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Jurusan</th>
                        <th>Alamat</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($siswa_list as $index => $siswa): ?>
                    <tr>
                        <td><?php echo $index + 1 ?></td>
                        <td><?php echo isset($siswa->siswa_nis) ? $siswa->siswa_nis : '-' ?></td>
                        <td><?php echo $siswa->siswa_nama ?></td>
                        <td><?php echo $siswa->siswa_kelas ?></td>
                        <td><?php echo isset($siswa->siswa_jurusan) ? $siswa->siswa_jurusan : '-' ?></td>
                        <td><?php echo isset($siswa->siswa_alamat) ? $siswa->siswa_alamat : '-' ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="text-center text-muted py-4">
            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
            Belum ada siswa yang dibimbing
        </div>
        <?php endif; ?>
    </div>
</div>

<?php endif; ?>