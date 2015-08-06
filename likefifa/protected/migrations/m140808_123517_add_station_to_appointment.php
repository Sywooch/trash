<?php

/**
 * Добавляет метро к заявке
 * Class m140808_123517_add_station_to_appointment
 */
class m140808_123517_add_station_to_appointment extends CDbMigration
{
	public function safeUp()
	{
		$this->addColumn('lf_appointment', 'underground_station_id', 'INT(11) NULL DEFAULT NULL');
		$this->addForeignKey(
			'appointment_underground_station_id',
			'lf_appointment',
			'underground_station_id',
			'underground_station',
			'id',
			'SET NULL',
			'SET NULL'
		);
	}

	public function safeDown()
	{
		$this->dropForeignKey('appointment_underground_station_id', 'lf_appointment');
		$this->dropColumn('lf_appointment', 'appointment_underground_station_id');
	}
}