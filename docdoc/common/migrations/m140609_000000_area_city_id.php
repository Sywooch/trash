<?php

/**
 * Файл класса m140609_000000_area_city_id.
 *
 * Добавляет идентификатор города в таблицу округов
 *
 * @author  Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @link    https://docdoc.megaplan.ru/task/1003804/card/
 * @package common.migrations
 */
class m140609_000000_area_city_id extends CDbMigration
{

	/**
	 * Применяет миграцию в трансакции
	 *
	 * @return bool
	 */
	public function safeUp()
	{
		$this->addColumn("area", "city_id", "INT NOT NULL DEFAULT 1");
		$this->addForeignKey("area_city_id", "area", "city_id", "city", "id_city");
	}

	/**
	 * Откатывает миграцию в трансакции
	 *
	 * @return bool
	 */
	public function safeDown()
	{
		$this->dropForeignKey("area_city_id", "area");
		$this->dropColumn("area", "city_id");
	}
}