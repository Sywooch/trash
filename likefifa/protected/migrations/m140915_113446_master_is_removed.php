<?php

/**
 * Добавляет колонку, помечающую мастера как удаленного
 *
 * Class m140915_113446_master_is_removed
 */
class m140915_113446_master_is_removed extends CDbMigration
{
	public function up()
	{
		$this->alterColumn('lf_master', 'is_blocked', 'TINYINT(1) NOT NULL AFTER `is_published`');
		$this->addColumn('lf_master', 'is_removed', 'TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_blocked`');
	}

	public function down()
	{
		$this->dropColumn('lf_master', 'is_removed');
	}
}