<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Webhook_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        // Carga los modelos necesarios
        $this->load->model('comedor/ticket_model');
        $this->load->model('general/general_model', 'generalticket'); // Se carga general_model con el alias 'generalticket'
    }

    /**
     * Método público para registrar logs específicos de las operaciones de este modelo.
     * @param string $mensaje El mensaje a registrar.
     * @param string $prefijo_archivo Prefijo opcional para el nombre del archivo de log.
     */
    public function _logManual($mensaje, $prefijo_archivo = 'webhook') {
        // ruta del archivo de log específica del modelo de procesamiento de MP
        $ruta_log = APPPATH . 'logs/' . $prefijo_archivo . '_manual_' . date('Y-m-d') . '.log';
        $fecha = date('Y-m-d H:i:s');

        // Extraer solo la ruta del directorio del archivo de log
        $log_dir = dirname($ruta_log); // Obtiene el directorio padre de la ruta del archivo

        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true); // Crea el directorio de forma recursiva si no existe
        }

        file_put_contents($ruta_log, "[$fecha] $mensaje\n", FILE_APPEND);
    }


    /**
     * Mapea un código de estado de detalle de Mercado Pago a un mensaje más amigable para el usuario.
     * @param string $mp_code El código de estado de detalle de Mercado Pago.
     * @return string Un mensaje descriptivo para el usuario.
     */
    private function _mapMpStatusDetail($mp_code) {
        switch ($mp_code) {
            case 'approved':
                return '¡Listo! Se acreditó tu pago.';
            case 'pending':
                return 'Tu pago está pendiente de aprobación.';
            case 'in_process':
                return 'Tu pago está en proceso de revisión.';
            case 'rejected':
                return 'Tu pago fue rechazado. Esto puede deberse a fondos insuficientes, un error en los datos de la tarjeta, o problemas con la seguridad. Por favor, revisa tus datos o intenta con otro medio de pago.';
            case 'cc_rejected_bad_filled_card_number':
                return 'Rechazado: Número de tarjeta inválido.';
            case 'cc_rejected_bad_filled_date':
                return 'Rechazado: Fecha de vencimiento inválida.';
            case 'cc_rejected_bad_filled_security_code':
                return 'Rechazado: Código de seguridad inválido.';
            case 'cc_rejected_blacklist':
                return 'Rechazado: No pudimos procesar tu pago. Contacta a tu entidad financiera para más información.';
            case 'cc_rejected_call_for_authorize':
                return 'Rechazado: Debes autorizar el pago con tu entidad financiera.';
            case 'cc_rejected_card_disabled':
                return 'Rechazado: Tu tarjeta está inhabilitada. Contacta a tu entidad financiera.';
            case 'cc_rejected_card_error':
                return 'Rechazado: Error general de la tarjeta. Intenta con otra o contacta a tu entidad financiera.';
            case 'cc_rejected_duplicated_payment':
                return 'Rechazado: Ya se ha realizado un pago con esta tarjeta. Si crees que es un error, contáctanos.';
            case 'cc_rejected_high_risk':
                return 'Rechazado: El pago fue rechazado por seguridad. Intenta con otro medio de pago.';
            case 'cc_rejected_insufficient_amount':
                return 'Rechazado: Saldo insuficiente.';
            case 'cc_rejected_max_attempts':
                return 'Rechazado: Has excedido el número máximo de intentos.';
            case 'cc_rejected_other_reason':
                return 'Rechazado: No pudimos procesar tu pago por un motivo desconocido. Intenta con otra tarjeta o medio de pago.';
            case 'expired':
                return 'Tu pago ha expirado.';
            case 'expired_by_date_cutoff':
                return 'La fecha límite para pagar ha expirado.';
            case 'refunded':
                return 'Tu pago ha sido reembolsado.';
            case 'charged_back':
                return 'Tu pago fue contracargado.';
            case 'reversed':
                return 'Tu pago ha sido revertido.';
            default:
                return 'Estado desconocido. Código: ' . $mp_code . '. Por favor, contáctanos si necesitas ayuda.';
        }
    }

    /**
     * Procesa un pago aprobado, incluyendo la deducción de saldo,
     * registro de transacciones, inserción de viandas y envío de email.
     * También marca la compra como procesada y la elimina de la tabla de pendientes.
     *
     * @param object $compra_pendiente Objeto de la compra pendiente de la DB.
     * @param object $payment_info Objeto de información del pago de Mercado Pago.
     * @return bool True si el procesamiento fue exitoso, false en caso contrario.
     * @throws Exception Si ocurre un error crítico durante el procesamiento.
     */
    public function procesarPagoAprobado($compra_pendiente, $payment_info) {
        $this->_logManual('INICIANDO PROCESAMIENTO PAGO APROBADO (WEBHOOK_MODEL) para Compra ID: ' . $compra_pendiente->id, 'webhook');

        $this->db->trans_begin(); // Inicia la transacción de la base de datos

        try {
            // Verifica si la compra ya fue procesada para evitar duplicados
            if ($compra_pendiente->procesada == 1) {
                $this->_logManual('PAGO APROBADO (WEBHOOK_MODEL): Compra ' . $compra_pendiente->id . ' ya estaba procesada. No se realizó ninguna acción adicional.', 'webhook');
                $this->db->trans_rollback();
                return true;
            }

            $id_usuario = $compra_pendiente->id_usuario;
            $total_compra = (float)$compra_pendiente->total;
            $external_reference = $compra_pendiente->external_reference;
            $payment_id_mp = $payment_info->id;

            $this->_logManual('PAGO APROBADO (WEBHOOK_MODEL): Saldo actual del usuario ' . $id_usuario . ' (antes de deducción): ' . $this->ticket_model->getSaldoByIDUser($id_usuario), 'webhook');

            $monto_pagado_mp = 0;
            if (isset($payment_info->transaction_amount)) {
                $monto_pagado_mp = (float)$payment_info->transaction_amount;
            } elseif (isset($payment_info->total_paid_amount)) {
                $monto_pagado_mp = (float)$payment_info->total_paid_amount;
            }
            $this->_logManual('PAGO APROBADO (WEBHOOK_MODEL): Monto pagado por MP: ' . $monto_pagado_mp, 'webhook');

            $saldo_inicial_usuario = $this->ticket_model->getSaldoByIDUser($id_usuario);
            $saldo_a_deducir = $total_compra - $monto_pagado_mp;
            $saldo_a_deducir = max(0, min($saldo_a_deducir, $saldo_inicial_usuario));
            $this->_logManual('PAGO APROBADO (WEBHOOK_MODEL): Saldo a deducir calculado: ' . $saldo_a_deducir, 'webhook');

            if ($saldo_a_deducir > 0) {
                $nuevo_saldo_usuario = $saldo_inicial_usuario - $saldo_a_deducir;
                if (!$this->ticket_model->updateSaldoByIDUser($id_usuario, $nuevo_saldo_usuario)) {
                    throw new Exception('Fallo al deducir saldo parcial (updateSaldoByIDUser) para usuario ' . $id_usuario);
                }
                $this->_logManual('PAGO APROBADO (WEBHOOK_MODEL): Saldo de usuario ' . $id_usuario . ' actualizado a: ' . $nuevo_saldo_usuario . ' (se dedujo ' . $saldo_a_deducir . ').', 'webhook');
            } else {
                $this->_logManual('PAGO APROBADO (WEBHOOK_MODEL): No se descontó saldo. Saldo a deducir calculado: ' . $saldo_a_deducir . '. Saldo inicial: ' . $saldo_inicial_usuario, 'webhook');
            }

            $saldo_para_registro_transaccion = $this->ticket_model->getSaldoByIDUser($id_usuario);
            $this->_logManual('PAGO APROBADO (WEBHOOK_MODEL): Saldo final del usuario de la DB para registro: ' . $saldo_para_registro_transaccion, 'webhook');

            $data_transaccion = [
                'id_usuario' => $id_usuario,
                'monto' => -1 * $total_compra,
                'fecha' => date('Y-m-d'),
                'hora' => date('H:i:s'),
                'transaccion' => 'Compra por Mercado Pago',
                'saldo' => $saldo_para_registro_transaccion,
                'external_reference' => $external_reference,
            ];
            $id_transaccion = $this->ticket_model->addTransaccion($data_transaccion);

            if ($id_transaccion === false) {
                throw new Exception('No se pudo insertar la transacción principal.');
            }
            $this->_logManual('PAGO APROBADO (WEBHOOK_MODEL): Transacción principal insertada, ID: ' . $id_transaccion, 'webhook');

            $email_vendedor_mp = 'mercado@pago';
            $id_vendedor_mp = $this->ticket_model->getVendedorIdByEmail($email_vendedor_mp);

            if ($id_vendedor_mp !== null) {
                $data_log_carga = [
                    'fecha'      => date('Y-m-d'),
                    'hora'       => date('H:i:s'),
                    'id_usuario' => $id_usuario,
                    'monto'      => $monto_pagado_mp,
                    'id_vendedor' => $id_vendedor_mp,
                    'formato'    => 'MP',
                    'transaccion_id'=> $id_transaccion,
                ];

                if (!$this->ticket_model->addLogCarga($data_log_carga)) {
                    throw new Exception('No se pudo insertar el registro en log_carga para la compra MP.');
                }
                $this->_logManual('PAGO APROBADO (WEBHOOK_MODEL): Registro en log_carga insertado para el pago de Mercado Pago.', 'webhook');
            } else {
                throw new Exception('No se pudo obtener el ID del vendedor MP para log_carga. Correo: ' . $email_vendedor_mp);
            }

            $viandas_en_compra = $this->ticket_model->getViandasCompraPendiente($compra_pendiente->id);
            if (empty($viandas_en_compra)) {
                throw new Exception('No se encontraron viandas para la compra pendiente ' . $compra_pendiente->id . '. No se procederá con la inserción de compras/logs.');
            }

            foreach ($viandas_en_compra as $vianda_item) {
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

                if (!$this->ticket_model->addCompra($data_compra)) {
                    throw new Exception('No se pudo insertar un item de compra en la base de datos.');
                }
                $this->_logManual('PAGO APROBADO (WEBHOOK_MODEL): Item de compra insertado para ' . $vianda_item['menu'] . '.', 'webhook');

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
                $this->_logManual('PAGO APROBADO (WEBHOOK_MODEL): Log de compra insertado para item: ' . $vianda_item['menu'], 'webhook');
            }

            // Elimina la compra pendiente solo si todo lo demás fue exitoso
            if (!$this->ticket_model->deleteCompraPendiente($compra_pendiente->id)) {
                log_message('error', 'PAGO APROBADO (WEBHOOK_MODEL): Fallo al eliminar el registro de compra pendiente ' . $compra_pendiente->id . '.');
            } else {
                $this->_logManual('PAGO APROBADO (WEBHOOK_MODEL): Registro de compra pendiente ' . $compra_pendiente->id . ' eliminado.', 'webhook');
            }

            $usuario = $this->ticket_model->getUserById($id_usuario);
            $compras_para_recibo = $this->ticket_model->getlogComprasByIDTransaccion($id_transaccion);

            if ($usuario && $compras_para_recibo) {
                $this->_logManual('PAGO APROBADO (WEBHOOK_MODEL): Intentando enviar email de compra exitosa.', 'webhook');

                $costoVianda = $this->ticket_model->getCostoById($usuario->id_precio);
                $dataRecibo['compras'] = $compras_para_recibo;
                $dataRecibo['total'] = $costoVianda * count($compras_para_recibo);
                $dataRecibo['fechaHoy'] = date('d/m/Y', time());
                $dataRecibo['horaAhora'] = date('H:i:s', time());
                $dataRecibo['recivoNumero'] = $external_reference;
                $dataRecibo['user_name'] = $usuario->nombre . ' ' . $usuario->apellido;

                $subject = "¡Recibo de compra de comedor! - Compra #" . $external_reference;

                $CI =& get_instance();
                $message = $CI->load->view('general/correos/recibo_compra', $dataRecibo, true);

                // Llamada a la función de envío de email desde general_model
                if ($this->generalticket->smtpSendEmail($usuario->mail, $subject, $message)) {
                    $this->_logManual('PAGO APROBADO (WEBHOOK_MODEL): Email de compra exitosa enviado a ' . $usuario->mail . ' para ' . $external_reference . '.', 'webhook');
                } else {
                    $this->_logManual('PAGO APROBADO (WEBHOOK_MODEL): Fallo al enviar email de compra exitosa al usuario ' . $usuario->mail . ' para external_reference ' . $external_reference, 'webhook');
                }
            } else {
                $this->_logManual('PAGO APROBADO (WEBHOOK_MODEL): Condición ($usuario && $compras_para_recibo) es FALSE. No se pudo enviar el correo de confirmación de compra exitosa para ' . $external_reference . '.', 'webhook');
            }

            $this->db->trans_commit();
            $this->_logManual('PAGO APROBADO (WEBHOOK_MODEL): Transacción de DB para compra ID: ' . $compra_pendiente->id . ' COMPLETADA y COMMIT.', 'webhook');
            return true;
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $this->_logManual('PAGO APROBADO (WEBHOOK_MODEL): EXCEPCIÓN: ' . $e->getMessage() . ' en ' . $e->getFile() . ' línea ' . $e->getLine() . '. Rollback de DB para compra ID: ' . $compra_pendiente->id, 'webhook');
            throw $e;
        }
    }

    /**
     * Procesa un pago rechazado, incluyendo el envío de un correo electrónico al usuario
     * y la eliminación de la compra de la tabla de pendientes.
     *
     * @param object $compra_pendiente Objeto de la compra pendiente de la DB.
     * @param object $payment_info Objeto con la información del pago de Mercado Pago.
     * @return bool True si el procesamiento fue exitoso, false en caso contrario.
     * @throws Exception Si ocurre un error crítico durante el procesamiento.
     */
    public function procesarPagoRechazado($compra_pendiente, $payment_info) {
        $external_reference = $compra_pendiente->external_reference;
        $mp_status_detail = isset($payment_info->status_detail) ? $payment_info->status_detail : 'N/A';
        $this->_logManual('INICIANDO PROCESAMIENTO PAGO RECHAZADO (WEBHOOK_MODEL) para Compra ID: ' . $compra_pendiente->id . ', Detalle: ' . $mp_status_detail, 'webhook');

        $this->db->trans_begin();

        try {
            if ($compra_pendiente->procesada == 1) {
                $this->_logManual('PAGO RECHAZADO (WEBHOOK_MODEL): Compra ' . $compra_pendiente->id . ' ya estaba procesada. No se realizó ninguna acción adicional.', 'webhook');
                $this->db->trans_rollback();
                return true;
            }

            // Elimina la compra pendiente
            if (!$this->ticket_model->deleteCompraPendiente($compra_pendiente->id)) {
                log_message('error', 'PAGO RECHAZADO (WEBHOOK_MODEL): Fallo al eliminar el registro de compra pendiente ' . $compra_pendiente->id . '.');
            }
            $this->_logManual('PAGO RECHAZADO (WEBHOOK_MODEL): Registro de compra pendiente ' . $compra_pendiente->id . ' eliminado.', 'webhook');
            
            $usuario = $this->ticket_model->getUserById($compra_pendiente->id_usuario);

            if ($usuario && $usuario->mail) {
                $viandas_rechazadas = $this->ticket_model->getViandasCompraPendiente($compra_pendiente->id);
                // Llamada al método privado dentro de este mismo modelo
                $user_friendly_status_detail = $this->_mapMpStatusDetail($mp_status_detail);

                $subject = "¡Pago Rechazado! - Tu compra #" . $external_reference;

                $dataEmail = [
                    'external_reference' => $external_reference,
                    'status_detail' => $user_friendly_status_detail,
                    'user_name' => $usuario->nombre . ' ' . $usuario->apellido,
                    'viandas' => $viandas_rechazadas,
                    'fechaHoy' => date('d/m/Y', time()),
                    'horaAhora' => date('H:i:s', time()),
                ];

                $CI =& get_instance();
                $message = $CI->load->view('general/correos/pago_rechazado', $dataEmail, true);

                // Llamada a la función de envío de email desde general_model
                if ($this->generalticket->smtpSendEmail($usuario->mail, $subject, $message)) {
                    $this->_logManual('PAGO RECHAZADO (WEBHOOK_MODEL): Correo enviado a ' . $usuario->mail . ' para ' . $external_reference, 'webhook');
                } else {
                    $this->_logManual('PAGO RECHAZADO (WEBHOOK_MODEL): Fallo al enviar correo a ' . $usuario->mail . ' para ' . $external_reference, 'webhook');
                }
            } else {
                $this->_logManual('PAGO RECHAZADO (WEBHOOK_MODEL): Usuario o email no encontrado para external_reference ' . $external_reference . '. No se pudo enviar correo de rechazo.', 'webhook');
            }


            $this->db->trans_commit();
            $this->_logManual('PAGO RECHAZADO (WEBHOOK_MODEL): Transacción de DB para compra ID: ' . $compra_pendiente->id . ' COMPLETADA y COMMIT.', 'webhook');
            return true;
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $this->_logManual('PAGO RECHAZADO (WEBHOOK_MODEL): EXCEPCIÓN: ' . $e->getMessage() . ' en ' . $e->getFile() . ' línea ' . $e->getLine() . '. Rollback de DB para compra ID: ' . $compra_pendiente->id, 'webhook');
            throw $e;
        }
    }
}