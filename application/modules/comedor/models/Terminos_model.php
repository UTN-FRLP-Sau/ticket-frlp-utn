<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Terminos_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    public function aceptar_terminos($data) {
        
        $this->db->insert('terminos', $data);
    }

    public function validarAceptarTerminos($user_id) {
        $this->db->where('id_usuario', $user_id);
        $query = $this->db->get('terminos');
        
        return $query->num_rows() > 0;
    }
}