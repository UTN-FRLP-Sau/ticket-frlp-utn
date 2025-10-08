<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Comentarios_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    public function guardar_comentario($data) {
        $this->db->insert('comentarios', $data);
    }
}