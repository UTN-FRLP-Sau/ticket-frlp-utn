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
        // 1. Verificación si el usuario ya está logueado
        if ($this->session->userdata('is_user')) {
            if ($this->session->userdata('is_admin')) {
                redirect(base_url('logout'));
            }
            // Si es usuario normal y ya logueado, redirige al panel de usuario
            redirect(base_url('usuario'));
        }

        $data = [
            'titulo' => 'Login'
        ];

        // 2. Manejo de la solicitud POST (cuando el usuario envía el formulario de login)
        if ($this->input->method() == 'post') {
            $documento = $this->input->post('documento');
            $password = $this->input->post('password');
            $usuario = $this->login_model->getUserByDocumento($documento);

            // Verificamos que el usuario exista
            if (!$usuario) {
                $this->session->set_flashdata('error', 'El documento no se encuentra relacionado a ningun usuario activo');
                redirect(base_url('login'));
            }
            // Si existe el usuario, verificamos que se encuentre activo
            elseif ($usuario->estado != 0 && $usuario->estado != 2) {
                $this->session->set_flashdata('error', 'El usuario relacionado a ese documento no se encuentra activo');
                redirect(base_url('login'));
            }
            // Para cuando la plataforma esta en mantenimiento
            elseif ($usuario->estado == 2) {
                $this->session->set_flashdata('error', 'La plataforma se encuentra en mantenimiento, por favor intente más tarde.');
                redirect(base_url('login'));
            }
            // Si existe y está activo, validamos el login con la contraseña
            elseif ($this->login_model->validateUser($documento, md5($password))) {
                // Autenticación exitosa: Establecer datos de sesión
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
                
                // Limpieza de registros compras pendientes
                $this->load->library('cron_tasks');
                // Llama al método de limpieza de la librería, pasándole el ID del usuario logueado
                $this->cron_tasks->cleanupUserExpiredOrders($usuario->id);

                // Redirigir al usuario al panel después de la autenticación y la limpieza
                redirect(base_url('usuario'));
            }
            // Si no se valida, la contraseña es incorrecta
            else {
                $this->session->set_flashdata('error', 'Contraseña incorrecta');
                redirect(base_url('login'));
            }
        }
        // 3. Manejo de la solicitud GET (cuando el usuario visita la página de login)
        else {
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
                    $this->session->set_flashdata('success', "Ya existe una solicitud de recuperacion de contraseña, por favor revise su correo");
                    redirect(base_url('login'));
                } else { //Si no existe
                    //Armo la informacion para enviar el correo
                    $data['tipo'] = 'solicitud';
                    $data['nombre'] = $usuario->nombre;
                    $data['apellido'] = $usuario->apellido;
                    $data['dni'] = $usuario->documento;
                    $data['link'] = base_url("usuario/recovery/{$token}");
                    $subject = "Solicitud de restablecimiento de contraseña";
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
                            "La contraseña se ha actualizado correctamente"
                        );
                    } else {
                        $this->session->set_flashdata(
                            'alerta',
                            "Se ha producido un error, por favor vuelva a intentarlo"
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