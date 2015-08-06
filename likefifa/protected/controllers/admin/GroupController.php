<?php

class GroupController extends BackendController
{
	/**
	 * @param LfGroup $model
	 */
	public function actionCreate($model = null)
	{
		if($model == null) {
			$model=new LfGroup;
			$this->breadcrumbs=array(
				'Группы'=>array('index'),
				'Создание группы',
			);
			$this->pageTitle = 'Создание группы';
		}

		if(isset($_POST['LfGroup']))
		{
			$model->attributes=$_POST['LfGroup'];
			if($model->save())
				$this->redirect(array('index'));
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
			'Группы'=>array('index'),
			'Изменение группы',
		);
		$this->pageTitle = 'Изменение группы';
		$this->actionCreate($model);
	}

	/**
	 * @param integer $id the ID of the model to be deleted
	 *
	 * @throws CHttpException
	 */
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			$this->loadModel($id)->delete();

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
		$dataProvider=new CActiveDataProvider('LfGroup', array(
			'criteria' => array(
			),
			'pagination' => array(
				'pageSize' => 20,
			),
		));
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * @param integer $id
	 *
	 * @return LfGroup
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=LfGroup::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
}
