<?php

/**
 * Файл класса m140627_062334_city_has_mobile.
 *
 * Признак мобильной версии сайта для города
 */
class m140627_062334_city_has_mobile extends CDbMigration
{
	/**
	 * Применяет миграцию
	 *
	 * @return void
	 */
	public function up()
	{
		$this->addColumn("city", "has_mobile", "TINYINT(1) DEFAULT 0");
	}

	/**
	 * Откатывает миграцию
	 *
	 * @return void
	 */
	public function down()
	{
		$this->dropColumn("city", "has_mobile");
	}
}