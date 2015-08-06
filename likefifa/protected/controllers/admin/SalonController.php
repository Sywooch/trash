<?php

use likefifa\models\forms\LfSalonAdminFilter;

class SalonController extends BackendController
{
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new LfSalon;
		$model->scenario = 'adminSalon';

		$this->breadcrumbs=array(
			'Салоны'=>array('index'),
			'Создание салона',
		);
		$this->pageTitle = 'Создание салона';

		if(isset($_POST['LfSalon']))
		{
			$model->attributes=$_POST['LfSalon'];
			if($model->save()) {
				$model->uploadFile();
				if (!empty($_POST['LfSalon']['prices'])) {
					$model->applyPrices($_POST['LfSalon']['prices']);
				}
				$this->saveRedirect();
			}
		}

		$this->render('form', compact('model'));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);
		$model->scenario = 'adminSalon';

		$this->breadcrumbs=array(
			'Салоны'=>array('index'),
			'Измененние салона',
		);
		$this->pageTitle = 'Измененние салона';

		if(isset($_POST['delete_logo'])) {
			$model->deleteFile('logo');
			$model->logo = null;
		}
		if(isset($_POST['LfSalon']))
		{
			$model->attributes=$_POST['LfSalon'];
			if($model->save()) {
				if (( isset($_FILES['LfSalon']['name']['logo']) && ($_FILES['LfSalon']['name']['logo'] != '') )){
					$model->uploadFile();
				}
				if (!empty($_POST['LfSalon']['prices'])) {
					$model->applyPrices($_POST['LfSalon']['prices']);
				}
				$this->saveRedirect();
			}
		}

		$this->render('form', compact('model'));
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

			if(!isset($_GET['ajax'])) {
				$this->saveRedirect();
			}

		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Список всех салонов
	 *
	 * @return void
	 */
	public function actionIndex()
	{
		$this->pageTitle = 'Салоны';
		$model = new LfSalonAdminFilter('search');
		$modelName = CHtml::modelName($model);
		if (isset($_GET[$modelName])) {
			$model->attributes = $_GET[$modelName];
		}

		$this->render("index", compact("model"));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=LfSalon::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Изменяет значение атрибута. Используется в гридах.
	 *
	 * @throws CHttpException
	 */
	public function actionEditField()
	{
		$pk = Yii::app()->request->getPost('pk');
		$name = Yii::app()->request->getPost('name');
		$value = Yii::app()->request->getPost('value');

		if ($pk === null || $name === null || $value === null) {
			throw new CHttpException(400);
		}

		$model = $this->loadModel($pk);
		$model->$name = $value;
		if(!$model->save(true, [$name])) {
			$str = '';
			foreach($model->errors as $error) {
				$str = implode('<br/>', $error);
			}
			echo $str;
			http_response_code(403);
		}
	}
}
