<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Repartidor extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('repartidor_model');
    }

    public function buscar_compra_por_fecha_user()
    {
        $id_vendedor = $this->session->userdata('id_vendedor');
        $admin = $this->repartidor_model->getAdminById($id_vendedor);
        $data['titulo']='Entrega de Viandas';
        if ($admin->nivel == 2) {
            if ($this->input->method() == 'post') {
                $documento = $this->input->post('numeroDni');
                $fecha = date('Y-m-d', time());
                if ($usuario = $this->repartidor_model->getUserByDocumento($documento)){
                    $compra = $this->repartidor_model->getComprasByFechaAndUser($fecha,$usuario->id);
                    $repartidor = $this->repartidor_model->getAdminById($compra->id_repartidor);
                } else {
                    $usuario=False;
                    $compra=False;
                    $repartidor = False;
                }
                $data['usuario'] = $usuario;
                $data['compra'] = $compra;
                $data['repartidor'] = $repartidor;
                $this->load->view('header', $data);
                $this->load->view('index', $data);
                $this->load->view('general/footer');
            } else {
                redirect(base_url('admin'));
            }

        } else {
            redirect(base_url('usuario'));
        }
    }

    public function entregar_compra_by_id()
    {
        $id_vendedor = $this->session->userdata('id_vendedor');
        $admin = $this->repartidor_model->getAdminById($id_vendedor);
        $data['titulo']='Entrega de Viandas';
        if ($admin->nivel == 2) {
            if ($this->input->method() == 'post') {
                $id_compra = $this->input->post('idCompra');
                $compra = $this->repartidor_model->getComprasByID($id_compra);
                $usuario = $this->repartidor_model->getUserByID($compra->id_usuario);
                $this->repartidor_model->updateCompraByID($id_compra, $id_vendedor);
                $compra = $this->repartidor_model->getComprasByID($id_compra);
                $repartidor = $this->repartidor_model->getAdminById($compra->id_repartidor);
                $data['usuario'] = $usuario;
                $data['repartidor'] = $repartidor;
                $data['compra'] = $compra;
                $this->load->view('header', $data);
                $this->load->view('index', $data);
                $this->load->view('general/footer');
            } else {
                redirect(base_url('admin'));
            }
        } else {
            redirect(base_url('usuario'));
        }
    }

    public function entregar_vianda()
    {
       $id_vendedor = $this->session->userdata('id_vendedor');
        $admin = $this->repartidor_model->getAdminById($id_vendedor);
        if ($admin->nivel == 2) {

        } else {
            redirect(base_url('usuario'));
        }
    }

    public function historial_entregas_by_fecha()
    {
        $id_vendedor = $this->session->userdata('id_vendedor');
        $admin = $this->repartidor_model->getAdminById($id_vendedor);
        if ($admin->nivel == 2) {
            $time_num = $this->uri->segment(4);
            $fecha = date('Y-m-d', $time_num);
            $compras_registros = $this->repartidor_model->getComprasByDiaComprado($fecha);
            $compras = array();
            foreach ($compras_registros as $compra) {
                $usuario = $this->repartidor_model->getUserByID($compra->id_usuario);
                $repartidor = $this->repartidor_model->getAdminById($compra->id_repartidor);

                $compra_i['documento'] = $usuario->documento;
                $compra_i['nombre'] = ucwords($usuario->nombre);
                $compra_i['apellido'] = strtoupper($usuario->apellido);
                $compra_i['menu'] = $compra->menu;
                $compra_i['turno'] = $compra->turno;
                $compra_i['retiro'] = $compra->retiro;
                if ($compra->id_repartidor != 0){
                    $compra_i['repartidor'] = $repartidor->nombre_usuario;
                }

                array_push($compras, $compra_i);
            }
            $data['titulo'] = 'Asistencia al comedor';
            $data['compras'] = $compras;
            $data['fecha'] = $fecha;

            $this->load->view('header', $data);
            $this->load->view('asistencia', $data);
            $this->load->view('general/footer');
        } else {
            redirect(base_url('usuario'));
        }
    }
}