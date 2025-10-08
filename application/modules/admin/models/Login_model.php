<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Login_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function validateVendedor($nickName, $password)
    {
        /*Usado en:
        index
        */
        $this->db->where('nombre_usuario', $nickName);
        $this->db->where('pass', $password);
        return $this->db->get('vendedores')->row();
    }

    public function getVendedorByUserName($nickName)
    {
        /*Usado en:
        index
        */
        $this->db->select('*');
        $this->db->where('nombre_usuario', $nickName);
        $query = $this->db->get('vendedores');
        return $query->row();
    }
}