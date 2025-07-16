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
        /* Usado en:
        pago.php
        comprar()
        */
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

        // Envuelve el guardado en un try-catch para capturar excepciones
        try {
            $saved = $preference->save();

            if (!$saved) {
                log_message('error', 'Error guardando preferencia Mercado Pago: La validación de la preferencia falló o los datos son inválidos.');
                return false;
            }

            return [
                'id' => $preference->id,
                'init_point' => $preference->init_point,
                'monto_a_pagar' => $monto_a_pagar,
                'saldo_usuario' => $saldo_usuario
            ];
        } catch (Exception $e) {
            // Captura cualquier excepción que Mercado Pago SDK pueda lanzar (ej. errores de conexión, errores de API).
            log_message('error', 'Excepción al intentar guardar la preferencia de Mercado Pago: ' . $e->getMessage());
            return false;
        }
    }




    public function procesarCompraConSaldo($compra, $saldo_utilizado)
    {
        /* Usado en:
        pago.php
        comprar()
        */
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
            'saldo' => $saldo_final_transaccion,
            'external_reference' => $compra->external_reference
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

        // Actualizamos el estado 'mp_estado' a 'approved' y marcamos 'procesada' en la tabla 'compras_pendientes'
        $this->db->set('mp_estado', 'approved');
        $this->db->set('procesada', 1);
        $this->db->where('id', $compra->id);
        $this->db->update('compras_pendientes');

        $this->setCompraPendienteProcesada($compra->external_reference);


        $this->db->trans_complete();

        return $this->db->trans_status();
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

        Pago.php
        comprar()
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

    public function getComprasByExternalReference($external_reference)
    {
        $this->db->select('*');
        $this->db->where('external_reference', $external_reference);
        $query = $this->db->get('compra');
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
        log_message('debug', 'Ticket_model: getUserById() llamado con ID: ' . $id); // Nuevo log
        $this->db->select('*');
        $this->db->where('id', $id);
        $query = $this->db->get('usuarios');
        
        log_message('debug', 'Ticket_model: getUserById() - Número de filas encontradas: ' . $query->num_rows()); // Nuevo log
        
        $result = $query->row();
        if ($result) {
            log_message('debug', 'Ticket_model: getUserById() - Usuario encontrado: ' . json_encode($result)); // Nuevo log
        } else {
            log_message('debug', 'Ticket_model: getUserById() - Usuario NO encontrado para ID: ' . $id); // Nuevo log
        }
        return $result;
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
        /* Usado en:
        Ticket 
        compra()
        */
        return $this->db->insert('compras_pendientes', $data);
    }

    public function getCompraPendiente($external_reference) {
        log_message('debug', 'TICKET_MODEL: getCompraPendiente llamado con external_reference: ' . $external_reference);

        $this->db->where('external_reference', $external_reference);
        $query = $this->db->get('compras_pendientes');

        // **AÑADE ESTAS LÍNEAS DE LOG**
        log_message('debug', 'TICKET_MODEL: getCompraPendiente - SQL Query: ' . $this->db->last_query());
        log_message('debug', 'TICKET_MODEL: getCompraPendiente - Número de filas encontradas: ' . $query->num_rows());

        if ($query->num_rows() > 0) {
            $result = $query->row();
            log_message('debug', 'TICKET_MODEL: getCompraPendiente - Compra pendiente encontrada: ' . json_encode($result));
            return $result;
        } else {
            log_message('debug', 'TICKET_MODEL: getCompraPendiente - No se encontró compra pendiente para la referencia.');
            return null;
        }
    }

    public function setCompraPendienteProcesada($external_reference) {
        /* Usado en:
        Webhook
        mercadopago()
        */
        $this->db->where('external_reference', $external_reference);
        $this->db->update('compras_pendientes', ['procesada' => 1]);
        return $this->db->affected_rows();
    }

    public function updateCompraPendienteEstado($id_compra_pendiente, $estado) {
        $this->db->where('id', $id_compra_pendiente);
        $this->db->update('compras_pendientes', ['mp_estado' => $estado]);
        return $this->db->affected_rows() > 0;
    }
 
    public function getComprasPendientes($user_id) {
        // Seleccionamos solo las columnas necesarias para los nuevos requisitos
        $this->db->select('cp.id, cp.external_reference, cp.mp_estado, cp.datos');
        $this->db->from('compras_pendientes AS cp');
        $this->db->where('cp.id_usuario', $user_id);
        // Filtramos por los estados que deseas distinguir
        $this->db->where_in('cp.mp_estado', ['pending', 'pasarela']);
        $query = $this->db->get();

        $result_data = [];
        foreach ($query->result() as $row) {
            $viandas_json = $row->datos;
            $viandas_array = json_decode($viandas_json, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($viandas_array)) {
                foreach ($viandas_array as $vianda) {
                    $date_field = isset($vianda['dia_comprado']) ? 'dia_comprado' : 'fecha';
                    if (isset($vianda[$date_field]) && isset($vianda['turno'])) {
                        $result_data[] = [
                            'dia_comprado' => $vianda[$date_field],
                            'turno' => $vianda['turno'],
                            'menu' => $vianda['menu'] ?? null,
                            'mp_estado' => $row->mp_estado
                        ];
                    }
                }
            } else {
                log_message('error', 'Error al decodificar JSON de viandas para compra pendiente ID: ' . $row->id . ' - ' . json_last_error_msg());
            }
        }

        $unique_viandas = [];
        foreach ($result_data as $item) {
            $key = $item['dia_comprado'] . '-' . $item['turno'];
            $unique_viandas[$key] = $item;
        }

        return array_values($unique_viandas);
    }

    public function getAnyPasarelaPurchaseForUser(int $id_usuario)
    {
        $this->db->where('id_usuario', $id_usuario);
        $this->db->where('mp_estado', 'pasarela');
        $this->db->where('procesada', 0);
        $this->db->order_by('created_at', 'DESC'); // La más reciente primero
        $this->db->limit(1);
        $query = $this->db->get('compras_pendientes');

        if ($query->num_rows() > 0) {
            return $query->row();
        }
        return null;
    }

    public function getMercadoPagoPayment($payment_id)
    {
        try {
            $payment = MercadoPago\Payment::find_by_id($payment_id);
            if ($payment) {
                return $payment; // Retorna el objeto Payment de Mercado Pago
            }
            log_message('warning', 'No se encontró el pago en Mercado Pago con ID: ' . $payment_id);
            return null;
        } catch (Exception $e) {
            log_message('error', 'Error al obtener pago de Mercado Pago (ID: ' . $payment_id . '): ' . $e->getMessage());
            return null;
        }
    }

    public function getViandasCompraPendiente($compra_id)
    {
        $this->db->select('datos');
        $this->db->where('id', $compra_id);
        $query = $this->db->get('compras_pendientes');

        if ($query->num_rows() > 0) {
            $row = $query->row();
            // Decodifica el JSON del campo 'datos'
            $viandas = json_decode($row->datos, true); // true para obtener un array asociativo
            if (json_last_error() === JSON_ERROR_NONE) {
                return $viandas;
            } else {
                log_message('error', 'Error al decodificar JSON de viandas para compra_id ' . $compra_id . ': ' . json_last_error_msg());
                return null;
            }
        }
        return null;
    }

    public function getTransaccionByExternalReference($external_reference)
        {
            $this->db->where('external_reference', $external_reference);
            $query = $this->db->get('transacciones');
            if ($query->num_rows() > 0) {
                return $query->row();
            }
            return null;
    }

public function esFechaViandaAunOrdenable(string $fechaViandaStr)
    {
        // Obtener la configuración dentro del modelo
        $configuracion = $this->getConfiguracion();

        // Asegurarse de que $configuracion sea un array o un objeto válido
        if (is_array($configuracion) && isset($configuracion[0])) {
            $config = $configuracion[0];
        } elseif (is_object($configuracion)) {
            $config = $configuracion; // Asume que ya es el objeto directo si no es un array
        } else {
            log_message('error', 'esFechaViandaAunOrdenable (MODEL): Configuración inválida proporcionada.');
            return false; // No se puede validar sin configuración
        }

        try {
            $fechaVianda = new DateTime($fechaViandaStr);
            $fechaHoraActual = new DateTime('now');
            $hoy = new DateTime($fechaHoraActual->format('Y-m-d')); // Solo la parte de la fecha

            // 1. **Verificar si la fecha de la vianda ya pasó.**
            // Si la fecha de la vianda es anterior al día de hoy, ya no es ordenable.
            if ($fechaVianda < $hoy) {
                log_message('debug', 'esFechaViandaAunOrdenable (MODEL): La fecha de la vianda (' . $fechaViandaStr . ') ya ha pasado.');
                return false;
            }

            // 2. **Verificar períodos generales de operación del comedor (apertura/cierre, vacaciones).**
            // Se asume que $config tiene las propiedades apertura, vacaciones_i, vacaciones_f, cierre
            $apertura = new DateTime($config->apertura);
            $vaca_ini = new DateTime($config->vacaciones_i);
            $vaca_fin = new DateTime($config->vacaciones_f);
            $cierre = new DateTime($config->cierre);

            $esPeriodoValido = false;
            if (($fechaVianda >= $apertura && $fechaVianda <= $vaca_ini) || ($fechaVianda >= $vaca_fin && $fechaVianda <= $cierre)) {
                $esPeriodoValido = true;
            }

            // En entorno de desarrollo, si el comedor no está en un período válido,
            // se permite continuar la validación para facilitar las pruebas.
            if (!$esPeriodoValido && $_SERVER['CI_ENV'] !== 'development') {
                log_message('debug', 'esFechaViandaAunOrdenable (MODEL): La fecha de la vianda (' . $fechaViandaStr . ') está fuera de los períodos generales de operación del comedor.');
                return false;
            }

            // 3. **Implementar la regla específica de corte semanal de pedidos.**
            $diaFinalCompra = (int)$config->dia_final; // Por ejemplo, 2 para martes
            $horaFinalCompra = $config->hora_final;    // Por ejemplo, '04:00:00' (4 AM)

            // Calcular el lunes de la semana a la que pertenece la vianda (ej. para 2025-07-21, es 2025-07-21)
            $lunesSemanaVianda = clone $fechaVianda;
            if ((int)$lunesSemanaVianda->format('N') !== 1) { // Si no es lunes
                $lunesSemanaVianda->modify('last monday'); // Ir al lunes anterior
            }
            $lunesSemanaVianda->setTime(0,0,0); // Resetear la hora para la comparación

            // Calcular la fecha y hora de corte para poder ordenar esta vianda.
            // Esta fecha de corte ocurre en la semana *anterior* a la de la vianda.
            // Por ejemplo, para una vianda del Lunes 21 de Julio, el corte es el Martes 4 AM de la semana del 14 de Julio.
            $fechaCorteParaEstaVianda = clone $lunesSemanaVianda;
            $fechaCorteParaEstaVianda->modify('-1 week'); // Ir al lunes de la semana anterior

            // Avanzar al día de corte (diaFinalCompra) de esa semana anterior
            while ((int)$fechaCorteParaEstaVianda->format('N') !== $diaFinalCompra) {
                $fechaCorteParaEstaVianda->modify('+1 day');
            }
            
            // Establecer la hora de corte
            list($hora, $minuto, $segundo) = explode(':', $horaFinalCompra);
            $fechaCorteParaEstaVianda->setTime((int)$hora, (int)$minuto, (int)$segundo);

            // Comparar la hora actual con la fecha de corte calculada para esta vianda específica.
            if ($fechaHoraActual > $fechaCorteParaEstaVianda) {
                // Si la hora actual es *después* de la fecha y hora de corte para esta vianda,
                // significa que el plazo para pedirla ha expirado.
                log_message('debug', 'esFechaViandaAunOrdenable (MODEL): Vianda ' . $fechaViandaStr . ' es inválida. La fecha de corte (' . $fechaCorteParaEstaVianda->format('Y-m-d H:i:s') . ') para esta vianda ha pasado. Hora actual: ' . $fechaHoraActual->format('Y-m-d H:i:s'));
                return false;
            } else {
                // Si la hora actual es *antes o igual* a la fecha y hora de corte,
                // la vianda aún es ordenable (suponiendo que pasó las validaciones anteriores).
                log_message('debug', 'esFechaViandaAunOrdenable (MODEL): Vianda ' . $fechaViandaStr . ' es válida. Hora actual: ' . $fechaHoraActual->format('Y-m-d H:i:s') . ', Fecha de corte: ' . $fechaCorteParaEstaVianda->format('Y-m-d H:i:s'));
                return true;
            }

        } catch (Exception $e) {
            log_message('error', 'Error en esFechaViandaAunOrdenable (MODEL): ' . $e->getMessage());
            return false;
        }
    }
}