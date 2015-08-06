<?php

/**
 * m140214_000000_lf_salons_cteated class file.
 *
 * Создает поле "Дата создания" для салонов
 *
 * @author   Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @see      https://docdoc.megaplan.ru/task/1002987/card/
 * @package  migrations
 */
class m140214_000000_lf_salons_cteated extends CDbMigration
{

	/**
	 * Применение миграции
	 *
	 * @return void
	 */
	public function up()
	{
		$this->addColumn("lf_salons", "created", "TIMESTAMP DEFAULT CURRENT_TIMESTAMP");

		$salons = LfSalon::model()->findAll();
		if ($salons) {
			foreach ($salons as $model) {
				$model->created = "0000-00-00 00:00:00";
				$model->save();
			}
		}
	}

	/**
	 * Откат миграции
	 *
	 * @return void
	 */
	public function down()
	{
		$this->dropColumn("lf_salons", "created");
	}
}