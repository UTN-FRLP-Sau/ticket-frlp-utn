<?php
defined('BASEPATH') or exit('No direct script access allowed');

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

        $CI = &get_instance();
        $CI->load->model('ticket_model');

        $CI->db->trans_begin();

        // try-catch para atrapar cualquier excepción que pueda causar un 500
        try {
            if ((isset($data['type']) && $data['type'] === 'payment') || (isset($data['action']) && $data['action'] === 'payment.created')) {
                $payment_id = isset($data['data']['id']) ? $data['data']['id'] : null;

                // Si no se encontró el payment_id en data.id, y es un 'topic' 'payment', buscar en 'resource'
                // y limpiar la URL si es necesario
                if (!$payment_id && isset($data['topic']) && $data['topic'] === 'payment' && isset($data['resource'])) {
                    $payment_id = str_replace('https://api.mercadopago.com/v1/payments/', '', $data['resource']);
                    $this->log_manual('Payment ID extraído de resource (posible URL): ' . $payment_id);
                }

                if (!$payment_id) {
                    $this->log_manual('ERROR: No se pudo obtener Payment ID de la notificación de pago. Data: ' . json_encode($data));
                    $CI->db->trans_rollback();
                    http_response_code(200);
                    return;
                }

                $this->log_manual('Payment ID final a buscar: ' . $payment_id);

                $payment = MercadoPago\Payment::find_by_id($payment_id);

                if ($payment) {
                    $this->log_manual('Estado del pago: ' . $payment->status . '. External Reference de MP: ' . (isset($payment->external_reference) ? $payment->external_reference : 'N/A'));
                } else {
                    $this->log_manual('ERROR: No se pudo obtener el pago con ID: ' . $payment_id . ' desde la API de MP.');
                    $CI->db->trans_rollback();
                    http_response_code(200);
                    return;
                }

                if ($payment->status === 'approved') {
                    $external_reference = trim($payment->external_reference);
                    $this->log_manual('External Reference (trimmed) obtenido de MP: ' . $external_reference);

                    $compra_pendiente = $CI->ticket_model->getCompraPendiente($external_reference);

                    $this->log_manual('Estado inicial compra pendiente (desde DB): ' . print_r($compra_pendiente, true));

                    if ($compra_pendiente && isset($compra_pendiente->id) && $compra_pendiente->procesada == 0) {
                        $this->log_manual('Iniciando procesamiento de compra pendiente. ID: ' . $compra_pendiente->id . ', External Ref: ' . $external_reference);

                        $seleccion = json_decode($compra_pendiente->datos, true);
                        if ($seleccion === null) {
                            $this->log_manual('ERROR: Error al decodificar datos JSON de la compra pendiente. Datos: ' . $compra_pendiente->datos);
                            $CI->db->trans_rollback();
                            http_response_code(200);
                            return;
                        }
                        $this->log_manual('JSON de datos decodificado con éxito. Ítems encontrados: ' . count($seleccion));

                        $n_compras = 0;
                        $dias = $seleccion;
                        $id_usuario = $compra_pendiente->id_usuario;
                        $monto_total_compra_pendiente = (float)$compra_pendiente->total;

                        foreach ($dias as $index => $compra) {
                            $this->log_manual('Procesando item de compra #' . ($index + 1) . ': ' . json_encode($compra));

                            $data_compra = [
                                'fecha' => date('Y-m-d'),
                                'hora' => date('H:i:s'),
                                'dia_comprado' => $compra['dia_comprado'],
                                'id_usuario' => $id_usuario,
                                'precio' => $compra['precio'],
                                'tipo' => $compra['tipo'],
                                'turno' => $compra['turno'],
                                'menu' => $compra['menu'],
                                'transaccion_id' => -1,
                                'external_reference' => $external_reference
                            ];

                            $data_log = [
                                'fecha' => date('Y-m-d'),
                                'hora' => date('H:i:s'),
                                'dia_comprado' => $compra['dia_comprado'],
                                'id_usuario' => $id_usuario,
                                'precio' => $compra['precio'],
                                'tipo' => $compra['tipo'],
                                'turno' => $compra['turno'],
                                'menu' => $compra['menu'],
                                'transaccion_tipo' => 'Compra por Mercado Pago',
                                'transaccion_id' => -1,
                                'external_reference' => $external_reference
                            ];

                            $resultado_add_compra = $CI->ticket_model->addCompra($data_compra);
                            if ($resultado_add_compra) {
                                $this->log_manual('Item de compra #' . ($index + 1) . ' añadido con éxito. DB insert ID: ' . $resultado_add_compra);
                                $CI->ticket_model->addLogCompra($data_log);
                                $this->log_manual('Log de item de compra #' . ($index + 1) . ' añadido.');
                                $n_compras++;
                            } else {
                                $db_error = $CI->db->error();
                                $this->log_manual('ERROR: Fallo al insertar item de compra #' . ($index + 1) . ': ' . print_r($data_compra, true) . ' DB Error: ' . $db_error['message'] . ' Code: ' . $db_error['code']);
                                throw new Exception('Fallo al insertar item de compra en DB.');
                            }
                        }
                        $this->log_manual('Bucle de inserción de items de compra finalizado. Total de items insertados: ' . $n_compras);

                        if ($n_compras > 0) {
                            // saldo actual del usuario ANTES de la transacción
                            $saldo_actual_usuario = $CI->ticket_model->getSaldoByIDUser($id_usuario);
                            $this->log_manual('Saldo actual del usuario ' . $id_usuario . ' antes de la compra: ' . $saldo_actual_usuario);

                            // Calcula el nuevo saldo después de la compra
                            if($monto_total_compra_pendiente >= $saldo_actual_usuario){
                                $saldo_final_transaccion = 0;
                            }
                            else{
                                $saldo_final_transaccion = $saldo_actual_usuario - $monto_total_compra_pendiente;
                            }
                            $this->log_manual('Monto total de la compra: ' . $monto_total_compra_pendiente . '. Saldo calculado para la transacción: ' . $saldo_final_transaccion);

                            $transaction_compra = [
                                'fecha' => date('Y-m-d'),
                                'hora' => date('H:i:s'),
                                'id_usuario' => $id_usuario,
                                'transaccion' => 'Compra por Mercado Pago',
                                'monto' => -$monto_total_compra_pendiente,
                                'saldo' => $saldo_final_transaccion
                            ];
                            $this->log_manual('Intentando insertar transacción principal con datos: ' . json_encode($transaction_compra));
                            $id_insert = $CI->ticket_model->addTransaccion($transaction_compra);
                            
                            if ($id_insert === 0 || $id_insert === false) {
                                $db_error = $CI->db->error();
                                $this->log_manual('CRÍTICO: Fallo al insertar transacción principal: ' . print_r($transaction_compra, true) . ' DB Error: ' . $db_error['message'] . ' Code: ' . $db_error['code']);
                                throw new Exception('Fallo al insertar transacción principal en DB.');
                            }
                            $this->log_manual('ID de transacción principal insertada: ' . $id_insert);

                            // Actualiza el transaccion_id en las compras y logs de compra
                            $compras_actualizadas = $CI->ticket_model->updateTransactionInCompraByExternalReference($external_reference, $id_insert);
                            $this->log_manual('Filas actualizadas en tabla "compra" por external_reference: ' . $compras_actualizadas);

                            $logcompras_actualizadas = $CI->ticket_model->updateTransactionInLogCompraByExternalReference($external_reference, $id_insert);
                            $this->log_manual('Filas actualizadas en tabla "log_compras" por external_reference: ' . $logcompras_actualizadas);

                            // Actualiza el saldo del usuario en la tabla 'usuarios'
                            if (!$CI->ticket_model->updateSaldoByIDUser($id_usuario, $saldo_final_transaccion)) {
                                $db_error = $CI->db->error();
                                $this->log_manual('CRÍTICO: Fallo al actualizar el saldo del usuario ' . $id_usuario . ' a ' . $saldo_final_transaccion . '. DB Error: ' . $db_error['message'] . ' Code: ' . $db_error['code']);
                                throw new Exception('Fallo al actualizar saldo del usuario en DB.');
                            } else {
                                $this->log_manual('Saldo del usuario ' . $id_usuario . ' actualizado a: ' . $saldo_final_transaccion);
                            }

                            // Marca la compra pendiente como procesada
                            $rows_affected = $CI->ticket_model->setCompraPendienteProcesada($external_reference);

                            if ($rows_affected > 0) {
                                $this->log_manual('ÉXITO: Compra pendiente marcada como procesada: ' . $external_reference . ' (Filas afectadas: ' . $rows_affected . ')');
                            } else {
                                $this->log_manual('ADVERTENCIA: setCompraPendienteProcesada NO actualizó ninguna fila para: ' . $external_reference . '. Esto puede significar que ya estaba procesada o que la external_reference no coincide. (Filas afectadas: ' . $rows_affected . ')');
                            }
                        } else {
                            $this->log_manual('ERROR: No se pudo registrar ninguna compra individual para el usuario ID ' . $id_usuario . ' a partir de los datos decodificados. No se creó transacción principal.');
                            throw new Exception('No se procesaron ítems de compra válidos.');
                        }
                    } else {
                        // Si la compra pendiente ya está procesada o no se encuentra
                        if ($compra_pendiente === null) {
                            $this->log_manual('ERROR: Compra pendiente NO encontrada para external_reference: ' . $external_reference . '. Esto podría indicar un problema de sincronización o que fue eliminada/modificada. No se procesará.');
                        } else {
                            $this->log_manual('ADVERTENCIA: Compra pendiente ID ' . $compra_pendiente->id . ' con External Ref: ' . $external_reference . ' ya estaba procesada (procesada=' . $compra_pendiente->procesada . '). Ignorando reintento.');
                        }
                        $CI->db->trans_commit(); // commit si no hay nada que procesar
                        http_response_code(200); // Responder 200 OK si ya fue procesada o no se encontró
                        return;
                    }
                } else {
                    $this->log_manual('ADVERTENCIA: Pago no aprobado o inválido para el ID: ' . $payment_id . ' (Estado: ' . ($payment ? $payment->status : 'N/A') . '). No se procesará la compra.');
                    $CI->db->trans_commit(); // commit si el pago no es aprobado
                    http_response_code(200); // Responder 200 OK aunque no esté aprobado, para que MP no reintente este pago.
                    return;
                }
            } elseif (isset($data['topic']) && $data['topic'] === 'merchant_order') {
                $this->log_manual('Notification type: merchant_order. Resource: ' . $data['resource']);
                $CI->db->trans_commit();
                http_response_code(200);
                return;
            } else {
                $this->log_manual('Webhook recibido con formato desconocido o ignorado: ' . $input);
                $CI->db->trans_commit();
                http_response_code(200); // Responde 200 si el tipo de notificación no es el esperado
                return;
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
                http_response_code(200);
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
}