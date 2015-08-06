<?php

/**
 * m140506_000000_lf_group_genitive class file.
 *
 * Родительный падеж для специализации
 *
 * @author   Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @see      https://docdoc.megaplan.ru/task/1003467/card/
 * @package  migrations
 */
class m140506_000000_lf_group_genitive extends CDbMigration
{

	/**
	 * Применяет миграцию
	 *
	 * @return void
	 */
	public function up()
	{
		$this->addColumn("lf_group", "genitive", "VARCHAR(128) NOT NULL");

		$this->update("lf_group", array("genitive" => "визажистов"), "id = :id", array(":id" => 1));
		$this->update("lf_group", array("genitive" => "парикмахеров"), "id = :id", array(":id" => 2));
		$this->update("lf_group", array("genitive" => "мастеров ногтевого сервиса"), "id = :id", array(":id" => 3));
		$this->update("lf_group", array("genitive" => "массажистов"), "id = :id", array(":id" => 4));
		$this->update("lf_group", array("genitive" => "косметологов"), "id = :id", array(":id" => 5));
		$this->update(
			"lf_group",
			array("genitive" => "мастеров по пирсингу и татуировкам"),
			"id = :id",
			array(":id" => 6)
		);
	}

	/**
	 * Откатывает миграцию
	 *
	 * @return void
	 */
	public function down()
	{
		$this->dropColumn("lf_group", "genitive");
	}
}