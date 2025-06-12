<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_alter_2_compra extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_column('compra', [
            'retiro' => [
                'type' => 'BOOLEAN',
                'default' => False
            ],
            'id_repartidor' => [
                'type' => 'INT',
                'constraint' => '3',
                'default' => 0
            ]
        ]);
    }

    public function down()
    {
        $this->dbforge->drop_column('compra', 'retiro');
        $this->dbforge->drop_column('compra', 'id_repartidor');
    }
}