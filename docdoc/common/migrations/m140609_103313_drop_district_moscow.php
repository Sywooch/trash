<?php

class m140609_103313_drop_district_moscow extends CDbMigration
{
	/**
	 * удаление старой таблицы районов Москвы
	 */
	public function up()
	{
		$this->dropTable('district_moscow');
		$this->dropTable('district_underground_station');
	}

	public function down()
	{
		echo "m140609_103313_drop_district_moscow does not support migration down.\n";
		return false;
	}
}