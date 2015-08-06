<?php

namespace dfs\docdoc\objects\google\calls;

use dfs\docdoc\objects\google\BigQuery;
use Yii;

/**
 * Модель для таблицы по неотвеченным звонкам
 *
 * Class MissedCallsRaw
 * @package dfs\docdoc\objects\google
 *
 * @property string $table
 */
class MissedCallsRaw extends BigQuery
{
	/**
	 * @var string
	 */
	public $dataset = 'calls';

	/**
	 * @var string
	 */
	public $baseTable = 'missed_calls_raw';

	/**
	 * @var array
	 */
	public $fields = [
		['name' => 'direction', 'type' => 'string', 'mode' => 'nullable'],
		['name' => 'date', 'type' => 'string', 'mode' => 'nullable'],
		['name' => 'caller', 'type' => 'string', 'mode' => 'nullable'],
		['name' => 'phone_number', 'type' => 'string', 'mode' => 'nullable'],
		['name' => 'called_number', 'type' => 'string', 'mode' => 'nullable'],
		['name' => 'contact_name', 'type' => 'string', 'mode' => 'nullable'],
		['name' => 'category_name', 'type' => 'string', 'mode' => 'nullable'],
		['name' => 'duration', 'type' => 'string', 'mode' => 'nullable'],
		['name' => 'response_duration', 'type' => 'string', 'mode' => 'nullable'],
		['name' => 'tariff_duration', 'type' => 'string', 'mode' => 'nullable'],
		['name' => 'scenario_name', 'type' => 'string', 'mode' => 'nullable'],
		['name' => 'cost', 'type' => 'string', 'mode' => 'nullable'],
		['name' => 'clinic_id', 'type' => 'integer', 'mode' => 'nullable'],
		['name' => 'clinic_name', 'type' => 'string', 'mode' => 'nullable'],
		['name' => 'partner_id', 'type' => 'integer', 'mode' => 'nullable'],
		['name' => 'partner_name', 'type' => 'string', 'mode' => 'nullable'],
	];

}
