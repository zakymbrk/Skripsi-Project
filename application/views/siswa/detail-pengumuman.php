<?php 
$pengumuman_id = $this->uri->segment(4);
$this->db->where('pengumuman_id', $pengumuman_id);
$pengumuman = $this->db->get('tb_pengumuman')->row();

if(!$pengumuman){
    redirect('siswa/view/informasi');
}

// Get creator
$creator = null;
if($pengumuman->created_by){
    $this->db->where('id', $pengumuman->created_by);
    $creator = $this->db->get('tb_user')->row();
}
?>

<div class="card-mobile">
    <div class="d-flex align-items-start mb-3">
        <div class="flex-shrink-0">
            <div style="width: 50px; height: 50px; background-color: #563b82; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-info-circle text-white fa-lg"></i>
            </div>
        </div>
        <div class="flex-grow-1 ms-3">
            <h5 class="mb-1 fw-bold"><?php echo $pengumuman->judul ?></h5>
            <p class="small text-muted mb-0">
                <i class="fas fa-calendar me-1"></i>
                <?php echo date('l, d F Y', strtotime($pengumuman->created_at)); ?>
                <?php if($creator){ ?>
                    | <?php echo $creator->nama_lengkap; ?>
                <?php } ?>
            </p>
        </div>
    </div>
    
    <hr>
    
    <div class="mt-3" style="font-family: 'Segoe UI Emoji', 'Segoe UI', sans-serif; white-space: pre-wrap;">
        <?php 
        // Convert basic markdown-like formatting to HTML
        $content = htmlspecialchars($pengumuman->isi, ENT_QUOTES, 'UTF-8');
        $content = nl2br($content);
        // Convert **bold** to <strong>bold</strong>
        $content = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $content);
        // Convert *italic* to <em>italic</em>
        $content = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $content);
        // Convert _underline_ to <u>underline</u>
        $content = preg_replace('/_(.*?)_/', '<u>$1</u>', $content);
        echo $content;
        ?>
    </div>
    
    <div class="mt-4">
        <a href="<?php echo base_url('siswa/view/home') ?>" class="btn btn-outline-primary btn-mobile">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

