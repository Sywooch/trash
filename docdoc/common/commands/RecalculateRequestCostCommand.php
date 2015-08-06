<?php

use dfs\common\components\console\Command;
use dfs\docdoc\models\RequestModel;
use dfs\docdoc\objects\Rejection;
use dfs\docdoc\models\ClinicContractModel;
use dfs\docdoc\models\ClinicBillingModel;

/**
 * Обновление стоимости заявок
 *
 * Примеры команд:
 *    ./yiic recalculateRequestCost came
 *    ./yiic recalculateRequestCost monthRecalculate
 */
class RecalculateRequestCostCommand extends Command
{
	const ITERATION_REQUEST_LIMIT = 100;

	/**
	 * Перевод заявок в статус дошёл и установка стоимости
	 *
	 * @param int $month
	 */
	public function actionCame($month = 2)
	{
		$this->log('----- Перевод заявок в статус дошёл и установка стоимости -----');

		$model = RequestModel::model()
			->inBilling(RequestModel::BILLING_STATUS_NO)
			->inBillingState(RequestModel::BILLING_STATE_RECORD)
			->betweenDateAdmission(strtotime('-' . $month . ' month'));

		$clone = clone $model;
		$count = $clone->count();
		$pages = ceil($count / 1000);

		$n = 0;
		for ($j = 0; $j <= $pages; $j++) {
			$clone =  clone $model;
			foreach ($clone->findAll([ 'limit' => 1000, 'order' => 'req_id DESC', 'offset' => $j * 1000 ]) as $request) {
				$result = $request->saveBillingState(RequestModel::BILLING_STATE_CAME);

				$result = $result && $request->billing_status != RequestModel::BILLING_STATUS_NO && $request->request_cost;

				if ($request->hasErrors()) {
					foreach ($request->getErrors() as $field => $errors) {
						$this->log($request->req_id . ' error field ' . $field . ': ' . implode(', ', $errors));
					}
				}

				$this->log($request->req_id . ' - request_cost = ' . $request->request_cost . '   ' . ($result ? 'OK' : 'ERROR'));
				$n++;
			}
		}
		$this->log('Всего обработано заявок: ' . $n);
	}

	/**
	 * Перевод заявок-дублей в статус отменена клиникой
	 *
	 * @param string $from дата Y-m-d
	 * @param string $to дата Y-m-d
	 */
	public function actionRefused($from = null, $to = null)
	{
		$this->log('----- Перевод заявок в статус отменена клиникой -----');
		$from = is_null($from) ? strtotime("-1 day") : strtotime($from);
		$to   = is_null($from) ? null : strtotime($to);

		$items = RequestModel::model()
			->inBilling([RequestModel::BILLING_STATUS_NO, RequestModel::BILLING_STATUS_YES])
			->betweenDateAdmission($from, $to)
			->findAll();

		$n = 0;

		foreach ($items as $request) {

			if ($firstRequest = $request->isRepeated()) {

				$duplicate = $request;
				if (
					$request->isInBilling()
					&& $request->request_cost > $firstRequest->request_cost
				) {
					$duplicate = $firstRequest;
					$firstRequest = $request;
				}

				$result = $duplicate->saveBillingState(RequestModel::BILLING_STATE_REFUSED, [
					'reject_reason' => Rejection::REASON_REPEAT_REQUEST
				]);

				$duplicate->addHistory("Заявка-дубль для заявки #{$firstRequest->req_id}. Убираем заявку из биллинга");

				$this->log($duplicate->req_id . ' - billing_status = ' . $duplicate->billing_status . '   ' . ($result ? 'OK' : 'ERROR'));

				$n++;
			}
		}
		$this->log('Всего обработано заявок: ' . $n);
	}

	/**
	 * Пересчёт стоимости заявок за последние 2 месяца
	 *
	 * @param int $month
	 */
	public function actionMonthRecalculate($month = 2)
	{
		$this->log('----- Пересчёт стоимости заявок за последние 2 месяца -----');

		$lastId = 0;
		$countAll = 0;

		while (true) {
			$model = new RequestModel();
			$model
				->inBilling(RequestModel::BILLING_STATUS_YES)
				->createdInInterval(strtotime('-' . $month . ' month'));

			if ($lastId > 0) {
				$model->latest($lastId);
			}

			$items = $model->findAll([
					'order' => 'req_id ASC',
					'limit' => self::ITERATION_REQUEST_LIMIT,
				]);

			foreach ($items as $request) {
				$lastId = $request->req_id;
				$result = $request->saveRequestCost();
				$this->log($request->req_id . ' - request_cost = ' . $request->request_cost . '   ' . $result);
			}
			$count = count($items);
			$countAll += $count;
			if ($count < self::ITERATION_REQUEST_LIMIT) {
				break;
			}
			echo 'Обработано: ' . $countAll . PHP_EOL;
		}

		$this->log('Всего обработано заявок: ' . $countAll);
	}

	/**
	 * Установка даты биллинга заявкам
	 *
	 * @param int $lastId
	 */
	public function actionBillingDate($lastId = 0)
	{
		$this->log('----- Установка даты биллинга заявкам -----');

		$countAll = 0;

		if (!$lastId) {
			$last = RequestModel::model()->find([
				'order' => 'req_id DESC',
				'limit' => 1,
			]);
			$lastId = $last->req_id + 1;
		}

		while (true) {
			$model = new RequestModel();

			$items = $model->findAll([
				'condition' => 'req_id < ' . $lastId,
				'order' => 'req_id DESC',
				'limit' => self::ITERATION_REQUEST_LIMIT,
				'params' => [ 'lastId' => $lastId ],
			]);

			foreach ($items as $request) {
				$lastId = $request->req_id;
				$result = $request->saveRequestCost();
				$this->log($request->req_id . ' - date_billing = ' . $request->date_billing . '   ' . $result);
			}
			$count = count($items);
			$countAll += $count;
			if ($count < self::ITERATION_REQUEST_LIMIT) {
				break;
			}
			echo 'Обработано: ' . $countAll . PHP_EOL;
		}

		$this->log('Всего обработано заявок: ' . $countAll);
	}


	/**
	 * Установка даты биллинга заявкам
	 *
	 * @param string $date
	 */
	public function actionClinicBilling($date = null)
	{
		$this->log('----- Подсчет биллинга клиник  -----');

		$contracts = ClinicContractModel::model()
			->with(
				[
					'contract' => [
						'joinType' => 'INNER JOIN',
						'scopes' => [
							'realContracts' => []
						]
					]
				])
			->findAll();

		if (empty($date)) {
			//считаем за предыдущий день
			$date = date('Y-m-d', time() - 24 * 3600);
		}

		$dateBilling = (new \DateTime($date))->modify('first day of this month' )->format('Y-m-d');

		foreach ($contracts as $c) {

			$billing = $c->saveBillingByPeriod($dateBilling);

			$this->log('Обработка клиники: ' . $billing->clinic_id);
		}

	}
}
