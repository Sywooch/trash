<?php

namespace dfs\docdoc\reports;

use dfs\docdoc\objects\google\requests\AnswerSpeed;

/**
 * Class AnswerSpeedReport
 * @package dfs\docdoc\reports
 */
class AnswerSpeedReport extends BigQueryReport
{
	/**
	 * @var string
	 */
	public $date;

	/**
	 * @param $date
	 */
	public function __construct($date)
	{
		$this->date = $date;
	}

	/**
	 * @return AnswerSpeed
	 */
	public function getBqModel()
	{
		return new AnswerSpeed(null, $this->date);
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

		$sql = '
			SELECT
				h1.id, h1.request_id, (h2.created - h1.created) AS duration,
				r.req_type, p.name, CONCAT(u.user_lname, " ", u.user_fname) AS operator
			FROM request_history h1
			LEFT JOIN request_history h2 ON h2.id = (
				SELECT id
				FROM request_history
				WHERE
					request_id = h1.request_id
					AND created > h1.created
					AND (text = "Оператор поднял трубку по заявке"
						OR text = "Изменен статус -> \'Принята\'" OR user_id <> 0)
				ORDER BY id
				LIMIT 1
  			)
			INNER JOIN request r ON r.req_id = h1.request_id
			LEFT JOIN partner p ON p.id = r.partner_id
			LEFT JOIN user u ON u.user_id = r.req_user_id
			WHERE
				r.req_created >= :startTime AND r.req_created <= :endTime
				AND (((h1.text = "Изменен статус -> \'Новая\'" OR h1.text = "Поступил повторный звонок.") AND r.req_type <> 3)
				OR (h1.text = "Изменен статус -> \'В обработке\'" AND r.req_type = 3))
			GROUP BY h1.created
		';

		$items = \Yii::app()->db
			->createCommand($sql)
			->bindValues([
				':startTime'    => $startTime,
				':endTime'      => $endTime,
			])
			->queryAll();

		foreach ($items as $item) {
			$row = [
				'id'            => $item['id'],
				'request_id'    => $item['request_id'],
				'duration'      => $item['duration'],
				'req_type'      => $item['req_type'],
				'partner'       => $item['name'],
				'operator'      => $item['operator'],
			];

			$this->addData($row, true);
		}
	}

}