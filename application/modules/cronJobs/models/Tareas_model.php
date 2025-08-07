<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tareas_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getComprasPendientes() {
        $this->db->where_in('mp_estado', ['pending', 'in_process']);
        $query = $this->db->get('compras_pendientes'); 
        return $query->result();
    }

    public function actualizarEstadoPago($external_reference, $new_status) {
        $this->db->where('external_reference', $external_reference);
        $this->db->update('compras_pendientes', ['mp_estado' => $new_status]); 
        return $this->db->affected_rows();
    }
}