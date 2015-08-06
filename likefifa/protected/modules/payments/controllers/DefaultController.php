<?php

namespace dfs\modules\payments\controllers;

/**
 * Class DefaultController
 *
 * Оплата
 *
 * @author Aleksey Parshukov <parshukovag@gmail.com>
 * @date 17.09.2013
 *
 */
class DefaultController extends \FrontendController
{
	/**
	 * Успешная оплата
	 */
	public function actionSuccess()
	{
		$this->render('success');
	}

	/**
	 * Не успешная оплата
	 */
	public function actionFail()
	{
		$this->render('fail');
	}
}