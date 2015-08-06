<?php

namespace dfs\docdoc\back\controllers;

use dfs\docdoc\reports\Report;
use dfs\docdoc\reports\RequestConversion;
use dfs\docdoc\reports\DoctorConversion;


/**
 * Аналитика
 */
class AnalyticsController extends BackendController
{
	/**
	 * Параметры полей таблицы
	 *
	 * @var array
	 */
	protected $_fields = [
		'doctor_name' => [
			'label' => 'Врач',
			'width' => 250,
		],
		'clinic_name' => [
			'label' => 'Клиника',
			'width' => 500,
		],
		'count_all' => [
			'label' => 'Кол-во заявок',
			'width' => 60,
		],
		'count_success' => [
			'label' => 'В биллинге',
			'width' => 60,
		],
		'conversion' => [
			'label' => 'Конверсия',
			'width' => 50,
		],
		'conversion_diff' => [
			'label' => 'Отклонение конверсии',
			'width' => 50,
		],
		'position' => [
			'label' => 'Место',
			'width' => 40,
		],
	];

	/**
	 * Параметры страниц с отчётами
	 *
	 * @var array
	 */
	protected $_params = [
		'conversion_doctors' => [
			'title'   => 'Врачи. Заявка - биллинг',
			'url'     => '/2.0/analytics/conversionRequests/by/doctors',
			'columns' => [ 'doctor_name', 'clinic_name', 'count_all', 'count_success', 'conversion', 'position' ],
			'view'    => 'index',
		],
		'conversion_clinics' => [
			'title'   => 'Клиники. Заявка - биллинг',
			'url'     => '/2.0/analytics/conversionRequests/by/clinics',
			'columns' => [ 'clinic_name', 'count_all', 'count_success', 'conversion', 'position' ],
			'view'    => 'index',
		],
		'conversion_visit' => [
			'title'   => 'Конверсия. Визиты - заявки',
			'url'     => '/2.0/analytics/conversionDoctors',
			'columns' => [ 'doctor_name', 'clinic_name', 'conversion', 'conversion_diff', 'position' ],
			'view'    => 'index',
		],
	];


	/**
	 * Главная страница
	 */
	public function actionIndex()
	{
		$this->actionConversionRequests();
	}

	/**
	 * Страница "Врачи. Заявка - биллинг"
	 *
	 * @param string $by
	 */
	public function actionConversionRequests($by = RequestConversion::SEPARATION_BY_DOCTORS)
	{
		$request = \Yii::app()->request;

		$report = new RequestConversion();

		$report
			->setSeparation($by)
			->setPeriod(
				$request->getQuery('dateFrom', date('Y-m-d', strtotime('-1 month'))),
				$request->getQuery('dateTill', date('Y-m-d'))
			)
			->setClinic($request->getQuery('clinicId'), $request->getQuery('withClinicChild'));

		$type = $report->getSeparation() === RequestConversion::SEPARATION_BY_CLINICS ? 'conversion_clinics' : 'conversion_doctors';

		$this->renderReport($report, $type);
	}

	/**
	 * Страница "Конверсия. Визиты - заявки"
	 */
	public function actionConversionDoctors()
	{
		$this->renderReport(new DoctorConversion(), 'conversion_visit');
	}

	/**
	 * Рендер отчёта (html, json или excel)
	 *
	 * @param Report $report
	 * @param string $type
	 */
	public function renderReport($report, $type)
	{
		$params = $this->_params[$type];

		$request = \Yii::app()->request;

		if ($request->isAjaxRequest) {
			$this->renderJSON([
				'data' => $report->getReportData(),
			]);
		}
		elseif ($request->getQuery('contentType') === 'xls') {
			$this->renderExcel($report->getExcelReport($params['columns']));
		} else {
			$vars = [
				'tabs' => $this->_params,
				'activeTab' => $type,
				'report' => $report,
				'tableConfig' => [
					'url'      => $params['url'],
					'dtDom'    => 'lfrtip',
					'fields'   => $this->_fields,
					'columns'  => $params['columns'],
					'rowsData' => $report->getReportData(),
				],
			];

			$this->render($params['view'], $vars);
		}
	}
}
