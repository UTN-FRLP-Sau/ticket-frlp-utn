<?php

use phpDocumentor\Reflection\PseudoTypes\True_;

defined('BASEPATH') or exit('No direct script access allowed');

class Ticket extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('ticket_model');

        if (!$this->session->userdata('is_user')) {
            redirect(base_url('login'));
        }
    }

    public function estadoComedor()
    {
        //Con esta funcion se verifica si el comedor se encuentra cerrado, definiendo los periodos
        //entre la fecha de apertura y cierre, y las vacaciones de invierno
        $configuracion = $this->ticket_model->getConfiguracion();
        $hoy = date('Y-m-d', time());
        $apertura = $configuracion[0]->apertura;
        $vaca_ini = $configuracion[0]->vacaciones_i;
        $vaca_fin = $configuracion[0]->vacaciones_f;
        $cierre = $configuracion[0]->cierre;

        if ($hoy >= $apertura && $hoy <= $vaca_ini) {
            //Primer semestre
            return true;
        } elseif ($hoy >= $vaca_fin && $hoy <= $cierre) {
            //Segundo semestre
            return true;
        } elseif ($_SERVER['CI_ENV'] == 'development') {
            return true;
        }
        return false;
    }

    public function estadoCompra()
    {
        //Con esta funcion se verifica si el comedor habilitado para usarse, definindo los periodos
        // de compra entre el lunes y el jueves
        $configuracion = $this->ticket_model->getConfiguracion();
        $hoy = date('N');
        $ahora = date('H:i:s', time());
        $dia_ini = $configuracion[0]->dia_inicial;
        $dia_fin = $configuracion[0]->dia_final;
        $hora_fin = $configuracion[0]->hora_final;

        if ($hoy >= $dia_ini && $hoy < $dia_fin) {
            //Si hoy esta entre el lunes y el jueves
            return true;
        } elseif ($hoy == $dia_fin && $ahora <= $hora_fin) {
            //y si es viernes hasta las 12:00AM
            return true;
        } elseif ($_SERVER['CI_ENV'] == 'development') {
            return true;
        }
        return false;
    }

    public function index()
    {
        $id_usuario = $this->session->userdata('id_usuario');
        $usuario = $this->ticket_model->getUserById($id_usuario);

        if ($this->estadoComedor()) {
            if ($this->estadoCompra()) {

                $numWeeksToDisplay = 4; // Define semanas a mostrar
                $weeksData = [];
                $all_dates_in_range = []; // Para almacenar todas las fechas de todas las semanas

                // fecha y hora actual con la zona horaria
                $currentDateTime = new DateTime('now');
                $currentDate = new DateTime($currentDateTime->format('Y-m-d')); // Solo la fecha de hoy, sin la hora

                // Obtiene el lunes de la semana actual
                $mondayOfCurrentWeek = clone $currentDate;
                if ($mondayOfCurrentWeek->format('N') !== '1') { // Si hoy no es lunes, va al lunes más cercano anterior
                    $mondayOfCurrentWeek->modify('last monday');
                }

                // Primera iteración para recolectar todas las fechas y hacer una única consulta
                for ($w = 0; $w < $numWeeksToDisplay; $w++) {
                    $mondayOfThisWeek = clone $mondayOfCurrentWeek;
                    $mondayOfThisWeek->modify('+' . $w . ' week');

                    for ($d = 0; $d < 5; $d++) { // Lunes a Viernes
                        $dayDate = clone $mondayOfThisWeek;
                        $dayDate->modify('+' . $d . ' day');
                        $all_dates_in_range[] = $dayDate->format('Y-m-d');
                    }
                }
                
                // Realizar una única consulta para todas las compras y feriados dentro del rango total
                $minDateRange = !empty($all_dates_in_range) ? min($all_dates_in_range) : date('Y-m-d');
                $maxDateRange = !empty($all_dates_in_range) ? max($all_dates_in_range) : date('Y-m-d');


                $compras_usuario_total = $this->ticket_model->getComprasInRangeByIdUser($minDateRange, $maxDateRange, $id_usuario);
                $feriados_total = $this->ticket_model->getFeriadosInRange($minDateRange, $maxDateRange);

                // Formatea los comprados para fácil acceso
                $comprados_con_turno = [];
                foreach ($compras_usuario_total as $compra) {
                    $comprados_con_turno[] = $compra->dia_comprado . '_' . $compra->turno;
                }
                
                // Itera para construir la estructura de datos para la vista
                for ($w = 0; $w < $numWeeksToDisplay; $w++) {
                    $week = [];
                    $mondayOfThisWeek = clone $mondayOfCurrentWeek;
                    $mondayOfThisWeek->modify('+' . $w . ' week');

                    for ($d = 0; $d < 5; $d++) { // Lunes a Viernes
                        $dayDate = clone $mondayOfThisWeek;
                        $dayDate->modify('+' . $d . ' day');

                        $date_ymd = $dayDate->format('Y-m-d');
                        $dayOfWeekNumber = $dayDate->format('N'); // 1 (for Monday) through 7 (for Sunday)
                        $spanishDayNames = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];
                        $dayName = $spanishDayNames[$dayOfWeekNumber - 1]; // Ajuste de indice para el array

                        $dia_comprado_mediodia = in_array($date_ymd . '_manana', $comprados_con_turno);
                        $dia_comprado_noche = in_array($date_ymd . '_noche', $comprados_con_turno);
                        $es_feriado = in_array($date_ymd, array_column($feriados_total, 'fecha'));
                        
                        $es_pasado = ($dayDate < $currentDate); // Compara la fecha de la vianda con la fecha actual (solo día)

                        $week[] = [
                            'day_name'          => $dayName,
                            'date_display'      => $dayDate->format('d'),
                            'date_ymd'          => $date_ymd,
                            'comprado_mediodia' => $dia_comprado_mediodia,
                            'comprado_noche'    => $dia_comprado_noche,
                            'es_feriado'        => $es_feriado,
                            'es_pasado'         => $es_pasado
                        ];
                    }
                    $weeksData[] = $week;
                }

                $data = [
                    'titulo' => 'Comprar Viandas',
                    'usuario' => $usuario,
                    'weeksData' => $weeksData, // pasamos los datos estructurados por semana
                    'costoVianda' => $this->ticket_model->getCostoByID($usuario->id_precio)
                ];

                $this->load->view('usuario/header', $data);
                $this->load->view('index', $data);
                $this->load->view('general/footer');
            } else {
                $data = [
                    'titulo' => 'Comprar Viandas',
                    'alerta' => "<p>Fuera del horario de compra</p><p>La compra se realiza desde el Lunes hasta el Viernes a las {$this->config->item('hora_final')}</p>"
                ];

                $this->load->view('usuario/header', $data);
                $this->load->view('alerta_comedor_cerrado', $data);
                $this->load->view('general/footer');
            }
        } else {
            $data = [
                'titulo' => 'Comprar Viandas',
                'alerta' => '<p>El comedor no funciona en este Periodo</p>'
            ];

            $this->load->view('usuario/header', $data);
            $this->load->view('alerta_comedor_cerrado', $data);
            $this->load->view('general/footer');
        }
    }

    public function compra()
    {
        $id_usuario = $this->session->userdata('id_usuario');
        $usuario = $this->ticket_model->getUserById($id_usuario);
        $costoVianda = $this->ticket_model->getCostoByID($usuario->id_precio);

        $seleccion = [];
        $totalCompraCalculado = 0;

        $postChecks = $this->input->post('check');
        $postMenus = $this->input->post('selectMenu');

        if (!empty($postChecks)) {
            foreach ($postChecks as $date_ymd => $turnosSeleccionados) {
                foreach ($turnosSeleccionados as $turno => $value) {
                    $tipoServicio = "Comer aqui"; 
                    $menuSeleccionado = isset($postMenus[$date_ymd][$turno]) ? $postMenus[$date_ymd][$turno] : null;

                    if ($menuSeleccionado) { 
                        $dayOfWeek = new DateTime($date_ymd);
                        $spanishDayNames = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];
                        $dia_semana_nombre = $spanishDayNames[$dayOfWeek->format('N') - 1];

                        $seleccion[] = [
                            'dia' => $dia_semana_nombre,
                            'dia_comprado' => $date_ymd,
                            'tipo' => $tipoServicio,
                            'turno' => $turno,
                            'menu' => $menuSeleccionado,
                            'precio' => $costoVianda // El precio de una vianda individual
                        ];
                        $totalCompraCalculado += $costoVianda; // Sumamos el costo de cada vianda al total
                    }
                }
            }
        }
        
        if (empty($seleccion)) {
            redirect(base_url('comedor/ticket'));
        }

        $external_reference = $id_usuario . '-' . time();

        $this->ticket_model->guardarCompraPendiente([
            'external_reference' => $external_reference,
            'id_usuario' => $id_usuario,
            'datos' => json_encode($seleccion),
            'total' => $totalCompraCalculado,
            'procesada' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $this->session->set_userdata('external_reference', $external_reference);
        log_message('debug', 'TICKET: external_reference guardada en sesión: ' . $external_reference);
        redirect(base_url('comedor/pago/comprar'));
    }

    public function compraSuccess()
    {
        $data['titulo'] = 'Confirmacion';
        $id_transaccion= $this->session->flashdata('transaccion');
        $id_usuario = $this->session->userdata('id_usuario');

        $cargas = $this->ticket_model->getCargaByTransaccion($id_transaccion);
        $usuario = $this->ticket_model->getUserById($id_usuario);

        $data['transaccion'] = $id_transaccion;
        $data['tipo'] = 'compra';


        if ($id_transaccion) {
            $compras = $this->ticket_model->getComprasByIDTransaccion($id_transaccion);
            $data['compras']=$compras;
            $this->session->set_flashdata('transaccion', $id_transaccion);

            if ($this->input->method() == 'post') {
                $costoVianda = $this->ticket_model->getCostoByID($usuario->id_precio);
                $id_transaccion= $this->session->flashdata('transaccion');
                //Confeccion del correo del recivo
                $usuario = $this->ticket_model->getUserById($id_usuario);
                $compras = $this->ticket_model->getComprasByIDTransaccion($id_transaccion);
                $dataRecivo['compras'] = $compras;
                $dataRecivo['total'] = $costoVianda * count($compras);
                $dataRecivo['fechaHoy'] = date('d/m/Y', time());
                $dataRecivo['horaAhora'] = date('H:i:s', time());
                $dataRecivo['recivoNumero'] = $id_transaccion;

                $subject = "Recibo de compra del comedor";
                $message = $this->load->view('general/correos/recibo_compra', $dataRecivo, true);

                if ($this->generalticket->smtpSendEmail($usuario->mail, $subject, $message)) {
                    redirect(base_url('usuario'));
                }
            } else {
                $this->load->view('usuario/header', $data);
                $this->load->view('comedor/compra_confirmacion', $data);
                $this->load->view('general/footer');
            }
        } else {
            redirect(base_url('usuario'));
        }
    }

    public function devolverCompra()
    {
        log_message('debug', 'DevolverCompra: Método iniciado.');

        // Verifica si el comedor está abierto para devoluciones
        if (!$this->estadoComedor()) {
            $data = [
                'titulo' => 'Devolver Compras',
                'alerta' => "<p>Comedor cerrado</p>"
            ];
            $this->load->view('usuario/header', $data);
            $this->load->view('alerta_comedor_cerrado', $data);
            $this->load->view('general/footer');
            return;
        }

        // Verifica si el horario de compra/devolución está habilitado
        if (!$this->estadoCompra()) {
            $data = [
                'titulo' => 'Devolver Compras',
                'alerta' => "<p>Fuera del horario de devolución</p><p>La devolución se realiza desde el Lunes hasta el Viernes a las {$this->config->item('hora_final')}</p>"
            ];
            $this->load->view('usuario/header', $data);
            $this->load->view('alerta_comedor_cerrado', $data);
            $this->load->view('general/footer');
            return; // Detener ejecución
        }

        // datos del usuario logueado
        $id_usuario = $this->session->userdata('id_usuario');
        $usuario = $this->ticket_model->getUserById($id_usuario);
        // el costo de la vianda
        $costoVianda = $this->ticket_model->getCostoById($usuario->id_precio); 
        $saldoUser = $usuario->saldo; // Saldo actual del usuario

        // RANGO DE FECHAS PARA LAS COMPRAS ELEGIBLES PARA DEVOLUCIÓN ---
        // Este rango reflejará la lógica de 4 semanas a partir de la fecha actual.

        // 1. Encuentra el lunes de la semana actual (base para el cálculo de 4 semanas)
        $currentDate = new DateTime();
        $mondayOfCurrentWeek = clone $currentDate;
        if ($mondayOfCurrentWeek->format('N') !== '1') { // Si hoy no es lunes (1=lunes, 7=domingo)
            $mondayOfCurrentWeek->modify('last monday'); // Retrocede al lunes anterior
        }

        // 2. Calcula la fecha del viernes de la cuarta semana a partir de ese lunes
        $endOfFourthWeek = clone $mondayOfCurrentWeek;
        $endOfFourthWeek->modify('+3 weeks'); // Avanza al lunes de la 4ª semana (semana 0, 1, 2, 3)
        $endOfFourthWeek->modify('+4 days');  // Avanza 4 días desde ese lunes para llegar al viernes
        
        // 3. Establece el rango de fechas para la consulta de devoluciones
        $start_date = date('Y-m-d'); // Las devoluciones son para viandas futuras, por lo tanto, desde hoy
        $end_date = $endOfFourthWeek->format('Y-m-d'); // Hasta el viernes de la cuarta semana

        // Caso de seguridad: Si por alguna razón la fecha de inicio es posterior a la de fin
        if ($start_date > $end_date) {
            $end_date = $start_date; 
        }

        log_message('debug', 'DevolverCompra: Rango de fechas para devoluciones: ' . $start_date . ' a ' . $end_date);

        // Obtiene las compras del usuario en el rango definido.
        $data['compras'] = $this->ticket_model->getComprasInRangeByIdUser($start_date, $end_date, $id_usuario);

        $data['titulo'] = 'Devolucion de compras';
        $data['devolucion'] = TRUE; // Bandera para la vista

        // --- MANEJO DE LA SOLICITUD POST (cuando el usuario envía el formulario) ---
        if ($this->input->method() == 'post') {
            log_message('debug', 'DevolverCompra: Recibida solicitud POST.');
            $ids_a_devolver = $this->input->post('devolver'); // Captura el array de IDs de los checkboxes

            if (!empty($ids_a_devolver) && is_array($ids_a_devolver)) {
                log_message('debug', 'DevolverCompra: IDs a devolver: ' . json_encode($ids_a_devolver));
                $n_devolucion = 0; // Contador de devoluciones exitosas
                $monto_total_devolucion = 0; // Suma total del dinero a devolver
                $log_compras_insertadas_ids = []; // Para guardar IDs temporales de log_compra

                foreach ($ids_a_devolver as $id_compra) {
                    $compra = $this->ticket_model->getCompraById($id_compra); // Obtiene los detalles de la compra por su ID

                    if ($compra && $compra->id_usuario == $id_usuario) {
                        $data_log = [
                            'fecha' => date('Y-m-d', time()),
                            'hora' => date('H:i:s', time()),
                            'dia_comprado' => $compra->dia_comprado,
                            'id_usuario' => $id_usuario,
                            'precio' => $compra->precio,
                            'tipo' => $compra->tipo, 
                            'turno' => $compra->turno,
                            'menu' => $compra->menu,
                            'transaccion_tipo' => 'Devolucion', 
                            'transaccion_id' => -$id_usuario
                        ];
                        
                        // Llama al modelo para eliminar la compra
                        // Este método realiza un DELETE del registro de la compra.
                        if ($this->ticket_model->removeCompra($id_compra)) { 
                            $this->ticket_model->addLogCompra($data_log); // Registra la devolución en el log
                            $log_id_temp = $this->db->insert_id(); // Obtiene el ID del log insertado
                            $log_compras_insertadas_ids[] = $log_id_temp; // Guarda el ID temporal

                            $n_devolucion++; // Incrementa el contador de devoluciones
                            $monto_total_devolucion += $compra->precio; // Suma el precio de esta vianda al total a devolver
                            log_message('debug', 'DevolverCompra: Compra ID ' . $id_compra . ' procesada para devolución (eliminada).');
                        } else {
                            log_message('error', 'DevolverCompra: Falló el proceso de eliminación de la compra ID: ' . $id_compra);                 
                        }
                    } else {
                        log_message('warning', 'DevolverCompra: Intento de devolver compra inválida o no elegible: ' . $id_compra);
                        // Mensaje si se intenta devolver una vianda que no cumple las reglas
                    }
                }

                // Si se realizaron devoluciones exitosas
                if ($n_devolucion > 0) {
                    // Actualiza el saldo del usuario con el monto total devuelto
                    $nuevo_saldo = $saldoUser + $monto_total_devolucion;
                    $this->ticket_model->updateSaldoByIDUser($id_usuario, $nuevo_saldo);
                    log_message('debug', 'DevolverCompra: Saldo de usuario ' . $id_usuario . ' actualizado a: ' . $nuevo_saldo);

                    // Genera la transacción de devolución en la tabla de transacciones
                    $transaction_devolucion = [
                        'fecha' => date('Y-m-d', time()),
                        'hora' => date('H:i:s', time()),
                        'id_usuario' => $id_usuario,
                        'transaccion' => 'Devolucion',
                        'monto' => $monto_total_devolucion, // Monto total devuelto
                        'saldo' => $nuevo_saldo // Saldo final después de la devolución
                    ];
                    $id_transaccion_real = $this->ticket_model->addTransaccion($transaction_devolucion);
                    log_message('debug', 'DevolverCompra: Transacción de devolución creada con ID: ' . $id_transaccion_real);

                    // Actualiza los registros de log_compra con el ID de transacción real
                    foreach ($log_compras_insertadas_ids as $log_id) {
                        $this->ticket_model->updateTransactionInLogCompraByID($log_id, $id_transaccion_real);
                    }
                    log_message('debug', 'DevolverCompra: Logs de compra actualizados con transacción real ID: ' . $id_transaccion_real);

                    // Mensaje de éxito y redirección a la página de confirmación
                    $this->session->set_flashdata('success', 'Se han devuelto ' . $n_devolucion . ' vianda(s) exitosamente. Tu saldo ha sido actualizado.');
                    $this->session->set_flashdata('transaccion', $id_transaccion_real); // Pasa el ID de la transacción real
                    redirect(base_url('usuario/devolver/success'));
                } else {
                    log_message('info', 'DevolverCompra: No se pudo devolver ninguna compra o no se seleccionaron viandas válidas.');
                    $this->session->set_flashdata('info', 'No se pudieron devolver las viandas seleccionadas o no seleccionaste ninguna vianda válida para devolver.');
                    redirect(base_url('usuario/devolver_compra')); // Redirige para refrescar y mostrar el mensaje
                }
            } else {
                log_message('warning', 'DevolverCompra: No se seleccionaron compras para devolver (array vacío).');
                $this->session->set_flashdata('info', 'Por favor, selecciona al menos una vianda para devolver.');
                redirect(base_url('usuario/devolver_compra')); // Redirige para refrescar y mostrar el mensaje
            }
        } 
        // --- FIN DEL MANEJO POST ---

        // Si es una solicitud GET (carga inicial de la página) o después de una redirección POST,
        // se carga la vista para mostrar las viandas disponibles.
        $this->load->view('usuario/header', $data);
        $this->load->view('usuario/devolver_compra', $data);
        $this->load->view('general/footer');
    }

        public function devolverCompraSuccess()
    {
        $data['titulo'] = 'Confirmacion';
        $id_transaccion= $this->session->flashdata('transaccion');
        $id_usuario = $this->session->userdata('id_usuario');

        $usuario = $this->ticket_model->getUserById($id_usuario);

        $data['transaccion'] = $id_transaccion;
        $data['tipo'] = 'devolucion';

        if ($id_transaccion) {
            $compras = $this->ticket_model->getlogComprasByIDTransaccion($id_transaccion);
            $data['compras']=$compras;
            $costoVianda = $this->ticket_model->getCostoById($usuario->id_precio);
            $this->session->set_flashdata('transaccion', $id_transaccion);

            if ($this->input->method() == 'post') {
                $id_transaccion= $this->session->flashdata('transaccion');
                //Confeccion del correo del recivo
                $dataRecivo['compras'] = $compras;
                $dataRecivo['total'] = $costoVianda * count($compras);
                $dataRecivo['fechaHoy'] = date('d/m/Y', time());
                $dataRecivo['horaAhora'] = date('H:i:s', time());
                $dataRecivo['recivoNumero'] = $id_transaccion;

                $subject = "Recibo de devolucion del comedor";
                $message = $this->load->view('general/correos/recibo_devolucion', $dataRecivo, true);

                if ($this->generalticket->smtpSendEmail($usuario->mail, $subject, $message)){
                    redirect(base_url('usuario/devolver_compra'));
                }
            } else {
                $this->load->view('usuario/header', $data);
                $this->load->view('comedor/compra_confirmacion', $data);
                $this->load->view('general/footer');
            }
        } else {
            redirect(base_url('usuario'));
        }
    }
}