<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_mp_vendedor extends CI_Migration {

    public function up()
    {

        $this->db->where('nombre_usuario', 'MP');
        $query = $this->db->get('vendedores');

        if ($query->num_rows() == 0) {
            $data = array(
                'nombre_usuario' => 'MP',
                'nombre'         => 'Mercado Pago',
                'apellido'       => '',
                'mail'           => 'mercado@pago',
                'pass'           => password_hash(uniqid(), PASSWORD_DEFAULT), // Genera una contraseña hasheada
                'estado'         => 0,
                'nivel'          => 0,
            );

            $this->db->insert('vendedores', $data);
            log_message('debug', 'Migración: Vendedor MP insertado con ID auto-asignado.');
        } else {
            log_message('debug', 'Migración: Vendedor MP ya existe. No se insertó.');
        }
    }

    public function down()
    {
        $this->db->where('nombre_usuario', 'MP');
        $this->db->delete('vendedores');
        log_message('debug', 'Migración: Vendedor MP eliminado.');
    }
}