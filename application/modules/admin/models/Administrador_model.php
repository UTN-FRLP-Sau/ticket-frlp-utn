<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Administrador_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAdminById($id)
    {
        /*Usado en:
        createVendedor
        */
        $this->db->select('*');
        $this->db->where('id_vendedor', $id);
        $query = $this->db->get('vendedores');
        return $query->row();
    }

    public function addNewVendedor($data)
    {
        /*Usado en:
        createVendedor
        */
        $this->db->insert('vendedores', $data);

        return true;
    }

    public function getCargaByIdvendedorForEmailCSV($idvendedor)
    {
        /*Usado en:
        confirmarCargasCVS
        */
        $this->db->select('*');
        $this->db->from('log_carga');
        $this->db->join('usuarios', 'log_carga.id_usuario=usuarios.id');
        $this->db->where('id_vendedor', $idvendedor);
        $this->db->order_by('log_carga.id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row();
    }

    public function addCargaLog($data)
    {
        /*Usado en:
        confirmarCargasCVS
        */
        $this->db->insert('log_carga', $data);
        return true;
    }

    public function addTransaccion($data)
    {
        /* Usdo en:
        confirmarCargasCVS
        devolver_compras_by_fecha
        */
        $this->db->insert('transacciones', $data);
        return $this->db->insert_id();
    }

    public function updateAndGetSaldoByUserId($iduser, $saldo)
    {
        /*Usado en:
        confirmarCargasCVS
        */
        $saldoActual = $this->db->select('saldo')->where('id', $iduser)->get('usuarios')->row('saldo');
        $saldoNuevo = $saldoActual + $saldo;

        $this->db->set('saldo', $saldoNuevo);
        $this->db->where('id', $iduser);
        $this->db->update('usuarios');
        return $saldoNuevo;
    }

    public function getUserByDocumento($documento)
    {
        /*Usado en:
        confirmarCargasCVS
        */
        $this->db->select('*');
        $this->db->where('documento', $documento);
        $query = $this->db->get('usuarios');
        return $query->row();
    }

    public function getComentarios() 
    {
        /*Usado en:
        ver_comentarios
        */
        $query = $this->db->get('comentarios');
        return $query->result();
    }

    public function getConfiguracion()
    {
        /*Usado en:
        configuracion_general
        */
        return $this->db->get('configuracion')->result();
    }

    public function updateConfiguracion($data)
    {
        /*Usado en:
        configuracion_general
        */
        $this->db->where('id', 1);
        $this->db->update('configuracion', $data);
        return true;
    }

    public function getFeriadosByAño($año)
    {
        /*Usado en:
        feriados_list
        */
        $this->db->select('*');
        $this->db->where('YEAR(fecha)', $año);
        $this->db->order_by('fecha', 'ASC');
        $query = $this->db->get('feriado');
        return $query->result();
    }

    public function deletedFeriadoById($id)
    {
        /*Usado en:
        borrar_feriado
        */
        $this->db->where('id',$id);
        $this->db->delete('feriado');
        return true;
    }

    public function addFeriado($data)
    {
        /*Usado en:
        añadir_feriado_fecha
        add_csv_feriado
        */
        $this->db->insert('feriado', $data);
        return true;
    }

    public function getComprasByFecha($fecha)
    {
        /*Usado en:
        devolver_compras_by_fecha
        */
        $this->db->select('*');
        $this->db->where('dia_comprado', $fecha);
        $query = $this->db->get('compra');
        return $query->result();
    }

    public function getUserByID($id_user)
    {
        /*Usado en:
        confirmarCargasCVS
        */
        $this->db->select('*');
        $this->db->where('id', $id_user);
        $query = $this->db->get('usuarios');
        return $query->row();
    }

    public function removeCompra($idcompra)
    {
        /* Usado en:
        devolver_compras_by_fecha
        */
        if ($this->db->delete('compra', ['id' => $idcompra])) {
            return true;
        } else {
            return false;
        }
    }

    public function updateSaldoByIDUser($id_user, $saldo_nuevo)
    {
        /* Usado en:
        devolver_compras_by_fecha
        */
        $this->db->set('saldo', $saldo_nuevo);
        $this->db->where('id', $id_user);
        if ($this->db->update('usuarios')) {
            return true;
        } else {
            return false;
        }
    }

    public function addLogCompra($data)
    {
        /* Usado en:
        devolver_compras_by_fecha
        */
        if ($this->db->insert('log_compra', $data)) {
            return true;
        } else {
            return false;
        }
    }

    public function getPrecios()
    {
        /* Usado en:
        configuracion_costos
        */
        return $this->db->get('precios')->result();
    }
    

    public function updatePrecios($id_precio, $costo)
    {
        /* Usado en:
        configuracion_costos
        */
        $this->db->set('costo', $costo);
        $this->db->where('id', $id_precio);
        if ($this->db->update('precios')) {
            return true;
        } else {
            return false;
        }
    }

    public function getComprasByUserId($id_user)
    {
        /*Usado en:
        ver_compras_userid
        */
        $this->db->select('*');
        $this->db->where('id_usuario', $id_user);
        $this->db->order_by('dia_comprado', 'DESC');
        $query = $this->db->get('compra');
        return $query->result();
    }

    public function getComprasInRangeByIDUser($limit, $start, $id_user)
    {
        /*Usado en:
        ver_compras_userid
        */
        $this->db->select('*');
        $this->db->where('id_usuario', $id_user);
        $this->db->limit($limit, $start);
        $this->db->order_by('dia_comprado', 'DESC');
        $query = $this->db->get("compra");

        if ($query->num_rows() > 0) {
            return $query->result();
        }
        return false;
    }

    public function getCompraById($id)
    {
        /*Usado en:
        devolver_compra_by_id
        */
        $this->db->select('*');
        $this->db->where('id', $id);
        $query = $this->db->get('compra');
        return $query->row();
    }

    public function getLinkVirtuales()
    {
        return $this->db->get('linkpagos')->result();
    }
    
    public function addLinkVirtual($data)
    {
        /*Usado en:
        */
        $this->db->insert('linkpagos', $data);
        return true;
    }

    public function removeLinkVirtual($id)
    {
        /*Usado en:
        */
        $this->db->where('id',$id);
        $this->db->delete('linkpagos');
        return true;
    }

    public function getLast20Cargas()
    {
        /*Usado en:
        */
        $this->db->select('cargasvirtuales.*, usuarios.nombre, usuarios.apellido, usuarios.documento, vendedores.nombre_usuario as vendedor_username');
        $this->db->from('cargasvirtuales');
        $this->db->join('usuarios', 'cargasvirtuales.usuario = usuarios.id', 'inner');
        $this->db->join('vendedores', 'cargasvirtuales.confirmacion_vendedor = vendedores.id_vendedor', 'left');
        $this->db->order_by('cargasvirtuales.id', 'DESC');
        $this->db->limit(5);
        $query = $this->db->get();
        return $query->result();
    }

    public function getCargasByFecha($fecha)
    {
        /*Usado en:
        */
        $this->db->select('cargasvirtuales.*, usuarios.nombre, usuarios.apellido, usuarios.documento, vendedores.nombre_usuario as vendedor_username');
        $this->db->from('cargasvirtuales');
        $this->db->join('usuarios', 'cargasvirtuales.usuario = usuarios.id', 'inner');
        $this->db->join('vendedores', 'cargasvirtuales.confirmacion_vendedor = vendedores.id_vendedor', 'left');
        $this->db->where('DATE(cargasvirtuales.timestamp)', $fecha);
        $query = $this->db->get();
        return $query->result();
    }

    public function getCargaVirtualByID($id)
    {
        /*Usado en:
        */
        $this->db->select('*');
        $this->db->where('id', $id);
        $query = $this->db->get('cargasvirtuales');
        return $query->row();
    }

    public function updateCargaVirtualByID($id, $vendedor_id, $estado)
    {
        /*Usado en:
        */
        $this->db->set('confirmacion_vendedor', $vendedor_id);
        $this->db->set('estado', $estado);
        $this->db->set('confirmacion_timestamp', date('Y-m-d H:i:s'));
        $this->db->where('id', $id);
        return $this->db->update('cargasvirtuales');
    }

    public function rmCargaVirtualByID($id)
    {
        /*Usado en:
        */
        $this->db->where('id',$id);
        $this->db->delete('cargasvirtuales');
        return true;
    }

  public function getComprasConMedioDePago($id_user, $limit = null, $offset = null)
{
    $this->db->select('c.*, t.transaccion');
    $this->db->from('compra c');
    $this->db->join('transacciones t', 't.id = c.transaccion_id', 'left');
    $this->db->where('c.id_usuario', $id_user);
    $this->db->order_by('c.dia_comprado', 'DESC');

    if ($limit !== null && $offset !== null) {
        $this->db->limit($limit, $offset);
    }

    $query = $this->db->get();
    $compras = $query->result();

    // Mapeo de transacciones a MP o Saldo
    foreach ($compras as $compra) {
        switch ($compra->transaccion) {
            case 'Compra por Mercado P':
                $compra->medio_pago = 'MP';
                break;
            case 'Compra con saldo':
                $compra->medio_pago = 'Saldo';
                break;
            default:
                $compra->medio_pago = $compra->transaccion;
        }
    }

    return $compras;
}

}
