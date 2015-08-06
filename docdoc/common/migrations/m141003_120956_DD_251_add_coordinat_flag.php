<?php

/**
 * m141003_120956_DD_251_add_coordinat_flag
 * Добавление флага, что поиск ближайшей станции метро нужно вести только по координатам
 */
class m141003_120956_DD_251_add_coordinat_flag extends CDbMigration
{
	/**
	 * @return bool|void
	 */
	public function up()
	{
		$this->addColumn('underground_station', 'only_coord_search', 'TINYINT(1) DEFAULT 0');

		//станции во всех городах, кроме Москвы и Питера ищем по координатам
		$this->execute("
			UPDATE
				underground_station us, underground_line ul
			SET
				only_coord_search = 1
			WHERE
				us.underground_line_id = ul.id	AND ul.city_id NOT IN (1,2)
		");

		//станции внутри кольцевой ищем по координатам
		$intoCircle = [147, 123, 67, 53, 153, 139, 42,
			146, 91, 80, 135, 9, 4, 152, 171, 168, 115, 21, 155, 151, 142, 54, 172,
			100, 75, 65, 63, 134, 113, 8
		];

		$this->execute("
			UPDATE
				underground_station
			SET
				only_coord_search = 1
			WHERE
				id IN (" . implode(",", $intoCircle) . ")
		");

	}

	/**
	 * @return bool|void
	 */
	public function down()
	{
		$this->dropColumn('underground_station', 'only_coord_search');
	}
}