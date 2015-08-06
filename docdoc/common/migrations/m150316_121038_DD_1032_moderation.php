<?php

/**
 * Обозначение новых или удалённых элементов для модерации
 */
class m150316_121038_DD_1032_moderation extends CDbMigration
{
	public function up()
	{
		$this->addColumn('moderation', 'is_new', 'tinyint(1) NOT NULL DEFAULT 0');
		$this->addColumn('moderation', 'is_delete', 'tinyint(1) NOT NULL DEFAULT 0');
	}

	public function down()
	{
		$this->dropColumn('moderation', 'is_new');
		$this->dropColumn('moderation', 'is_delete');
	}
}
