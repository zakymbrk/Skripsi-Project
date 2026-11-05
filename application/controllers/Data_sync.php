<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Data_sync extends CI_Controller {
    
    public function __construct()
    {
        parent::__construct();
        require_role(array(1, 3));
        $this->load->model('M_data_sync');
        $this->load->model('M_user');
        $this->load->model('M_siswa');
        
        // Only allow AJAX requests
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }
    }

    private function ensure_user_access($user)
    {
        $session_user = get_session_userdata();
        if (!$user || !$session_user) {
            return false;
        }

        return (int) $session_user['level'] === 1 || (int) $session_user['id'] === (int) $user->id;
    }
    
    /**
     * Get complete student data for synchronization
     */
    public function get_student_data()
    {
        $user_code = $this->input->post('user_code');
        
        if (!$user_code) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'status' => 'error',
                    'message' => 'User code required'
                )));
            return;
        }
        
        // Get user ID from user_code
        $user = $this->M_user->get_data_user($user_code);
        if (!$user) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'status' => 'error',
                    'message' => 'User not found'
                )));
            return;
        }

        if (!$this->ensure_user_access($user)) {
            show_error('Akses ditolak.', 403);
        }
        
        $student_data = $this->M_data_sync->get_complete_student_data($user->id);
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array(
                'status' => 'success',
                'data' => $student_data
            )));
    }
    
    /**
     * Update student data with synchronization
     */
    public function update_student_data()
    {
        $user_code = $this->input->post('user_code');
        $data = $this->input->post('data');
        
        if (!$user_code || !$data) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'status' => 'error',
                    'message' => 'User code and data required'
                )));
            return;
        }
        
        // Get user ID from user_code
        $user = $this->M_user->get_data_user($user_code);
        if (!$user) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'status' => 'error',
                    'message' => 'User not found'
                )));
            return;
        }

        if (!$this->ensure_user_access($user)) {
            show_error('Akses ditolak.', 403);
        }
        
        $result = $this->M_data_sync->update_student_data($user->id, $data);
        
        if ($result) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'status' => 'success',
                    'message' => 'Data updated successfully'
                )));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'status' => 'error',
                    'message' => 'Failed to update data'
                )));
        }
    }
    
    /**
     * Get synchronization status
     */
    public function get_sync_status()
    {
        $user_code = $this->input->post('user_code');
        
        if (!$user_code) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'status' => 'error',
                    'message' => 'User code required'
                )));
            return;
        }
        
        // Get user ID from user_code
        $user = $this->M_user->get_data_user($user_code);
        if (!$user) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'status' => 'error',
                    'message' => 'User not found'
                )));
            return;
        }

        if (!$this->ensure_user_access($user)) {
            show_error('Akses ditolak.', 403);
        }
        
        $status = $this->M_data_sync->get_sync_status($user->id);
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array(
                'status' => 'success',
                'data' => $status
            )));
    }
    
    /**
     * Force data synchronization
     */
    public function synchronize_data()
    {
        $user_code = $this->input->post('user_code');
        
        if (!$user_code) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'status' => 'error',
                    'message' => 'User code required'
                )));
            return;
        }
        
        // Get user ID from user_code
        $user = $this->M_user->get_data_user($user_code);
        if (!$user) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'status' => 'error',
                    'message' => 'User not found'
                )));
            return;
        }

        if (!$this->ensure_user_access($user)) {
            show_error('Akses ditolak.', 403);
        }
        
        $result = $this->M_data_sync->synchronize_data($user->id);
        
        if ($result) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'status' => 'success',
                    'message' => 'Data synchronized successfully'
                )));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'status' => 'error',
                    'message' => 'Failed to synchronize data'
                )));
        }
    }
    
    /**
     * Get recent changes for a student
     */
    public function get_recent_changes()
    {
        $user_code = $this->input->post('user_code');
        $limit = $this->input->post('limit') ? (int)$this->input->post('limit') : 10;
        
        if (!$user_code) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'status' => 'error',
                    'message' => 'User code required'
                )));
            return;
        }
        
        // Get user ID from user_code
        $user = $this->M_user->get_data_user($user_code);
        if (!$user) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'status' => 'error',
                    'message' => 'User not found'
                )));
            return;
        }

        if (!$this->ensure_user_access($user)) {
            show_error('Akses ditolak.', 403);
        }
        
        $changes = $this->M_data_sync->get_recent_changes($user->id, $limit);
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array(
                'status' => 'success',
                'data' => $changes
            )));
    }
    
    /**
     * Real-time data update notification
     */
    public function notify_update()
    {
        $user_code = $this->input->post('user_code');
        $updated_fields = $this->input->post('updated_fields');
        $source_page = $this->input->post('source_page');
        
        if (!$user_code || !$updated_fields) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'status' => 'error',
                    'message' => 'User code and updated fields required'
                )));
            return;
        }
        
        // Store notification in session or database for real-time updates
        $notification = array(
            'user_code' => $user_code,
            'updated_fields' => $updated_fields,
            'source_page' => $source_page,
            'timestamp' => time()
        );
        
        // Store in session for immediate access
        $existing_notifications = $this->session->userdata('data_sync_notifications') ?: array();
        $existing_notifications[] = $notification;
        $this->session->set_userdata('data_sync_notifications', $existing_notifications);
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array(
                'status' => 'success',
                'message' => 'Update notification sent'
            )));
    }
    
    /**
     * Check for pending updates
     */
    public function check_updates()
    {
        $user_code = $this->input->post('user_code');
        
        if (!$user_code) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'status' => 'error',
                    'message' => 'User code required'
                )));
            return;
        }
        
        // Get notifications for this user
        $all_notifications = $this->session->userdata('data_sync_notifications') ?: array();
        $user_notifications = array();
        
        foreach ($all_notifications as $notification) {
            if ($notification['user_code'] == $user_code) {
                $user_notifications[] = $notification;
            }
        }
        
        // Clear notifications for this user
        $remaining_notifications = array_filter($all_notifications, function($notification) use ($user_code) {
            return $notification['user_code'] != $user_code;
        });
        
        $this->session->set_userdata('data_sync_notifications', $remaining_notifications);
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array(
                'status' => 'success',
                'updates' => $user_notifications
            )));
    }
}