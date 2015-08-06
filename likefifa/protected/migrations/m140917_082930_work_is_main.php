<?php

/**
 * Добавляет работам колонку is_main, отмечающую ТОП10
 *
 * Class m140917_082930_work_is_main
 */
class m140917_082930_work_is_main extends CDbMigration
{
	public function up()
	{
		$this->addColumn(LfWork::model()->tableName(), 'is_main', 'TINYINT(1) NOT NULL DEFAULT 0');
		$this->createIndex('lf_work_is_main', LfWork::model()->tableName(), 'is_main');
	}

	public function down()
	{
		$this->dropIndex('lf_work_is_main', LfWork::model()->tableName());
		$this->dropColumn(LfWork::model()->tableName(), 'is_main');
	}
}