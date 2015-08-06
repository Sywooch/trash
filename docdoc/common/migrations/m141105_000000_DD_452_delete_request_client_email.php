<?php

/**
 * Файл класса m141105_000000_DD_452_delete_request_client_email
 *
 * Удаляет неиспользуемое поле client_email в таблице request
 *
 * @author  Mikhail Vasilyev <mvasilyev@docdoc.ru>
 * @link    https://docdoc.atlassian.net/browse/DD-452
 * @package migrations
 */
class m141105_000000_DD_452_delete_request_client_email extends CDbMigration
{

	/**
	 * Применяет миграцию
	 *
	 * @return void
	 */
	public function up()
	{
		$this->dropColumn("request", "client_email");
	}

	/**
	 * Откатывает миграцию
	 *
	 * @return void
	 */
	public function down()
	{
		$this->addColumn("request", "client_email", "VARCHAR(255)");
	}
}