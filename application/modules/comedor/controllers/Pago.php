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
            return; // Es buena práctica añadir return después de un redirect.
        }

        $this->load->model('ticket_model');
        $compra = $this->ticket_model->getCompraPendiente($external_reference);
        // --- LOG DE COMPRA PENDIENTE ---
        log_message('debug', 'PAGO: Resultado de getCompraPendiente para ' . $external_reference . ': ' . ($compra ? json_encode($compra) : 'NO ENCONTRADA'));

        if (!$compra) {
            // --- LOG DE REDIRECCIÓN POR COMPRA NO ENCONTRADA ---
            log_message('error', 'PAGO: Compra pendiente no encontrada para external_reference: ' . $external_reference . '. Redirigiendo a comedor/ticket.');
            redirect('comedor/ticket');
            return; // Añadir return.
        }

        $notificacion_url = 'https://d89e-200-10-126-116.ngrok-free.app/webhook/mercadopago';
        $back_urls = [
            "success" => 'https://d89e-200-10-126-116.ngrok-free.app/comedor/pago/compra_exitosa',
            "failure" => 'https://d89e-200-10-126-116.ngrok-free.app/comedor/pago/compra_fallida',
            "pending" => 'https://d89e-200-10-126-116.ngrok-free.app/comedor/pago/compra_pendiente'
        ];
        // --- LOG DE URLs DE RETORNO ---
        log_message('debug', 'PAGO: back_urls configuradas: ' . json_encode($back_urls));

        // Usar la función del modelo que maneja saldo y preferencia MP
        $preferencia_info = $this->ticket_model->generarPreferenciaConSaldo(
            $external_reference,
            $access_token,
            $notificacion_url,
            $back_urls
        );

        // --- LOG DEL RESULTADO DE generarPreferenciaConSaldo ---
        log_message('debug', 'PAGO: Resultado de generarPreferenciaConSaldo: ' . ($preferencia_info === null ? 'NULL (Saldo suficiente)' : ($preferencia_info === false ? 'FALSE (Error)' : json_encode($preferencia_info))));


        if ($preferencia_info === null) {
            // El saldo alcanza para cubrir toda la compra, procesar directamente
            log_message('info', 'PAGO: Saldo cubre toda la compra (' . $compra->total . '). Procesando directamente.');
            $saldo_usuario = $this->ticket_model->getSaldoByIDUser($compra->id_usuario); // Puede ser útil para depuración, aunque el procesado lo hace el modelo.
            $procesado = $this->ticket_model->procesarCompraConSaldo($compra, (float)$compra->total);
            
            if ($procesado) {
                log_message('info', 'PAGO: Compra procesada exitosamente con saldo. Redirigiendo a compra_exitosa.');
                $this->compra_exitosa();
                return; // Para que no siga y no redirija a MP
            } else {
                log_message('error', 'PAGO: ERROR al procesar la compra directamente con saldo.');
                show_error('Error procesando la compra con saldo disponible.');
            }
        }

        if ($preferencia_info === false) {
            log_message('error', 'PAGO: ERROR en generarPreferenciaConSaldo (retornó FALSE).');
            show_error('Error procesando la preferencia de pago.');
        }

        // Si no se cubre todo con saldo, redirigir a Mercado Pago
        // --- LOG ANTES DE REDIRIGIR A MERCADO PAGO ---
        $init_point = isset($preferencia_info['init_point']) ? $preferencia_info['init_point'] : 'NO DEFINIDO';
        log_message('info', 'PAGO: Redirigiendo a Mercado Pago. init_point: ' . $init_point);
        
        // Asegúrate de que MercadoPago SDK esté incluido y el Access Token seteado
        require_once FCPATH . 'vendor/autoload.php';
        MercadoPago\SDK::setAccessToken($access_token);

        if ($init_point === 'NO DEFINIDO' || empty($init_point)) {
             log_message('error', 'PAGO: init_point de Mercado Pago es inválido o no existe. No se puede redirigir.');
             show_error('No se pudo generar la URL de pago de Mercado Pago. Intente nuevamente.');
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