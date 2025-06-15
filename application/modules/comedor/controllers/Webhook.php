<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Webhook extends CI_Controller
{
    private function log_manual($mensaje)
    {
        // Define la ruta del archivo de log para que sea específica del webhook
        $ruta_log = APPPATH . 'logs/webhook_manual_' . date('Y-m-d') . '.log';
        $fecha = date('Y-m-d H:i:s');
        file_put_contents($ruta_log, "[$fecha] $mensaje\n", FILE_APPEND);
    }

    public function mercadopago()
    {
        $this->log_manual('Entré al webhook');

        // Carga MercadoPago SDK y configuración
        require_once FCPATH . 'vendor/autoload.php';
        $this->config->load('mercadopago');
        MercadoPago\SDK::setAccessToken($this->config->item('mp_access_token'));

        $input = file_get_contents('php://input');
        $this->log_manual('Webhook recibido (RAW): ' . $input);

        $data = json_decode($input, true);

        // Agregado un try-catch para atrapar cualquier excepción que pueda causar un 500
        try {
            if ( (isset($data['type']) && $data['type'] === 'payment') || (isset($data['action']) && $data['action'] === 'payment.created') ) {
                $payment_id = isset($data['data']['id']) ? $data['data']['id'] : null;

                // Si no se encontró el payment_id en data.id, y es un 'topic' 'payment', buscar en 'resource'
                if (!$payment_id && isset($data['topic']) && $data['topic'] === 'payment' && isset($data['resource'])) {
                    $payment_id = $data['resource'];
                }

                if (!$payment_id) {
                    $this->log_manual('ERROR: No se pudo obtener Payment ID de la notificación de pago. Data: ' . json_encode($data));
                    http_response_code(200);
                    return;
                }

                $this->log_manual('Payment ID recibido: ' . $payment_id);

                $payment = MercadoPago\Payment::find_by_id($payment_id);

                if ($payment) {
                    $this->log_manual('Estado del pago: ' . $payment->status);
                } else {
                    $this->log_manual('ERROR: No se pudo obtener el pago con ID: ' . $payment_id . ' desde la API de MP.');
                    http_response_code(200);
                    return;
                }

                if ($payment->status === 'approved') {
                    $external_reference = trim($payment->external_reference); // TRIM para asegurar la limpieza
                    $this->log_manual('External Reference (trimmed) obtenido de MP: ' . $external_reference);

                    $CI =& get_instance();
                    $CI->load->model('ticket_model');
                    $compra_pendiente = $CI->ticket_model->getCompraPendiente($external_reference);

                    $this->log_manual('Estado inicial compra pendiente (desde DB): ' . print_r($compra_pendiente, true));

                    if ($compra_pendiente && isset($compra_pendiente->id) && $compra_pendiente->procesada == 0) {
                        $this->log_manual('Iniciando procesamiento de compra pendiente. ID: ' . $compra_pendiente->id . ', External Ref: ' . $external_reference);

                        $seleccion = json_decode($compra_pendiente->datos, true);
                        if ($seleccion === null) {
                            $this->log_manual('ERROR: Error al decodificar datos JSON de la compra pendiente. Datos: ' . $compra_pendiente->datos);
                            // En este caso, no podemos procesar. Responder 200 OK para que MP no reintente.
                            http_response_code(200);
                            return;
                        }
                        $this->log_manual('JSON de datos decodificado con éxito. Ítems encontrados: ' . count($seleccion));

                        $n_compras = 0;
                        $dias = $seleccion;
                        $id_usuario = $compra_pendiente->id_usuario;
                        $monto_total_compra_pendiente = $compra_pendiente->total;

                        foreach ($dias as $index => $compra) {
                            $this->log_manual('Procesando item de compra #'.($index+1).': ' . json_encode($compra));

                            $data_compra = [
                                'fecha' => date('Y-m-d', time()),
                                'hora' => date('H:i:s', time()),
                                'dia_comprado' => $compra['dia_comprado'],
                                'id_usuario' => $id_usuario,
                                'precio' => $compra['precio'],
                                'tipo' => $compra['tipo'],
                                'turno' => $compra['turno'],
                                'menu' => $compra['menu'],
                                'transaccion_id' => -1
                            ];

                            $data_log = [
                                'fecha' => date('Y-m-d', time()),
                                'hora' => date('H:i:s', time()),
                                'dia_comprado' => $compra['dia_comprado'],
                                'id_usuario' => $id_usuario,
                                'precio' => $compra['precio'],
                                'tipo' => $compra['tipo'],
                                'turno' => $compra['turno'],
                                'menu' => $compra['menu'],
                                'transaccion_tipo' => 'Compra',
                                'transaccion_id' => -1
                            ];

                            $resultado_add_compra = $CI->ticket_model->addCompra($data_compra);
                            if ($resultado_add_compra) {
                                $this->log_manual('Item de compra #'.($index+1).' añadido con éxito. DB insert ID: ' . $CI->db->insert_id());
                                $CI->ticket_model->addLogCompra($data_log);
                                $this->log_manual('Log de item de compra #'.($index+1).' añadido.');
                                $n_compras++;
                            } else {
                                $db_error = $CI->db->error();
                                $this->log_manual('ERROR: Fallo al insertar item de compra #'.($index+1).': ' . print_r($data_compra, true) . ' DB Error: ' . $db_error['message'] . ' Code: ' . $db_error['code']);
                            }
                        }
                        $this->log_manual('Bucle de inserción de items de compra finalizado. Total de items insertados: ' . $n_compras);

                        if ($n_compras > 0) {
                            $transaction_compra = [
                                'fecha' => date('Y-m-d', time()),
                                'hora' => date('H:i:s', time()),
                                'id_usuario' => $id_usuario,
                                'transaccion' => 'Compra',
                                'monto' => -$monto_total_compra_pendiente,
                                'saldo' => null
                            ];
                            $this->log_manual('Intentando insertar transacción principal con datos: ' . json_encode($transaction_compra));
                            $id_insert = $CI->ticket_model->addTransaccion($transaction_compra);
                            if ($id_insert === 0 || $id_insert === false) { // 0 si no se insertó o false si hubo error en DB
                                $db_error = $CI->db->error();
                                $this->log_manual('CRÍTICO: Fallo al insertar transacción principal: ' . print_r($transaction_compra, true) . ' DB Error: ' . $db_error['message'] . ' Code: ' . $db_error['code']);
                                http_response_code(500);
                                return;
                            }
                            $this->log_manual('ID de transacción principal insertada: ' . $id_insert);

                            // --- ADVERTENCIA: ---
                            // --- Necesitas vincular las compras de forma más robusta a la external_reference ---
                            // --- Ya que la tabla 'compra' no tiene una external_reference ni id_compra_pendiente directa ---
                            $compras_actualizadas = 0;
                            $compras = $CI->ticket_model->getComprasByIDTransaccion(-$id_usuario); // Obtener compras con transaccion_id = -id_usuario
                            $this->log_manual('Intentando actualizar transaccion_id en ' . count($compras) . ' compras encontradas por transaccion_id = -' . $id_usuario);
                            foreach ($compras as $compra) {
                                $CI->ticket_model->updateTransactionInCompraByID($compra->id, $id_insert);
                                $compras_actualizadas += $CI->db->affected_rows(); // Suma las filas afectadas por CADA update
                            }
                            $this->log_manual('Filas actualizadas en tabla "compra": ' . $compras_actualizadas);

                            $logcompras_actualizadas = 0;
                            $logcompras = $CI->ticket_model->getLogComprasByIDTransaccion(-$id_usuario); 
                            $this->log_manual('Intentando actualizar transaccion_id en ' . count($logcompras) . ' log_compras encontradas por transaccion_id = -' . $id_usuario);
                            foreach ($logcompras as $compra) {
                                $CI->ticket_model->updateTransactionInLogCompraByID($compra->id, $id_insert);
                                $logcompras_actualizadas += $CI->db->affected_rows(); // Suma las filas afectadas por CADA update
                            }
                            $this->log_manual('Filas actualizadas en tabla "log_compras": ' . $logcompras_actualizadas);
                            // --- FIN ADVERTENCIA ---

                            // Marcar la compra pendiente como procesada
                            $rows_affected = $CI->ticket_model->setCompraPendienteProcesada($external_reference);
                            
                            if ($rows_affected > 0) {
                                $this->log_manual('ÉXITO: Compra pendiente marcada como procesada: ' . $external_reference . ' (Filas afectadas: ' . $rows_affected . ')');
                            } else {
                                $this->log_manual('ADVERTENCIA: setCompraPendienteProcesada NO actualizó ninguna fila para: ' . $external_reference . '. Esto puede significar que ya estaba procesada o que la external_reference no coincide. (Filas afectadas: ' . $rows_affected . ')');
                            }
                        } else {
                            $this->log_manual('ERROR: No se pudo registrar ninguna compra individual para el usuario ID ' . $id_usuario . ' a partir de los datos decodificados. No se creó transacción principal.');
                        
                        }
                    } else {
                        if ($compra_pendiente === null) {
                            $this->log_manual('ERROR: Compra pendiente NO encontrada para external_reference: ' . $external_reference . '. Esto podría indicar un problema de sincronización.');
                        } else {
                            $this->log_manual('ADVERTENCIA: Compra pendiente ID ' . $compra_pendiente->id . ' con External Ref: ' . $external_reference . ' ya estaba procesada (procesada=' . $compra_pendiente->procesada . '). Ignorando reintento.');
                        }
                        http_response_code(200); // Responder 200 OK si ya fue procesada o no se encontró
                        return;
                    }
                } else {
                    $this->log_manual('ADVERTENCIA: Pago no aprobado o inválido para el ID: ' . $payment_id . ' (Estado: ' . ($payment ? $payment->status : 'N/A') . '). No se procesará la compra.');
                    http_response_code(200); // Responder 200 OK aunque no esté aprobado, para que MP no reintente este pago.
                    return;
                }
            } elseif (isset($data['topic']) && $data['topic'] === 'merchant_order') {
                $this->log_manual('Notification type: merchant_order. Resource: ' . $data['resource']);
                http_response_code(200); // Siempre responder 200 para merchant_order si no hay errores en tu manejo.
                return;
            } else {
                $this->log_manual('Webhook recibido con formato desconocido o ignorado: ' . $input);
                http_response_code(200); // Responder 200 si el tipo de notificación no es el esperado
                return;
            }

        } catch (Exception $e) {
            $this->log_manual('EXCEPCIÓN EN EL WEBHOOK: ' . $e->getMessage() . ' en ' . $e->getFile() . ' línea ' . $e->getLine());
            // Si hay una excepción, es un error del servidor. Responder 500 para que MP reintente.
            http_response_code(500);
            return;
        }

        http_response_code(200);
    }
}