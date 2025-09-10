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

                                $this->log_preferencia('Usuario ID: '. $compra->id_usuario . ' ;DNI: '. $documento . ' ;External Reference: '. $compra->external_reference . ' ; Monto: ' . $payment_info->transaction_amount) . ' ; Pago aprobado procesado por CRON_MP';
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
                                    $this->log_preferencia('Usuario ID: '. $compra->id_usuario . ' ;DNI: '. $documento . ' ;External Reference: '. $compra->external_reference . ' ; Monto: ' . $payment_info->transaction_amount) . ' ; Pago rechazado procesado por CRON_MP';
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

    public function otra_tarea_diaria() {
        $this->_logManual('CRON_CLI: Ejecutando otra tarea diaria.', 'Cron');
        echo "Otra tarea diaria ejecutada.\n";
    }
}