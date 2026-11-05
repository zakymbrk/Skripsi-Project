<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_penilaian extends CI_Model {

    private $table = 'tb_penilaian';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Ambil penilaian siswa tertentu
     */
    public function get_penilaian_by_siswa($siswa_id)
    {
        $this->db->where('siswa_id', $siswa_id);
        return $this->db->get($this->table)->row();
    }

    /**
     * Ambil semua penilaian milik siswa bimbingan pembimbing tertentu
     */
    public function get_penilaian_by_pembimbing($pembimbing_id)
    {
        $this->db->select('p.*, s.siswa_nama, s.siswa_nis, s.siswa_kelas, d.dudi_nama');
        $this->db->from($this->table . ' p');
        $this->db->join('tb_siswa s', 's.siswa_id = p.siswa_id');
        $this->db->join('tb_dudi d', 'd.dudi_id = s.dudi_id', 'left');
        $this->db->join('tb_pengelompokan k', 'k.siswa_id = s.siswa_id');
        $this->db->where('k.pembimbing_id', $pembimbing_id);
        $this->db->order_by('s.siswa_nama', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Ambil semua penilaian untuk laporan global (Hubin)
     */
    public function get_all_penilaian()
    {
        $this->db->select('p.*, s.siswa_nama, s.siswa_nis, s.siswa_kelas, s.siswa_jurusan, d.dudi_nama, pem.pembimbing_nama');
        $this->db->from($this->table . ' p');
        $this->db->join('tb_siswa s', 's.siswa_id = p.siswa_id');
        $this->db->join('tb_dudi d', 'd.dudi_id = s.dudi_id', 'left');
        $this->db->join('tb_pengelompokan k', 'k.siswa_id = s.siswa_id', 'left');
        $this->db->join('tb_pembimbing pem', 'pem.pembimbing_id = k.pembimbing_id', 'left');
        $this->db->order_by('s.siswa_nama', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Upsert penilaian siswa
     */
    public function upsert_penilaian($data, $siswa_id)
    {
        $this->db->where('siswa_id', $siswa_id);
        $existing = $this->db->get($this->table)->row();

        if ($existing) {
            $this->db->where('siswa_id', $siswa_id);
            return $this->db->update($this->table, $data);
        } else {
            $data['siswa_id'] = $siswa_id;
            $data['created_at'] = date('Y-m-d H:i:s');
            return $this->db->insert($this->table, $data);
        }
    }

    /**
     * Statistik global penilaian untuk Hubin
     */
    public function get_statistik_global()
    {
        $stats = [
            'total_siswa'           => 0,
            'rata_rata_nilai_akhir' => 0,
            'siswa_nilai_baik'      => 0, // nilai_akhir >= 75
            'siswa_nilai_kurang'    => 0, // nilai_akhir < 75
        ];

        $result = $this->db->get($this->table)->result();
        if (empty($result)) {
            return $stats;
        }

        $stats['total_siswa'] = count($result);
        $total_nilai = 0;
        foreach ($result as $row) {
            $total_nilai += $row->nilai_akhir;
            if ($row->nilai_akhir >= 75) {
                $stats['siswa_nilai_baik']++;
            } else {
                $stats['siswa_nilai_kurang']++;
            }
        }
        $stats['rata_rata_nilai_akhir'] = round($total_nilai / $stats['total_siswa'], 2);

        return $stats;
    }
}
