<?php

namespace dfs\docdoc\back\controllers;

use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\models\ApiClinicModel;
use CHttpException;
use CHtml;
use Yii;
use Exception;

/**
 * Файл класса ApiClinicController.
 *
 * Контроллер интерфейса для таблицы api_clinic
 *
 * @author  Mikhail Vasilyev <mvasilyev@docdoc.ru>
 * @link    https://docdoc.atlassian.net/browse/DD-69
 * @package dfs.docdoc.back.controllers
 */
class ApiClinicController extends BackendController
{

	/**
	 * Создает модель.
	 *
	 * @throws CHttpException
	 */
	public function actionCreate()
	{
		throw new CHttpException(403, "Запрещено добавлять новую запись");
	}

	/**
	 * Обновлеет модель.
	 *
	 * @param int $id идентификатор модели
	 *
	 * @return void
	 */
	public function actionUpdate($id)
	{
		$model = $this->loadModel($id);
		$clinic = new ClinicModel;

		$clinicId = Yii::app()->request->getPost("clinicId");

		if ($clinicId) {
			$transaction = Yii::app()->db->beginTransaction();
			try {
				$clinic = ClinicModel::model()->findByPk($clinicId);
				if ($clinic) {
					$clinic->external_id = $model->id;
					if ($model->save() && $clinic->save()) {
						$transaction->commit();
						$this->redirect(array('index'));
					}
				}
			} catch (Exception $e) {
				$transaction->rollback();
			}
		}

		$this->render("update", compact("model", "clinic"));
	}

	/**
	 * Удаляет модель.
	 *
	 * @param int $id идентификатор модели
	 *
	 * @throws CHttpException
	 *
	 * @return void
	 */
	public function actionDelete($id)
	{
		throw new CHttpException(403, "Удаление запрещено");
	}

	/**
	 * Список моделей.
	 *
	 * @return void
	 */
	public function actionIndex()
	{
		$model = new ApiClinicModel('search');

		$model->unsetAttributes();
		$model->dbCriteria->order = $model->getTableAlias() . '.id, ' . $model->getTableAlias() . '.ctime';

		$get = Yii::app()->request->getQuery(CHtml::modelName($model));

		if ($get) {
			$model->attributes = $get;
		}

		$this->render("index", compact("model"));
	}

	/**
	 * Получает модель по идентификатору
	 *
	 * @param int $id идентификатор модели
	 *
	 * @throws CHttpException
	 *
	 * @return ApiClinicModel
	 */
	public function loadModel($id)
	{
		$model = ApiClinicModel::model()->findByPk($id);

		if ($model === null) {
			throw new CHttpException(404, "Модель не найдена");
		}

		$model->setScenario('backend');

		return $model;
	}

	/**
	 * Выводит на экран JSON список клиник для autocomplete
	 *
	 * @param string $term искомое совпадение
	 *
	 * @throws CHttpException
	 *
	 * @return void
	 */
	public function actionAutocomplete($term)
	{
		if (!$term) {
			throw new CHttpException("404", "Некорректный запрос");
		}

		echo json_encode(ClinicModel::model()->getListByTerm($term));
	}
}
