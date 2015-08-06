<?php

/**
 * Настройка, которая позволяет выключить вывод расписания
 */
class m150220_125812_DD_892_clinic_schedule extends CDbMigration
{
	public function up()
	{
		$this->addColumn('clinic', 'scheduleForDoctors', 'tinyint(1) NOT NULL DEFAULT "1"');
	}

	public function down()
	{
		$this->dropColumn('clinic', 'scheduleForDoctors');
	}
}
