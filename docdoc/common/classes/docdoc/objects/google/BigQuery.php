<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 12.11.14
 * Time: 15:40
 */

namespace dfs\docdoc\objects\google;

use Curl;
use Yii;
use dfs\docdoc\exceptions\GoogleBqInsertException;
use dfs\docdoc\models\GoogleBigQueryModel;
use dfs\docdoc\objects\google\requests\Requests;

/**
 * Базовая модель для таблиц google bigquery
 *
 * Class BigQuery
 *
 * @package dfs\docdoc\objects\google
 *
 */
class BigQuery extends \CComponent
{
	/**
	 * Сколько записей сбрасывать в Big Query за раз
	 */
	const FLUSH_SIZE = 300;

	/**
	 * @var GoogleBigQueryModel
	 */
	protected $model;

	/**
	 * Проект в big query
	 *
	 * @var
	 */
	protected $_project;

	/**
	 * Датасет
	 *
	 * @var string
	 */
	public $dataset = null;

	/**
	 * @var null
	 */
	public $baseTable = null;

	/**
	 * @var array
	 */
	public $fields = [];

	/**
	 * Реальное имя таблицы с учетом имени контура
	 *
	 * @var string
	 */
	protected $_table = null;

	/**
	 * Конструктор
	 *
	 * @param null $project
	 */
	public function __construct($project = null)
	{
		if (is_null($project)) {
			$this->_project = \Yii::app()->params['google_big_query']['project'];
		}

		$this->setTableName();
	}

	/**
	 * Получение модели по имени таблицы
	 *
	 * @param $dataset
	 * @param $table
	 *
	 * @return null | BigQuery
	 */
	public static function getModel($dataset, $table)
	{

		//костыль для динамических имен таблиц
		if (
			$dataset == 'requests'
			&& (mb_substr($table, 0, 8) == "requests" || mb_substr($table, 0, 12) == "answer_speed")
		) {
			$className = Requests::getModelClassName($table);
		} else {
			$className = self::getModelClassName($table);
		}

		$class = __NAMESPACE__ . "\\{$dataset}\\{$className}";
		if (class_exists($class)) {
			return new $class();
		}
		return null;
	}

	/**
	 * @return GoogleBigQueryModel
	 */
	protected function getTokenModel()
	{
		if ($model = GoogleBigQueryModel::model()->find()) {
			$this->model = $model;
		} else {
			$this->model = new GoogleBigQueryModel();
		}

		return $this->model;
	}

	/**
	 * Имя столбца с первичным ключем
	 * если какое-то поле играет роль первичного ключа метод должен возвращать имя столбца
	 * если метод не переопределен, то будет использоваться счетчик
	 *
	 * @return null|string
	 */
	public function getPrimaryKey()
	{
		return null;
	}

	/**
	 * Получение имени класса по имени таблицы
	 *
	 * @param string $table
	 *
	 * @return string
	 */
	public static function getModelClassName($table)
	{
		$t = explode("_", $table);
		$className = "";
		$bqEnv = empty(Yii::app()->params['google_big_query']['env']) ? "" : Yii::app()->params['google_big_query']['env'];
		foreach ($t as $v) {
			if ($v != $bqEnv) {
				$className .= ucfirst($v);
			}
		}

		return $className;
	}

	/**
	 * Получить имя таблицы с суффиксом для окружения
	 *
	 * @return null
	 */
	public function getTable()
	{
		return $this->_table;
	}

	/**
	 * Установка имени таблицы с учетом окружения
	 *
	 */
	public function setTableName()
	{
		$bqEnv = empty(Yii::app()->params['google_big_query']['env']) ? "" : "_" . Yii::app()->params['google_big_query']['env'];
		$this->_table = $this->baseTable . $bqEnv;
	}

