<?php

use dfs\docdoc\objects\google\users\User;

/**
 * Class m150213_090219_DD_844_create_bq_client
 */
class m150213_090219_DD_844_create_bq_client extends CDbMigration
{
	/**
	 * Создать таблицу client в BQ
	 *
	 * @return bool|void
	 */
	public function up()
	{
		try {
			$bq = new \dfs\docdoc\objects\google\BigQuery();
			$bq->updateToken();
			$clientTable = new User();
			$clientTable->createTable($clientTable->getTable());
		} catch (Exception $e) {
			//сборка не должна падать, если не удастся создать таблицу
		}
	}

	/**
	 * @return bool|void
	 */
	public function down()
	{
		try {
			$bq = new \dfs\docdoc\objects\google\BigQuery();
			$bq->updateToken();
			$clientTable = new User();
			$clientTable->deleteTable($clientTable->getTable());
		} catch (Exception $e) {
			//сборка не должна падать, если не удастся удалить таблицу
		}
	}
}