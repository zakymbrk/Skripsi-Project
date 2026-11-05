<?php
defined('BASEPATH')OR exit('No direct script access allowed');

class M_siswa extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        //Do your magic here
    }

    public function normalize_status_pengajuan($status = null)
    {
        $allowed_status = array('draft', 'menunggu', 'disetujui', 'ditolak', 'selesai');
        $normalized = is_string($status) ? trim(strtolower($status)) : '';

        if ($normalized === '') {
            return 'draft';
        }

        return in_array($normalized, $allowed_status, true) ? $normalized : 'draft';
    }

    private $table = 'tb_siswa'; 
    private $status= true; 

    function generateRandomString($length = 20) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    function normalize_pkl_schedule_data($data)
    {
        if (empty($data) || (isset($data['status_pengajuan']) && $data['status_pengajuan'] !== 'disetujui')) {
            return $data;
        }

        $tanggal_mulai = !empty($data['tanggal_mulai']) ? trim((string) $data['tanggal_mulai']) : date('Y-m-d');
        $start_dt = DateTime::createFromFormat('Y-m-d', $tanggal_mulai);
        if (!$start_dt || $start_dt->format('Y-m-d') !== $tanggal_mulai) {
            $tanggal_mulai = date('Y-m-d');
            $start_dt = new DateTime($tanggal_mulai);
        }

        $tanggal_selesai = !empty($data['tanggal_selesai']) ? trim((string) $data['tanggal_selesai']) : date('Y-m-d', strtotime($tanggal_mulai . ' +3 months -1 day'));
        $end_dt = DateTime::createFromFormat('Y-m-d', $tanggal_selesai);
        if (!$end_dt || $end_dt < $start_dt) {
            $tanggal_selesai = date('Y-m-d', strtotime($tanggal_mulai . ' +3 months -1 day'));
            $end_dt = new DateTime($tanggal_selesai);
        }

        $diff_days = $start_dt->diff($end_dt)->days;
        if ($diff_days < 60 || $diff_days > 150) {
            $tanggal_selesai = date('Y-m-d', strtotime($tanggal_mulai . ' +3 months -1 day'));
        }

        $data['tanggal_mulai'] = $tanggal_mulai;
        $data['tanggal_selesai'] = $tanggal_selesai;
        $data['lama_pelaksanaan'] = 90;
        $data['periode'] = date('Y', strtotime($tanggal_mulai)) . '/' . (date('Y', strtotime($tanggal_mulai)) + 1);

        return $data;
    }

    function normalize_all_approved_pkl_schedule()
    {
        $this->db->where('status_pengajuan', 'disetujui');
        $students = $this->db->get($this->table)->result();

        $updated = 0;
        foreach ($students as $student) {
            $normalized = $this->normalize_pkl_schedule_data((array) $student);
            if (empty($normalized['tanggal_mulai']) || empty($normalized['tanggal_selesai'])) {
                continue;
            }

            $changed = ($student->tanggal_mulai !== $normalized['tanggal_mulai']) ||
                ($student->tanggal_selesai !== $normalized['tanggal_selesai']) ||
                ((isset($student->lama_pelaksanaan) ? (int) $student->lama_pelaksanaan : 0) !== 90) ||
                ((isset($student->periode) ? $student->periode : '') !== $normalized['periode']);

            if ($changed) {
                $this->db->where('siswa_id', $student->siswa_id);
                $this->db->update($this->table, [
                    'tanggal_mulai' => $normalized['tanggal_mulai'],
                    'tanggal_selesai' => $normalized['tanggal_selesai'],
                    'lama_pelaksanaan' => 90,
                    'periode' => $normalized['periode']
                ]);
                $updated++;
            }
        }

        return $updated;
    }

    function get_semua_siswa()
    {
        $siswa = $this->db->get($this->table);
        return $siswa;
    }

    function tambah_data_siswa($data)
    {
        $data = $this->normalize_pkl_schedule_data($data);

        // Ensure required fields are set with defaults if not provided
        if(!isset($data['siswa_nis'])) {
            $data['siswa_nis'] = NULL;
        }
        
        // Check NIS uniqueness before insert
        if(isset($data['siswa_nis']) && !empty($data['siswa_nis'])) {
            $this->db->where('siswa_nis', $data['siswa_nis']);
            $existing_nis = $this->db->get('tb_siswa')->row();
            
            if($existing_nis) {
                log_message('error', 'Duplicate NIS found during insert: ' . $data['siswa_nis']);
                $this->status = false;
                return $this->status;
            }
        }
        
        // Check NISN uniqueness before insert
        if(isset($data['siswa_nisn']) && !empty($data['siswa_nisn'])) {
            $this->db->where('siswa_nisn', $data['siswa_nisn']);
            $existing_nisn = $this->db->get('tb_siswa')->row();
            
            if($existing_nisn) {
                log_message('error', 'Duplicate NISN found during insert: ' . $data['siswa_nisn']);
                $this->status = false;
                return $this->status;
            }
        }
        
        /*===========================
        =    INSERT KE DATABASE     =
        ===========================*/
        $insert = $this->db->insert($this->table, $data);
        if(!$insert){
            $this->status = false;
        }
        return $this->status;  
    }

    //search siswa
    function get_data_siswa($siswa_code)
    {
        $result = array();

        $this->db->where('siswa_code', $siswa_code);
        $ceksiswa = $this->db->get($this->table);
        if($ceksiswa->num_rows() != 0){
            $result = $ceksiswa->row();
        }

        return $result;
    }

    function update_siswa($data, $siswa_code)
    {
        $data = $this->normalize_pkl_schedule_data($data);

        // Filter out fields that don't exist in the database
        $allowed_fields = $this->get_allowed_siswa_fields();
        $filtered_data = array();
        
        foreach($data as $field => $value) {
            if(in_array($field, $allowed_fields)) {
                $filtered_data[$field] = $value;
            }
        }
        
        if(empty($filtered_data)) {
            return false; // No valid fields to update
        }
        
        $this->db->where('siswa_code', $siswa_code);
        $result = $this->db->update('tb_siswa', $filtered_data);
        
        return $result;
    }

    function hapus_siswa($siswa_code)
    {
        $this->db->where('siswa_code', $siswa_code);
        $student = $this->db->get($this->table)->row();

        if (!$student) {
            return false;
        }

        $siswa_id = (int) $student->siswa_id;
        $user_id = isset($student->user_id) ? (int) $student->user_id : null;

        $this->db->trans_start();

        $this->db->where('siswa_id', $siswa_id);
        $this->db->delete('tb_pengelompokan');

        $this->db->where('siswa_id', $siswa_id);
        $this->db->delete('tb_presensi');

        $this->db->where('siswa_id', $siswa_id);
        $this->db->delete('tb_logbook');

        $this->db->where('siswa_id', $siswa_id);
        $this->db->delete('tb_penilaian');

        $this->db->where('siswa_id', $siswa_id);
        $this->db->update('tb_siswa', ['pembimbing_id' => null]);

        $this->db->where('siswa_code', $siswa_code);
        $this->db->delete($this->table);

        if ($user_id) {
            $this->db->where('id', $user_id);
            $this->db->delete('tb_user');
        }

        $this->db->trans_complete();

        return $this->db->trans_status();
    }
    
    // New function to get siswa with full biodata
    function get_siswa_with_biodata($siswa_code)
    {
        $this->db->select('
            tb_siswa.*, 
            tb_user.user_code,
            tb_user.nama_lengkap as user_nama, 
            tb_user.username, 
            tb_user.foto_profil, 
            tb_user.alamat as user_alamat, 
            tb_user.telepon as user_telepon, 
            tb_user.email as user_email, 
            tb_user.tempat_lahir as user_tempat_lahir,
            tb_user.tanggal_lahir as user_tanggal_lahir,
            tb_user.jenis_kelamin as user_jenis_kelamin,
            tb_user.nip_nim as user_nip_nim,
            tb_user.is_active as user_is_active,
            tb_dudi.dudi_nama, 
            tb_dudi.is_mitra as dudi_is_mitra,
            COALESCE(pembimbing_kelompok.pembimbing_nama, tb_pembimbing.pembimbing_nama) as pembimbing_nama,
            COALESCE(pembimbing_kelompok.pembimbing_email, tb_pembimbing.pembimbing_email) as pembimbing_email,
            COALESCE(pembimbing_kelompok.pembimbing_telepon, tb_pembimbing.pembimbing_telepon) as pembimbing_telepon
        ');
        $this->db->from('tb_siswa');
        $this->db->join('tb_user', 'tb_user.id = tb_siswa.user_id', 'left');
        $this->db->join('tb_dudi', 'tb_dudi.dudi_id = tb_siswa.dudi_id', 'left');
        $this->db->join('tb_pengelompokan', 'tb_pengelompokan.siswa_id = tb_siswa.siswa_id', 'left');
        $this->db->join('tb_pembimbing as pembimbing_kelompok', 'pembimbing_kelompok.pembimbing_id = tb_pengelompokan.pembimbing_id', 'left');
        $this->db->join('tb_pembimbing', 'tb_pembimbing.pembimbing_id = tb_siswa.pembimbing_id', 'left');
        $this->db->where('tb_siswa.siswa_code', $siswa_code);
        return $this->db->get()->row();
    }
    
    // New function to get siswa with biodata by user_id
    function get_siswa_with_biodata_by_user_id($user_id)
    {
        $this->db->select('
            tb_siswa.*, 
            tb_user.id as user_id,
            tb_user.user_code, 
            tb_user.nama_lengkap as user_nama, 
            tb_user.username, 
            tb_user.foto_profil, 
            tb_user.alamat as user_alamat, 
            tb_user.telepon as user_telepon, 
            tb_user.email as user_email, 
            tb_user.tempat_lahir as user_tempat_lahir, 
            tb_user.tanggal_lahir as user_tanggal_lahir, 
            tb_user.jenis_kelamin as user_jenis_kelamin, 
            tb_user.nip_nim as user_nip_nim, 
            tb_user.is_active as user_is_active,
            tb_dudi.dudi_nama, 
            tb_dudi.is_mitra as dudi_is_mitra,
            COALESCE(pembimbing_kelompok.pembimbing_nama, tb_pembimbing.pembimbing_nama) as pembimbing_nama,
            COALESCE(pembimbing_kelompok.pembimbing_email, tb_pembimbing.pembimbing_email) as pembimbing_email,
            COALESCE(pembimbing_kelompok.pembimbing_telepon, tb_pembimbing.pembimbing_telepon) as pembimbing_telepon
        ');
        $this->db->from('tb_user');
        $this->db->join('tb_siswa', 'tb_siswa.user_id = tb_user.id', 'left');
        $this->db->join('tb_dudi', 'tb_dudi.dudi_id = tb_siswa.dudi_id', 'left');
        $this->db->join('tb_pengelompokan', 'tb_pengelompokan.siswa_id = tb_siswa.siswa_id', 'left');
        $this->db->join('tb_pembimbing as pembimbing_kelompok', 'pembimbing_kelompok.pembimbing_id = tb_pengelompokan.pembimbing_id', 'left');
        $this->db->join('tb_pembimbing', 'tb_pembimbing.pembimbing_id = tb_siswa.pembimbing_id', 'left');
        $this->db->where('tb_user.id', $user_id);
        return $this->db->get()->row();
    }
    
    // New function to update siswa by user_id
    function update_siswa_by_user_id($data, $user_id)
    {
        // Filter out fields that don't exist in the database
        $allowed_fields = $this->get_allowed_siswa_fields();
        $filtered_data = array();
        
        foreach($data as $field => $value) {
            if(in_array($field, $allowed_fields)) {
                $filtered_data[$field] = $value;
            }
        }
        
        if(empty($filtered_data)) {
            return false; // No valid fields to update
        }
        
        // Check NIS uniqueness before update (exclude current record)
        if(isset($filtered_data['siswa_nis']) && !empty($filtered_data['siswa_nis'])) {
            $this->db->where('siswa_nis', $filtered_data['siswa_nis']);
            $this->db->where('user_id !=', $user_id);
            $existing_nis = $this->db->get('tb_siswa')->row();
            
            if($existing_nis) {
                log_message('error', 'Duplicate NIS found: ' . $filtered_data['siswa_nis'] . ' already exists for user_id: ' . $existing_nis->user_id);
                return false; // Prevent duplicate NIS
            }
        }
        
        // Check NISN uniqueness before update (exclude current record)
        if(isset($filtered_data['siswa_nisn']) && !empty($filtered_data['siswa_nisn'])) {
            $this->db->where('siswa_nisn', $filtered_data['siswa_nisn']);
            $this->db->where('user_id !=', $user_id);
            $existing_nisn = $this->db->get('tb_siswa')->row();
            
            if($existing_nisn) {
                log_message('error', 'Duplicate NISN found: ' . $filtered_data['siswa_nisn'] . ' already exists for user_id: ' . $existing_nisn->user_id);
                return false; // Prevent duplicate NISN
            }
        }
        
        $this->db->where('user_id', $user_id);
        $result = $this->db->update('tb_siswa', $filtered_data);
        
        // Log the query for debugging
        log_message('debug', 'Siswa update query: ' . $this->db->last_query());
        log_message('debug', 'Siswa update affected rows: ' . $this->db->affected_rows());
        
        return $result;
    }
    
    // Helper function to get allowed fields in tb_siswa table
    private function get_allowed_siswa_fields()
    {
        $fields = $this->db->field_data('tb_siswa');
        $allowed_fields = array();
        foreach($fields as $field) {
            $allowed_fields[] = $field->name;
        }
        return $allowed_fields;
    }
    
    // Function to get pengajuan count for today
    function get_pengajuan_hari_ini_count()
    {
        $this->db->select('COUNT(*) as count');
        $this->db->from('tb_siswa');
        $this->db->where('DATE(created_at)', date('Y-m-d'));
        $query = $this->db->get();
        return $query->row()->count ? $query->row()->count : 0;
    }
    
    // Function to get students without pembimbing assigned
    function get_siswa_belum_assign_count()
    {
        $this->db->select('COUNT(*) as count');
        $this->db->from('tb_siswa');
        $this->db->where('pembimbing_id IS NULL');
        $this->db->where('status_pengajuan', 'disetujui'); // Only approved students without pembimbing
        $query = $this->db->get();
        return $query->row()->count ? $query->row()->count : 0;
    }
}