<?php

/**
 * Блокирует мастеров с рейтингом меньше 3
 * Class m140811_101036_block_masters
 */
class m140811_101036_block_masters extends CDbMigration
{
	public function up()
	{
		$this->execute("UPDATE lf_master SET is_published = 0 WHERE rating < 3");
	}

	public function down()
	{
		echo "m140811_101036_block_masters does not support migration down.\n";
		return false;
	}
}