<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_alter_config extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_column('configuracion', [
            'correo_contacto' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => 'comedor@frlp.utn.edu.ar'
            ]
        ]);
    }

    public function down()
    {
        $this->dbforge->drop_column('configuracion', 'correo_contacto');
    }
}