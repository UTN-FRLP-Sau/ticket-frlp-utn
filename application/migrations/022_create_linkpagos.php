<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_create_linkpagos extends CI_Migration 
{
    public function up() 
    {
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => '3',
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'valor' => [
                'type' => 'INT',
                'constraint' => '10',
                'unsigned' => TRUE
            ],
            'link' => [
                'type' => 'VARCHAR',
                'constraint' => '50'
            ],
            'tipo_user' => [
                'type' => 'VARCHAR',
                'constraint' => '20'
            ],
        ]);
        // This table stores virtual load transactions, including user, amount, state, and timestamps.
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('linkpagos');

    }

    public function down() 
    {
        $this->dbforge->drop_table('linkpagos', TRUE);
    }
}