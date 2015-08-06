<?php
namespace dfs\docdoc\objects;

/**
 * Class Phone
 *
 * Объект номера телефона
 *
 * @package dfs\docdoc\objects
 */
class Phone
{

	/**
	 * Номер телефона
	 *
	 * @var string
	 */
	private $_number;

	/**
	 * создание объекта телефона
	 *
	 * @param string $number
	 */
	function __construct($number = '')
	{
		$this->setNumber($number);
	}

	/**
	 *	Магический метод __toString
	 *
	 * Используется в моделях для получения телефонов в модели в виде объекта Phone
	 * например, в модели можно создать метод
	 *
	 * function getPhoneAttributeName($number)
	 * {
	 * 	 return new Phone($number);
	 * }
	 *
	 * при этом получим
	 *
	 * $model->phoneAttributeName = '+7 (912) 123-12-12';
	 *
	 * echo $model->phoneAttributeName; //выведет 79121231212
	 * var_dump($model->phoneAttributeName->isValid()); //true
	 * echo $model->phoneAttributeName->prettyFormat('8 '); //выведет 8 (912) 123-12-12
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->_number;
	}

	/**
	 * Установка номера телефона.
	 *
	 * удаляет все, кроме цифр, проверяет первую 7
	 *
	 * @param string $number
	 */
	public function setNumber($number)
	{
		$this->_number = preg_replace('/[\D]/', '', $number);

		switch (strlen($this->_number)) {
			case 11:
				if (substr($this->_number, 0, 1) == 8) {
					$this->_number = '7' . substr($this->_number, 1, strlen($this->_number));
				}
				break;

			case 10:
				//  Считаем, что короткие номера - это наши номера - и добавляем для них семёрочку
				$this->_number = '7' . $this->_number;
				break;
		}
	}

	/**
	 * Проверка валидности номера телефона
	 *
	 * @return bool
	 */
	public function isValid()
	{
		return (bool)preg_match("/^(7)([0-9]){10}$/", $this->_number);
	}

	/**
	 * Геттер для номера телефона
	 *
	 * @return string отформатированный номер телефона
	 */
	public function getNumber()
	{
		return $this->_number;
	}

	/**
	 * Преобразует телефон из 74951234567 в красивый +7 (495) 123-45-67
	 *
	 * @param string $first на что заменить первую цифру телефона (8 , +7 , пусто)
	 *
	 * @return string красивый номер телефона
	 */
	public function prettyFormat($first = '8 ')
	{
		if (empty($this->_number)) {
			return '';
		}
		return $first ."(". substr($this->_number, 1, 3) . ") " .
			substr($this->_number, 4, 3) . "-" .
			substr($this->_number, 7, 2) . "-" .
			substr($this->_number, 9, 2);
	}

	/**
	 * Очистка номера телефона
	 *
	 * @param $number
	 *
	 * @return string
	 */
	public static function strToNumber($number) {
		$phone = new self($number);
		return $phone->getNumber();
	}

	/**
	 * Получает неполный номер для непроплаченных клиник
	 *
	 * @return string
	 */
	public function getIncompleteNumber()
	{
		return substr($this->prettyFormat(), 0, -4) . "...";
	}

	/**
	 * Форматирует начало телефона (+7 или 8)
	 * Приводит к виду +7ХХХХХХХХХХ или к 8800ХХХХХХХ
	 *
	 * @return Phone
	 */
	public function formatPrefix()
	{
		if (substr($this->_number, 1, 3) == 800) {
			$this->_number = 8 . substr($this->_number, 1);
		} else {
			$this->_number = "+" . $this->_number;
		}

		return $this;
	}
}
