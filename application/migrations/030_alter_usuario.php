<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_alter_usuario extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_column('usuarios', [
            'certificado_path' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => ''
            ]
        ]);
    }

    public function down()
    {
        $this->dbforge->drop_column('usuarios', 'certificado_path');
    }
}