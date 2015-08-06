<?php


namespace likefifa\components\console;

use CDbConnection;
use likefifa\components\config\Environment;
use Yii;

Yii::import('system.cli.commands.MigrateCommand');

class MigrateCommand extends \MigrateCommand
{
	/**
	 * Каталог с файлом схемы
	 *
	 * @var string
	 */
	public $schemaFilePath;

	/**
	 * Название файла схемы
	 *
	 * @var string
	 */
	public $schemaName = 'schema.mysql.sql';

	/**
	 * Действие выполняется после окончания действий команды
	 *
	 * @param string $action
	 * @param array  $params
	 * @param int    $exitCode
	 *
	 * @return int
	 */
	public function afterAction($action, $params, $exitCode = 0)
	{
		if(!Environment::isStage() && !Environment::isProduction()) {
			$this->updateSchemaFile();
		}

		return parent::afterAction($action, $params, $exitCode);
	}

	/**
	 * Обновляет файл схемы данных. Файл нужен для заупска тестов
	 */
	protected function updateSchemaFile()
	{
		echo 'Create ' . $this->schemaName . ' file...', PHP_EOL;

		$file = Yii::getPathOfAlias($this->schemaFilePath) . DIRECTORY_SEPARATOR . $this->schemaName;
		$tables = Yii::app()->db->createCommand('SHOW TABLES')->queryColumn();
		$data = [
			'SET foreign_key_checks = 0',
		];
		foreach ($tables as $table) {
			$createData = Yii::app()->db->createCommand('SHOW CREATE TABLE ' . $table)->queryRow();
			$createQuery = array_values($createData)[1];
			$data[] = preg_replace('|AUTO_INCREMENT=(\d+)|', '', $createQuery);
		}

		$schemaData = implode(";\r\n\r\n", $data);

		file_put_contents($file, $schemaData);
	}
} 