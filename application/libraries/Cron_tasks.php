<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cron_tasks {

    protected $CI; // Esta propiedad contendrá el superobjeto de CodeIgniter

    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->model('comedor/ticket_model');

    }

    /**
     * Procesa y limpia las órdenes de compra pendientes de un usuario específico.
     * Utiliza los métodos existentes del modelo.
     *
     * @param int $userId El ID del usuario cuyas compras pendientes se van a revisar.
     */
    public function cleanupUserExpiredOrders($userId) {
        log_message('debug', 'Cron_tasks: cleanupUserExpiredOrders iniciado para usuario ID: ' . $userId);

        // 1. Obtener todas las compras pendientes del usuario
        // Este método ya filtra por estados 'pending' y 'pasarela'.
        $pendingPurchases = $this->CI->ticket_model->getComprasPendientes($userId);
        
        foreach ($pendingPurchases as $purchase) {
            $date = $purchase['dia_comprado'];
            $mp_estado = $purchase['mp_estado']; 

            // Verificar si la fecha de la vianda es aún ordenable
            if (!$this->CI->ticket_model->esFechaViandaAunOrdenable($date)) {
                // Si la fecha de la vianda no es válida, actualiza el estado en la DB
                $this->CI->ticket_model->updateCompraPendienteEstado($purchase['id'], 'expired_by_date_cutoff');
                log_message('warning', 'Cron_tasks: Compra pendiente ID ' . $purchase['id'] . ' marcada como expired_by_date_cutoff debido a fecha de vianda inválida.');
                
                // Y luego la elimina
                $this->CI->ticket_model->deleteCompraPendiente($purchase['id']);
                log_message('debug', 'Cron_tasks: Registro de compra pendiente ' . $purchase['id'] . ' eliminado tras expiración por fecha.');
            }
        }
        log_message('debug', 'Cron_tasks: cleanupUserExpiredOrders finalizado para usuario ID: ' . $userId);
    }
}