<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_user_code_to_user extends CI_Migration {
    public function up()
    {
        // Add user_code column (unique, nullable)
        $fields = [
            'user_code' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => TRUE,
                // CodeIgniter DBForge does not support 'unique' directly in field definition;
                // we will add a UNIQUE index after column creation.
                'comment' => 'Unique code for user'
            ]
        ];
        $this->dbforge->add_column('tb_user', $fields);
        // Add unique index (allows multiple NULL values)
        $this->db->query('ALTER TABLE `tb_user` ADD UNIQUE INDEX `idx_user_code` (`user_code`)');
    }

    public function down()
    {
        $this->dbforge->drop_column('tb_user', 'user_code');
    }
}
?>
