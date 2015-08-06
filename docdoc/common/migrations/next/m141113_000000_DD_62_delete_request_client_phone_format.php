<?php

use dfs\docdoc\models\RequestModel;
use dfs\docdoc\objects\Phone;

/**
 * Файл класса m141113_000000_DD_62_delete_request_client_phone_format
 *
 * Удаляет неиспользуемое поле client_phone_format в таблице request
 *
 * @author  Mikhail Vasilyev <mvasilyev@docdoc.ru>
 * @link    https://docdoc.atlassian.net/browse/DD-62
 * @package migrations
 */
class m141113_000000_DD_62_delete_request_client_phone_format extends CDbMigration
{

	/**
	 * Применяет миграцию
	 *
	 * @return void
	 */
	public function up()
	{
		$this->dropColumn("request", "client_phone_format");
	}

	/**
	 * Откатывает миграцию
	 *
	 * @return void
	 */
	public function down()
	{
		$this->addColumn("request", "client_phone_format", "VARCHAR(12)");
		$this->execute("UPDATE request SET client_phone_format = client_phone");
	}
}