<?php
namespace dfs\docdoc\validators;

use dfs\docdoc\objects\Phone;

/**
 * Class PhoneValidator
 *
 * @package dfs\docdoc\validators
 */
class PhoneValidator extends \CValidator
{
	/**
	 * флаг, возможно ли пустое значение
	 *
	 * @var bool
	 */
	public $allowEmpty = true;

	/**
	* Метод проверяет корректность номера телефона
	* @param \CModel $object модель валидации
	* @param string $attribute атрибут, который валидируется в данный момент
	*/
	protected function validateAttribute($object,$attribute){

		$value = $object->$attribute;
		if($this->allowEmpty && $this->isEmpty($value)) {
			return;
		}

		$phone = new Phone($value);

		if(!$phone->isValid()){
			$this->addError($object, $attribute, 'Некорректный формат номера телефона');
		}

		$object->$attribute = $phone->getNumber();
	}

}