<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Registro extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->model('usuario/Usuario_model'); 
    }

    public function registro() {
        $this->form_validation->set_rules('nombre', 'Nombre', 'required|trim|min_length[2]|max_length[50]');
        $this->form_validation->set_rules('apellido', 'Apellido', 'required|trim|min_length[2]|max_length[50]');
        $this->form_validation->set_rules('dni', 'DNI', 'required|trim|numeric|max_length[20]|is_unique[usuarios.documento]');
        $this->form_validation->set_rules('legajo', 'Legajo', 'required|trim|numeric|is_unique[usuarios.legajo]');
        $this->form_validation->set_rules('email', 'Correo Electrónico', 'required|trim|valid_email|is_unique[usuarios.mail]');
        $this->form_validation->set_rules('password', 'Contraseña', 'required|min_length[6]|matches[passconf]');
        $this->form_validation->set_rules('passconf', 'Confirmar Contraseña', 'required');
        $this->form_validation->set_rules('claustro', 'Claustro', 'required|in_list[Alumno,No docente,Docente]');
        $this->form_validation->set_rules('carrera', 'Carrera', 'trim');

        $this->form_validation->set_message('required', 'El campo %s es obligatorio.');
        $this->form_validation->set_message('min_length', 'El campo %s debe tener al menos %s caracteres.');
        $this->form_validation->set_message('max_length', 'El campo %s no debe superar los %s caracteres.');
        $this->form_validation->set_message('numeric', 'El campo %s solo puede contener números.');
        $this->form_validation->set_message('exact_length', 'El campo %s debe tener exactamente %s dígitos.');
        $this->form_validation->set_message('valid_email', 'El campo %s debe ser una dirección de correo válida.');
        $this->form_validation->set_message('is_unique', 'El %s ya está registrado.');
        $this->form_validation->set_message('matches', 'Las contraseñas no coinciden.');
        $this->form_validation->set_message('in_list', 'El campo %s debe ser uno de los valores permitidos.');

        $data = array();
        $data['titulo'] = 'Registro de Usuario - Comedor';

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('header', $data);
            $this->load->view('usuario/registro');
            $this->load->view('general/footer');
        } else {
            $tipo_usuario = $this->input->post('claustro');
            $certificado_path = null;
            $error_upload = false;

            // Lógica condicional para la subida del archivo
            if ($tipo_usuario == 'Alumno') {
                $upload_path = './uploads/certificados/';

                if (!is_dir($upload_path)) {
                    mkdir($upload_path, 0777, TRUE);
                }

                $config['upload_path']   = $upload_path;
                $config['allowed_types'] = 'gif|jpg|jpeg|png|pdf';
                $config['max_size']      = 8192; // (8 MB)
                $config['encrypt_name']  = TRUE;
                
                $this->load->library('upload', $config);
                
                if (!$this->upload->do_upload('userfile')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', strip_tags($error));
                    $error_upload = true;
                } else {
                    $upload_data = $this->upload->data();
                    $certificado_path = 'uploads/certificados/' . $upload_data['file_name'];
                }
            }

            // Si no hubo un error en la subida (o si no era necesario subir un archivo)
            if (!$error_upload) {
                $id_precio = 1;

                if ($tipo_usuario == 'Docente') {
                    $id_precio = 3;
                } elseif ($tipo_usuario == 'No docente') {
                    $id_precio = 4;
                } elseif ($tipo_usuario == 'Alumno') {
                    $becado = $this->input->post('beca');
                    if (isset($becado) && $becado == 'Si') {
                        $id_precio = 2;
                    }
                }
                
                $data_usuario = array(
                    'nombre'           => $this->input->post('nombre'),
                    'apellido'         => $this->input->post('apellido'),
                    'documento'        => $this->input->post('dni'), 
                    'legajo'           => $this->input->post('legajo'),
                    'mail'             => $this->input->post('email'), 
                    'pass'             => md5($this->input->post('password')),
                    'tipo'             => $tipo_usuario, 
                    'especialidad'     => ($tipo_usuario == 'Alumno') ? $this->input->post('carrera') : null,
                    'certificado_path' => $certificado_path,
                    'id_precio'        => $id_precio
                );

                if ($this->Usuario_model->registrar_usuario($data_usuario)) {
                    $this->session->set_flashdata('success', 'Registro exitoso. Tu cuenta será revisada por un administrador. Recibirás un correo cuando se apruebe.');
                    redirect('login');
                } else {
                    $this->session->set_flashdata('error', 'Ocurrió un error al registrar el usuario. Por favor, inténtalo de nuevo.');
                    redirect('usuario/registro');
                }
            } else {
                // Si hubo un error de subida, volver a cargar la vista
                $this->load->view('header', $data);
                $this->load->view('usuario/registro');
                $this->load->view('general/footer');
            }
        }
    }
}