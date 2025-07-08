<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Faq extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data = [
            'titulo' => 'Preguntas Frecuentes'
        ];

        $this->load->view('usuario/header', $data);

        $this->load->view('comedor/faq', $data);


        $this->load->view('general/footer');
    }
}