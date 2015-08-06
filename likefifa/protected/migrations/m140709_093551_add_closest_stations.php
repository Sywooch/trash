<?php

/**
 * Добавляет ближайшие станции метро из дампа, взятого с docdoc
 *
 * Class m140709_093551_add_closest_stations
 */
class m140709_093551_add_closest_stations extends CDbMigration
{
	public function up()
	{
		$query =
			file_get_contents(Yii::getPathOfAlias('application.data') . DIRECTORY_SEPARATOR . 'closest_stations.sql');
		$this->execute($query);
	}

	public function down()
	{
		$this->dropTable('closest_station');
	}
}