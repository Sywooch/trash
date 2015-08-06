<?php

/**
 * Иконки и сортировка меню в БО
 *
 * Class m140811_071516_change_admin_controllers
 */
class m140811_071516_change_admin_controllers extends CDbMigration
{
	public function safeUp()
	{
		$this->addColumn('admin_controller', 'icon', 'VARCHAR(25) NULL DEFAULT NULL');

		$this->execute("UPDATE `admin_controller` SET `sort`='6' WHERE `id`='13';
		UPDATE `admin_controller` SET `sort`='5' WHERE `id`='18';
		UPDATE `admin_controller` SET `sort`='7' WHERE `id`='19';
		UPDATE `admin_controller` SET `sort`='8' WHERE `id`='14';");
		
		$this->execute("UPDATE `admin_controller` SET `icon`='road' WHERE `id`='1';
		UPDATE `admin_controller` SET `icon`='road' WHERE `id`='2';
		UPDATE `admin_controller` SET `icon`='building' WHERE `id`='3';
		UPDATE `admin_controller` SET `icon`='copy' WHERE `id`='4';
		UPDATE `admin_controller` SET `icon`='graduation-cap' WHERE `id`='6';
		UPDATE `admin_controller` SET `icon`='cubes' WHERE `id`='7';
		UPDATE `admin_controller` SET `icon`='leaf' WHERE `id`='8';
		UPDATE `admin_controller` SET `icon`='file-text' WHERE `id`='9';
		UPDATE `admin_controller` SET `icon`='female' WHERE `id`='10';
		UPDATE `admin_controller` SET `icon`='group' WHERE `id`='11';
		UPDATE `admin_controller` SET `icon`='wechat' WHERE `id`='12';
		UPDATE `admin_controller` SET `icon`='camera' WHERE `id`='13';
		UPDATE `admin_controller` SET `icon`='camera' WHERE `id`='14';
		UPDATE `admin_controller` SET `icon`='file' WHERE `id`='15';
		UPDATE `admin_controller` SET `icon`='envelope' WHERE `id`='16';
		UPDATE `admin_controller` SET `icon`='female' WHERE `id`='17';
		UPDATE `admin_controller` SET `icon`='search' WHERE `id`='18';
		UPDATE `admin_controller` SET `icon`='money' WHERE `id`='19';
		");
	}

	public function safeDown()
	{
		$this->dropColumn('admin_controller', 'icon');
	}
}