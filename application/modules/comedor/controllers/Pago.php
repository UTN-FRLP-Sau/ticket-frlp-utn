<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pago extends CI_Controller
{
    private function log_manual($mensaje)
    {
        // ruta del archivo de log específica del webhook
        $ruta_log = APPPATH . 'logs/pago_manual_' . date('Y-m-d') . '.log';
        $fecha = date('Y-m-d H:i:s');
        file_put_contents($ruta_log, "[$fecha] $mensaje\n", FILE_APPEND);
    }


    public function comprar()
    {
        // --- LOG DE INICIO DEL MÉTODO ---
        log_message('debug', 'PAGO: Método comprar() iniciado.');

        $this->config->load('ticket');
        $access_token = $this->config->item('MP_ACCESS_TOKEN');

        $external_reference = $this->session->userdata('external_reference');
        // --- LOG DE VERIFICACIÓN DE external_reference ---
        log_message('debug', 'PAGO: external_reference de sesión: ' . ($external_reference ? $external_reference : 'VACIA'));

        if (!$external_reference) {
            // --- LOG DE REDIRECCIÓN POR FALTA DE external_reference ---
            log_message('error', 'PAGO: external_reference vacía. Redirigiendo a comedor/ticket.');
            redirect('comedor/ticket');
            return;
        }

        $this->load->model('ticket_model');
        $this->load->library('session'); // Asegúrate de que la librería de sesión esté cargada

        $compra = $this->ticket_model->getCompraPendiente($external_reference);
        // --- LOG DE COMPRA PENDIENTE ---
        log_message('debug', 'PAGO: Resultado de getCompraPendiente para ' . $external_reference . ': ' . ($compra ? json_encode($compra) : 'NO ENCONTRADA'));

        if (!$compra) {
            // --- LOG DE REDIRECCIÓN POR COMPRA PENDIENTE NO ENCONTRADA ---
            log_message('error', 'PAGO: Compra pendiente no encontrada para external_reference: ' . $external_reference . '. Redirigiendo a comedor/ticket.');
            redirect('comedor/ticket');
            return;
        }

        // --- INICIO DE LA REVALIDACIÓN DE FECHAS DE VIANDAS AL RETOMAR PAGO ---
        $viandas_en_compra = $this->ticket_model->getViandasCompraPendiente($compra->id);

        $hayViandaInvalida = false;
        if (is_array($viandas_en_compra)) { // Asegurarse de que $viandas_en_compra sea un array
            foreach ($viandas_en_compra as $vianda) {
                if (!$this->ticket_model->esFechaViandaAunOrdenable($vianda['dia_comprado'])) { // Usar 'dia_comprado' si es la clave en el JSON
                    $hayViandaInvalida = true;
                    log_message('warning', 'PAGO: Vianda ' . $vianda['dia_comprado'] . ' de la compra ' . $external_reference . ' no es válida en el momento del pago.');
                    break; // Una vianda inválida es suficiente para cancelar toda la compra
                }
            }
        } else {
            log_message('error', 'PAGO: getViandasCompraPendiente no devolvió un array para compra ID: ' . $compra->id);
            $hayViandaInvalida = true;
        }


        if ($hayViandaInvalida) {
            // Marca la compra como expirada/cancelada debido a la revalidación de fechas
            $this->ticket_model->updateCompraPendienteEstado($compra->id, 'expired_by_date_cutoff', 'Compra expirada por revalidación de fecha en el momento del pago.');
            $this->session->unset_userdata('external_reference');


            $this->session->set_flashdata('error_message', 'Tu compra pendiente no pudo ser completada. Algunas viandas ya no pueden ser compradas debido a que sus plazos de pedido han expirado. Por favor, inicia una nueva compra.');
            redirect('comedor/ticket');
            $this->session->unset_userdata('error_compra');
            return;
        }
        // --- FIN DE LA REVALIDACIÓN DE FECHAS DE VIANDAS ---


        // Actualiza estado a 'pasarela' si aún no ha sido procesada ---
        // Esto marca que la orden ya entró en el flujo de Mercado Pago desde nuestro lado
        // Se considera 'null' o cadena vacía como el estado inicial antes de ir a MP
        if ($compra->mp_estado === null || $compra->mp_estado === '') {
            $this->ticket_model->updateCompraPendienteEstado($compra->id, 'pasarela', 'Usuario redirigido a pasarela de pago');
            log_message('debug', 'PAGO: mp_estado actualizado a "pasarela" para ' . $external_reference);
        }

        // URLs de retorno y notificación para Mercado Pago
        $notification_url = 'https://ticket.frlp.utn.edu.ar/webhook/mercadopago?source_news=webhooks';
        $back_urls = array(
            "success" => "https://ticket.frlp.utn.edu.ar/comedor/pago/compra_exitosa",
            "failure" => "https://ticket.frlp.utn.edu.ar/comedor/pago/compra_fallida",
            "pending" => "https://ticket.frlp.utn.edu.ar/comedor/pago/compra_pendiente",
        );

        $documento = $this->session->userdata('documento');
        $nombre = $this->session->userdata('nombre');
        $apellido = $this->session->userdata('apellido');
        log_message('debug', "DEBUG: nombre=$nombre, apellido=$apellido, documento=$documento");
        // Intenta generar la preferencia, que devolverá null si el saldo es suficiente
        $preferencia_info = $this->ticket_model->generarPreferenciaConSaldo(
            $external_reference,
            $access_token,
            $notification_url,
            $back_urls,
            $nombre,
            $apellido,
            $documento
        );
        log_message('debug', 'PAGO: Resultado de generarPreferenciaConSaldo: ' . ($preferencia_info === null ? 'NULL (Saldo suficiente)' : ($preferencia_info === false ? 'FALSE (Error)' : json_encode($preferencia_info))));

        // --- LÓGICA DE PROCESAMIENTO DE COMPRA ---
        // Caso 1: Saldo de la cuenta cubre toda la compra
        if ($preferencia_info === null) {
            log_message('info', 'PAGO: Saldo cubre toda la compra (' . $compra->total . '). Intentando procesar directamente.');

            // Llama a procesarCompraConSaldo
            $procesado_con_saldo = $this->ticket_model->procesarCompraConSaldo($compra, (float)$compra->total);

            // una vez generada la compra borro la compra pendiente
            $this->ticket_model->deleteCompraPendiente($compra->id);

            if ($procesado_con_saldo) {
                log_message('info', 'PAGO: Compra procesada exitosamente con saldo. Redirigiendo a compra_exitosa.');
                $this->session->set_flashdata('send_balance_purchase_email', true);
                redirect('comedor/pago/compra_exitosa?external_reference=' . $external_reference);
                return;
            } else {
                log_message('error', 'PAGO: ERROR al procesar la compra directamente con saldo. Falló procesarCompraConSaldo.');
                show_error('No se pudo procesar la compra con el saldo disponible. Intente nuevamente.');
                return;
            }
        }
        // Caso 2: Error al generar la preferencia de Mercado Pago (e.g., API issues)
        else if ($preferencia_info === false) {
            log_message('error', 'PAGO: ERROR en generarPreferenciaConSaldo (retornó FALSE).');
            show_error('Error al generar la preferencia de pago. Intente nuevamente.');
            return;
        }
        // Caso 3: Saldo insuficiente, se generó preferencia de Mercado Pago
        else {
            $init_point = isset($preferencia_info['init_point']) ? $preferencia_info['init_point'] : 'NO DEFINIDO';
            log_message('info', 'PAGO: Saldo insuficiente. Redirigiendo a Mercado Pago. init_point: ' . $init_point);

            if ($init_point === 'NO DEFINIDO' || empty($init_point)) {
                log_message('error', 'PAGO: init_point de Mercado Pago es inválido o vacío. No se puede redirigir.');
                show_error('No se pudo obtener la URL de pago de Mercado Pago. Intente nuevamente.');
                return;
            }

            // Redirigir al usuario al init_point de Mercado Pago
            log_message('debug', 'PAGO: Redirigiendo a: ' . $init_point);
            redirect($init_point);
            return;
        }
    }

    public function compra_exitosa()
    {
        log_message('debug', 'PAGO: Método compra_exitosa() alcanzado.');

        $this->session->unset_userdata('error_compra'); 

        $external_reference = $this->input->get('external_reference');
        log_message('debug', 'PAGO: external_reference en compra_exitosa (GET): ' . ($external_reference ? $external_reference : 'VACIA/NULA'));
        $this->log_manual('PAGO: external_reference en compra_exitosa (GET): ' . ($external_reference ? $external_reference : 'VACIA/NULA'));

        if (!$external_reference) {
            log_message('error', 'PAGO: external_reference no encontrada en la URL de compra_exitosa. Redirigiendo.');
            redirect(base_url('comedor'));
            return;
        }

        $this->load->model('ticket_model');
        $this->load->model('general/general_model', 'generalticket');

        $transaccion_data = $this->ticket_model->getTransaccionByExternalReference($external_reference);
        log_message('debug', 'PAGO: transaccion_data desde getTransaccionByExternalReference: ' . ($transaccion_data ? json_encode($transaccion_data) : 'NO ENCONTRADA'));

        $this->log_manual('PAGO: transaccion_data desde getTransaccionByExternalReference: ' . ($transaccion_data ? json_encode($transaccion_data) : 'NO ENCONTRADA'));

        // Si el pago fue realizado pero aun no se encuentra la transacción (no se recibió el webhook de MP)
        if (!$transaccion_data) {
            log_message('error', 'PAGO: No se encontró la transacción para external_reference: ' . $external_reference);
            // cambio estado a pendiente para que el usuario no pueda volver a pagar
            $filas_afectadas = $this->ticket_model->updateCompraPendienteEstadoByExternalReference($external_reference, 'pending');

            if ($filas_afectadas > 0) {
                $this->log_manual('PAGO: Estado de compra actualizado a "pending" para external_reference: ' . ($external_reference ? $external_reference : 'VACIA/NULA') . '. Filas afectadas: ' . $filas_afectadas);
            } else {
                $this->log_manual('PAGO: Advertencia: No se pudo actualizar el estado de compra a "pending" o ya estaba en ese estado para external_reference: ' . ($external_reference ? $external_reference : 'VACIA/NULA') . '. Filas afectadas: ' . $filas_afectadas);
            }

            // Borro la referencia externa de la sesión para que no se intente procesar de nuevo
            $this->session->unset_userdata('external_reference');
            redirect(base_url('comedor/pago/compra_pendiente'));
            return;
        }

        $id_transaccion = $transaccion_data->id;
        $id_usuario = $transaccion_data->id_usuario;

        log_message('debug', 'PAGO: id_transaccion en compra_exitosa (obtenido de DB): ' . $id_transaccion);
        log_message('debug', 'PAGO: id_usuario en compra_exitosa (obtenido de DB): ' . $id_usuario);

        $usuario = $this->ticket_model->getUserById($id_usuario);
        log_message('debug', 'PAGO: Usuario recuperado en compra_exitosa: ' . ($usuario ? json_encode($usuario) : 'NO ENCONTRADO'));

        $compras = $this->ticket_model->getComprasByExternalReference($external_reference);
        log_message('debug', 'PAGO: Compras recuperadas en compra_exitosa (desde tabla "compra" por external_reference): ' . ($compras ? json_encode($compras) : 'NO ENCONTRADAS'));

        // Solo envia el correo si este es un flujo de compra con saldo
        if ($this->session->flashdata('send_balance_purchase_email')) {
            log_message('debug', 'PAGO: Flashdata "send_balance_purchase_email" detectado. Preparando envío de correo para compra con saldo.');

            if ($usuario && $compras) {
                // HTML del correo
                $dataEmail['user_name'] = $usuario->nombre . ' ' . $usuario->apellido;
                $dataEmail['compras'] = $compras;
                $dataEmail['total'] = abs($transaccion_data->monto);
                $dataEmail['total'] = abs($transaccion_data->monto); 
                
                $dataEmail['fechaHoy'] = date('d/m/Y', time());
                $dataEmail['horaAhora'] = date('H:i:s', time());
                $dataEmail['recivoNumero'] = $external_reference; // external_reference como número de recibo

                $subject = "Confirmación de Compra - Comedor Universitario";
                $message = $this->load->view('general/correos/recibo_compra', $dataEmail, true);

                log_message('debug', 'PAGO: Intentando enviar email de confirmación para compra con saldo.');
                if ($this->generalticket->smtpSendEmail($usuario->mail, $subject, $message)) {
                    log_message('info', 'PAGO: Email de confirmación de compra con saldo enviado a ' . $usuario->mail . ' para external_reference ' . $external_reference . '.');
                } else {
                    log_message('error', 'PAGO: Fallo al enviar email de confirmación de compra con saldo a ' . $usuario->mail . ' para external_reference ' . $external_reference . '.');
                }
            } else {
                log_message('error', 'PAGO: No se pudo enviar el correo de confirmación de compra con saldo. Usuario o compras no encontrados para external_reference ' . $external_reference . '.');
            }
        } else {
            log_message('debug', 'PAGO: Flashdata "send_balance_purchase_email" NO detectado. Se asume que el correo se maneja por webhook de MP o ya fue enviado.');
        }

        $this->session->unset_userdata('external_reference');
        $this->session->unset_userdata('error_compra');

        $this->load->view('usuario/header', ['titulo' => '¡Pago exitoso!']);
        $this->load->view('compra_exitosa');
        $this->load->view('general/footer');
    }

    public function compra_fallida()
    {
        log_message('debug', 'PAGO: Método compra_fallida() alcanzado.');
        $this->session->unset_userdata('error_compra'); 
        $this->session->unset_userdata('external_reference');
        $this->load->view('usuario/header', ['titulo' => 'Pago fallido']);
        $this->load->view('compra_fallida');
        $this->load->view('general/footer');
    }

    public function compra_pendiente()
    {
        log_message('debug', 'PAGO: Método compra_pendiente() alcanzado.');

        $external_reference = $this->input->get('external_reference');
        
        log_message('debug', 'PAGO: external_reference en compra_exitosa (GET): ' . ($external_reference ? $external_reference : 'VACIA/NULA'));
        $this->log_manual('PAGO: external_reference en compra_exitosa (GET): ' . ($external_reference ? $external_reference : 'VACIA/NULA'));

        $filas_afectadas = $this->ticket_model->updateCompraPendienteEstadoByExternalReference($external_reference, 'pending');

        if ($filas_afectadas > 0) {
            $this->log_manual('PAGO: Estado de compra actualizado a "pending" para external_reference: ' . ($external_reference ? $external_reference : 'VACIA/NULA') . '. Filas afectadas: ' . $filas_afectadas);
        } else {
            $this->log_manual('PAGO: Advertencia: No se pudo actualizar el estado de compra a "pending" o ya estaba en ese estado para external_reference: ' . ($external_reference ? $external_reference : 'VACIA/NULA') . '. Filas afectadas: ' . $filas_afectadas);
        }

        $this->session->unset_userdata('error_compra'); 
        $this->session->unset_userdata('external_reference');
        $this->load->view('usuario/header', ['titulo' => 'Pago pendiente']);
        $this->load->view('compra_pendiente');
        $this->load->view('general/footer');
    }


    public function cancelar_compra_ajax()
    {
        // Asegurarse de que la solicitud es AJAX
        if (!$this->input->is_ajax_request()) {
            show_404(); // O redirigir a una página de error
        }

        $external_reference = $this->input->post('external_reference');

        if (empty($external_reference)) {
            echo json_encode(['success' => false, 'message' => 'Referencia externa no proporcionada.']);
            return;
        }

        $this->load->model('ticket_model');
        $compra_pendiente = $this->ticket_model->getCompraPendiente($external_reference);

        if (!$compra_pendiente) {
            echo json_encode(['success' => false, 'message' => 'Compra pendiente no encontrada para la referencia proporcionada.']);
            return;
        }

        // --- PRECAUCIÓN DE RACE CONDITION: Verificar el estado actual antes de cancelar ---
        // Si el estado ya cambió a aprobado o rechazado por un webhook, no permitir cancelar.
        if ($compra_pendiente->mp_estado === 'approved' || $compra_pendiente->mp_estado === 'rejected' || $compra_pendiente->mp_estado === 'cancelled' || $compra_pendiente->mp_estado === 'expired_by_date_cutoff') {
            echo json_encode(['success' => false, 'message' => 'Esta compra ya no está pendiente de acción por el usuario. Estado actual: ' . $compra_pendiente->mp_estado . '.']);
            return;
        }

        // --- Actualiza estado a 'cancelled_by_user' ---
        $this->ticket_model->updateCompraPendienteEstado($compra_pendiente->id, 'cancelled_by_user', 'Usuario canceló orden desde modal en el menú principal');
        log_message('debug', 'PAGO: Compra pendiente ' . $compra_pendiente->id . ' marcada como cancelada por usuario.');
        
        if (!$this->ticket_model->deleteCompraPendiente($compra_pendiente->id)) {
            log_message('error', 'PAGO: Fallo al eliminar el registro de compra pendiente ' . $compra_pendiente->id . ' después de la cancelación del usuario.');
        
        } else {
            log_message('debug', 'PAGO: Registro de compra pendiente ' . $compra_pendiente->id . ' eliminado tras cancelación por usuario.');
        }

        // --- Limpiar la external_reference de la sesión del usuario ---
        $this->session->unset_userdata('external_reference');
        $this->session->unset_userdata('error_compra');

        echo json_encode(['success' => true, 'message' => 'Compra pendiente cancelada exitosamente. Ahora puedes seleccionar nuevas viandas.']);
    }
}