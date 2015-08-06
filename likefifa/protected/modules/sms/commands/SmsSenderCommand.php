<?php
namespace dfs\modules\sms\commands;

use dfs\common\components\console\Command;
use dfs\modules\sms\models\Sms;
use CLogger;
use Yii;

/*
 * Отправляет СМС
 * Запускать каждую минуту
 */
class SmsSenderCommand extends Command
{
	/**
	 * Выбирает СМС для отправки и отправляет
	 *
	 * @throws \Exception
	 *
	 * @param array $args command line parameters for this command.
	 *
	 * @return int|void
	 */
	public function run($args)
	{
		$this->log("Start SMSsend");
		$success_counter = 0;

		if ($sms_model = Sms::model()->findAll(
				'send_time<:time AND status=:status',
				array(
					':time' => time(),
					':status' => Sms::STATUS_NEW,
				)
			)
		) {
			$log_message = sprintf("Выбранно %d сообщений", count($sms_model));
			$this->log($log_message, CLogger::LEVEL_INFO);

			foreach ($sms_model as $sms) {
				if ($this->send($sms)) {
					$success_counter++;
				}
			}
		}

		$this->log("{$success_counter} messages has been sent");
		$this->log("End SMSsend");
	}

	/**
	 * Отправляет СМС
	 *
	 * @param Sms $sms
	 *
	 * @return bool
	 */
	private function send($sms) {
		$log_message = 'Отправка СМС. Текст: "' . $sms->message . '". Номер: "' . $sms->number . '".';

		// Сообщение запрещено к отправке
		if (!$sms->canSend()) {
			$log_message = "$log_message (Проигнорированно)";
			$this->log($log_message, CLogger::LEVEL_WARNING);
			$sms->status = Sms::STATUS_SENT;
			return $sms->save();
		}

		// Сообщение просрочено
		if ($sms->send_time < (time()-3600)) {
			$log_message = "$log_message (Просрочено)";
			$this->log($log_message, CLogger::LEVEL_WARNING);
			$sms->status = Sms::STATUS_TIMEOUT;
			return $sms->save();
		}

		// Отправка сообщения
		$result = Yii::app()->sms->send_sms($sms->number, $sms->message);
		if($result !== false) {
			$log_message = "$log_message (Успешно)";
			$this->log($log_message);
			$sms->status = Sms::STATUS_SENT;
			echo "Message sent at " . date("m.d.y H:i:s", $sms->send_time) . "\n";
			return $sms->save();
		} else {
			$log_message = "$log_message (Не удалось)";
			$this->log($log_message, CLogger::LEVEL_WARNING);
			$sms->status = Sms::STATUS_FAILED;

			$this->log('Ошибка: ' . Yii::app()->sms->getLastError(), CLogger::LEVEL_WARNING);
			$this->log('Сообщение: ' . $log_message, CLogger::LEVEL_WARNING);

			return $sms->save();
		}
	}
}