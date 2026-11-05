<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Validation_log_model
 *
 * Model untuk menyimpan log hasil validasi presensi dan logbook.
 */
class Validation_log_model extends CI_Model {
    protected $table = 'validation_logs';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Insert satu record log validasi.
     *
     * @param array $data Associative array dengan kunci:
     *   - siswa_id
     *   - jenis_validasi (presensi|logbook)
     *   - rule
     *   - hasil (valid|invalid)
     *   - keterangan
     *   - created_at (optional, default now)
     * @return bool
     */
    public function insert($data)
    {
        if (!isset($data['created_at'])) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }
        return $this->db->insert($this->table, $data);
    }

    /**
     * Retrieve logs for a specific siswa.
     */
    public function get_by_siswa($siswa_id, $limit = 100, $offset = 0)
    {
        $this->db->where('siswa_id', $siswa_id);
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit, $offset);
        return $this->db->get($this->table)->result();
    }
}
?>
