<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tasks {

    protected $CI; // Esta propiedad contendrá el superobjeto de CodeIgniter

    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->model('comedor/ticket_model');

    }

    /**
     * Método para registrar logs específicos de las operaciones de este controlador.
     * @param string $mensaje El mensaje a registrar.
     * @param string $prefijo_archivo Prefijo opcional para el nombre del archivo de log.
     */
    private function _logManual($mensaje, $prefijo_archivo = 'limpieza_compras_expiradas') {
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

    /**
     * Procesa y limpia las órdenes de compra pendientes de un usuario específico.
     * Utiliza los métodos existentes del modelo.
     *
     * @param int $userId El ID del usuario cuyas compras pendientes se van a revisar.
     */
    public function cleanupUserExpiredOrders($userId) {

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
                $this->_logManual('Compra pendiente ID ' . $purchase['id'] . ' marcada como expired_by_date_cutoff debido a fecha de vianda inválida.');
                // Y luego la elimina
                $this->CI->ticket_model->deleteCompraPendiente($purchase['id']);
                $this->_logManual('Registro de compra pendiente ' . $purchase['id'] . ' eliminado tras expiración por fecha.');
            }
        }
    }
}