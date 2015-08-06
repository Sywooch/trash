<?php

namespace dfs\docdoc\widgets\controllers;

use Browser\Browser;
use Browser\Os;

use dfs\docdoc\components\AppController;
use dfs\docdoc\objects\google\partners\WidgetsStats;
use dfs\docdoc\objects\google\users\User;
use Yii;

/**
 * Файл класса BQController.
 *
 * Контроллер для работы с BigQuery
 */
class BQController extends AppController
{

	/**
	 * Отправка события в BigQuery
	 */
	public function actionEvent()
	{
		$request = Yii::app()->request;
		$status = 0;

		$requestData = json_decode($request->getQuery('data'), true);
		$requestData === null && $requestData = [];

		$userId = Yii::app()->gaClient->getId();

		$browser = new Browser($request->getUserAgent());
		$os = new Os($request->getUserAgent());
		//если есть юзер - добавляем его
		$userData = [
			'userId'   => $userId,
			'ip'       => $request->getUserHostAddress(),
			'region'   => 'msk',
			'browser'  => $browser->getName(),
			'platform' => $os->getName(),
		];
		$user = new User();
		$user->add($userData);

		$data = [
			'date'     => date('Y-m-d H:i:s'),
			'partner'  => (isset($requestData['partner']) && (int)$requestData['partner']) ? $requestData['partner'] : null,
			'action'   => isset($requestData['action']) ? $requestData['action'] : null,
			'widget'   => isset($requestData['widget']) ? $requestData['widget'] : null,
			'template' => isset($requestData['template']) ? $requestData['template'] : null,
			'theme'    => isset($requestData['theme']) ? $requestData['theme'] : null,
			'userId'   => $userId,
			'domain'   => isset($requestData['srcDomain']) ? $requestData['srcDomain'] : null,
			'url'      => isset($requestData['srcPath']) ? $requestData['srcPath'] : null,
		];

		$stat = new WidgetsStats();
		try {
			$stat->add($data);
			$status = 1;
		} catch (\Exception $e) {
			trigger_error($e->getMessage(), E_USER_WARNING);
		}

		$this->renderJSON($status);
	}
}
