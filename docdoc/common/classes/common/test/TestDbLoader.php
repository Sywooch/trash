<?php
namespace dfs\common\test;

use RuntimeException;

/**
 * Class TestDbLoader
 *
 * Перегружает базу используя контрольную сумму
 *
 * Скрипт восстановления структуры базы данных
 *
 * @package dfs\common\test
 */
class TestDbLoader
{
	/**
	 * Имя файла со структурой базы данных
	 *
	 * @var string
	 */
	private $_dbFileName;

	/**
	 * @param string $dbFileName Имя файла со структурой базы данных
	 *
	 * @throws \RuntimeException
	 */
	public function __construct($dbFileName)
	{
		$this->setFileName($dbFileName);
		if ($this->isSchemaUpdated()){
			$this->load();
		}
	}

	/**
	 * Обновление базы данных
	 */
	private function load()
	{
		echo "Update Database" . PHP_EOL;
		$this->truncateDb($this->getDbName());
		$this->loadSchema();
		$this->updateCachedCheckSum();
	}

	/**
	 * Задаёт имя файла бызы
	 *
	 * @param string $dbFileName Имя файла со структурой базы данных
	 *
	 * @return $this
	 * @throws \RuntimeException
	 */
	public function setFileName($dbFileName)
	{
		if (!is_file($dbFileName) || !is_readable($dbFileName)) {
			throw new RuntimeException("Fail to read '{$dbFileName}'");
		}

		$this->_dbFileName = $dbFileName;

		return $this;
	}

	/**
	 * Очищает базу данных
	 *
	 * @param string $dbName
	 *
	 * @return int
	 */
	private function truncateDb($dbName)
	{
		return $this
			->getDb()
			->createCommand("
				DROP DATABASE {$dbName};
				CREATE DATABASE {$dbName};
				USE {$dbName};
			")
			->execute();
	}

	/**
	 * Активная база данных
	 *
	 * @return string
	 */
	public function getDbName()
	{
		return $this
			->getDb()
			->createCommand("SELECT DATABASE()")
			->queryScalar();
	}

	/**
	 * Загружает схему в базу
	 *
	 * @return int
	 */
	public function loadSchema()
	{
		$command = $this
			->getDb()
			->createCommand(
				file_get_contents($this->_dbFileName)
			);

		$command->prepare();
		$statement = $command->getPdoStatement();
		$statement->execute();

		//при выполнении multiply запроса будет показана ошибка только в случае, если ошибка возникла в первом запросе
		//чтобы определить были ли ошибки во всех запросах, нужно пробежаться по всем полученным результатам
		//если в каком-то из них была ошибка - nextRowset выбросит исключение
		$num = 0;
		while ($rowset = $statement->nextRowset()) {
			$num++;
		}

		return $num;
	}

	/**
	 * Подключение к базе данных
	 *
	 * @return \CDbConnection
	 */
	private function getDb()
	{
		return \Yii::app()->getDb();
	}

	/**
	 * Проверяет обновилась схема или нет
	 *
	 * @return bool
	 */
	public function isSchemaUpdated()
	{
		return $this->getCheckSum() !== $this->getCachedCheckSum();
	}

	/**
	 * Чексумма существующего файла
	 *
	 * @return string
	 */
	private function getCheckSum()
	{
		return md5_file($this->_dbFileName);
	}

	/**
	 * Сохранённая чек сумма структуры базы данных
	 *
	 * @return string|null
	 */
	private function getCachedCheckSum()
	{
		return is_file($this->getCheckSumFileName())
			? file_get_contents($this->getCheckSumFileName())
			: null;
	}

	/**
	 * Путь до файла с чексуммой
	 *
	 * @return string
	 */
	private function getCheckSumFileName()
	{
		return \Yii::app()->runtimePath . DIRECTORY_SEPARATOR . "DbSchema.md5";
	}

	/**
	 * Записывает чексумму во временный файл
	 *
	 * @return int
	 */
	private function updateCachedCheckSum()
	{
		return file_put_contents($this->getCheckSumFileName(), $this->getCheckSum());
	}
} 