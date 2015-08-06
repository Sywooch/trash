<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 04.07.14
 * Time: 14:57
 */

namespace dfs\docdoc\extensions;

use dfs\common\config\Environment;
use Yii;
use CLogger;
use CErrorEvent;

/**
 * Trait ErrorHandler
 *
 * @see \CWebApplication
 * @method bool onError
 * @method \CErrorHandler getErrorHandler
 * @method bool displayError
 * @method bool displayException
 * @method bool end
 */
trait ErrorHandlerTrait
{
	/**
	 * Переопределяется CApplication::handleError, чтобы
	 * не останавливалось выполнение в production и stage
	 *
	 * @param integer $code    the level of the error raised
	 * @param string  $message the error message
	 * @param string  $file    the filename that the error was raised in
	 * @param integer $line    the line number the error was raised at
	 */
	public function handleError($code, $message, $file, $line)
	{
		if (Environment::isDebug()) {
			parent::handleError($code, $message, $file, $line);
			return;
		}

		if ($code & error_reporting()) {
			$log = "$message ($file:$line)\nStack trace:\n";

			$trace = debug_backtrace();

			// skip the first 3 stacks as they do not tell the error position
			if (count($trace) > 3) {
				$trace = array_slice($trace, 3);
			}

			foreach ($trace as $i => $t) {
				if (!isset($t['file'])) {
					$t['file'] = 'unknown';
				}

				if (!isset($t['line'])) {
					$t['line'] = 0;
				}

				if (!isset($t['function'])) {
					$t['function'] = 'unknown';
				}

				$log .= "#$i {$t['file']}({$t['line']}): ";

				if (isset($t['object']) && is_object($t['object'])) {
					$log .= get_class($t['object']) . '->';
				}

				$log .= "{$t['function']}()\n";
			}

			if (isset($_SERVER['REQUEST_URI'])) {
				$log .= 'REQUEST_URI=' . $_SERVER['REQUEST_URI'];
			}

			Yii::log($log, CLogger::LEVEL_ERROR, 'php');

			// Отправляем ошибку в NewRelic
			Yii::app()->newRelic->noticeErrorLong($code, $message, $file, $line, $log);
		}
	}

	/**
	 * Переопределяется CApplication::handleException, чтобы
	 * отправить событие в new relic
	 *
	 * @param $exception
	 */
	public function handleException($exception)
	{
		// Отправляем ошибку в NewRelic
		Yii::app()->newRelic->noticeError($exception->__toString(), $exception);

		parent::handleException($exception);
	}
}
