<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo base_url('hubin/view') ?>"><i class="fas fa-home"></i> Home</a></li>
        <li class="breadcrumb-item"><a href="<?php echo base_url('hubin/view/data-dudi') ?>">Data DUDI</a></li>
        <li class="breadcrumb-item active">Detail DUDI</li>
    </ol>
</nav>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="fas fa-building me-2"></i>Detail DUDI</h2>
    <div>
        <a href="<?php echo base_url('hubin/edit_dudi/' . $dudi->dudi_code) ?>" class="btn btn-warning">
            <i class="fas fa-edit me-2"></i>Edit DUDI
        </a>

    </div>
</div>

<?php if(!empty($this->session->flashdata('message'))){ ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <?php echo $this->session->flashdata('message'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php } ?>

<?php if(!empty($this->session->flashdata('error_message'))){ ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <?php echo $this->session->flashdata('error_message'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php } ?>

<!-- Company Information Card -->
<div class="card shadow mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Perusahaan</h5>
        <?php if($dudi->sumber_data == 'siswa'): ?>
            <small class="text-white-50">Data diisi oleh siswa yang mengajukan perusahaan ini</small>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-borderless">
                <tbody>
                    <tr>
                        <td width="25%" class="fw-bold">Nama Perusahaan</td>
                        <td><?php echo htmlspecialchars($dudi->dudi_nama) ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Alamat</td>
                        <td>
                            <?php 
                            // Check if we have student-filled data
                            if(isset($dudi->student_filled_data) && !empty($dudi->student_filled_data['alamat'])):
                                echo htmlspecialchars($dudi->student_filled_data['alamat']);
                            elseif($dudi->dudi_alamat == 'Alamat belum diisi' && $dudi->is_mitra == 0): 
                                echo '<span class="text-warning">Belum diisi oleh siswa</span>';
                            else:
                                echo htmlspecialchars($dudi->dudi_alamat);
                            endif; 
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Telepon</td>
                        <td>
                            <?php 
                            // Check if we have student-filled data
                            if(isset($dudi->student_filled_data) && !empty($dudi->student_filled_data['telepon'])):
                                echo htmlspecialchars($dudi->student_filled_data['telepon']);
                            elseif(empty($dudi->dudi_telepon) && $dudi->is_mitra == 0): 
                                echo '<span class="text-warning">Belum diisi oleh siswa</span>';
                            else:
                                echo !empty($dudi->dudi_telepon) ? htmlspecialchars($dudi->dudi_telepon) : '-';
                            endif; 
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Email</td>
                        <td>
                            <?php 
                            // Check if we have student-filled data
                            if(isset($dudi->student_filled_data) && !empty($dudi->student_filled_data['email'])):
                                echo htmlspecialchars($dudi->student_filled_data['email']);
                            elseif(empty($dudi->dudi_email) && $dudi->is_mitra == 0): 
                                echo '<span class="text-warning">Belum diisi oleh siswa</span>';
                            else:
                                echo !empty($dudi->dudi_email) ? htmlspecialchars($dudi->dudi_email) : '-';
                            endif; 
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Penanggung Jawab (PIC)</td>
                        <td>
                            <?php 
                            // Check if we have student-filled data
                            if(isset($dudi->student_filled_data) && !empty($dudi->student_filled_data['pic'])):
                                echo htmlspecialchars($dudi->student_filled_data['pic']);
                            elseif(empty($dudi->dudi_pic) && $dudi->is_mitra == 0): 
                                echo '<span class="text-warning">Belum diisi oleh siswa</span>';
                            else:
                                echo !empty($dudi->dudi_pic) ? htmlspecialchars($dudi->dudi_pic) : '-';
                            endif; 
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold">NIP PIC</td>
                        <td>
                            <?php 
                            // Check if we have student-filled data
                            if(isset($dudi->student_filled_data) && !empty($dudi->student_filled_data['nip_pic'])):
                                echo htmlspecialchars($dudi->student_filled_data['nip_pic']);
                            elseif(empty($dudi->dudi_nip_pic) && $dudi->is_mitra == 0): 
                                echo '<span class="text-warning">Belum diisi oleh siswa</span>';
                            else:
                                echo !empty($dudi->dudi_nip_pic) ? htmlspecialchars($dudi->dudi_nip_pic) : '-';
                            endif; 
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Instruktur</td>
                        <td>
                            <?php 
                            // Check if we have student-filled data
                            if(isset($dudi->student_filled_data) && !empty($dudi->student_filled_data['instruktur'])):
                                echo htmlspecialchars($dudi->student_filled_data['instruktur']);
                            elseif(empty($dudi->dudi_instruktur) && $dudi->is_mitra == 0): 
                                echo '<span class="text-warning">Belum diisi oleh siswa</span>';
                            else:
                                echo !empty($dudi->dudi_instruktur) ? htmlspecialchars($dudi->dudi_instruktur) : '-';
                            endif; 
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold">NIP Instruktur</td>
                        <td>
                            <?php 
                            // Check if we have student-filled data
                            if(isset($dudi->student_filled_data) && !empty($dudi->student_filled_data['nip_instruktur'])):
                                echo htmlspecialchars($dudi->student_filled_data['nip_instruktur']);
                            elseif(empty($dudi->dudi_nip_instruktur) && $dudi->is_mitra == 0): 
                                echo '<span class="text-warning">Belum diisi oleh siswa</span>';
                            else:
                                echo !empty($dudi->dudi_nip_instruktur) ? htmlspecialchars($dudi->dudi_nip_instruktur) : '-';
                            endif; 
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <td class="fw-bold">Status Mitra</td>
                        <td>
                            <?php if($dudi->is_mitra == 1): ?>
                                <span class="badge bg-success">Mitra</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Non-Mitra</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr class="table-light">
                        <td class="fw-bold text-primary"><i class="fas fa-map-marker-alt me-2"></i>Koordinat Geolocation (GPS)</td>
                        <td>
                            <?php if(!empty($dudi->latitude) && !empty($dudi->longitude)): ?>
                                <span class="badge bg-primary me-2">Lat: <?php echo htmlspecialchars($dudi->latitude); ?></span>
                                <span class="badge bg-primary">Long: <?php echo htmlspecialchars($dudi->longitude); ?></span>
                                <a href="https://maps.google.com/?q=<?php echo $dudi->latitude; ?>,<?php echo $dudi->longitude; ?>" target="_blank" class="btn btn-xs btn-outline-primary py-0 px-2 ms-2 small">
                                    <i class="fas fa-external-link-alt me-1"></i> Buka Peta
                                </a>
                            <?php else: ?>
                                <span class="text-muted"><i class="fas fa-exclamation-triangle text-warning me-1"></i> Koordinat belum disetel</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr class="table-light">
                        <td class="fw-bold text-primary"><i class="fas fa-circle-notch me-2"></i>Radius Toleransi Presensi</td>
                        <td>
                            <span class="badge bg-info text-dark fw-bold">
                                <?php echo isset($dudi->radius) ? (int)$dudi->radius : 100; ?> Meter
                            </span>
                            <small class="text-muted ms-2">(Aturan Rule-Based Validation RBV-006)</small>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Status Kerjasama</td>
                        <td>
                            <?php 
                            $status_badges = [
                                'mitra' => ['class' => 'success', 'text' => 'Mitra'],
                                'non_mitra' => ['class' => 'secondary', 'text' => 'Non-Mitra'],
                                'pengajuan' => ['class' => 'warning', 'text' => 'Pengajuan']
                            ];
                            $status_info = $status_badges[$dudi->status_kerjasama] ?? ['class' => 'secondary', 'text' => 'Unknown'];
                            ?>
                            <span class="badge bg-<?php echo $status_info['class']; ?>">
                                <?php echo $status_info['text']; ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Sumber Data</td>
                        <td>
                            <span class="badge bg-<?php echo $dudi->sumber_data == 'siswa' ? 'primary' : 'info'; ?>">
                                <?php echo ucfirst($dudi->sumber_data); ?>
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Students Doing PKL Here Card -->
<div class="card shadow">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Siswa yang Melakukan PKL di Perusahaan Ini</h5>
    </div>
    <div class="card-body">
        <?php if(!empty($students)): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-success">
                        <tr>
                            <th>No</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Jurusan</th>
                            <th>Periode</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach($students as $student): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($student->siswa_nama); ?></strong>
                                <?php if($student->nama_lengkap && $student->nama_lengkap != $student->siswa_nama): ?>
                                    <br><small class="text-muted">(<?php echo htmlspecialchars($student->nama_lengkap); ?>)</small>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($student->siswa_kelas); ?></td>
                            <td><?php echo htmlspecialchars($student->siswa_jurusan); ?></td>
                            <td><?php echo !empty($student->periode) ? htmlspecialchars($student->periode) : '-'; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="alert alert-info mt-3">
                <i class="fas fa-info-circle me-2"></i>
                Menampilkan <?php echo count($students); ?> siswa yang saat ini sedang melakukan PKL di perusahaan ini.
            </div>
        <?php else: ?>
            <div class="text-center py-4">
                <i class="fas fa-user-graduate fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Tidak ada siswa yang melakukan PKL di perusahaan ini</h5>
                <p class="text-muted">Belum ada siswa yang ditugaskan untuk melakukan PKL di perusahaan ini.</p>
            </div>
        <?php endif; ?>
    </div>
</div>