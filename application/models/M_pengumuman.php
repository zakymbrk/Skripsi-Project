<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_pengumuman extends CI_Model {

    private $table = 'tb_pengumuman';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Ambil daftar pengumuman terbaru
     */
    public function get_recent_pengumuman($limit = 5)
    {
        $this->db->where('is_active', 1);
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get($this->table)->result();
    }

    /**
     * Ambil semua pengumuman (untuk halaman list admin)
     */
    public function get_all_pengumuman()
    {
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get($this->table)->result();
    }

    /**
     * Ambil satu pengumuman by ID
     */
    public function get_pengumuman($pengumuman_id)
    {
        $this->db->where('pengumuman_id', $pengumuman_id);
        return $this->db->get($this->table)->row();
    }

    /**
     * Tambah pengumuman baru
     */
    public function tambah_pengumuman($data)
    {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update pengumuman
     */
    public function update_pengumuman($data, $pengumuman_id)
    {
        $this->db->where('pengumuman_id', $pengumuman_id);
        return $this->db->update($this->table, $data);
    }

    /**
     * Hapus pengumuman
     */
    public function delete_pengumuman($pengumuman_id)
    {
        $this->db->where('pengumuman_id', $pengumuman_id);
        return $this->db->delete($this->table);
    }
}
