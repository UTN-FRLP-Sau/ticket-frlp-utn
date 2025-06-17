<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pago extends CI_Controller
{
        


    public function comprar()
    {
        $this->config->load('ticket');

        $access_token = $this->config->item('MP_ACCESS_TOKEN');
        $public_key = $this->config->item('MP_PUBLIC_KEY');
        $webhook_secret = $this->config->item('MP_WEBHOOK_SECRET');

        $external_reference = $this->session->userdata('external_reference');
        if (!$external_reference) {
            redirect('comedor/ticket');
        }

        $this->load->model('ticket_model');
        $compra = $this->ticket_model->getCompraPendiente($external_reference);

        if (!$compra) {
            redirect('comedor/ticket');
        }

        require_once FCPATH . 'vendor/autoload.php';
        MercadoPago\SDK::setAccessToken($access_token);

        $preference = new MercadoPago\Preference();

        $item = new MercadoPago\Item();
        $item->title = "Compra de menú universitario";
        $item->quantity = 1;
        $item->unit_price = (float)$compra->total;
        $preference->items = [$item];

        $preference->external_reference = $external_reference;

        $ngrok_url = 'https://640a-200-10-126-116.ngrok-free.app';

        $preference->back_urls = [
            "success" => $ngrok_url . "/comedor/pago/compra_exitosa",
            "failure" => $ngrok_url . "/comedor/pago/compra_fallida",
            "pending" => $ngrok_url . "/comedor/pago/compra_pendiente"
        ];
        $preference->auto_return = "approved";
        $preference->notification_url = $ngrok_url . "/webhook/mercadopago";

        $saved = $preference->save();

        if (!$saved) {
            log_message('error', 'Error guardando preferencia Mercado Pago: ' . print_r($preference->getLastApiResponse(), true));
            show_error('No se pudo procesar la preferencia de pago');
        }

        redirect($preference->init_point);
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
