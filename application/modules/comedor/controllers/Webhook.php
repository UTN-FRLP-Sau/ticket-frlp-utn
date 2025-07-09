<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Webhook extends CI_Controller
{
    private function log_manual($mensaje)
    {
        // ruta del archivo de log específica del webhook
        $ruta_log = APPPATH . 'logs/webhook_manual_' . date('Y-m-d') . '.log';
        $fecha = date('Y-m-d H:i:s');
        file_put_contents($ruta_log, "[$fecha] $mensaje\n", FILE_APPEND);
    }

    public function mercadopago()
    {
        $this->log_manual('Entré al webhook');

        // Carga MercadoPago SDK y configuración
        require_once FCPATH . 'vendor/autoload.php';
        $this->config->load('ticket');

        $access_token = $this->config->item('MP_ACCESS_TOKEN');
        MercadoPago\SDK::setAccessToken($access_token);

        $input = file_get_contents('php://input');
        $this->log_manual('Webhook recibido (RAW): ' . $input);

        $data = json_decode($input, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->log_manual('ERROR JSON DECODE: Fallo al decodificar JSON. Mensaje: ' . json_last_error_msg() . '. RAW: ' . $input);
            http_response_code(400); // Bad Request
            return;
        }
        if (!is_array($data) || empty($data)) {
            $this->log_manual('ERROR JSON DATA: Datos del webhook vacíos o no válidos después de json_decode. RAW: ' . $input);
            http_response_code(400); // Bad Request
            return;
        }

        // Obtener la instancia de CodeIgniter para acceder al modelo y la base de datos
        $CI = &get_instance();
        $CI->load->model('ticket_model');

        // Inicia una transacción de base de datos para asegurar atomicidad
        $CI->db->trans_begin();

        try {
            // Verifica que la notificación es de tipo 'payment' y contiene un ID de pago
            if ((isset($data['type']) && $data['type'] == 'payment' && isset($data['data']['id']))) {
                $payment_id = $data['data']['id'];
                $this->log_manual('Webhook tipo PAYO (payment). ID de pago: ' . $payment_id);

                // Obtener la información completa del pago desde la API de Mercado Pago
                $payment_info = $CI->ticket_model->getMercadoPagoPayment($payment_id);

                if ($payment_info) {
                    $external_reference = $payment_info->external_reference;
                    $mp_status_from_mp = $payment_info->status; // Obtener el estado actual del pago de MP
                    $this->log_manual('Estado de pago de MP para ' . $payment_id . ': ' . $mp_status_from_mp . '. External Reference: ' . $external_reference);

                    // Buscar la compra pendiente en la base de datos con la external_reference
                    $compra_pendiente = $CI->ticket_model->getCompraPendiente($external_reference);

                    if ($compra_pendiente) {
                        $CI->ticket_model->updateCompraPendienteEstado($compra_pendiente->id, $mp_status_from_mp);
                        $this->log_manual('Actualizado mp_estado de compra_pendiente ' . $compra_pendiente->id . ' a: ' . $mp_status_from_mp);

                        // Lógica basada en el estado de Mercado Pago
                        if ($mp_status_from_mp == 'approved') {
                            // Solo procesar si la compra pendiente aún no ha sido marcada como procesada
                            if ($compra_pendiente->procesada == 0) {
                                $this->log_manual('PAGO APROBADO: Procesando compra pendiente ' . $compra_pendiente->id);
                                $this->processApprovedPayment($CI, $compra_pendiente, $payment_info);
                                $this->log_manual('PAGO APROBADO: Compra ' . $compra_pendiente->id . ' procesada y marcada.');
                            } else {
                                $this->log_manual('PAGO APROBADO: Compra ' . $compra_pendiente->id . ' ya estaba procesada. No se realizó ninguna acción adicional.');
                            }
                        } else if ($mp_status_from_mp == 'rejected') {
                            $this->log_manual('PAGO RECHAZADO: Notificación para compra pendiente ' . $compra_pendiente->id . '. No se realizarán acciones de compra.');
                            // agregar lógica adicional si lo consideras necesario:
                            // - Enviar un email al usuario informando del rechazo.
                            // - Registrar un log específico de rechazo.
                            // - Podrías incluso eliminar la compra_pendiente si no hay posibilidad de reintento.
                        } else if ($mp_status_from_mp == 'pending') {
                            $this->log_manual('PAGO PENDIENTE: Notificación para compra pendiente ' . $compra_pendiente->id . '. Se espera confirmación futura.');
                            // No se realiza ninguna acción de procesamiento de compra aquí.
                            // El estado en la DB ya fue actualizado a 'pending'.
                            // Si el pago se aprueba más tarde, Mercado Pago enviará otra notificación 'approved'.
                        } else {
                            $this->log_manual('ESTADO DESCONOCIDO/IGNORADO: Notificación para compra pendiente ' . $compra_pendiente->id . ' con estado: ' . $mp_status_from_mp);
                        }
                    } else {
                        $this->log_manual('ADVERTENCIA: Compra pendiente no encontrada para external_reference: ' . $external_reference . '. ID de pago: ' . $payment_id);
                    }
                } else {
                    $this->log_manual('ADVERTENCIA: No se pudo obtener la información de pago de MP para ID: ' . $payment_id);
                }
            } else {
                // Si el tipo no es 'payment' o no tiene 'data.id', podría ser una notificación de 'merchant_order' o 'payment' con otro formato..
                $this->log_manual("Webhook recibido con formato desconocido o tipo no 'payment' (o sin data.id): " . $input);
            }

            // Confirmar que la transacción de DB se realizó con éxito y hacer commit
            if ($CI->db->trans_status() === FALSE) {
                // Esto solo se ejecutaría si hay un error en la base de datos que no fue capturado por una excepción
                $CI->db->trans_rollback();
                $this->log_manual('ERROR: Transacción de DB fallida al final del bloque try (sin excepción lanzada). Haciendo rollback.');
                http_response_code(500); // Esto indicaría un error interno no manejado.
                return;
            } else {
                $CI->db->trans_commit();
                $this->log_manual('TRANSACCIÓN COMMIT: Proceso de webhook completado exitosamente.');
                http_response_code(200); // Responde 200 OK a Mercado Pago
                return;
            }

        } catch (Exception $e) {
            $CI->db->trans_rollback(); // rollback si ocurre una excepción
            $this->log_manual('EXCEPCIÓN EN EL WEBHOOK: ' . $e->getMessage() . ' en ' . $e->getFile() . ' línea ' . $e->getLine() . '. Realizando rollback de DB.');
            // Si hay una excepción, es un error del servidor. Responde 500 para que MP reintente.
            http_response_code(500);
            return;
        }
    }

    /**
     * Encapsula la lógica de procesamiento para pagos aprobados.
     * @param object $CI Instancia del controlador de CodeIgniter
     * @param object $compra_pendiente Objeto de la compra pendiente de la DB
     * @param object $payment_info Objeto de información del pago de Mercado Pago
     */
    private function processApprovedPayment($CI, $compra_pendiente, $payment_info) {
        log_message('debug', 'processApprovedPayment: Iniciando procesamiento de pago aprobado desde MP.');

        $id_usuario = $compra_pendiente->id_usuario;
        $total_compra = (float)$compra_pendiente->total;
        $external_reference = $compra_pendiente->external_reference;
        $payment_id = $payment_info->id; // ID de pago de Mercado Pago
        
        // Obtener el saldo del usuario
        $saldo_inicial_usuario = $CI->ticket_model->getSaldoByIDUser($id_usuario);
        log_message('debug', 'processApprovedPayment: Saldo actual del usuario ' . $id_usuario . ' (antes de deducción): ' . $saldo_inicial_usuario);

        $monto_pagado_mp = 0;
        if (isset($payment_info->transaction_amount)) {
            $monto_pagado_mp = (float)$payment_info->transaction_amount;
        } elseif (isset($payment_info->total_paid_amount)) {
            $monto_pagado_mp = (float)$payment_info->total_paid_amount;
        }
        log_message('debug', 'processApprovedPayment: Monto pagado por MP: ' . $monto_pagado_mp);

        $saldo_a_deducir_en_webhook = $total_compra - $monto_pagado_mp;

        $saldo_a_deducir_en_webhook = max(0, min($saldo_a_deducir_en_webhook, $saldo_inicial_usuario));
        log_message('debug', 'processApprovedPayment: Saldo a deducir calculado: ' . $saldo_a_deducir_en_webhook);

        // Calcular el saldo final después de la deducción
        $saldo_final_despues_deduccion = $saldo_inicial_usuario - $saldo_a_deducir_en_webhook;
        log_message('debug', 'processApprovedPayment: Saldo final después de la deducción calculada: ' . $saldo_final_despues_deduccion);


        if ($saldo_a_deducir_en_webhook > 0) {
            // Usar updateSaldoByIDUser, pasando el saldo final resultante
            if (!$CI->ticket_model->updateSaldoByIDUser($id_usuario, $saldo_final_despues_deduccion)) {
                log_message('error', 'processApprovedPayment: Fallo al deducir saldo parcial (updateSaldoByIDUser) para usuario ' . $id_usuario . ' a saldo: ' . $saldo_final_despues_deduccion);
                throw new Exception('Fallo al actualizar el saldo del usuario en el webhook.');
            }
            log_message('info', 'processApprovedPayment: Saldo de usuario ' . $id_usuario . ' actualizado a: ' . $saldo_final_despues_deduccion . ' (por pago MP, se dedujo ' . $saldo_a_deducir_en_webhook . ').');
        } else {
            log_message('info', 'processApprovedPayment: No se descontó saldo en webhook. Saldo a deducir calculado: ' . $saldo_a_deducir_en_webhook . '. Saldo inicial: ' . $saldo_inicial_usuario);
        }

        // Obtener el saldo final del usuario de la DB
        $saldo_para_registro_transaccion = $CI->ticket_model->getSaldoByIDUser($id_usuario);
        log_message('debug', 'processApprovedPayment: Saldo final del usuario de la DB para registro: ' . $saldo_para_registro_transaccion);

        try {
            log_message('debug', 'processApprovedPayment: Intentando obtener viandas para compra pendiente ' . $compra_pendiente->id);
            // Obtener los ítems de vianda asociados a esta compra pendiente
            $viandas_en_compra = $CI->ticket_model->getViandasCompraPendiente($compra_pendiente->id);
            log_message('debug', 'processApprovedPayment: viandas_en_compra: ' . (empty($viandas_en_compra) ? 'VACIO' : json_encode($viandas_en_compra)));


            if (!$viandas_en_compra) {
                log_message('error', 'processApprovedPayment: No se encontraron viandas para la compra pendiente ' . $compra_pendiente->id . '. No se procederá con la inserción de compras/logs.');
                throw new Exception('No se encontraron viandas para procesar.');
            }

            foreach ($viandas_en_compra as $vianda_item) {
                log_message('debug', 'processApprovedPayment: Procesando item de vianda: ' . json_encode($vianda_item));
                // Inserta cada vianda en la tabla 'compra'
                $data_compra = [
                    'fecha' => date('Y-m-d'),
                    'hora' => date('H:i:s'),
                    'dia_comprado' => $vianda_item['dia_comprado'],
                    'id_usuario' => $id_usuario,
                    'precio' => $vianda_item['precio'],
                    'tipo' => $vianda_item['tipo'],
                    'turno' => $vianda_item['turno'],
                    'menu' => $vianda_item['menu'],
                    'external_reference' => $external_reference,
                    'transaccion_id' => $payment_id, // ID de pago de Mercado Pago
                ];

                $id_compra_item = $CI->ticket_model->addCompra($data_compra);

                if ($id_compra_item === false) {
                    log_message('error', 'processApprovedPayment: Falló la inserción de un item de compra. Datos: ' . json_encode($data_compra));
                    throw new Exception('No se pudo insertar un item de compra en la base de datos.');
                }
                log_message('debug', 'processApprovedPayment: Item de compra insertado, ID: ' . $id_compra_item);


                // Registrar cada vianda en el log de compras
                $log_data = [
                    'id_usuario'       => $id_usuario,
                    'fecha'            => date('Y-m-d'),
                    'hora'             => date('H:i:s'),
                    'dia_comprado'     => $vianda_item['dia_comprado'],
                    'precio'           => $vianda_item['precio'],
                    'tipo'             => $vianda_item['tipo'],
                    'turno'            => $vianda_item['turno'],
                    'menu'             => $vianda_item['menu'],
                    'external_reference' => $external_reference,
                    'transaccion_tipo' => 'Compra por Mercado Pago',
                    'transaccion_id'   => $payment_id
                ];
                $CI->ticket_model->addLogCompra($log_data);
                log_message('debug', 'processApprovedPayment: Log de compra insertado para item: ' . $vianda_item['menu']);
            }

            // Registrar la transacción principal en la tabla 'transacciones'
            $data_transaccion = [
                'id_usuario' => $id_usuario,
                'monto' => $total_compra, // El monto total de la compra
                'fecha' => date('Y-m-d'),
                'hora' => date('H:i:s'),
                'transaccion' => 'Compra por Mercado Pago',
                'saldo' => $saldo_para_registro_transaccion,
                'external_reference' => $external_reference,
            ];
            $id_transaccion = $CI->ticket_model->addTransaccion($data_transaccion);
           

            if ($id_transaccion === false) {
                log_message('error', 'processApprovedPayment: Falló la inserción de la transacción principal. Datos: ' . json_encode($data_transaccion));
                throw new Exception('No se pudo insertar la transacción principal.');
            }
            log_message('debug', 'processApprovedPayment: Transacción principal insertada, ID: ' . $id_transaccion);


            // Marca la compra pendiente como procesada
            if (!$CI->ticket_model->setCompraPendienteProcesada($external_reference)) {
                 log_message('error', 'processApprovedPayment: Fallo al marcar compra pendiente ' . $external_reference . ' como procesada.');
                 throw new Exception('Fallo al marcar la compra pendiente como procesada.');
            }
            log_message('debug', 'processApprovedPayment: Compra pendiente ' . $external_reference . ' marcada como procesada.');


            log_message('info', 'processApprovedPayment: Compra procesada exitosamente con MP para ' . $external_reference);

        } catch (Exception $e) {
            log_message('error', 'processApprovedPayment: EXCEPCIÓN AL PROCESAR VIANDAS/COMPRA: ' . $e->getMessage() . ' en ' . $e->getFile() . ' línea ' . $e->getLine());
            // Relanza la excepción para que mercadopago() la capture y haga rollback
            throw $e;
        }
    }

}
