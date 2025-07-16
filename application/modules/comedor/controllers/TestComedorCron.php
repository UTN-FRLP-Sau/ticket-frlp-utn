<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TestComedorCron extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ticket_model');

        log_message('debug', 'CRON DEBUG: Constructor de TestComedorCron cargado.');
        // echo "DEBUG: Constructor de TestComedorCron cargado.\n";
    }

    public function cleanupExpiredOrders()
    {
        // echo "DEBUG: ¡Método cleanupExpiredOrders de TestComedorCron alcanzado!\n";

        log_message('info', 'CRON: cleanupExpiredOrders iniciado.');
        echo "DEBUG: Método cleanupExpiredOrders iniciado.\n";

        // 1. Obtener la configuración de cierre del comedor
        $configuracion = $this->ticket_model->getConfiguracion();
        log_message('debug', 'CRON DEBUG: Resultado getConfiguracion: ' . (empty($configuracion) ? 'VACIO' : json_encode($configuracion[0])));
        echo "DEBUG: Configuración obtenida.\n";

        if (empty($configuracion) || !isset($configuracion[0]->dia_final) || !isset($configuracion[0]->hora_final)) {
            log_message('error', 'CRON: Configuración del comedor no encontrada o incompleta para el cronjob. Abortando.');
            echo "DEBUG ERROR: Configuración del comedor incompleta. Abortando.\n";
            return;
        }
        $config = $configuracion[0];

        // 2. Obtener todas las compras pendientes en estado 'pasarela'
        $pending_purchases = $this->ticket_model->getAllComprasPendientes('pasarela');
        log_message('debug', 'CRON DEBUG: Compras pendientes obtenidas (count): ' . count($pending_purchases));
        echo "DEBUG: Se encontraron " . count($pending_purchases) . " compras pendientes en estado 'pasarela'.\n";

        $count_expired_by_cron = 0;

        // Define el límite de tiempo para que una compra en 'pasarela' expire (en minutos)
        $EXPIRATION_MINUTES_LIMIT = 15;

        if (!empty($pending_purchases)) {
            foreach ($pending_purchases as $purchase) {
                // Se modificó esta línea para no referenciar mp_id, ya que no existe en la tabla
                log_message('debug', 'CRON DEBUG: Procesando compra ID: ' . $purchase->id . ' (External Ref: ' . $purchase->external_reference . ')');
                echo "DEBUG: Procesando compra ID: " . $purchase->id . " (External Ref: " . $purchase->external_reference . ")\n";

                $mark_as_expired = false; // Reinicia el flag para cada compra

                if (isset($purchase->created_at)) {
                    $created_at_timestamp = strtotime($purchase->created_at);
                    $expiration_timestamp = $created_at_timestamp + ($EXPIRATION_MINUTES_LIMIT * 60); // Suma minutos en segundos

                    $current_timestamp = time(); // Hora actual

                    if ($current_timestamp > $expiration_timestamp) {
                        // La compra ha expirado porque ha pasado el límite de tiempo
                        $mark_as_expired = true;
                        log_message('info', 'CRON: Compra ID ' . $purchase->id . ' marcada para expirar: Ha excedido el tiempo límite de ' . $EXPIRATION_MINUTES_LIMIT . ' minutos.');
                        echo "DEBUG: Compra ID " . $purchase->id . " marcada para expirar (límite de tiempo excedido).\n";
                    } else {
                        log_message('debug', 'CRON DEBUG: Compra ID ' . $purchase->id . ' NO ha expirado por tiempo. Aún en ventana de pago.');
                    }
                } else {
                    log_message('warning', 'CRON: Compra pendiente ID ' . $purchase->id . ' no tiene columna created_at o está vacía. No se pudo verificar por tiempo.');
                    echo "DEBUG WARNING: Compra ID " . $purchase->id . " sin fecha de creación. No se pudo verificar por tiempo.\n";
                    // Considera si quieres que estas compras también se marquen como expiradas o se ignoren.
                    // Por ahora, no se marcarán como expiradas por tiempo si no hay created_at.
                }

                // --- Lógica de Expiración Basada en la Fecha de Vianda ---
                // Si la compra no fue marcada como expirada por tiempo, verifica la lógica de la vianda
                if (!$mark_as_expired) {
                    $viandas_en_compra = json_decode($purchase->datos, true);
                    $earliest_vianda_date_obj = null; 

                    if (is_array($viandas_en_compra) && !empty($viandas_en_compra)) {
                        foreach ($viandas_en_compra as $vianda) {
                            if (isset($vianda['dia_comprado'])) {
                                try {
                                    $current_vianda_date_obj = new DateTime($vianda['dia_comprado']);
                                    // Se busca la fecha más temprana (la que primero "vence")
                                    if ($earliest_vianda_date_obj === null || $current_vianda_date_obj < $earliest_vianda_date_obj) {
                                        $earliest_vianda_date_obj = $current_vianda_date_obj;
                                    }
                                } catch (Exception $e) {
                                    log_message('error', 'CRON: Error al parsear fecha de vianda "' . ($vianda['dia_comprado'] ?? 'N/A') . '" en compra ID ' . $purchase->id . ': ' . $e->getMessage());
                                    echo "DEBUG ERROR: Error al parsear fecha en compra ID " . $purchase->id . ": " . $e->getMessage() . "\n";
                                    $mark_as_expired = true; // Marca como expirada por error de datos
                                    break;
                                }
                            }
                        }

                        if (!$mark_as_expired && $earliest_vianda_date_obj !== null) {
                            log_message('debug', 'CRON DEBUG: Verificando esFechaViandaAunOrdenable para fecha ' . $earliest_vianda_date_obj->format('Y-m-d') . ' en compra ID ' . $purchase->id);
                            if (!$this->ticket_model->esFechaViandaAunOrdenable($earliest_vianda_date_obj->format('Y-m-d'))) {
                                $mark_as_expired = true;
                                log_message('info', 'CRON: Compra ID ' . $purchase->id . ' marcada para expirar: fecha de vianda más próxima pasada.');
                                echo "DEBUG: Compra ID " . $purchase->id . " marcada para expirar (fecha de vianda pasada).\n";
                            }
                        } elseif ($earliest_vianda_date_obj === null && !$mark_as_expired) {
                            log_message('warning', 'CRON: Compra pendiente ID ' . $purchase->id . ' no contiene fechas válidas en ninguna vianda. Marcando como expirada.');
                            echo "DEBUG WARNING: Compra ID " . $purchase->id . " sin fechas válidas. Marcando como expirada.\n";
                            $mark_as_expired = true;
                        }

                    } else {
                        log_message('warning', 'CRON: Compra pendiente ID ' . $purchase->id . ' tiene datos de viandas mal formados o vacíos. Marcando como expirada.');
                        echo "DEBUG WARNING: Compra ID " . $purchase->id . " con datos de viandas mal formados. Marcando como expirada.\n";
                        $mark_as_expired = true;
                    }
                }

                // Si alguna de las condiciones la marcó como expirada, actualiza su estado
                if ($mark_as_expired) {
                    $this->ticket_model->updateCompraPendienteEstado(
                        $purchase->id,
                        'expired_by_cronjob', // Estado que indica expiración por cron
                        'Compra expirada automáticamente por cronjob al no completarse en el tiempo o pasar la fecha de cierre de pedidos.'
                    );
                    $count_expired_by_cron++;
                    log_message('info', 'CRON: Compra ID ' . $purchase->id . ' estado actualizado a expired_by_cronjob.');
                    echo "DEBUG: Compra ID " . $purchase->id . " actualizada a 'expired_by_cronjob'.\n";
                }
            }
        }

        log_message('info', 'CRON: cleanupExpiredOrders finalizado. Total de compras expiradas por cron: ' . $count_expired_by_cron);
        echo "DEBUG: Método cleanupExpiredOrders finalizado. Total de compras expiradas por cron: " . $count_expired_by_cron . "\n";
        echo "CRONJOB FINALIZADO CORRECTAMENTE.\n";
    }
}