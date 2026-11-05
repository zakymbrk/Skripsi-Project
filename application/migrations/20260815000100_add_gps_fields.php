<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_gps_fields extends CI_Migration {
    public function up(){
        // tb_presensi: latitude, longitude (DOUBLE)
        $fields = [
            'latitude' => [
                'type' => 'DOUBLE',
                'null' => TRUE,
                'comment' => 'Latitude GPS saat presensi'
            ],
            'longitude' => [
                'type' => 'DOUBLE',
                'null' => TRUE,
                'comment' => 'Longitude GPS saat presensi'
            ]
        ];
        $this->dbforge->add_column('tb_presensi', $fields);

        // tb_logbook: logbook_status ENUM('valid','invalid')
        $fields_log = [
            'logbook_status' => [
                'type' => "ENUM('valid','invalid')",
                'default' => 'invalid',
                'null' => FALSE,
                'comment' => 'Status validasi logbook'
            ]
        ];
        $this->dbforge->add_column('tb_logbook', $fields_log);
    }

    public function down(){
        $this->dbforge->drop_column('tb_presensi', 'latitude');
        $this->dbforge->drop_column('tb_presensi', 'longitude');
        $this->dbforge->drop_column('tb_logbook', 'logbook_status');
    }
}
?>
