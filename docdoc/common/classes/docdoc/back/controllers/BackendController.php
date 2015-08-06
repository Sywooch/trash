<?php

namespace dfs\docdoc\back\controllers;

use CActiveRecord;
use dfs\docdoc\components\AppController;
use dfs\docdoc\models\UserModel;
use CHtml;
use Yii;

/**
 * Class BackendController
 *
 * Дефолтный контроллер админки
 *
 * @package dfs\docdoc\back\controllers
 */
abstract class BackendController extends AppController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout = '//layouts/2.0';

	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs = array();

	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu = array();

	/**
	 * @param CActiveRecord $model
	 * @param array         $data
	 * @param bool          $save
	 *
	 * @return $this
	 */
	public function assignManyManyRelations(CActiveRecord $model, array $data, $save = false)
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
	 * Фильтры
	 *
	 * @return array
	 */
	public function filters()
	{
		return array(
			'accessControl',
		);
	}

	/**
	 * Возвращает правила доступа для контроллера
	 *
	 * @return array
	 */
	public function accessRules()
	{
		return [[Yii::app()->user->isGuest ? 'deny' : 'allow']];
	}
}