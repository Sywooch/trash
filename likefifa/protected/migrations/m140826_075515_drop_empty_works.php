<?php

/**
 * Удаляет работы без фотографий
 *
 * Class m140826_075515_drop_empty_works
 */
class m140826_075515_drop_empty_works extends CDbMigration
{
	public function up()
	{
		$this->execute("DELETE FROM lf_work WHERE image IS NULL");
	}

	public function down()
	{
		echo "m140826_075515_drop_empty_works does not support migration down.\n";
		return false;
	}
}