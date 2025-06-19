<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pago extends CI_Controller
{
    public function comprar()
    {
        $this->config->load('ticket');

        $access_token = $this->config->item('MP_ACCESS_TOKEN');

        $external_reference = $this->session->userdata('external_reference');
        if (!$external_reference) {
            redirect('comedor/ticket');
        }

        $this->load->model('ticket_model');
        $compra = $this->ticket_model->getCompraPendiente($external_reference);

        if (!$compra) {
            redirect('comedor/ticket');
        }

        $notificacion_url = 'https://8ca0-200-10-126-116.ngrok-free.app/webhook/mercadopago';
        $back_urls = [
            "success" => 'https://8ca0-200-10-126-116.ngrok-free.app/comedor/pago/compra_exitosa',
            "failure" => 'https://8ca0-200-10-126-116.ngrok-free.app/comedor/pago/compra_fallida',
            "pending" => 'https://8ca0-200-10-126-116.ngrok-free.app/comedor/pago/compra_pendiente'
        ];

        // Usar la función del modelo que maneja saldo y preferencia MP
        $preferencia_info = $this->ticket_model->generarPreferenciaConSaldo(
            $external_reference,
            $access_token,
            $notificacion_url,
            $back_urls
        );

        if ($preferencia_info === null) {
            // El saldo alcanza para cubrir toda la compra, procesar directamente
            $saldo_usuario = $this->ticket_model->getSaldoByIDUser($compra->id_usuario);
            $procesado = $this->ticket_model->procesarCompraConSaldo($compra, (float)$compra->total);
            if ($procesado) {
                $this->compra_exitosa();
                return;  // Para que no siga y no redirija a MP
            } else {
                show_error('Error procesando la compra con saldo disponible.');
            }
        }

        if ($preferencia_info === false) {
            show_error('Error procesando la preferencia de pago.');
        }

        // Si no se cubre todo con saldo, redirigir a Mercado Pago
        require_once FCPATH . 'vendor/autoload.php';
        MercadoPago\SDK::setAccessToken($access_token);

        redirect($preferencia_info['init_point']);
    }

    public function compra_exitosa()
    {
        $this->load->view('usuario/header', ['titulo' => '¡Pago exitoso!']);
        $this->load->view('compra_exitosa');
        $this->load->view('general/footer');
    }

    public function compra_fallida()
    {
        $this->load->view('usuario/header', ['titulo' => 'Pago fallido']);
        $this->load->view('compra_fallida');
        $this->load->view('general/footer');
    }

    public function compra_pendiente()
    {
        $this->load->view('usuario/header', ['titulo' => 'Pago pendiente']);
        $this->load->view('compra_pendiente');
        $this->load->view('general/footer');
    }
}
