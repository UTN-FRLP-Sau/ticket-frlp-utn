<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_external_reference_to_transacciones extends CI_Migration {

    public function up()
    {
        // Define la estructura de la columna
        $fields = array(
            'external_reference' => array(
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => TRUE,
                'after'      => 'saldo',
            ),
        );

        // Añade la columna a la tabla 'transacciones'
        $this->dbforge->add_column('transacciones', $fields);

    }

    public function down()
    {
        // Elimina la columna de la tabla 'transacciones'
        $this->dbforge->drop_column('transacciones', 'external_reference');
    }
}