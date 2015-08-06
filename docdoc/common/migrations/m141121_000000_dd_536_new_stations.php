<?php

/**
 * Файл класса m141121_000000_dd_536_new_stations
 *
 * Миграция, выполняющаю команду по пересчету координат для станций метро и ближайших станций
 *
 * @author  Mikhail Vasilyev <mvasilyev@docdoc.ru>
 * @link    https://docdoc.atlassian.net/browse/DD-194
 * @package migrations
 */
class m141121_000000_dd_536_new_stations extends CDbMigration
{

	/**
	 * Применяет миграцию
	 *
	 * @return void
	 */
	public function up()
	{
		exec("./yiic updateStation geoCoordinates");
		exec("./yiic updateStation closest --city=moscow");
	}
}
