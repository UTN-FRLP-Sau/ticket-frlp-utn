<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Administrador extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('administrador_model');


        if ($this->session->userdata('is_user')) {
            redirect(base_url('usuario'));
        }

        if (!$this->session->userdata('is_admin')) {
            redirect(base_url('admin/login'));
        }
    }

    public function createVendedor()
    {
        $id_vendedor = $this->session->userdata('id_vendedor');
        $admin = $this->administrador_model->getAdminById($id_vendedor);
        if ($admin->nivel == 1) {
            $data['titulo'] = 'Crear nuevo Vendedor';

            if ($this->input->method() == 'post') {
                $rules = [
                    [
                        'field' => 'nickName',
                        'label' => 'Nombre de Usuario',
                        'rules' => "trim|min_length[3]|required|max_length[20]|alpha|is_unique[vendedores.nombre_usuario]",
                        'errors' => [
                            'max_length' => 'El %s debe contener 3 y 20 letras',
                            'min_length' => 'El %s debe contener 3 y 20 letras',
                            'required' => 'Debe ingresar un %s',
                            'alpha' => 'El %s debe contener solo letras',
                            'is_unique' => 'Ese %s ya esta registrado',
                        ]
                    ],
                    [
                        'field' => 'nombre',
                        'label' => 'Nombre',
                        'rules' => 'trim|max_length[50]|alpha_numeric_spaces|required',
                        'errors' => [
                            'max_length' => 'El %s debe contener como maximo 50 caracteres',
                            'required' => 'Debe ingresar un %s',
                            'alpha_numeric_spaces' => 'Solo se aceptan letras en el %s',
                        ]
                    ],
                    [
                        'field' => 'apellido',
                        'label' => 'Apellido',
                        'rules' => 'trim|max_length[50]|alpha_numeric_spaces|required',
                        'errors' => [
                            'max_length' => 'El %s debe contener como maximo 50 caracteres',
                            'required' => 'Debe ingresar un %s',
                            'alpha_numeric_spaces' => 'Solo se aceptan letras en el %s',
                        ]
                    ],
                    [
                        'field' => 'documento',
                        'label' => 'Documento',
                        'rules' => 'trim|max_length[8]|numeric|required',
                        'errors' => [
                            'max_length' => 'El %s debe contener como maximo 8 digitos',
                            'required' => 'Debe ingresar un %s',
                            'numeric' => 'Solo se aceptan numeros %s',
                        ]
                    ],
                    [
                        'field' => 'email',
                        'label' => 'E-Mail',
                        'rules' => "trim|valid_email|required|is_unique[vendedores.mail]",
                        'errors' => [
                            'required' => 'Debe ingresar un %s',
                            'valid_email' => 'No es un %s valido',
                            'is_unique' => 'Ese %s ya esta registrado',
                        ]
                    ],
                    [
                        'field' => 'tipo',
                        'label' => 'Tipo',
                        'rules' => "trim|max_length[2]|required|numeric",
                        'errors' => [
                            'numeric' => 'Seleccione un tipo valido %s',
                        ]
                    ],
                ];
                $this->form_validation->set_rules($rules);
                if ($this->form_validation->run() != FALSE) {
                    $password = $this->input->post('documento');
                    $newUser = [
                        'nombre_usuario' => $this->input->post('nickName'),
                        'pass' => md5($password),
                        'nombre' => ucwords($this->input->post('nombre')),
                        'apellido' => ucwords($this->input->post('apellido')),
                        'mail' => strtolower($this->input->post('email')),
                        'estado' => 1,
                        'nivel' => $this->input->post('tipo')
                    ];

                    if ($this->administrador_model->addNewVendedor($newUser)) {
                        //Confeccion del correo para el nuevo vendedor
                        $correo = strtolower($this->input->post('email'));
                        $dataCorreo['apellido'] = $this->input->post('apellido');
                        $dataCorreo['nombre'] = $this->input->post('nombre');
                        $dataCorreo['nickName'] = $this->input->post('nickName');
                        $dataCorreo['lvl'] = 0;
                        $dataCorreo['password'] = $password;

                        $subject = "Bienvenido al Comedor Universitario UTN-FRLP";
                        $message = $this->load->view('general/correos/nuevo_vendedor', $dataCorreo, true);
                        if ($this->generalticket->smtpSendEmail($correo, $subject, $message)) {
                            redirect(base_url('admin/csv_carga'));
                        };
                        redirect(base_url('admin'));
                    }
                }
            }

            $this->load->view('header', $data);
            $this->load->view('crear_vendedor', $data);
            $this->load->view('general/footer');
        } else {
            redirect(base_url('admin'));
        }
    }

    public function cargar_archivo_csv()
    {
        $data['uploadSuccess'] = array();
        $data['titulo'] = 'Carga CSV';

        if ($this->input->method() == 'post') {
            $mi_archivo = 'archivo_csv';
            $separador = $this->input->post('separador');
            $config['upload_path'] = "uploads";
            $config['file_name'] = "carga";
            $config['overwrite'] = TRUE;
            $config['allowed_types'] = "csv";
            $config['max_size'] = "50000";
            $config['max_width'] = "2000";
            $config['max_height'] = "2000";

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload($mi_archivo)) {
                //*** ocurrio un error
                $data['subidoError'] = $this->upload->display_errors();
                $this->load->view('header', $data);
                $this->load->view('carga_csv', $data);
                $this->load->view('general/footer');
            } else {
                $data['subidoCorrecto'] = $this->upload->data();
                $file = fopen('uploads/carga.csv', 'r');
                $head = fgetcsv($file, 0, $separador);
                while ((!feof($file)) && ($file != '')) {
                    $cargas[] = fgetcsv($file, 0, $separador);
                }
                fclose($file);

                $data['cargas'] = $cargas;

                $this->load->view('header', $data);
                $this->load->view('carga_csv', $data);
                $this->load->view('general/footer');
            }
        } else {
            $this->load->view('header', $data);
            $this->load->view('carga_csv', $data);
            $this->load->view('general/footer');
        }
    }

    public function confirmarCargasCVS()
    {
        if ($this->input->method() == 'post') {
            $i = 0;
            while ($this->input->post("documento_{$i}")) {
                $fecha = $this->input->post("fecha_{$i}");
                $documento = $this->input->post("documento_{$i}");
                $monto = $this->input->post("monto_{$i}");
                $nombre = $this->input->post("nombre_{$i}");
                $apellido = $this->input->post("apellido_{$i}");
                $tipo = $this->input->post("tipo_{$i}");

                if ($this->administrador_model->getUserByDocumento($documento)) {
                    //obtengo el user de ese dni
                    $usuario = $this->administrador_model->getUserByDocumento($documento);
                    //obtengo el id del user
                    $iduser = $usuario->id;
                    //modifico y obtengo el saldo del usuario
                    $saldo = $this->administrador_model->updateAndGetSaldoByUserId($iduser, $monto);
                    //Genero y guardo la transaccion
                    $transaction_carga = [
                        'fecha' => date('Y-m-d', time()),
                        'hora' => date('H:i:s', time()),
                        'id_usuario' => $iduser,
                        'monto' => $monto
                    ];
                    //Verifico si es una devolucion o una carga
                    if ($monto >= 0) {
                        $transaction_carga['transaccion'] = 'Carga de Saldo';
                    } else {
                        $transaction_carga['transaccion'] = 'Devolucion de Saldo';
                    };
                    //Seteo el saldo al final de la transaccion
                    $transaction_carga['saldo'] = $saldo;
                    //Inserto la transaccion y obtengo su ID
                    $id_insert = $this->administrador_model->addTransaccion($transaction_carga);
                    //Genero la carga en la tabla carga_saldo como log
                    $cargaLog = [
                        'fecha' => date('Y-m-d', strtotime($fecha)),
                        'hora' => date('H:i:s', time()),
                        'id_usuario' => $iduser,
                        'monto' => $monto,
                        'id_vendedor' => $this->session->id_vendedor,
                        'formato' => $tipo,
                        'transaccion_id' => $id_insert
                    ];
                    $this->administrador_model->addCargaLog($cargaLog);
                    // Correo
                    $carga = $this->administrador_model->getCargaByIdvendedorForEmailCSV($this->session->id_vendedor);

                    //Solo tomo datos del primer elemento, que es la ultima carga del vendedor
                    $data['fecha'] = date('d-m-Y', strtotime($carga->fecha));
                    $data['hora'] = $carga->hora;
                    $data['documento'] = $carga->documento;
                    $data['apellido'] = $carga->apellido;
                    $data['nombre'] = $carga->nombre;
                    $data['monto'] = $carga->monto;
                    $data['saldo'] = $carga->saldo;
                    $data['tipo'] = $carga->formato;
                    $correo = $carga->mail;

                    //Confeccion del correo del recibo
                    $subject = "Carga de Saldo";
                    $message = $this->load->view('general/correos/carga_saldo', $data, true);
                    $this->generalticket->smtpSendEmail($correo, $subject, $message);
                } else {
                    $errores[] = array(
                        $documento,
                        $nombre,
                        $apellido,
                        $monto
                    );
                }
                $i++;
            }
            if (isset($errores)) {
                $data['errores'] = $errores;
            } else {
                $data['correcto'] = True;
            }
            $data['titulo'] = 'Carga CSV';
            $this->load->view('header', $data);
            $this->load->view('carga_csv', $data);
            $this->load->view('general/footer');
        } else {
            redirect(base_url('admin/csv_carga'));
        }
    }

    public function ver_comentarios()
    {
        $id_vendedor = $this->session->userdata('id_vendedor');
        $admin = $this->administrador_model->getAdminById($id_vendedor);
        if ($admin->nivel == 1) {
            $data['titulo'] = 'Comentarios';
            $comentarios = $this->administrador_model->getComentarios();
            $dataComentarios=array();
            foreach ($comentarios as $key => $comentario) {
                $usuario = $this->administrador_model->getUserByID($comentario->id_usuario);
                $dataComentarios_i = [
                    'id' => $comentario->id,
                    'usuario' => strtoupper($usuario->apellido).', '.ucwords($usuario->nombre),
                    'comentario' => $comentario->comentario,
                    'fecha' => $comentario->fecha,
                    'hora' => $comentario->hora
                ];
                array_push($dataComentarios, $dataComentarios_i);
            }
            $data['comentarios'] = $dataComentarios;
            $this->load->view('header', $data);
            $this->load->view('comentarios', $data);
            $this->load->view('general/footer');
        } else {
            redirect(base_url('admin'));
        }
    }

    public function configuracion_general()
    {
        $id_vendedor = $this->session->userdata('id_vendedor');
        $admin = $this->administrador_model->getAdminById($id_vendedor);
        if ($admin->nivel == 1) {
            $data['titulo'] = 'Configuracion General';
            if ($this->input->method() == 'post'){
                $newConfig = [
                    'apertura' => $this->input->post('apertura_comedor'),
                    'cierre' => $this->input->post('cierre_comedor'),
                    'vacaciones_i' => $this->input->post('inicio_receso'),
                    'vacaciones_f' => $this->input->post('fin_receso'),
                    'dia_inicial' => $this->input->post('inicio_venta_semana'),
                    'dia_final' => $this->input->post('fin_venta_semana'),
                    'hora_final' => $this->input->post('hora_cierre_venta')
                ];
                $this->administrador_model->updateConfiguracion($newConfig);

                $data['configuracion'] = $this->administrador_model->getConfiguracion();

                $this->load->view('header', $data);
                $this->load->view('configuracion_periodos', $data);
                $this->load->view('general/footer');
            } else {
                $data['configuracion'] = $this->administrador_model->getConfiguracion();
                $this->load->view('header', $data);
                $this->load->view('configuracion_periodos', $data);
                $this->load->view('general/footer');
            }
        } else {
            redirect(base_url('admin'));
        }
    }

    public function feriados_list() {
        $id_vendedor = $this->session->userdata('id_vendedor');
        $admin = $this->administrador_model->getAdminById($id_vendedor);
        if ($admin->nivel == 1) {
            $año = ($this->uri->segment(4)) ? $this->uri->segment(4) : date('Y', time());
            $feriados = $this->administrador_model->getFeriadosByAño($año);

            $data['titulo'] = 'Feriados';
            $data['feriados'] = $feriados;
            $data['año'] = $año;

            // Cargar vistas
            $this->load->view('header', $data);
            $this->load->view('feriados', $data);
            $this->load->view('general/footer');
        } else {
            redirect(base_url('admin'));
        }
    }

    public function borrar_feriado()
    {
        $id_vendedor = $this->session->userdata('id_vendedor');
        $admin = $this->administrador_model->getAdminById($id_vendedor);
        if ($admin->nivel == 1) {
            $id_feriado = $this->uri->segment(6);
            $año = $this->uri->segment(4);
            if ($this->administrador_model->deletedFeriadoById($id_feriado)){
                redirect(base_url('admin/configuracion/feriados_list/'.$año));
            } else {
            redirect(base_url('admin/configuracion/periodos'));
            }
        } else {
            redirect(base_url('admin'));
        }
    }


    public function devolver_compras_by_fecha($fecha, $motivo)
    {
        //Obntenemos las compras realizadas para esa fecha
        $compras = $this->administrador_model->getComprasByFecha($fecha);
        foreach ($compras as $key => $compra) {
            //Por cada compra realizada, obtenemos el usurio que realizo la compra
            $comprador = $this->administrador_model->getUserByID($compra->id_usuario);
            $saldo = $comprador->saldo; // obtenemos su saldo
            $costoVianda = $compra->precio; // obtenemos el costo de esa compra
            $id_compra = $compra->id;
            $data_log = [
                'fecha' => date('Y-m-d', time()),
                'hora' => date('H:i:s', time()),
                'dia_comprado' => $fecha,
                'id_usuario' => $comprador->id,
                'precio' => $compra->precio,
                'tipo' => $compra->tipo,
                'turno' => $compra->turno,
                'menu' => $compra->menu,
                'transaccion_tipo' => 'Reintegro',
            ];
            $data_transaccion=[
                'fecha' => date('Y-m-d', time()),
                'hora' => date('H:i:s', time()),
                'id_usuario' => $comprador->id,
                'transaccion' => 'Reintegro',
                'monto' => $costoVianda,
                'saldo' => $saldo+$costoVianda,
            ];
            if ($this->administrador_model->removeCompra($id_compra)) {
                // si se borra la compra, actualizamos el saldo del usuario
                $this->administrador_model->updateSaldoByIDUser($comprador->id, $saldo + $costoVianda);
                $id_transaccion = $this->administrador_model->addTransaccion($data_transaccion);
                $data_log['transaccion_id'] = $id_transaccion;
                // Generamso la transaccion y vinculamos los ids
                $this->administrador_model->addLogCompra($data_log);

                //Armamos el correo con el detalle
                $dataRecivo['compra'] = $compra;
                $dataRecivo['saldo'] = $saldo+$costoVianda;
                $dataRecivo['fechaHoy'] = date('Y-m-d', time());
                $dataRecivo['horaAhora'] = date('H:i:s', time());
                $dataRecivo['recivoNumero'] = $id_transaccion;

                $subject = 'Reintegro por '.$motivo;
                $message = $this->load->view('general/correos/recibo_reintegro', $dataRecivo, true);

                $this->generalticket->smtpSendEmail($comprador->mail, $subject, $message);
            }
        }
    }


    public function add_feriado()
    {
        $año = $this->input->post('ano');
        $fecha = $this->input->post('fecha_feriado');
        $detalle = $this->input->post('fecha_feriado_motivo');
        $newFeriado = [
            'fecha' => $fecha,
            'detalle' => $detalle
        ];
        $this->devolver_compras_by_fecha($fecha, $detalle);
        if ($this->administrador_model->addFeriado($newFeriado)){
            redirect(base_url('admin/configuracion/feriados_list/'.$año));
        } else {
        redirect(base_url('admin/configuracion/periodos'));
        }
    }

    public function add_csv_feriado()
    {
        $data['uploadSuccess'] = array();
        $data['titulo'] = 'Carga CSV';
        $año = $this->input->post('ano');

        if ($this->input->method() == 'post') {
            $mi_archivo = 'archivo_csv';
            $separador = $this->input->post('separador');
            $config['upload_path'] = "uploads";
            $config['file_name'] = "carga_feriados";
            $config['overwrite'] = TRUE;
            $config['allowed_types'] = "csv";
            $config['max_size'] = "50000";
            $config['max_width'] = "2000";
            $config['max_height'] = "2000";

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload($mi_archivo)) {
                redirect(base_url('admin/configuracion/feriados_list/'.$año));
            } else {
                $data['subidoCorrecto'] = $this->upload->data();
                $file = fopen('uploads/carga_feriados.csv', 'r');
                $head = fgetcsv($file, 0, $separador);
                while ((!feof($file)) && ($file != '')) {
                    $cargas[] = fgetcsv($file, 0, $separador);
                }
                fclose($file);

                $data['cargas'] = $cargas;
                $i=0;
                foreach ($cargas as $carga) {
                    if ($carga) {
                        $newFeriado =[
                            'fecha' => $cargas[$i][0],
                            'detalle' => $cargas[$i][1],
                        ];
                        if($this->administrador_model->addFeriado($newFeriado)){
                            $this->devolver_compras_by_fecha($cargas[$i][0], $cargas[$i][1]);
                        };
                        $i=$i+1;
                    }
                };

                redirect(base_url('admin/configuracion/feriados_list/'.$año));
            }
        } else {
            redirect(base_url('admin/configuracion/feriados_list/'.$año));
        }
    }

    public function configuracion_costos()
    {
        $id_vendedor = $this->session->userdata('id_vendedor');
        $admin = $this->administrador_model->getAdminById($id_vendedor);
        if ($admin->nivel == 1) {
            $data['titulo'] = 'Configuracion Precios';
            if ($this->input->method() == 'post'){
                $precios = $this->administrador_model->getPrecios();
                foreach ($precios as $key => $precio) {
                    $id_precio=$precio->id;
                    $costo = $this->input->post('precio_'.$id_precio);
                    $this->administrador_model->updatePrecios($id_precio, $costo);
                }

                $data['precios'] = $this->administrador_model->getPrecios();

                $this->load->view('header', $data);
                $this->load->view('configuracion_precios', $data);
                $this->load->view('general/footer');
            } else {
                $data['precios'] = $this->administrador_model->getPrecios();
                $this->load->view('header', $data);
                $this->load->view('configuracion_precios', $data);
                $this->load->view('general/footer');
            }
        } else {
            redirect(base_url('admin'));
        }
    }

    public function ver_compras_userid()
    {
        $id_vendedor = $this->session->userdata('id_vendedor');
        $admin = $this->administrador_model->getAdminById($id_vendedor);
        if ($admin->nivel == 1) {
            $id_user = $this->uri->segment(4);
            $usuario = $this->administrador_model->getUserByID($id_user);
            $compras = $this->administrador_model->getComprasByUserId($id_user);
            $data['titulo'] = 'Historico de compras de '.$usuario->nombre;
            $data['usuario'] = $usuario;
            $limit_por_pagina = 10;
            $start_index = ($this->uri->segment(5)) ? $this->uri->segment(5) : 0;
            $total_registros = count($compras);
            $start_index = floor($start_index/10)*10;

            $data['ultimo'] = floor($total_registros/$limit_por_pagina)*10;

            $data['compras'] = $this->administrador_model->getComprasInRangeByIDUser($limit_por_pagina, $start_index, $id_user);
            //Botones para la paginacion
            if ($start_index==0) {
                //Si el id es 0, estamos en la primera pagina, y lo seteamos
                $data['primera'] = 1;
            } elseif ($start_index <= 10) {
                $link[] =[
                    'id'=>0,
                    'num'=>floor($start_index/10)
                ];
            } elseif ($start_index>10) {
                $link[] = [
                    'id'=>$start_index - 10,
                    'num'=>floor($start_index/10)
                ];
            }

            $link[] = [
                'id' => $start_index,
                'num' => floor($start_index / 10) + 1,
                'act' =>'active'
            ];

            if ($start_index+10<=$total_registros) {
                $link[] = [
                    'id'=>$start_index + 10,
                    'num'=>floor($start_index/10)+2
                ];
            } else {
                $data['ultima'] = 1;
            }
            if ($total_registros>$limit_por_pagina){
                $data['links']= $link;
            }

            $this->load->view('header', $data);
            $this->load->view('ver_compras', $data);
            $this->load->view('general/footer');
        } else {
            redirect(base_url('usuario'));
        }
    }

    public function devolver_compra_by_id()
    {
        $id_vendedor = $this->session->userdata('id_vendedor');
        $admin = $this->administrador_model->getAdminById($id_vendedor);
        if ($admin->nivel == 1) {
            $id_user = $this->uri->segment(4);
            $id_compra = $this->uri->segment(5);
            $compra = $this->administrador_model->getCompraById($id_compra);
            $comprador = $this->administrador_model->getUserByID($id_user);
            $saldo = $comprador->saldo; // obtenemos su saldo
            $costoVianda = $compra->precio; // obtenemos el costo de esa compra
            $data_log = [
                'fecha' => date('Y-m-d', time()),
                'hora' => date('H:i:s', time()),
                'dia_comprado' => $compra->dia_comprado,
                'id_usuario' => $comprador->id,
                'precio' => $compra->precio,
                'tipo' => $compra->tipo,
                'turno' => $compra->turno,
                'menu' => $compra->menu,
                'transaccion_tipo' => 'Reintegro',
            ];
            $data_transaccion=[
                'fecha' => date('Y-m-d', time()),
                'hora' => date('H:i:s', time()),
                'id_usuario' => $comprador->id,
                'transaccion' => 'Reintegro',
                'monto' => $costoVianda,
                'saldo' => $saldo+$costoVianda,
            ];
            if ($this->administrador_model->removeCompra($id_compra)) {
                $this->administrador_model->updateSaldoByIDUser($id_user, $saldo + $costoVianda);
                $id_transaccion = $this->administrador_model->addTransaccion($data_transaccion);
                // Generamso la transaccion y vinculamos los ids
                $data_log['transaccion_id'] = $id_transaccion;
                $this->administrador_model->addLogCompra($data_log);
                //Armamos el correo con el detalle
                $dataRecivo['compra'] = $compra;
                $dataRecivo['saldo'] = $saldo+$costoVianda;
                $dataRecivo['fechaHoy'] = date('Y-m-d', time());
                $dataRecivo['horaAhora'] = date('H:i:s', time());
                $dataRecivo['recivoNumero'] = $id_transaccion;
                $motivo='Compra erronea o duplicada';

                $subject = 'Reintegro por'.$motivo;
                $message = $this->load->view('general/correos/recibo_reintegro_duplicada', $dataRecivo, true);

                $this->generalticket->smtpSendEmail($comprador->mail, $subject, $message);
            }

            redirect(base_url("admin/compras/usuario/{$id_user}"));
        } else {
            redirect(base_url('usuario'));
        }
    }

    public function listar_link_pagos()
    {
        $data['titulo'] = 'Links de Pagos';
        $id_vendedor = $this->session->userdata('id_vendedor');
        $admin = $this->administrador_model->getAdminById($id_vendedor);
        if ($admin->nivel == 1) {
            $data['links']= $this->administrador_model->getLinkVirtuales();
            $this->load->view('header', $data);
            $this->load->view('listar_links_mp', $data);
            $this->load->view('general/footer');
            
        } else {
            redirect(base_url('usuario'));
        }
    }

    public function add_link_pago()
    {
        $id_vendedor = $this->session->userdata('id_vendedor');
        $admin = $this->administrador_model->getAdminById($id_vendedor);
        if ($admin->nivel == 1 && $this->input->method() == 'post') {
            $user_tipo = $this->input->post('tipo_usuario');
            $monto = $this->input->post('monto');
            $link = $this->input->post('link');
            $newBoton = [
                'valor' => $monto,
                'link' => $link,
                'tipo_user' => $user_tipo,
            ];
            $this->administrador_model->addLinkVirtual($newBoton);
            redirect(base_url('admin/configuracion/links'));
        } else {
            redirect(base_url('admin/configuracion/links'));
        }
    }

    public function remove_link_pago()
    {
        $id_vendedor = $this->session->userdata('id_vendedor');
        $admin = $this->administrador_model->getAdminById($id_vendedor);
        if ($admin->nivel == 1 && $this->input->method() == 'post') {
            $id_link = $this->input->post('id_link');
            $this->administrador_model->removeLinkVirtual($id_link);
            redirect(base_url('admin/configuracion/links'));
        } else {
            redirect(base_url('admin/configuracion/links'));
        }
    }

    public function cargas_virtuales_all_list($fecha = null)
    {
        $data['titulo'] = 'Pagos Virtuales Realizados';
        $id_vendedor = $this->session->userdata('id_vendedor');
        $admin = $this->administrador_model->getAdminById($id_vendedor);
        if ($admin->nivel == 1) {
            if ($this->input->method() == 'post'){
                $fecha = $this->input->post('filter_date');
                redirect(base_url('admin/cargasvirtuales/list/'.$fecha));
            } else{
                if ($fecha) {
                    $fecha_formateada = date('Y-m-d', strtotime(str_replace('-', '/', $fecha)));
                    $data['fecha_filtrada'] = $fecha;
                    $data['cargas'] = $this->administrador_model->getCargasByFecha($fecha_formateada);
                } else {
                    $data['fecha_filtrada'] = "";
                    $data['cargas'] = $this->administrador_model->getLast20Cargas();
                }
            }
            $this->load->view('header', $data);
            $this->load->view('listar_cargas_virtuales', $data);
            $this->load->view('general/footer');
        } else {
            redirect(base_url('usuario'));
        }
    }

    public function carga_virtual_ok($fecha = null)
    {
        $id_vendedor = $this->session->userdata('id_vendedor');
        $admin = $this->administrador_model->getAdminById($id_vendedor);
        if ($admin->nivel == 1) {
            if ($this->input->method() == 'post'){
                $carga_id = $this->input->post('carga_id');
                $carga = $this->administrador_model->getCargaVirtualByID($carga_id);
                $usuario = $this->administrador_model->getUserByID($carga->usuario);
                $iduser = $usuario->id; //obtengo el id del user
                $saldo = $this->administrador_model->updateAndGetSaldoByUserId($iduser, $carga->monto); // modifico y luego obtengo el saldo

                //Genero y guardo la transaccion
                $transaction_carga = [
                    'fecha' => date('Y-m-d', time()),
                    'hora' => date('H:i:s', time()),
                    'id_usuario' => $iduser,
                    'monto' => $carga->monto,
                    'transaccion'=> 'Carga de Saldo',
                ];
                //Seteo el saldo al final de la transaccion
                $transaction_carga['saldo'] = $saldo;
                //Inserto la transaccion y obtengo su ID
                $id_insert = $this->administrador_model->addTransaccion($transaction_carga);

                //Genero la carga en la tabla log_carga
                $cargaLog = [
                    'fecha' => date('Y-m-d', time()),
                    'hora' => date('H:i:s', time()),
                    'id_usuario' => $iduser,
                    'monto' => $carga->monto,
                    'id_vendedor' => $this->session->id_vendedor,
                    'formato' => 'Virtual',
                    'transaccion_id' => $id_insert
                ];
                $this->administrador_model->addCargaLog($cargaLog);
                $this->session->set_flashdata('transaccion', $id_insert);
                $this->administrador_model->updateCargaVirtualByID($carga_id, $id_vendedor,'aprobado');
                //Confeccion del correo
                $data['transaccion'] = $id_insert;
                $data['fecha'] = date('d-m-Y', strtotime($cargaLog->fecha));
                $data['hora'] = $cargaLog['hora'];
                $data['documento'] = $usuario->documento;
                $data['apellido'] = $usuario->apellido;
                $data['nombre'] = $usuario->nombre;
                $data['monto'] = $carga->monto;
                $data['saldo'] = $saldo;
                $data['tipo'] = $cargaLog->formato;
                $correo = $usuario->mail;
                
                //Confeccion del correo del recibo
                $subject = "Acreditacion de Carga Virtual";
                $message = $this->load->view('general/correos/carga_saldo', $data, true);
                $this->generalticket->smtpSendEmail($correo, $subject, $message);

                redirect(base_url('admin/cargasvirtuales/list/'.$fecha));
            } else {
                redirect(base_url('admin/cargasvirtuales/list/'.$fecha));
            }
        }
    }

    public function carga_virtual_remove($fecha = null)
    {
        $id_vendedor = $this->session->userdata('id_vendedor');
        $admin = $this->administrador_model->getAdminById($id_vendedor);
        if ($admin->nivel == 1) {
            if ($this->input->method() == 'post'){
                $carga_id = $this->input->post('carga_id');
                $carga = $this->administrador_model->getCargaVirtualByID($carga_id);
                if($this->administrador_model->updateCargaVirtualByID($carga_id, $id_vendedor, 'rechazado')){
                    $usuario = $this->administrador_model->getUserByID($carga->usuario);
                    //Confeccion del correo
                    $data['fecha'] = date('d-m-Y', strtotime($carga->timestamp));
                    $data['documento'] = $usuario->documento;
                    $data['apellido'] = $usuario->apellido;
                    $data['nombre'] = $usuario->nombre;
                    $data['monto'] = $carga->monto;
                    $correo = $usuario->mail;
                    //Confeccion del correo del recibo
                    $subject = "Rechazo de Carga Virtual";
                    $message = $this->load->view('general/correos/carga_virtual_rechazo', $data, true);
                    $this->generalticket->smtpSendEmail($correo, $subject, $message);
                }
                redirect(base_url('admin/cargasvirtuales/list/'.$fecha));
            } else {
                redirect(base_url('admin/cargasvirtuales/list/'.$fecha));
            }
        }
    }
}