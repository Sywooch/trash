<?php

class UndergroundLineController extends BackendController
{
	/**
	 * @param UndergroundLine $model
	 */
	public function actionCreate($model = null)
	{
		if($model == null) {
			$model=new UndergroundLine;
			$this->breadcrumbs=array(
				'Ветки метро'=>array('index'),
				'Создание ветки метро',
			);
			$this->pageTitle = 'Создание ветки метро';
		}
		if(isset($_POST['UndergroundLine']))
		{
			$model->attributes=$_POST['UndergroundLine'];
			if($model->save())
				$this->redirect(array('index','id'=>$model->id));
		}

		$this->render('form',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);
		$this->breadcrumbs=array(
			'Ветки метро'=>array('index'),
			'Изменение ветки метро',
		);
		$this->pageTitle = 'Изменение ветки метро';
		$this->actionCreate($model);
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax'])) {
				$this->saveRedirect();
			}
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$model = new UndergroundLine('search');
		if (isset($_GET['UndergroundLine'])) {
			$model->attributes = $_GET['UndergroundLine'];
		}
		
		$this->render('index',array(
			'model' => $model,
		));
	}

	/**
	 * @param $id
	 *
	 * @return UndergroundLine
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=UndergroundLine::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='underground-line-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
