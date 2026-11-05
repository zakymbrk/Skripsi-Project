<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_demo_users extends CI_Migration {
    public function up()
    {
        // Insert demo Hubin (level 1)
        $this->db->replace('tb_user', [
            'user_code' => 'HUB001',
            'username' => 'hubin_demo',
            'password' => password_hash('hubin123', PASSWORD_BCRYPT),
            'nama_lengkap' => 'Demo Hubin',
            'level' => 1,
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        // Insert demo Pembimbing (level 2)
        $this->db->replace('tb_user', [
            'user_code' => 'PEM001',
            'username' => 'pembimbing_demo',
            'password' => password_hash('pembimbing123', PASSWORD_BCRYPT),
            'nama_lengkap' => 'Demo Pembimbing',
            'level' => 2,
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        // Insert demo Siswa (level 3)
        $this->db->replace('tb_user', [
            'user_code' => 'SIS001',
            'username' => 'siswa_demo',
            'password' => password_hash('siswa123', PASSWORD_BCRYPT),
            'nama_lengkap' => 'Demo Siswa',
            'level' => 3,
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function down()
    {
        // Remove the demo users
        $this->db->where('username', 'hubin_demo')->delete('tb_user');
        $this->db->where('username', 'pembimbing_demo')->delete('tb_user');
        $this->db->where('username', 'siswa_demo')->delete('tb_user');
    }
}
?>
