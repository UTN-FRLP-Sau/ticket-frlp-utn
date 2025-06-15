<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Alter_transactions_and_add_external_reference extends CI_Migration {

    public function up()
    {
        // --- Modifica la tabla 'transacciones' ---
        // Cambia 'monto' de INT(5) a DECIMAL(10,2)
        // Cambia 'saldo' de VARCHAR(5) a DECIMAL(10,2)

        $this->dbforge->modify_column('transacciones', [
            'monto' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => FALSE,
                'default' => '0.00'
            ],
            'saldo' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => TRUE,
                'default' => NULL
            ]
        ]);

        echo "Migración: Tabla 'transacciones' modificada (monto a DECIMAL, saldo a DECIMAL).\n";

        $this->dbforge->add_column('compra', [
            'external_reference' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
                'after' => 'menu'
            ]
        ]);

        echo "Migración: Columna 'external_reference' añadida a la tabla 'compra'.\n";

        $this->dbforge->add_column('log_compra', [
            'external_reference' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE, 
                'after' => 'menu'
            ]
        ]);

        echo "Migración: Columna 'external_reference' añadida a la tabla 'log_compra'.\n";

        echo "Migración 'Alter_transactions_and_add_external_reference' aplicada con éxito (UP).\n";
    }

    public function down()
    {
        // --- Revertir la tabla 'transacciones' ---
        // La reversión de DECIMAL a INT/VARCHAR puede causar pérdida de datos
        // si ya existen valores con decimales o números grandes.
        $this->dbforge->modify_column('transacciones', [
            'monto' => [
                'type' => 'INT',
                'constraint' => '5',
                'null' => FALSE,
                'default' => '0'
            ],
            'saldo' => [
                'type' => 'VARCHAR',
                'constraint' => '5',
                'null' => TRUE
            ]
        ]);

        echo "Migración: Tabla 'transacciones' revertida (monto a INT, saldo a VARCHAR). CUIDADO: posible pérdida de datos.\n";


        $this->dbforge->drop_column('compra', 'external_reference');
        echo "Migración: Columna 'external_reference' eliminada de la tabla 'compra'.\n";

        $this->dbforge->drop_column('log_compra', 'external_reference');
        echo "Migración: Columna 'external_reference' eliminada de la tabla 'log_compra'.\n";


        echo "Migración 'Alter_transactions_and_add_external_reference' revertida (DOWN).\n";
    }
}