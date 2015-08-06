<?php

namespace dfs\docdoc\front\controllers\pk;


/**
 * Class ToolsController
 *
 * @package dfs\docdoc\front\controllers\pk
 */
class ToolsController extends FrontController
{
	protected $_groups = [
		'Search' => [
			'title' => 'Поиск клиник',
			'desc' => true,
			'widgets' => [ 'DDWidgetSearch' ],
		],
		'Request' => [
			'title' => 'Форма заявки',
			'widgets' => [ 'DDWidgetRequest' ],
		],
		'Button' => [
			'title' => 'Кнопка записи',
			'widgets' => [ 'DDWidgetButton' ],
		],
		'WhiteLabel' => [
			'title' => 'WhiteLabel',
			'desc' => true,
			'widgets' => [ 'DDWidgetWhiteLabel' ],
		],
		'ClinicList' => [
			'title' => 'Список клиник',
			'desc' => true,
			'widgets' => [ 'DDWidgetClinicList' ],
		],
		'DoctorList' => [
			'title' => 'Список врачей',
			'desc' => true,
			'widgets' => [ 'DDWidgetDoctorList' ],
		],
	];

	protected $_widgets = [
		'DDWidgetSearch' => [
			'title' => '240х400 – с логотипом DocDoc',
			'params' => [
				'pid' => '',
				'container' => 'DDWidgetSearch',
				'widget' => 'Search',
				'action' => 'LoadWidget',
				'template' => 'SearchClinic_240x400',
				'city' => 'msk',
			],
		],
		'DDWidgetRequest' => [
			'title' => '728х90 – с логотипом DocDoc',
			'params' => [
				'pid' => '',
				'container' => 'DDWidgetRequest',
				'widget' => 'Request',
				'action' => 'LoadWidget',
				'template' => 'Request_728x90',
			],
		],
		'DDWidgetButton' => [
			'title' => 'Желтая кнопка – базовый вариант',
			'params' => [
				'pid' => '',
				'container' => 'DDWidgetButton',
				'widget' => 'Button',
				'action' => 'LoadWidget',
				'template' => 'Button_common',
			],
		],
		'DDWidgetWhiteLabel' => [
			'title' => 'Базовый вариант',
			'params' => [
				'pid' => '',
				'container' => 'DDWidgetWhiteLabel',
				'widget' => 'frame',
				'width' => '1000',
				'url' => '/',
			],
		],
		'DDWidgetClinicList' => [
			'title' => 'Ширина 700px',
			'params' => [
				'pid' => '',
				'container' => 'DDWidgetClinicList',
				'widget' => 'ClinicList',
				'action' => 'LoadWidget',
				'template' => 'ClinicList_700',
				'city' => 'msk',
				'station' => 'akademicheskaya',
				'spec' => 'akusherstvo',
				'page' => 1,
				'limit' => 5,
			],
		],
		'DDWidgetDoctorList' => [
			'title' => 'Ширина 700px',
			'params' => [
				'pid' => '',
				'container' => 'DDWidgetDoctorList',
				'widget' => 'DoctorList',
				'action' => 'LoadWidget',
				'template' => 'DoctorList_700',
				'city' => 'msk',
				'station' => 'akademicheskaya',
				'sector' => 'akusher',
				'limit' => 5,
				'order' => 'rating',
				'orderDirection' => 'DESC',
				'atHome' => 0,
			],
		],
	];


	/**
	 * Страница "Настройки"
	 */
	public function actionIndex()
	{
		\Yii::app()->clientScript->registerScriptFile("http://docdoc.ru/widget/js");
		$this->render('index', [
			'partner' => $this->_partner,
			'groups' => $this->_groups,
			'widgets' => $this->_widgets,
		]);
	}
}
