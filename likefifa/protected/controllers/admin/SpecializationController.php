<?php

class SpecializationController extends BackendController
{
	/**
	 * @param LfSpecialization $model
	 */
	public function actionCreate($model = null)
	{
		if ($model == null) {
			$model = new LfSpecialization;
			$this->breadcrumbs = array(
				'Специализации' => array('index'),
				'Создание спецализации',
			);
			$this->pageTitle = 'Создание спецализации';
		}

		if (isset($_POST['LfSpecialization'])) {
			$model->attributes = $_POST['LfSpecialization'];
			$this->assignManyManyRelations($model, $_POST['LfSpecialization']);
			if ($model->save()) {
				$this->redirect(array('index'));
			}
		}

		$this->render('form', compact('model'));
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
		$this->breadcrumbs = array(
			'Специализации' => array('index'),
			'Изменение спецализации',
		);
		$this->pageTitle = 'Изменение спецализации';
		$this->actionCreate($model);
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 *
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
		$dataProvider = new CActiveDataProvider('LfSpecialization', array(
			'criteria'   => array(),
			'pagination' => array(
				'pageSize' => 50,
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
	 * @param integer $id
	 *
	 * @return LfSpecialization
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model = LfSpecialization::model()->findByPk($id);
		if ($model === null) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}
		return $model;
	}
}
