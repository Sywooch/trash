<?php
use dfs\common\components\console\Command;

/**
 * AppointmentsCompleteCommand class file.
 *
 * Завершает заявки по истечении 24 часа
 *
 * @author  Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @see     https://docdoc.megaplan.ru/task/1003068/card/
 * @package commands
 */
class AppointmentsCompleteCommand extends Command
{

	/**
	 * @param array $args
	 *
	 * @return void
	 */
	public function run($args)
	{
		$this->log(
			"=======================================",
			CLogger::LEVEL_INFO,
			"protected.commands.AppointmentsCompleteCommand"
		);
		$this->log(
			"Начали завершаться незавершенные заявки",
			CLogger::LEVEL_INFO,
			"protected.commands.AppointmentsCompleteCommand"
		);

		$count = 0;

		$appointments = LfAppointment::model()->getNonAccepted();
		if ($appointments) {
			$this->log(
				"---------------------------------------",
				CLogger::LEVEL_INFO,
				"protected.commands.AppointmentsCompleteCommand"
			);

			foreach ($appointments as $model) {
				if ($model->automaticCompletion()) {
					$this->log(
						"Завершена заявка: №{$model->id}",
						CLogger::LEVEL_INFO,
						"protected.commands.AppointmentsCompleteCommand"
					);
					$count++;
				}
			}

			$this->log(
				"---------------------------------------",
				CLogger::LEVEL_INFO,
				"protected.commands.AppointmentsCompleteCommand"
			);
		}

		$this->log(
			"Завершено заявок: {$count}",
			CLogger::LEVEL_INFO,
			"protected.commands.AppointmentsCompleteCommand"
		);
		$this->log(
			"Заявки успешно завершены",
			CLogger::LEVEL_INFO,
			"protected.commands.AppointmentsCompleteCommand"
		);
		$this->log(
			"=======================================",
			CLogger::LEVEL_INFO,
			"protected.commands.AppointmentsCompleteCommand"
		);
	}
}