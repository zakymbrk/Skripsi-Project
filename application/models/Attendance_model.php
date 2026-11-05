<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Attendance_model extends CI_Model {
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Record attendance for a student.
     * Returns the inserted attendance ID or false on failure.
     */
    public function record_attendance($siswa_id, $lat, $lng, $status = 'present')
    {
        $data = [
            'siswa_id'   => $siswa_id,
            'date'       => date('Y-m-d'),
            'time'       => date('H:i:s'),
            'latitude'   => $lat,
            'longitude'  => $lng,
            'status'     => $status,
            'logbook_valid' => 0,
            'points_deducted' => 0
        ];
        $this->db->insert('tb_attendance', $data);
        return $this->db->insert_id();
    }

    /**
     * Update attendance record with logbook validation result.
     */
    public function update_logbook_status($attendance_id, $is_valid)
    {
        $data = [
            'logbook_valid' => $is_valid ? 1 : 0,
            'points_deducted' => $is_valid ? 0 : 5
        ];
        $this->db->where('attendance_id', $attendance_id);
        return $this->db->update('tb_attendance', $data);
    }

    /**
     * Retrieve today's attendance for all students (optionally filter by class).
     */
    public function get_today_attendance($class = null)
    {
        $this->db->select('a.*, s.siswa_nama, s.siswa_kelas');
        $this->db->from('tb_attendance a');
        $this->db->join('tb_siswa s', 's.siswa_id = a.siswa_id', 'left');
        $this->db->where('a.date', date('Y-m-d'));
        if ($class !== null) {
            $this->db->where('s.siswa_kelas', $class);
        }
        return $this->db->get()->result();
    }
}
?>
