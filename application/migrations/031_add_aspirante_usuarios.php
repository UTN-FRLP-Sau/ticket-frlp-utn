<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_aspirante_usuarios extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_column('usuarios', [
            'aspirante' => [
                'type' => 'BOOLEAN',
                'default' => False
            ]
        ]);

        $this->dbforge->add_column('configuracion', [
            'legajo_provisorio_limite' => [
                'type' => 'DATE',
                'default' => '2027-04-30'
            ]
        ]);

        // Backfill: hasta ahora "legajo provisorio" se detectaba por rango
        // (legajo > 900000 o < 20000). Se marca aspirante=1 a quienes ya
        // matcheaban ese rango para no perder el seguimiento existente al
        // pasar a depender del campo explícito.
        $this->db->where('tipo', 'Estudiante');
        $this->db->where('estado', 1);
        $this->db->group_start();
        $this->db->where('legajo >', 900000);
        $this->db->or_where('legajo <', 20000);
        $this->db->group_end();
        $this->db->update('usuarios', ['aspirante' => 1]);
    }

    public function down()
    {
        $this->dbforge->drop_column('usuarios', 'aspirante');
        $this->dbforge->drop_column('configuracion', 'legajo_provisorio_limite');
    }
}
