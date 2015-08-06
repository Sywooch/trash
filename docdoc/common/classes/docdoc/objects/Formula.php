<?php
namespace dfs\docdoc\objects;

/**
 * Class Formula
 *
 * Формула для расчета стратегий
 *
 * @package dfs\docdoc\objects
 */
class Formula
{
	/**
	 * исходная формула
	 *
	 * @var null
	 */
	private $_f = null;

	/**
	 * Формула, переведенная в массив
	 *
	 * @var null
	 */
	private $_formula = null;

	/**
	 * @param $formula
	 */
	function __construct($formula)
	{
		$this->setFormula($formula);
	}

	/**
	 * Инициализация формулы
	 *
	 * @param $formula
	 */
	public function setFormula($formula)
	{
		$this->_f = $formula;

		//вырезаем все переменные
		preg_match_all('/\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff\.]*)/', $this->_f, $variables);

		if (isset($variables[1])) {
			foreach ($variables[1] as $v) {
				$p = explode(".", $v);
				$this->_formula[$p[0]][$p[1]] = 0;
			}
		}
	}

	/**
	 * Установка значения переменной
	 *
	 * @param $object
	 * @param $variable
	 * @param $value
	 */
	public function set($object, $variable, $value) {
		$this->_formula[$object][$variable] = (float)$value;
	}

	/**
	 * Получение переменных
	 *
	 * @param $objectName
	 *
	 * @return array
	 */
	public function getVariables($objectName)
	{
		return isset($this->_formula[$objectName]) ? array_keys($this->_formula[$objectName]) : [];
	}

	/**
	 * Выполнение формулы
	 *
	 * @return float
	 * @throws \Exception
	 */
	public function evaluate()
	{
		$f = $this->_f;
		foreach ($this->_formula as $object => $vars) {
			foreach ($vars as $name => $val) {
				$f = str_replace("\${$object}.{$name}", $val, $f);
			}
		}

		//защита от выполнения кода
		if (preg_match("/[a-zA-Z]/", $f)) {
			throw new \Exception("Некорректное выражение " . $f);
		}

		//чтобы eval не падал
		if (is_null($f) || $f === '') {
			return null;
		}

		eval("\$val = " . $f . ";");
		return $val;
	}
}
