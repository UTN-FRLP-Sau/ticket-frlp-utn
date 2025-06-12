<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Vendedor_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getVendedorById($id)
    {
        /*Usado en:
        descargarCierreCajaDiario
        descargarCierreCajaSemana
        descargarResumenPedidosSemana
        */
        $this->db->select('*');
        $this->db->where('id_vendedor', $id);
        $query = $this->db->get('vendedores');
        return $query->row();
    }

    public function addCargaLog($data)
    {
        /*Usado en:
        cargarSaldo
        */
        $this->db->insert('log_carga', $data);
        return true;
    }

    public function getUserByDocumento($documento)
    {
        /*Usado en:
        index
        createUser
        cargarSaldo
        */
        $this->db->select('*');
        $this->db->where('documento', $documento);
        $query = $this->db->get('usuarios');
        return $query->row();
    }

    public function addNewUser($data)
    {
        /*Usado en:
        createUser
        */
        if ($this->db->insert('usuarios', $data)) {
            return $this->db->insert_id();
        } else {
            return false;
        };
    }

    public function addLogNewUser($data)
    {
        /*Usado en:
        createUser
        */
        $this->db->insert('log_alta_usuarios', $data);
        return true;
    }

    public function getUserById($id)
    {
        /*Usado en:
        createUser
        descargarExcel
        */
        $this->db->select('*');
        $this->db->where('id', $id);
        $query = $this->db->get('usuarios');
        return $query->row();
    }

    public function updateUserById($iduser, $data)
    {
        /*Usado en:
        updateUser
        */
        $this->db->where('id', $iduser);
        $this->db->update('usuarios', $data);
        return true;
    }

    public function getCargasByIdvendedor($idvendedor)
    {
        /*Usado en:
        historialCargas
        */
        $this->db->select('*');
        $this->db->from('log_carga');
        $this->db->join('usuarios', 'log_carga.id_usuario=usuarios.id');
        $this->db->where('id_vendedor', $idvendedor);
        $this->db->limit(20);
        $this->db->order_by('log_carga.id', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function updateAndGetSaldoByUserId($iduser, $saldo)
    {
        /*Usado en:
        cargaSaldo
        */
        $saldoActual = $this->db->select('saldo')->where('id', $iduser)->get('usuarios')->row('saldo');
        $saldoNuevo = $saldoActual + $saldo;

        $this->db->set('saldo', $saldoNuevo);
        $this->db->where('id', $iduser);
        $this->db->update('usuarios');
        return $saldoNuevo;
    }

    public function addTransaccion($data)
    {
        /* Usdo en:
        cargarSaldo
        createUser
        */
        $this->db->insert('transacciones', $data);
        return $this->db->insert_id();
    }

    public function getCargaByTransaccion($id_transaccion)
    {
        /* Usdo en:
        correoCargaSaldo
        */
        $this->db->select('*');
        $this->db->from('log_carga');
        $this->db->join('usuarios', 'log_carga.id_usuario=usuarios.id');
        $this->db->where('log_carga.transaccion_id', $id_transaccion);
        $query = $this->db->get();
        return $query->result();
    }

    public function getCargasByFechaForPDF($fecha)
    {
        /* Usado en:
        descargarCierreCajaDiario
        */
        $this->db->select('*');
        $this->db->from('log_carga');
        $this->db->join('vendedores', 'log_carga.id_vendedor=vendedores.id_vendedor');
        $this->db->join('usuarios', 'log_carga.id_usuario=usuarios.id');
        $this->db->order_by('log_carga.id', 'DESC');
        $this->db->where('fecha', $fecha);
        $query = $this->db->get();
        return $query->result();
    }

    public function getCargasByRangeFechaForPDF($fecha1, $fecha2)
    {
        /* Usado en:
        descargarCierreCajaSemana
        */
        $this->db->select('*');
        $this->db->from('log_carga');
        $this->db->where('fecha>=', $fecha1);
        $this->db->where('fecha<=', $fecha2);
        $query = $this->db->get();
        return $query->result();
    }

    public function getComprasByRangeFechaForPDF($fecha1, $fecha2)
    {
        /* Usado en:
        descargarResumenPedidosSemana
        */
        $this->db->select('*');
        $this->db->from('compra');
        $this->db->where('dia_comprado>=', $fecha1);
        $this->db->where('dia_comprado<=', $fecha2);
        $query = $this->db->get();
        return $query->result();
    }

    public function getComprasByFechaForExls($fecha)
    {
        /* Usado en:
        descarcgarExcel
        */
        $this->db->select('*');
        $this->db->from('compra c');
        $this->db->join('usuarios u', 'c.id_usuario=u.id');
        $this->db->order_by('u.apellido', 'ASC');
        $this->db->where('dia_comprado', $fecha);
        $query = $this->db->get();
        return $query->result();
    }

    public function updateMenu($id, $data)
    {
        /*Usado en:
        updateMenu
        */
        $this->db->where('id', $id);
        $this->db->update('menu', $data);
        return true;
    }

    public function getMenu()
    {
        /*Usado en:
        updateMenu
        */
        $this->db->select('*');
        $query = $this->db->get('menu');
        return $query->result();
    }

    // public function addHistorialMenu($data)
    // {
    //     /*Usado en:
    //     addHistorialMenu
    //     */
    //     $this->db->insert('historial_menu', $data);
    //     return true;
    // }

    // public function getHistorialMenu()
    // {
    //     $query = $this->db->get('historial_menu');
    //     return $query->result();
    // }
}