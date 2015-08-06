<?php
namespace dfs\docdoc\reports;

/**
 * Class LkReportRequests
 *
 * отчет "список заявок"
 * @package dfs\docdoc\reports
 */
class LkReportDoctors extends Report
{
	/**
	 * Параметры столбцов
	 *
	 * @var array
	 */
	protected $_fields = [
		'id'                   => ['title' => '#', 'width' => 5],
		'name'                 => ['title' => 'Врач', 'width' => 35],
		'specialty'            => ['title' => 'Специальность', 'width' => 25],
		'price'                => ['title' => 'Цена', 'width' => 10],
		'departure_title'      => ['title' => 'Выезд на дом', 'width' => 8],
		'kids_reception_title' => ['title' => 'Детский врач', 'width' => 8],
		'status_title'         => ['title' => 'Статус', 'width' => 10],
	];

	/**
	 * Заголовок отчёта
	 *
	 * @var string
	 */
	public $title = 'Врачи';


}
