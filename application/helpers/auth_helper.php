<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Authorization helper — server-side role & ownership checks
 */

if (!function_exists('get_session_userdata')) {
    function get_session_userdata()
    {
        $CI =& get_instance();
        return $CI->session->userdata('userdata');
    }
}

if (!function_exists('require_login')) {
    function require_login()
    {
        $userdata = get_session_userdata();
        if (!$userdata) {
            redirect('auth/');
            exit;
        }
        return $userdata;
    }
}

if (!function_exists('require_role')) {
    /**
     * @param int|array $allowed_levels
     * @return array session userdata
     */
    function require_role($allowed_levels)
    {
        $userdata = require_login();
        $levels = is_array($allowed_levels) ? $allowed_levels : array($allowed_levels);
        if (!in_array((int)$userdata['level'], $levels, true)) {
            redirect('auth/');
            exit;
        }
        return $userdata;
    }
}

if (!function_exists('require_pembimbing_siswa')) {
    /**
     * Verify siswa_id belongs to pembimbing_id (prevents IDOR)
     *
     * @param int $siswa_id
     * @param int $pembimbing_id
     * @return bool
     */
    function require_pembimbing_siswa($siswa_id, $pembimbing_id)
    {
        if (empty($siswa_id) || empty($pembimbing_id)) {
            return false;
        }
        $CI =& get_instance();
        $CI->load->model('M_pengelompokan');
        return $CI->M_pengelompokan->is_siswa_assigned_to_pembimbing($siswa_id, $pembimbing_id);
    }
}

if (!function_exists('deny_pembimbing_siswa_access')) {
    /**
     * Block request if siswa is not under pembimbing
     */
    function deny_pembimbing_siswa_access($siswa_id, $pembimbing_id, $redirect_url = 'guru/view/home')
    {
        if (empty($siswa_id)) {
            return;
        }
        if (!require_pembimbing_siswa($siswa_id, $pembimbing_id)) {
            $CI =& get_instance();
            $CI->session->set_flashdata('error_message', 'Akses ditolak. Anda tidak memiliki wewenang atas data siswa ini.');
            redirect($redirect_url);
            exit;
        }
    }
}

if (!function_exists('get_siswa_by_user_id')) {
    function get_siswa_by_user_id($user_id)
    {
        $CI =& get_instance();
        $CI->db->where('user_id', $user_id);
        return $CI->db->get('tb_siswa')->row();
    }
}

if (!function_exists('require_siswa_owner')) {
    /**
     * Verify logged-in siswa owns the record
     */
    function require_siswa_owner($siswa_id)
    {
        $userdata = require_role(3);
        $siswa = get_siswa_by_user_id($userdata['id']);
        if (!$siswa || (int)$siswa->siswa_id !== (int)$siswa_id) {
            $CI =& get_instance();
            $CI->session->set_flashdata('error_message', 'Akses ditolak.');
            redirect('siswa/view/home');
            exit;
        }
        return $siswa;
    }
}
