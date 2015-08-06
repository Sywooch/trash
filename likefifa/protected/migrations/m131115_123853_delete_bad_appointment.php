<?php
/**
 * Обновляет битые заявки. Время истекло.
 *
 * @see https://docdoc.megaplan.ru/task/1002481/card/
 */

class m131115_123853_delete_bad_appointment extends CDbMigration
{

	public function up()
	{
		$status = array(LfAppointment::STATUS_NEW, LfAppointment::STATUS_15_MIN_LEFT);
		$appointments =
			Yii::app()->db->createCommand(
				'SELECT id, phone, specialization_id
				FROM lf_appointment
				WHERE `status` IN(' . join(',', $status) . ')'
			)->queryAll();
		foreach ($appointments as $appointment) {
			$id = $appointment['id'];
			if (!$appointment["specialization_id"] || !$appointment["phone"]) {
				Yii::app()->db->createCommand()->update(
					"lf_appointment",
					array(
						"status" => LfAppointment::STATUS_REJECTED_BY_TIMEOUT,
					),
					"id = '{$id}'"
				);
				echo "> Заявка №{$id} - время истекло \n";
			}
		}
	}
}