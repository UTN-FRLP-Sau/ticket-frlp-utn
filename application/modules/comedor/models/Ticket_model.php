<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ticket_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function addCompra($data)
    {
        /* Usado en:
        compra
        */
        if ($this->db->insert('compra', $data)) {
            return $this->db->insert_id();
        } else {
            return false;
        }
    }

    public function addLogCompra($data)
    {
        /* Usado en:
        compra
        devolverCompra
        */
        if ($this->db->insert('log_compra', $data)) {
            return true;
        } else {
            return false;
        }
    }

    public function updateTransactionInCompraByExternalReference($external_reference, $transaction_id)
    {
        $this->db->where('external_reference', $external_reference);
        $this->db->where('transaccion_id', -1);
        $this->db->set('transaccion_id', $transaction_id);
        $this->db->update('compra');
        return $this->db->affected_rows();
    }

    public function updateTransactionInLogCompraByExternalReference($external_reference, $transaction_id)
    {
        $this->db->where('external_reference', $external_reference);
        $this->db->where('transaccion_id', -1);
        $this->db->set('transaccion_id', $transaction_id);
        $this->db->update('log_compra');
        return $this->db->affected_rows();
    }
    
    public function updateSaldoByIDUser($id_user, $saldo_nuevo)
    {
        /* Usado en:
        compra
        devolverCompra
        */
        $this->db->set('saldo', $saldo_nuevo);
        $this->db->where('id', $id_user);
        if ($this->db->update('usuarios')) {
            return true;
        } else {
            return false;
        }
    }

    public function getCostoByID($id)
    {
        /* Usado en:
        index
        compra
        devolverCompra
        */
        $this->db->select('costo');
        $this->db->where('id', $id);
        $query = $this->db->get('precios');
        return $query->row('costo');
    }

    public function getCompraById($id)
    {
        /*Usado en:
        devolverCompra
        */
        $this->db->select('*');
        $this->db->where('id', $id);
        $query = $this->db->get('compra');
        return $query->row();
    }

    public function getSaldoByIDUser($id_user)
    {
        /*Usado en:
        compra
        devolverCompra
        */
        $this->db->select('saldo');
        $this->db->where('id', $id_user);
        $query = $this->db->get('usuarios');
        return $query->row('saldo');
    }

    public function getComprasInRangeByIdUser($fecha_i, $fecha_f, $idusuario)
    {
        /* Usado en:
        index
        devolverCompra
        */
        $this->db->select('*');
        $this->db->where('id_usuario', $idusuario);
        $this->db->where('dia_comprado >=', $fecha_i);
        $this->db->where('dia_comprado <=', $fecha_f);
        $this->db->order_by('dia_comprado');
        $query = $this->db->get('compra');
        return $query->result();
    }

    public function getComprasByIDTransaccion($id_trans)
    {
        /* Usado en:
        compra
        */
        $this->db->select('*');
        $this->db->where('transaccion_id', $id_trans);
        $query = $this->db->get('compra');
        return $query->result();
    }

    public function getLogComprasByIDTransaccion($id_trans)
    {
        /* Usado en:
        compra
        devolverCompra
        */
        $this->db->select('*');
        $this->db->where('transaccion_id', $id_trans);
        $query = $this->db->get('log_compra');
        return $query->result();
    }

    public function addTransaccion($data)
    {
        log_message('debug', 'Ticket_model: addTransaccion - Datos recibidos: ' . json_encode($data));
        if ($this->db->insert('transacciones', $data)) {
            $insert_id = $this->db->insert_id();
            log_message('debug', 'Ticket_model: addTransaccion - Inserción exitosa. ID: ' . $insert_id);
            return $insert_id;
        } else {
            $db_error = $this->db->error();
            log_message('error', 'Ticket_model: addTransaccion - Fallo en la inserción. DB Error: ' . $db_error['message'] . ' Code: ' . $db_error['code']);
            return false;
        }
    }

    public function updateTransactionInCompraByID($id_compra, $id_trans)
    {
        /* Usado en:
        compra
        */
        $this->db->set('transaccion_id', $id_trans);
        $this->db->where('id', $id_compra);
        $this->db->update('compra');
    }

    public function updateTransactionInLogCompraByID($id_compra, $id_trans)
    {
        /* Usado en:
        compra
        devolverCompra
        */
        $this->db->set('transaccion_id', $id_trans);
        $this->db->where('id', $id_compra);
        $this->db->update('log_compra');
    }

    public function removeCompra($idcompra)
    {
        /* Usado en:
        devolverCompra
        */
        if ($this->db->delete('compra', ['id' => $idcompra])) {
            return true;
        } else {
            return false;
        }
    }

    public function getFeriadosInRange($fecha_i, $fecha_f)
    {
        /* Usado en:
        index
        */
        $this->db->select('*');
        $this->db->where('fecha >=', $fecha_i);
        $this->db->where('fecha <=', $fecha_f);
        $query = $this->db->get('feriado');
        return $query->result();
    }


    public function getUserById($id)
    {
        /* Usado en:
        index
        compra
        devolverCompra
        */
        $this->db->select('*');
        $this->db->where('id', $id);
        $query = $this->db->get('usuarios');
        return $query->row();
    }

    public function getConfiguracion()
    {
        /*Usado en:
        estadoComedor
        estadoCompra
        */
        return $this->db->get('configuracion')->result();
    }

    public function getCargaByTransaccion($id_transaccion)
    {
        /* Usdo en:

        */
        $this->db->select('*');
        $this->db->from('log_carga');
        $this->db->join('usuarios', 'log_carga.id_usuario=usuarios.id');
        $this->db->where('log_carga.transaccion_id', $id_transaccion);
        $query = $this->db->get();
        return $query->result();
    }

    public function guardarCompraPendiente($data) {
        return $this->db->insert('compras_pendientes', $data);
    }

    public function getCompraPendiente($external_reference) {
        return $this->db->get_where('compras_pendientes', ['external_reference' => $external_reference])->row();
    }

    public function setCompraPendienteProcesada($external_reference) {
        $this->db->where('external_reference', $external_reference);
        $this->db->update('compras_pendientes', ['procesada' => 1]);
        return $this->db->affected_rows();
    }
}