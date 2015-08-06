<?php

/**
 * Class m141212_091126_DD_676_add_request_token
 */
class m141212_091126_DD_676_add_request_token extends CDbMigration
{
	/**
	 * Добавляем поле token
	 *
	 * @return bool|void
	 */
	public function up()
	{
		$this->addColumn('request', 'token', 'char(32) DEFAULT NULL');
		$this->createIndex('token', 'request', 'token');
	}

	/**
	 * @return bool
	 */
	public function down()
	{
		$this->dropColumn('request', 'token');
	}
}