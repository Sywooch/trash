<?php
use dfs\common\components\console\Command;

/**
 * Команда для создания реалтайм предупреждений
 *
 * Class NotificationCommand
 */
class NotificationCommand extends Command
{
	public function run($args)
	{
		$this->appointments();
	}

	/**
	 * Предупреждает оператора о необходимости прозвона
	 */
	protected function appointments()
	{
		$criteria = new CDbCriteria();
		$criteria->addCondition('control >= UNIX_TIMESTAMP() + 240');
		$criteria->addCondition('control < UNIX_TIMESTAMP() + 300');
		$data = LfAppointment::model()->findAll($criteria);
		foreach ($data as $appointment) {
			Yii::app()->elephant->emit(
				'message',
				[
					'event' => 'appointment_call',
					'data'  => [
						'id'       => $appointment->id,
						'date'     => date('d.m.Y', $appointment->control),
						'time'     => date('H:i', $appointment->control),
						'operator' => $appointment->admin_id ? $appointment->admin->name : null,
						'service'  => $appointment->service_id ? $appointment->service->name : null,
					]
				]
			);
		}
	}
} 