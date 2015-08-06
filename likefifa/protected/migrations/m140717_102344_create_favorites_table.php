<?php

/**
 * Создает таблицу lf_appointment_favorites для хранения избранных заявок
 * Class m140717_102344_create_favorites_table
 */
class m140717_102344_create_favorites_table extends CDbMigration
{
	public function up()
	{
		$this->createTable(
			'lf_appointment_favorites',
			[
				'id'             => 'pk',
				'appointment_id' => 'INT NOT NULL',
				'admin_id'       => 'INT NOT NULL',
			]
		);
		$this->addForeignKey('ap_fav_admin_id', 'lf_appointment_favorites', 'admin_id', 'admin', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('ap_fav_app_id', 'lf_appointment_favorites', 'appointment_id', 'lf_appointment', 'id', 'CASCADE', 'CASCADE');
	}

	public function down()
	{
		$this->dropTable('lf_appointment_favorites');
	}
}