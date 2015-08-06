<?php

namespace dfs\docdoc\components;

/**
 * Class CommandEvent
 * @package dfs\docdoc\components
 */
class CommandHandler extends \CComponent
{
	/**
	 * Отправляем события в newrelic
	 *
	 * @param \CEvent $event
	 */
	public static function onRunCommand(\CEvent $event)
	{
		$command = $event->sender;
		\Yii::app()->newRelic->nameTransaction("yiic/{$command->getName()}/{$event->params['action']}");
	}
} 