<?php

use phpDocumentor\Reflection\PseudoTypes\True_;

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_create_vendedores extends CI_Migration
{
	public function up()
	{
		$this->dbforge->add_field([
			'id_vendedor' => [
				'type' => 'INT',
				'constraint' => '10',
				'unsigned' => TRUE,
				'auto_increment' => TRUE
			],
			'nombre_usuario' => [
				'type' => 'VARCHAR',
				'constraint' => '20',
				'unique' => TRUE
			],
			'pass' => [
				'type' => 'VARCHAR',
				'constraint' => '32'
			],
			'nombre' => [
				'type' => 'VARCHAR',
				'constraint' => '50'
			],
			'apellido' => [
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => TRUE,
				'blank' => TRUE
			],
			'mail' => [
				'type' => 'VARCHAR',
				'constraint' => '50'
			],
			'estado' => [
				'type' => 'BOOLEAN',
				'default' => TRUE
			],
			'nivel' => [
				'type' => 'INT',
				'constraint' => '2',
				'default' => 0
			]
		]);
		$this->dbforge->add_key('id_vendedor', TRUE);
		$this->dbforge->create_table('vendedores');

		$data = [
			[
				'id_vendedor' => 1,
				'nombre_usuario' => 'jorge',
				'pass' => 'e2a612d80f28f71a500fda3b271088d3',
				'nombre' => 'Jorge',
				'apellido' => 'Ronconi',
				'mail' => 'jorge.ronconi@gmail.com',
				'nivel' => 1
			],
		];

		$this->db->insert_batch('vendedores', $data);
	}
	public function down()
	{
		$this->dbforge->drop_table('vendedores');
	}
}