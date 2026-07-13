<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Faq extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->load->model('ticket_model');
        $configuracion = $this->ticket_model->getConfiguracion();

        $data = [
            'titulo' => 'Preguntas Frecuentes',
            'configuracion' => $configuracion
        ];

        $this->load->view('usuario/header', $data);

        $this->load->view('comedor/faq', $data);


        $this->load->view('general/footer');
    }
}