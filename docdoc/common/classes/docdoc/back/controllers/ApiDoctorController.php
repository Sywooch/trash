<?php

namespace dfs\docdoc\back\controllers;

use dfs\docdoc\models\ApiClinicModel;
use dfs\docdoc\models\ApiDoctorModel;
use CHttpException;
use CHtml;
use dfs\docdoc\models\DoctorClinicModel;
use dfs\docdoc\models\DoctorModel;
use Yii;
use Exception;

/**
 * Файл класса ApiDoctorController.
 *
 * Контроллер интерфейса для таблицы api_doctor
 *
 * @author  Mikhail Vasilyev <mvasilyev@docdoc.ru>
 * @link    https://docdoc.atlassian.net/browse/DD-68
 * @package dfs.docdoc.back.controllers
 */
class ApiDoctorController extends BackendController
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

		$doctorId = Yii::app()->request->getPost("doctorId");
		$clinicId = Yii::app()->request->getPost("clinicId");

		if ($doctorId && $clinicId) {
			$doctorClinic = DoctorClinicModel::model()->findDoctorClinic($doctorId, $clinicId);

			if ($doctorClinic) {
				$transaction = Yii::app()->db->beginTransaction();
				try {
					$doctorClinic->saveExternalId($model->id);
					$transaction->commit();
					$this->redirect(array('index'));
				} catch (Exception $e) {
					$transaction->rollback();
				}
			}
		} elseif(Yii::app()->request->isPostRequest) {
			$model->enabled = Yii::app()->request->getPost("enabled");
			$model->save();
		}

		$this->render("update", compact("model"));
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
		$model = new ApiDoctorModel('search');

		$model->unsetAttributes();
		$model->dbCriteria->order = $model->getTableAlias() . '.api_clinic_id, ' . $model->getTableAlias() . '.name';

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
	 * @return ApiDoctorModel
	 */
	public function loadModel($id)
	{
		$model = ApiDoctorModel::model()->findByPk($id);

		if ($model === null) {
			throw new CHttpException(404, "Модель не найдена");
		}

		$model->setScenario('backend');

		return $model;
	}

	/**
	 * Выводит на экран JSON список докторов из autocomplete
	 *
	 * @param string $term искомое совпадение
	 *
	 * @throws CHttpException
	 *
	 * @return void
	 */
	public function actionAutocomplete($clinicId, $term)
	{
		if (!$term) {
			throw new CHttpException("400", "Некорректный запрос");
		}

		$apiClinic = ApiClinicModel::model()->findByPk($clinicId);
		if (!$apiClinic) {
			throw new CHttpException("404", "API клиники с таким идентификатором не существует");
		}

		$clinic = $apiClinic->clinic;
		if ($clinic) {
			$list = DoctorModel::model()->inClinics([$clinic->id])->getListByTerm($term);
		} else {
			$list = DoctorModel::model()->getListByTerm($term);
		}

		echo json_encode($list);
	}

	/**
	 * Отклеивает от реального врача
	 *
	 * @param integer $id идентификатор модели
	 *
	 * @throws CHttpException
	 *
	 * @return void
	 */
	public function actionUnmerged($id)
	{
		$model = ApiDoctorModel::model()->findByPk($id);

		if ($model === null) {
			throw new CHttpException(404, "Модель не найдена");
		}

		if($model->doctorClinic){
			$model->doctorClinic->doc_external_id = null;
			$model->doctorClinic->save();
		}

		$this->redirect($this->createUrl("apiDoctor/update", ["id" => $id]));
	}
}
