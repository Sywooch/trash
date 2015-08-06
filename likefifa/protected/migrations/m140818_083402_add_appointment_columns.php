<?php

/**
 * Добавляет служебные колонки в таблицу заявок
 *
 * Class m140818_083402_add_appointment_columns
 */
class m140818_083402_add_appointment_columns extends CDbMigration
{
	public function up()
	{
		$this->addColumn('lf_appointment', 'is_viewed', "TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Определяет, видел ли оператор заявку'");
		$this->execute('update lf_appointment set is_viewed = 1');

		$this->addColumn('lf_appointment', 'create_source', "ENUM('front','bo') NOT NULL DEFAULT 'front' COMMENT 'Источник заявки'");
	}

	public function down()
	{
		$this->dropColumn('lf_appointment', 'is_viewed');
		$this->dropColumn('lf_appointment', 'create_source');
	}
}