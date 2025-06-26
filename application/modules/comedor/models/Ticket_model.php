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
    public function generarPreferenciaConSaldo($external_reference, $access_token, $notification_url, $back_urls)
    {
        $compra = $this->getCompraPendiente($external_reference);
        if (!$compra) {
            return null;
        }

        $saldo_usuario = $this->getSaldoByIDUser($compra->id_usuario);
        $monto_total = (float)$compra->total;
        $monto_a_pagar = $monto_total - $saldo_usuario;

        log_message('debug', "Saldo usuario: $saldo_usuario, total: $monto_total, monto a pagar: $monto_a_pagar");

        if ($monto_a_pagar <= 0) {
            return null;
        }

        // Carga SDK MercadoPago y setea Access Token
        require_once FCPATH . 'vendor/autoload.php';
        MercadoPago\SDK::setAccessToken($access_token);

        // Crea item para la preferencia con el monto a pagar ajustado
        $item = new MercadoPago\Item();
        $item->title = "Compra de menú universitario";
        $item->quantity = 1;
        $item->unit_price = $monto_a_pagar;

        $preference = new MercadoPago\Preference();
        $preference->items = [$item];
        $preference->external_reference = $external_reference;
        $preference->back_urls = $back_urls;
        $preference->auto_return = "approved";
        $preference->notification_url = $notification_url;

        $saved = $preference->save();

        if (!$saved) {
            log_message('error', 'Error guardando preferencia Mercado Pago: ' . print_r($preference->getLastApiResponse(), true));
            return false;
        }

        return [
            'id' => $preference->id,
            'init_point' => $preference->init_point,
            'monto_a_pagar' => $monto_a_pagar,
            'saldo_usuario' => $saldo_usuario
        ];
    }



    public function procesarCompraConSaldo($compra, $saldo_utilizado)
    {
        $this->db->trans_start();

        // Decodificamos los datos JSON de la compra para obtener todos los ítems
        $seleccion = json_decode($compra->datos, true);
        if (!$seleccion || count($seleccion) == 0) {
            $this->db->trans_rollback();
            return false;
        }

        $id_transaccion = null;
        $n_compras = 0;

        // Insertamos cada ítem como una compra individual y su log correspondiente
        foreach ($seleccion as $item) {
            $data_compra = [
                'fecha' => date('Y-m-d'),
                'hora' => date('H:i:s'),
                'dia_comprado' => $item['dia_comprado'],
                'id_usuario' => $compra->id_usuario,
                'precio' => $item['precio'],
                'tipo' => $item['tipo'],
                'turno' => $item['turno'],
                'menu' => $item['menu'],
                'transaccion_id' => -1,
                'external_reference' => $compra->external_reference
            ];

            $id_compra = $this->addCompra($data_compra);
            if (!$id_compra) {
                $this->db->trans_rollback();
                return false;
            }

            $data_log = [
                'fecha' => date('Y-m-d'),
                'hora' => date('H:i:s'),
                'dia_comprado' => $item['dia_comprado'],
                'id_usuario' => $compra->id_usuario,
                'precio' => $item['precio'],
                'tipo' => $item['tipo'],
                'turno' => $item['turno'],
                'menu' => $item['menu'],
                'transaccion_tipo' => 'Compra con saldo',
                'transaccion_id' => -1,
                'external_reference' => $compra->external_reference
            ];

            $this->addLogCompra($data_log);
            $n_compras++;
        }

        $saldo_actual_usuario = $this->getSaldoByIDUser($compra->id_usuario);
        $saldo_final_transaccion = $saldo_actual_usuario - $saldo_utilizado;

        // Registramos una única transacción para todo el saldo utilizado
        $data_transaccion = [
            'fecha' => date('Y-m-d'),
            'hora' => date('H:i:s'),
            'id_usuario' => $compra->id_usuario,
            'transaccion' => 'Compra con saldo',
            'monto' => -$saldo_utilizado,
            'saldo' => $saldo_final_transaccion
        ];

        $id_transaccion = $this->addTransaccion($data_transaccion);
        if (!$id_transaccion) {
            $this->db->trans_rollback();
            return false;
        }

        // Actualizamos todas las compras recién insertadas con el ID de la transacción
        $this->db->where('external_reference', $compra->external_reference);
        $this->db->where('transaccion_id', -1);
        $this->db->set('transaccion_id', $id_transaccion);
        $this->db->update('compra');

        // También actualizamos los logs de compra con el ID de la transacción
        $this->db->where('external_reference', $compra->external_reference);
        $this->db->where('transaccion_id', -1);
        $this->db->set('transaccion_id', $id_transaccion);
        $this->db->update('log_compra');

        // Actualizamos saldo en tabla usuarios
        $saldo_actual = $this->getSaldoByIDUser($compra->id_usuario);
        $saldo_nuevo = $saldo_actual - $saldo_utilizado;
        if (!$this->updateSaldoByIDUser($compra->id_usuario, $saldo_final_transaccion)) {
            $this->db->trans_rollback();
            return false;
        }

        // Marcamos la compra pendiente como procesada
        $this->setCompraPendienteProcesada($compra->external_reference);

        $this->db->trans_complete();

        return $this->db->trans_status();
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