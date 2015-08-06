<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 12.11.14
 * Time: 15:40
 */

namespace dfs\docdoc\objects\google\booking;

use dfs\docdoc\objects\google\BigQuery;

/**
 * Модель для отчета  со статистики по букингу
 *
 * Class Stats
 * @package dfs\docdoc\objects\google
 *
 * @property string $table
 */
class ScheduleReport extends BigQuery
{
	public $dataset = 'booking';

	public $baseTable = 'schedule_report';

} 
