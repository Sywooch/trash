<?php
/**
 * Created by PhpStorm.
 * User: atyutyunnikov
 * Date: 24.02.15
 * Time: 16:30
 */

use dfs\common\components\console\Command;
use dfs\docdoc\models\RequestModel;

/**
 * Команда для работы с заявками
 *
 * Class RequestCommand
 */
class RequestCommand extends Command
{
	/**
	 * Если до 7 числа каждого месяца я не меняю статус на "отменена" или "подтверждена",
	 * то заявка автоматом уходит в отмену.
	 *
	 * @link https://docdoc.atlassian.net/browse/DD-914
	 */
	public function actionRejectPartners()
	{
		$from = mktime(0, 0, 0, date("m")-1, 1, date("Y"));
		$to = mktime(23, 59, 59, date("m"), 0, date("Y"));

		$criteria = RequestModel::model()
			->createdInInterval($from, $to)
			->byPartnerStatus(RequestModel::PARTNER_STATUS_HOLD)
			->getDbCriteria();

		$criteria->mergeWith(['join' => 'force index(crDate)']); //не использует этот индекс по умолчанию, а надо!
		$criteria->limit = $limit = 500;

		$total = RequestModel::model()->count($criteria);
		$log = "Всего заявок найдено: $total";

		$saved = 0;

		while($total > 0) {
			$requests = RequestModel::model()->findAll($criteria);

			foreach($requests as $request){
				$request->partner_status = RequestModel::PARTNER_STATUS_REJECT;

				if(!$request->save(true, ['partner_status'])){
					$this->log($request->req_id . ' ошибка сохранения: ' . var_export($request->getErrors(), true), CLogger::LEVEL_ERROR);
				} else {
					$saved++;
				}
			}

			$total -= $limit;
		}

		$this->log($log . ". Сохранено: $saved");
	}
}