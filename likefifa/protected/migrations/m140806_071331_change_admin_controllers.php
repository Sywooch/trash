<?php

/**
 * Добавляет колонки для группировки и сортировки меню БО
 *
 * Class m140806_071331_change_admin_controllers
 */
class m140806_071331_change_admin_controllers extends CDbMigration
{
	public function up()
	{
		$this->addColumn('admin_controller', 'sort', 'TINYINT(2) UNSIGNED NULL DEFAULT 0');
		$this->addColumn('admin_controller', 'col_group', 'VARCHAR(25) NULL DEFAULT NULL');
		$this->execute(
			"UPDATE `admin_controller` SET `sort`='1' WHERE `id`='15';
						UPDATE `admin_controller` SET `sort`='2' WHERE `id`='10';
						UPDATE `admin_controller` SET `sort`='3' WHERE `id`='11';
						UPDATE `admin_controller` SET `sort`='4' WHERE `id`='12';
						UPDATE `admin_controller` SET `col_group`='Контент' WHERE `id`='1';
						UPDATE `admin_controller` SET `col_group`='Контент' WHERE `id`='2';
						UPDATE `admin_controller` SET `col_group`='Контент' WHERE `id`='3';
						UPDATE `admin_controller` SET `col_group`='Контент' WHERE `id`='4';
						UPDATE `admin_controller` SET `col_group`='Контент' WHERE `id`='8';
						UPDATE `admin_controller` SET `col_group`='Контент' WHERE `id`='9';
						UPDATE `admin_controller` SET `col_group`='Контент' WHERE `id`='17';
						"
		);

		$this->execute(
			"UPDATE `admin_controller` SET `col_group`='Прочее' WHERE `id`='5';
			UPDATE `admin_controller` SET `col_group`='Прочее' WHERE `id`='6';
			UPDATE `admin_controller` SET `col_group`='Прочее' WHERE `id`='7';
			UPDATE `admin_controller` SET `col_group`='Контент' WHERE `id`='8';
			UPDATE `admin_controller` SET `col_group`='Контент' WHERE `id`='9';
			UPDATE `admin_controller` SET `col_group`='Прочее' WHERE `id`='1';
			UPDATE `admin_controller` SET `col_group`='Прочее' WHERE `id`='2';
			UPDATE `admin_controller` SET `col_group`='Прочее' WHERE `id`='3';
			UPDATE `admin_controller` SET `col_group`='Прочее' WHERE `id`='4';
			UPDATE `admin_controller` SET `col_group`='Прочее' WHERE `id`='17';
			UPDATE `admin_controller` SET `col_group`='Контент' WHERE `id`='16';
			"
		);
	}

	public function down()
	{
		$this->dropColumn('admin_controller', 'sort');
		$this->dropColumn('admin_controller', 'col_group');
	}
}