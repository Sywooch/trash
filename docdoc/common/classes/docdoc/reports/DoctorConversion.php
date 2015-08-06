<?php

namespace dfs\docdoc\reports;

use dfs\docdoc\models\DoctorClinicModel;
use dfs\docdoc\models\DoctorModel;


class DoctorConversion extends Report
{
	/**
	 * Параметры столбцов
	 *
	 * @var array
	 */
	protected $_fields = [
		'doctor_name'     => ['title' => 'Врач', 'width' => 30],
		'clinic_name'     => ['title' => 'Клиника', 'width' => 50],
		'count_all'       => ['title' => 'Кол-во заявок', 'width' => 10],
		'conversion'      => ['title' => 'Конверсия', 'width' => 10],
		'conversion_diff' => ['title' => 'Отклонение конверсии', 'width' => 10],
		'position'        => ['title' => 'Место', 'width' => 10],
	];


	/**
	 * Запуск формирования отчета
	 */
	public function execute()
	{
		$data = $this->queryDoctorsConversion();

		$conversionAvg = floatval(DoctorModel::model()->getAvgConversion()) * 100;

		$n = 1;
		foreach ($data as &$item) {
			$item['conversion_diff'] = intval($item['conversion'] - $conversionAvg);
			$item['conversion'] = intval($item['conversion']);
			$item['position'] = $n++;
		}

		$this->_reportData = $data;
	}

	/**
	 * Подзапрос для подсчета количества заявок
	 *
	 * @return array
	 */
	private function queryDoctorsConversion()
	{
		$c = new \CDbCriteria();

		$select = [
			'id'             => 't.id as id',
			'doctor_name'    => 't.name as doctor_name',
			'clinic_id'      => 'clinic.id as clinic_id',
			'clinic_name'    => 'clinic.name as clinic_name',
			'conversion'     => '(t.conversion * 100) as conversion',
		];

		$join = [
			'LEFT JOIN doctor_4_clinic as dc ON (t.id = dc.doctor_id and dc.type = ' . DoctorClinicModel::TYPE_DOCTOR . ')',
			'LEFT JOIN clinic ON (dc.clinic_id = clinic.id)',
		];

		$c->select = $select;
		$c->join = implode("\n", $join);
		$c->condition = 't.conversion IS NOT NULL';
		$c->order = 't.conversion DESC';

		$model = new DoctorModel();
		$model->applyScopes($c);

		return $model->getCommandBuilder()
			->createFindCommand($model->getTableSchema(), $c, $model->getTableAlias())
			->queryAll();
	}
}
