<?php

/**
 * Файл класса m140602_000000_city_is_active.
 *
 * Флаг активности города
 *
 * @author   Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @see      https://docdoc.megaplan.ru/task/1003803/card/
 * @package  common.migrations
 */
class m140602_000000_city_is_active extends CDbMigration
{

	/**
	 * Применяет миграцию
	 *
	 * @return void
	 */
	public function up()
	{
		$this->addColumn("city", "is_active", "TINYINT(1) DEFAULT 1");
		$this->createIndex("city_is_active", "city", "is_active");
	}

	/**
	 * Откатывает миграцию
	 *
	 * @return void
	 */
	public function down()
	{
		$this->dropIndex("city_is_active", "city");
		$this->dropColumn("city", "is_active");
	}
}