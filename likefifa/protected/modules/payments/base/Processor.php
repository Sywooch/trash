<?php
namespace dfs\modules\payments\base;
use dfs\modules\payments\models\PaymentsInvoice;

/**
 * Class Processor
 *
 * Абстракный класс процессора
 *
 * @author Aleksey Parshukov <parshukovag@gmail.com>
 * @date 26.09.2013
 *
 * @see https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=1310733
 *
 * @package dfs\modules\payments\base
 */
abstract class Processor
{
	/**
	 * Создать ссылку на мёрчента
	 *
	 * @param PaymentsInvoice $invoice
	 *
	 * @return string
	 */
	abstract function buildMerchantUrl(PaymentsInvoice $invoice);

	/**
	 * Конфингурация процессора
	 *
	 * @return array
	 */
	public function getConfig() {
		$name = get_class($this);
		$pops = strrpos($name, "\\");
		if ($pops!==false) {
			$name = substr($name, ++$pops);
		}
		$name = strtolower($name);
		$modules = \Yii::app()->modules;

		return isset($modules['payments']['processors'][$name])
			? $modules['payments']['processors'][$name]
			: array();
	}

	/**
	 * Получение одного из значений конфигурации
	 *
	 * @param  string $name
	 * @param  mixed $default
	 * @return mixed
	 */
	public function getConfigParam($name, $default = null) {
		$config = $this->getConfig();
		return isset($config[$name])
			? $config[$name]
			: $default;
	}
} 