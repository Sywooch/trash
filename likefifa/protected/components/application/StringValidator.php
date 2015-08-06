<?php

namespace likefifa\components\application;

use CValidator;
use Exception;
use CModel;

/**
 * Файл класса StringValidator
 *
 * Класс, описывающий правила валидации
 *
 * @author  Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @link    https://docdoc.megaplan.ru/task/1003365/card/
 * @package components.common
 */
class StringValidator extends CValidator
{

	/**
	 * Тип строки
	 *
	 * @var string
	 */
	public $type = "";

	/**
	 * Возможно ли пустое значение
	 *
	 * @var bool
	 */
	public $allowEmpty = true;

	/**
	 * Правила для строк
	 *
	 * @var string[]
	 */
	private $_rules = array(
		'rewriteName' => array(
			'/^[a-z_-]+$/u',
			'Значение может содержать символы латинского алфавита в нижнем регистре, нижнее подчеркивание и тире'
		),
		'prefix'      => array(
			'/^([a-z\-_]){1,}\.$/u',
			'Значение может содержать только латинские символы в нижнем регистре и тире. Строка должна заканчиваться
				точкой'
		),
		'russianWord' => array(
			'/^([а-яА-ЯёЁ\-_\s]){1,}$/u',
			'Значение может содержать только русские буквы и знак пробела'
		),
	);

	/**
	 * Метод проверяет соответствие строки правилу
	 *
	 * @param CModel $object    модель валидации
	 * @param string $attribute атрибут, который валидируется в данный момент
	 *
	 * @throws Exception
	 *
	 * @return bool
	 */
	protected function validateAttribute($object, $attribute)
	{
		$value = $object->$attribute;

		if ($this->isEmpty($value)) {
			if ($this->allowEmpty) {
				return true;
			} else {
				$this->addError($object, $attribute, 'Значение не может быть пустым');
				return false;
			}
		}

		if (!is_string($value)) {
			$this->addError($object, $attribute, 'Значение не является строкой');
			return false;
		}

		if (empty($this->_rules[$this->type])) {
			throw new Exception('StringValidator. Неизвестный тип строки');
		}

		$rule = $this->_rules[$this->type];
		if (!preg_match($rule[0], $value)) {
			$this->addError($object, $attribute, $rule[1]);
			return false;
		}

		return true;
	}
}