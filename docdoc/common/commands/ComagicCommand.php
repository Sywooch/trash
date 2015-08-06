<?php

/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 30.07.14
 * Time: 18:23
 */
use dfs\common\components\console\Command;
use dfs\docdoc\models\ComagicLogModel;
use dfs\docdoc\objects\comagic\LogLoader;

/**
 * Команда, шлет call
 *
 * Class ComagicCommand
 */
class ComagicCommand extends Command
{
	/**
	 * Метод по умолчанию
	 */
	public function actionIndex()
	{
		$logger = Yii::getLogger();

		$config = \Yii::app()->params['comagic'];
		$logLoader = new LogLoader($config['url'], $config['login'], $config['password'], $config['customer_id']);

		if (!$dateFrom = ComagicLogModel::model()->getMaxCallDate()) {
			$dateFrom = date('Y-m-d', time());
		} else {
			$dateFrom = date('Y-m-d H:i:s', strtotime('-2 hour', strtotime($dateFrom)));
		}

		$dateTill = date('Y-m-d H:i:s', time());
		$logger->log('dateFrom: ' . $dateFrom . ' dateTill: ' . $dateTill);

		$logs = $logLoader->loadLogs($dateFrom, $dateTill);
		$logger->log('Логов в респонсе: ' . count($logs));

		$logged = 0;

		foreach ($logs as $row) {
			is_array($row['file_link']) && $row['file_link'] = implode(PHP_EOL, $row['file_link']);

			$log = new ComagicLogModel('log_collector');
			$log->attributes = $row;
			$log->save();

			!$log->getErrors() && $logged++;
		}

		$logger->log("Записано в базу: " . $logged);

		$notCheckedLogs = ComagicLogModel::model()
			->notChecked()
			->withoutRequestId()
			->findAll();

		$withRequests = 0;

		foreach ($notCheckedLogs as $log) {
			$log->saveRequest();

			$log->request_id && $withRequests++;
		}

		$logger->log("Найдено заявок по логам: " . $withRequests);
	}

}
