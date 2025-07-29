<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Webhook extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ticket_model');
    }
    private function log_manual($mensaje)
    {
        // ruta del archivo de log específica del webhook
        $ruta_log = APPPATH . 'logs/webhook_manual_' . date('Y-m-d') . '.log';
        $fecha = date('Y-m-d H:i:s');
        file_put_contents($ruta_log, "[$fecha] $mensaje\n", FILE_APPEND);
    }

private function mapMercadoPagoStatusDetail($mp_code)
{
    switch ($mp_code) {
        // --- Pagos Acreditados / Aprobados ---
        case 'accredited':
            return '¡Listo! Se acreditó tu pago.';
        case 'partially_refunded': // Del 'approvedpartially_refunded'
            return 'El pago se realizó con al menos un reembolso parcial.';

        // --- Pagos Autorizados (pendientes de captura) ---
        case 'pending_capture': // Del 'authorizedpending_capture'
            return 'El pago fue autorizado y está a la espera de ser capturado.';

        // --- Pagos En Proceso ---
        case 'offline_process': // Del 'in_processoffline_process'
            return 'Por una falta de procesamiento online, el pago está siendo procesado de manera offline.';
        case 'pending_contingency': // Del 'in_processpending_contingency'
            return 'Estamos procesando tu pago. No te preocupes, en menos de 2 días hábiles te avisaremos por e-mail si se acreditó.';
        case 'pending_review_manual': // Del 'in_processpending_review_manual'
            return 'Estamos procesando tu pago. No te preocupes, en menos de 2 días hábiles te avisaremos por e-mail si se acreditó o si necesitamos más información.';

        // --- Pagos Pendientes ---
        case 'pending_waiting_transfer': // Del 'pendingpending_waiting_transfer'
            return 'Para los casos de transferencia bancaria, el pago está esperando que el usuario termine el proceso en su banco.';
        case 'pending_waiting_payment': // Del 'pendingpending_waiting_payment'
            return 'Para los casos de pagos offline, el pago queda pendiente hasta que el usuario lo realice.';
        case 'pending_challenge': // Del 'pendingpending_challenge'
            return 'Para los casos de pagos con tarjeta de crédito, hay una confirmación pendiente a causa de un challenge.';

        // --- Pagos Rechazados ---
        // Errores de Banco / Generales
        case 'bank_error': // Del 'rejectedbank_error'
            return 'El pago fue rechazado por un error con el banco.';
        case 'rejected_by_bank': // Del 'rejectedrejected_by_bank'
            return 'Operación rechazada por el banco.';
        case 'rejected_by_regulations': // Del 'rejectedrejected_by_regulations'
            return 'Pago rechazado por regulaciones.';
        case 'insufficient_amount': // Del 'rejectedinsufficient_amount' (general, no solo tarjeta)
            return 'Pago rechazado por montos insuficientes.';
        case 'rejected_insufficient_data': // Del 'rejectedrejected_insufficient_data'
            return 'El pago fue rechazado debido a falta de toda la información obligatoria requerida.';
        case 'rejected_other_reason': // Del 'rejectedcc_rejected_other_reason' o 'rejected_other_reason'
            return 'El pago fue rechazado por un motivo desconocido. Por favor, inténtalo de nuevo o con otro medio de pago.';
        
        // Rechazos Específicos de Tarjeta
        case 'cc_rejected_3ds_mandatory': // Del 'rejectedcc_rejected_3ds_mandatory'
            return 'Pago rechazado por no tener el challenge 3DS cuando es obligatorio.';
        case 'cc_rejected_bad_filled_card_number': // Del 'rejectedcc_rejected_bad_filled_card_number'
            return 'Revisa el número de tarjeta.';
        case 'cc_rejected_bad_filled_date': // Del 'rejectedcc_rejected_bad_filled_date'
            return 'Revisa la fecha de vencimiento.';
        case 'cc_rejected_bad_filled_other': // Del 'rejectedcc_rejected_bad_filled_other'
            return 'Revisa los datos.';
        case 'cc_rejected_bad_filled_security_code': // Del 'rejectedcc_rejected_bad_filled_security_code'
            return 'Revisa el código de seguridad de la tarjeta.';
        case 'cc_rejected_blacklist': // Del 'rejectedcc_rejected_blacklist'
            return 'No pudimos procesar tu pago.';
        case 'cc_rejected_call_for_authorize': // Del 'rejectedcc_rejected_call_for_authorize'
            return 'Debes autorizar ante el medio de pago el pago de este monto.'; // Adapté el mensaje
        case 'cc_rejected_card_disabled': // Del 'rejectedcc_rejected_card_disabled'
            return 'Llama al emisor de tu tarjeta para activarla o usa otro medio de pago. El teléfono está al dorso de tu tarjeta.';
        case 'cc_rejected_card_error': // Del 'rejectedcc_rejected_card_error'
            return 'No pudimos procesar tu pago.';
        case 'cc_rejected_duplicated_payment': // Del 'rejectedcc_rejected_duplicated_payment'
            return 'Ya hiciste un pago por ese valor. Si necesitas volver a pagar usa otra tarjeta u otro medio de pago.';
        case 'cc_rejected_high_risk': // Del 'rejectedcc_rejected_high_risk'
            return 'Tu pago fue rechazado. Elige otro de los medios de pago, te recomendamos con medios en efectivo.';
        case 'cc_rejected_insufficient_amount': // Del 'rejectedcc_rejected_insufficient_amount'
            return 'Tu tarjeta no tiene fondos suficientes.'; // Adapté el mensaje
        case 'cc_rejected_invalid_installments': // Del 'rejectedcc_rejected_invalid_installments'
            return 'El medio de pago no procesa pagos en cuotas/meses.'; // Adapté el mensaje
        case 'cc_rejected_max_attempts': // Del 'rejectedcc_rejected_max_attempts'
            return 'Llegaste al límite de intentos permitidos. Elige otra tarjeta u otro medio de pago.';
        case 'cc_amount_rate_limit_exceeded': // Del 'rejectedcc_amount_rate_limit_exceeded'
            return 'El pago fue rechazado porque superó el límite (Capacidad Máxima Permitida) del medio de pago.';

        // Expiración de Pagos en Efectivo/Cajero
        case 'expired_by_date_cutoff':
            return 'El plazo para realizar el pago en efectivo ha expirado. Debes generar una nueva orden de compra.';

        // Revisión Manual (ya lo tenías, pero el original era rejected)
        case 'rejected_by_manual_review':
            return 'Tu pago está en revisión. Te avisaremos cuando tengamos una resolución.';


        // Valor por defecto si el código no está mapeado
        default:
            return ' Desconocido' . $mp_code;
    }
}

    public function mercadopago()
    {
        $this->log_manual('Entré al webhook');

        // Cargo MercadoPago SDK y configuración
        require_once FCPATH . 'vendor/autoload.php';
        $this->config->load('ticket');

        $access_token = $this->config->item('MP_ACCESS_TOKEN');
        $secret_key = $this->config->item('MP_WEBHOOK_SECRET'); // La clave secreta para validar la firma
        MercadoPago\SDK::setAccessToken($access_token);

        $input = file_get_contents('php://input');
        $this->log_manual('Webhook recibido (RAW): ' . $input);

        $data = json_decode($input, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->log_manual('ERROR JSON DECODE: ' . json_last_error_msg() . '. RAW: ' . $input);
            http_response_code(400);
            return;
        }

        if (!is_array($data) || empty($data)) {
            $this->log_manual('ERROR JSON DATA: Datos vacíos o inválidos. RAW: ' . $input);
            http_response_code(400);
            return;
        }

        // --- INICIO: VALIDACION DE FIRMA ---

        // Extraer headers importantes para validación
        $xSignature = $_SERVER['HTTP_X_SIGNATURE'] ?? '';
        $xRequestId = $_SERVER['HTTP_X_REQUEST_ID'] ?? '';
        $notification_topic = $data['topic'] ?? ''; // Obtener el topic para decidir cómo construir el manifiesto

        if (empty($xSignature)) {
            $this->log_manual('ERROR: Header x-signature no encontrado.');
            http_response_code(401); // 401 Unauthorized
            return;
        }

        // Extraer ts y v1 del header x-signature
        $ts = null;
        $v1 = null;
        $parts = explode(',', $xSignature);
        foreach ($parts as $part) {
            $kv = explode('=', $part, 2);
            if (count($kv) == 2) {
                $key = trim($kv[0]);
                $value = trim($kv[1]);
                if ($key === 'ts') $ts = $value;
                if ($key === 'v1') $v1 = $value;
            }
        }

        if (!$ts || !$v1) {
            $this->log_manual('ERROR: ts o v1 no encontrados en x-signature.');
            http_response_code(401); // 401 Unauthorized
            return;
        }

        //  Construir el manifest de forma adaptativa y con JSON normalizado ---
        $manifest = '';

        // Si la notificación tiene 'data' y 'data.id', es probablemente un evento detallado (e.g., payment.created)
        if (isset($data['data']['id']) && !empty($data['data']['id'])) {
            $dataID_for_manifest = $data['data']['id'];
            $manifest = "id:$dataID_for_manifest;";
            if (!empty($xRequestId)) {
                $manifest .= "request-id:$xRequestId;";
            }
            $manifest .= "ts:$ts;";
        } else {
            // Para notificaciones simples (e.g., {"resource":"ID","topic":"payment"} o merchant_order)
            // el manifiesto es 'ts:{timestamp};' + el cuerpo RAW NORMALIZADO de la solicitud.
            $normalized_json_body = json_encode($data); 

            if ($normalized_json_body === false) {
                 $this->log_manual('ERROR: Fallo al normalizar el JSON para la firma. JSON: ' . $input);
                 http_response_code(400);
                 return;
            }
            $manifest = "ts:$ts;" . $normalized_json_body;
        }

        // Calcular HMAC SHA256 usando la clave secreta
        $calculatedSignature = hash_hmac('sha256', $manifest, $secret_key);

        // Comparar firmas
        if (!hash_equals($calculatedSignature, $v1)) {
            $this->log_manual("ERROR: Validación HMAC fallida. Calculado: $calculatedSignature, recibido: $v1. Manifiesto usado: '$manifest'");
            http_response_code(401); // 401 Unauthorized
            return; // Detener ejecución aquí
        }

        $this->log_manual('Validación HMAC exitosa.');
        // --- FIN: VALIDACION DE FIRMA ---



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
        $CI->load->model('general_model', 'generalticket');

        // Inicia una transacción de base de datos para asegurar atomicidad
        $CI->db->trans_begin();

        try {
            // Verifica que la notificación es de tipo 'payment' y contiene un ID de pago
            if ((isset($data['type']) && $data['type'] == 'payment' && isset($data['data']['id']))) {
                $payment_id = $data['data']['id'];
                $this->log_manual('Webhook tipo PAYO (payment). ID de pago: ' . $payment_id);

                // Obtener la información completa del pago desde la API de Mercado Pago
                $payment_info = $this->ticket_model->getMercadoPagoPayment($payment_id);

                if ($payment_info) {
                    $external_reference = $payment_info->external_reference;
                    $mp_status_from_mp = $payment_info->status;
                    $mp_status_detail = isset($payment_info->status_detail) ? $payment_info->status_detail : 'N/A';

                    // FORZAR REJECTED (DEBUG) ---
                    // Activa esta línea para forzar el estado a 'rejected'
                    // sirve para probar el envío de correos de rechazo.
                    // $mp_status_from_mp = 'rejected';
                    // $this->log_manual('DEBUG: Estado de MP forzado a RECHAZADO para pruebas.');
                    // --- FIN CÓDIGO PARA FORZAR REJECTED (DEBUG) ---

                    $this->log_manual('Estado de pago de MP para ' . $payment_id . ': ' . $mp_status_from_mp . ' (Detalle: ' . $mp_status_detail . '). External Reference: ' . $external_reference);

                    $compra_pendiente = $this->ticket_model->getCompraPendiente($external_reference);

                    if ($compra_pendiente) {
                        $this->ticket_model->updateCompraPendienteEstado($compra_pendiente->id, $mp_status_from_mp, $mp_status_detail);
                        $this->log_manual('Actualizado mp_estado de compra_pendiente ' . $compra_pendiente->id . ' a: ' . $mp_status_from_mp . ' (Detalle: ' . $mp_status_detail . ').');

                        switch ($mp_status_from_mp) {
                            case 'approved':
                                if ($compra_pendiente->procesada == 0) {
                                    $this->log_manual('PAGO APROBADO: Procesando compra pendiente ' . $compra_pendiente->id);
                                    $this->processApprovedPayment($CI, $compra_pendiente, $payment_info);
                                    $this->log_manual('PAGO APROBADO: Compra ' . $compra_pendiente->id . ' procesada y marcada.');
                                } else {
                                    $this->log_manual('PAGO APROBADO: Compra ' . $compra_pendiente->id . ' ya estaba procesada. No se realizó ninguna acción adicional.');
                                }
                                break;

                            case 'rejected':
                            case 'cancelled':
                            case 'expired_by_date_cutoff':
                                $this->log_manual('PAGO RECHAZADO/CANCELADO: Notificación para compra pendiente ' . $compra_pendiente->id . ' con estado: ' . $mp_status_from_mp . ' (Detalle: ' . $mp_status_detail . '). No se realizarán acciones de compra.');
                                // $payment_info->status_detail = 'rejected_by_bank'; ACTIVAR PARA DEBUG DE CORREO DE RECHAZO CON MOTIVO DE RECHAZO PERSONALIZAD
                                $this->processRejectedPayment($CI, $compra_pendiente, $payment_info);
                                break;

                            case 'pending':
                            case 'in_process':
                                $this->log_manual('PAGO PENDIENTE/EN PROCESO: Notificación para compra pendiente ' . $compra_pendiente->id . '. Estado: ' . $mp_status_from_mp . ' (Detalle: ' . $mp_status_detail . '). Se espera confirmación futura.');
                                if ($this->ticket_model->updateCompraPendienteEstado($compra_pendiente->id, 'pending')) {
                                    $this->log_manual('PAGO PENDIENTE/EN PROCESO: Estado de compra pendiente ' . $compra_pendiente->id . ' actualizado a "pending" correctamente.');
                                } else {
                                    $this->log_manual('PAGO PENDIENTE/EN PROCESO: Fallo al actualizar el estado de compra pendiente ' . $compra_pendiente->id . ' a "pending".');
                                }
                                $this->session->unset_userdata('external_reference');
                                $this->session->unset_userdata('error_compra'); 
                                break;

                            default:
                                $this->log_manual('ESTADO DESCONOCIDO/NO MANEJADO (para acciones): Notificación para compra pendiente ' . $compra_pendiente->id . ' con estado: ' . $mp_status_from_mp . ' (Detalle: ' . $mp_status_detail . ').');
                                break;
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
        
        // Obtengo el saldo del usuario
        $saldo_inicial_usuario = $this->ticket_model->getSaldoByIDUser($id_usuario);
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

        // Calculo el saldo final después de la deducción
        $saldo_final_despues_deduccion = $saldo_inicial_usuario - $saldo_a_deducir_en_webhook;
        log_message('debug', 'processApprovedPayment: Saldo final después de la deducción calculada: ' . $saldo_final_despues_deduccion);

        if ($saldo_a_deducir_en_webhook > 0) {
            // Uso updateSaldoByIDUser, pasando el saldo final resultante
            if (!$this->ticket_model->updateSaldoByIDUser($id_usuario, $saldo_final_despues_deduccion)) {
                log_message('error', 'processApprovedPayment: Fallo al deducir saldo parcial (updateSaldoByIDUser) para usuario ' . $id_usuario . ' a saldo: ' . $saldo_final_despues_deduccion);
                throw new Exception('Fallo al actualizar el saldo del usuario en el webhook.');
            }
            log_message('info', 'processApprovedPayment: Saldo de usuario ' . $id_usuario . ' actualizado a: ' . $saldo_final_despues_deduccion . ' (por pago MP, se dedujo ' . $saldo_a_deducir_en_webhook . ').');
        } else {
            log_message('info', 'processApprovedPayment: No se descontó saldo en webhook. Saldo a deducir calculado: ' . $saldo_a_deducir_en_webhook . '. Saldo inicial: ' . $saldo_inicial_usuario);
        }

        // Obtengo el saldo final del usuario de la DB
        $saldo_para_registro_transaccion = $this->ticket_model->getSaldoByIDUser($id_usuario);
        log_message('debug', 'processApprovedPayment: Saldo final del usuario de la DB para registro: ' . $saldo_para_registro_transaccion);

        $data_transaccion = [
            'id_usuario' => $id_usuario,
            'monto' => -1 * $total_compra, // El monto total de la compra (negativo porque es una deducción)
            'fecha' => date('Y-m-d'),
            'hora' => date('H:i:s'),
            'transaccion' => 'Compra por Mercado Pago',
            'saldo' => $saldo_para_registro_transaccion,
            'external_reference' => $external_reference,
        ];
        $id_transaccion = $this->ticket_model->addTransaccion($data_transaccion);
        
        if ($id_transaccion === false) {
            log_message('error', 'processApprovedPayment: Falló la inserción de la transacción principal. Datos: ' . json_encode($data_transaccion));
            throw new Exception('No se pudo insertar la transacción principal.');
        }
        log_message('debug', 'processApprovedPayment: Transacción principal insertada, ID: ' . $id_transaccion);

        // Correo de vendedor MP
        $email_vendedor_mp = 'mercado@pago';

        // Obtener el ID del vendedor MP usando el correo
        $id_vendedor_mp = $this->ticket_model->getVendedorIdByEmail($email_vendedor_mp);

        // Verifica si se encontró el ID antes de usarlo
        if ($id_vendedor_mp !== null) {
            // Inserto registro un registro en log_carga con el monto total acreditado en la cuenta de MP
            $data_log_carga = [
                'fecha'         => date('Y-m-d'),
                'hora'          => date('H:i:s'),
                'id_usuario'    => $id_usuario,
                'monto'         => $monto_pagado_mp,
                'id_vendedor'   => $id_vendedor_mp,
                'formato'       => 'MP',
                'transaccion_id'=> $id_transaccion,
            ];

            if (!$this->ticket_model->addLogCarga($data_log_carga)) {
                log_message('error', 'processApprovedPayment: Falló la inserción en log_carga para la compra MP: ' . json_encode($data_log_carga));
                throw new Exception('No se pudo insertar el registro en log_carga para la compra MP.');
            }
            log_message('debug', 'processApprovedPayment: Registro en log_carga insertado para el pago de Mercado Pago.');

        } else {
            // caso donde no se encuentra el ID del vendedor MP por su correo
            log_message('error', 'processApprovedPayment: No se encontró el ID para el vendedor MP con correo: ' . $email_vendedor_mp);
            throw new Exception('No se pudo obtener el ID del vendedor MP para log_carga.');
        }


        try {
            log_message('debug', 'processApprovedPayment: Intentando obtener viandas para compra pendiente ' . $compra_pendiente->id);
            // Obtengo los ítems de vianda asociados a esta compra pendiente
            $viandas_en_compra = $this->ticket_model->getViandasCompraPendiente($compra_pendiente->id);
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
                    'transaccion_id' => $id_transaccion,
                ];

                $id_compra_item = $this->ticket_model->addCompra($data_compra);

                if ($id_compra_item === false) {
                    log_message('error', 'processApprovedPayment: Falló la inserción de un item de compra. Datos: ' . json_encode($data_compra));
                    throw new Exception('No se pudo insertar un item de compra en la base de datos.');
                }
                log_message('debug', 'processApprovedPayment: Item de compra insertado, ID: ' . $id_compra_item);

                // Registrar cada vianda en el log de compras
                $log_data = [
                    'id_usuario'         => $id_usuario,
                    'fecha'              => date('Y-m-d'),
                    'hora'               => date('H:i:s'),
                    'dia_comprado'       => $vianda_item['dia_comprado'],
                    'precio'             => $vianda_item['precio'],
                    'tipo'               => $vianda_item['tipo'],
                    'turno'              => $vianda_item['turno'],
                    'menu'               => $vianda_item['menu'],
                    'external_reference' => $external_reference,
                    'transaccion_tipo'   => 'Compra por Mercado Pago',
                    'transaccion_id'     => $id_transaccion
                ];
                $this->ticket_model->addLogCompra($log_data);
                log_message('debug', 'processApprovedPayment: Log de compra insertado para item: ' . $vianda_item['menu']);
            }

            // Marca la compra pendiente como procesada
            if (!$this->ticket_model->setCompraPendienteProcesada($external_reference)) {
                log_message('error', 'processApprovedPayment: Fallo al marcar compra pendiente ' . $external_reference . ' como procesada.');
                throw new Exception('Fallo al marcar la compra pendiente como procesada.');
            }
            log_message('debug', 'processApprovedPayment: Compra pendiente ' . $external_reference . ' marcada como procesada.');

            log_message('info', 'processApprovedPayment: Compra procesada exitosamente con MP para ' . $external_reference);
            log_message('debug', 'WEBHOOK DEBUG: Valor de $id_usuario ANTES de obtener usuario: ' . $id_usuario);
            log_message('debug', 'WEBHOOK DEBUG: Valor de $id_transaccion ANTES de obtener compras para recibo: ' . $id_transaccion);

            if (!$this->ticket_model->deleteCompraPendiente($compra_pendiente->id)) {
                log_message('error', 'processApprovedPayment: Fallo al eliminar el registro de compra pendiente ' . $compra_pendiente->id . '.');
            }
            log_message('debug', 'processApprovedPayment: Registro de compra pendiente ' . $compra_pendiente->id . ' eliminado.');


            // --- Lógica de envío de email para compra exitosa ---
            $usuario = $this->ticket_model->getUserById($id_usuario); 
            $compras_para_recibo = $this->ticket_model->getlogComprasByIDTransaccion($id_transaccion); 
            log_message('debug', 'WEBHOOK DEBUG: Resultado de $usuario: ' . ($usuario ? 'Objeto Usuario' : 'NULL/FALSE'));
            log_message('debug', 'WEBHOOK DEBUG: Resultado de $compras_para_recibo: ' . (is_array($compras_para_recibo) ? json_encode($compras_para_recibo) : 'NULL/FALSE/NO ARRAY'));
            

            if ($usuario && $compras_para_recibo) {
               
                log_message('debug', 'WEBHOOK: Intentando enviar email de compra exitosa.');

                $costoVianda = $this->ticket_model->getCostoById($usuario->id_precio);
                $dataRecibo['compras'] = $compras_para_recibo;
                $dataRecibo['total'] = $costoVianda * count($compras_para_recibo);
                $dataRecibo['fechaHoy'] = date('d/m/Y', time());
                $dataRecibo['horaAhora'] = date('H:i:s', time());
                $dataRecibo['recivoNumero'] = $external_reference; 

                $subject = "¡Recibo de compra de comedor! - Compra #" . $external_reference;
                
                $message = $CI->load->view('general/correos/recibo_compra', $dataRecibo, true);

                if ($CI->generalticket->smtpSendEmail($usuario->mail, $subject, $message)) {

                    log_message('debug', 'WEBHOOK: Email de compra exitosa enviado a ' . $usuario->mail . ' para ' . $external_reference . '.');
                } else {
                    log_message('error', 'WEBHOOK: Fallo al enviar email de compra exitosa al usuario ' . $usuario->mail . ' para external_reference ' . $external_reference);
                }
            } else {
                log_message('error', 'WEBHOOK: Condición ($usuario && $compras_para_recibo) es FALSE. No se pudo enviar el correo de confirmación de compra exitosa para ' . $external_reference . '.');
            }
            // --- Fin de lógica de envío de email ---

        } catch (Exception $e) {
            log_message('error', 'processApprovedPayment: EXCEPCIÓN AL PROCESAR VIANDAS/COMPRA: ' . $e->getMessage() . ' en ' . $e->getFile() . ' línea ' . $e->getLine());
            // Relanza la excepción para que mercadopago() la capture y haga rollback
            throw $e;
        }
    }

    /**
     * Procesa un pago rechazado, incluyendo el envío de un correo electrónico al usuario.
     * @param CI_Controller $CI Instancia de CodeIgniter.
     * @param object $compra_pendiente Objeto de la compra pendiente.
     * @param object $payment_info Objeto con la información del pago de Mercado Pago.
     */
    private function processRejectedPayment($CI, $compra_pendiente, $payment_info)
    {
        $external_reference = $compra_pendiente->external_reference;
        $mp_status_detail = $payment_info->status_detail;

        // Marca la compra pendiente como procesada
        if (!$this->ticket_model->setCompraPendienteProcesada($external_reference)) {
            log_message('error', 'PAGO RECHAZADO: Fallo al marcar compra pendiente ' . $external_reference . ' como procesada.');
            throw new Exception('Fallo al marcar la compra pendiente como procesada.');
        }
        log_message('debug', 'PAGO RECHAZADO: Compra pendiente ' . $external_reference . ' marcada como procesada.');
        $this->log_manual('PROCESANDO RECHAZO: external_reference: ' . $external_reference . ', status_detail: ' . $mp_status_detail);

        $this->log_manual('Procediendo a enviar email.');

        $usuario = $this->ticket_model->getUserById($compra_pendiente->id_usuario);

        if ($usuario && $usuario->mail) {
            // Obtengo los ítems de vianda asociados a esta compra pendiente
            $viandas_rechazadas = $this->ticket_model->getViandasCompraPendiente($compra_pendiente->id);
            log_message('debug', 'PAGO RECHAZADO: Viandas asociadas a la compra rechazada: ' . (empty($viandas_rechazadas) ? 'VACIO' : json_encode($viandas_rechazadas)));

            $user_friendly_status_detail = $this->mapMercadoPagoStatusDetail($mp_status_detail);
            log_message('debug', 'WEBHOOK: Motivo de rechazo mapeado: ' . $mp_status_detail . ' -> ' . $user_friendly_status_detail);


            $subject = "¡Pago Rechazado! - Tu compra #" . $external_reference;
            
            $dataEmail = [
                'external_reference' => $external_reference,
                'status_detail' => $user_friendly_status_detail,
                'user_name' => $usuario->nombre . ' ' . $usuario->apellido,
                'viandas' => $viandas_rechazadas,
                'fechaHoy' => date('d/m/Y', time()),
                'horaAhora' => date('H:i:s', time()),
            ];
            
            $message = $CI->load->view('general/correos/pago_rechazado', $dataEmail, true);

            if ($CI->generalticket->smtpSendEmail($usuario->mail, $subject, $message)) {
                $this->log_manual('EMAIL PAGO RECHAZADO: Correo enviado a ' . $usuario->mail . ' para ' . $external_reference);
            } else {
                $this->log_manual('ERROR ENVIO EMAIL PAGO RECHAZADO: Fallo al enviar correo a ' . $usuario->mail . ' para ' . $external_reference);
            }
        } else {
            $this->log_manual('ADVERTENCIA PROCESANDO RECHAZO: Usuario o email no encontrado para external_reference ' . $external_reference . '. No se pudo enviar correo de rechazo.');
        }

        // Elimina la compra pendiente de la base de datos
        if (!$this->ticket_model->deleteCompraPendiente($compra_pendiente->id)) {
            log_message('error', 'processRejectedPayment: Fallo al eliminar el registro de compra pendiente ' . $compra_pendiente->id . '.');
        }
        log_message('debug', 'processRejectedPayment: Registro de compra pendiente ' . $compra_pendiente->id . ' eliminado.');
    }
}
