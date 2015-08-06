<?php

class m140530_095749_doctor_has_slots extends CDbMigration
{
	/**
	 * добавление признака - есть ли у доктора слоты
	 */
	public function safeUp()
	{
		$this->execute("ALTER TABLE `doctor_4_clinic`
				ADD COLUMN `has_slots` TINYINT(1) DEFAULT 0 AFTER `schedule_step`");
	}

	public function safeDown()
	{
		$this->execute("ALTER TABLE `doctor_4_clinic` DROP COLUMN `has_slots`");
	}
}