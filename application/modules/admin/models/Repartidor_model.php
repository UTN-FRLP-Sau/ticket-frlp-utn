<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Repartidor_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

        public function getAdminById($id)
    {
        /*Usado en:
        createVendedor
        */
        $this->db->select('*');
        $this->db->where('id_vendedor', $id);
        $query = $this->db->get('vendedores');
        return $query->row();
    }

        public function getUserByDocumento($documento)
    {
        /*Usado en:
        confirmarCargasCVS
        */
        $this->db->select('*');
        $this->db->where('documento', $documento);
        $query = $this->db->get('usuarios');
        return $query->row();
    }

    public function getComprasByFechaAndUser($fecha,$id_user)
    {
        /*Usado en:
        devolver_compras_by_fecha
        */
        $this->db->select('*');
        $this->db->where('dia_comprado', $fecha);
        $this->db->where('id_usuario', $id_user);
        $query = $this->db->get('compra');
        if ($query->num_rows() == 1) {
            return $query->row();
        } elseif ($query->num_rows() > 1){
            return $query->row(0);
        } else {
            return false;
        }
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

    public function updateCompraByID($id_compra, $id_vendedor)
    {
        $this->db->set('retiro', TRUE);
        $this->db->set('id_repartidor', $id_vendedor);
        $this->db->where('id', $id_compra);
    
        // Ejecuta la actualización y verifica si fue exitosa
        if ($this->db->update('compra')) {
            return TRUE; // La actualización fue exitosa
        } else {
            return FALSE; // La actualización falló
        }
    }


    public function getComprasByID($id_compra)
    {
        /*Usado en:
        ver_compras_userid
        */
        $this->db->select('*');
        $this->db->where('id', $id_compra);
        $query = $this->db->get('compra');
        return $query->row();
    }
    
    public function getUserByID($id_user)
    {
        /*Usado en:
        ver_compras_userid
        */
        $this->db->select('*');
        $this->db->where('id', $id_user);
        $query = $this->db->get('usuarios');
        return $query->row();
    }

    public function getComprasByDiaComprado($fecha)
    {
        /*Usado en:
        historial_entregas_by_fecha
        */
        $this->db->select('*');
        $this->db->where('dia_comprado', $fecha);
        $query = $this->db->get('compra');
        return $query->result();
    }
}