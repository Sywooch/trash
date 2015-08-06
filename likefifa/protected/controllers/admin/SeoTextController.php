<?php

class SeoTextController extends BackendController
{
	/**
	 * @param LfSeoText $model
	 */
	public function actionCreate($model = null)
	{
		if ($model == null) {
			$model = new LfSeoText;
			if(($id = Yii::app()->request->getQuery('id')) != null) {
				$copyModel = LfSeoText::model()->findByPk($id);
				if($copyModel != null) {
					$model->attributes = $copyModel->attributes;
					$model->id = null;
				}
			}
			$this->breadcrumbs = array(
				'Сео тексты' => array('index'),
				'Добавление текста',
			);
			$this->pageTitle = 'Добавление текста';
		}

		if (isset($_POST['LfSeoText'])) {
			$model->attributes = $_POST['LfSeoText'];
			if ($model->save()) {
				$this->redirect(array('index'));
			}
		}

		$this->render('form', compact('model'));
	}

	/**
	 * @param integer $id
	 */
	public function actionUpdate($id)
	{
		$model = $this->loadModel($id);
		$this->breadcrumbs = array(
			'Сео тексты' => array('index'),
			'Изменение текста',
		);
		$this->pageTitle = 'Изменение текста';
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
		$model = new LfSeoText('search');

		$modelName = CHtml::modelName($model);
		if (isset($_GET[$modelName])) {
			$model->attributes = $_GET[$modelName];
		}

		$this->render('index', compact('model'));
	}

	/**
	 * @param integer $id
	 *
	 * @return LfSeoText
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model = LfSeoText::model()->findByPk($id);
		if ($model === null) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}
		return $model;
	}
}
