
<?php

class m140401_125345_index_optimization extends CDbMigration
{
	/**
	 * Для джойна между собой таблиц doctor_4_clinic и underground_station_4_clinic по clinic_id
	 * нужен простой индекс для clinic_id в каждой из таблиц
	 */
	public function safeUp()
	{

		$this->execute("ALTER TABLE `underground_station_4_clinic` ADD INDEX `clinic_idx` (`clinic_id` ASC)");

		$this->execute("ALTER TABLE `doctor_4_clinic` ADD INDEX `clinic_idx` (`clinic_id` ASC)");
	}

	public function safeDown()
	{
		

	}

}