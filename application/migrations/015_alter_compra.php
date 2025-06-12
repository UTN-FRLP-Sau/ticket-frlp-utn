<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_alter_compra extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_column('compra', [
            'transaccion_id' => [
                'type' => 'INT',
                'constraint' => '10',
                'default' => 0
            ]
        ]);
    }

    public function down()
    {
        $this->dbforge->drop_column('compra', 'transaccion_id');
    }
}