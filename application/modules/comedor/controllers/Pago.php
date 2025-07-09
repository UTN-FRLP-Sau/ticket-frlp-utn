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


        if ($preferencia_info === null) {
            // Si el saldo alcanza para cubrir toda la compra, procesar directamente
            log_message('info', 'PAGO: Saldo cubre toda la compra (' . $compra->total . '). Procesando directamente.');
            
            $procesado = $this->ticket_model->procesarCompraConSaldo($compra, (float)$compra->total);
            
            if ($procesado) {
                log_message('info', 'PAGO: Compra procesada exitosamente con saldo. Redirigiendo a compra_exitosa.');
                $this->compra_exitosa(); // Llama al método para cargar la vista de éxito
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
