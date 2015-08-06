<?php
namespace dfs\docdoc\reports;


/**
 * Базовый класс для отчетов в bigQuery
 *
 *
 * Class BigQueryReport
 * @package dfs\docdoc\reports
 */
abstract class BigQueryReport
{
	/**
	 * Максимальное кол-во записей в $data
	 */
	const MAX_COUNT_ITEMS = 1000;

	/**
	 * Данные отчета
	 *
	 * @var array
	 */
	protected $data = [];

	/**
	 * класс модели Bq
	 *
	 * @return \dfs\docdoc\objects\google\BigQuery
	 */
	abstract function getBqModel();


	/**
	 * Получение данных отчета
	 *
	 * @return array
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * Добавление данных в отчет
	 *
	 * @param array $row
	 * @param bool $autoFlush
	 *
	 * @return array
	 */
	public function addData($row, $autoFlush = false)
	{
		if ($autoFlush && count($this->data) > self::MAX_COUNT_ITEMS) {
			$this->insertIntoBigQuery();
			$this->data = [];
		}

		return $this->data[] = $row;
	}

	/**
	 * Вставить отчет в google biq query
	 *
	 *
	 * @return bool
	 */
	public function insertIntoBigQuery()
	{
		$bqModel = $this->getBqModel();

		$bqModel->addAll($this->data);
	}

	/**
	 * Очистка таблицы
	 *
	 * @return bool
	 */
	public function clear()
	{
		return $this->getBqModel()->clearTable();
	}
} 
