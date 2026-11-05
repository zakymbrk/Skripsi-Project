<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_logbook extends CI_Model {

    private $table = 'tb_logbook';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Ambil logbook siswa pada tanggal tertentu
     */
    public function get_logbook_by_date($siswa_id, $tanggal)
    {
        $this->db->where('siswa_id', $siswa_id);
        $this->db->where('tanggal', $tanggal);
        return $this->db->get($this->table)->row();
    }

    /**
     * Simpan logbook kegiatan harian baru
     */
    public function simpan_logbook($data)
    {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Ambil riwayat logbook siswa
     */
    public function get_riwayat_logbook_siswa($siswa_id, $limit = 50, $offset = 0)
    {
        $this->db->where('siswa_id', $siswa_id);
        $this->db->order_by('tanggal', 'DESC');
        $this->db->limit($limit, $offset);
        return $this->db->get($this->table)->result();
    }

    /**
     * Hitung total logbook siswa
     */
    public function count_logbook_siswa($siswa_id)
    {
        $this->db->where('siswa_id', $siswa_id);
        return $this->db->count_all_results($this->table);
    }

    /**
     * Statistik logbook siswa (valid, tidak valid, menunggu)
     */
    public function get_statistik_logbook_siswa($siswa_id)
    {
        $stats = [
            'total'         => 0,
            'valid'         => 0,
            'tidak_valid'   => 0,
            'menunggu'      => 0
        ];

        $this->db->select('status_validasi, COUNT(*) as count');
        $this->db->where('siswa_id', $siswa_id);
        $this->db->group_by('status_validasi');
        $query = $this->db->get($this->table)->result();

        foreach ($query as $row) {
            $stats['total'] += $row->count;
            if (isset($stats[$row->status_validasi])) {
                $stats[$row->status_validasi] += $row->count;
            }
        }

        return $stats;
    }

    /**
     * Ambil logbook siswa bimbingan berdasarkan pembimbing_id
     */
    public function get_logbook_by_pembimbing($pembimbing_id, $tanggal = null, $siswa_id = null)
    {
        $this->db->select('l.*, s.siswa_nama, s.siswa_nis, s.siswa_kelas, s.siswa_jurusan, d.dudi_nama');
        $this->db->from($this->table . ' l');
        $this->db->join('tb_siswa s', 's.siswa_id = l.siswa_id');
        $this->db->join('tb_pengelompokan k', 'k.siswa_id = s.siswa_id');
        $this->db->join('tb_dudi d', 'd.dudi_id = s.dudi_id', 'left');
        $this->db->where('k.pembimbing_id', $pembimbing_id);

        if (!empty($tanggal)) {
            $this->db->where('l.tanggal', $tanggal);
        }
        if (!empty($siswa_id)) {
            $this->db->where('l.siswa_id', $siswa_id);
        }

        $this->db->order_by('l.tanggal', 'DESC');
        $this->db->order_by('l.created_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Ambil semua logbook global untuk Hubin / Admin
     */
    public function get_all_logbook_global($tanggal = null, $limit = 100, $offset = 0, $status_validasi = null, $siswa_id = null)
    {
        $this->db->select('l.*, s.siswa_nama, s.siswa_nis, s.siswa_kelas, s.siswa_jurusan, d.dudi_nama, pem.pembimbing_nama');
        $this->db->from($this->table . ' l');
        $this->db->join('tb_siswa s', 's.siswa_id = l.siswa_id');
        $this->db->join('tb_dudi d', 'd.dudi_id = s.dudi_id', 'left');
        $this->db->join('tb_pengelompokan k', 'k.siswa_id = s.siswa_id', 'left');
        $this->db->join('tb_pembimbing pem', 'pem.pembimbing_id = k.pembimbing_id', 'left');

        if (!empty($tanggal)) {
            $this->db->where('l.tanggal', $tanggal);
        }
        if (!empty($status_validasi)) {
            $this->db->where('l.status_validasi', $status_validasi);
        }
        if (!empty($siswa_id)) {
            $this->db->where('l.siswa_id', $siswa_id);
        }

        $this->db->order_by('l.tanggal', 'DESC');
        $this->db->order_by('l.created_at', 'DESC');
        $this->db->limit($limit, $offset);
        return $this->db->get()->result();
    }

    /**
     * Update catatan pembimbing pada logbook (dengan verifikasi kepemilikan)
     */
    public function update_catatan_pembimbing($logbook_id, $catatan, $pembimbing_id = null, $nilai_pembimbing = null, $status_validasi = 'valid', $alasan_penolakan = null)
    {
        if ($pembimbing_id !== null) {
            $this->db->select('l.logbook_id');
            $this->db->from($this->table . ' l');
            $this->db->join('tb_pengelompokan k', 'k.siswa_id = l.siswa_id');
            $this->db->where('l.logbook_id', $logbook_id);
            $this->db->where('k.pembimbing_id', $pembimbing_id);
            $owned = $this->db->get()->row();

            if (!$owned) {
                return false;
            }
        }

        $this->db->where('logbook_id', $logbook_id);
        return $this->db->update($this->table, [
            'catatan_pembimbing' => $catatan,
            'nilai_pembimbing'   => $nilai_pembimbing,
            'status_validasi'   => $status_validasi,
            'alasan_penolakan'  => $alasan_penolakan,
            'updated_at'         => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Ambil logbook berdasarkan ID dengan join data siswa
     */
    public function get_logbook_by_id($logbook_id)
    {
        $this->db->select('l.*, s.siswa_nama, s.siswa_nis, s.siswa_kelas');
        $this->db->from($this->table . ' l');
        $this->db->join('tb_siswa s', 's.siswa_id = l.siswa_id');
        $this->db->where('l.logbook_id', $logbook_id);
        return $this->db->get()->row();
    }
}
