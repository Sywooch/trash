<?php
namespace dfs\modules\payments;

use CWebModule;

/**
 * Class PaymentsModule
 *
 * @author Aleksey Parshukov <parshukovag@gmail.com>
 * @date 17.09.2013
 *
 * @see https://docdoc.megaplan.ru/task/1001896/card/
 *
 * Модуль работы с платёжными системами
 */
class PaymentsModule extends CWebModule
{
	/**
	 * Нейспейс где искать контроллеры
	 *
	 * @var string
	 */
	public $controllerNamespace = '\dfs\modules\payments\controllers';

	/**
	 * Собитые которое будет вызванно после проведение инвойса
	 *
	 * @var callback[]
	 */
	protected $onInvoiceClose;

	/**
	 * Конфигурация процессоров
	 *
	 * @var array
	 */
	protected $processors;

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
		$this->setImport(array(
			'payments.models.*',
			'payments.components.*',
		));
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
