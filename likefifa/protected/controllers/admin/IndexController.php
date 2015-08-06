<?php

use likefifa\models\AdminModel;
use likefifa\models\DevEvent;
use likefifa\models\forms\BoIndexReport;
use likefifa\models\forms\PaymentsOperationsAdminFilter;

/**
 * Файл класса IndexController
 *
 * Контроллер для главной страницы в БО
 *
 * @author  Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @link    https://docdoc.megaplan.ru/task/1002402/card/
 * @package admin.controllers
 */
class IndexController extends BackendController
{
	public function accessRules()
	{
		return CMap::mergeArray(
			[
				[
					'allow',
					'actions' => [
						'mainChart',
					],
					'users'   => AdminModel::model()->getAdminsForThisController($this->id),
				]
			],
			parent::accessRules()
		);
	}

	/**
	 * Главная страница
	 *
	 * @return void
	 */
	public function actionIndex()
	{
		if (AdminModel::getModel()->isOperator()) {
			$this->redirect("/admin/appointment/");
		}

		$amountSum = PaymentsOperationsAdminFilter::getRealAmount();

		$mastersRegData =
			Yii::app()->db->createCommand(
				"select count(*) from lf_master where date(created) > :created group by date(created)"
			)->bindValues([':created' => date('Y-m-d', strtotime('-30 days'))])->queryColumn();
		$mastersRegData = array_slice($mastersRegData, -13);

		$appointmentsData =
			Yii::app()->db->createCommand(
				"select count(*) from lf_appointment where date(created) > :created group by date(created)"
			)->bindValues([':created' => date('Y-m-d', strtotime('-30 days'))])->queryColumn();
		$appointmentsData = array_slice($appointmentsData, -13);

		$opinionsData =
			Yii::app()->db->createCommand(
				"select count(*) from lf_opinion where date(from_unixtime(created)) > :created group by date(from_unixtime(created))"
			)->bindValues([':created' => date('Y-m-d', strtotime('-30 days'))])->queryColumn();
		$opinionsData = array_slice($opinionsData, -13);

		$report = new BoIndexReport();
		if(($attributes = Yii::app()->request->getQuery(CHtml::modelName($report))) != null) {
			$report->attributes = $attributes;
			$this->performAjaxValidation($report);
			$report->validate();
		}

		$this->render('index', compact('amountSum', 'mastersRegData', 'appointmentsData', 'opinionsData', 'report'));
	}

	/**
	 * Выводит данные для графика показателей
	 */
	public function actionMainChart()
	{
		$mastersData = Yii::app()->db->createCommand()
			->select([new CDbExpression('DATE(created) AS created'), new CDbExpression('COUNT(id) AS count')])
			->from(LfMaster::model()->tableName())
			->where('created > "0000-00-00"')
			->group(new CDbExpression('DATE(created)'))
			->queryAll(false);
		$masters = $this->_convertChartData($mastersData);

		$salonsData = Yii::app()->db->createCommand()
			->select([new CDbExpression('DATE(created) AS created'), new CDbExpression('COUNT(id) AS count')])
			->from(LfSalon::model()->tableName())
			->where('created > "0000-00-00"')
			->group(new CDbExpression('DATE(created)'))
			->queryAll(false);
		$salons = $this->_convertChartData($salonsData);

		$appointmentData = Yii::app()->db->createCommand()
			->select([new CDbExpression('DATE(created) AS created'), new CDbExpression('COUNT(id) AS count')])
			->from(LfAppointment::model()->tableName())
			->where('created > "0000-00-00"')
			->group(new CDbExpression('DATE(created)'))
			->queryAll(false);
		$appointments = $this->_convertChartData($appointmentData);

		$events = Yii::app()->db->createCommand()
			->select(
				[
					new CDbExpression('DATE(date) AS x'),
					new CDbExpression('value AS text'),
					new CDbExpression('"e" AS title')
				]
			)
			->from(DevEvent::model()->tableName())
			->queryAll();
		foreach ($events as &$e) {
			$e['x'] = strtotime($e['x']) * 1000;
		}

		echo CJSON::encode(compact('masters', 'salons', 'appointments', 'events'));
	}

	/**
	 * Конвертирует данные для графика
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	private function _convertChartData(array $data)
	{
		$newData = [];
		foreach ($data as $d) {
			$newData[] = [strtotime(array_values($d)[0]) * 1000, (int)array_values($d)[1]];
		}

		return $newData;
	}
}