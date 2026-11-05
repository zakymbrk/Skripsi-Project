<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_presensi extends CI_Model {

    private $table = 'tb_presensi';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Ambil data presensi hari ini untuk siswa tertentu
     */
    public function get_presensi_hari_ini($siswa_id, $tanggal = null)
    {
        if ($tanggal === null) {
            $tanggal = date('Y-m-d');
        }
        $this->db->where('siswa_id', $siswa_id);
        $this->db->where('tanggal', $tanggal);
        return $this->db->get($this->table)->row();
    }

    /**
     * Simpan presensi masuk baru
     */
    public function simpan_presensi_masuk($data)
    {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update presensi pulang pada record tanggal yang sama
     */
    public function update_presensi_pulang($presensi_id, $data)
    {
        $this->db->where('presensi_id', $presensi_id);
        $this->db->where('jam_pulang IS NULL', null, false);
        return $this->db->update($this->table, $data);
    }

    /**
     * Ambil riwayat presensi siswa dengan pagination atau limit
     */
    public function get_riwayat_presensi_siswa($siswa_id, $limit = 50, $offset = 0)
    {
        $this->db->where('siswa_id', $siswa_id);
        $this->db->order_by('tanggal', 'DESC');
        $this->db->limit($limit, $offset);
        return $this->db->get($this->table)->result();
    }

    /**
     * Hitung total hari presensi siswa
     */
    public function count_presensi_siswa($siswa_id)
    {
        $this->db->where('siswa_id', $siswa_id);
        return $this->db->count_all_results($this->table);
    }

    /**
     * Statistik kehadiran siswa (total hadir, izin, sakit, alpha)
     */
    public function get_statistik_presensi_siswa($siswa_id)
    {
        $stats = [
            'total' => 0,
            'hadir' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alpha' => 0,
            'valid' => 0
        ];

        $this->db->select('status_kehadiran, status_validasi, COUNT(*) as count');
        $this->db->where('siswa_id', $siswa_id);
        $this->db->group_by(['status_kehadiran', 'status_validasi']);
        $query = $this->db->get($this->table)->result();

        foreach ($query as $row) {
            $stats['total'] += $row->count;
            if ($row->status_validasi === 'valid') {
                $stats['valid'] += $row->count;
                if (isset($stats[$row->status_kehadiran])) {
                    $stats[$row->status_kehadiran] += $row->count;
                }
            }
        }

        return $stats;
    }

    /**
     * Ambil presensi siswa berdasarkan pembimbing_id
     */
    public function get_presensi_by_pembimbing($pembimbing_id, $tanggal = null, $siswa_id = null)
    {
        $this->db->select('p.*, s.siswa_nama, s.siswa_nis, s.siswa_kelas, s.siswa_jurusan, d.dudi_nama');
        $this->db->from('tb_presensi p');
        $this->db->join('tb_siswa s', 's.siswa_id = p.siswa_id');
        $this->db->join('tb_pengelompokan k', 'k.siswa_id = s.siswa_id');
        $this->db->join('tb_dudi d', 'd.dudi_id = s.dudi_id', 'left');
        $this->db->where('k.pembimbing_id', $pembimbing_id);

        if (!empty($tanggal)) {
            $this->db->where('p.tanggal', $tanggal);
        }
        if (!empty($siswa_id)) {
            $this->db->where('p.siswa_id', $siswa_id);
        }

        $this->db->order_by('p.tanggal', 'DESC');
        $this->db->order_by('p.jam_masuk', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Ambil semua presensi global untuk Hubin / Admin
     */
    public function get_all_presensi_global($tanggal = null, $status_kehadiran = null, $limit = 100, $offset = 0, $siswa_id = null)
    {
        $this->db->select('p.*, s.siswa_nama, s.siswa_nis, s.siswa_kelas, s.siswa_jurusan, d.dudi_nama, pem.pembimbing_nama');
        $this->db->from('tb_presensi p');
        $this->db->join('tb_siswa s', 's.siswa_id = p.siswa_id');
        $this->db->join('tb_dudi d', 'd.dudi_id = s.dudi_id', 'left');
        $this->db->join('tb_pengelompokan k', 'k.siswa_id = s.siswa_id', 'left');
        $this->db->join('tb_pembimbing pem', 'pem.pembimbing_id = k.pembimbing_id', 'left');

        if (!empty($tanggal)) {
            $this->db->where('p.tanggal', $tanggal);
        }
        if (!empty($status_kehadiran)) {
            $this->db->where('p.status_kehadiran', $status_kehadiran);
        }
        if (!empty($siswa_id)) {
            $this->db->where('p.siswa_id', $siswa_id);
        }

        $this->db->order_by('p.tanggal', 'DESC');
        $this->db->order_by('p.jam_masuk', 'DESC');
        $this->db->limit($limit, $offset);
        return $this->db->get()->result();
    }
}