	/**
	 * Обновление токена
	 *
	 * @return bool
	 * @throws \CException
	 */
	public function updateToken()
	{
		$curl = new Curl();

		$curl->headers = [
			'Cache-Control' => 'no-cache',
			'Content-Type' => 'application/x-www-form-urlencoded',
		];

		$client_id = \Yii::app()->params['google_big_query']['client_id'];
		$client_secret = \Yii::app()->params['google_big_query']['client_secret'];
		$refresh_token = \Yii::app()->params['google_big_query']['refresh_token'];

		$data = [
			'refresh_token' => $refresh_token,
			'client_id' => $client_id,
			'client_secret' => $client_secret,
			'grant_type' => 'refresh_token'
		];

		$data = http_build_query($data);
		$resp = $curl->post('https://accounts.google.com/o/oauth2/token', $data);
		$decoded = json_decode($resp->body, true);

		if (isset($decoded['access_token'])) {
			$model = $this->getTokenModel();
			$model->token = $decoded['access_token'];
			return $model->save();
		} else {
			throw new \CException($resp);
		}
	}

	/**
	 * Вставка данных в big query
	 *
	 * @param array $data
	 *
	 * @return bool
	 * @throws \CException
	 * @throws GoogleBqInsertException
	 */
	public function insertAll(array $data)
	{
		if (!$this->getTokenModel()->token) {
			throw new \CException('Не задан токен для google big data');
		} else {
			$_data['kind'] = 'bigquery#tableDataInsertAllRequest';
			$_data['rows'] = [];

			if (is_null($this->getTableSchema($this->_table))) {
				$this->createTable($this->_table);
			}

			//свой каунтер, ключ в массиве может не начинаться с 0 и получитя rows после json_encode как объект
			$i = 0;
			foreach ($data as $row) {
				if (is_string($row)) {
					$row = json_decode($row, 1);
				}

				if (isset($row['insertId'])) {
					$_data['rows'][$i]['insertId'] = $row['insertId'];
					unset($row['insertId']);
				}

				if (empty($row)) {
					$insertException = new GoogleBqInsertException('пустой датасет');
					$insertException->setInsertErrors([
						[
							"index" => $i,
							"errors" => [
								["reason" => GoogleBqInsertException::REASON_INVALID, "message" => 'пустой датасет'],
							]
						],
					]);
					throw $insertException;
				}

				$_data['rows'][$i]['json'] = $row;
				$i++;
			}

			$jsonData = json_encode($_data);

			$curl = new \Curl();
			$curl->headers['Content-Type'] = 'application/json';
			$curl->headers['Authorization'] = 'Bearer ' . $this->getTokenModel()->token;
			$curl->headers['Cache-Control'] = 'no-cache';
			//вставляем в таблицу

			$url = "https://www.googleapis.com/bigquery/v2/projects/{$this->_project}/datasets/{$this->dataset}/tables/{$this->_table}/insertAll";
			$resp = $curl->post($url, $jsonData);

			$respJson = json_decode($resp->body, true);

			if (isset($respJson['error'])) {
				throw new \CException('Ошибка инсерта в Google Big Query ' . $resp->body);
			}

			if (isset($respJson['insertErrors'])) {
				$insertException = new GoogleBqInsertException('Ошибка инсерта в гугл биг дата ' . $resp->body);
				$insertException->setInsertErrors($respJson['insertErrors']);
				throw $insertException;
			}

			return true;
		}
	}

	/**
	 * Очитска таблицы
	 *
	 * @return bool
	 */
	public function clearTable()
	{
		//@TODO убрать этот костыль
		$this->updateToken();

		$table = $this->getTable();

		if (!empty($this->getTableSchema($table))) {
			$this->deleteTable($table);
		}
		$this->createTable($table);

		return true;
	}

	/**
	 * Вставка данных в big query
	 *
	 * @return bool
	 * @throws \CException
	 */
	public static function flush()
	{
		$tables = new \ARedisSet("BQ.BQ_TABLES");
		foreach ($tables as $v) {
			list($dataset, $table) = explode(".", $v);
			$bqModel = self::getModel($dataset, $table);

			if ($bqModel) {
				$bqModel->_table = $table;
				$bqModel->flushTable();
			}
		}
	}

	/**
	 * Сброс таблицы в Google BQ
	 *
	 */
	public function flushTable()
	{
		$list = new \ARedisList("BQ.{$this->dataset}.{$this->_table}");

		$num = ceil($list->getCount() / self::FLUSH_SIZE);
		for ($i = 0; $i < $num; $i++) {
			$range = $list->range(0, self::FLUSH_SIZE);
			if (!empty($range)) {
				$this->insertItems($list, $range);
			}
		}
	}

