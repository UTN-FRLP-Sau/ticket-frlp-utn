<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Login extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('login_model');
    }

    public function index()
    {
        if ($this->session->userdata('is_user')) {
            if ($this->session->userdata('is_admin')) {
                redirect(base_url('logout'));
            }
            redirect(base_url('usuario'));
        }

        $data = [
            'titulo' => 'Login'
        ];

        if ($this->input->method() == 'post') {
            $documento = $this->input->post('documento');
            $password = $this->input->post('password');
            $usuario = $this->login_model->getUserByDocumento($documento);

            //Verificamos que el usuario exista
            if (!$usuario) {
                $this->session->set_flashdata('error', 'El documento no se encuentra relacionado a ningun usuario activo');
                //si existe el usuario, verificamos que se encuentre activo
                redirect(base_url('login'));
            } elseif ($usuario->estado != 1) {
                $this->session->set_flashdata('error', 'El usuario relacionado a ese documento no se encuentra activo');
                //si existe y encuentra activo, validamos el login
                redirect(base_url('login'));
            } elseif ($this->login_model->validateUser($documento, md5($password))) {
                $session = [
                    'id_usuario'  => $usuario->id,
                    'apellido' => $usuario->apellido,
                    'nombre' => $usuario->nombre,
                    'documento' => $usuario->documento,
                    'is_user' => TRUE,
                    'is_admin' => FALSE,
                    'admin_lvl' => FALSE,
                ];
                $this->session->set_userdata($session);
                //Si no se valida, la contraseña es incorrecta
                redirect(base_url('usuario'));
            } else {
                $this->session->set_flashdata('error', 'Contraseña incorrecta');
                redirect(base_url('login'));
            }
        } else {
            $this->load->view('header', $data);
            $this->load->view('login');
            $this->load->view('general/footer');
        }
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect(base_url('login'), 'refresh');
    }

    public function passwordRecoveryRequest()
    {
        $data['titulo'] = 'Recuperacion de Contraseña';
        $data['tipo'] = 'solicitud';
        $documento = $this->input->post('documento');

        if ($this->input->method() == 'post') {
            $usuario = $this->login_model->getUserByDocumento($documento);

            if ($usuario) {
                //Genero un un string con el id_user_emal, para general el tocken
                $str = "{$usuario->id}_{$usuario->mail}";
                //genero el tocken
                $token = md5($str);
                if ($this->login_model->getRecoveryByToken($token)) {
                    //Si existe, informo la existencia y lo redirijo a login
                    $this->session->set_flashdata('success', "Ya existe una solicitud de rescuperacion de contraseña, por favor revise su correo");
                    redirect(base_url('login'));
                } else { //Si no existe
                    //Armo la informacion para enviar el correo
                    $data['tipo'] = 'solicitud';
                    $data['nombre'] = $usuario->nombre;
                    $data['apellido'] = $usuario->apellido;
                    $data['dni'] = $usuario->documento;
                    $data['link'] = base_url("usuario/recovery/{$token}");
                    $subject = "Solicitud de restablecimineto de contraseña";
                    $message = $this->load->view('general/correos/cambio_contraseña', $data, true);
                    //Si el correo se envia
                    if ($this->generalticket->smtpSendEmail($usuario->mail, $subject, $message)) {
                        //Genero la solicitud de contraseña en la db
                        $newLog = [
                            'fecha' => date('Y-m-d', time()),
                            'hora' => date('H:i:s', time()),
                            'id_usuario' => $usuario->id,
                            'token' => $token
                        ];
                        $this->login_model->addLogPassrecovery($newLog);
                        $this->session->set_flashdata(
                            'success',
                            "Solicitud de cambio de contraseña fue realizada correctamente y enviada a su casilla de correo"
                        );
                        redirect(base_url('login'));
                    }
                }
            } else {
                $this->session->set_flashdata('error', "No existe ninguna cuenta asociada a ese documento");
                redirect(base_url('usuario/recovery'));
            }

            $this->load->view('header', $data);
            $this->load->view('passwordRecoveryRequest', $data);
            $this->load->view('general/footer');
        }
        $this->load->view('header', $data);
        $this->load->view('passwordRecoveryRequest', $data);
        $this->load->view('general/footer');
    }

    public function newPasswordRequest()
    {
        $data['titulo'] = 'Cambio de Contraseña';
        $token_uri = $this->uri->segment(3);
        $recovery = $this->login_model->getRecoveryByToken($token_uri);

        if (!empty($recovery)) {
            if ($this->input->method() == 'post') {
                $pass1 = $this->input->post('password1');
                $pass2 = $this->input->post('password2');
                $token = $recovery->token;
                if ($pass1 == $pass2) {
                    $iduser = $recovery->id_usuario;
                    if ($this->login_model->updatePasswordById($pass1, $iduser)) {
                        $id_rec = $recovery->id;
                        $this->login_model->deleteRecoverylogById($id_rec);
                        $this->session->set_flashdata(
                            'success',
                            "La contraseña se a actualizado correctamente"
                        );
                    } else {
                        $this->session->set_flashdata(
                            'alerta',
                            "Se a producido un errror, por favor vuelva a intentarlo"
                        );
                        redirect(base_url("usuario/recovery/{$token}"));
                    }
                    redirect(base_url('login'));
                } else {
                    $this->session->set_flashdata(
                        'alerta',
                        'Las contraseñas no coinciden'
                    );
                    redirect(base_url("usuario/recovery/{$token}"));
                }
            } else {
                $this->load->view('header', $data);
                $this->load->view('newPasswordRequest', $data);
                $this->load->view('general/footer');
            }
        } else {
            $this->session->set_flashdata('error', "No es una solicitud correcta");
            redirect(base_url('usuario/recovery'));
        }
    }
}
