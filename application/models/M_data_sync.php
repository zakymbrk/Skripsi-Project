<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_data_sync extends CI_Model {
    
    public function __construct()
    {
        parent::__construct();
        // Load required models
        $this->load->model('M_user');
        $this->load->model('M_siswa');
    }
    
    /**
     * Get complete student data with all related information
     * @param int $user_id
     * @return object|null
     */
    public function get_complete_student_data($user_id)
    {
        // Get user data
        $user = $this->M_user->get_user_by_id($user_id);
        if (!$user) {
            return null;
        }
        
        // Get student data
        $siswa = $this->M_siswa->get_siswa_with_biodata_by_user_id($user_id);
        
        // Combine data
        $complete_data = (object) array_merge(
            (array) $user,
            (array) $siswa
        );
        
        // Add additional computed fields
        $complete_data->full_name = $user->nama_lengkap;
        $complete_data->student_id = $siswa ? $siswa->siswa_id : null;
        $complete_data->nis = $siswa ? $siswa->siswa_nis : null;
        $complete_data->nisn = $siswa ? $siswa->siswa_nisn : null;
        $complete_data->class = $siswa ? $siswa->siswa_kelas : null;
        $complete_data->major = $siswa ? $siswa->siswa_jurusan : null;
        
        return $complete_data;
    }
    
    /**
     * Update student data across all related tables
     * @param int $user_id
     * @param array $data
     * @return bool
     */
    public function update_student_data($user_id, $data)
    {
        $this->db->trans_start();
        
        try {
            // Separate user and student data
            $user_data = array();
            $siswa_data = array();
            
            // Map fields to appropriate tables
            $user_fields = ['nama_lengkap', 'username', 'email', 'telepon', 'alamat', 
                           'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 
                           'foto_profil', 'updated_at'];
                           
            $siswa_fields = ['siswa_nama', 'siswa_nis', 'siswa_nisn', 'siswa_kelas', 
                            'siswa_jurusan', 'siswa_telepon', 'siswa_alamat'];
            
            // Separate the data
            foreach ($data as $key => $value) {
                if (in_array($key, $user_fields)) {
                    $user_data[$key] = $value;
                } elseif (in_array($key, $siswa_fields)) {
                    $siswa_data[$key] = $value;
                }
            }
            
            // Update user table if there's user data
            if (!empty($user_data)) {
                // Ensure updated_at is set
                if (!isset($user_data['updated_at'])) {
                    $user_data['updated_at'] = date('Y-m-d H:i:s');
                }
                
                $this->db->where('id', $user_id);
                $this->db->update('tb_user', $user_data);
            }
            
            // Update student table if there's student data
            if (!empty($siswa_data)) {
                // Check if student record exists
                $this->db->where('user_id', $user_id);
                $existing_siswa = $this->db->get('tb_siswa')->row();
                
                if ($existing_siswa) {
                    // Update existing record
                    $this->db->where('user_id', $user_id);
                    $this->db->update('tb_siswa', $siswa_data);
                } else {
                    // Create new student record
                    $siswa_data['user_id'] = $user_id;
                    $siswa_data['siswa_code'] = 'siswa_' . time() . '_' . rand(1000, 9999);
                    $this->db->insert('tb_siswa', $siswa_data);
                }
            }
            
            $this->db->trans_complete();
            
            if ($this->db->trans_status() === FALSE) {
                log_message('error', 'Failed to update student data for user_id: ' . $user_id);
                return false;
            }
            
            // Log the update
            log_message('info', 'Successfully updated student data for user_id: ' . $user_id);
            return true;
            
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Exception in update_student_data: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Create new student with synchronized data
     * @param array $user_data
     * @param array $siswa_data
     * @return bool|int User ID if successful, false if failed
     */
    public function create_student($user_data, $siswa_data)
    {
        $this->db->trans_start();
        
        try {
            // Create user first
            $user_data['created_at'] = date('Y-m-d H:i:s');
            $user_data['updated_at'] = date('Y-m-d H:i:s');
            $user_data['level'] = 2; // Student level
                $user_data['level'] = 3; // Student level
            $user_data['is_active'] = 1;
            
            $this->db->insert('tb_user', $user_data);
            $user_id = $this->db->insert_id();
            
            if (!$user_id) {
                throw new Exception('Failed to create user');
            }
            
            // Create student record
            $siswa_data['user_id'] = $user_id;
            $siswa_data['siswa_code'] = 'siswa_' . time() . '_' . rand(1000, 9999);
            $siswa_data['created_at'] = date('Y-m-d H:i:s');
            
            $this->db->insert('tb_siswa', $siswa_data);
            
            $this->db->trans_complete();
            
            if ($this->db->trans_status() === FALSE) {
                log_message('error', 'Failed to create student record');
                return false;
            }
            
            log_message('info', 'Successfully created student with user_id: ' . $user_id);
            return $user_id;
            
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Exception in create_student: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get synchronization status for a student
     * @param int $user_id
     * @return array
     */
    public function get_sync_status($user_id)
    {
        $status = array(
            'user_exists' => false,
            'siswa_exists' => false,
            'data_consistent' => true,
            'last_updated' => null,
            'issues' => array()
        );
        
        // Check user existence
        $user = $this->M_user->get_user_by_id($user_id);
        if ($user) {
            $status['user_exists'] = true;
            $status['last_updated'] = $user->updated_at;
        } else {
            $status['issues'][] = 'User record not found';
            $status['data_consistent'] = false;
        }
        
        // Check student existence
        $this->db->where('user_id', $user_id);
        $siswa = $this->db->get('tb_siswa')->row();
        if ($siswa) {
            $status['siswa_exists'] = true;
            // Check if last_updated is more recent
            if ($siswa->updated_at && 
                (!$status['last_updated'] || $siswa->updated_at > $status['last_updated'])) {
                $status['last_updated'] = $siswa->updated_at;
            }
        } else {
            $status['issues'][] = 'Student record not found';
            $status['data_consistent'] = false;
        }
        
        // Check data consistency
        if ($status['user_exists'] && $status['siswa_exists']) {
            // Check if names match
            if (isset($siswa->siswa_nama) && $siswa->siswa_nama != $user->nama_lengkap) {
                $status['issues'][] = 'Name mismatch between user and student records';
                $status['data_consistent'] = false;
            }
        }
        
        return $status;
    }
    
    /**
     * Synchronize data between user and student tables
     * @param int $user_id
     * @return bool
     */
    public function synchronize_data($user_id)
    {
        $this->db->trans_start();
        
        try {
            $user = $this->M_user->get_user_by_id($user_id);
            $this->db->where('user_id', $user_id);
            $siswa = $this->db->get('tb_siswa')->row();
            
            if (!$user) {
                throw new Exception('User not found');
            }
            
            // Sync user data to student table
            $sync_data = array(
                'siswa_nama' => $user->nama_lengkap,
                'updated_at' => date('Y-m-d H:i:s')
            );
            
            if ($siswa) {
                $this->db->where('user_id', $user_id);
                $this->db->update('tb_siswa', $sync_data);
            } else {
                $sync_data['user_id'] = $user_id;
                $sync_data['siswa_code'] = 'siswa_' . time() . '_' . rand(1000, 9999);
                $sync_data['created_at'] = date('Y-m-d H:i:s');
                $this->db->insert('tb_siswa', $sync_data);
            }
            
            $this->db->trans_complete();
            
            if ($this->db->trans_status() === FALSE) {
                log_message('error', 'Failed to synchronize data for user_id: ' . $user_id);
                return false;
            }
            
            log_message('info', 'Successfully synchronized data for user_id: ' . $user_id);
            return true;
            
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Exception in synchronize_data: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get recent changes for a student
     * @param int $user_id
     * @param int $limit
     * @return array
     */
    public function get_recent_changes($user_id, $limit = 10)
    {
        $changes = array();
        
        // Get user changes
        $this->db->select('id, nama_lengkap, username, email, nip_nim, updated_at');
        $this->db->where('id', $user_id);
        $user_changes = $this->db->get('tb_user')->result();
        
        foreach ($user_changes as $change) {
            $changes[] = array(
                'type' => 'user',
                'timestamp' => $change->updated_at,
                'data' => $change
            );
        }
        
        // Get student changes
        $this->db->select('siswa_id, siswa_nama, siswa_nis, siswa_kelas, updated_at');
        $this->db->where('user_id', $user_id);
        $siswa_changes = $this->db->get('tb_siswa')->result();
        
        foreach ($siswa_changes as $change) {
            $changes[] = array(
                'type' => 'student',
                'timestamp' => $change->updated_at,
                'data' => $change
            );
        }
        
        // Sort by timestamp
        usort($changes, function($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });
        
        return array_slice($changes, 0, $limit);
    }
}