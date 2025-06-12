<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_create_precios extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => '2',
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'costo' => [
                'type' => 'INT',
                'constraint' => '4',
                'unsigned' => TRUE
            ],
            'tipo_user' => [
                'type' => 'VARCHAR',
                'constraint' => '20'
            ],
        ]);

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('precios');

        $data = [
            ['id' => 1, 'costo' => 180, 'tipo_user' => 'Estudiante'],
            ['id' => 2, 'costo' => 90, 'tipo_user' => 'Becado'],
            ['id' => 3, 'costo' => 380, 'tipo_user' => 'Docente'],
            ['id' => 4, 'costo' => 380, 'tipo_user' => 'No Docente']
        ];

        $this->db->insert_batch('precios', $data);
    }
    public function down()
    {
        $this->dbforge->drop_table('precios');
    }
}
