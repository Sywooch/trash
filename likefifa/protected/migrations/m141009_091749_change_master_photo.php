<?php

/**
 * Изменяет длину поля аватарки мастера и лого салона
 */
class m141009_091749_change_master_photo extends CDbMigration
{
	public function up()
	{
		$this->alterColumn(LfMaster::model()->tableName(), 'photo', 'VARCHAR(100) NULL DEFAULT NULL');
		$this->alterColumn(LfSalon::model()->tableName(), 'logo', 'VARCHAR(100) NULL DEFAULT NULL');
		$this->dropColumn('article', 'image');
	}

	public function down()
	{
		$this->alterColumn(LfMaster::model()->tableName(), 'photo', 'VARCHAR(32) NULL DEFAULT NULL');
		$this->alterColumn(LfSalon::model()->tableName(), 'logo', 'VARCHAR(32) NULL DEFAULT NULL');
		$this->addColumn('article', 'image', 'VARCHAR(256)');
	}
}