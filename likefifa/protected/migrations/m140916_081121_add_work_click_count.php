<?php

/**
 * Добавляет колонку с количеством кликов для работ
 *
 * Class m140916_081121_add_work_click_count
 */
class m140916_081121_add_work_click_count extends CDbMigration
{
	public function up()
	{
		$this->addColumn('lf_work', 'click_count', 'INT UNSIGNED NOT NULL DEFAULT 0');
	}

	public function down()
	{
		$this->dropColumn('lf_work', 'click_count');
	}
}