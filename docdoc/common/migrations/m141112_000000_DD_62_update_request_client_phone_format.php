<?php

/**
 * Файл класса m141112_000000_DD_62_update_request_client_phone_format
 *
 * Переписывает поле client_phone используя client_phone_format в таблице request
 *
 * @author  Mikhail Vasilyev <mvasilyev@docdoc.ru>
 * @link    https://docdoc.atlassian.net/browse/DD-62
 * @package migrations
 */
class m141112_000000_DD_62_update_request_client_phone_format extends CDbMigration
{

	/**
	 * Применяет миграцию
	 *
	 * @return void
	 */
	public function up()
	{
		$this->execute(
			"
				UPDATE request
				SET client_phone = client_phone_format
				WHERE client_phone_format IS NOT NULL AND client_phone_format != ''
			"
		);
	}
}