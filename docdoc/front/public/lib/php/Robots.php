<?php
/**
 * User: aparshukov
 * Date: 2/19/14
 * Time: 2:04 PM
 *
 * Класс генерации робата
 */
class Robots
{
	/**
	 * Данные робота
	 *
	 * @var array
	 */
	private $data = array();

	/**
	 * Добавляет значение
	 *
	 * @param string $name
	 * @param string $value
	 */
	public function addValue($name, $value)
	{
		assert('is_string($name)');
		assert('is_string($value)');
		$this->data[$name][] = $value;
	}

	/**
	 * Задаёт значение
	 *
	 * @param string $name
	 * @param string|array $value
	 */
	public function setValue($name, $value)
	{
		assert('is_string($name)');
		$this->data[$name] = (array) $value;
	}

	/**
	 * Удаляет добавленное ранее значение
	 *
	 * @param string $name
	 */
	public function unsetValue($name)
	{
		unset($this->data[$name]);
	}

	/**
	 * Генерирует робота
	 *
	 * @return string
	 */
	public function toString()
	{
		$output = array();
		foreach ($this->data as $key=>$val) {
			assert('is_array($val)');
			foreach($val as $val2) {
				$output[] = "{$key}: {$val2}\n";
			}
		}

		return join("", $output);
	}
}
