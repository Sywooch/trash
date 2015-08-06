<?php
namespace dfs\docdoc\extensions;

/**
 * Class ConsoleApplication
 *
 * Нужен для переопределения error handler, чтобы в продакшете не останавливать выполнение
 * Используется в YiiAppRunner::create
 */
class ConsoleApplication extends \CConsoleApplication
{
	use ErrorHandlerTrait;

	/**
	 * Событие запуска команды
	 *
	 * @param \CEvent $event
	 */
	public function onRunCommand(\CEvent $event)
	{
		$this->raiseEvent('onRunCommand', $event);
	}

} 
