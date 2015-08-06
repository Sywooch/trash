<?php
use likefifa\models\LfServiceAlias;

/**
 * Контроллер алиасов услуг
 */
class ServiceAliasesController extends BackendController
{

	/**
	 * Создание алиаса
	 *
	 * @param LfServiceAlias $model
	 *
	 * @return void
	 */
	public function actionCreate($model = null)
	{
		if($model == null) {
			$model = new LfServiceAlias;
			$this->breadcrumbs = array(
				'Алиасы услуг' => array('index'),
				'Добавление алиаса',
			);
			$this->pageTitle = 'Добавление алиаса';
		}

		$post = Yii::app()->request->getPost(CHtml::modelName($model));
		if ($post) {
			$model->attributes = $post;
			if ($model->save()) {
				$this->redirect(array('index'));
			}
		}

		$this->render("form", compact("model"));
	}

	/**
	 * Обновление алиаса
	 *
	 * @param int $id идентификатор модели
	 *
	 * @return bool
	 */
	public function actionUpdate($id)
	{
		$model = $this->loadModel($id);
		$this->breadcrumbs = array(
			'Алиасы услуг' => array('index'),
			'Изменение алиаса',
		);
		$this->pageTitle = 'Изменение алиаса';
		$this->actionCreate($model);
	}

	/**
	 * Удаляет администратора
	 *
	 * @param int $id идентификатор модели
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		$this->redirect(array("index"));
	}

	/**
	 * Список администраторов
	 *
	 * @return void
	 */
	public function actionIndex()
	{
		$model = new LfServiceAlias('search');
		$model->unsetAttributes();
		$get = Yii::app()->request->getQuery(CHtml::modelName($model));
		if ($get) {
			$model->attributes = $get;
		}

		$this->render('index', compact("model"));
	}

	/**
	 * Получает модель по идентификатору
	 *
	 * @param integer $id идентификатор модели
	 *
	 * @return LfServiceAlias
	 *
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model = LfServiceAlias::model()->findByPk($id);
		if ($model === null) {
			throw new CHttpException(404);
		}

		return $model;
	}
}
