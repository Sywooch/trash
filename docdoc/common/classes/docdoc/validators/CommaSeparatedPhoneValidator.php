<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 11.12.14
 * Time: 13:48
 */

namespace dfs\docdoc\validators;

use dfs\docdoc\objects\Phone;

class CommaSeparatedPhoneValidator extends \CValidator
{
	/**
	 * флаг, возможно ли пустое значение
	 *
	 * @var bool
	 */
	public $allowEmpty = true;

	/**
	 * @var bool
	 */
	public $emptyToNull = true;

	/**
	 * Метод проверяет корректность номеров телефона, хранящихся через зпт
	 *
	 * @param \CModel $object    модель валидации
	 * @param string  $attribute атрибут, который валидируется в данный момент
	 */
	protected function validateAttribute($object, $attribute)
	{
		$value = $object->$attribute;

		if ($this->allowEmpty && $this->isEmpty($value)) {
			$this->emptyToNull && $object->$attribute = null;

			return;
		}

		$phones = explode(',', $object->$attribute);
		$validPhones = [];

		foreach ($phones as $value) {
			$phone = new Phone($value);

			if (!$phone->isValid()) {
				$this->addError($object, $attribute, 'Некорректный формат номера телефона');
				break;
			}

			$validPhones[] = $phone->getNumber();
		}

		$object->$attribute = implode(',', $validPhones);
	}
} 
