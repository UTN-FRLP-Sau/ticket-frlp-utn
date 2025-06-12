<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_alter_2_log_carga extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_column('log_carga', [
            'transaccion_id' => [
                'type' => 'INT',
                'constraint' => '10',
                'default' => 0
            ]
        ]);
    }

    public function down()
    {
        $this->dbforge->drop_column('log_carga', 'transaccion_id');
    }
}