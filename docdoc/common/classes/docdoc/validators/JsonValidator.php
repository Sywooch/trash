<?php
namespace dfs\docdoc\validators;

/**
 * Class JsonValidator
 *
 * @package dfs\docdoc\validators
 */
class JsonValidator extends \CValidator
{
	/**
	 * флаг, возможно ли пустое значение
	 *
	 * @var bool
	 */
	public $allowEmpty = true;

	/**
	* Метод проверяет корректность JSON
	* @param \CModel $object модель валидации
	* @param string $attribute атрибут, который валидируется в данный момент
	*/
	protected function validateAttribute($object, $attribute)
	{
		$value = $object->$attribute;
		if($this->allowEmpty && $this->isEmpty($value)) {
			return;
		}

		$result = json_decode($value);

		if($result === null){
			$this->addError($object, $attribute, json_last_error_msg());
		}
	}

}