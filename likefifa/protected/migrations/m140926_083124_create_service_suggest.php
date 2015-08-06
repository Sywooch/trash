<?php

/**
 * Создает таблицу для хранения триграмм
 */
class m140926_083124_create_service_suggest extends CDbMigration
{
	public function up()
	{
		$this->createTable(
			'lf_service_suggest',
			[
				'id'       => 'pk',
				'keyword'  => 'VARCHAR(255) NOT NULL',
				'trigrams' => 'VARCHAR(255) NOT NULL',
				'freq'     => 'INTEGER NOT NULL',
			]
		);
	}

	public function down()
	{
		$this->dropTable('lf_service_suggest');
	}
}