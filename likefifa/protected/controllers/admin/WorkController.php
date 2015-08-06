<?php

use likefifa\models\AdminModel;
use likefifa\models\forms\LfWorkAdminFilter;
use likefifa\models\RegionModel;

class WorkController extends BackendController
{
	public function accessRules()
	{
		return CMap::mergeArray(
			[
				[
					'allow',
					'actions' => [
						'indexGrid',
						'saveGridIndex'
					],
					'users'   => AdminModel::model()->getAdminsForThisController($this->id),
				]
			],
			parent::accessRules()
		);
	}

	/**
	 * @param integer $id
	 */
	public function actionUpdate($id)
	{
		$model = $this->loadModel($id);

		$this->breadcrumbs = array(
			'Работы' => array('index'),
			'Изменение работы',
		);
		$this->pageTitle = 'Изменение работы';

		if (isset($_POST['LfWork'])) {
			$model->attributes = $_POST['LfWork'];
			if ($model->save()) {
				$this->saveRedirect();
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
		$model = new LfWorkAdminFilter('search');
		$model->unsetAttributes();
		$model->dbCriteria->order = 't.id DESC';

		$modelName = CHtml::modelName($model);
		if (isset($_GET[$modelName])) {
			$model->attributes = $_GET[$modelName];
		}

		if (!empty(Yii::app()->request->getQuery('service'))) {
			$model->service_id = Yii::app()->request->getQuery('service');
		}

		if (!empty(Yii::app()->request->getQuery('specialization'))) {
			$model->specialization_id = Yii::app()->request->getQuery('specialization');
		}

		$this->render('index', compact('model'));
	}

	/**
	 * @param integer $id
	 *
	 * @return LfWork
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model = LfWork::model()->findByPk($id);
		if ($model === null) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}
		return $model;
	}

	public function actionAjaxupdate()
	{
		$act = $_GET['act'];

		if ($act == 'altUpdate') {
			$altAll = $_POST['alt'];
			if (count($altAll) > 0) {
				foreach ($altAll as $workId => $alt) {
					$model = $this->loadModel($workId);
					$model->alt = $alt;
					$model->save();
				}
			}
		}
	}

	/**
	 * Вызывает модальное окно с выбором позиции на главной
	 */
	public function actionIndexGrid($id)
	{
		$model = $this->loadModel($id);

		$criteria = new CDbCriteria;
		$criteria->addCondition('t.index_position IS NOT NULL');
		if ($model->master->city->region_id == RegionModel::MOSCOW_ID) {
			$criteria->addCondition('t.index_position >= 0 AND t.index_position < 10');
		} else {
			$criteria->addCondition('t.index_position >= 10');
		}
		$criteria->index = 'index_position';
		$works = LfWork::model()->findAll($criteria);

		$this->renderPartial('index_grid', compact('model', 'works'));
	}

	/**
	 * Сохраняет позицию на главной у работы
	 *
	 * @param integer $id
	 * @param integer $index
	 */
	public function actionSaveGridIndex($id, $index)
	{
		$model = $this->loadModel($id);
		Yii::app()->db->createCommand('UPDATE lf_work SET index_position = null WHERE index_position = :index_position')->execute([
				':index_position' => $index,
			]);
		$model->index_position = $index;
		$model->save();
	}
}
