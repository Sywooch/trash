<?php

/**
 * m140318_000000_moscow_area_is_big class file.
 *
 * Добавляет флаг крупного города (is_big)
 * Для новых городов добавляется флаг is_new
 *
 * @author   Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @see      https://docdoc.megaplan.ru/task/1003365/card/
 * @package  migrations
 */
class m140318_000000_moscow_area_is_big extends CDbMigration
{

	/**
	 * Применяет миграцию
	 *
	 * @return void
	 */
	public function up()
	{
		$this->addColumn("moscow_area", "is_big", "INT NOT NULL");
		$this->addColumn("moscow_area", "is_new", "INT NOT NULL");
		$this->createIndex("moscow_area_is_big", "moscow_area", "is_big");

		$bigCities = array(
			"Балашиха",
			"Воскресенск",
			"Дмитров",
			"Домодедово",
			"Железнодорожный",
			"Клин",
			"Коломна",
			"Королев",
			"Люберцы",
			"Мытищи",
			"Наро-Фоминск",
			"Ногинск",
			"Одинцово",
			"Орехово-Зуево",
			"Подольск",
			"Пушкино",
			"Раменское",
			"Сергиев Посад",
			"Серпухов",
			"Солнечногорск",
			"Химки",
			"Чехов",
			"Щёлково",
			"Электросталь",
		);

		foreach ($bigCities as $cityName) {
			$criteria = new CDbCriteria;
			$criteria->condition = "t.name = :name";
			$criteria->params = array(":name" => $cityName);
			$model = MoscowArea::model()->find($criteria);
			if (!$model) {
				$model = new MoscowArea;
				$model->name = $cityName;
				$model->is_new = 1;
			}
			$model->is_big = 1;
			$model->save();
		}
	}

	/**
	 * Откатывает миграцию
	 *
	 * @return void
	 */
	public function down()
	{
		$this->dropIndex("moscow_area_is_big", "moscow_area");

		$cities = MoscowArea::model()->findAll();
		foreach ($cities as $model) {
			if ($model->is_new) {
				$model->delete();
			}
		}

		$this->dropColumn("moscow_area", "is_big");
		$this->dropColumn("moscow_area", "is_new");
	}
}