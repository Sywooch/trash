<?php

namespace dfs\docdoc\reports;

use dfs\docdoc\models\RequestModel;


class RequestConversion extends Report
{
	const SEPARATION_BY_DOCTORS = 'doctors';
	const SEPARATION_BY_CLINICS = 'clinics';

	/**
	 * Параметры столбцов
	 *
	 * @var array
	 */
	protected $_fields = [
		'doctor_name'   => ['title' => 'Врач', 'width' => 30],
		'clinic_name'   => ['title' => 'Клиника', 'width' => 50],
		'count_all'     => ['title' => 'Кол-во заявок', 'width' => 10],
		'count_success' => ['title' => 'В биллинге', 'width' => 10],
		'conversion'    => ['title' => 'Конверсия', 'width' => 10],
		'position'      => ['title' => 'Место', 'width' => 10],
	];

	/**
	 * Разделение по врачам или клиникам
	 *
	 * @var string
	 */
	protected $_separation = self::SEPARATION_BY_DOCTORS;

	/**
	 * Начальная дата создания заявки
	 *
	 * @var int
	 */
	protected $_dateBegin = 0;

	/**
	 * Конечная дата создания заявки
	 *
	 * @var int
	 */
	protected $_dateEnd = 0;

	/**
	 * Филитрация по клинике
	 *
	 * @var int
	 */
	protected $_clinicId = null;

	/**
	 * Учитывать филиалы клиники
	 *
	 * @var bool
	 */
	protected $_withClinicChild = false;


	/**
	 * @return string
	 */
	public function getSeparation()
	{
		return $this->_separation;
	}

	/**
	 * @return string
	 *
	 */
	public function getPeriodBegin()
	{
		return $this->_dateBegin ? date('d.m.Y', $this->_dateBegin) : null;
	}

	/**
	 * @return string
	 *
	 */
	public function getPeriodEnd()
	{
		return $this->_dateEnd ? date('d.m.Y', $this->_dateEnd) : null;
	}


	/**
	 * Разделение отчёта по врачам или клиникам
	 *
	 * @param $separation
	 *
	 * @return $this
	 */
	public function setSeparation($separation)
	{
		if ($separation) {
			$this->_separation = $separation;
		}

		return $this;
	}

	/**
	 * Установка периода выборки заявок
	 * 
	 * @param int $dateBegin
	 * @param int $dateEnd
	 *
	 * @return $this
	 */
	public function setPeriod($dateBegin, $dateEnd)
	{
		if ($dateBegin) {
			$current = time();
			$this->_dateBegin = $this->convertDate($dateBegin, $current);
			$this->_dateEnd = $this->convertDate($dateEnd, $current);
		}

		return $this;
	}

	/**
	 * Фильтр по клинике
	 *
	 * @param int  $clinicId
	 * @param bool $withChild
	 *
	 * @return $this
	 */
	public function setClinic($clinicId, $withChild = false)
	{
		if ($clinicId) {
			$this->_clinicId = $clinicId;
			$this->_withClinicChild = $withChild;
		}

		return $this;
	}

	/**
	 * Запуск формирования отчета
	 *
	 * @throws \CException
	 */
	public function execute()
	{
		$data = [];

		foreach ($this->queryRequestCount() as $row) {
			$id = $row['id'];
			if (!isset($data[$id])) {
				$data[$id] = $row;
				$data[$id]['count_all'] = 0;
				$data[$id]['count_success'] = 0;
				$data[$id]['count_fail'] = 0;
				unset($data[$id]['billing_status']);
			}

			$data[$id]['count_all'] += $row['request_count'];

			switch ($row['billing_status']) {
				case RequestModel::BILLING_STATUS_NO:
				case RequestModel::BILLING_STATUS_REFUSED:
					$data[$id]['count_fail'] += $row['request_count'];
					break;
				case RequestModel::BILLING_STATUS_YES:
				case RequestModel::BILLING_STATUS_PAID:
					$data[$id]['count_success'] += $row['request_count'];
					break;
			}
		}

		foreach ($data as &$item) {
			$item['conversion'] = intval($item['count_success'] / $item['count_all'] * 100);
		}

		usort($data, function($a, $b) {
				if ($a['conversion'] < $b['conversion']) {
					return true;
				}
				if ($a['conversion'] == $b['conversion']) {
					return $a['count_success'] < $b['count_success'];
				}
				return false;
			});

		$n = 1;
		foreach ($data as &$item) {
			$item['position'] = $n++;
		}

		$this->_reportData = array_values($data);
	}

	/**
	 * Подзапрос для подсчета количества заявок
	 *
	 * @return array
	 */
	private function queryRequestCount()
	{
		$c = new \CDbCriteria();

		$select = [
			'id'             => 'clinic.id as id',
			'clinic_id'      => 'clinic.id as clinic_id',
			'clinic_name'    => 'IF(clinic.short_name, clinic.short_name, clinic.name) as clinic_name',
			'request_count'  => 'COUNT(t.req_id) as request_count',
			'billing_status' => 't.billing_status as billing_status',
		];

		$join = [
			'INNER JOIN clinic ON (t.clinic_id = clinic.id)',
		];

		$group = [];

		if ($this->_separation == self::SEPARATION_BY_CLINICS) {
			$group[] = 't.clinic_id';
		} else {
			$select['id'] = 'doctor.id as id';
			$select['doctor_id'] = 'doctor.id as doctor_id';
			$select['doctor_name'] = 'doctor.name as doctor_name';

			$join[] = 'INNER JOIN doctor ON (t.req_doctor_id = doctor.id)';

			$group[] = 't.req_doctor_id';
		}

		$group[] = 't.billing_status';

		if ($this->_dateBegin && $this->_dateEnd) {
			$c->scopes['createdInInterval'] = [ $this->_dateBegin, $this->_dateEnd + 86400 ];
		}

		if ($this->_clinicId) {
			$c->condition = 'clinic.id = :clinic_id' . ($this->_withClinicChild ? ' OR clinic.parent_clinic_id = :clinic_id' : '');
			$c->params['clinic_id'] = $this->_clinicId;
		}

		$c->select = $select;
		$c->join = implode("\n", $join);
		$c->group = implode(', ', $group);

		$model = new RequestModel();
		$model->applyScopes($c);

		return $model->getCommandBuilder()
			->createFindCommand($model->getTableSchema(), $c, $model->getTableAlias())
			->queryAll();
	}
}
