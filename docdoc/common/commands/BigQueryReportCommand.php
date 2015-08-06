<?php

use dfs\common\components\console\Command;
use dfs\docdoc\reports\RequestReport;
use dfs\docdoc\reports\PartnerReport;
use dfs\docdoc\reports\AnswerSpeedReport;
use dfs\docdoc\reports\MissedCallsReport;
use dfs\docdoc\models\RequestModel;

/**
 * Отчеты в BigQuery
 *
 * Class BigQueryReportCommand
 */
class BigQueryReportCommand extends Command
{

	/**
	 * Отчет по заявкам
	 *
	 */
	public function actionRequests()
	{
		$prevTime = strtotime('-1 month');
		$data = [
			RequestModel::KIND_DOCTOR => [
				date('d.m.Y', $prevTime),
				date('d.m.Y'),
			],
			RequestModel::KIND_DIAGNOSTICS => [
				date('d.m.Y', $prevTime),
				date('d.m.Y'),
			]
		];

		foreach ($data as $kind => $items) {
			foreach ($items as $date) {
				$reportBuilder = new RequestReport($kind, $date);

				$tableName = $reportBuilder->getBqModel()->getTable();
				try {
					$reportBuilder->clear();
					$this->log("- Таблица {$tableName} успешно очищена");
					$reportBuilder->generate();
					$this->log("-- В таблицу {$tableName} загружено " .count($reportBuilder->getData()) . " заявок");
					$reportBuilder->insertIntoBigQuery();
				} catch (Exception $e) {
					$this->log($e->getMessage());
				}
			}
		}
	}

	/**
	 * Отчет по партнерам
	 */
	public function actionPartners()
	{
		$reportBuilder = new PartnerReport();

		try {
			$reportBuilder->clear();
			$this->log("- Таблица partners успешно очищена");
			$reportBuilder->generate();
			$this->log("-- В таблицу partners загружено " .count($reportBuilder->getData()) . " записей");
			$reportBuilder->insertIntoBigQuery();
		} catch (Exception $e) {
			$this->log($e->getMessage());
		}
	}

	/**
	 * Отчет по пропущенным звонкам
	 */
	public function actionMissedCalls()
	{
		$reports = [
			'dfs\docdoc\reports\MissedCallsRawReport',
			'dfs\docdoc\reports\MissedCallsReport'
		];

		foreach ($reports as $report) {
			/**
			 * @var MissedCallsReport $reportBuilder
			 */
			$reportBuilder = new $report();
			$table = $reportBuilder->getBqModel()->getTable();

			try {
				$reportBuilder->generate();
				$this->log("-- В таблицу {$table} загружено " . count($reportBuilder->getData()) . " записей");
				$reportBuilder->insertIntoBigQuery();
			} catch (Exception $e) {
				$this->log($e->getMessage());
			}
		}
	}

	/**
	 * Отчет скрость ответа по заявкам
	 */
	public function actionAnswerSpeed()
	{
		$prevTime = strtotime('-1 month');
		$data = [
			date('d.m.Y', $prevTime),
			date('d.m.Y'),
		];

		foreach ($data as $date) {
			$reportBuilder = new AnswerSpeedReport($date);
			$table = $reportBuilder->getBqModel()->getTable();

			try {
				$reportBuilder->clear();
				$this->log("- Таблица {$table} успешно очищена");
				$reportBuilder->generate();
				$this->log("-- Данные успешно загружены в таблицу");
				$reportBuilder->insertIntoBigQuery();
			} catch (Exception $e) {
				$this->log($e->getMessage());
			}
		}
	}
}
