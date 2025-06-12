<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Terminos extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->load->model('terminos_model');

        if (!$this->session->userdata('is_user')) {
            redirect(base_url('login'));
        }
    }
    public function index() 
    {
        $user_id = $this->session->userdata('id_usuario');
        $user_accepted_terms = $this->terminos_model->validarAceptarTerminos($user_id);

        if ($user_accepted_terms) {
            // El usuario ya aceptó los términos, redirige a la página principal
            redirect(base_url('usuario'));
        }

        $data = [
            'titulo' => 'Términos y condiciones'
        ];

        // $this->load->view('usuario/header', $data);
        $this->load->view('terminos_condiciones');
        // $this->load->view('general/footer');
    }

    public function aceptarTerminos() {
        $user_id = $this->session->userdata('id_usuario');
    
        $user_accepted_terms = $this->terminos_model->validarAceptarTerminos($user_id);
    
        if (!$user_accepted_terms) {
            // Registro la aceptación en la tabla de términos
            $data = [
                'id_usuario' => $user_id,
            ];
            $this->terminos_model->aceptar_terminos($data);
        }
    
        // Redirige al usuario a la página principal del comedor
        redirect(base_url('usuario'));
    }

    public function rechazarTerminos() 
    {
        // Destruyo la sesión y redirige al login de usuario
        $this->session->sess_destroy();
        redirect(base_url('login'), 'refresh');
    }
}
