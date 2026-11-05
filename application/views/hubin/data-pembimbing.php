<?php 
$search = $this->input->get('nama_pembimbing') ? $this->input->get('nama_pembimbing') : '';

// Count total rows with search
$total_rows = $this->M_pembimbing->count_pembimbing($search);

// Pagination
$config['base_url'] = base_url('hubin/view/data-pembimbing');
$config['total_rows'] = $total_rows;
$config['per_page'] = 10;
$config['uri_segment'] = 4;
$config['use_page_numbers'] = TRUE;
$config['page_query_string'] = TRUE;
$config['query_string_segment'] = 'page';
$config['reuse_query_string'] = TRUE;

$this->pagination->initialize($config);
$page = ($this->input->get('page')) ? $this->input->get('page') : 1;
$offset = ($page - 1) * $config['per_page'];

// Get data with pagination
$getpembimbing = $this->M_pembimbing->get_pembimbing_paginated($config['per_page'], $offset, $search);
?>

<!-- Action Buttons -->

<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo base_url('hubin/view') ?>"><i class="fas fa-home"></i> Home</a></li>
        <li class="breadcrumb-item active">Data Pembimbing</li>
    </ol>
</nav>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0"><i class="fas fa-user-tie me-2"></i>Data Pembimbing</h2>
        <a href="<?php echo base_url('hubin/view/tambah-pembimbing') ?>" class="btn btn-primary mt-2">
            <i class="fas fa-plus me-2"></i>Tambah Pembimbing
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

<?php if(!empty($this->session->flashdata('new_user_username'))): ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <h5 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Informasi Akun Pembimbing Baru</h5>
        <hr>
        <p class="mb-2"><strong>Akun pembimbing berhasil dibuat!</strong></p>
        <p class="mb-1"><strong>Nama:</strong> <?php echo $this->session->flashdata('new_user_nama'); ?></p>
        <p class="mb-1"><strong>Username:</strong> <code><?php echo $this->session->flashdata('new_user_username'); ?></code></p>
        <p class="mb-0"><strong>Password:</strong> <code><?php echo $this->session->flashdata('new_user_password'); ?></code></p>
        <hr>
        <p class="mb-0 small"><i class="fas fa-exclamation-triangle me-1"></i>Harap catat dan berikan informasi akun ini kepada guru pembimbing yang bersangkutan untuk login ke Portal Pembimbing.</p>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if(!empty($this->session->flashdata('error_message'))){ ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <?php echo $this->session->flashdata('error_message'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php } ?>

<!-- Card -->
<div class="card fade-in">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-user-tie me-2"></i>Daftar Pembimbing
        </h5>
        <div class="d-flex align-items-center flex-grow-1 mx-4">
            <form action="" method="get" class="d-flex w-100">
                <input type="search" value="<?php echo $search ?>" name="nama_pembimbing" 
                       placeholder="Cari nama pembimbing..." class="form-control me-2" required>
                <button class="btn btn-outline-primary" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
        <div class="d-flex align-items-center gap-2">
            <!-- Import Button -->
            <a href="<?php echo base_url('hubin/view/import-pembimbing') ?>" class="btn btn-info btn-sm text-white">
                <i class="fas fa-file-import me-1"></i> Import Pembimbing
            </a>
            <!-- Export Button -->
            <a href="<?php echo base_url('export_excel/export_data_pembimbing') ?>" class="btn btn-success btn-sm" id="export-all-btn">
                <i class="fas fa-file-excel"></i> Export Data
            </a>
            <a href="#" class="btn btn-success btn-sm" id="export-selected-btn" onclick="exportSelected(event)" style="display:none;">
                <i class="fas fa-file-excel"></i> Export Terpilih (<span id="export-count">0</span>)
            </a>
            <button id="delete-selected-btn" style="display:none;" onclick="deleteSelected()" class="btn btn-danger btn-sm" title="Hapus Pembimbing Terpilih">
                <i class="fas fa-trash"></i> Hapus <span id="delete-count">0</span> Terpilih
            </button>
            <span class="badge bg-primary">
                <i class="fas fa-user-tie me-1"></i>Total: <?php echo $getpembimbing->num_rows() ?> Pembimbing
            </span>
        </div>
    </div>
    <div class="card-body">

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">
                            <input type="checkbox" id="select-all" onchange="toggleSelectAll()">
                        </th>
                        <th>#</th>
                        <th>Nama Pembimbing</th>
                        <th>NIP</th>
                        <th>Tempat Tugas</th>
                        <th>Pendidikan</th>
                        <th>Jabatan</th>
                        <th>Jumlah Siswa</th>
                        <th>Telepon</th>
                        <th>Email</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    if($getpembimbing->num_rows() > 0):
                        foreach($getpembimbing->result() as $show):
                    ?>
                    <tr>
                        <td class="text-center">
                            <input type="checkbox" class="row-checkbox" value="<?php echo $show->pembimbing_id; ?>" data-pembimbing-code="<?php echo $show->pembimbing_code; ?>">
                        </td>
                        <td><?php echo $no++ ?></td>
                        <td><strong><?php echo $show->pembimbing_nama ?></strong></td>
                        <td><?php echo $show->pembimbing_nip ? $show->pembimbing_nip : '-' ?></td>
                        <td>
                            <?php if(isset($show->tempat_tugas) && !empty($show->tempat_tugas)): ?>
                                <small><?php echo $show->tempat_tugas ?></small>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if(isset($show->pendidikan_terakhir) && !empty($show->pendidikan_terakhir)): ?>
                                <small><?php echo $show->pendidikan_terakhir ?></small>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if(isset($show->jabatan) && !empty($show->jabatan)): ?>
                                <small><?php echo $show->jabatan ?></small>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php 
                            // Hitung jumlah siswa bimbingan
                            $this->db->where('pembimbing_id', $show->pembimbing_id);
                            $jumlah_siswa = $this->db->count_all_results('tb_pengelompokan');
                            if($jumlah_siswa > 0): 
                            ?>
                                <span class="badge bg-success"><?php echo $jumlah_siswa ?> siswa</span>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $show->pembimbing_telepon ? $show->pembimbing_telepon : '-' ?></td>
                        <td><?php echo $show->pembimbing_email ? $show->pembimbing_email : '-' ?></td>
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <a href="<?php echo base_url('hubin/detail_pembimbing_unified/'.$show->pembimbing_code) ?>" 
                                   class="btn btn-sm btn-secondary" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button type="button" 
                                        class="btn btn-sm btn-danger delete-single-btn" 
                                        onclick="deleteSingle('<?php echo $show->pembimbing_code; ?>')" 
                                        title="Hapus Data">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php 
                        endforeach;
                    else:
                    ?>
                    <tr>
                        <td colspan="12" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                            Tidak ada data pembimbing
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted">
                <?php if($config['total_rows'] > 0): ?>
                    Menampilkan <?php echo $offset + 1; ?> - <?php echo min($offset + $config['per_page'], $config['total_rows']); ?> dari <?php echo $config['total_rows']; ?> data
                <?php else: ?>
                    Menampilkan 0 dari 0 data
                <?php endif; ?>
            </div>
            <nav>
                <?php echo $this->pagination->create_links(); ?>
            </nav>
        </div>
    </div>
</div>

<script>
// Select all checkboxes
function toggleSelectAll() {
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.row-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    
    updateActionButton();
}

// Update button visibility and count
function updateActionButton() {
    const checkboxes = document.querySelectorAll('.row-checkbox:checked');
    const count = checkboxes.length;
    const deleteBtn = document.getElementById('delete-selected-btn');
    const deleteCount = document.getElementById('delete-count');
    const deleteButtons = document.querySelectorAll('.delete-single-btn');
    const exportAllBtn = document.getElementById('export-all-btn');
    const exportSelectedBtn = document.getElementById('export-selected-btn');
    const exportCount = document.getElementById('export-count');
    
    if (count > 0) {
        // Show batch delete button, hide individual delete buttons
        deleteBtn.style.display = 'block';
        deleteCount.textContent = count;
        
        // Hide export all, show export selected
        if(exportAllBtn) exportAllBtn.style.display = 'none';
        if(exportSelectedBtn) {
            exportSelectedBtn.style.display = 'inline-block';
            exportCount.textContent = count;
        }
        
        // Hide all individual delete buttons
        deleteButtons.forEach(btn => {
            btn.style.display = 'none';
        });
    } else {
        // Hide batch delete button, show individual delete buttons
        deleteBtn.style.display = 'none';
        
        // Show export all, hide export selected
        if(exportAllBtn) exportAllBtn.style.display = 'inline-block';
        if(exportSelectedBtn) exportSelectedBtn.style.display = 'none';
        
        // Show all individual delete buttons
        deleteButtons.forEach(btn => {
            btn.style.display = 'inline-block';
        });
    }
}

// Delete single pembimbing
function deleteSingle(pembimbingCode) {
    if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
        window.location.href = '<?php echo base_url("hubin/hapus_pembimbing/") ?>' + pembimbingCode;
    }
}

