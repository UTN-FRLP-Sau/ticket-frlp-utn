<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_notificaciones_usuarios extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_column('usuarios', [
            'notif_recordatorio_compra' => [
                'type' => 'BOOLEAN',
                'default' => True
            ],
            'notif_recordatorio_retiro' => [
                'type' => 'BOOLEAN',
                'default' => True
            ]
        ]);
    }

    public function down()
    {
        $this->dbforge->drop_column('usuarios', 'notif_recordatorio_compra');
        $this->dbforge->drop_column('usuarios', 'notif_recordatorio_retiro');
    }
}
