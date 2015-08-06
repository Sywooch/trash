<?php

/**
 * Добавляет колонку is_free в таблицу мастеров
 *
 * Class m140718_064559_create_master_free
 */
class m140718_064559_create_master_free extends CDbMigration
{
	public function up()
	{
		$this->addColumn('lf_master', 'is_free', 'boolean DEFAULT 0');
		$this->createIndex('master_is_free', 'lf_master', 'is_free');
	}

	public function down()
	{
		$this->dropIndex('master_is_free', 'lf_master');
		$this->dropColumn('lf_master', 'is_free');
	}
}