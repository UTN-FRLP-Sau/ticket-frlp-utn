<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;

class Vendedor extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('vendedor_model');

        if ($this->session->userdata('is_user')) {
            redirect(base_url('usuario'));
        }

        if (!$this->session->userdata('is_admin')) {
            redirect(base_url('admin/login'));
        }
    }

    public function index()
    {
        $data = [
            'titulo' => 'Carga de Saldo'
        ];
        if ($this->input->method() == 'post') {
            $documento = $this->input->post('numeroDni');
            $usuario = $this->vendedor_model->getUserByDocumento($documento);
            if ($usuario) {
                $data['usuario'] = $usuario;
                $this->session->set_userdata('documento_buscado', $documento);
            } else {
                $data['usuario'] = FALSE;
            }

            $this->load->view('header', $data);
            $this->load->view('index', $data);
            $this->load->view('general/footer');
        } else {
            $this->load->view('header', $data);
            $this->load->view('index');
            $this->load->view('general/footer');
        }
    }

    public function correoCargaSaldo($id_transaccion)
    {
        $cargas = $this->vendedor_model->getCargaByTransaccion($id_transaccion);

        foreach ($cargas as $carga) {
            //Solo tomo datos del unico elemento que trae el array
            $data['transaccion'] = $id_transaccion;
            $data['fecha'] = date('d-m-Y', strtotime($carga->fecha));
            $data['hora'] = $carga->hora;
            $data['documento'] = $carga->documento;
            $data['apellido'] = $carga->apellido;
            $data['nombre'] = $carga->nombre;
            $data['monto'] = $carga->monto;
            $data['saldo'] = $carga->saldo;
            $data['tipo'] = $carga->formato;
            $correo = $carga->mail;
        }
        //Confeccion del correo del recibo
        $subject = "Carga de Saldo";
        $message = $this->load->view('general/correos/carga_saldo', $data, true);
        $this->generalticket->smtpSendEmail($correo, $subject, $message);

        return true;
    }

    public function cargarSaldo()
    {
        if ($this->input->method() == 'post') {
            $documento = $this->input->post('dni'); //obtengo el numero de documento
            $carga = $this->input->post('carga'); // obtengo el monto a cargar
            $data['titulo'] = 'Carga de Saldo';
            $data['usuario'] = $this->vendedor_model->getUserByDocumento($documento);
            // reglas de validez del formulario
            $rules = [
                [
                    'field' => 'carga',
                    'label' => 'Saldo a Cargar',
                    'rules' => 'trim|required|differs[0]',
                    'errors' => [
                        'required' => 'Debe ingresar un monto a cargar',
                        'differs' => 'El monto debe ser distinto de 0 (cero)',
                    ]
                ],
                [
                    'field' => 'metodo_carga',
                    'label' => 'Metodo de Carga',
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Se debe especificar un metodo de carga'
                    ]
                ],
            ];
            $this->form_validation->set_rules($rules);
            if ($this->form_validation->run() == FALSE) {
                $this->load->view('header', $data);
                $this->load->view('index', $data);
                $this->load->view('general/footer');
            } else {
                $usuario = $this->vendedor_model->getUserByDocumento($documento); //obtengo el user de ese dni
                $iduser = $usuario->id; //obtengo el id del user
                $saldo = $this->vendedor_model->updateAndGetSaldoByUserId($iduser, $carga); // modifico y luego obtengo el saldo

                //Genero y guardo la transaccion
                $transaction_carga = [
                    'fecha' => date('Y-m-d', time()),
                    'hora' => date('H:i:s', time()),
                    'id_usuario' => $iduser,
                    'monto' => $carga
                ];
                //Verifico si es una devolucion o una carga
                if ($carga >= 0) {
                    $transaction_carga['transaccion'] = 'Carga de Saldo';
                } else {
                    $transaction_carga['transaccion'] = 'Devolucion de Saldo';
                };
                //Seteo el saldo al final de la transaccion
                $transaction_carga['saldo'] = $saldo;
                //Inserto la transaccion y obtengo su ID
                $id_insert = $this->vendedor_model->addTransaccion($transaction_carga);

                //Genero la carga en la tabla log_carga
                $cargaLog = [
                    'fecha' => date('Y-m-d', time()),
                    'hora' => date('H:i:s', time()),
                    'id_usuario' => $iduser,
                    'monto' => $carga,
                    'id_vendedor' => $this->session->id_vendedor,
                    'formato' => $this->input->post('metodo_carga'),
                    'transaccion_id' => $id_insert
                ];
                $this->vendedor_model->addCargaLog($cargaLog);
                $this->session->set_flashdata('transaccion', $id_insert);

                redirect(base_url('admin/cargar_saldo/succes'));
            }
        } else {
            redirect(base_url('admin'));
        }
    }

    public function cargarSaldoSucces()
    {
        $id_transaccion= $this->session->flashdata('transaccion');
        $cargas = $this->vendedor_model->getCargaByTransaccion($id_transaccion);
        $data['titulo'] = 'Confirmacion';

        if ($id_transaccion) {
            foreach ($cargas as $carga) {
                //Solo tomo datos del unico elemento que trae el array
                $data['transaccion'] = $id_transaccion;
                $data['fecha'] = date('d-m-Y', strtotime($carga->fecha));
                $data['hora'] = $carga->hora;
                $data['documento'] = $carga->documento;
                $data['apellido'] = $carga->apellido;
                $data['nombre'] = $carga->nombre;
                $data['monto'] = $carga->monto;
                $data['saldo'] = $carga->saldo;
                $data['tipo'] = $carga->formato;
                $correo = $carga->mail;
            }
            $this->session->set_flashdata('transaccion', $id_transaccion);

            if ($this->input->method() == 'post') {
                $id_transaccion= $this->session->flashdata('transaccion');
                $this->correoCargaSaldo($id_transaccion);
                redirect(base_url('admin'));
            } else {
                $this->load->view('header', $data);
                $this->load->view('carga_succes', $data);
                $this->load->view('general/footer');
            }
        } else {
            redirect(base_url('admin'));
        }
    }


    public function createUser()
    {
        $data = [
            'titulo' => 'Nuevo Usuario'
        ];

        // Verifico si se carga informacion en el formulario
        if ($this->input->method() == 'post') {
            // Si el methodo es POST, obtengo el dni y el legajo
            // Verifico la informcion del formulario
            $rules = [
                [
                    'field' => 'saldo',
                    'label' => 'Saldo a Ingresar',
                    'rules' => 'trim|max_length[4]',
                    'errors' => [
                        'max_length' => 'El monto a cargar no debe superar los $9999'
                    ]
                ],
                [
                    'field' => 'legajo',
                    'label' => 'Legajo',
                    'rules' => 'trim|min_length[5]|required|max_length[6]|numeric|integer|is_unique[usuarios.legajo]',
                    'errors' => [
                        'max_length' => 'El %s debe contener entre 5 y 6 digitos',
                        'min_length' => 'El %s debe contener entre 5 y 6 digitos',
                        'required' => 'Debe ingresar un %s',
                        'numeric' => 'El %s debe ser un numero',
                        'integer' => 'El %s debe ser un entero',
                        'is_unique' => 'Ese %s ya esta registrado',
                    ]
                ],
                [
                    'field' => 'dni',
                    'label' => 'Documento',
                    'rules' => 'trim|max_length[8]|numeric|required|integer|is_unique[usuarios.documento]',
                    'errors' => [
                        'required' => 'Debe ingresar un %s',
                        'max_length' => 'El %s debe contener 5 numeros como maximo',
                        'numeric' => 'El %s debe ser un numero',
                        'integer' => 'El %s debe ser un entero',
                        'is_unique' => 'Ese %s ya esta registrado',
                    ]
                ],
                [
                    'field' => 'nombre',
                    'label' => 'Nombre',
                    'rules' => 'trim|max_length[50]|required',
                    'errors' => [
                        'max_length' => 'El %s debe contener como maximo 50 caracteres',
                        'required' => 'Debe ingresar un %s',
                    ]
                ],
                [
                    'field' => 'apellido',
                    'label' => 'Apellido',
                    'rules' => 'trim|max_length[50]|required',
                    'errors' => [
                        'max_length' => 'El %s debe contener como maximo 50 caracteres',
                        'required' => 'Debe ingresar un %s',
                    ]
                ],
                [
                    'field' => 'claustro',
                    'label' => 'Claustro',
                    'rules' => 'trim|in_list[Estudiante,Docente,No Docente]|required',
                    'errors' => [
                        'required' => 'Debe elegir un %s',
                        'in_list' => 'No es un %s valido',
                    ]
                ],
                [
                    'field' => 'email',
                    'label' => 'E-Mail',
                    'rules' => 'trim|valid_email|required|is_unique[usuarios.mail]',
                    'errors' => [
                        'required' => 'Debe ingresar un %s',
                        'valid_email' => 'No es un %s valido',
                        'is_unique' => 'Ese %s ya esta registrado',
                    ]
                ],
            ];
            $this->form_validation->set_rules($rules);
            if ($this->form_validation->run() == FALSE) {
                $this->load->view('header', $data);
                $this->load->view('crear_usuario', $data);
                $this->load->view('general/footer');
            } else {
                $numerodni = $this->input->post('dni');
                $legajo = $this->input->post('legajo');

                //Crer Password aleatorio de la forma 3letras+3numeros
                $numeros_permitidos = '0123456789';
                $letras_permitidas = 'abcdefghijklmnopqrstuvwxyz';
                $num3 = substr(str_shuffle($numeros_permitidos), 0, 3);
                $pal3 = substr(str_shuffle($letras_permitidas), 0, 3);
                $password = "{$pal3}{$num3}";

                //Asigno el costo de la vianda segun el tipo de usuario
                //1-Estudiante || 2-Becado || 3-Docente || 4-No Docente
                if ($this->input->post('beca') == 'Si') {
                    $idPrecio = 2;
                } else {
                    if ($this->input->post('claustro') == 'Estudiante') {
                        $idPrecio = 1;
                    } elseif ($this->input->post('claustro') == 'No Docente') {
                        $idPrecio = 4;
                    } elseif ($this->input->post('claustro') == 'Docente') {
                        $idPrecio = 3;
                    }
                };
                // Si la especialidad esta vacia, la guardo como null
                if ($this->input->post('especialidad') == "") {
                    $especialidad = null;
                } else {
                    $especialidad = $this->input->post('especialidad');
                }

                $newUser = [
                    'tipo' => $this->input->post('claustro'),
                    'legajo' => $legajo,
                    'documento' => $numerodni,
                    'pass' => md5($password),
                    'nombre' => ucwords($this->input->post('nombre')),
                    'apellido' => ucwords($this->input->post('apellido')),
                    'especialidad' => $especialidad,
                    'mail' => strtolower($this->input->post('email')),
                    'saldo' => $this->input->post('saldo'),
                    'estado' => 1,
                    'id_precio' => $idPrecio
                ];
                $id_usuario = $this->vendedor_model->addNewUser($newUser);
                if ($id_usuario) {
                    // Creamos el log de alta
                    $logNewUser = [
                        'fecha' => date('Y-m-d', time()),
                        'hora' => date('H:i:s', time()),
                        'id_vendedor' => $this->session->id_vendedor,
                        'id_usuario' => $id_usuario
                    ];
                    $this->vendedor_model->addLogNewUser($logNewUser);

                    // realizamos la carga de saldo
                    $usuario = $this->vendedor_model->getUserByDocumento($numerodni);
                    if ($this->input->post('saldo') != 0) {

                        //Genero y guardo la transaccion
                        $transaction_carga = [
                            'fecha' => date('Y-m-d', time()),
                            'hora' => date('H:i:s', time()),
                            'id_usuario' => $usuario->id,
                            'monto' => $this->input->post('saldo')
                        ];
                        //Verifico si es una devolucion o una carga
                        if ($this->input->post('saldo') >= 0) {
                            $transaction_carga['transaccion'] = 'Carga de Saldo';
                        } else {
                            $transaction_carga['transaccion'] = 'Devolucion de Saldo';
                        };
                        //Seteo el saldo al final de la transaccion
                        $transaction_carga['saldo'] = $this->input->post('saldo');
                        //Inserto la transaccion y obtengo su ID
                        $id_insert = $this->vendedor_model->addTransaccion($transaction_carga);

                        $newCarga = [
                            'fecha' => date('Y-m-d', time()),
                            'hora' => date('H:i:s', time()),
                            'id_usuario' => $usuario->id,
                            'monto' => $this->input->post('saldo'),
                            'id_vendedor' => $this->session->id_vendedor,
                            'formato' => 'Efectivo',
                            'transaccion_id' => $id_insert
                        ];
                        $this->vendedor_model->addCargaLog($newCarga);
                        $this->correoCargaSaldo($id_insert);
                    }

                    //Confeccion del correo del recivo
                    $correo = $this->input->post('email');
                    $dataCorreo['dni'] = $numerodni;
                    $dataCorreo['apellido'] = $this->input->post('apellido');
                    $dataCorreo['nombre'] = $this->input->post('nombre');
                    $dataCorreo['password'] = $password;

                    $subject = "Bienvenido al Comedor Universitario UTN-FRLP";
                    $message = $this->load->view('general/correos/nuevo_usuario', $dataCorreo, true);
                    $this->generalticket->smtpSendEmail($correo, $subject, $message);

                    redirect(base_url('admin'));
                }
            }
        } else {
            $this->load->view('header', $data);
            $this->load->view('crear_usuario', $data);
            $this->load->view('general/footer');
        }
    }

    public function updateUser()
    {
        $data = [
            'titulo' => 'Actualizar Usuario'
        ];
        $iduser = $this->uri->segment(3);

        if (null == $this->vendedor_model->getUserById($iduser)) {
            redirect(base_url('admin'));
        }

        $usuario = $this->vendedor_model->getUserById($iduser);
        $data['usuario'] = $usuario;

        //Asigno el costo de la vianda segun el tipo de usuario
        //1-Estudiante || 2-Becado || 3-Docente || 4-No Docente
        if ($this->input->post('beca') == 'Si') {
            $idPrecio = 2;
        } else {
            if ($this->input->post('claustro') == 'Estudiante') {
                $idPrecio = 1;
            } elseif ($this->input->post('claustro') == 'No Docente') {
                $idPrecio = 4;
            } elseif ($this->input->post('claustro') == 'Docente') {
                $idPrecio = 3;
            }
        };

        // Verifico si se carga informacion en el formulario
        if ($this->input->method() == 'post') {
            // Verifico la informcion del formulario
            $unique_legajo =  '';
            $unique_documento =  '';
            $unique_email =  '';
            if ($this->input->post('legajo') != $usuario->legajo) {
                $unique_legajo =  '|is_unique[usuarios.legajo]';
            } elseif ($this->input->post('documento') != $usuario->documento) {
                $unique_documento = '|is_unique[usuarios.documento]';
            } elseif ($this->input->post('documento') != $usuario->documento) {
                $unique_email = '|is_unique[usuarios.mail]';
            }
            $rules = [
                [
                    'field' => 'legajo',
                    'label' => 'Legajo',
                    'rules' => "trim|min_length[5]|required|max_length[6]|numeric|integer{$unique_legajo}",
                    'errors' => [
                        'max_length' => 'El %s debe contener 5 y 6 digitos',
                        'min_length' => 'El %s debe contener 5 y 6 digitos',
                        'required' => 'Debe ingresar un %s',
                        'numeric' => 'El %s debe ser un numero',
                        'integer' => 'El %s debe ser un entero',
                        'is_unique' => 'Ese %s ya esta registrado',
                    ]
                ],
                [
                    'field' => 'documento',
                    'label' => 'Documento',
                    'rules' => "trim|max_length[8]|numeric|required|integer{$unique_documento}",
                    'errors' => [
                        'required' => 'Debe ingresar un %s',
                        'max_length' => 'El %s debe contener 5 numeros como maximo',
                        'numeric' => 'El %s debe ser un numero',
                        'integer' => 'El %s debe ser un entero',
                        'is_unique' => 'Ese %s ya esta registrado',
                    ]
                ],
                [
                    'field' => 'nombre',
                    'label' => 'Nombre',
                    'rules' => 'trim|max_length[50]|required',
                    'errors' => [
                        'max_length' => 'El %s debe contener como maximo 50 caracteres',
                        'required' => 'Debe ingresar un %s',
                    ]
                ],
                [
                    'field' => 'apellido',
                    'label' => 'Apellido',
                    'rules' => 'trim|max_length[50]|required',
                    'errors' => [
                        'max_length' => 'El %s debe contener como maximo 50 caracteres',
                        'required' => 'Debe ingresar un %s',
                    ]
                ],
                [
                    'field' => 'claustro',
                    'label' => 'Claustro',
                    'rules' => 'trim|in_list[Estudiante,Docente,No Docente]|required',
                    'errors' => [
                        'required' => 'Debe elegir un %s',
                        'in_list' => 'No es un %s valido',
                    ]
                ],
                [
                    'field' => 'email',
                    'label' => 'E-Mail',
                    'rules' => "trim|valid_email|required{$unique_email}",
                    'errors' => [
                        'required' => 'Debe ingresar un %s',
                        'valid_email' => 'No es un %s valido',
                        'is_unique' => 'Ese %s ya esta registrado',
                    ]
                ],
            ];
            $this->form_validation->set_rules($rules);
            if ($this->form_validation->run() == FALSE) {
                $this->load->view('header', $data);
                $this->load->view('modificar_usuario', $data);
                $this->load->view('general/footer');
            } else {
                $updateUser = [
                    'legajo' => $this->input->post('legajo'),
                    'documento' => $this->input->post('documento'),
                    'nombre' => ucwords($this->input->post('nombre')),
                    'apellido' => ucwords($this->input->post('apellido')),
                    'tipo' => ucwords($this->input->post('claustro')),
                    'especialidad' => ucwords($this->input->post('especialidad')),
                    'mail' => strtolower($this->input->post('email')),
                    'id_precio' => $idPrecio
                ];

                if ($this->vendedor_model->updateUserById($iduser, $updateUser)) {
                    redirect(base_url('admin'));
                }
            }
        } else {
            $this->load->view('header', $data);
            $this->load->view('modificar_usuario', $data);
            $this->load->view('general/footer');
        }
    }

    public function descargarExcel()
    {
        $data = [
            'titulo' => 'Descarga de Planilla'
        ];
        if ($this->input->method() == 'post') {
            $strtime = strtotime($this->input->post('fecha'));
            $fecha_dowload = date('Y-m-d', $strtime);

            $filename = "Listado_{$fecha_dowload}.xlsx";
            $compras = $this->vendedor_model->getComprasByFechaForExls($fecha_dowload);
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('C1', $fecha_dowload);
            $sheet->setCellValue('A2', '#');
            $sheet->setCellValue('B2', 'Documento');
            $sheet->setCellValue('C2', 'Apellido');
            $sheet->setCellValue('D2', 'Nombre');
            $sheet->setCellValue('E2', 'Menu');
            $sheet->setCellValue('F2', 'Turno');
            $sheet->setCellValue('G2', 'Claustro');
            // $sheet->setCellValue('H2', 'Tipo');
            $rows = 3;
            $i = 1;
            foreach ($compras as $compra) {
                $usuario = $this->vendedor_model->getUserById($compra->id_usuario);
                if ($usuario != null) {
                    $sheet->setCellValue("A{$rows}", $i++);
                    $sheet->setCellValue("B{$rows}", $usuario->documento);
                    $sheet->setCellValue("C{$rows}", $usuario->apellido);
                    $sheet->setCellValue("D{$rows}", $usuario->nombre);
                    $sheet->setCellValue("E{$rows}", $compra->menu);
                    $sheet->setCellValue("F{$rows}", $compra->turno);
                    $sheet->setCellValue("G{$rows}", $usuario->tipo);
                    // $sheet->setCellValue("H{$rows}", $compra->tipo);
                    $rows++;
                }
            }
            header('Content-Type: application/vnd.ms-excel');
            header("Content-Disposition: attachment; filename={$filename}");
            $writer = new Xlsx($spreadsheet);
            ob_end_clean();
            $writer->save("php://output");
        } else {
            $this->load->view('header', $data);
            $this->load->view('descarga_planilla', $data);
            $this->load->view('general/footer');
        }
    }

    public function viewDescargarInformes()
    {
        $data['titulo'] = 'Descarga de Informes';
        $this->load->view('header', $data);
        $this->load->view('descarga_informe', $data);
        $this->load->view('general/footer');
    }

    public function descargarCierreCajaDiario()
    {
        // En esta funcion, se imprime el detalle de todas las cargas de la fecha,
        // mostrandos quien a quien y cuanto se cargo, ademas de informar el total
        // de cargas y el total de ingresos, detallando el efectivo del virtual
        if ($this->input->method() == 'post') {
            $id_vendedor = $this->session->userdata('id_vendedor');
            // Del formulario obtenemos la fecha para realizar el cierre
            $strtime = strtotime($this->input->post('cierre_fecha'));
            $fecha = date('Y-m-d', $strtime);
            // Obtenemos todas las cargas de esa fecha
            $cargas = $this->vendedor_model->getCargasByFechaForPDF($fecha);
            // Recorremos la cargas para separarlas por formato
            // Iniciamos contadores
            $nVirtual = 0; //cargas en efectivo
            $totalVirtual = 0; // dinero total en eefectivo
            $nEfectivo = 0; //cargas virtuales
            $totalEfectivo = 0; //dinero total virtual
            $nMercadoPago = 0; // cargas por mercado pago
            $totalMercadoPago = 0; // dinero total por mercado pago
            foreach ($cargas as $carga) {
                
                // Se convierte el monto a valor absoluto
                $carga->monto = abs($carga->monto);

                if ($carga->formato == 'Efectivo') {
                    // Si la carga fue en efectivo
                    $totalEfectivo = $totalEfectivo + $carga->monto;
                    $nEfectivo = $nEfectivo + 1;
                } elseif ($carga->formato == 'Virtual') {
                    // Si la carga fue virtual
                    $totalVirtual = $totalVirtual + $carga->monto;
                    $nVirtual = $nVirtual + 1;
                } elseif ($carga->formato == 'MP') {
                    // Si la carga fue con Mercado Pago
                    $totalMercadoPago = $totalMercadoPago + $carga->monto;
                    $nMercadoPago = $nMercadoPago + 1;
                }
                // Agregamos el external_reference al objeto de la carga
                $carga->external_reference = isset($carga->external_reference) ? $carga->external_reference : 'N/A';
            }

            //Guardamos todo en $data
            $data['cargas'] = $cargas;
            $data['total_virtual'] = $totalVirtual;
            $data['cantidad_virtual'] = $nVirtual;
            $data['total_efectivo'] = $totalEfectivo;
            $data['cantidad_efectivo'] = $nEfectivo;
            $data['total_mercadopago'] = $totalMercadoPago;
            $data['cantidad_mercadopago'] = $nMercadoPago;
            $data['vendedor'] = $this->vendedor_model->getVendedorById($id_vendedor);
            $data['fecha'] = date("d-m-Y", $strtime);

            $dompdf = new Dompdf();
            $dompdf->loadHtml($this->load->view('pdf_view/cajaDiaria', $data, true));
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $dompdf->stream("Cierre_{$fecha}.pdf", array("Attachment" => 0)); //0 para ver, 1 para descargar
        } else {
            redirect(base_url('admin/informe'));
        }
    }

    public function descargarCierreCajaSemana()
    {
        if ($this->input->method() == 'post') {
            $id_vendedor = $this->session->userdata('id_vendedor');
            $strtime1 = strtotime($this->input->post('cierre_fecha_1'));
            $strtime2 = strtotime($this->input->post('cierre_fecha_2'));
            $fecha1 = date('Y-m-d', $strtime1);
            $fecha2 = date('Y-m-d', $strtime2);
            $cargas = $this->vendedor_model->getCargasByRangeFechaForPDF($fecha1, $fecha2);

            $fecha_actual_loop = $fecha1;
            $i = 0;
            $detalle = array();

            while (strtotime($fecha_actual_loop) <= strtotime($fecha2)) { // Comparar como timestamps
                $cantidad_efec = 0;
                $total_efec = 0;
                $cantidad_virt = 0;
                $total_virt = 0;
                $cantidad_mp = 0; // contador de cantidad para Mercado Pago por día
                $total_mp = 0;    // acumulador de monto para Mercado Pago por día

                foreach ($cargas as $carga) {
                    if ($carga->fecha == $fecha_actual_loop) {
                        if ($carga->formato == 'Efectivo') {
                            $cantidad_efec = $cantidad_efec + 1;
                            $total_efec = $total_efec + $carga->monto;
                        } elseif ($carga->formato == 'Virtual') {
                            $cantidad_virt = $cantidad_virt + 1;
                            $total_virt = $total_virt + $carga->monto;
                        } elseif ($carga->formato == 'MP') {
                            $cantidad_mp = $cantidad_mp + 1;
                            $total_mp = $total_mp + $carga->monto;
                        }
                    }
                }
                
                $detalle[$i]['fecha'] = $fecha_actual_loop;
                $detalle[$i]['cantidad_efectivo'] = $cantidad_efec;
                $detalle[$i]['total_efectivo'] = $total_efec;
                $detalle[$i]['cantidad_virtual'] = $cantidad_virt;
                $detalle[$i]['total_virtual'] = $total_virt;
                $detalle[$i]['cantidad_mercadopago'] = $cantidad_mp;
                $detalle[$i]['total_mercadopago'] = $total_mp;

                $i = $i + 1;
                // Avanzar al siguiente dia
                $fecha_actual_loop = date('Y-m-d', strtotime($fecha1) + ($i * 24 * 60 * 60));
            }

            $data['detalle'] = $detalle;
            $data['vendedor'] = $this->vendedor_model->getVendedorById($id_vendedor);
            $data['fecha1'] = date("d-m-Y", $strtime1);
            $data['fecha2'] = date("d-m-Y", $strtime2);
            
            // Totales generales para todo el rango de fechas
            $data['cantidad_efectivo'] = array_sum(array_column($detalle, 'cantidad_efectivo'));
            $data['cantidad_virtual'] = array_sum(array_column($detalle, 'cantidad_virtual'));
            $data['total_efectivo'] = array_sum(array_column($detalle, 'total_efectivo'));
            $data['total_virtual'] = array_sum(array_column($detalle, 'total_virtual'));
            
            // Nuevo: Totales generales para Mercado Pago
            $data['cantidad_mercadopago'] = array_sum(array_column($detalle, 'cantidad_mercadopago'));
            $data['total_mercadopago'] = array_sum(array_column($detalle, 'total_mercadopago'));

            $dompdf = new Dompdf();
            $dompdf->loadHtml($this->load->view('pdf_view/cajaSemanal', $data, true));
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $dompdf->stream("Cierre_{$fecha1}_{$fecha2}.pdf", array("Attachment" => 0));
        } else {
            redirect(base_url('admin/informe'));
        }
    }

    public function descargarResumenPedidosSemana()
    {
        if ($this->input->method() == 'post') {
            $id_vendedor = $this->session->userdata('id_vendedor');
            $strtime1 = strtotime($this->input->post('semana_fecha_1'));
            $strtime2 = strtotime($this->input->post('semana_fecha_2'));
            $fecha1 = date('Y-m-d', $strtime1);
            $fecha2 = date('Y-m-d', $strtime2);
            $compras = $this->vendedor_model->getComprasByRangeFechaForPDF($fecha1, $fecha2);

            $fecha  = $fecha1;
            $i = 0;
            $detalle = array();
            while ($fecha <= $fecha2) {
                $basico = 0;
                $vegano = 0;
                $celiaco = 0;
                foreach ($compras as $compra) {
                    if ($compra->dia_comprado == $fecha) {
                        if ($compra->menu == 'Basico') {
                            $basico = $basico + 1;
                        } else if ($compra->menu == 'Veggie') {
                            $vegano = $vegano + 1;
                        } else if ($compra->menu == 'Celiaco') {
                            $celiaco = $celiaco + 1;
                        }
                    }
                }
                $detalle[$i]['fecha'] = $fecha;
                $detalle[$i]['basico'] = $basico;
                $detalle[$i]['vegano'] = $vegano;
                $detalle[$i]['celiaco'] = $celiaco;
                $i = $i + 1;
                $fecha = date('Y-m-d', $strtime1 + ($i * 24 * 60 * 60));
            }

            $data['detalle'] = $detalle;
            $data['vendedor'] = $this->vendedor_model->getVendedorById($id_vendedor);
            $data['total'] = array_sum(array_column($detalle, 'total'));
            $data['fecha1'] = date("d-m-Y", $strtime1);
            $data['fecha2'] = date("d-m-Y", $strtime2);
            $data['cantidad'] = array_sum(array_column($detalle, 'cantidad'));

            $dompdf = new Dompdf();
            $dompdf->loadHtml($this->load->view('pdf_view/resumenSemanal', $data, true));
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $dompdf->stream("Cierre_{$fecha1}_{$fecha2}.pdf", array("Attachment" => 0)); //0 para ver, 1 para descargar
        } else {
            redirect(base_url('admin/informe'));
        }
    }

    public function historialCargas()
    {
        $id_vendedor = $this->session->userdata('id_vendedor');
        $cargas = $this->vendedor_model->getCargasByIdvendedor($id_vendedor);
        $data = [
            'titulo' => 'Historial de cargas',
            'cargas' => $cargas
        ];
        $this->load->view('header', $data);
        $this->load->view('historial', $data);
        $this->load->view('general/footer');
    }

    public function updateMenu()
    {
        $menu = $this->vendedor_model->getMenu();

        $data['titulo'] = 'Actualizar Menu';
        $data['menu'] = $menu;

        if ($this->input->method() == 'post') {
            for ($i = 1; $i <= 5; $i++) {
                $this->form_validation->set_rules("basico_{$i}", 'Menu Basico', 'required');
                $this->form_validation->set_rules("veggie_{$i}", 'Menu Veggie', 'required');
                $this->form_validation->set_rules("sin_tacc_{$i}", 'Menu Sin TACC', 'required');
            }
            if ($this->form_validation->run() == FALSE) {
                $this->load->view('header', $data);
                $this->load->view('menu', $data);
                $this->load->view('general/footer');
            } else {
                for ($i = 1; $i <= 5; $i++) {
                    $menu_item['menu1'] = $this->input->post("basico_{$i}");
                    $menu_item['menu2'] = $this->input->post("veggie_{$i}");
                    $menu_item['menu3'] = $this->input->post("sin_tacc_{$i}");
                    $this->vendedor_model->updateMenu($i, $menu_item);

                    // $historialMenu = [
                    //     'id_vendedor' => $this->session->id_vendedor,
                    //     'id_dia' => $i,
                    //     'menu1' => $menu_item['menu1'],
                    //     'menu2' => $menu_item['menu2'],
                    //     'menu3' => $menu_item['menu3'],
                    //     'fecha' => date('Y-m-d', time())
                    // ];
                    // $this->vendedor_model->addHistorialMenu($historialMenu);
                }
                redirect(base_url('admin/menu'));
            }
        } else {
            $this->load->view('header', $data);
            $this->load->view('menu', $data);
            $this->load->view('general/footer');
        }
    }

    public function triggerPasswordRecovery($documento)
    {
        $this->load->model('usuario/login_model');

        // Buscamos al usuario por el documento proporcionado en la URL
        $usuario = $this->login_model->getUserByDocumento($documento);

        if ($usuario) {
            // Generamos un token único para la solicitud
            $str = "{$usuario->id}_{$usuario->mail}";
            $token = md5($str);

            // Si existe una solicitud anterior, la eliminamos para generar una nueva.
            $existing_recovery = $this->login_model->getRecoveryByToken($token);
            if ($existing_recovery) {
                $this->login_model->deleteRecoverylogById($existing_recovery->id);
            }

            // Preparamos los datos para el correo electrónico
            $data['nombre'] = $usuario->nombre;
            $data['apellido'] = $usuario->apellido;
            $data['dni'] = $usuario->documento;
            $data['tipo'] = 'solicitud';
            $data['link'] = base_url("usuario/recovery/{$token}");
            $subject = "Solicitud de restablecimiento de contraseña";
            $message = $this->load->view('general/correos/cambio_contraseña', $data, true);

            // Intentamos enviar el correo
            if ($this->generalticket->smtpSendEmail($usuario->mail, $subject, $message)) {
                // Si el correo se envía, registramos la nueva solicitud en la base de datos
                $this->login_model->addLogPassrecovery(['fecha' => date('Y-m-d'), 'hora' => date('H:i:s'), 'id_usuario' => $usuario->id, 'token' => $token]);
                $status = 'success';
                $message = urlencode("Se ha enviado correctamente el correo de restablecimiento de contraseña al usuario.");
            } else {
                $status = 'error';
                $message = urlencode("Hubo un error al intentar enviar el correo electrónico.");
            }
        } else {
            $status = 'error';
            $message = urlencode("No se encontró un usuario con el documento proporcionado.");
        }
        // Redirigimos de vuelta al panel principal del administrador
        redirect(base_url('admin?status=' . $status . '&msg=' . $message));
    }
}