<?php

class m140529_131251_delete_sip_from_user extends CDbMigration
{
	/**
	 * удаление поля SIP с таблицы user
	 */
	public function safeUp()
	{
		$this->execute("ALTER TABLE `user` DROP COLUMN `SIP`");
		$this->execute("ALTER TABLE `user` DROP COLUMN `user_sip`");
	}

	public function safeDown()
	{
		$this->execute("ALTER TABLE `user` ADD COLUMN `SIP` INT(11) DEFAULT NULL");
		$this->execute("ALTER TABLE `user` ADD COLUMN `user_sip` TINYINT(4) DEFAULT NULL");
	}
}