<?php
namespace dfs\docdoc\validators;

/**
 * Class PhoneValidator
 *
 * @package dfs\docdoc\validators
 */
class StringValidator extends \CValidator
{

	/**
	 * тип строки
	 *
	 * Возможные варианты
	 *
	 * word - слово, содержащее русские или латинские буквы, дефис и знак _
	 * russian_word - слово, содержащее русские буквы, дефис и знак _
	 * russian_fio - ФИО на русском языке, содержит русские буквы, дефис и пробел
	 *
	 * @var string
	 */
	public $type = 'word';

	/**
	 * флаг, возможно ли пустое значение
	 *
	 * @var bool
	 */
	public $allowEmpty = true;

	/**
	 * Правила для типов строк
	 * @var array
	 */
	private $rules = [
		'word'            => [
			'/^([a-zA-Zа-яА-ЯёЁ\-_]){1,}$/u',
			'Значение может содержать только буквы'
		],
		'russian_word'    => [
			'/^([а-яА-ЯёЁ\-_\s]){1,}$/u',
			'Значение может содержать только русские буквы'
		],
		'russian_fio'     => [
			'/^([а-яА-ЯёЁ \-]){1,}$/u',
			'Некорректное имя'
		],
		'latinCharacters' => [
			'/^([a-zA-Z\-_]){1,}$/u',
			'Значение может содержать только латинские символы'
		],
		'prefix'          => [
			'/^([a-zA-Z\-_]){1,}\.$/u',
			'Значение может содержать только латинские символы должно заканчиваться точкой'
		],
		'relativeUrl'     => [
			'/^\//',
			'Введите относительный URL'
		],
		'uid'             => [
			'/^([0-9a-zA-Z\-_]){1,}$/u',
			'Значение может содержать латинские символы, цифры, знак подчеркивания и дефис'
		],
	];

	/**
	 * Метод проверяет соответствие строки правилу
	 *
	 * @param \CModel $object    модель валидации
	 * @param string  $attribute атрибут, который валидируется в данный момент
	 *
	 * @throws \Exception
	 */
	protected function validateAttribute($object, $attribute)
	{
		$value = $object->$attribute;
		if ($this->allowEmpty && $this->isEmpty($value)) {
			return;
		}

		if (!is_string($value)) {
			$this->addError($object, $attribute, 'Некорректное тип значения');

			return;
		}

		if (!isset($this->rules[$this->type])) {
			throw new \Exception('StringValidator. Неизвестный тип строки');
		}

		$rule = $this->rules[$this->type];

		if (!preg_match($rule[0], $value)) {
			$this->addError($object, $attribute, $rule[1]);
		}
	}

}