	/**
	 * отправка данных в BQ и обработка ответа
	 *
	 * @param \ARedisList $list
	 * @param array $insertArr
	 */
	private function insertItems($list, $insertArr)
	{
		try {
			$this->insertAll($insertArr);
			foreach ($insertArr as $removeItem) {
				$list->remove($removeItem);
			}
		} catch (GoogleBqInsertException $e) {
			foreach ($insertArr as $j => $removeItem) {
				$error = $e->getErrorInfo($j);
				if (!$error || !$e->isRetry($error['reason'])) {
					\Yii::log("Google Big Query error. Reason: " . $error['reason'] . ". Data: " . $removeItem);
					$list->remove($removeItem);
				}
			}
		}
	}

	/**
	 * Добавление запроса в очередь
	 *
	 * @param array $data Только одна строка инсерта
	 *
	 * @return bool
	 */
	public function add(array $data)
	{
		$tables = new \ARedisSet("BQ.BQ_TABLES");
		$tables->add($this->dataset . "." . $this->_table);

		$counter = new \ARedisCounter("BQ.{$this->dataset}.{$this->_table}.counter");
		$counter->increment();

		$pk = $this->getPrimaryKey();
		$data['insertId'] = ($pk !== null && isset($data[$pk])) ? $data[$pk] : $counter->getValue();

		$list = new \ARedisList("BQ.{$this->dataset}.{$this->_table}");
		return $list->add(json_encode($data));
	}

	/**
	 * Вставка массива строк
	 *
	 * @param array $data
	 */
	public function addAll(array $data)
	{
		foreach ($data as $row) {
			$this->add($row);
		}
	}

	/**
	 * Создание таблицы
	 *
	 * @param string $table
	 *
	 * @throws \dfs\docdoc\exceptions\GoogleBqInsertException
	 * @throws \CException
	 */
	public function createTable($table)
	{
		$schema = [
			'kind' => 'bigquery#table',
			'schema' => [
				'fields' => $this->fields,
			],
			'tableReference' => [
				'projectId' => $this->_project,
				'datasetId' => $this->dataset,
				'tableId' => $table,
			]
		];
		$jsonData = json_encode($schema);

		$curl = new \Curl();
		$curl->headers['Content-Type'] = 'application/json';
		$curl->headers['Authorization'] = 'Bearer ' . $this->getTokenModel()->token;
		$curl->headers['Cache-Control'] = 'no-cache';

		$url = "https://www.googleapis.com/bigquery/v2/projects/{$this->_project}/datasets/{$this->dataset}/tables";
		$resp = $curl->post($url, $jsonData);

		$respJson = json_decode($resp->body, true);

		if (isset($respJson['error'])) {
			throw new \CException('Ошибка создания таблицы ' . $table . ' в Google Big Query ' . $resp->body);
		}
	}

	/**
	 * Удаление таблицы
	 *
	 * @param $table
	 *
	 * @throws \CException
	 */
	public function deleteTable($table)
	{
		$curl = new \Curl();
		$curl->headers['Content-Type'] = 'application/json';
		$curl->headers['Authorization'] = 'Bearer ' . $this->getTokenModel()->token;
		$curl->headers['Cache-Control'] = 'no-cache';

		$url = "https://www.googleapis.com/bigquery/v2/projects/{$this->_project}/datasets/{$this->dataset}/tables/{$table}";
		$resp = $curl->delete($url);

		$respJson = json_decode($resp->body, true);

		if (isset($respJson['error'])) {
			throw new \CException('Ошибка удаления таблицы в Google Big Query ' . $resp->body);
		}
	}

	/**
	 * Получение схемы таблицы
	 *
	 * @param $table
	 *
	 * @return mixed|null
	 * @throws \CException
	 */
	public function getTableSchema($table)
	{
		$curl = new \Curl();
		$curl->headers['Content-Type'] = 'application/json';
		$curl->headers['Authorization'] = 'Bearer ' . $this->getTokenModel()->token;
		$curl->headers['Cache-Control'] = 'no-cache';

		$url = "https://www.googleapis.com/bigquery/v2/projects/{$this->_project}/datasets/{$this->dataset}/tables/{$table}";
		$resp = $curl->get($url);

		$respJson = json_decode($resp->body, true);

		if (isset($respJson['error'])) {
			return null;
		}

		return !empty($resp) ? json_decode($resp) : null;
	}
} 
