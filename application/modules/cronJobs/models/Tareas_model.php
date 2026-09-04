<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tareas_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getComprasPendientes() {
        $this->db->where_in('mp_estado', ['pending', 'in_process','pasarela']);
        $query = $this->db->get('compras_pendientes'); 
        return $query->result();
    }

    public function actualizarEstadoPago($external_reference, $new_status) {
        $this->db->where('external_reference', $external_reference);
        $this->db->update('compras_pendientes', ['mp_estado' => $new_status]);
        return $this->db->affected_rows();
    }

    /**
     * Devuelve los estudiantes activos (estado = 1) con la notificación de
     * recordatorio de compra habilitada (notif_recordatorio_compra = 1) que
     * todavía no tienen ninguna compra confirmada ni compra pendiente
     * (mp_estado en 'pending'/'pasarela', al igual que Ticket_model::getComprasPendientes())
     * para ningún día dentro del rango [$fecha_inicio, $fecha_fin] ("semana próxima").
     *
     * @param string $fecha_inicio Fecha AAAA-MM-DD (lunes de la semana próxima).
     * @param string $fecha_fin Fecha AAAA-MM-DD (domingo de la semana próxima).
     * @return array Lista de objetos usuario (id, nombre, apellido, mail).
     */
    public function getEstudiantesSinCompraProximaSemana($fecha_inicio, $fecha_fin) {
        // Candidatos: usuarios activos que no desactivaron el recordatorio
        $this->db->select('id, nombre, apellido, mail');
        $this->db->where('estado', 1);
        $this->db->where('notif_recordatorio_compra', 1);
        $candidatos = $this->db->get('usuarios')->result();

        if (empty($candidatos)) {
            return [];
        }

        // Usuarios que ya tienen una compra confirmada para algún día de la semana próxima
        $this->db->distinct();
        $this->db->select('id_usuario');
        $this->db->where('dia_comprado >=', $fecha_inicio);
        $this->db->where('dia_comprado <=', $fecha_fin);
        $con_compra = $this->db->get('compra')->result();
        $ids_con_compra = array_column($con_compra, 'id_usuario');

        // Usuarios con una compra pendiente (pending/pasarela) que incluya algún día de la semana próxima.
        // Misma lógica de decodificación de 'datos' que Ticket_model::getComprasPendientes().
        $this->db->select('id_usuario, datos');
        $this->db->where_in('mp_estado', ['pending', 'pasarela']);
        $pendientes = $this->db->get('compras_pendientes')->result();

        $ids_con_pendiente = [];
        foreach ($pendientes as $pendiente) {
            $viandas = json_decode($pendiente->datos, true);
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($viandas)) {
                continue;
            }
            foreach ($viandas as $vianda) {
                $campo_fecha = isset($vianda['dia_comprado']) ? 'dia_comprado' : 'fecha';
                if (!isset($vianda[$campo_fecha])) {
                    continue;
                }
                if ($vianda[$campo_fecha] >= $fecha_inicio && $vianda[$campo_fecha] <= $fecha_fin) {
                    $ids_con_pendiente[] = $pendiente->id_usuario;
                    break;
                }
            }
        }

        $ids_con_alguna_compra = array_unique(array_merge($ids_con_compra, $ids_con_pendiente));

        $estudiantes_sin_compra = [];
        foreach ($candidatos as $candidato) {
            if (!in_array($candidato->id, $ids_con_alguna_compra)) {
                $estudiantes_sin_compra[] = $candidato;
            }
        }

        return $estudiantes_sin_compra;
    }

    /**
     * Devuelve, agrupados por estudiante, los turnos ('manana'/'noche') con
     * compra ya APROBADA (fila existente en la tabla 'compra', que sólo se
     * completa desde Webhook_model::procesarPagoAprobado() -> Ticket_model::addCompra()
     * cuando el pago se confirma) para el día de hoy ('dia_comprado' = hoy).
     *
     * Filtra por usuarios activos (estado = 1) que no desactivaron el aviso
     * de recordatorio de retiro (notif_recordatorio_retiro = 1). Un estudiante
     * que compró ambos turnos el mismo día (permitir_ambos_turnos_mismo_dia)
     * aparece una sola vez en el resultado, con los dos turnos en 'turnos'.
     *
     * @return array Lista de objetos { id, nombre, apellido, mail, turnos: string[] }.
     */
    public function getComprasAprobadasDeHoy() {
        $hoy = date('Y-m-d');

        $this->db->select('compra.id_usuario, compra.turno, usuarios.nombre, usuarios.apellido, usuarios.mail');
        $this->db->from('compra');
        $this->db->join('usuarios', 'usuarios.id = compra.id_usuario');
        $this->db->where('compra.dia_comprado', $hoy);
        $this->db->where('usuarios.estado', 1);
        $this->db->where('usuarios.notif_recordatorio_retiro', 1);
        $filas = $this->db->get()->result();

        $estudiantes = [];
        foreach ($filas as $fila) {
            if (!isset($estudiantes[$fila->id_usuario])) {
                $estudiantes[$fila->id_usuario] = (object) [
                    'id' => $fila->id_usuario,
                    'nombre' => $fila->nombre,
                    'apellido' => $fila->apellido,
                    'mail' => $fila->mail,
                    'turnos' => [],
                ];
            }
            if (!in_array($fila->turno, $estudiantes[$fila->id_usuario]->turnos, true)) {
                $estudiantes[$fila->id_usuario]->turnos[] = $fila->turno;
            }
        }

        return array_values($estudiantes);
    }
}