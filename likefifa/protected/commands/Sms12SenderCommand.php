<?php

use dfs\common\components\console\Command;

/*
 * По рассписанию, когда баланс становится меньше 400 руб., но больше 0 рублей. Отсылается 1 раз в день 2 дня, далее через 3 дня. После не отсылается
 * По рассписанию. От нуля и меньше. Отсылается 1 раз в день 2 дня, далее через 3 дня. После не отсылается.
 * Отправляем в 12:00 в том числе и в выходные
 */
class Sms12SenderCommand extends Command
{

	/**
	 * @param array $args command line parameters for this command.
	 *
	 * @return int|void
	 * @throws Exception
	 */
	public function run($args)
	{
		$this->log("Start Sms12SenderCommand");

		$masters = LfMaster::model()->findAll();
		foreach ($masters as $master) {
			$send = false;
			$err = '';
			$balance = $master->getBalance();
			if ($balance <= 0) {
				$sms = new Sms;
				$send = $sms->makeNullBalanceSmsForMaster($master, $err);

				// Если мастер принял договор аферты - блокируем
				if ($master->is_popup == LfMaster::IS_POPUP_RECEIVED && $master->is_published == 1) {
					$master->is_blocked = 1;
					if ($master->save()) {
						$this->log("У мастера баланс меньше нуля #{$master->id}");
					} else {
						$this->log("Не удалось заблокировать мастера #{$master->id}. Модель не сохраняется");
					}
				}
			} else if ($balance <= Yii::app()->params["littelBalance"]) {
				$sms = new Sms;
				$send = $sms->makeLittleBalanceSmsForMaster($master, $err);
				if ($send) {
					$this->log("У мастера баланс меньше " .
						Yii::app()->params["littelBalance"] .
						" рублей #{$master->id}");
				}
			}

			if (!$send) {
				$this->log("Предупреждение не отправлялось мастер #{$master->id}, баланс {$balance}р: $err.");
			}
		}

		$this->log("End Sms12SenderCommand");
	}
}