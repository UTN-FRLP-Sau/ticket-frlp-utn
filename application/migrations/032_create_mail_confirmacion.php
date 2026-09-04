<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_create_mail_confirmacion extends CI_Migration
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
            'mail_nuevo' => [
                'type' => 'VARCHAR',
                'constraint' => '255'
            ],
            'token' => [
                'type' => 'VARCHAR',
                'constraint' => '32'
            ]
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('mail_confirmacion');
    }

    public function down()
    {
        $this->dbforge->drop_table('mail_confirmacion');
    }
}
