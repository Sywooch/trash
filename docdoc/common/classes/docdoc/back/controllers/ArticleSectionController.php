<?php
namespace dfs\docdoc\back\controllers;

use dfs\docdoc\models\ArticleSectionModel;
use    CHttpException,
	Yii,
	CModel,
	CHtml,
	CActiveForm;

class ArticleSectionController extends BackendController
{

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model = new ArticleSectionModel('backend');

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		$modelName = CHtml::modelName($model);
		if (isset($_POST[$modelName])) {
			$model->attributes = $_POST[$modelName];
			if ($model->save()) {
				$this->redirect(array('index', 'id' => $model->id));
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
			if ($model->save())
				$this->redirect(array('index', 'id' => $model->id));
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
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		} else
			throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$model = new ArticleSectionModel('search');
		$model->unsetAttributes(); // clear any default values
		$modelName = CHtml::modelName($model);
		if (isset($_GET[$modelName]))
			$model->attributes = $_GET[$modelName];
		$this->render(
			'index',
			array(
				'model' => $model,
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
	 * @return ArticleSectionModel
	 */
	public function loadModel($id)
	{
		$model = ArticleSectionModel::model()->findByPk($id);
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
		if (isset($_POST['ajax']) && $_POST['ajax'] === 'article-section-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
