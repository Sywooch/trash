<?php

/**
 * Удаляет работы из специализации "эпиляция"
 *
 * Class m140911_084004_remove_epilacia_works
 */
class m140911_084004_remove_epilacia_works extends CDbMigration
{
	public function up()
	{
		$this->delete(LfWork::model()->tableName(), 'specialization_id = 18');
	}

	public function down()
	{
		echo "m140911_084004_remove_epilacia_works does not support migration down.\n";
		return false;
	}
}