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

        // Carga los modelos necesarios
        $this->load->model('tareas_model');
        $this->load->model('comedor/ticket_model');
        $this->load->model('general/general_model', 'generalticket');
        // Carga el Webhook_model para usar sus funciones de procesamiento de pagos
        $this->load->model('comedor/Webhook_model', 'webhook_model');
    }

    // Las funciones privadas `log_manual` y `mapMercadoPagoStatusDetail`
    // han sido eliminadas de este controlador.
    // Para logs, se usará `log_message` de CodeIgniter o el `_logManual` del `webhook_model`.
    // Para el mapeo de detalles de estado, se usará `_mapMpStatusDetail` del `webhook_model`.

    public function consultar_estado_mp() {
        $this->config->load('ticket');
        $access_token = $this->config->item('MP_ACCESS_TOKEN');

        if ($access_token === null || empty($access_token)) {
            log_message('error', 'CRON_MP: Access Token de Mercado Pago no encontrado.');
            echo "Error: Access Token no configurado.\n";
            return;
        }

        SDK::setAccessToken($access_token);

        // Obtener compras con estado pendiente o in_process
        $compras_pendientes = $this->tareas_model->getComprasPendientes();

        if (empty($compras_pendientes)) {
            echo "No hay compras pendientes o en proceso.\n";
            log_message('info', 'CRON_MP: No hay compras pendientes o en proceso para consultar.');
            return;
        }

        foreach ($compras_pendientes as $compra) {
            $external_reference = $compra->external_reference;
            echo "Consultando pago para external_reference: $external_reference\n";
            log_message('info', 'CRON_MP: Consultando pago para external_reference: ' . $external_reference);

            try {
                $filters = [
                    "external_reference" => $external_reference
                ];

                $payments = Payment::search($filters);

                if (empty($payments)) {
                    echo "No se encontraron pagos para el external_reference: $external_reference\n";
                    log_message('info', 'CRON_MP: No se encontraron pagos para el external_reference: ' . $external_reference);
                    // Si no se encuentran pagos, la compra sigue pendiente hasta que se reintente o expire
                } else {
                    $found_approved = false;
                    foreach ($payments as $payment_info) { // Renombro $payment a $payment_info para claridad
                        $estado = $payment_info->status;
                        $mp_status_detail = isset($payment_info->status_detail) ? $payment_info->status_detail : 'N/A';

                        echo "ID de Pago MP: {$payment_info->id} | Estado: {$estado} | Detalle: {$mp_status_detail}\n";
                        log_message('info', "CRON_MP: ID de Pago MP: {$payment_info->id} | Estado: {$estado} | Detalle: {$mp_status_detail}");

                        if ($estado === 'approved') {
                            // Llama a la función del Webhook_model para manejar el pago aprobado
                            if ($this->webhook_model->procesarPagoAprobado($compra, $payment_info)) {
                                $found_approved = true;
                                log_message('info', 'CRON_MP: Pago aprobado procesado por Webhook_model para compra ID: ' . $compra->id);
                                break; // Si se encuentra un aprobado, procesamos y salimos para esta external_reference
                            } else {
                                log_message('error', 'CRON_MP: Fallo en procesarPagoAprobado desde Webhook_model para compra ID: ' . $compra->id);
                            }
                        } elseif ($estado === 'rejected' || $estado === 'cancelled' || $estado === 'expired_by_date_cutoff') {
                            // Solo procesa el rechazo si no hemos encontrado un aprobado ya
                            if (!$found_approved) {
                                // Llama a la función del Webhook_model para manejar el pago rechazado
                                if ($this->webhook_model->procesarPagoRechazado($compra, $payment_info)) {
                                    log_message('info', 'CRON_MP: Pago rechazado procesado por Webhook_model para compra ID: ' . $compra->id);
                                } else {
                                    log_message('error', 'CRON_MP: Fallo en procesarPagoRechazado desde Webhook_model para compra ID: ' . $compra->id);
                                }
                            }
                        } else {
                            echo "El pago ID: {$payment_info->id} aún está pendiente o en proceso ({$estado}). Se espera confirmación futura.\n";
                            log_message('info', "CRON_MP: El pago ID: {$payment_info->id} aún está pendiente o en proceso ({$estado}).");
                        }
                    } // Fin del foreach ($payments as $payment_info)

                    if (!$found_approved) {
                        echo "Ningún pago aprobado encontrado para esta external_reference; el último estado significativo fue in_process/pending/rejected.\n";
                        log_message('info', "CRON_MP: Ningún pago aprobado encontrado para external_reference: {$external_reference}.");
                    }
                }

            } catch (Exception $e) {
                log_message('error', 'CRON_MP: Error crítico al procesar external_reference ' . $external_reference . ': ' . $e->getMessage() . ' en ' . $e->getFile() . ' línea ' . $e->getLine());
                echo "Error crítico al procesar: " . $e->getMessage() . "\n";
            }

            echo "--------------------------------------\n";
        }

        echo "Consulta de estados finalizada.\n";
        log_message('info', 'CRON_MP: Consulta de estados finalizada.');
    }

    // Las funciones `manejarPagoAprobado` y `manejarPagoRechazado` han sido
    // eliminadas de este controlador ya que su lógica ahora está encapsulada
    // y manejada en el `Webhook_model`.

    public function otra_tarea_diaria() {
        log_message('info', 'CRON_CLI: Ejecutando otra tarea diaria.');
        echo "Otra tarea diaria ejecutada.\n";
    }
}