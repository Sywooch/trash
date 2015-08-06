<?php

namespace dfs\docdoc\front\controllers\lk;

use dfs\docdoc\models\DoctorModel;
use dfs\docdoc\models\SectorModel;

/**
 * Class ReportsController
 *
 * @package dfs\docdoc\front\controllers\lk
 */
class ReportsController extends FrontController
{
	/**
	 * Главная страница
	 */
	public function actionIndex()
	{
		$this->render('index');
	}

	/**
	 * Поиск врачей по имени
	 */
	public function getDoctorList()
	{
		$data = [];
		$term = \Yii::app()->request->getQuery('term');

		if ($term) {
			$criteria = new \CDbCriteria;
			$criteria->condition = 't.name LIKE :name';
			$criteria->params = [ 'name' => "%$term%" ];
			$criteria->order = 't.name';
			$criteria->limit = 100;

			$doctors = DoctorModel::model()
				->inClinics([$this->_clinic->id])
				->findAll($criteria);

			foreach ($doctors as $doctor) {
				$data[] = $doctor->name;
			}
		}

		$this->renderJSON($data);
	}

	/**
	 * Поиск специальности по названию
	 */
	public function getSpecList()
	{
		$data = [];
		$term = \Yii::app()->request->getQuery('term');

		if ($term) {
			$criteria = new \CDbCriteria;
			$criteria->condition = 't.name LIKE :name';
			$criteria->params = [ 'name' => "%$term%" ];
			$criteria->order = 't.name';
			$criteria->limit = 100;

			$sectors = SectorModel::model()->findAll($criteria);

			foreach ($sectors as $sector) {
				$data[] = $sector->name;
			}
		}

		$this->renderJSON($data);
	}
}
