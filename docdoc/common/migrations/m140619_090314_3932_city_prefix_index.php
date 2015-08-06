<?php

/**
 * Файл класса m140619_090314_3932_city_prefix_index.
 *
 * Добавление в таблицу city 4 новых поля из конфига
 */
class m140619_090314_3932_city_prefix_index extends CDbMigration
{
	/**
	 * Применяет миграцию
	 * @return void
	 */
	public function up()
	{
		$this->createIndex("prefix", "city", "prefix");
	}

	/**
	 * Откатывает миграцию
	 * @return void
	 */
	public function down()
	{
		$this->dropIndex("prefix", "city");
	}
}