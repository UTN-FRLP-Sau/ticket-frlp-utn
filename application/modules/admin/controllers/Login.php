<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Login extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('login_model');
    }

    public function index()
    {
        if ($this->session->userdata('is_admin')) {
            redirect(base_url('admin'));
        }

        if ($this->session->userdata('is_user')) {
            redirect(base_url('logout'));
        }

        $data = [
            'titulo' => 'Login Vendedor'
        ];

        if ($this->input->method() == 'post') {
            $nickName = $this->input->post('nick-name');
            $password = $this->input->post('password');
            $nickName = strtolower($nickName);

            if ($this->login_model->validateVendedor($nickName, md5($password))) {
                $vendedor = $this->login_model->getVendedorByUserName($nickName);
                $session = [
                    'id_vendedor'  => $vendedor->id_vendedor,
                    'nick_name' => $nickName,
                    'apellido' => $vendedor->apellido,
                    'nombre' => $vendedor->nombre,
                    'is_user' => FALSE,
                    'admin_lvl' => $vendedor->nivel,
                    'is_admin' => TRUE
                ];

                $this->session->set_userdata($session);
                redirect(base_url('admin'));
            } else {
                redirect(base_url('admin/login'));
            }
        } else {
            $this->load->view('header', $data);
            $this->load->view('login');
            $this->load->view('general/footer');
        }
    }
}