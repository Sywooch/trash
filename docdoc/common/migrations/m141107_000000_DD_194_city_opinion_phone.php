<?php

use dfs\docdoc\models\CityModel;

/**
 * Файл класса m141107_000000_DD_194_city_opinion_phone
 *
 * Миграция, добавляющая телефон для сборки отзывов
 *
 * @author  Mikhail Vasilyev <mvasilyev@docdoc.ru>
 * @link    https://docdoc.atlassian.net/browse/DD-194
 * @package migrations
 */
class m141107_000000_DD_194_city_opinion_phone extends CDbMigration
{

	/**
	 * Применяет миграцию в транзакции
	 *
	 * @return bool
	 */
	public function safeUp()
	{
		$this->addColumn("city", "opinion_phone", "CHAR(12)");

		foreach (CityModel::model()->findAll() as $city) {
			$this->update(
				"city",
				["opinion_phone" => $city->site_phone],
				"id_city = :id_city",
				["id_city" => $city->id_city]
			);
		}

		return true;
	}

	/**
	 * Откатывает миграцию
	 *
	 * @return void
	 */
	public function down()
	{
		$this->dropColumn("city", "opinion_phone");
	}
}
