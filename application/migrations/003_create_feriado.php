<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_create_feriado extends CI_Migration
{
	public function up()
	{
		$this->dbforge->add_field([
			'id' => [
				'type' => 'INT',
				'constraint' => '3',
				'unsigned' => TRUE,
				'auto_increment' => TRUE
			],
			'fecha' => [
				'type' => 'DATE'
			],
			'detalle' => [
				'type' => 'VARCHAR',
				'constraint' => '100'
			]
		]);

		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('feriado');

		$data = [
			['fecha' => '2022-05-01', 'detalle' => 'Día del Trabajador'],
			['fecha' => '2022-05-25', 'detalle' => 'Sin actividad. Día de la Revolución de Mayo'],
			//['fecha' => '2022-05-26', 'detalle' => '2do. Turno de examen Final. Suspensión de clases'],
			//['fecha' => '2022-05-26', 'detalle' => '2do. Turno de examen Final. Suspensión de clases'],
			['fecha' => '2022-06-17', 'detalle' => 'Sin actividad. Paso a la inmortalidad del Gral. Don Martín Miguel de Güemes'],
			['fecha' => '2022-06-20', 'detalle' => 'Sin actividades. Paso a la inmortalidad del Gral. Manuel Belgrano'],
			//['fecha' => '2022-06-21', 'detalle' => '3er. Turno examen Final. Suspensión de clases'],
			['fecha' => '2022-07-09', 'detalle' => 'Día de la Independencia'],
			//['fecha' => '2022-07-18', 'detalle' => 'Receso invernal'],
			//['fecha' => '2022-07-19', 'detalle' => 'Receso invernal'],
			//['fecha' => '2022-07-20', 'detalle' => 'Receso invernal'],
			//['fecha' => '2022-07-21', 'detalle' => 'Receso invernal'],
			//['fecha' => '2022-07-22', 'detalle' => 'Receso invernal'],
			//['fecha' => '2022-07-23', 'detalle' => 'Receso invernal'],
			//['fecha' => '2022-07-25', 'detalle' => 'Receso invernal'],
			//['fecha' => '2022-07-26', 'detalle' => 'Receso invernal'],
			//['fecha' => '2022-07-27', 'detalle' => 'Receso invernal'],
			//['fecha' => '2022-07-28', 'detalle' => 'Receso invernal'],
			//['fecha' => '2022-07-29', 'detalle' => 'Receso invernal'],
			//['fecha' => '2022-07-30', 'detalle' => 'Receso invernal'],
			['fecha' => '2022-08-15', 'detalle' => 'Sin actividades. Paso a la inmortalidad del Gral. San Martín'],
			['fecha' => '2022-08-19', 'detalle' => 'Sin actividad. Día de la UTN'],
			//['fecha' => '2022-08-23', 'detalle' => '4to. Turno de examen. Suspensión de clases'],
			['fecha' => '2022-09-12', 'detalle' => 'Sin actividad. Día del Estudiante'],
			['fecha' => '2022-09-24', 'detalle' => 'Sin actividad. Día de la FRLP'],
			//['fecha' => '2022-09-26', 'detalle' => '5to. Turno de examen Final. Suspensión de clases'],
			//['fecha' => '2022-09-27', 'detalle' => '5to. Turno de examen Final. Suspensión de clases'],
			//['fecha' => '2022-09-28', 'detalle' => '5to. Turno de examen Final. Suspensión de clases'],
			//['fecha' => '2022-09-29', 'detalle' => '5to. Turno de examen Final. Suspensión de clases'],
			//['fecha' => '2022-09-30', 'detalle' => '5to. Turno de examen Final. Suspensión de clases'],
			['fecha' => '2022-10-07', 'detalle' => 'Sin actividad. Feriado con fin turístico'],
			['fecha' => '2022-10-10', 'detalle' => 'Sin actividad. Día del Respeto a la Diversidad Cultural'],
			//['fecha' => '2022-10-20', 'detalle' => '6to. Turno de examen Final. Suspensión de clases'],
			['fecha' => '2022-11-01', 'detalle' => 'Sin actividad. Día de la Soberanía Nacional'],
			['fecha' => '2022-11-26', 'detalle' => 'Sin actividad. Día del Trabajador Nodocente'],
			['fecha' => '2022-12-08', 'detalle' => 'Sin actividad. Día de la Inmaculada Concepción de la Virgen María'],
			['fecha' => '2022-12-09', 'detalle' => 'Sin actividad. Feriado con fin turístico'],
			//['fecha' => '2022-12-12', 'detalle' => '7mo. Turno de examen Final'],
			//['fecha' => '2022-12-13', 'detalle' => '7mo. Turno de examen Final'],
			//['fecha' => '2022-12-14', 'detalle' => '7mo. Turno de examen Final'],
			//['fecha' => '2022-12-15', 'detalle' => '7mo. Turno de examen Final'],
			//['fecha' => '2022-12-16', 'detalle' => '7mo. Turno de examen Final'],
			['fecha' => '2022-12-25', 'detalle' => 'Sin actividad. Navidad'],
			// Pasamos al siguiente año
			['fecha' => '2023-01-01', 'detalle' => 'Receso de actividades'],
			['fecha' => '2023-01-02', 'detalle' => 'Receso de actividades'],
			['fecha' => '2023-01-03', 'detalle' => 'Receso de actividades'],
			['fecha' => '2023-01-04', 'detalle' => 'Receso de actividades'],
			['fecha' => '2023-01-05', 'detalle' => 'Receso de actividades'],
			['fecha' => '2023-01-06', 'detalle' => 'Receso de actividades'],
			['fecha' => '2023-01-07', 'detalle' => 'Receso de actividades'],
			['fecha' => '2023-01-08', 'detalle' => 'Receso de actividades'],
			['fecha' => '2023-01-09', 'detalle' => 'Receso de actividades'],
			['fecha' => '2023-01-10', 'detalle' => 'Receso de actividades'],
			['fecha' => '2023-01-11', 'detalle' => 'Receso de actividades'],
			['fecha' => '2023-01-12', 'detalle' => 'Receso de actividades'],
			['fecha' => '2023-01-13', 'detalle' => 'Receso de actividades'],
			['fecha' => '2023-01-14', 'detalle' => 'Receso de actividades'],
			['fecha' => '2023-01-15', 'detalle' => 'Receso de actividades'],
			['fecha' => '2023-01-16', 'detalle' => 'Receso de actividades'],
			['fecha' => '2023-01-17', 'detalle' => 'Receso de actividades'],
			['fecha' => '2023-01-18', 'detalle' => 'Receso de actividades'],
			['fecha' => '2023-01-19', 'detalle' => 'Receso de actividades'],
			['fecha' => '2023-01-20', 'detalle' => 'Receso de actividades'],
			['fecha' => '2023-01-21', 'detalle' => 'Receso de actividades'],
			['fecha' => '2023-01-22', 'detalle' => 'Receso de actividades'],
			['fecha' => '2023-01-23', 'detalle' => 'Receso de actividades'],
			['fecha' => '2023-01-24', 'detalle' => 'Receso de actividades'],
			['fecha' => '2023-01-25', 'detalle' => 'Receso de actividades'],
			['fecha' => '2023-01-26', 'detalle' => 'Receso de actividades'],
			['fecha' => '2023-01-27', 'detalle' => 'Receso de actividades'],
			['fecha' => '2023-01-28', 'detalle' => 'Receso de actividades'],
			['fecha' => '2023-01-29', 'detalle' => 'Receso de actividades'],
			['fecha' => '2023-01-30', 'detalle' => 'Receso de actividades'],
			['fecha' => '2023-01-31', 'detalle' => 'Receso de actividades'],
			//['fecha' => '2023-02-13', 'detalle' => '8vo. Turno examen Final'],
			//['fecha' => '2023-02-14', 'detalle' => '8vo. Turno examen Final'],
			//['fecha' => '2023-02-15', 'detalle' => '8vo. Turno examen Final'],
			//['fecha' => '2023-02-16', 'detalle' => '8vo. Turno examen Final'],
			//['fecha' => '2023-02-16', 'detalle' => '8vo. Turno examen Final'],
			['fecha' => '2023-02-20', 'detalle' => 'Sin actividad. Carnaval'],
			['fecha' => '2023-02-21', 'detalle' => 'Sin actividad. Carnaval'],
			//['fecha' => '2023-03-06', 'detalle' => '9no. Turno examen Final'],
			//['fecha' => '2023-03-07', 'detalle' => '9no. Turno examen Final'],
			//['fecha' => '2023-03-08', 'detalle' => '9no. Turno examen Final'],
			//['fecha' => '2023-03-09', 'detalle' => '9no. Turno examen Final'],
			//['fecha' => '2023-03-10', 'detalle' => '9no. Turno examen Final'],
			//['fecha' => '2023-03-20', 'detalle' => '10mo. Turno examen Final'],
			//['fecha' => '2023-03-21', 'detalle' => '10mo. Turno examen Final'],
			//['fecha' => '2023-03-22', 'detalle' => '10mo. Turno examen Final'],
			//['fecha' => '2023-03-23', 'detalle' => '10mo. Turno examen Final'],
			['fecha' => '2023-03-24', 'detalle' => 'Sin actividad. Día Nacional de la Memoria por la Verdad y la Justicia']
		];

		$this->db->insert_batch('feriado', $data);
	}
	public function down()
	{
		$this->dbforge->drop_table('feriado');
	}
}