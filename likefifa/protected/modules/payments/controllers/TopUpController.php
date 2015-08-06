<?php
namespace dfs\modules\payments\controllers;
use dfs\modules\payments\models\PaymentsProcessor;


/**
 * Class TopUpController
 *
 * Пополнение счёта
 *
 * @author Aleksey Parshukov <parshukovag@gmail.com>
 * @date 17.09.2013
 *
 */
class TopUpController extends \FrontendController
{
	/**
	 * Запрос на пополнение счёта
	 */
	public function actionIndex()
	{
		$this->render('index');
		// Выбираем робокассу
		// Получаем ссылку на оплату

		$paymentsProcessor='robokassa';
		$processor = PaymentsProcessor::findByKey($paymentsProcessor);
		$this->redirect($processor->getRedirectUrl());
	}
}