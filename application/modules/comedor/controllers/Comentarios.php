<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Comentarios extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('comentarios_model');
    }

    public function index() {
        $data = [
            'titulo' => 'Comentarios'
        ];

        $this->load->view('usuario/header', $data);
        $this->load->view('comentarios');
        $this->load->view('general/footer');
    }

    public function agregar_comentario() 
    {
        //Verifico que el usuario esté logeado para poder enviar un comentario, si no, lo redirijo al login
        if (!$this->session->userdata('is_user')) {
            redirect(base_url('login'));
        } else {
            if ($this->input->method() == 'post'){

                // Reglas de validez de la caja de comentarios                    
                $rules = [
                    [
                        'field' => 'comentario',
                        'label' => 'comentario',
                        'rules' => 'trim|required',
                        'errors' => [
                            'required' => 'El campo comentario es obligatorio, debe ingresar un comentario'
                        ]

                    ],
                ];

                $this->form_validation->set_rules($rules);

                if($this->form_validation->run() == FALSE){
                    $data = [
                        'titulo' => 'Comentarios'
                    ];

                    $this->load->view('usuario/header', $data);
                    $this->load->view('comentarios');
                    $this->load->view('general/footer');
                } else {
                    $comentario = $this->input->post('comentario');

                    $newComment = [
                        'id_usuario' => $this->session->userdata('id_usuario'),
                        'comentario' => $comentario,
                        'fecha' => date('Y-m-d', time()),
                        'hora' => date('H:i:s', time())
                    ];

                    $this->comentarios_model->guardar_comentario($newComment);
                    $this->session->set_flashdata('success', 'Su comentario se ha enviado con éxito');
                    redirect(base_url('comentarios'));
                } 

            } else {
                redirect(base_url('comentarios'));
            }
        }    
    }
}