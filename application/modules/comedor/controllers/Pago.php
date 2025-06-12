<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pago extends CI_Controller
{
    public function comprar()
    {
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
        MercadoPago\SDK::setAccessToken('TU_ACCESS_TOKEN'); // Reemplaza por el tuyo

        $preference = new MercadoPago\Preference();

        $item = new MercadoPago\Item();
        $item->title = "Compra de menú universitario";
        $item->quantity = 1;
        $item->unit_price = (float)$compra->total;
        $preference->items = [$item];

        $preference->external_reference = $external_reference;

        $preference->back_urls = [
            "success" => base_url("comedor/pago/compra_exitosa"),
            "failure" => base_url("comedor/pago/compra_fallida"),
            "pending" => base_url("comedor/pago/compra_pendiente")
        ];
        $preference->auto_return = "approved";
        $preference->notification_url = base_url("webhook/mercadopago");

        $preference->save();

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