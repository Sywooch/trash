<?php

/**
 * Добавляет стацнии, ближайшие к городам
 *
 * Class m140825_122621_city_near_stations
 */
class m140825_122621_city_near_stations extends CDbMigration
{
	public function up()
	{
		// Выключает города, в которых нет мастеров и салонов
		$this->execute("update cities c
			left join lf_master t on t.city_id = c.id
			left join lf_salons s on s.city_id = c.id
			set c.is_active = 0
			where t.id is null and s.id is null ");

		// Создает таблицу с ближайшими станциями метро
		$query =
			file_get_contents(Yii::getPathOfAlias('application.data') . DIRECTORY_SEPARATOR . 'city_near_stations.sql');
		$this->execute($query);
	}

	public function down()
	{
		$this->dropTable('city_near_stations');
	}
}