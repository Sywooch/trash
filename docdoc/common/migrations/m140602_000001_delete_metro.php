<?php

/**
 * m140602_000001_delete_metro class file.
 *
 * Удаляет таблицы в БД от ДокДока.
 *
 * @author   Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @see      https://docdoc.megaplan.ru/task/1003491/card/
 * @package  common.migrations
 */
class m140602_000001_delete_metro extends CDbMigration
{

	/**
	 * Применяет миграцию
	 *
	 * @return void
	 */
	public function up()
	{
		$this->dropTable("metro");

		$this->renameColumn("underground_line", "metro_id", "city_id");
	}

	/**
	 * Откатывает миграцию
	 *
	 * @return void
	 */
	public function down()
	{
		$this->createTable(
			"metro",
			array(
				"id"        => "pk",
				"title"     => "VARCHAR(255) NOT NULL",
				"file_name" => "VARCHAR(255) NOT NULL",
				"id_city"   => "INT NOT NULL",
			),
			"ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci"
		);

		$this->insert(
			"metro",
			array(
				"title"     => "Схема метро Москвы",
				"file_name" => "map_msk",
				"id_city"   => 1
			)
		);

		$this->insert(
			"metro",
			array(
				"title"     => "Схема метро Санкт-Петербурга",
				"file_name" => "map_spb",
				"id_city"   => 2
			)
		);

		$this->renameColumn("underground_line", "city_id", "metro_id");
	}
}