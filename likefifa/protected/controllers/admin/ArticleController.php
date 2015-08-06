<?php

class ArticleController extends BackendController
{
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model = new Article;

		$this->breadcrumbs = array(
			'Статьи' => array('index'),
			'Создание статьи',
		);
		$this->pageTitle = 'Создание статьи';

		if (isset($_POST['Article'])) {
			$model->attributes = $_POST['Article'];
			if ($model->save()) {
				$this->assignManyManyRelations($model, $_POST['Article']);

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
			'Статьи' => array('index'),
			'Изменение статьи',
		);
		$this->pageTitle = 'Изменение статьи';

		if (isset($_POST['Article'])) {
			$model->attributes = $_POST['Article'];
			if (empty($_POST['Article']['article_section_id'])) {
				$model->article_section_id = null;
			}
			$this->assignManyManyRelations($model, $_POST['Article']);
			if (!isset($_POST['Article']['services'])) {
				$deleteServices = LfArticleService::model()->findAll('article_id = ' . $model->id);
				foreach ($deleteServices as $item) {
					$item->delete();
				}
			}
			if ($model->save()) {
				$this->redirect(array('index'));
			}
		}

		$this->render('form', compact('model'));
	}

	/**
	 * @param $id
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
	 */
	public function actionIndex()
	{
		$dataProvider = new CActiveDataProvider('Article', array(
			'criteria'   => array(
				'order' => 't.id DESC'
			),
			'pagination' => array(
				'pageSize' => 50,
			),
		));
		$this->render('index', compact('dataProvider'));
	}

	/**
	 * @param $id
	 *
	 * @return Article
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model = Article::model()->findByPk($id);
		if ($model === null) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}
		return $model;
	}
}
