<?php

/**
 * Изменение типа данных в столбце с координатами клиник
 */
class m141114_072211_DD_380_change_coord_type extends CDbMigration
{
	/**
	 * @return bool|void
	 */
	public function safeUp()
	{
		$this->execute("ALTER TABLE `clinic`
			CHANGE COLUMN `longitude` `longitude` DOUBLE(13,10) NULL DEFAULT NULL");

		$this->execute("ALTER TABLE `clinic`
			CHANGE COLUMN `latitude` `latitude` DOUBLE(13,10) NULL DEFAULT NULL");

		$this->execute("ALTER TABLE `clinic`
			ADD COLUMN `coord_temp` DOUBLE(13,10) NULL DEFAULT NULL");


		$this->execute("UPDATE clinic SET coord_temp = latitude");

		$this->execute("UPDATE clinic SET latitude = longitude");

		$this->execute("UPDATE clinic SET longitude = coord_temp");

		$this->execute("ALTER TABLE clinic DROP COLUMN coord_temp");


		$this->execute("ALTER TABLE underground_station
			ADD COLUMN coord_temp DOUBLE(13,10) NULL DEFAULT NULL");

		$this->execute("UPDATE underground_station SET coord_temp = latitude");

		$this->execute("UPDATE underground_station SET latitude = longitude");

		$this->execute("UPDATE underground_station SET longitude = coord_temp");

		$this->execute("ALTER TABLE underground_station DROP COLUMN coord_temp");
	}

	/**
	 * @return bool|void
	 */
	public function safeDown()
	{
		$this->execute("ALTER TABLE clinic
			ADD COLUMN coord_temp DOUBLE(13,10) NULL DEFAULT NULL");

		$this->execute("UPDATE clinic SET coord_temp = latitude");

		$this->execute("UPDATE clinic SET latitude = longitude");

		$this->execute("UPDATE clinic SET longitude = coord_temp");

		$this->execute("ALTER TABLE clinic DROP COLUMN coord_temp");


		$this->execute("ALTER TABLE underground_station
			ADD COLUMN coord_temp DOUBLE(13,10) NULL DEFAULT NULL");

		$this->execute("UPDATE underground_station SET coord_temp = latitude");

		$this->execute("UPDATE underground_station SET latitude = longitude");

		$this->execute("UPDATE underground_station SET longitude = coord_temp");

		$this->execute("ALTER TABLE underground_station DROP COLUMN coord_temp");
	}
}
