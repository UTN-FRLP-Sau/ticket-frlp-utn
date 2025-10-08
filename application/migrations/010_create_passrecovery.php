<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_create_passrecovery extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => '10',
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'fecha' => [
                'type' => 'DATE'
            ],
            'hora' => [
                'type' => 'TIME'
            ],
            'id_usuario' => [
                'type' => 'INT',
                'constraint' => '10',
                'unsigned' => TRUE
            ],
            'token' => [
                'type' => 'VARCHAR',
                'constraint' => '32'
            ]
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('passrecovery');
    }

    public function down()
    {
        $this->dbforge->drop_table('passrecovery');
    }
}
