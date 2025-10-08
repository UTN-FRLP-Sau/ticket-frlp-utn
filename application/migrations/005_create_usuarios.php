<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_create_usuarios extends CI_Migration
{
	public function up()
	{
		$this->dbforge->add_field([
			'id' => [
				'type' => 'INT',
				'constraint' => '10',
				'unsigned' => TRUE,
				'auto_increment' => TRUE
			],
			'tipo' => [
				'type' => 'VARCHAR',
				'constraint' => '20'
			],
			'legajo' => [
				'type' => 'INT',
				'constraint' => '6'
			],
			'documento' => [
				'type' => 'INT',
				'constraint' => '8'
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
				'constraint' => '50'
			],
			'especialidad' => [
				'type' => 'VARCHAR',
				'constraint' => '10',
				'null' => TRUE,
				'blank' => TRUE
			],
			'mail' => [
				'type' => 'VARCHAR',
				'constraint' => '50'
			],
			'saldo' => [
				'type' => 'INT',
				'constraint' => '5'
			],
			'estado' => [
				'type' => 'BOOLEAN',
				'default' => TRUE
			],
			'id_precio' => [
				'type' => 'INT',
				'constraint' => '2'
			],
		]);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('usuarios');
	}
	public function down()
	{
		$this->dbforge->drop_table('usuarios');
	}
}