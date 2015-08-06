<?php

/**
 * Добавляет иконку и группу для пункта меню Регионы в БО
 *
 * Class m140908_124718_admin_menu_fixes
 */
class m140908_124718_admin_menu_fixes extends CDbMigration
{
	public function up()
	{
		$this->update(
			'admin_controller',
			[
				'col_group' => 'Прочее',
				'icon'      => 'fighter-jet',
			],
			'id = 20'
		);
	}

	public function down()
	{
		echo "m140908_124718_admin_menu_fixes does not support migration down.\n";
		return false;
	}
}