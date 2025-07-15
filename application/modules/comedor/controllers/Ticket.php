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


    public function index()
    {
        // Carga de modelos y helpers, y lógica de autenticación (mantén tu código existente aquí)
        // Por ejemplo, las líneas que cargan el modelo ticket_model y obtienen los datos del usuario:
        $id_usuario = $this->session->userdata('id_usuario');
        $usuario = $this->ticket_model->getUserById($id_usuario);

        // --- DIAGNÓSTICO: Cargar explícitamente la base de datos si no está ya cargada ---
        // Esto puede ayudar si hay algún problema con la carga automática en este contexto específico.
        $this->load->database(); 

        // --- Lógica para el modal de compra pendiente ---
        $show_pending_purchase_modal = false;
        $pending_purchase_details = null;
        $pending_purchase_viandas = [];

        $external_reference_from_session = $this->session->userdata('external_reference');
        log_message('debug', 'TICKET_INDEX: external_reference_from_session: ' . ($external_reference_from_session ? $external_reference_from_session : 'VACIO'));

        if ($external_reference_from_session) {
            // **IMPORTANTE: Primero llamas al modelo y asignas el resultado**
            $compra_pendiente = $this->ticket_model->getCompraPendiente($external_reference_from_session);

            // **Luego, realizas los logs con el valor YA asignado de $compra_pendiente**
            log_message('debug', 'TICKET_INDEX: Raw result of getCompraPendiente: ' . print_r($compra_pendiente, true));

            // **CORRECCIÓN DE LÓGICA:** Aseguramos que $compra_pendiente sea un objeto válido antes de acceder a sus propiedades
            if ($compra_pendiente && property_exists($compra_pendiente, 'mp_estado')) { 
                log_message('debug', 'TICKET_INDEX: mp_estado from retrieved purchase: ' . $compra_pendiente->mp_estado); 

                if ($compra_pendiente->mp_estado !== 'approved') {
                    $show_pending_purchase_modal = true;
                    $pending_purchase_details = $compra_pendiente;
                    $pending_purchase_viandas = $this->ticket_model->getViandasCompraPendiente($compra_pendiente->id);
                    log_message('debug', 'TICKET_INDEX: Modal de compra pendiente marcado para mostrar.');
                } else {
                    // Si la compra se encontró y está aprobada, la limpiamos de la sesión para no volver a preguntar
                    $this->session->unset_userdata('external_reference'); 
                    log_message('debug', 'TICKET_INDEX: Modal de compra pendiente NO marcado. Razón: Compra aprobada. external_reference limpiada.');
                }
            } else {
                // Si $compra_pendiente es nulo/falso (no se encontró) o no tiene la propiedad mp_estado, limpiar la referencia
                $this->session->unset_userdata('external_reference');
                log_message('debug', 'TICKET_INDEX: Modal de compra pendiente NO marcado. Razón: No se encontró compra o es inválida. external_reference limpiada.');
            }
        }

        if ($this->estadoComedor()) {
            // Obtener la configuración aquí para todas las validaciones de fechas y horarios
            $configuracion = $this->ticket_model->getConfiguracion();
            $vacaciones_invierno_inicio = $configuracion[0]->vacaciones_i;
            $vacaciones_invierno_fin = $configuracion[0]->vacaciones_f;
            $dia_inicial_compra = (int)$configuracion[0]->dia_inicial; // Lunes = 1
            $dia_final_compra = (int)$configuracion[0]->dia_final;     // Viernes = 5
            $hora_final_compra = $configuracion[0]->hora_final;       // Ej. '12:00:00'

            $numWeeksToDisplay = 5; // Define 5 semanas a mostrar
            $weeksData = [];
            $all_dates_in_range = [];

            // Fecha y hora actual del sistema
            $currentDateTime = new DateTime('now');
            $currentDate = new DateTime($currentDateTime->format('Y-m-d')); // Solo la fecha de hoy, sin la hora
            $currentTime = $currentDateTime->format('H:i:s');
            $currentDayOfWeek = (int)$currentDateTime->format('N'); // 1 (Lunes) a 7 (Domingo)

            // Obtiene el lunes de la semana actual (la semana que incluye $currentDate)
            $mondayOfCurrentWeek = clone $currentDate;
            if ($mondayOfCurrentWeek->format('N') !== '1') { // Si hoy no es lunes, va al lunes más cercano anterior
                $mondayOfCurrentWeek->modify('last monday');
            }

            // Calcular el final del período de compra para la *próxima* semana.
            // Esto es, el día y la hora límite de la semana actual para comprar para la semana siguiente.
            $endOfCurrentPurchaseWindow = clone $mondayOfCurrentWeek;
            // Avanza al día final de compra de la semana actual (ej. Viernes)
            $endOfCurrentPurchaseWindow->modify('+' . ($dia_final_compra - 1) . ' days');
            // Establece la hora de cierre
            list($hora, $minuto, $segundo) = explode(':', $hora_final_compra);
            $endOfCurrentPurchaseWindow->setTime((int)$hora, (int)$minuto, (int)$segundo);

            // Determinar si ya se pasó el límite de compra para la próxima semana
            // Es decir, si es después del viernes a la hora de cierre, o si es sábado/domingo.
            $isPastPurchaseCutoffForNextWeek = false;
            // Si el día actual es mayor que el día final de compra (ej. sábado/domingo)
            if ($currentDayOfWeek > $dia_final_compra) {
                $isPastPurchaseCutoffForNextWeek = true;
            } elseif ($currentDayOfWeek === $dia_final_compra) { // Si es el día final de compra (ej. viernes)
                // Y la hora actual es mayor o igual a la hora final de compra
                if ($currentTime >= $hora_final_compra) {
                    $isPastPurchaseCutoffForNextWeek = true;
                }
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

            // Realiza una única consulta para todas las compras y feriados dentro del rango total
            $minDateRange = !empty($all_dates_in_range) ? min($all_dates_in_range) : date('Y-m-d');
            $maxDateRange = !empty($all_dates_in_range) ? max($all_dates_in_range) : date('Y-m-d');

            $compras_usuario_total = $this->ticket_model->getComprasInRangeByIdUser($minDateRange, $maxDateRange, $id_usuario);
            $feriados_total = $this->ticket_model->getFeriadosInRange($minDateRange, $maxDateRange);

            // Formatea los comprados para fácil acceso, incluyendo el tipo de menú
            $comprados_con_turno = [];
            foreach ($compras_usuario_total as $compra) {
                $comprados_con_turno[$compra->dia_comprado][$compra->turno] = $compra->menu;
            }

            // Itera para construir la estructura de datos para la vista
            for ($w = 0; $w < $numWeeksToDisplay; $w++) {
                $week = [];
                $mondayOfThisWeek = clone $mondayOfCurrentWeek;
                $mondayOfThisWeek->modify('+' . $w . ' week');

                // Variables para identificar la semana
                $isCurrentWeek = ($w === 0);
                $isNextWeek = ($w === 1);
                $isSecondWeekOrBeyond = ($w >= 2);
                
                // Calcula el viernes de la semana actual de esta iteración para el título
                $fridayOfThisWeek = clone $mondayOfThisWeek;
                $fridayOfThisWeek->modify('+4 days'); // Lunes + 4 días = Viernes

                // Formatea las fechas para el título de la semana
                $weekStartDateDisplay = $mondayOfThisWeek->format('d \d\e F');
                $weekEndDateDisplay = $fridayOfThisWeek->format('d \d\e F');

                // Para que 'F' (nombre del mes) esté en español
                $meses = array(
                    'January' => 'Enero', 'February' => 'Febrero', 'March' => 'Marzo',
                    'April' => 'Abril', 'May' => 'Mayo', 'June' => 'Junio',
                    'July' => 'Julio', 'August' => 'Agosto', 'September' => 'Septiembre',
                    'October' => 'Octubre', 'November' => 'Noviembre', 'December' => 'Diciembre'
                );
                $weekStartDateDisplay = strtr($weekStartDateDisplay, $meses);
                $weekEndDateDisplay = strtr($weekEndDateDisplay, $meses); // Traduce el mes del viernes

                for ($d = 0; $d < 5; $d++) { // Lunes a Viernes
                    $dayDate = clone $mondayOfThisWeek;
                    $dayDate->modify('+' . $d . ' day');

                    $date_ymd = $dayDate->format('Y-m-d');
                    $dayOfWeekNumber = (int)$dayDate->format('N'); // 1 (Lunes) a 7 (Domingo)
                    $spanishDayNames = ['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado', 'domingo'];
                    $dayName = $spanishDayNames[$dayOfWeekNumber - 1]; // Ajuste de índice para el array

                    // Obtiene el tipo de menú comprado para cada turno
                    $comprado_mediodia_menu = isset($comprados_con_turno[$date_ymd]['manana']) ? $comprados_con_turno[$date_ymd]['manana'] : null;
                    $comprado_noche_menu = isset($comprados_con_turno[$date_ymd]['noche']) ? $comprados_con_turno[$date_ymd]['noche'] : null;

                    // Determina si hay compra
                    $dia_comprado_mediodia = ($comprado_mediodia_menu !== null);
                    $dia_comprado_noche = ($comprado_noche_menu !== null);

                    $es_receso_invernal = ($dayDate >= new DateTime($vacaciones_invierno_inicio) && $dayDate <= new DateTime($vacaciones_invierno_fin));
                    $es_feriado = in_array($date_ymd, array_column($feriados_total, 'fecha'));
                    $es_pasado = ($dayDate < $currentDate); // Compara la fecha de la vianda con la fecha actual (solo día)

                    // Lógica para deshabilitar la compra para cada día
                    $disable_purchase = false;

                    if ($es_pasado || $es_feriado || $es_receso_invernal) {
                        // Siempre deshabilitado para fechas pasadas, feriados o receso invernal.
                        $disable_purchase = true;
                    } elseif ($isCurrentWeek) {
                        // Siempre deshabilitado para la semana actual.
                        $disable_purchase = true;
                    } elseif ($isNextWeek) {
                        // Para la PRÓXIMA semana, se aplica la ventana de compra habitual.
                        // Si ya pasó la hora de corte de compra de la semana actual (viernes 12:00PM, o fin de semana),
                        // entonces la próxima semana está deshabilitada para la compra.
                        if ($isPastPurchaseCutoffForNextWeek) {
                            $disable_purchase = true;
                        }
                    }
                    // Si es la segunda semana o posterior ($isSecondWeekOrBeyond es true),
                    // y no es pasado, feriado, o receso, disable_purchase permanece false,
                    // permitiendo la compra.

                    // Override para entorno de desarrollo
                    //if ($_SERVER['CI_ENV'] == 'development') {
                    //    $disable_purchase = false;
                    //}

                    $week[] = [
                        'day_name'                => $dayName,
                        'date_display'            => $dayDate->format('d'),
                        'date_ymd'                => $date_ymd,
                        'comprado_mediodia'       => $dia_comprado_mediodia,
                        'comprado_noche'          => $dia_comprado_noche,
                        'comprado_mediodia_menu'  => $comprado_mediodia_menu,
                        'comprado_noche_menu'     => $comprado_noche_menu,
                        'es_feriado'              => $es_feriado,
                        'es_pasado'               => $es_pasado,
                        'es_receso_invernal'      => $es_receso_invernal,
                        'disable_purchase'        => $disable_purchase
                    ];
                }
                $weeksData[] = [
                    'week_index' => $w,
                    'week_start_date_display' => $weekStartDateDisplay,
                    'week_end_date_display'   => $weekEndDateDisplay,
                    'days' => $week
                ];
            }

            $data = [
                'titulo' => 'Comprar Viandas',
                'usuario' => $usuario,
                'weeksData' => $weeksData,
                'costoVianda' => $this->ticket_model->getCostoByID($usuario->id_precio),
                'permitir_ambos_turnos_mismo_dia' => $this->config->item('permitir_ambos_turnos_mismo_dia'),
                'vacaciones_invierno_inicio' => $vacaciones_invierno_inicio,
                'vacaciones_invierno_fin' => $vacaciones_invierno_fin
            ];

            $this->load->view('usuario/header', $data);
            $this->load->view('index', $data);
            $this->load->view('general/footer');

        } else {
            // El comedor no está habilitado en este período (vacaciones, cierre anual, etc.)
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

        $postMenus = $this->input->post('selectMenu');
        $erroresCompra = []; // Array para acumular mensajes de error
        $seleccion = []; 
        $totalCompraCalculado = 0;

        if (!empty($postMenus)) {
            foreach ($postMenus as $date_ymd => $turnosData) {
                $permitir_ambos_turnos = $this->config->item('permitir_ambos_turnos_mismo_dia');
                $selectedTurnsForDay = [];

                foreach (['manana', 'noche'] as $turno) {
                    if (isset($turnosData[$turno]) && !empty($turnosData[$turno])) {
                        $selectedTurnsForDay[$turno] = $turnosData[$turno];
                    }
                }

                // si se seleccionan ambos y no está permitido, generar error
                if (!$permitir_ambos_turnos && count($selectedTurnsForDay) > 1) {
                    $erroresCompra[] = "Error: Para el día " . date('d/m/Y', strtotime($date_ymd)) . " solo se permite seleccionar un turno de vianda (mañana o noche), no ambos.";
                    $selectedTurnsForDay = []; // Limpiar las selecciones para este día
                    log_message('error', 'Intento de compra dual de vianda para ' . $date_ymd . ' cuando la restricción está activa. Se ha impedido la compra para este día.');
                }

                foreach ($selectedTurnsForDay as $turno => $menuSeleccionado) {
                    $tipoServicio = "Comer aqui"; 
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
                            'precio' => $costoVianda
                        ];
                        $totalCompraCalculado += $costoVianda;
                    }
                }
            }
        }

        //  Verifica si hay errores de validación específicos
        if (!empty($erroresCompra)) {
            $this->session->set_flashdata('error_compra', $erroresCompra);
            redirect(base_url('comedor/ticket')); // Redirige a la página principal de selección con los errores
            return;
        }

        //  Verifica si, después de todo el procesamiento y filtrado, no quedó nada en $seleccion
        // Esto captura casos donde el formulario se envió vacío o todas las selecciones fueron invalidadas.
        if (empty($seleccion)) {
            redirect(base_url('comedor/ticket')); // Redirige a la página principal de selección
            return; // Detener la ejecución
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


    public function devolverCompra()
    {
        log_message('debug', 'DevolverCompra: Método iniciado.');

        // datos del usuario logueado
        $id_usuario = $this->session->userdata('id_usuario');
        $usuario = $this->ticket_model->getUserById($id_usuario);
        // el costo de la vianda
        $costoVianda = $this->ticket_model->getCostoById($usuario->id_precio);
        $saldoUser = $usuario->saldo; // Saldo actual del usuario

        // Obtener la configuración aquí para todas las validaciones de fechas y horarios
        $configuracion = $this->ticket_model->getConfiguracion();
        $dia_final_compra = (int)$configuracion[0]->dia_final;     // Viernes = 5 (o tu Jueves = 4)
        $hora_final_compra_str = $configuracion[0]->hora_final;    // Ej. '17:00:00'
        $vacaciones_invierno_inicio = $configuracion[0]->vacaciones_i;
        $vacaciones_invierno_fin = $configuracion[0]->vacaciones_f;

        // Fecha y hora actual del sistema
        $currentDateTime = new DateTime('now');
        $currentDate = new DateTime($currentDateTime->format('Y-m-d')); // Solo la fecha de hoy, sin la hora
        $currentTime = $currentDateTime->format('H:i:s');
        $currentDayOfWeek = (int)$currentDateTime->format('N'); // 1 (Lunes) a 7 (Domingo)

        // Convertir la hora final de compra a un objeto DateTime para una comparación precisa
        $hora_final_compra_dt = DateTime::createFromFormat('H:i:s', $hora_final_compra_str);
      
        $hora_final_compra_dt->setDate(
            (int)$currentDateTime->format('Y'),
            (int)$currentDateTime->format('m'),
            (int)$currentDateTime->format('d')
        );


        // Obtiene el lunes de la semana actual (la semana que incluye $currentDate)
        $mondayOfCurrentWeek = clone $currentDate;
        if ($mondayOfCurrentWeek->format('N') !== '1') { // Si hoy no es lunes, va al lunes más cercano anterior
            $mondayOfCurrentWeek->modify('last monday');
        }

        // Determinar si ya se pasó el límite de compra/devolución para la próxima semana
        $isPastPurchaseCutoffForNextWeek = false;

        // Si el día actual es mayor que el día final de compra (ej. sábado/domingo > jueves)
        if ($currentDayOfWeek > $dia_final_compra) {
            $isPastPurchaseCutoffForNextWeek = true;
            log_message('debug', 'DevolverCompra: Condición 1 (Día de la semana) - es un día posterior al día final de compra.');
        } elseif ($currentDayOfWeek === $dia_final_compra) { // Si es el día final de compra (ej. jueves)
            // Y la hora actual es mayor o igual a la hora final de compra
            // Usar objetos DateTime para una comparación de tiempo robusta
            if ($currentDateTime >= $hora_final_compra_dt) {
                $isPastPurchaseCutoffForNextWeek = true;
                log_message('debug', 'DevolverCompra: Condición 2 (Día y Hora) - es el día final y la hora actual ha pasado la hora de cierre.');
            } else {
                log_message('debug', 'DevolverCompra: Condición 2 (Día y Hora) - es el día final pero aún no ha pasado la hora de cierre.');
            }
        } else {
            log_message('debug', 'DevolverCompra: Condición 3 (Día de la semana) - aún no es el día final de compra.');
        }

    

        // Este rango reflejará la lógica de 5 semanas a partir de la fecha actual.

        // 1. Calcula la fecha de inicio para las devoluciones
        $start_date_dt = clone $mondayOfCurrentWeek; // Empezamos desde el lunes de la semana actual
        if ($isPastPurchaseCutoffForNextWeek) {
            // Si ya pasó el horario de corte (viernes después de hora_final o fin de semana),
            // la devolución solo es posible a partir de la segunda semana siguiente.
            $start_date_dt->modify('+2 weeks'); // Avanza al lunes de la segunda semana siguiente
            log_message('debug', 'DevolverCompra: Calculando start_date: +2 semanas (isPastPurchaseCutoffForNextWeek es TRUE).');
        } else {
            // Si NO ha pasado el horario de corte, la devolución es posible a partir de la próxima semana.
            $start_date_dt->modify('+1 week'); // Avanza al lunes de la próxima semana
            log_message('debug', 'DevolverCompra: Calculando start_date: +1 semana (isPastPurchaseCutoffForNextWeek es FALSE).');
        }
        $start_date = $start_date_dt->format('Y-m-d');

        // 2. Calcula la fecha del viernes de la cuarta semana a partir del lunes de la semana actual
        // (Esto define el límite superior de tu ventana de 5 semanas para mostrar viandas)
        $endOfFourthWeek = clone $mondayOfCurrentWeek;
        $endOfFourthWeek->modify('+4 weeks'); // Avanza al lunes de la 5ª semana (semana 0, 1, 2, 3)
        $endOfFourthWeek->modify('+4 days');  // Avanza 4 días desde ese lunes para llegar al viernes

        $end_date = $endOfFourthWeek->format('Y-m-d'); // Hasta el viernes de la 4ta semana

        // Seguridad: Si el inicio supera el fin, ajusta (puede pasar si el rango es muy corto)
        if ($start_date > $end_date) {
            $end_date = $start_date; // Asegura que al menos un día sea consultable
            log_message('warning', 'DevolverCompra: start_date era mayor que end_date, ajustado a end_date = start_date.');
        }

        log_message('debug', 'DevolverCompra: Rango de fechas para devoluciones: ' . $start_date . ' a ' . $end_date);

        /*  
        // --- INICIO DE DEPURACIÓN (Reubicado para tener todas las variables definidas) ---
        error_log("--- DEBUG DEVOLVER COMPRA ---");
        error_log("currentDateTime: " . $currentDateTime->format('Y-m-d H:i:s'));
        error_log("currentDayOfWeek: " . $currentDayOfWeek . " (1=Lunes, 5=Viernes, etc.)");
        error_log("currentTime: " . $currentTime);
        error_log("dia_final_compra (config): " . $dia_final_compra);
        error_log("hora_final_compra_str (config): " . $hora_final_compra_str); // Mostrar la cadena original
        error_log("hora_final_compra_dt (objeto): " . $hora_final_compra_dt->format('Y-m-d H:i:s')); // Mostrar el objeto DateTime
        error_log("isPastPurchaseCutoffForNextWeek: " . ($isPastPurchaseCutoffForNextWeek ? 'true' : 'false'));
        error_log("mondayOfCurrentWeek: " . $mondayOfCurrentWeek->format('Y-m-d'));
        error_log("Calculated start_date for devolution: " . $start_date);
        error_log("Calculated end_date for devolution: " . $end_date);
        // --- FIN DE DEPURACIÓN --- 
        // */


        // Verifica si el comedor está abierto para devoluciones
        // Este if ahora está después de la definición de $id_usuario y los cálculos de fecha
        if (!$this->estadoComedor()) {
            $data = [
                'titulo' => 'Devolver Compras',
                'alerta' => "<p>Comedor cerrado</p>",
                'compras' => []
            ];
            $this->load->view('usuario/header', $data);
            $this->load->view('usuario/devolver_compra', $data);
            $this->load->view('general/footer');
            return;
        }

        // Obtiene las compras del usuario en el rango definido.
        $data['compras'] = $this->ticket_model->getComprasInRangeByIdUser($start_date, $end_date, $id_usuario);
        log_message('debug', 'Número de compras obtenidas (antes de filtrar): ' . count($data['compras']));


        // Filtrar compras para excluir feriados, receso invernal o días pasados
        $compras_filtradas = [];
        $feriados_fechas_cache = array_column($this->ticket_model->getFeriadosInRange($start_date, $end_date), 'fecha');
        log_message('debug', 'Feriados en rango: ' . json_encode($feriados_fechas_cache));

        foreach ($data['compras'] as $compra) {
            $compra_date = new DateTime($compra->dia_comprado);
            $es_receso_invernal = ($compra_date >= new DateTime($vacaciones_invierno_inicio) && $compra_date <= new DateTime($vacaciones_invierno_fin));
            $es_feriado = in_array($compra->dia_comprado, $feriados_fechas_cache); // Usamos el cache
            $es_pasado = ($compra_date < $currentDate);

            if (!$es_receso_invernal && !$es_feriado && !$es_pasado &&
                $compra->dia_comprado >= $start_date && $compra->dia_comprado <= $end_date) {
                $compras_filtradas[] = $compra;
            } else {
                 log_message('debug', 'DevolverCompra: Vianda ID ' . $compra->id . ' no elegible: Receso(' . ($es_receso_invernal?'true':'false') . '), Feriado(' . ($es_feriado?'true':'false') . '), Pasado(' . ($es_pasado?'true':'false') . '), Fuera de rango (' . $compra->dia_comprado . ').');
            }
        }
        $data['compras'] = $compras_filtradas;
        log_message('debug', 'Número de compras FILTRADAS y enviadas a la vista: ' . count($compras_filtradas));
        log_message('debug', 'Contenido de $compras_filtradas (para ver las fechas): ' . json_encode($compras_filtradas));


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
                    
                        $compra_date = new DateTime($compra->dia_comprado);
                        $es_receso_invernal_post = ($compra_date >= new DateTime($vacaciones_invierno_inicio) && $compra_date <= new DateTime($vacaciones_invierno_fin));
                        $es_feriado_post = in_array($compra->dia_comprado, $feriados_fechas_cache);
                        $es_pasado_post = ($compra_date < $currentDate);

                        // Asegurarse de que la vianda esté dentro del rango de devolución permitido
                        // y no sea un feriado/receso/pasado.
                        if ($compra->dia_comprado >= $start_date && $compra->dia_comprado <= $end_date &&
                            !$es_receso_invernal_post && !$es_feriado_post && !$es_pasado_post) {

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
                            log_message('warning', 'DevolverCompra: Intento de devolver compra fuera de rango o no elegible (POST): ' . $id_compra . ' - Fecha: ' . $compra->dia_comprado);
                        }
                    } else {
                        log_message('warning', 'DevolverCompra: Intento de devolver compra inválida o no elegible: ' . $id_compra);
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