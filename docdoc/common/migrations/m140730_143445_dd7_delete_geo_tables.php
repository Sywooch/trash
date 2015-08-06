<?php

/**
 * Файл класса m140730_143445_dd7_delete_geo_tables.
 */
class m140730_143445_dd7_delete_geo_tables extends CDbMigration
{
	/**
	 * @var array
	 */
	private $_deleteTables = [
		'net_euro',
		'net_ru',
		'net_city_ip',
		'net_country_ip',
		'net_city',
		'net_country',
	];

	/**
	 * Применяет миграцию
	 *
	 * @return void
	 */
	public function up()
	{
		foreach ($this->_deleteTables as $table) {
			$this->execute('DROP TABLE IF EXISTS ' . $table);
		}
	}

	/**
	 * Откатывает миграцию
	 *
	 * @return void
	 */
	public function down()
	{
	}
}