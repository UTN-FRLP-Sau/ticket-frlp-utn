<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_alter_log_compra extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_column('log_compra', [
            'transaccion_tipo' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
                'default' => '0'
            ],
            'transaccion_id' => [
                'type' => 'INT',
                'constraint' => '10',
                'default' => 0
            ]
        ]);
    }

    public function down()
    {
        $this->dbforge->drop_column('log_compra', 'transaccion_tipo');
        $this->dbforge->drop_column('log_compra', 'transaccion_id');
    }
}