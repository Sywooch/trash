<?php

use dfs\docdoc\models\ClinicModel;

/**
 * Рейтинг для клиник для вывода на сайте
 */
class m150218_135648_DD_942_clinic_rating extends CDbMigration
{
	public function up()
	{
		$this->addColumn('clinic', 'rating_show', 'float(9,2) DEFAULT NULL');
		$this->createIndex('clinic_rating_show', 'clinic', 'rating_show');

		// Устанавливаем рейтин для всех клиник
		ClinicModel::model()->updateRatingShow();
	}

	public function down()
	{
		$this->dropIndex('clinic_rating_show', 'clinic');
		$this->dropColumn('clinic', 'rating_show');
	}
}
