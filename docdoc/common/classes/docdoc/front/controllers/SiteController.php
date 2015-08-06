<?php

namespace dfs\docdoc\front\controllers;

/**
 * Class SiteController
 *
 * @package dfs\docdoc\front\controllers
 */
class SiteController extends FrontController
{
	/**
	 * Вывод ошибки
	 */
	public function actionError()
	{
		$error = \Yii::app()->errorHandler->error;

		if (!$error) {
			$error = [
				'code'    => '404',
				'message' => '',
			];
		}

		if (\Yii::app()->request->isAjaxRequest) {
			echo $error['message'];
		} else {
			$this->initXslTemplates();
			$this->render('error', $error);
		}
	}
}
