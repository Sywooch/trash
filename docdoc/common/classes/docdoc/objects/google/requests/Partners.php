<?php

namespace dfs\docdoc\objects\google\requests;

use dfs\docdoc\objects\google\BigQuery;
use Yii;

/**
 * Модель для статистики по партнерам
 *
 * Class Partners
 * @package dfs\docdoc\objects\google
 *
 * @property string $table
 */
class Partners extends BigQuery
{
	public $dataset = 'requests';

	public $baseTable = 'partners';

	public $fields = [
		['name' => 'id', 'type' => 'integer', 'mode' => 'nullable'],
		['name' => 'name', 'type' => 'string', 'mode' => 'nullable'],
	];

}
