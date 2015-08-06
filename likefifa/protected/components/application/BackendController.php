<?php

use likefifa\models\AdminModel;

abstract class BackendController extends Controller
{
	public $layout = '//layouts/admin';

	public $newAppointmentCount = 0;
	public $newOpinionCount = 0;

	/**
	 * Урл для возврата после сохранения формы
	 *
	 * @var string
	 */
	public $returnUrl;

	public function beforeAction($action)
	{
		Yii::app()->setComponent(
			'bootstrap',
			[
				'class' => 'application.vendors.clevertech.yii-booster.src.components.Booster',
			]
		);
		Yii::app()->bootstrap->init();
		Yii::app()->setTheme('admin');

		Yii::app()->session->open();

		// Сохраняем урл для возврата в список из форм изменения
		$this->returnUrl =
			isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
		if (($this->action->id == 'create' || $this->action->id == 'update') && isset($_POST['returnUrl'])) {
			$this->returnUrl = $_POST['returnUrl'];
		}

		// Если удаление - возвращаемся на предыдущий урл
		if ($this->action->id == 'delete') {
			$_POST['returnUrl'] = $this->returnUrl;
		}

		return parent::beforeAction($action);
	}

	public function filters()
	{
		return array(
			'accessControl',
		);
	}

	/**
	 * Возвращает правила доступа для контроллера
	 *
	 * @return string[]
	 */
	public function accessRules()
	{
		return array(
			array(
				'allow',
				'actions' => array(
					'admin',
					'index',
					'view',
					'create',
					'update',
					'delete',
					'change',
					'masterData',
					'servicePrice',
					'recharge',
					'deleteWork',
					'saveWork',
					'sendEmail',
					'search',
					'editField',
				),
				'users'   => AdminModel::model()->getAdminsForThisController($this->id),
			),
			array(
				'deny',
				'users' => array('*'),
			),
		);
	}

	/**
	 * @param CActiveRecord $model
	 * @param               $data
	 * @param bool          $save
	 *
	 * @return $this
	 */
	public function assignManyManyRelations($model, $data, $save = false)
	{
		foreach ($model->relations() as $relationName => $relation) {
			if (
				$relation[0] !== CActiveRecord::MANY_MANY
				|| !isset($data[$relationName])
				|| !is_array($data[$relationName])
			) {
				continue;
			}
			$model->$relationName = $data[$relationName];
		}

		$model->save();
		return $this;
	}

	/**
	 * Редирект после сохранения формы
	 */
	public function saveRedirect()
	{
		$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : ['index']);
	}

	/**
	 * @param CActiveRecord $model
	 *
	 * @return boolean
	 */
	protected function checkMassRemove($model)
	{
		$massRemove = Yii::app()->request->getPost('massRemove', false);
		$ids = Yii::app()->request->getPost('ids', []);
		if(!$massRemove || count($ids) == 0) {
			return false;
		}

		$criteria = new CDbCriteria;
		$criteria->addInCondition('t.id', $ids);
		$data = $model->findAll($criteria);
		foreach($data as $row) {
			$row->delete();
		}

		echo CJSON::encode(['status' => 'success']);
		Yii::app()->end();

		return true;
	}

	/**
	 * AJAX валидация
	 *
	 * @param CModel $model
	 *
	 * @return void
	 */
	protected function performAjaxValidation($model)
	{
		$ajax = Yii::app()->request->getPost("ajax");
		if(!$ajax) {
			$ajax = Yii::app()->request->getQuery("ajax");
		}
		if ($ajax) {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}