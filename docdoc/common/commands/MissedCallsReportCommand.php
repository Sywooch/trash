<?php

use dfs\common\components\console\Command;
use dfs\docdoc\reports\MissedCallsReport;
use dfs\docdoc\reports\MissedCalls;

/**
 * Class MissedCallsReportCommand
 */
class MissedCallsReportCommand extends Command
{
	/**
	 * Парсит лог неудавшихся звонков и рассылает xls отчет на имейлы менеджеров
	 *
	 * @return int
	 */
	public function actionIndex()
	{
		$config = Yii::app()->params;
		$report = new MissedCallsReport();

		$calls = $report->loadCalls();
		$report->setCalls($calls);
		$data = $report->getData();
		$this->log('Всего обработанно звонков = ' . count($data));

		// Генерация xls
		$objPHPExcel = (new MissedCalls())->excel($data);

		// Сохранение в runtime
		$file = Yii::app()->runtimePath . '/failed-calls-report-' . time() . '.xls';
		PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5')->save($file);

		// Отправка по имейлам
		$emails = [
			$config['email']['account'],
			$config['email']['support'],
			$config['email']['analytics'],
		];
		$sent = $this->_send($emails, $file);

		$this->log("Отчет отправлен на $sent из " . count($emails) . " email адресов");

		unlink($file);

		return $sent === count($emails) ? 0 : 1;
	}

	/**
	 * Отправка отчета на указанные имейлы
	 *
	 * @param string[] $emails
	 * @param string $file
	 *
	 * @return int
	 */
	protected function _send(array $emails, $file)
	{
		$config = Yii::app()->params;
		$mailer = Yii::app()->mailer;
		$date = date('d.m.Y', strtotime('-1 day', time()));
		$attachment = Swift_Attachment::fromPath($file);
		$attachment->setFilename("Отчет неудавшихся звонков за {$date}.xls");

		$message = $mailer
			->createMessage("[docdoc.ru] Отчет неудавшихся звонков за {$date}")
			->setFrom($config['email']['from'], 'DocDoc')
			->setTo($emails)
			->attach($attachment);

		return $mailer->send($message);
	}
}