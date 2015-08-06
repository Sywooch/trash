<?php

namespace dfs\docdoc\objects\google\requests;

use dfs\docdoc\objects\google\BigQuery;
use Yii;

/**
 * Модель для отчета статистики по заявкам
 *
 * Class Requests
 * @package dfs\docdoc\objects\google
 *
 * @property string $table
 */
class Requests extends BigQuery
{
	public $dataset = 'requests';

	public $baseTable = 'requests';

	public $fields = [
		['name' => 'id', 'type' => 'integer', 'mode' => 'nullable'],
		['name' => 'date', 'type' => 'string', 'mode' => 'nullable'],
		['name' => 'month', 'type' => 'string', 'mode' => 'nullable'],
		['name' => 'week', 'type' => 'integer', 'mode' => 'nullable'],
		['name' => 'hour', 'type' => 'integer', 'mode' => 'nullable'],
		['name' => 'city', 'type' => 'string', 'mode' => 'nullable'],
		['name' => 'source', 'type' => 'string', 'mode' => 'nullable'],
		['name' => 'type', 'type' => 'string', 'mode' => 'nullable'],
		['name' => 'req', 'type' => 'integer', 'mode' => 'nullable'],
		['name' => 'app', 'type' => 'integer', 'mode' => 'nullable'],
		['name' => 'visit', 'type' => 'integer', 'mode' => 'nullable'],
		['name' => 'deleted', 'type' => 'integer', 'mode' => 'nullable'],
		['name' => 'partner', 'type' => 'string', 'mode' => 'nullable'],
		['name' => 'clinic', 'type' => 'string', 'mode' => 'nullable'],
		['name' => 'reject_reason', 'type' => 'string', 'mode' => 'nullable'],
		['name' => 'cost', 'type' => 'integer', 'mode' => 'nullable'],
		['name' => 'income', 'type' => 'integer', 'mode' => 'nullable'],
		['name' => 'operator', 'type' => 'string', 'mode' => 'nullable'],
		['name' => 'clientid', 'type' => 'integer', 'mode' => 'nullable'],
		['name' => 'speed', 'type' => 'integer', 'mode' => 'nullable'],
		['name' => 'sector', 'type' => 'string', 'mode' => 'nullable'],
		['name' => 'doctor_name', 'type' => 'string', 'mode' => 'nullable'],
		['name' => 'date_admission', 'type' => 'string', 'mode' => 'nullable'],
	];

	/**
	 * Конструктор
	 *
	 * @param null $project
	 * @param null $date
	 */
	public function __construct($project = null, $date = null)
	{
		$this->baseTable .= '_' . date('m', strtotime($date)) . '_' . date('Y', strtotime($date));

		parent::__construct();
	}

	/**
	 * Получение имени класса по имени таблицы
	 *
	 * @param string $table
	 *
	 * @return string
	 */
	public static function getModelClassName($table)
	{
		$t = explode("_", $table);
		$className = "";
		$bqEnv = empty(Yii::app()->params['google_big_query']['env']) ? "" : Yii::app()->params['google_big_query']['env'];
		foreach ($t as $v) {
			if ($v != $bqEnv && !ctype_digit($v)) {
				$className .=  ucfirst($v);
			}
		}

		return $className;
	}

}
