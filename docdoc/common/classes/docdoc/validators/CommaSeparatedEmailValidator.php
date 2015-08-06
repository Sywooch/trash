<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 11.12.14
 * Time: 13:39
 */

namespace dfs\docdoc\validators;


class CommaSeparatedEmailValidator extends \CValidator
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
	 * Метод проверяет корректность емейлов через зпт
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

		$emails = explode(',', $object->$attribute);

		foreach ($emails as $e) {
			if (!filter_var($e, FILTER_VALIDATE_EMAIL)) {
				$this->addError($object, $attribute, 'Некорректный формат почтового ящика');
				break;
			}
		}
	}
} 
