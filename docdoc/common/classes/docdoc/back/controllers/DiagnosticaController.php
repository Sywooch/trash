<?php

namespace dfs\docdoc\back\controllers;

use dfs\docdoc\models\DiagnosticaModel;
use CHttpException,
	Yii,
	CModel,
	CHtml,
	CActiveForm;

/**
 * Class DiagnosticaController
 *
 * @package dfs\docdoc\back\controllers
 */
class DiagnosticaController extends BackendController
{

	/**
	 * Displays a particular model.
	 *
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$linkedCenters = array();
		// @todo Нужно по другому получать центры, сейчас они хранятся в другой таблице
		//
		//		foreach ($model->diagnosticCenters as $center) {
		//			$linkedCenters[] = array(
		//				'label' => $center->name,
		//				'url'	=> array('/admin/diagnosticCenter/update/', 'id' => $center->id),
		//			);
		//		}

		$this->render(
			'view',
			[
				'model'         => $this->loadModel($id),
				'linkedCenters' => $linkedCenters,
			]
		);
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model = new DiagnosticaModel();

		$modelName = CHtml::modelName($model);
		if (isset($_POST[$modelName])) {
			$model->attributes = $_POST[$modelName];
			if ($model->save()) {
				$this->redirect(array('index'));
			}
		}

		$this->render(
			'create',
			[
				'model' => $model,
			]
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

		$modelName = CHtml::modelName($model);
		if (isset($_POST[$modelName])) {
			$model->attributes = $_POST[$modelName];
			if ($model->save()) {
				$this->redirect(array('index'));
			}
		}

		$linkedCenters = array();
		//		@todo Нужно по другому получать центры, сейчас они хранятся в другой таблице
		//
		//		foreach ($model->diagnosticCenters as $center) {
		//			$linkedCenters[] = array(
		//				'label' => $center->name,
		//				'url'	=> array('/admin/diagnosticCenter/update/', 'id' => $center->id),
		//			);
		//		}

		$this->render(
			'update',
			[
				'model'         => $model,
				'linkedCenters' => $linkedCenters,
			]
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
			if (!isset($_GET['ajax'])) {
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
			}
		} else {
			throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
		}
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$model = new DiagnosticaModel();
		$model->sort = null;
		$modelName = CHtml::modelName($model);
		if (isset($_GET[$modelName]))
			$model->attributes = $_GET[$modelName];

		$this->render(
			'index',
			[
				'model' => $model,
			]
		);
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 *
	 * @param $id
	 *
	 * @return DiagnosticaModel
	 * @throws \CHttpException
	 */
	public function loadModel($id)
	{
		$model = DiagnosticaModel::model()->findByPk($id);
		$model->setScenario('backend');
		if ($model === null) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 *
	 * @param CModel $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if (isset($_POST['ajax']) && $_POST['ajax'] === 'diagnostica-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
