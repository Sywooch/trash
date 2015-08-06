<?php

namespace dfs\docdoc\helpers;

use Browser\Browser;


/**
 * Class BrowserHelper
 *
 * @package dfs\docdoc\helpers
 */
class BrowserHelper
{
	/**
	 * Проверка на старые браузеры
	 */
	static public function browserIsSupported()
	{
		$browser = new Browser();
		$browserName = $browser->getName();

		$cfg = \Yii::app()->params['supportedBrowsers'];

		return isset($cfg[$browserName]) && $browser->getVersion() >= $cfg[$browserName];
	}
} 
