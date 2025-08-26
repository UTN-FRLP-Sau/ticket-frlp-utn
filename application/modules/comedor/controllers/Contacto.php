<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Contacto extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    
        $this->load->model('Configuracion_model'); 
    }

    public function index()
    {
        // Obtengo la configuración de la base de datos
        $configuracion = $this->Configuracion_model->obtener_configuracion();

        // Asigno el correo a una variable, con un valor predeterminado si no se encuentra
        $correo_contacto = isset($configuracion->correo_contacto) ? $configuracion->correo_contacto : 'sau@frlp.utn.edu.ar';

        $data = [
            'titulo' => 'Contacto',
            'correo_contacto' => $correo_contacto 
        ];

        $this->load->view('usuario/header', $data);
        $this->load->view('contacto', $data);
        $this->load->view('general/footer');
    }
}