// Delete selected pembimbing
function deleteSelected() {
    const checkboxes = document.querySelectorAll('.row-checkbox:checked');
    if (checkboxes.length === 0) {
        alert('Pilih pembimbing yang akan dihapus!');
        return;
    }
    
    const pembimbingCodes = Array.from(checkboxes).map(cb => cb.getAttribute('data-pembimbing-code'));
    const count = pembimbingCodes.length;
    
    if (confirm('Apakah Anda yakin ingin menghapus ' + count + ' pembimbing terpilih?\nTindakan ini tidak dapat dibatalkan!')) {
        // Submit form to batch delete endpoint
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?php echo base_url("hubin/batch_delete_pembimbing") ?>';
        
        pembimbingCodes.forEach(code => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'selected_pembimbing[]';
            input.value = code;
            form.appendChild(input);
        });
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Export selected pembimbing
function exportSelected(event) {
    event.preventDefault();
    const checkboxes = document.querySelectorAll('.row-checkbox:checked');
    if (checkboxes.length === 0) {
        alert('Pilih pembimbing yang akan diekspor!');
        return;
    }
    
    const pembimbingIds = Array.from(checkboxes).map(cb => cb.value);
    const count = pembimbingIds.length;
    
    // Redirect to export with selected IDs
    window.location.href = '<?php echo base_url("export_excel/export_data_pembimbing") ?>?selected=' + encodeURIComponent(pembimbingIds.join(','));
}

// Add event listeners to row checkboxes
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateActionButton);
    });
    
    // Initialize button state on page load
    updateActionButton();
});
</script>