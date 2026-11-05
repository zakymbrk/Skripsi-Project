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
                        <td><?php echo $pembimbing->pembimbing_nama ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">Username Login</td>
                        <td><?php echo isset($pembimbing->username) && !empty($pembimbing->username) ? htmlspecialchars($pembimbing->username) : '-' ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">Password Login</td>
                        <td><code><?php echo isset($pembimbing->password) && !empty($pembimbing->password) ? htmlspecialchars($pembimbing->password) : '-' ?></code></td>
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
                        <td>
                            <?php if(isset($pembimbing->pembimbing_alamat) && !empty($pembimbing->pembimbing_alamat)): ?>
                                <?php echo nl2br(htmlspecialchars($pembimbing->pembimbing_alamat)) ?>
                            <?php else: ?>
                                <?php if(isset($pembimbing->alamat) && !empty($pembimbing->alamat)): ?>
                                    <?php echo nl2br(htmlspecialchars($pembimbing->alamat)) ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
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
                            <?php if(isset($pembimbing->pembimbing_email) && !empty($pembimbing->pembimbing_email)): ?>
                                <a href="mailto:<?php echo $pembimbing->pembimbing_email ?>"><?php echo $pembimbing->pembimbing_email ?></a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">Telepon</td>
                        <td>
                            <?php if(isset($pembimbing->pembimbing_telepon) && !empty($pembimbing->pembimbing_telepon)): ?>
                                <a href="tel:<?php echo $pembimbing->pembimbing_telepon ?>"><?php echo $pembimbing->pembimbing_telepon ?></a>
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
        
        <!-- Print Area for table content only -->
        <div id="printArea" class="d-none d-print-block">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td width="30%" class="fw-bold">Nama Pembimbing</td>
                            <td><?php echo $pembimbing->pembimbing_nama ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">NIP</td>
                            <td><?php echo isset($pembimbing->pembimbing_nip) && !empty($pembimbing->pembimbing_nip) ? $pembimbing->pembimbing_nip : '-' ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Email</td>
                            <td><?php echo isset($pembimbing->pembimbing_email) && !empty($pembimbing->pembimbing_email) ? $pembimbing->pembimbing_email : '-' ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Jenis Kelamin</td>
                            <td><?php echo isset($pembimbing->jenis_kelamin) && !empty($pembimbing->jenis_kelamin) ? $pembimbing->jenis_kelamin : '-' ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Tempat Lahir</td>
                            <td><?php echo isset($pembimbing->tempat_lahir) && !empty($pembimbing->tempat_lahir) ? $pembimbing->tempat_lahir : '-' ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Tanggal Lahir</td>
                            <td><?php echo isset($pembimbing->tanggal_lahir) && !empty($pembimbing->tanggal_lahir) ? date('d F Y', strtotime($pembimbing->tanggal_lahir)) : '-' ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Alamat</td>
                            <td><?php echo isset($pembimbing->pembimbing_alamat) && !empty($pembimbing->pembimbing_alamat) ? $pembimbing->pembimbing_alamat : (isset($pembimbing->alamat) && !empty($pembimbing->alamat) ? $pembimbing->alamat : '-') ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Nomor Telepon</td>
                            <td><?php echo isset($pembimbing->pembimbing_telepon) && !empty($pembimbing->pembimbing_telepon) ? $pembimbing->pembimbing_telepon : '-' ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Jumlah Siswa Bimbingan</td>
                            <td><?php echo count($siswa_list); ?> Siswa</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Status Ketersediaan</td>
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
                        <?php if(isset($pembimbing->created_at) && $pembimbing->created_at): ?>
                        <tr>
                            <td class="fw-bold">Tanggal Dibuat</td>
                            <td><?php echo date('d F Y H:i:s', strtotime($pembimbing->created_at)) ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if(isset($pembimbing->updated_at) && $pembimbing->updated_at): ?>
                        <tr>
                            <td class="fw-bold">Terakhir Diubah</td>
                            <td><?php echo date('d F Y H:i:s', strtotime($pembimbing->updated_at)) ?></td>
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
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($siswa_list as $index => $siswa): ?>
                    <tr>
                        <td><?php echo $index + 1 ?></td>
                        <td><?php echo $siswa->siswa_nama ?></td>
                        <td><?php echo $siswa->siswa_kelas ?></td>
                        <td>
                            <a href="<?php echo base_url('hubin/view/detail-siswa/'.$siswa->siswa_code) ?>" 
                               class="btn btn-sm btn-info" title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
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