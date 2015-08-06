<?php

namespace dfs\modules\payments\controllers;
use dfs\modules\payments\processors\Robokassa;

/**
 * Class ResultControlle
 *
 * Приём платежей
 *
 * @author Aleksey Parshukov <parshukovag@gmail.com>
 * @date 26.09.2013
 *
 * @package dfs\modules\payments\controllers
 */
class ResultController extends \Controller
{
	/**
	 * Приём платежей от робокассы
	 */
	public function actionRobokassa()
	{
		$processor=new Robokassa();
		try {
			echo $processor->result($_REQUEST);
		}
		catch(\Exception $e) {
			echo $e->getMessage();
		}
	}
} 