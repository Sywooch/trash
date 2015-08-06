<?php

namespace components;

use PDO;
use PDOException;

/**
 * Class Db
 *
 * All operations for DB
 *
 * @package components
 */
class Db
{

	/**
	 * Table name
	 *
	 * @var string
	 */
	public $tableName = "";

	/**
	 * Condition
	 *
	 * @var string
	 */
	public $condition = "";

	/**
	 * Params
	 *
	 * @var array
	 */
	public $params = [];

	/**
	 * Limit
	 *
	 * @var string
	 */
	public $limit = "";

	/**
	 * Sort
	 *
	 * @var string
	 */
	public $order = "";

	/**
	 * Relation names
	 *
	 * @var string[]
	 */
	public $with = [];

	/**
	 * Fields for select
	 *
	 * @var string[]
	 */
	public $fields = [];

	/**
	 * PDO model
	 *
	 * @var PDO
	 */
	private static $_pdo;

	/**
	 * Relations
	 *
	 * @var array
	 */
	public $relations = [];

	/**
	 * Set PDO model
	 *
	 * @return bool
	 */
	public static function setPdo($host, $user, $password, $dbName)
	{
		try {
			self::$_pdo = new PDO(
				"mysql:host={$host};dbname={$dbName};charset=UTF8",
				$user,
				$password,
				[
					PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
					PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
					PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
				]
			);
		} catch (PDOException $e) {
			return false;
		}

		return true;
	}

	/**
	 * Makes SQL query
	 *
	 * @return string
	 */
	private function _getQuery()
	{
		$join = [];
		$select = [];

		$select[] = "t.id AS t__id";
		foreach ($this->fields as $field) {
			$select[] = "t.{$field} AS t__{$field}";
		}

		foreach ($this->with as $with) {
			$relation = $this->relations[$with];
			/**
			 * @var Model $class
			 */
			$class = new $relation[0];
			$select[] = $class->tableName() . ".id AS {$with}__id";
			foreach (array_keys($class->rules()) as $field) {
				$select[] = $class->tableName() . ".{$field} AS {$with}__{$field}";
			}

			$this->condition = str_replace("{$with}.", $class->tableName() . ".", $this->condition);
			$this->order = str_replace("{$with}.", $class->tableName() . ".", $this->order);
			$join[] =
				" LEFT JOIN " .
				$class->tableName() .
				" ON t." .
				$relation[1] .
				" = " .
				$class->tableName() .
				".id";
		}

		$query = "SELECT " . implode(", ", $select);
		$query .= " FROM " . $this->tableName . " AS t";

		foreach ($join as $item) {
			$query .= $item;
		}

		if ($this->condition) {
			$query .= " WHERE {$this->condition}";
		}

		if ($this->limit) {
			$query .= " LIMIT {$this->limit}";
		}

		if ($this->order) {
			$query .= " ORDER BY {$this->order}";
		}

		return $query;
	}

	/**
	 * Fetch one row
	 *
	 * @return array
	 */
	public function find()
	{
		return self::fetch($this->_getQuery(), $this->params);
	}

	/**
	 * Fetch all rows
	 *
	 * @return array
	 */
	public function findAll()
	{
		return self::fetchAll($this->_getQuery(), $this->params);
	}

	/**
	 * Add condition
	 *
	 * @param string $condition condition
	 * @param string $operator  operator
	 *
	 * @return bool
	 */
	public function addCondition($condition = "", $operator = "AND")
	{
		if (!$condition) {
			return false;
		}

		if ($this->condition) {
			$this->condition .= " {$operator} {$condition}";
		} else {
			$this->condition = $condition;
		}

		return true;
	}

	/**
	 * Execute query
	 *
	 * @param string $condition condition
	 * @param array  $params    params
	 *
	 * @return bool
	 */
	public static function execute($condition, $params = [])
	{
		return self::$_pdo->prepare($condition)->execute($params);
	}

	/**
	 * Fetch one row
	 *
	 * @param string $condition condition
	 * @param array  $params    params
	 *
	 * @return array
	 */
	public static function fetch($condition, $params = [])
	{
		$sth = self::$_pdo->prepare($condition);
		$sth->execute($params);

		return $sth->fetch();
	}

	/**
	 * Fetch all rows
	 *
	 * @param string $condition condition
	 * @param array  $params    params
	 *
	 * @return array
	 */
	public static function fetchAll($condition, $params = [])
	{
		$sth = self::$_pdo->prepare($condition);
		$sth->execute($params);

		return $sth->fetchAll();
	}

	/**
	 * Start transaction
	 *
	 * @return void
	 */
	public static function startTransaction()
	{
		self::$_pdo->beginTransaction();
	}

	/**
	 * Commit transaction
	 *
	 * @return void
	 */
	public static function commitTransaction()
	{
		self::$_pdo->commit();
	}

	/**
	 * Rollback transaction
	 *
	 * @return void
	 */
	public static function rollbackTransaction()
	{
		self::$_pdo->rollBack();
	}

	/**
	 * Insert new record
	 * Returns new ID
	 *
	 * @param Model $model model
	 *
	 * @return int
	 */
	public static function insert($model)
	{
		$columns = [];
		$values = [];
		$substitutions = [];

		foreach ($model->rules() as $field => $value) {
			$columns[] = $field;
			$substitutions[] = "?";
			$values[] = $model->$field;
		}

		$query =
			"INSERT INTO " .
			$model->tableName() .
			" (" .
			implode(",", $columns) .
			") VALUES (" .
			implode(",", $substitutions) .
			")";
		if (!self::execute($query, $values)) {
			return 0;
		}

		return self::$_pdo->lastInsertId();
	}

	/**
	 * Update record
	 *
	 * @param Model $model model
	 *
	 * @return bool
	 */
	public static function update($model)
	{
		$sets = [];
		$values = [];

		foreach ($model->rules() as $field => $value) {
			$sets[] = "$field = ?";
			$values[] = $model->$field;
		}

		$values[] = $model->id;

		$query = "UPDATE " . $model->tableName() . " SET " . implode(",", $sets) . " WHERE id = ?";

		return self::execute($query, $values);
	}

	/**
	 * Delete record
	 *
	 * @param Model $model model
	 *
	 * @return bool
	 */
	public static function delete($model)
	{
		return self::execute("DELETE FROM " . $model->tableName() . " WHERE id = ?", [$model->id]);
	}
}