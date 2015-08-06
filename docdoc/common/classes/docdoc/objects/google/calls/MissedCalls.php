<?php

namespace dfs\docdoc\objects\google\calls;

use dfs\docdoc\objects\google\BigQuery;
use Yii;

/**
 * Модель для статистики по неотвеченным звонкам
 *
 * Class MissedCalls
 * @package dfs\docdoc\objects\google
 *
 * @property string $table
 */
class MissedCalls extends BigQuery
{
	/**
	 * @var string
	 */
	public $dataset = 'calls';

	/**
	 * @var string
	 */
	public $baseTable = 'missed_calls';

	/**
	 * @var array
	 */
	public $fields = [
		['name' => 'date', 'type' => 'string', 'mode' => 'nullable'],
		['name' => 'clinic_id', 'type' => 'integer', 'mode' => 'nullable'],
		['name' => 'clinic_name', 'type' => 'string', 'mode' => 'nullable'],
		['name' => 'clinic_phone', 'type' => 'string', 'mode' => 'nullable'],
		['name' => 'calls', 'type' => 'integer', 'mode' => 'nullable'],
		['name' => 'unique', 'type' => 'integer', 'mode' => 'nullable'],
		['name' => 'success', 'type' => 'integer', 'mode' => 'nullable'],
		['name' => 'failed', 'type' => 'integer', 'mode' => 'nullable'],
		['name' => 'percentage_of_failed', 'type' => 'integer', 'mode' => 'nullable'],
	];

}
