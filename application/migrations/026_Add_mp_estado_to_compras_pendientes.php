<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_mp_estado_to_compras_pendientes extends CI_Migration {

    public function up()
    {
        // Agrego columna 'mp_estado' a la tabla 'compra_pendiente'
        // Esto almacenará el estado de Mercado Pago (pendiente, aprobada, rechazada, etc.)
        $fields_mp_estado = array(
            'mp_estado' => array(
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'default'    => null,
                'null'       => null,
                'after'      => 'procesada' // Posiciona la columna después de la columna 'procesada'
            )
        );
        $this->dbforge->add_column('compras_pendientes', $fields_mp_estado);

        log_message('info', 'Migration 26: Column "mp_estado" added to table "compras_pendientes".');

        // Modificar el tipo de columna 'transaccion_id' en la tabla 'compra' a BIGINT
        $fields_compra_transaccion_id = array(
            'transaccion_id' => array(
                'type'       => 'BIGINT',
                'constraint' => 20,
                'null'       => TRUE,
                'default'    => 0,
            )
        );
        $this->dbforge->modify_column('compra', $fields_compra_transaccion_id);

        log_message('info', 'Migration 26: Column "transaccion_id" in table "compra" modified to BIGINT.');

        // Modificar el tipo de columna 'transaccion_id' en la tabla 'log_compra' a BIGINT
        $fields_log_compra_transaccion_id = array(
            'transaccion_id' => array(
                'type'       => 'BIGINT',
                'constraint' => 20,
                'null'       => TRUE, 
                'default'    => 0,
            )
        );
        $this->dbforge->modify_column('log_compra', $fields_log_compra_transaccion_id);

        log_message('info', 'Migration 26: Column "transaccion_id" in table "log_compra" modified to BIGINT.');
    }

    public function down()
    {
        // Eliminar la columna 'mp_estado' si se hace un rollback de la migración
        $this->dbforge->drop_column('compras_pendientes', 'mp_estado');

        log_message('info', 'Migration 26: Column "mp_estado" dropped from table "compras_pendientes".');

        // Revertir el tipo de columna 'transaccion_id' en la tabla 'compra' a INT
        $fields_compra_transaccion_id_revert = array(
            'transaccion_id' => array(
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => TRUE,
                'default'    => 0, 
            )
        );
        $this->dbforge->modify_column('compra', $fields_compra_transaccion_id_revert);

        log_message('info', 'Migration 26: Column "transaccion_id" in table "compra" reverted to INT.');

        // Revertir el tipo de columna 'transaccion_id' en la tabla 'log_compra' a INT
        $fields_log_compra_transaccion_id_revert = array(
            'transaccion_id' => array(
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => TRUE,
                'default'    => 0,
            )
        );
        $this->dbforge->modify_column('log_compra', $fields_log_compra_transaccion_id_revert);

        log_message('info', 'Migration 26: Column "transaccion_id" in table "log_compra" reverted to INT.');
    }
}
