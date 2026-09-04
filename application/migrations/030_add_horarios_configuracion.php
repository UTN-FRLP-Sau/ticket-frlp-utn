<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_horarios_configuracion extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_column('configuracion', [
            'hora_apertura_venta' => [
                'type' => 'TIME',
                'default' => '00:01:00'
            ],
            'retiro_mediodia_desde' => [
                'type' => 'TIME',
                'default' => '12:00:00'
            ],
            'retiro_mediodia_hasta' => [
                'type' => 'TIME',
                'default' => '14:00:00'
            ],
            'retiro_noche_desde' => [
                'type' => 'TIME',
                'default' => '20:00:00'
            ],
            'retiro_noche_hasta' => [
                'type' => 'TIME',
                'default' => '22:00:00'
            ]
        ]);
    }

    public function down()
    {
        $this->dbforge->drop_column('configuracion', 'hora_apertura_venta');
        $this->dbforge->drop_column('configuracion', 'retiro_mediodia_desde');
        $this->dbforge->drop_column('configuracion', 'retiro_mediodia_hasta');
        $this->dbforge->drop_column('configuracion', 'retiro_noche_desde');
        $this->dbforge->drop_column('configuracion', 'retiro_noche_hasta');
    }
}
