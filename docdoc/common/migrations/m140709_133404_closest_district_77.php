<?php

/**
 * Файл класса m140709_133404_closest_district_77.
 *
 * Удаление станции Краснопресненская из района Зябликово
 */
class m140709_133404_closest_district_77 extends CDbMigration
{
	/**
	 * Применяет миграцию
	 *
	 * @return void
	 */
	public function up()
	{
		$this->execute("DELETE FROM `district_has_underground_station`
			WHERE id_district = (
				SELECT id FROM `district` WHERE name = 'Зябликово'
			) AND id_station = (
				SELECT id FROM `underground_station` WHERE name = 'Краснопресненская'
			)");

		$this->execute("DELETE FROM `closest_district`
			WHERE district_id = (
				SELECT id FROM `district` WHERE name = 'Зябликово'
			) AND closest_district_id IN (
				SELECT id FROM `district` WHERE name IN ('Арбат', 'Пресненский', 'Тверской', 'Хамовники', 'Якиманка', 'Беговой', 'Дорогомилово')
			)");
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