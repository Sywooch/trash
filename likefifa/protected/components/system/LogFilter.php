<?php


namespace likefifa\components\system;

use CLogFilter;
use Yii;

/**
 * Класс для фильтрации 404 ошибок
 * Class LogFilter
 *
 * @package likefifa\components\common
 */
class LogFilter extends CLogFilter
{
	public $excluded = [

	];

	public function filter(&$logs)
	{
		if ($logs) {
			foreach ($logs as $logKey => $log) {
				if ($log[2] == 'exception.CHttpException.404') {
					foreach ($this->excluded as $exclude) {
						if (Yii::app()->request->getUrl() == $exclude) {
							unset($logs[$logKey]);
						}
					}
				}
			}
		}
	}
}