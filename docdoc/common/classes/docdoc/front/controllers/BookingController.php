<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 03.09.14
 * Time: 20:25
 */

namespace dfs\docdoc\front\controllers;


use dfs\docdoc\extensions\Controller;
use dfs\docdoc\models\BookingModel;
use Yii;
use CHttpException;

/**
 * Class BookingController
 *
 * @package dfs\docdoc\front\controllers
 */
class BookingController extends Controller
{
	/**
	 * Грузит модель
	 *
	 * @param int $id
	 *
	 * @return BookingModel
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model = BookingModel::model()->findByPk($id);

		if ($model === null) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}

		return $model;
	}

	/**
	 * Подтверждение зарезервированной брони
	 *
	 * @param $id
	 *
	 * @throws CHttpException
	 */
	public function actionConfirm($id)
	{
		if (!\Yii::app()->request->isPostRequest) {
			throw  new CHttpException(400, 'Bad Request');
		}

		$model = $this->loadModel($id);

		$data = ['success' => false, 'errors' => []];

		try {
			if($model->confirm()){
				$data['success'] = true;
			} else {
				foreach ($model->getErrors() as $errors) {
					$data['errors'] = array_merge($data['errors'], $errors);
				}
			}

		} catch (\CException $e) {
			$data['errors'][] = $e->getMessage();
		}

		$this->renderJson($data);
	}
} 
