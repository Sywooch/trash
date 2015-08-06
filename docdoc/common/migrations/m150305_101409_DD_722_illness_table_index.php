<?php

/**
 * Добавление индексов для таблицы заболеваний
 */
class m150305_101409_DD_722_illness_table_index extends CDbMigration
{
	public function up()
	{
		$this->createIndex('illness_sector_id_key', 'illness', 'sector_id');
		$this->createIndex('illness_rewrite_name_key', 'illness', 'rewrite_name');
		$this->createIndex('illness_name_key', 'illness', 'name');
	}

	public function down()
	{
		$this->dropIndex('illness_sector_id_key', 'illness');
		$this->dropIndex('illness_rewrite_name_key', 'illness');
		$this->dropIndex('illness_name_key', 'illness');
	}
}
