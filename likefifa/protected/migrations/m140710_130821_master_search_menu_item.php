<?php
use likefifa\models\AdminControllerModel;

/**
 * Class m140710_130821_master_search_menu_item
 *
 * Добавляет запись о новом элементе меню в админке
 */
class m140710_130821_master_search_menu_item extends CDbMigration
{
	public function up()
	{
		$this->insert(
			AdminControllerModel::model()->tableName(),
			[
				'name'         => 'Поиск мастера',
				'rewrite_name' => 'masterSearch'
			]
		);
	}

	public function down()
	{
		$this->delete(
			AdminControllerModel::model()->tableName(),
			'name = :name and rewrite_name = :r_name',
			[
				':name'   => 'Поиск мастера',
				':r_name' => 'masterSearch'
			]
		);
	}
}