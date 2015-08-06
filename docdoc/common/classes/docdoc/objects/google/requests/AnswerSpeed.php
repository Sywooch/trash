<?php

namespace dfs\docdoc\objects\google\requests;

use dfs\docdoc\objects\google\BigQuery;
use Yii;

/**
 * Модель для отчета скорости ответа по заявкам
 *
 * Class AnswerSpeed
 * @package dfs\docdoc\objects\google
 *
 * @property string $table
 */
class AnswerSpeed extends BigQuery
{
	/**
	 * @var string
	 */
	public $dataset = 'requests';

	/**
	 * @var string
	 */
	public $baseTable = 'answer_speed';

	/**
	 * @var array
	 */
	public $fields = [
		['name' => 'id', 'type' => 'integer', 'mode' => 'nullable'],
		['name' => 'request_id', 'type' => 'integer', 'mode' => 'nullable'],
		['name' => 'duration', 'type' => 'integer', 'mode' => 'nullable'],
		['name' => 'req_type', 'type' => 'integer', 'mode' => 'nullable'],
		['name' => 'partner', 'type' => 'string', 'mode' => 'nullable'],
		['name' => 'operator', 'type' => 'string', 'mode' => 'nullable'],
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

}
