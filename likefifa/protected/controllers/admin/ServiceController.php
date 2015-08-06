<?php

class ServiceController extends BackendController
{
	/**
	 * @param LfService $model
	 */
	public function actionCreate($model = null)
	{
		if ($model == null) {
			$model = new LfService;
			$this->breadcrumbs = array(
				'Услуги' => array('index'),
				'Добавление услуги',
			);
			$this->pageTitle = 'Добавление услуги';
		}

		if (isset($_POST['LfService'])) {
			$model->attributes = $_POST['LfService'];
			if ($model->save()) {
				$this->redirect(array('index'));
			}
		}

		$this->render(
			'form',
			array(
				'model' => $model,
			)
		);
	}

	/**
	 * @param integer $id
	 */
	public function actionUpdate($id)
	{
		$model = $this->loadModel($id);
		$this->breadcrumbs = array(
			'Услуги' => array('index'),
			'Изменение услуги',
		);
		$this->pageTitle = 'Изменение услуги ' . $model->id;
		$this->actionCreate($model);
	}

	/**
	 * @param integer $id
	 *
	 * @throws CHttpException
	 */
	public function actionDelete($id)
	{
		if (Yii::app()->request->isPostRequest) {
			$this->loadModel($id)->delete();

			if (!isset($_GET['ajax'])) {
				$this->saveRedirect();
			}
		} else {
			throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
		}
	}

	/**
	 * Lists all models.
	 *
	 * @return void
	 */
	public function actionIndex()
	{
		$model = new LfService('search');
		if (isset($_GET['LfService'])) {
			$model->attributes = $_GET['LfService'];
		}
		$model->dbCriteria->order = 't.weight';

		$this->render('index', compact('model'));
	}

	/**
	 * @param $id
	 *
	 * @return LfService
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model = LfService::model()->findByPk($id);
		if ($model === null) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}
		return $model;
	}
}
