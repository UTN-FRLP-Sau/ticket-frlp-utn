<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_create_comentarios extends CI_Migration 
{
    public function up() 
    {
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => TRUE,
				'auto_increment' => TRUE
            ],
            'id_usuario'=> [
                'type' => 'INT',
                'constraint' => '10',
                'unsigned' => TRUE
            ],
            'comentario' => [
                'type' => 'TEXT',
            ],
            'fecha' => [
                'type' => 'DATE',
            ],
            'hora' => [
                'type' => 'TIME',
            ]
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('comentarios');
    }

    public function down() 
    {
        $this->dbforge->drop_table('comentarios');
    }
}