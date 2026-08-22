<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Usuario extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('usuario_model');

        if (!$this->session->userdata('is_user')) {
            if ($this->session->userdata('is_admin')) {
                redirect(base_url('logout'));
            }
            redirect(base_url('login'));
        }
    }

    public function changePassword()
    {
        $data = [
            'titulo' => 'Cambio de contraseña'
        ];
        $id_user = $this->session->userdata('id_usuario');
        $usuario = $this->usuario_model->getUserByID($id_user);
        $data['usuario'] = $usuario;

        if ($this->input->method() == 'post') {
            $rules = [
                [
                    'field' => 'password_anterior',
                    'label' => 'Contraseña',
                    'rules' => 'trim|required',
                    'errors' => [
                        'required' => 'Debe ingresar su %s actual.'
                    ]
                ],
                [
                    'field' => 'password_nuevo',
                    'label' => 'Contraseña Nueva',
                    'rules' => 'trim|required',
                    'errors' => [
                        'required' => 'Debe ingresar una %s.'
                    ]
                ],
                [
                    'field' => 'password_confirmado',
                    'label' => 'Contraseña',
                    'rules' => 'trim|matches[password_nuevo]',
                    'errors' => [
                        'matches' => 'Las contraseñas no coinciden.'
                    ]
                ]
            ];
            $this->form_validation->set_rules($rules);
            if ($this->form_validation->run() == FALSE) {
                $this->load->view('header', $data);
                $this->load->view('change_password', $data);
                $this->load->view('general/footer');
            } else {
                $password_anterior = $this->input->post('password_anterior');
                $password_nuevo = $this->input->post('password_nuevo');
                $password = $usuario->pass;
                if ($password == md5($password_anterior)) {
                    if ($this->usuario_model->updatePassword($id_user, md5($password_nuevo))) {
                        $this->session->set_flashdata(
                            'success',
                            'Contraseña actualizada correctamente'
                        );
                        redirect(base_url('usuario/cambio-password'));
                    }
                } else {
                    $this->session->set_flashdata(
                        'error',
                        'Contraseña incorrecta'
                    );
                    redirect(base_url('usuario/cambio-password'));
                }
            }
        } else {
            $this->load->view('header', $data);
            $this->load->view('change_password');
            $this->load->view('general/footer');
        }
    }

    public function notificaciones()
    {
        $data = [
            'titulo' => 'Mis notificaciones'
        ];
        $id_user = $this->session->userdata('id_usuario');

        if ($this->input->method() == 'post') {
            $preferencias = [
                'notif_recordatorio_compra' => $this->input->post('notif_recordatorio_compra') ? 1 : 0,
                'notif_recordatorio_retiro' => $this->input->post('notif_recordatorio_retiro') ? 1 : 0,
            ];
            if ($this->usuario_model->updatePreferenciasNotificacion($id_user, $preferencias)) {
                $this->session->set_flashdata(
                    'success',
                    'Preferencias de notificaciones actualizadas correctamente'
                );
            }
            redirect(base_url('usuario/notificaciones'));
        } else {
            $data['preferencias'] = $this->usuario_model->getPreferenciasNotificacion($id_user);

            $this->load->view('header', $data);
            $this->load->view('notificaciones', $data);
            $this->load->view('general/footer');
        }
    }

    public function perfil()
    {
        $data = [
            'titulo' => 'Mi perfil'
        ];
        $id_user = $this->session->userdata('id_usuario');
        $usuario = $this->usuario_model->getPerfil($id_user);

        if ($this->input->method() == 'post') {
            $unique_email = ($this->input->post('email') != $usuario->mail) ? '|is_unique[usuarios.mail]' : '';

            $rules = [
                [
                    'field' => 'email',
                    'label' => 'E-Mail',
                    'rules' => "trim|required|valid_email{$unique_email}",
                    'errors' => [
                        'required' => 'Debe ingresar un %s',
                        'valid_email' => 'No es un %s valido',
                        'is_unique' => 'Ese %s ya esta registrado',
                    ]
                ],
            ];

            // El legajo solo es editable mientras el usuario sea aspirante
            // (todavía no tiene legajo oficial). No se confía en el estado
            // del campo en el HTML: se revalida server-side.
            if ($usuario->aspirante == 1) {
                $unique_legajo = ($this->input->post('legajo') != $usuario->legajo) ? '|is_unique[usuarios.legajo]' : '';
                $rules[] = [
                    'field' => 'legajo',
                    'label' => 'Legajo',
                    'rules' => "trim|min_length[5]|max_length[6]|required|numeric|integer{$unique_legajo}",
                    'errors' => [
                        'max_length' => 'El %s debe contener entre 5 y 6 digitos',
                        'min_length' => 'El %s debe contener entre 5 y 6 digitos',
                        'required' => 'Debe ingresar un %s',
                        'numeric' => 'El %s debe ser un numero',
                        'integer' => 'El %s debe ser un entero',
                        'is_unique' => 'Ese %s ya esta registrado',
                    ]
                ];
            }

            $this->form_validation->set_rules($rules);
            if ($this->form_validation->run() == FALSE) {
                $data['usuario'] = $usuario;
                $this->load->view('header', $data);
                $this->load->view('perfil', $data);
                $this->load->view('general/footer');
            } else {
                $updateData = [];
                $mensajes_success = [];

                if ($usuario->aspirante == 1) {
                    $updateData['legajo'] = $this->input->post('legajo');
                    $updateData['aspirante'] = 0;
                    $mensajes_success[] = 'legajo actualizado correctamente';
                }

                if (!empty($updateData)) {
                    $this->usuario_model->updatePerfil($id_user, $updateData);
                }

                // El mail no se aplica directo: queda pendiente de confirmación
                // en la casilla nueva (ver Login::confirmarCorreo()).
                $mail_nuevo = strtolower($this->input->post('email'));
                if ($mail_nuevo != $usuario->mail) {
                    $this->load->model('login_model');

                    if (!$this->login_model->getMailConfirmacionPendiente($id_user, $mail_nuevo)) {
                        $token = bin2hex(random_bytes(16));
                        $emailData = [
                            'nombre' => $usuario->nombre,
                            'apellido' => $usuario->apellido,
                            'dni' => $usuario->documento,
                            'link' => base_url("usuario/confirmar-correo/{$token}"),
                        ];
                        $subject = 'Confirmá tu nuevo correo';
                        $message = $this->load->view('general/correos/confirmar_correo', $emailData, true);

                        if ($this->generalticket->smtpSendEmail($mail_nuevo, $subject, $message)) {
                            $this->login_model->addMailConfirmacion([
                                'fecha' => date('Y-m-d', time()),
                                'hora' => date('H:i:s', time()),
                                'id_usuario' => $id_user,
                                'mail_nuevo' => $mail_nuevo,
                                'token' => $token,
                            ]);
                        }
                    }
                    $mensajes_success[] = 'te enviamos un correo a tu nueva casilla para confirmar el cambio de mail';
                }

                if (!empty($mensajes_success)) {
                    $this->session->set_flashdata(
                        'success',
                        ucfirst(implode(', y ', $mensajes_success)) . '.'
                    );
                } else {
                    $this->session->set_flashdata('success', 'No se detectaron cambios para guardar.');
                }
                redirect(base_url('usuario/perfil'));
            }
        } else {
            $data['usuario'] = $usuario;
            $this->load->view('header', $data);
            $this->load->view('perfil', $data);
            $this->load->view('general/footer');
        }
    }

    public function ultimosMovimientos()
    {
        $data['titulo'] = 'Ultimos movimientos';

        $id_usuario = $this->session->userdata('id_usuario');
        $limit_por_pagina = 10;
        $start_index = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $total_records = count($this->usuario_model->getTransaccionesByIdUser($id_usuario));
        $start_index = floor($start_index / 10) * 10;


        $data['ultimo'] = floor($total_records / $limit_por_pagina) * 10;

        $data['compras'] = $this->usuario_model->getTransaccinesInRangeByIDUser($limit_por_pagina, $start_index, $id_usuario);
        //Esta parte arma los botones de la paginacion
        if ($start_index == 0) {
            //Si el id es 0, estamos en la primera pagina, y lo seteamos
            $data['primera'] = 1;
        } elseif ($start_index <= 10) {
            $links[] = [
                'id' => 0,
                'num' => floor($start_index / 10)
            ];
        } elseif ($start_index > 10) {
            $links[] = [
                'id' => $start_index - 10,
                'num' => floor($start_index / 10)
            ];
        }

        $links[] = [
            'id' => $start_index,
            'num' => floor($start_index / 10) + 1,
            'act' => 'active'
        ];

        if ($start_index + 10 <= $total_records) {
            $links[] = [
                'id' => $start_index + 10,
                'num' => floor($start_index / 10) + 2
            ];
        } else {
            $data['ultima'] = 1;
        }
        if ($total_records > $limit_por_pagina) {
            $data['links'] = $links;
        }

        $this->load->view('header', $data);
        $this->load->view('historial', $data);
        $this->load->view('general/footer');
    }

    public function botones_de_pago()
    {
        $data = [
            'titulo' => 'Carga por Link de pago'
        ];
        $id_user = $this->session->userdata('id_usuario');
        $usuario = $this->usuario_model->getUserByID($id_user);
        $cargas_virtuales = $this->usuario_model->getCargasVirtualesByUserID($id_user);
        $data['usuario'] = $usuario;
        $data['saldo'] = $usuario->saldo;
        $data['monto_acreditar'] = !empty($cargas_virtuales)
            ? array_sum(array_column($cargas_virtuales, 'monto'))
            : 0;
        $data['links'] = $this->usuario_model->getLinkByUserType($usuario->tipo);

        $this->load->view('header', $data);
        $this->load->view('bonotes_pago', $data);
        $this->load->view('general/footer');
    }

    public function add_carga_virtual()
    {
        $id_user = $this->session->userdata('id_usuario');
        $usuario = $this->usuario_model->getUserByID($id_user);
        if ($this->input->method() == 'post') {
            $id_link = $this->input->post('id_link');
            $link_pago = $this->usuario_model->getLinkByID($id_link);
            $link_mp = $link_pago->link;
            $nuevaCarga =[
                'usuario'=> $usuario->id,
                'timestamp' => date('Y-m-d H:i:s'),
                'monto' => $link_pago->valor,
                'estado' => 'revision',
            ];
            if ($this->usuario_model->createCargaVirtual($nuevaCarga)) {
                //echo "<script>window.open('$link_mp', '_blank')</script>";
                redirect($link_mp);
                //redirect(base_url('usuario/carga_virtual/add'));
            } else {
                redirect(base_url('usuario/carga_virtual/add'));
            }
        } else {
            redirect(base_url('usuario/carga_virtual/add'));
        }
    }

}