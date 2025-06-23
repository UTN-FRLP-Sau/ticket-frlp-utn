<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Alter_log_compra_transaction_fields extends CI_Migration {

    public function up()
    {
        $this->dbforge->modify_column('log_compra', [
            'transaccion_tipo' => [
                'type'       => 'VARCHAR',
                'constraint' => '30',
                'null'       => TRUE,
                'default'    => '0',
            ],
        ]);

        $this->dbforge->modify_column('log_compra', [
            'transaccion_id' => [
                'type'       => 'VARCHAR',
                'constraint' => '30',
                'null'       => TRUE,
                'default'    => '0',
            ],
        ]);
    }

    public function down()
    {
        $this->dbforge->modify_column('log_compra', [
            'transaccion_tipo' => [
                'type'       => 'VARCHAR',
                'constraint' => '10',
                'null'       => TRUE,
                'default'    => '0',
            ],
        ]);

        $this->dbforge->modify_column('log_compra', [
            'transaccion_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => TRUE,
                'null'       => TRUE,
                'default'    => 0,
            ],
        ]);
    }
}