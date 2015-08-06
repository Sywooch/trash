<?php

namespace dfs\docdoc\objects\google\users;

use dfs\docdoc\objects\google\BigQuery;

/**
 * Модель для клиентов
 *
 * Class Client
 * @package dfs\docdoc\objects\google
 *
 * @property string $table
 */
class User extends BigQuery
{
	public $dataset = 'users';

	public $baseTable = 'user';

	public $fields = [
		['name' => 'userId', 'type' => 'string', 'mode' => 'required'],
		['name' => 'ip', 'type' => 'string', 'mode' => 'nullable'],
		['name' => 'region', 'type' => 'string', 'mode' => 'nullable'],
		['name' => 'browser', 'type' => 'string', 'mode' => 'nullable'],
		['name' => 'platform', 'type' => 'string', 'mode' => 'nullable'],
	];

	/**
	 * первичным ключем у нас является ga_id
	 *
	 * @return null|string
	 */
	public function getPrimaryKey()
	{
		return 'userId';
	}
}
