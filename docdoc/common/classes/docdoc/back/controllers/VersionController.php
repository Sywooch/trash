<?php
namespace dfs\docdoc\back\controllers;

use dfs\docdoc\components\Version;

/**
 * Class VersionController
 *
 * @package dfs\docdoc\back\controllers
 */
class VersionController extends BackendController
{
	/**
	 * История версий
	 */
	public function actionHistory()
	{
		$this->render(
			'history',
			array(
				'version' => new Version(),
			)
		);
	}
} 