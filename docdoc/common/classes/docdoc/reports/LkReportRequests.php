<?php
namespace dfs\docdoc\reports;

use PHPExcel;
use PHPExcel_Style_Alignment;

/**
 * Class LkReportRequests
 *
 * отчет "список заявок"
 * @package dfs\docdoc\reports
 */
class LkReportRequests extends Report
{
	/**
	 * Параметры столбцов
	 *
	 * @var array
	 */
	protected $_fields = [
		'id'                  => ['title' => '#', 'width' => 10],
		'city_title'          => ['title' => 'Город', 'width' => 20],
		'created'             => ['title' => 'Дата обращения', 'width' => 15],
		'billing_date'        => ['title' => 'Дата попадания в биллинг', 'width' => 15],
		'date_admission'      => ['title' => 'Дата приема', 'width' => 15],
		'req_type'            => ['title' => 'Способ обращения', 'width' => 15],
		'kind'                => ['title' => 'Тип заявки', 'width' => 13],
		'client_name'         => ['title' => 'Пациент', 'width' => 30],
		'client_phone'        => ['title' => 'Телефон', 'width' => 20],
		'phones'              => ['title' => 'Телефон', 'width' => 30],
		'req_doctor_name'     => ['title' => 'Врач', 'width' => 30],
		'req_sector_name'     => ['title' => 'Специальность', 'width' => 17],
		'diagnostics_name'    => ['title' => 'Услуга', 'width' => 40],
		'speciality'          => ['title' => 'Специальность', 'width' => 40],
		'service'             => ['title' => 'Услуга', 'width' => 40],
		'cost'                => ['title' => 'Стоимость', 'width' => 10],
		'cost_title'          => ['title' => 'Стоимость', 'width' => 20],
		'state_title'         => ['title' => 'Статус', 'width' => 20],
		'partner_status'      => ['title' => 'Статус', 'width' => 20],
		'online'              => ['title' => 'Онлайн-запись', 'width' => 20],
		'billing_status_name' => ['title' => 'Статус в биллинге', 'width' => 20],
		'branch'              => ['title' => 'Филиал', 'width' => 20],
	];

	/**
	 * Заголовок отчёта
	 *
	 * @var string
	 */
	public $title = 'Заявки';

	/**
	 * обработка значения столбца state_title
	 *
	 * @param array[] $row
	 * @return string
	 */
	public function setColumnValue_state_title($row)
	{
		return isset($row['state']['title']) ? $row['state']['title'] : null;
	}
}
