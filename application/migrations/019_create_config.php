<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_create_config extends CI_Migration 
{
    public function up() 
    {
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT'
            ],
            'apertura'=> [
                'type' => 'DATE'
            ],
            'cierre' => [
                'type' => 'DATE'
            ],
            'vacaciones_i' => [
                'type' => 'DATE'
            ],
            'vacaciones_f' => [
                'type' => 'DATE'
            ],
            'dia_inicial' => [
                'type' => 'INT',
                'constraint' => '1'
            ],
            'dia_final' => [
                'type' => 'INT',
                'constraint' => '1'
            ],
            'hora_final' => [
                'type' => 'TIME',
            ]
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('configuracion');
        $data = [
            [
                'id' => 1,
                'apertura' => '2024-04-04',
                'cierre' => '2024-12-04',
                'vacaciones_i' => '2024-07-08',
                'vacaciones_f' => '2024-07-22',
                'dia_inicial' => '1',
                'dia_final' => '5',
                'hora_final' => '04:00:00'
            ],
        ];

        $this->db->insert_batch('configuracion', $data);
    }

    public function down() 
    {
        $this->dbforge->drop_table('configuracion');
    }
}