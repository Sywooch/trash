<?php


class m140421_114757_add_partner_phone extends CDbMigration
{
	/**
	 * добавление ссылки на таблицу phone с телефоном партера
	 */
	public function safeUp()
	{
		$this->execute("ALTER TABLE `partner`
				ADD COLUMN `phone_id` INT(11) NULL DEFAULT NULL AFTER `cost_per_request`");

	}

	public function safeDown()
	{
		$this->execute("ALTER TABLE `partner` DROP COLUMN `phone_id`");
	}

}
