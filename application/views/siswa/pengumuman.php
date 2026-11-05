<?php 
// Get all pengumuman
$this->db->order_by('created_at', 'DESC');
$pengumuman = $this->db->get('tb_pengumuman')->result();
?>

<div class="card-mobile">
    <h5 class="mb-3"><i class="fas fa-bullhorn me-2 text-primary"></i>Daftar Pengumuman</h5>
    
    <?php if($pengumuman): ?>
        <?php foreach($pengumuman as $p): ?>
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex align-items-start">
                    <div class="flex-shrink-0">
                        <div style="width: 50px; height: 50px; background-color: #563b82; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-info-circle text-white fa-lg"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1 fw-bold"><?php echo $p->judul ?></h6>
                        <p class="small text-muted mb-2">
                            <i class="fas fa-calendar me-1"></i>
                            <?php echo date('l, d F Y', strtotime($p->created_at)); ?> | 
                            <?php 
                            $this->db->where('id', $p->created_by);
                            $creator = $this->db->get('tb_user')->row();
                            echo $creator ? $creator->nama_lengkap : 'Admin';
                            ?>
                        </p>
                        <p class="small mb-2">
                            <?php echo strlen(strip_tags($p->isi)) > 150 ? substr(strip_tags($p->isi), 0, 150) . '...' : strip_tags($p->isi); ?>
                        </p>
                        <a href="<?php echo base_url('siswa/view/detail-pengumuman/'.$p->pengumuman_id) ?>" class="btn btn-sm btn-outline-primary">
                            Selengkapnya <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="card-mobile text-center" style="padding: 40px 20px;">
            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
            <p class="text-muted mb-0">Belum ada pengumuman</p>
        </div>
    <?php endif; ?>
</div>