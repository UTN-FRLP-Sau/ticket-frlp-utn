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

        // Redirige según el estado actual de mp_estado en la compra pendiente
        if (isset($compra->mp_estado)) {
            if ($compra->mp_estado == 'approved') {
                log_message('info', 'PAGO: Compra ' . $compra->id . ' ya aprobada. Redirigiendo a compra_exitosa.');
                redirect('comedor/pago/compra_exitosa');
                return;
            }
            
            if ($compra->mp_estado == 'pending') {
                log_message('info', 'PAGO: Compra ' . $compra->id . ' en estado pendiente. Redirigiendo a compra_pendiente.');
                redirect('comedor/pago/compra_pendiente');
                return;
            }

            if ($compra->mp_estado == 'rejected') {
                log_message('info', 'PAGO: Compra ' . $compra->id . ' en estado rechazada. Redirigiendo a compra_fallida.');
                redirect('comedor/pago/compra_fallida');
                return;
            }
        }

        // URLs de retorno y notificación para Mercado Pago
        $notification_url = 'https://c648735dd6f5.ngrok-free.app/webhook/mercadopago';
        $back_urls = array(
            "success" => 'https://c648735dd6f5.ngrok-free.app/comedor/pago/compra_exitosa',
            "failure" => 'https://c648735dd6f5.ngrok-free.app/comedor/pago/compra_fallida',
            "pending" => 'https://c648735dd6f5.ngrok-free.app/comedor/pago/compra_pendiente',
        );

        $preferencia_info = $this->ticket_model->generarPreferenciaConSaldo(
            $external_reference,
            $access_token,
            $notification_url,
            $back_urls
        );

        // --- LOG DEL RESULTADO DE generarPreferenciaConSaldo ---
        log_message('debug', 'PAGO: Resultado de generarPreferenciaConSaldo: ' . ($preferencia_info === null ? 'NULL (Saldo suficiente)' : ($preferencia_info === false ? 'FALSE (Error)' : json_encode($preferencia_info))));

        // Compra con Saldo de la cuenta
        if ($preferencia_info === null) {
            // Si el saldo alcanza para cubrir toda la compra, procesar directamente
            log_message('info', 'PAGO: Saldo cubre toda la compra (' . $compra->total . '). Procesando directamente.');
            
            $procesado = $this->ticket_model->procesarCompraConSaldo($compra, (float)$compra->total);
            
            if ($procesado) {
                log_message('info', 'PAGO: Compra procesada exitosamente con saldo. Redirigiendo a compra_exitosa.');
                redirect(base_url('comedor/pago/compra_exitosa?external_reference=' . $external_reference));
                return;
            } else {
                log_message('error', 'PAGO: ERROR al procesar la compra directamente con saldo.');
                show_error('Error procesando la compra con saldo disponible.');
                return;
            }
        }

        if ($preferencia_info === false) {
            log_message('error', 'PAGO: ERROR en generarPreferenciaConSaldo (retornó FALSE).');
            show_error('Error procesando la preferencia de pago.');
            return;
        }

        // Si no se cubrió todo con saldo y se generó una preferencia de MP, redirige
        $init_point = isset($preferencia_info['init_point']) ? $preferencia_info['init_point'] : 'NO DEFINIDO';
        log_message('info', 'PAGO: Redirigiendo a Mercado Pago. init_point: ' . $init_point);
        
        // Carga el SDK de Mercado Pago
        require_once FCPATH . 'vendor/autoload.php';
        MercadoPago\SDK::setAccessToken($access_token);

        if ($init_point === 'NO DEFINIDO' || empty($init_point)) {
             log_message('error', 'PAGO: init_point de Mercado Pago es inválido o no existe. No se puede redirigir.');
             show_error('No se pudo generar la URL de pago de Mercado Pago. Intente nuevamente.');
             return;
        }
        redirect($init_point);
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
