<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_create_cargas_botonera extends CI_Migration 
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
            'usuario'=> [
                'type' => 'INT'
            ],
            'timestamp' => [
                'type' => 'DATETIME'
            ],
            'monto' => [
                'type' => 'INT'
            ],
            'estado' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'confirmacion_timestamp' => [
                'type' => 'DATETIME',
                'null' => TRUE
            ],
            'confirmacion_vendedor' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE
            ],
        ]);
        // This table stores virtual load transactions, including user, amount, state, and timestamps.
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('cargasvirtuales');

    }

    public function down() 
    {
        $this->dbforge->drop_table('cargasvirtuales', TRUE);
    }
}