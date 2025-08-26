<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Configuracion_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Obtiene la fila de configuración de la base de datos.
     * 
     * @return object|null Objeto que contiene los datos de configuración o null si no se encuentra.
     */
    public function obtener_configuracion()
    {
        // Realiza una consulta para obtener todos los datos de la tabla 'configuracion'
        $query = $this->db->get('configuracion');
        
        // Retorna la primera fila como un objeto
        return $query->row();
    }

}