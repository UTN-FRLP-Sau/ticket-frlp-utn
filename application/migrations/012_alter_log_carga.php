<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_alter_log_carga extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_column('log_carga', [
            'formato' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
                'default' => 'Efectivo'
            ]
        ]);
    }

    public function down()
    {
        $this->dbforge->drop_column('log_carga', 'formato');
    }
}