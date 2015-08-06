<?php
/**
 * Поле отвечающее за публикацию мастера на сайте в каталоге
 * Для салонов тоже пришлось сделать для SearchController
 *
 * @see https://docdoc.megaplan.ru/task/1002401/card/
 */

class m131113_133120_lf_master_is_published extends CDbMigration
{

	/**
	 * Добавляет поле
	 * Записывает значения для всех мастеров, что они опубликованы
	 *
	 * @return void
	 */
	public function up()
	{
		$this->addColumn('lf_master', 'is_published', 'int');

		Yii::app()->db->createCommand()->update(
			"lf_master",
			array(
				"is_published" => 1
			)
		);

		$this->addColumn('lf_salons', 'is_published', 'int');

		Yii::app()->db->createCommand()->update(
			"lf_salons",
			array(
				"is_published" => 1
			)
		);
	}

	/**
	 * Удаляет поле
	 *
	 * @return void
	 */
	public function down()
	{
		$this->dropColumn('lf_master', 'is_published');
		$this->dropColumn('lf_salons', 'is_published');
	}
}