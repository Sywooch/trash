<?php

class m140820_091452_DD_136_add_mobile_phone extends CDbMigration
{
	public function up()
	{
		$this->execute("ALTER TABLE `clinic`
			ADD COLUMN `phone_mobile` CHAR(12) NULL DEFAULT NULL AFTER `phone_appointment`");
	}

	/**
	 * Откатывает миграцию
	 *
	 * @return void
	 */
	public function down()
	{
		$this->dropColumn("clinic", "phone_mobile");
	}
}