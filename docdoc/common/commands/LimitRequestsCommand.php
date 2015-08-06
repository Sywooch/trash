<?php
use dfs\common\components\console\Command;
use dfs\docdoc\models\ClinicRequestLimitModel;
use dfs\docdoc\models\MailQueryModel;

/**
 * Class RecordsCommand
 */
class LimitRequestsCommand extends Command
{

	/**
	 * Отправка уведомления о достигнутом лимита записей
	 */
	public function actionSendNotice()
	{
		$logger = Yii::getLogger();

		$limits = ClinicRequestLimitModel::model()
			->actual()
			->findAll();

		foreach ($limits as $limit) {
			$clinicContract = $limit->clinicContract;
			$services = $limit->contractGroup->getServicesInGroup();
			$count = $clinicContract->getRequestNumInBilling(date('Y-m-01'), date('Y-m-t'), (int)current($services));

			if ($count >= $limit->limit) {
				(new MailQueryModel())->sendMailLimitRequests($limit);
				$limit->date_notice = date('Y-m-d');
				$limit->save();
				$logger->log("Достигнут лимит по клинике id={$limit->clinicContract->clinic->id}");
			}
		}
	}
}
