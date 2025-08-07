<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use MercadoPago\SDK;
use MercadoPago\Payment;

class Tareas extends CI_Controller { 

    public function __construct() {
        parent::__construct();
        if (!$this->input->is_cli_request()) {
            show_404(); 
        }

        $this->load->model('tareas_model');
    }

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
            return;
        }

        foreach ($compras_pendientes as $compra) {
            $external_reference = $compra->external_reference;
            echo "Consultando pago para external_reference: $external_reference\n";

            try {
                $filters = [
                    "external_reference" => $external_reference
                    //"limit" => 1 // Limita a 1 resultado para evitar sobrecarga
                    
                    // "offset" => 0,

                    // "sort" => "date_created",
                ];

                $payments = Payment::search($filters); 

                if (empty($payments)) {
                    echo "No se encontraron pagos para el external_reference: $external_reference\n";
                } else {
                    // Iterar sobre los pagos encontrados (puede haber más de uno si external_reference no es único)
                    foreach ($payments as $payment) {
                        $estado = $payment->status;

                        echo "Estado encontrado: $estado\n";

                        if ($estado === 'approved') {
                            $this->manejarPagoAprobado($compra);
                        } elseif ($estado === 'rejected') {
                            $this->manejarPagoRechazado($compra);
                        } else {
                            echo "El pago aún está pendiente o en proceso.\n";
                        }
                    }
                }

            } catch (Exception $e) {
                log_message('error', 'CRON_MP: Error al consultar MercadoPago: ' . $e->getMessage());
                echo "Error al consultar MP: " . $e->getMessage() . "\n";
            }

            echo "--------------------------------------\n";
        }

    }

    private function manejarPagoAprobado($compra) {
        $this->tareas_model->actualizarEstadoPago($compra->external_reference, 'approved'); 
        echo "Pago aprobado actualizado para compra ID: {$compra->id}\n";
        log_message('info', "CRON_MP: Compra aprobada ID: {$compra->id}");
    }

    private function manejarPagoRechazado($compra) {
        $this->tareas_model->actualizarEstadoPago($compra->external_reference, 'rejected'); 
        echo "Pago rechazado actualizado para compra ID: {$compra->id}\n";
        log_message('info', "CRON_MP: Compra rechazada ID: {$compra->id}");
    }

    public function otra_tarea_diaria() {
        log_message('info', 'CRON_CLI: Ejecutando otra tarea diaria.');
        echo "Otra tarea diaria ejecutada.\n";
    }
}