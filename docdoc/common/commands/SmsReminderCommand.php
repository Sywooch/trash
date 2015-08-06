<?php
use dfs\common\components\console\Command;
use dfs\docdoc\models\RequestModel;
use dfs\docdoc\models\SmsRequestModel;


/**
 * Напоминания по заявкам
 *
 */
class SmsReminderCommand extends Command
{
	/**
	 * Отправка напоминания
	 *
	 * @return void
	 */
	public function actionSend()
	{

		$statuses = array(RequestModel::STATUS_RECORD);
		$sources = array(
			RequestModel::SOURCE_SITE,
			RequestModel::SOURCE_PHONE,
			RequestModel::SOURCE_IPHONE,
		);
		$startDate = time();
		$endDate = time() + SmsRequestModel::TIME_TO_REMINDER;
		$requests = RequestModel::model()
			->inStatuses($statuses)
			->inSourceTypes($sources)
			->forPeriodOfDateAdmission($startDate, $endDate)
			->findAll(array(
				'condition' => 'status_sms <> :status',
				'params'    => array(':status' => SmsRequestModel::STATUS_SMS_REMINDER),
			));

		$this->log("Начало отправки напоминаний......");
		$count = 0;
		foreach ($requests as $request) {
			if (!empty($request->clinic) && $request->clinic->sendSMS == 'yes') {
				$this->log("Отправлено напоминание по заявке " . $request->req_id);
				if ((new SmsRequestModel())->requestReminder($request)) {
					$count++;
				}
			}
		}
		$this->log("Отправлено {$count} напоминаний......");
	}

}