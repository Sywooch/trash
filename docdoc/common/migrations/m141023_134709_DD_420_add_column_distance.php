<?php

use dfs\docdoc\models\ClinicModel;

/**
 * Class m141023_134709_DD_420_add_column_distance
 */
class m141023_134709_DD_420_add_column_distance extends CDbMigration
{
	/**
	 * Добавляем расстояние от станции метро до клиники
	 *
	 * @return bool|void
	 */
	public function up()
	{
		$this->addColumn('underground_station_4_clinic', 'distance', 'INT(6) UNSIGNED NULL');

		$clinics = ClinicModel::model()->findAll();
		foreach ($clinics as $clinic) {
			if (!empty($clinic->latitude) && !empty($clinic->latitude)) {
				foreach ($clinic->stations as $station) {
					$this->update(
						'underground_station_4_clinic',
						['distance' => $station->calcDistanceToClinic($clinic)],
						'undegraund_station_id=:station AND clinic_id=:clinic',
						[':station' => $station->id, ':clinic' => $clinic->id]);
				}
			}
		}
	}

	public function down()
	{
		$this->dropColumn('underground_station_4_clinic', 'distance');
	}
}