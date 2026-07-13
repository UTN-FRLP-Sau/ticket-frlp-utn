<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use MercadoPago\SDK;
use MercadoPago\Payment;

class Tareas extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // Asegura que solo se pueda ejecutar desde la línea de comandos (CLI)
        if (!$this->input->is_cli_request()) {
            show_404();
        }

        // Carga los modelos
        $this->load->model('tareas_model');
        $this->load->model('comedor/ticket_model');
        $this->load->model('general/general_model', 'generalticket');
        $this->load->model('comedor/Webhook_model', 'webhook_model');
    }

    /**
     * Método para registrar logs específicos de las operaciones de este controlador.
     * @param string $mensaje El mensaje a registrar.
     * @param string $prefijo_archivo Prefijo opcional para el nombre del archivo de log.
     */
    private function _logManual($mensaje, $prefijo_archivo = 'Cron') {
        // ruta del archivo de log específica del controlador de tareas
        $ruta_log = APPPATH . 'logs/' . $prefijo_archivo . '_manual_' . date('Y-m-d') . '.log';
        $fecha = date('Y-m-d H:i:s');

        // Extraer solo la ruta del directorio del archivo de log
        $log_dir = dirname($ruta_log); // Obtiene el directorio padre de la ruta del archivo

        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true); // Crea el directorio de forma recursiva si no existe
        }

        file_put_contents($ruta_log, "[$fecha] $mensaje\n", FILE_APPEND);
    }

    private function log_preferencia($mensaje)
    {
        // ruta del archivo de log específica del webhook
        $ruta_log = APPPATH . 'logs/preferencia_' . date('Y-m-d') . '.log';
        $fecha = date('Y-m-d H:i:s');
        file_put_contents($ruta_log, "[$fecha] $mensaje\n", FILE_APPEND);
    }

    public function consultar_estado_mp() {
        $this->config->load('ticket');
        $access_token = $this->config->item('MP_ACCESS_TOKEN');
        $this->_logManual('CRON_MP: ************************************************************');
        
        if ($access_token === null || empty($access_token)) {
            $this->_logManual('CRON_MP: Access Token de Mercado Pago no encontrado.', 'Cron_error');
            echo "Error: Access Token no configurado.\n";
            return;
        }

        SDK::setAccessToken($access_token);

        // Obtener compras con estado pendiente o in_process
        $compras_pendientes = $this->tareas_model->getComprasPendientes();

        if (empty($compras_pendientes)) {
            echo "No hay compras pendientes o en proceso.\n";
            $this->_logManual('CRON_MP: No hay compras pendientes o en proceso para consultar.', 'Cron');
            return;
        }

        foreach ($compras_pendientes as $compra) {
            $external_reference = $compra->external_reference;
            echo "Consultando pago para external_reference: $external_reference\n";
            $this->_logManual('CRON_MP: Consultando pago para external_reference: ' . $external_reference, 'Cron');

            try {
                $filters = [
                    "external_reference" => $external_reference
                ];

                $payments = Payment::search($filters);

                if (empty($payments)) {
                    echo "No se encontraron pagos para el external_reference: $external_reference\n";
                    $this->_logManual('CRON_MP: No se encontraron pagos para el external_reference: ' . $external_reference, 'Cron');
                    // Si no se encuentran pagos, la compra sigue pendiente hasta que se reintente o expire
                } else {
                    $found_approved = false;
                    foreach ($payments as $payment_info) { // Renombro $payment a $payment_info
                        $estado = $payment_info->status;
                        $mp_status_detail = isset($payment_info->status_detail) ? $payment_info->status_detail : 'N/A';

                        echo "ID de Pago MP: {$payment_info->id} | Estado: {$estado} | Detalle: {$mp_status_detail}\n";
                        $this->_logManual("CRON_MP: ID de Pago MP: {$payment_info->id} | Estado: {$estado} | Detalle: {$mp_status_detail}", 'Cron');
                        
                        // Extraigo el DNI del campo de descripción de la preferencia
                        $descripcion = $payment_info->additional_info->items[0]->description;
                        $parts = preg_split('/\s+/', trim($descripcion)); // separa por espacios
                        $documento = end($parts); // toma siempre el último string, que es el DNI

                        if ($estado === 'approved') {
                            // Llama a la función del Webhook_model para manejar el pago aprobado
                            if ($this->webhook_model->procesarPagoAprobado($compra, $payment_info)) {
                                $found_approved = true;

                                $this->log_preferencia('Pago APROBADO CRON ;Usuario ID: '. $compra->id_usuario . ' ;DNI: '. $documento . ' ;External Reference: '. $compra->external_reference . ' ; Monto: ' . $payment_info->transaction_amount);
                                $this->_logManual('CRON_MP: Pago aprobado procesado por Webhook_model para compra ID: ' . $compra->id, 'Cron');
                                break; // Si se encuentra un aprobado, procesamos y salimos para esta external_reference
                            } else {
                                $this->_logManual('CRON_MP: Fallo en procesarPagoAprobado desde Webhook_model para compra ID: ' . $compra->id, 'Cron_error');
                            }
                        } elseif ($estado === 'rejected' || $estado === 'cancelled' || $estado === 'expired_by_date_cutoff') {
                            // Solo procesa el rechazo si no hemos encontrado un aprobado ya
                            if (!$found_approved) {
                                // Llama a la función del Webhook_model para manejar el pago rechazado
                                if ($this->webhook_model->procesarPagoRechazado($compra, $payment_info)) {
                                    $this->log_preferencia('Pago RECHAZADO CRON ;Usuario ID: '. $compra->id_usuario . ' ;DNI: '. $documento . ' ;External Reference: '. $compra->external_reference . ' ; Monto: ' . $payment_info->transaction_amount);
                                    $this->_logManual('CRON_MP: Pago rechazado procesado por Webhook_model para compra ID: ' . $compra->id, 'Cron');
                                } else {
                                    $this->_logManual('CRON_MP: Fallo en procesarPagoRechazado desde Webhook_model para compra ID: ' . $compra->id, 'Cron_error');
                                }
                            }
                        } else {
                            echo "El pago ID: {$payment_info->id} aún está pendiente o en proceso ({$estado}). Se espera confirmación futura.\n";
                            $this->_logManual("CRON_MP: El pago ID: {$payment_info->id} aún está pendiente o en proceso ({$estado}).", 'Cron');
                        }
                    } // Fin del foreach ($payments as $payment_info)

                    if (!$found_approved) {
                        echo "Ningún pago aprobado encontrado para esta external_reference; el último estado significativo fue in_process/pending/rejected.\n";
                        $this->_logManual("CRON_MP: Ningún pago aprobado encontrado para external_reference: {$external_reference}.", 'Cron');
                    }
                }

            } catch (Exception $e) {
                $this->_logManual('CRON_MP: Error crítico al procesar external_reference ' . $external_reference . ': ' . $e->getMessage() . ' en ' . $e->getFile() . ' línea ' . $e->getLine(), 'Cron_error');
                echo "Error crítico al procesar: " . $e->getMessage() . "\n";
            }

            echo "--------------------------------------\n";
        }

        echo "Consulta de estados finalizada.\n";
        $this->_logManual('CRON_MP: Consulta de estados finalizada.', 'Cron');
    }




    /* 
    Para probar este método creando un archivo de log con fecha antigua (más de 2 meses atrás)

    Comando para windows:

    crear archivo de log antiguo:
    type nul > C:\xampp\htdocs\ticket-frlp-utn\application\logs\log_antiguo.log        

    modificar fecha de archivo:
    $file.LastWriteTime = "06/15/2025 10:00 AM"

    ejecutar tarea:
    php index.php cronJobs/Tareas/eliminar_logs_antiguos
    --------------------------------------------------------------------------------------------
    Para linux:
    touch /var/www/html/ticket-frlp-utn/application/logs/log_antiguo.log
    touch -t 202506151000.00 /var/www/html/ticket-frlp-utn/application/logs/log_antiguo.log
    php /var/www/html/ticket-frlp-utn/index.php cronJobs/Tareas/eliminar_logs_antiguos

    */
    
    public function eliminar_logs_antiguos() {
        $this->_logManual('CRON_CLI: Iniciando limpieza de logs antiguos.', 'Cron');
        
        $log_path = APPPATH . 'logs/';
        $two_months_ago = strtotime('-2 months'); // Fecha límite: 2 meses atrás

        // Verifica si el directorio existe
        if (!is_dir($log_path)) {
            $this->_logManual('CRON_CLI: El directorio de logs no existe. Abortando limpieza.', 'Cron');
            return;
        }
        
        // Escanea los archivos del directorio de logs
        $files = scandir($log_path);
        
        foreach ($files as $file) {
            // Ignora los directorios . y ..
            if ($file === '.' || $file === '..') {
                continue;
            }

            $file_path = $log_path . $file;
            
            // Verifica si es un archivo y no un directorio
            if (is_file($file_path)) {
                // Obtiene la fecha de la última modificación del archivo
                $file_date = filemtime($file_path);
                
                // Compara la fecha de modificación con la fecha límite
                if ($file_date < $two_months_ago) {
                    // Elimina el archivo si es más viejo que 2 meses
                    if (unlink($file_path)) {
                        $this->_logManual("CRON_CLI: Log eliminado: $file", 'Cron');
                    } else {
                        $this->_logManual("CRON_CLI: Error al eliminar el log: $file", 'Cron');
                    }
                }
            }
        }
        
        $this->_logManual('CRON_CLI: Limpieza de logs finalizada.', 'Cron');
        echo "Limpieza de logs antiguos finalizada.\n";
    }


    public function otra_tarea_diaria() {
        $this->_logManual('CRON_CLI: Ejecutando otra tarea diaria.', 'Cron');
        echo "Otra tarea diaria ejecutada.\n";
    }

    public function limpiar_passrecovery() {
        $this->db->where('TIMESTAMP(fecha, hora) <', date('Y-m-d H:i:s', strtotime('-1 hour')), false);
        $this->db->delete('passrecovery');
        $affected = $this->db->affected_rows();
        $this->_logManual("CRON_CLI: Se eliminaron {$affected} registros de passrecovery.", 'Cron');
        echo "Registros eliminados: {$affected}\n";
    }

    /**
     * Recordatorio semanal de compra: todos los jueves, les avisa por mail a los
     * estudiantes activos (estado = 1) que no desactivaron la notificación
     * (notif_recordatorio_compra = 1) y que todavía no tienen ninguna compra
     * (ni pendiente) para ningún día de la semana próxima.
     *
     * Pensado para ser invocado a diario por el scheduler del servidor (a la hora
     * de cierre de venta configurada menos 30 minutos); internamente valida que
     * hoy sea jueves y no hace nada si no lo es, para no depender de reconfigurar
     * el cron cada vez que cambie 'hora_final' desde el admin.
     */
    public function recordatorio_compra_semanal() {
        $this->_logManual('CRON_RECORDATORIO: ************************************************************');

        $hoy = new DateTime('now');
        if ((int)$hoy->format('N') !== 4) { // 1 = lunes ... 4 = jueves ... 7 = domingo
            $this->_logManual('CRON_RECORDATORIO: Hoy (' . $hoy->format('Y-m-d') . ') no es jueves, se omite la ejecución.', 'Cron');
            echo "Hoy no es jueves, no corresponde enviar el recordatorio.\n";
            return;
        }

        // "Semana próxima" = lunes a domingo de la semana siguiente a la actual,
        // usando la misma convención de semana (lunes a lunes) que
        // Ticket_model::esFechaViandaAunOrdenable().
        $lunesSemanaActual = clone $hoy;
        $lunesSemanaActual->setTime(0, 0, 0);
        if ((int)$lunesSemanaActual->format('N') !== 1) {
            $lunesSemanaActual->modify('last monday');
        }
        $lunesProximaSemana = clone $lunesSemanaActual;
        $lunesProximaSemana->modify('+7 days');
        $domingoProximaSemana = clone $lunesProximaSemana;
        $domingoProximaSemana->modify('+6 days');

        $fecha_inicio = $lunesProximaSemana->format('Y-m-d');
        $fecha_fin = $domingoProximaSemana->format('Y-m-d');

        $this->_logManual("CRON_RECORDATORIO: Hoy es jueves. Buscando estudiantes sin compra para la semana próxima ({$fecha_inicio} a {$fecha_fin}).", 'Cron');

        $estudiantes = $this->tareas_model->getEstudiantesSinCompraProximaSemana($fecha_inicio, $fecha_fin);

        if (empty($estudiantes)) {
            $this->_logManual('CRON_RECORDATORIO: No hay estudiantes para notificar (todos tienen compra/pendiente o desactivaron el aviso).', 'Cron');
            echo "No hay estudiantes para notificar.\n";
            return;
        }

        $enviados = [];
        $fallidos = [];

        foreach ($estudiantes as $estudiante) {
            if (empty($estudiante->mail)) {
                $fallidos[] = $estudiante->id . ':sin_mail';
                $this->_logManual("CRON_RECORDATORIO: Usuario ID {$estudiante->id} no tiene mail configurado, se omite.", 'Cron_error');
                continue;
            }

            $data = [
                'nombre' => $estudiante->nombre,
                'apellido' => $estudiante->apellido,
                'fecha_inicio' => $fecha_inicio,
                'fecha_fin' => $fecha_fin,
            ];

            $subject = 'Recordatorio: todavía no compraste tu vianda para la próxima semana';

            try {
                $message = $this->load->view('general/correos/recordatorio_compra_semanal', $data, true);

                if ($this->generalticket->smtpSendEmail($estudiante->mail, $subject, $message)) {
                    $enviados[] = $estudiante->id;
                    $this->_logManual("CRON_RECORDATORIO: Mail enviado a usuario ID {$estudiante->id} ({$estudiante->mail}).", 'Cron');
                } else {
                    $fallidos[] = $estudiante->id;
                    $this->_logManual("CRON_RECORDATORIO: Fallo al enviar mail a usuario ID {$estudiante->id} ({$estudiante->mail}).", 'Cron_error');
                }
            } catch (Exception $e) {
                $fallidos[] = $estudiante->id;
                $this->_logManual("CRON_RECORDATORIO: Excepción al enviar mail a usuario ID {$estudiante->id}: " . $e->getMessage(), 'Cron_error');
            }
        }

        $resumen = sprintf(
            'CRON_RECORDATORIO: Finalizado. Semana próxima: %s a %s. Candidatos: %d. Enviados: %d [%s]. Fallidos: %d [%s].',
            $fecha_inicio,
            $fecha_fin,
            count($estudiantes),
            count($enviados),
            implode(',', $enviados),
            count($fallidos),
            implode(',', $fallidos)
        );
        $this->_logManual($resumen, 'Cron');
        echo "Recordatorios enviados: " . count($enviados) . " / " . count($estudiantes) . "\n";
    }
}