<?php

/**
 * Class m140828_140925_DD_110_add_client_phone_index
 */
class m140828_140925_DD_110_add_client_phone_index extends CDbMigration
{
	/**
	 * Добавляем индекс к полю add_client_phone
	 *
	 * @return bool|void
	 */
	public function up()
	{
		$this->createIndex('add_client_phone_index', 'request', 'add_client_phone');
	}

	/**
	 * Откат миграции
	 *
	 * @return bool|void
	 */
	public function down()
	{
		$this->dropIndex('add_client_phone_index', 'request');
	}
}