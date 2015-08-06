<?php

class SalonPhotoController extends BackendController
{
	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		$this->breadcrumbs=array(
			'Фото салонов'=>array('index'),
			'Изменение',
		);
		$this->pageTitle = 'Изменения фото салона';

		if(isset($_POST['SalonPhoto']))
		{
			$model->attributes=$_POST['SalonPhoto'];
			if($model->save())
				$this->redirect(array('index'));
		}

		$this->render('form',array(
			'model'=>$model,
		));
	}

	/**
	 * @param integer $id
	 *
	 * @throws CDbException
	 * @throws CHttpException
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
		/*
		$dataProvider=new CActiveDataProvider('SalonPhoto', array(
			'criteria' => array(
			),
		));
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
		*/

		$model = new LfSalonPhoto('search');
		if (isset($_GET['SalonPhoto'])) {
			$model->attributes = $_GET['SalonPhoto'];
		}
		$this->render('index',array(
				'model' => $model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=LfSalonPhoto::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='salon-photo-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
