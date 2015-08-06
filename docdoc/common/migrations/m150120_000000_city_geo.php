<?php

use dfs\docdoc\models\CityModel;

/**
 * Добавляет координаты для городов
 */
class m150120_000000_city_geo extends CDbMigration
{
	/**
	 * @return void
	 */
	public function up()
	{
		$models = CityModel::model()->active()->findAll();
		foreach ($models as $model) {
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt(
				$curl,
				CURLOPT_URL,
				"http://maps.google.com/maps/api/geocode/json?" . http_build_query(["address" => $model->title])
			);
			$json = curl_exec($curl);

			if (!empty(json_decode($json)->results[0]->geometry->location)) {
				$location = json_decode($json)->results[0]->geometry->location;
				$model->lat = $location->lat;
				$model->long = $location->lng;
				$model->save();
			}
		}
	}

	/**
	 * @return void
	 */
	public function down()
	{
		$this->update("city", ["lat" => "", "long" => ""]);
	}
}