<?php

/**
 * Отмечает все заявки со статусом "время вышло" как завершенные
 *
 * Class m140717_075221_update_appointment_status
 */
class m140717_075221_update_appointment_status extends CDbMigration
{
	public function up()
	{
		$this->update(
			'lf_appointment',
			['status' => LfAppointment::STATUS_REJECTED],
			'status = :old_status',
			[':old_status' => 20]
		);
	}

	public function down()
	{
		echo "m140717_075221_update_appointment_status does not support migration down.\n";
		return false;
	}
}