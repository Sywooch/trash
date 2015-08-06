<?php

class m140522_122734_add_advert_to_clinic extends CDbMigration
{
	/**
	 * добавление признака - показывать в объявлении
	 */
	public function safeUp()
	{
		$this->execute("ALTER TABLE `clinic`
				ADD COLUMN `show_in_advert` TINYINT(1) DEFAULT 0 AFTER `diag_settings_id`");
		$this->execute("UPDATE clinic SET show_in_advert = 1
				WHERE id IN (1419, 1150, 546, 2044, 1, 1930, 2057, 2056, 249, 904, 1071, 1077)");

	}

	public function safeDown()
	{
		$this->execute("ALTER TABLE `clinic` DROP COLUMN `show_in_advert`");
	}
}