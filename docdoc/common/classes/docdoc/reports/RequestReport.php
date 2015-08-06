<?php

namespace dfs\docdoc\reports;

use dfs\docdoc\models\RequestModel;
use dfs\docdoc\objects\google\requests\RequestsDoctors;
use dfs\docdoc\objects\google\requests\RequestsDiag;
use dfs\docdoc\objects\Rejection;
use CActiveDataProvider;
use CDataProviderIterator;

/**
 * Class RequestReport
 * @package dfs\docdoc\reports
 */
class RequestReport extends BigQueryReport
{
	/**
	 * @var int
	 */
	public $kind;

	/**
	 * @var string
	 */
	public $date;

	public function __construct($kind, $date)
	{
		$this->kind = $kind;
		$this->date = $date;
	}

	public function getBqModel()
	{
		$model = null;
		if ($this->kind == RequestModel::KIND_DOCTOR) {
			$model =  new RequestsDoctors(null, $this->date);
		} elseif ($this->kind == RequestModel::KIND_DIAGNOSTICS) {
			$model = new RequestsDiag(null, $this->date);
		}

		return $model;
	}

	/**
	 * Генерация отчета
	 *
	 * @return array
	 */
	public function generate()
	{
		$month = date('m', strtotime($this->date));
		$year = date('Y', strtotime($this->date));
		$startTime = strtotime("{$year}-{$month}-1 00:00:00");
		$endTime = strtotime("last day of +0 month 23:59:59", $startTime);

		$speeds = $this->getSpeedOfResponse($startTime, $endTime);

		$dataProvider = new CActiveDataProvider(
			RequestModel::class,
			[
				'criteria' => array(
					'scopes' => [
						'createdInInterval' => [$startTime, $endTime],
						'byKind' => $this->kind,
					],
					'order' => 'req_id',
				),
			]
		);
		$requestsIterator = new CDataProviderIterator($dataProvider, 1000);

		foreach ($requestsIterator as $r) {
			$row = [
				'id'    => $r->req_id,
				'date'  => date('d.n.Y', $r->req_created),
				'month' => date('F', $r->req_created),
				'week'  => (int)date('W', $r->req_created),
				'hour'  => (int)date('H', $r->req_created),
				'city'  => !is_null($r->city) ? $r->city->title : '',
				'source' => $r->getSourceName(),
				'type' => $r->getTypeName(),
				'req' => 1,
				'app' => (int)!is_null($r->date_admission),
				'visit' => (!is_null($r->date_admission) && $r->req_status == RequestModel::STATUS_CAME) ? 1 : 0,
				'deleted' => $r->req_status == RequestModel::STATUS_REMOVED ? 1 : 0,
				'partner' => !is_null($r->partner) ? $r->partner->name : 'dd.docdoc.ru',
				'clinic'    => !is_null($r->clinic) ? $r->clinic->getParentClinicName() : '',
				'reject_reason' => Rejection::getReason($r->reject_reason),
				'cost' => $r->partner_status == 1 ? round($r->partner_cost) : 0,
				'income' => $r->isInBilling() ? $r->request_cost : 0,
				'operator' => !is_null($r->operator) ? $r->operator->getFullName() : '',
				'clientid' => $r->clientId,
				'speed'=> isset($speeds[$r->req_id]) ? $speeds[$r->req_id] : null,
				'sector' => $r->getServiceName(),
				'doctor_name' => $this->getDoctorName($r),
				'date_admission' => !is_null($r->date_admission) ? date('d.m.Y', $r->date_admission) : null,
			];

			$this->addData($row, true);
			echo "#" . $r->req_id . PHP_EOL;
		}
	}

	/**
	 * Получение значения для поля doctor_name
	 *
	 * @param RequestModel $request
	 *
	 * @return string
	 */
	public function getDoctorName(RequestModel $request)
	{
		$name = '';

		if ($request->kind == RequestModel::KIND_DOCTOR) {
			$name = !is_null($request->doctor) ? $request->doctor->name : '';
		} elseif ($request->kind == RequestModel::KIND_DIAGNOSTICS) {
			$name = !is_null($request->diagnostics) && $request->diagnostics->parent_id <> 0
				? $request->diagnostics->getFullName()
				: '';
		}

		return $name;
	}

	/**
	 * Получение скоростей ответов по заявкам
	 *
	 * @param integer $startTime
	 * @param integer $endTime
	 *
	 * @return array
	 */
	public function getSpeedOfResponse($startTime, $endTime)
	{
		$sql = '
			SELECT r.req_id, (UNIX_TIMESTAMP(hr2.created) - UNIX_TIMESTAMP(hr.created)) AS speed FROM request r
			LEFT JOIN request_history hr ON hr.id = (
				SELECT hri1.id FROM request_history hri1
				WHERE hri1.request_id = r.req_id
				ORDER BY hri1.id
				LIMIT 1
			)
			LEFT JOIN request_history hr2 ON hr2.id = (
				SELECT hri2.id FROM request_history hri2
				WHERE hri2.request_id = r.req_id
					AND (
						(r.req_type = 2 AND hri2.text = "Изменен статус -> \'Принята\'")
						OR
						(hri2.user_id <> 0 AND r.req_type = 3)
						OR
						(r.req_type <> 3 AND r.req_type <> 2 AND hri2.text LIKE "Звонок клиенту (исходящий)%")
					)
				ORDER BY hri2.id
				LIMIT 1
			)
			WHERE r.kind = :kind AND r.req_created >= :startTime AND r.req_created <= :endTime';

		$result = \Yii::app()->db
			->createCommand($sql)
			->bindValues([
				':kind'         => $this->kind,
				':startTime'    => $startTime,
				':endTime'      => $endTime,
			])
			->queryAll();

		$data = [];
		foreach ($result as $item) {
			$data[$item['req_id']] = $item['speed'];
		}

		return $data;
	}

}