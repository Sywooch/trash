<?php

use dfs\modules\payments\models\PaymentsOperations;
use likefifa\models\forms\PaymentsOperationsAdminFilter;

/**
 * Файл класса TransactionsController.
 *
 * Контроллер для транзакций
 *
 * @author  Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @link    https://docdoc.megaplan.ru/task/1003707/card/
 * @package controllers.admin
 */
class TransactionsController extends BackendController
{

	/**
	 * Список транзакций
	 *
	 * @return void
	 */
	public function actionIndex()
	{
		$this->pageTitle = 'Транзакции';
		$model = new PaymentsOperationsAdminFilter('search');
		$model->unsetAttributes();

		if(($attributes = Yii::app()->request->getQuery(CHtml::modelName($model))) != null) {
			$model->attributes = $attributes;
		}

		$sumCriteria = $model->search()->getCriteria();
		$sumCriteria->select = [new CDbExpression('SUM(t.amount_real) as sum')];
		$sum = PaymentsOperationsAdminFilter::model()->find($sumCriteria)->sum;

		$this->render('index', compact('model', 'sum'));
	}
}