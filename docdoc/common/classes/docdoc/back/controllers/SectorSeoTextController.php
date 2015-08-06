<?php

namespace dfs\docdoc\back\controllers;

use dfs\docdoc\models\SectorSeoTextModel;
use CActiveDataProvider,
	CHttpException,
	Yii,
	CModel,
	CHtml,
	CActiveForm;

class SectorSeoTextController extends BackendController
{
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model = new SectorSeoTextModel;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		$modelName = CHtml::modelName($model);
		if (isset($_POST[$modelName])) {
			$model->attributes = $_POST[$modelName];
			if ($model->save()) {
				$this->assignManyManyRelations($model, $_POST[$modelName], true);

				$this->redirect(array('index'));
			}
		}

		$this->render(
			'create',
			array(
				'model' => $model,
			)
		);
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model = $this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		$modelName = CHtml::modelName($model);
		if (isset($_POST[$modelName])) {
			$model->attributes = $_POST[$modelName];
			$this->assignManyManyRelations($model, $_POST[$modelName]);
			if ($model->save()) {
				$this->redirect(array('index'));
			}
		}

		$this->render(
			'update',
			array(
				'model' => $model,
			)
		);
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 *
	 * @param integer $id the ID of the model to be deleted
	 *
	 * @throws \CDbException
	 * @throws \CHttpException
	 */
	public function actionDelete($id)
	{
		if (Yii::app()->request->isPostRequest) {
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if (!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
		} else
			throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider = new CActiveDataProvider(
			'\dfs\docdoc\models\SectorSeoTextModel', array(
				'criteria'   => array(
					'scopes' => 'ordered',
				),
				'pagination' => array(
					'pageSize' => 20,
				),
			)
		);
		$this->render(
			'index',
			array(
				'dataProvider' => $dataProvider,
			)
		);
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 *
	 * @param int $id
	 *
	 * @throws \CHttpException
	 *
	 * @return SectorSeoTextModel
	 */
	public function loadModel($id)
	{
		$model = SectorSeoTextModel::model()->with('sectors')->findByPk($id);
		if ($model === null)
			throw new CHttpException(404, 'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 *
	 * @param CModel $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if (isset($_POST['ajax']) && $_POST['ajax'] === 'sector-seo-text-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
