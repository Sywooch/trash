<?php

/**
 * m140714_000000_underground_line_city_id class file.
 *
 * Делает связку линий метро и города
 *
 * @author   Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @see      https://docdoc.megaplan.ru/task/1003365/card/
 * @package  migrations
 */
class m140714_000000_underground_line_city_id extends CDbMigration
{

	/**
	 * Применяет миграцию
	 *
	 * @return bool
	 */
	public function safeUp()
	{
		$this->addColumn("underground_line", "city_id", "INT NOT NULL DEFAULT 1");
		$this->addForeignKey("underground_line_city_id", "underground_line", "city_id", "cities", "id");

		$this->addColumn("cities", "has_underground", "INT NOT NULL");
		$this->createIndex("city_has_underground", "cities", "has_underground");

		$this->update(
			"cities",
			array(
				"has_underground" => 1,
			),
			"id = :id",
			array(":id" => 1)
		);
	}

	/**
	 * Откатывает миграцию
	 *
	 * @return bool
	 */
	public function safeDown()
	{
		$this->dropForeignKey("underground_line_city_id", "underground_line");
		$this->dropColumn("underground_line", "city_id");

		$this->dropIndex("city_has_underground", "cities");
		$this->dropColumn("cities", "has_underground");
	}
}