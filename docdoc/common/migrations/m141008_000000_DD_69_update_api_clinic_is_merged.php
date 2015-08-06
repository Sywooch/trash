<?php

use dfs\docdoc\models\ApiClinicModel;
use dfs\docdoc\models\ClinicModel;

/**
 * Файл класса m141008_000000_DD_69_update_api_clinic_is_merged.
 *
 * Миграция для api_clinic is_merged
 *
 * @author  Mikhail Vasilyev <mvasilyev@docdoc.ru>
 * @link    https://docdoc.atlassian.net/browse/DD-69
 * @package dfs.docdoc.common.migrations
 */
class m141008_000000_DD_69_update_api_clinic_is_merged extends CDbMigration
{

	/**
	 * Применяет миграцию
	 *
	 * @return bool|void
	 */
	public function up()
	{
		foreach (ApiClinicModel::model()->findAll() as $model) {
			$criteria = new CDbCriteria;
			$criteria->condition = "t.external_id = :external_id";
			$criteria->params["external_id"] = $model->id;
			if (ClinicModel::model()->find($criteria)) {
				$model->is_merged = 1;
				$model->save();
			}
		}
	}

	/**
	 * Откатывает миграцию
	 *
	 * @return bool|void
	 */
	public function down()
	{
		$this->update("api_clinic", array("is_merged" => 0));
	}
}