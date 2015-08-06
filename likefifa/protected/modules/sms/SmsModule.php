<?php
namespace dfs\modules\payments;

use CWebModule;

/**
 * Class SmsModule
 *
 * @author Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @date   19.11.2013
 *
 * @see    https://docdoc.megaplan.ru/task/1002497/card/
 *
 * Модуль работы с sms-сообщениями
 */
class SmsModule extends CWebModule
{
	/**
	 * Директория с контроллерами
	 *
	 * @var string
	 */
	public $controllerNamespace = '\dfs\modules\sms\controllers';

	/**
	 * Активен модуль или нет
	 *
	 * @var bool
	 */
	protected $isActive = true;

	/**
	 * Инициализация модуля
	 */
	public function init()
	{
		$this->setImport(
			array(
				'payments.models.*',
			)
		);
	}

	/**
	 * @param \CController $controller
	 * @param \CAction     $action
	 *
	 * @return bool
	 */
	public function beforeControllerAction(\CController $controller, \CAction $action)
	{
		return parent::beforeControllerAction($controller, $action);
	}

	/**
	 * Активен модуль или нет
	 *
	 * @return bool
	 */
	public function isActive()
	{
		return $this->isActive;
	}
}
