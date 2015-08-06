<?php

class SectorController extends BackendController
{
	/**
	 * @param Sector $model
	 */
	public function actionCreate($model = null)
	{
		if ($model == null) {
			$model = new Sector;
			$this->breadcrumbs = array(
				'Направления' => array('index'),
				'Новое направление',
			);
			$this->pageTitle = 'Новое направление';
		}

		if (isset($_POST['Sector'])) {
			$model->attributes = $_POST['Sector'];
			if ($model->save()) {
				$this->redirect(array('index'));
			}
		}

		$this->render(
			'form',
			compact('model')
		);
	}

	/**
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model = $this->loadModel($id);
		$this->breadcrumbs = array(
			'Направления' => array('index'),
			'Изменение направления',
		);
		$this->pageTitle = 'Изменение направления';
		$this->actionCreate($model);
	}

	/**
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		if (!isset($_GET['ajax'])) {
			$this->saveRedirect();
		}
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider = new CActiveDataProvider('Sector', array(
			'criteria'   => array(
				'scopes' => 'ordered',
			),
			'pagination' => array(
				'pageSize' => 20,
			),
		));
		$this->render(
			'index',
			array(
				'dataProvider' => $dataProvider,
			)
		);
	}

	/**
	 * @param $id
	 *
	 * @return Sector
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model = Sector::model()->findByPk($id);
		if ($model === null) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}
		return $model;
	}
}
