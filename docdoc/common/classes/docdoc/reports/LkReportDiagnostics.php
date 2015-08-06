<?php

namespace dfs\docdoc\reports;

/**
 * Отчет "список диагностик"
 *
 * @package dfs\docdoc\reports
 */
class LkReportDiagnostics extends Report
{
	/**
	 * Параметры столбцов
	 *
	 * @var array
	 */
	protected $_fields = [
		'clinic_name'          => ['title' => 'Филиал', 'width' => 25],
		'diagnostic_name'      => ['title' => 'Исследование', 'width' => 35],
		'price'                => ['title' => 'Цена', 'width' => 10],
		'special_price'        => ['title' => 'Спеццена', 'width' => 10],
	];

	/**
	 * Заголовок отчёта
	 *
	 * @var string
	 */
	public $title = 'Диагностики';
}
