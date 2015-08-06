<?php

namespace components;

/**
 * Abstract class Model
 *
 * Contains all common methods for models
 *
 * @package components
 */
abstract class Model
{

	/**
	 * Model ID
	 *
	 * @var integer
	 */
	public $id = 0;

	/**
	 * DB params
	 *
	 * @var Db
	 */
	protected $db;

	/**
	 * Errors
	 *
	 * @var array
	 */
	public $errors = [];

	/**
	 * Returns table name
	 *
	 * @return string
	 */
	abstract public function tableName();

	/**
	 * Returns relations
	 *
	 * @return array
	 */
	abstract public function relations();

	/**
	 * Returns rules
	 *
	 * @return array
	 */
	abstract public function rules();

	/**
	 * Returns model field labels
	 *
	 * @return array
	 */
	abstract public function labels();

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->db = new Db;
		$this->db->tableName = $this->tableName();
		$this->db->relations = $this->relations();
		$this->db->fields = array_keys($this->rules());
	}

	/**
	 * Select model by ID
	 *
	 * @param int $id ID
	 *
	 * @return Model
	 */
	public function byId($id)
	{
		$this->db->addCondition("t.id = :id");
		$this->db->params["id"] = $id;

		return $this;
	}

	/**
	 * Add relations for select
	 *
	 * @param string[] $relations relations
	 *
	 * @return Model
	 */
	public function with($relations) {
		foreach ($relations as $relation) {
			$this->db->with[] = $relation;
		}

		return $this;
	}

	/**
	 * Find model
	 *
	 * @return null|Model
	 */
	public function find()
	{
		$this->db->limit = 1;

		$result = $this->db->find();
		if (!$result) {
			return null;
		}

		/**
		 * @var Model $model
		 */
		$model = new $this;
		if (!$model->setAttributes($result, "__")) {
			return null;
		}

		return $model;
	}

	/**
	 * Find all models
	 *
	 * @return null|Model[]
	 */
	public function findAll()
	{
		$result = $this->db->findAll();
		if (!$result) {
			return [];
		}

		$list = [];

		foreach ($result as $values) {
			/**
			 * @var Model $model
			 */
			$model = new $this;
			$model->setAttributes($values, "__");
			if ($model) {
				$list[] = $model;
			}
		}

		return $list;
	}

	/**
	 * Set model attributes
	 *
	 * @param array  $values    field values
	 * @param string $separator separator
	 *
	 * @return bool
	 */
	public final function setAttributes($values, $separator = ".")
	{
		if (!is_array($values)) {
			return false;
		}

		$attributes = [];

		foreach ($values as $key => $val) {
			$explode = explode($separator, $key, 2);
			if (!empty($explode[1])) {
				$attributes[$explode[0]][$explode[1]] = $val;
			}
		}

		if (!$attributes) {
			return false;
		}

		$relations = $this->relations();
		foreach ($attributes as $key => $fields) {
			if ($key == "t") {
				foreach ($fields as $name => $value) {
					if (property_exists($this, $name)) {
						$this->$name = $value;
					}
				}
			} else if (property_exists($this, $key)) {
				if ($this->$key) {
					$model = $this->$key;
				} else {
					$model = new $relations[$key][0];
				}
				foreach ($fields as $name => $value) {
					if (property_exists($model, $name)) {
						$model->$name = $value;
					}
				}
				$this->$key = $model;
			}
		}

		return true;
	}

	/**
	 * Runs before validate
	 *
	 * @return void
	 */
	protected function beforeValidate()
	{

	}

	/**
	 * Validate model
	 *
	 * @param bool $isBeforeValidate
	 *
	 * @return bool
	 */
	public final function validate($isBeforeValidate = true)
	{
		if ($isBeforeValidate) {
			$this->beforeValidate();
		}

		$validator = new Validator($this);
		$this->errors = array_merge($this->errors, $validator->validate());
		foreach ($this->relations() as $relation => $options) {
			if ($this->$relation) {
				if ($isBeforeValidate) {
					$this->$relation->beforeValidate();
				}
				$validator = new Validator($this->$relation, $relation);
				$this->errors = array_merge($this->errors, $validator->validate());
			} else if (!$this->$options[1]) {
				$this->$relation = new $options[0];
				$validator = new Validator($this->$relation, $relation);
				$this->errors = array_merge($this->errors, $validator->validate());
			}
		}

		return !$this->errors;
	}

	/**
	 * Save model
	 *
	 * @param bool $useTransaction whether to use transaction
	 *
	 * @return bool
	 */
	public final function save($useTransaction = true)
	{
		if (!$this->validate()) {
			return false;
		}

		if ($useTransaction) {
			Db::startTransaction();
		}

		if ($this->beforeSave() === false) {
			if ($useTransaction) {
				Db::rollbackTransaction();
			}
			return false;
		}

		$data = [];
		foreach ($this->rules() as $field => $value) {
			$data[$field] = $this->$field;
		}

		if ($this->id) {
			if (!Db::update($this)) {
				if ($useTransaction) {
					Db::rollbackTransaction();
				}
				return false;
			}
		} else {
			$this->id = Db::insert($this);
			if (!$this->id) {
				if ($useTransaction) {
					Db::rollbackTransaction();
				}
				return false;
			}
		}

		if ($this->afterSave() === false) {
			if ($useTransaction) {
				Db::rollbackTransaction();
			}
			return false;
		}

		if ($useTransaction) {
			Db::commitTransaction();
		}
		return true;
	}

	/**
	 * Delete model
	 *
	 * @param bool $useTransaction whether to use transaction
	 *
	 * @return bool
	 */
	public final function delete($useTransaction = true)
	{
		if (!$this->id) {
			return false;
		}

		if ($useTransaction) {
			Db::startTransaction();
		}

		if ($this->beforeDelete() === false) {
			if ($useTransaction) {
				Db::rollbackTransaction();
			}
			return false;
		}

		if (!Db::delete($this)) {
			if ($useTransaction) {
				Db::rollbackTransaction();
			}
			return false;
		}

		if ($this->afterDelete() === false) {
			if ($useTransaction) {
				Db::rollbackTransaction();
			}
			return false;
		}

		if ($useTransaction) {
			Db::commitTransaction();
		}
		return true;
	}

	/**
	 * Runs before delete model
	 *
	 * @return bool
	 */
	protected function beforeSave()
	{
		foreach ($this->relations() as $relation => $options) {
			if ($this->$relation) {
				$field = $options[1];
				if (!$this->$relation->save(false)) {
					return false;
				}
				$this->$field = $this->$relation->id;
			}
		}

		return true;
	}

	/**
	 * Runs after save model
	 *
	 * @return bool
	 */
	protected function afterSave()
	{
		return true;
	}

	/**
	 * Runs before delete model
	 *
	 * @return bool
	 */
	protected function beforeDelete()
	{
		return true;
	}

	/**
	 * Runs after delete model
	 *
	 * @return bool
	 */
	protected function afterDelete()
	{
		return true;
	}

	/**
	 * Gets rules for field
	 *
	 * @param string $field поле
	 *
	 * @return string[]
	 */
	public final function getRules($field)
	{
		$rules = $this->rules();
		if (array_key_exists($field, $rules)) {
			return $rules[$field];
		}

		return [];
	}

	/**
	 * Gets field label
	 *
	 * @param string $field поле
	 *
	 * @return string
	 */
	public final function getLabel($field)
	{
		$labels = $this->labels();
		if (array_key_exists($field, $labels)) {
			return $labels[$field];
		}

		return "";
	}
}