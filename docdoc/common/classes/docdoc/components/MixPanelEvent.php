<?php
namespace dfs\docdoc\components;

use Yii;

/**
 * Class MixPanelEvent
 *
 * Класс-регистратор событий MixPanel
 *
 * @package dfs\docdoc\components
 */
class MixPanelEvent
{
	/**
	 * Метод-обертка для вызова компонента Yii:app()->mixpanel из EventDispatcher'a
	 * т.к. напрямую вызвать компонент из него нельзя
	 *
	 * @param \CEvent $modelEvent
	 */
	public function requestStatusChanged($modelEvent)
	{
		try {
			/** @var MixpanelComponent $mixpanel */
			$mixpanel = Yii::app()->mixpanel;
			$mixpanel->event($modelEvent->sender);
		} catch (\Exception $e) {
			trigger_error($e->getMessage(), E_USER_WARNING);
		}

	}

}
