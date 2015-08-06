<?php

use dfs\common\components\console\Command;

/**
 * Отправка уведомления для всех мастеров
 *
 * Задача https://docdoc.megaplan.ru/task/1002071/card/
 */
class Plus2000Command extends Command
{
	/**
	 * @param array $args command line parameters for this command.
	 *
	 * @return int|void
	 */
	public function run($args)
	{
		$master_all = LfMaster::model()->findAll();
		$message = file_get_contents(Yii::getPathOfAlias('webroot.data') . '/Plus2000Command_message.txt');
		foreach ($master_all as $master) {
			if (!Messages::model()->find(
				'master_id<:master_id AND type=:type',
				array(
					':master_id' => $master->id,
					':type' => Messages::TYPE_2000,
				)
			)
			) {
				$to = trim($master->email);
				$subject = 'LikeFifa: Пополнение счета';
				letter::create()
					->from(BLACKHOLE_EMAIL)
					->to($to)
					->subject($subject)
					->html($message)
					->send();
				$message_model = new Messages;
				$message_model->message = $message;
				$message_model->type = Messages::TYPE_2000;
				$message_model->email = $to;
				$message_model->master_id = $master->id;
				if ($message_model->save()) {
					$log_message =
						"Мастеру с ID={$master->id} успешно отправлено письмо о получении подарка в 2000 рублей.";
					$this->log($log_message);
				} else {
					$log_message =
						"Мастеру с ID={$master->id} не было отправлено письмо о получении подарка в 2000 рублей (не удалось сохранить запись).";
					$this->log($log_message);
				}
			}
		}
	}

}