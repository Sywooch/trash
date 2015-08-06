<?php

/**
 * Class m140417_123309_fix_station_indexes
 *
 * Создание первичного ключа на таблице closest_stations
 *
 * @link https://docdoc.megaplan.ru/task/1003391/card.html
 *
 */
class m140417_123309_fix_station_indexes extends CDbMigration {

	/**
	 * запросы, которые нужно выполнить при миграции
	 */
	public function safeUp(){

		$this->execute("ALTER TABLE `closest_station`
				CHANGE COLUMN `station_id` `station_id` DOUBLE NOT NULL,
				CHANGE COLUMN `closest_station_id` `closest_station_id` DOUBLE NOT NULL,
				ADD PRIMARY KEY (`station_id`, `closest_station_id`)");
	}

	/**
	 * при откате ничего не делаем
	 */
	public function safeDown()
	{

	}

}
