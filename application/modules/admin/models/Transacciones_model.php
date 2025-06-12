<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Transacciones_model extends CI_Model //Tabla -log_carga-
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getCargasByFechaByIDUser($fecha, $id_user)
    {
        /*Usado en:
        index
        */
        $this->db->select('*');
        $this->db->where('id_usuario', $id_user);
        $this->db->where('fecha', $fecha);
        $query = $this->db->get('log_carga');
        return $query->result();
    }

    public function getHoraCompraByFechaByIDUser($fecha, $id_user)
    {
        /*Usado en:
        index
        */
        $this->db->select('hora');
        $this->db->where('id_usuario', $id_user);
        $this->db->where('fecha', $fecha);
        $query = $this->db->get('compra');
        return $query->row('hora');
    }

    public function getComprasByFechaByIDUser($fecha, $id_user)
    {
        /*Usado en:
        index
        */
        $this->db->select('*');
        $this->db->where('id_usuario', $id_user);
        $this->db->where('fecha', $fecha);
        $query = $this->db->get('compra');
        return $query->result();
    }

    public function getAllUser()
    {
        /*Usado en:
        index
        */
        $this->db->select('*');
        $query = $this->db->get('usuarios');
        return $query->result();
    }

    public function getSaldoByIDUser($id_user)
    {
        /*Usado en:
        index
        */
        $this->db->select('saldo');
        $this->db->where('id', $id_user);
        $query = $this->db->get('usuarios');
        return $query->row('saldo');
    }

    public function updateTransactionInCompraByID($id_compra, $id_trans)
    {
        /*Usado en:
        index
        */
        $this->db->set('transaccion_id', $id_trans);
        $this->db->where('id', $id_compra);
        $this->db->update('compra');
    }

    public function updateTransactionInLogCompraByID($id_compra, $id_trans)
    {
        /*Usado en:
        index
        */
        $array = [
            'transaccion_id' => $id_trans,
            'transaccion_tipo' => 'Compra'
        ];
        $this->db->set($array);
        $this->db->where('id', $id_compra);
        $this->db->update('log_compra');
    }

    public function updateTransactionInCargaByID($id_carga, $id_trans)
    {
        /*Usado en:
        index
        */
        $this->db->set('transaccion_id', $id_trans);
        $this->db->where('id', $id_carga);
        $this->db->update('log_carga');
    }

    public function addTransaccion($data)
    {
        /*Usado en:
        index
        */
        $this->db->insert('transacciones', $data);
        return $this->db->insert_id();
    }
}