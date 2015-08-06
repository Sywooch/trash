<?php

namespace components;

/**
 * Class Validator
 *
 * Validates model fields
 *
 * @package components
 */
class Validator
{

	/**
	 * Model
	 *
	 * @var Model
	 */
	private $_model = null;

	/**
	 * Errors
	 *
	 * @var array
	 */
	private $_errors = [];

	/**
	 * Relation name
	 *
	 * @var string
	 */
	private $_relation = "t";

	/**
	 * Error messages
	 *
	 * @return array
	 */
	private $_errorMessages = [
		"required" => "Field must be filled",
		"max"      => "Field is too long"
	];

	/**
	 * Constructor
	 *
	 * @param Model  $model    model
	 * @param string $relation relation name
	 */
	public function __construct($model, $relation = "t")
	{
		$this->_model = $model;
		$this->_relation = $relation;
	}

	/**
	 * Validate model
	 *
	 * @return array
	 */
	public function validate()
	{
		foreach ($this->_model->rules() as $field => $types) {
			foreach ($types as $key => $value) {
				if (is_int($key)) {
					$value = "_" . $value;
					$this->$value($field);
				} else {
					$key = "_" . $key;
					$this->$key($field, $value);
				}
			}
		}

		if ($this->_errors) {
			$errors = [];

			foreach ($this->_errors as $key => $value) {
				$errors[$this->_relation . "." . $key] = $this->_errorMessages[$value];
			}

			return $errors;
		}

		return $this->_errors;
	}

	/**
	 * Checks to fill
	 *
	 * @param string $field field name
	 *
	 * @return void
	 */
	private function _required($field)
	{
		if (!array_key_exists($field, $this->_errors) && !$this->_model->$field) {
			$this->_errors[$field] = "required";
		}
	}

	/**
	 * Checks max length
	 *
	 * @param string $field field name
	 * @param int    $max   max value
	 *
	 * @return void
	 */
	private function _max($field, $max)
	{
		if (!array_key_exists($field, $this->_errors) && strlen($this->_model->$field) > $max) {
			$this->_errors[$field] = "max";
		}
	}
}