<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 12.11.14
 * Time: 15:40
 */

namespace dfs\docdoc\objects\google\partners;

use dfs\docdoc\objects\google\BigQuery;

/**
 * модель для статистики виджетов
 *
 * Class BookingStats
 * @package dfs\docdoc\objects\google
 *
 * @property string $table
 */
class WidgetsStats extends BigQuery
{
	public $dataset = 'partners';

	public $baseTable = 'widgets_stats';
} 
