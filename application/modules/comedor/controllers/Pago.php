<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pago extends CI_Controller
{
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
        $compra = $this->ticket_model->getCompraPendiente($external_reference);
        // --- LOG DE COMPRA PENDIENTE ---
        log_message('debug', 'PAGO: Resultado de getCompraPendiente para ' . $external_reference . ': ' . ($compra ? json_encode($compra) : 'NO ENCONTRADA'));

        if (!$compra) {
            // --- LOG DE REDIRECCIÓN POR COMPRA PENDIENTE NO ENCONTRADA ---
            log_message('error', 'PAGO: Compra pendiente no encontrada para external_reference: ' . $external_reference . '. Redirigiendo a comedor/ticket.');
            redirect('comedor/ticket');
            return;
        }

        // URLs de retorno y notificación para Mercado Pago
        $notification_url = 'https://8ffead6fbe57.ngrok-free.app/webhook/mercadopago';
        $back_urls = array(
            "success" => "https://8ffead6fbe57.ngrok-free.app/comedor/pago/compra_exitosa",
            "failure" => "https://8ffead6fbe57.ngrok-free.app/comedor/pago/compra_fallida",
            "pending" => "https://8ffead6fbe57.ngrok-free.app/comedor/pago/compra_pendiente",
        );

        // Intenta generar la preferencia, que devolverá null si el saldo es suficiente
        $preferencia_info = $this->ticket_model->generarPreferenciaConSaldo(
            $external_reference,
            $access_token,
            $notification_url,
            $back_urls
        );
        log_message('debug', 'PAGO: Resultado de generarPreferenciaConSaldo: ' . ($preferencia_info === null ? 'NULL (Saldo suficiente)' : ($preferencia_info === false ? 'FALSE (Error)' : json_encode($preferencia_info))));

        // --- LÓGICA DE PROCESAMIENTO DE COMPRA ---

        // Caso 1: Saldo de la cuenta cubre toda la compra
        if ($preferencia_info === null) {
            log_message('info', 'PAGO: Saldo cubre toda la compra (' . $compra->total . '). Intentando procesar directamente.');

            // Llama a procesarCompraConSaldo
            $procesado_con_saldo = $this->ticket_model->procesarCompraConSaldo($compra, (float)$compra->total);

            if ($procesado_con_saldo) {
                log_message('info', 'PAGO: Compra procesada exitosamente con saldo. Redirigiendo a compra_exitosa.');
                redirect('comedor/pago/compra_exitosa?external_reference=' . $external_reference); // Redirigir directamente
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

        $external_reference = $this->input->get('external_reference');
        log_message('debug', 'PAGO: external_reference en compra_exitosa (GET): ' . ($external_reference ? $external_reference : 'VACIA/NULA'));

        if (!$external_reference) {
            log_message('error', 'PAGO: external_reference no encontrada en la URL de compra_exitosa. Redirigiendo.');
            redirect(base_url('comedor'));
            return;
        }

        $this->load->model('ticket_model');
        $this->load->model('general/general_model', 'generalticket');

        $transaccion_data = $this->ticket_model->getTransaccionByExternalReference($external_reference);
        log_message('debug', 'PAGO: transaccion_data desde getTransaccionByExternalReference: ' . ($transaccion_data ? json_encode($transaccion_data) : 'NO ENCONTRADA'));

        if (!$transaccion_data) {
            log_message('error', 'PAGO: No se encontró la transacción para external_reference: ' . $external_reference);
            redirect(base_url('comedor/compra_fallida'));
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


        if ($usuario && $compras) {
            $costoVianda = $this->ticket_model->getCostoById($usuario->id_precio);
            $dataRecivo['compras'] = $compras;
            $dataRecivo['total'] = $costoVianda * count($compras);
            $dataRecivo['fechaHoy'] = date('d/m/Y', time());
            $dataRecivo['horaAhora'] = date('H:i:s', time());
            $dataRecivo['recivoNumero'] = $id_transaccion;

            $subject = "Recibo de compra del comedor";
            $message = $this->generalticket->load->view('general/correos/recibo_compra', $dataRecivo, true);

            if (!$this->session->flashdata('mail_enviado')) {
                log_message('debug', 'PAGO: flashdata("mail_enviado") es FALSE. Procediendo a enviar email.');
                $this->generalticket->smtpSendEmail($usuario->mail, $subject, $message);
                $this->session->set_flashdata('mail_enviado', true);
                log_message('debug', 'PAGO: Email enviado y flashdata("mail_enviado") seteado a TRUE.');
            } else {
                log_message('debug', 'PAGO: flashdata("mail_enviado") ya estaba TRUE. No se reenvía el email.');
            }
        } else {
            log_message('error', 'PAGO: Condición ($usuario && $compras) es FALSE. No se pudo enviar el correo de confirmación. Posiblemente datos no encontrados con ID de usuario o transacción.');
        }

        $this->load->view('usuario/header', ['titulo' => '¡Pago exitoso!']);
        $this->load->view('compra_exitosa');
        $this->load->view('general/footer');
    }



    public function compra_fallida()
    {
        log_message('debug', 'PAGO: Método compra_fallida() alcanzado.');
        $this->load->view('usuario/header', ['titulo' => 'Pago fallido']);
        $this->load->view('compra_fallida');
        $this->load->view('general/footer');
    }

    public function compra_pendiente()
    {
        log_message('debug', 'PAGO: Método compra_pendiente() alcanzado.');
        $this->load->view('usuario/header', ['titulo' => 'Pago pendiente']);
        $this->load->view('compra_pendiente');
        $this->load->view('general/footer');
    }
}
