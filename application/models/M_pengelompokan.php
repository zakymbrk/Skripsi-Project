<?php
defined('BASEPATH')OR exit('No direct script access allowed');

class M_pengelompokan extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    private $table = 'tb_pengelompokan'; 
    private $status = true; 

    function tambah_pengelompokan($data)
    {
        // Check if already exists in pengelompokan table
        $this->db->where('pembimbing_id', $data['pembimbing_id']);
        $this->db->where('siswa_id', $data['siswa_id']);
        $existing = $this->db->get($this->table)->row();
        
        if($existing){
            return false; // Already grouped
        }
        
        $insert = $this->db->insert($this->table, $data);
        if(!$insert){
            $this->status = false;
        }
        return $this->status;        
    }
    
    /**
     * Check if a student is already assigned to a specific pembimbing
     * @param int $siswa_id
     * @param int $pembimbing_id
     * @return bool
     */
    function is_siswa_assigned_to_pembimbing($siswa_id, $pembimbing_id)
    {
        // Explicitly query the correct table (tb_pengelompokan) which contains both siswa_id and pembimbing_id
        // The tb_pembimbing table does NOT have siswa_id column, only tb_pengelompokan does
        // This prevents the "Unknown column 'siswa_id' in 'where clause'" error
        $this->db->select('*');
        $this->db->from('tb_pengelompokan');
        $this->db->where('siswa_id', $siswa_id);
        $this->db->where('pembimbing_id', $pembimbing_id);
        $result = $this->db->get();
        return $result->num_rows() > 0;
    }
    
    /**
     * Alternative method to check if a student is assigned to a specific pembimbing
     * This ensures we're using the correct relationship table
     * @param int $siswa_id
     * @param int $pembimbing_id
     * @return bool
     */
    function check_student_pembimbing_relationship($siswa_id, $pembimbing_id)
    {
        // Explicitly use tb_pengelompokan table to check the relationship
        // This prevents errors that occur when mistakenly querying tb_pembimbing with siswa_id
        $this->db->select('*');
        $this->db->from('tb_pengelompokan');
        $this->db->where('siswa_id', $siswa_id);
        $this->db->where('pembimbing_id', $pembimbing_id);
        $result = $this->db->get();
        return $result->num_rows() > 0;
    }
    
    /**
     * Additional safety method to check student-pembimbing assignment
     * This is a more explicit version to prevent the specific error mentioned
     * @param int $siswa_id
     * @param int $pembimbing_id
     * @return bool
     */
    function validate_student_pembimbing_assignment($siswa_id, $pembimbing_id)
    {
        // Ensure we're ONLY querying the correct table that contains both siswa_id and pembimbing_id
        // tb_pengelompokan is the junction table containing both foreign keys
        // DO NOT query tb_pembimbing with siswa_id as it doesn't exist in that table
        $query = $this->db->query("SELECT * FROM tb_pengelompokan WHERE siswa_id = ? AND pembimbing_id = ?", array($siswa_id, $pembimbing_id));
        return $query->num_rows() > 0;
    }
    
    function assign_siswa_to_pembimbing($pembimbing_id, $siswa_ids)
    {
        $success = 0;
        $failed = 0;
        
        foreach($siswa_ids as $siswa_id){
            $data = array(
                'pembimbing_id' => $pembimbing_id,
                'siswa_id' => $siswa_id
            );
            
            if($this->tambah_pengelompokan($data)){
                $success++;
            } else {
                $failed++;
            }
        }
        
        return array('success' => $success, 'failed' => $failed);
    }

    function get_siswa_by_pembimbing($pembimbing_id)
    {
        $this->db->select('tb_siswa.*, tb_dudi.dudi_nama, tb_pengelompokan.created_at as assigned_at');
        $this->db->from('tb_pengelompokan');
        $this->db->join('tb_siswa', 'tb_siswa.siswa_id = tb_pengelompokan.siswa_id');
        $this->db->join('tb_dudi', 'tb_dudi.dudi_id = tb_siswa.dudi_id', 'left');
        $this->db->where('tb_pengelompokan.pembimbing_id', $pembimbing_id);
        $this->db->order_by('tb_siswa.siswa_nama', 'ASC');
        return $this->db->get();
    }

    function get_pembimbing_by_siswa($siswa_id)
    {
        $this->db->select('tb_pembimbing.pembimbing_nama,
                          tb_pembimbing.pembimbing_email,
                          tb_pembimbing.pembimbing_telepon,
                          tb_pembimbing.pembimbing_alamat,
                          tb_pembimbing.pembimbing_nip,
                          tb_pembimbing.pendidikan_terakhir,
                          tb_pembimbing.jabatan,
                          tb_pembimbing.jurusan_keahlian,
                          tb_pembimbing.tahun_masuk,
                          tb_pembimbing.status_kepegawaian,
                          tb_pembimbing.tempat_tugas');
        $this->db->from('tb_pengelompokan');
        $this->db->join('tb_pembimbing', 'tb_pembimbing.pembimbing_id = tb_pengelompokan.pembimbing_id');
        $this->db->where('tb_pengelompokan.siswa_id', $siswa_id);
        return $this->db->get()->row();
    }

    function remove_siswa_from_pembimbing($pembimbing_id, $siswa_id)
    {
        $this->db->where('pembimbing_id', $pembimbing_id);
        $this->db->where('siswa_id', $siswa_id);
        return $this->db->delete($this->table);
    }

    function get_pembimbing_with_available_slots()
    {
        // Get pembimbing with less than 20 students
        $this->db->select('tb_pembimbing.*, COUNT(tb_pengelompokan.siswa_id) as jumlah_siswa');
        $this->db->from('tb_pembimbing');
        $this->db->join('tb_pengelompokan', 'tb_pengelompokan.pembimbing_id = tb_pembimbing.pembimbing_id', 'left');
        $this->db->group_by('tb_pembimbing.pembimbing_id');
        $this->db->having('jumlah_siswa <', 20);
        $this->db->order_by('jumlah_siswa', 'ASC');
        return $this->db->get();
    }
}