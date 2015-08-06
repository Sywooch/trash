<?php

/**
 * Class m140702_130522_add_master_comment
 * Задача #3975
 * Добавляет колонку комментария в таблицу мастеров
 */
class m140702_130522_add_master_comment extends CDbMigration
{
	public function up()
	{
		$this->addColumn('lf_master', 'comment', 'TEXT NULL DEFAULT NULL AFTER `rating_diff`');
		$this->createIndex('created', 'lf_master', 'created');
	}

	public function down()
	{
		$this->dropIndex('created', 'lf_master');
		$this->dropColumn('lf_master', 'created');
	}
}