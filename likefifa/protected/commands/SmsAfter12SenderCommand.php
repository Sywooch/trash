<?php

use dfs\common\components\console\Command;

/**
 * SmsAfter12SenderCommand class file.
 *
 * Отправляет сообщения для незавершенных заявок + завершает их
 * Запускать каждую минуту
 *
 * @author   Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @see      https://docdoc.megaplan.ru/task/1003065/card/
 * @package  commands
 */
class SmsAfter12SenderCommand extends Command
{

	/**
	 * Запускается команда
	 *
	 * @param array $args параметры команды
	 *
	 * @return void
	 */
	public function run($args)
	{
		$this->log("================================================================================");

		$this->_sendFirst();
		$this->_sendSecond();
		$this->_sendLast();

		$this->log("================================================================================");
	}

	/**
	 * Отправляет сообщения для незавершенных заявок первый раз через {@link LfAppointment::LONG_FIRST} часов
	 *
	 * @return void
	 */
	private function _sendFirst()
	{
		$this->log(
			"Рассылка первого напоминания о завершении заявки (через " . LfAppointment::LONG_FIRST . " ч.)",
			CLogger::LEVEL_INFO,
			"application.commands.SmsAfter12SenderCommand"
		);

		$appointmentIds = LfAppointment::model()->sendRemindersForFirstTime();
		if ($appointmentIds) {
			foreach ($appointmentIds as $id) {
				$this->log(
					"Оптравлено для заявки №{$id}",
					CLogger::LEVEL_INFO,
					"application.commands.SmsAfter12SenderCommand"
				);
			}
		}

		$this->log(
			"Отправлено сообщений: " . count($appointmentIds),
			CLogger::LEVEL_INFO,
			"application.commands.SmsAfter12SenderCommand"
		);

		$this->log(
			"Закончена рассылка первого напоминания о завершении заявки",
			CLogger::LEVEL_INFO,
			"application.commands.SmsAfter12SenderCommand"
		);
	}

	/**
	 * Отправляет сообщения для незавершенных заявок второй раз через {@link LfAppointment::LONG_SECOND} часов
	 *
	 * @return void
	 */
	private function _sendSecond()
	{
		$this->log(
			"Рассылка второго напоминания о завершении заявки (через " . LfAppointment::LONG_SECOND . " ч.)",
			CLogger::LEVEL_INFO,
			"application.commands.SmsAfter12SenderCommand"
		);

		$appointmentIds = LfAppointment::model()->sendRemindersForSecondTime();
		if ($appointmentIds) {
			foreach ($appointmentIds as $id) {
				$this->log(
					"Оптравлено для заявки №{$id}",
					CLogger::LEVEL_INFO,
					"application.commands.SmsAfter12SenderCommand"
				);
			}
		}

		$this->log(
			"Отправлено сообщений: " . count($appointmentIds),
			CLogger::LEVEL_INFO,
			"application.commands.SmsAfter12SenderCommand"
		);

		$this->log(
			"Закончена рассылка второго напоминания о завершении заявки",
			CLogger::LEVEL_INFO,
			"application.commands.SmsAfter12SenderCommand"
		);
	}

	/**
	 * Отправляет сообщения для незавершенных заявок последний раз раз через {@link LfAppointment::LONG_LAST} часов
	 * Завершает заявку
	 *
	 * @return void
	 */
	private function _sendLast()
	{
		$this->log(
			"Рассылка последнего напоминания о завершении заявки (через " .
				LfAppointment::LONG_LAST .
				" ч.) + завершение заявок",
			CLogger::LEVEL_INFO,
			"application.commands.SmsAfter12SenderCommand"
		);

		$appointmentIds = LfAppointment::model()->sendRemindersForLastTime();
		if ($appointmentIds) {
			foreach ($appointmentIds as $id) {
				$this->log(
					"Оптравлено для заявки №{$id}",
					CLogger::LEVEL_INFO,
					"application.commands.SmsAfter12SenderCommand"
				);
			}
		}

		$this->log(
			"Отправлено сообщений: " . count($appointmentIds),
			CLogger::LEVEL_INFO,
			"application.commands.SmsAfter12SenderCommand"
		);

		$this->log(
			"Закончена рассылка последнего напоминания о завершении заявки + завершение",
			CLogger::LEVEL_INFO,
			"application.commands.SmsAfter12SenderCommand"
		);
	}
}