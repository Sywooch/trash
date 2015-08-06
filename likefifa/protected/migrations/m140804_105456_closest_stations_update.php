<?php

/**
 * Добавляет ближайшие станции метро из дампа, взятого с docdoc
 *
 * Class m140804_105456_closest_stations_update
 */
class m140804_105456_closest_stations_update extends CDbMigration
{
	public function up()
	{
		$query =
			file_get_contents(Yii::getPathOfAlias('application.data') . DIRECTORY_SEPARATOR . 'closest_stations2.sql');
		$this->execute($query);
	}

	public function down()
	{
		echo "m140804_105456_closest_stations_update does not support migration down.\n";
		return false;
	}
}