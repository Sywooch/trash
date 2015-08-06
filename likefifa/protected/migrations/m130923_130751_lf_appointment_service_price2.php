<?php

/**
 * Добавляет поле service_price2 в таблицу lf_appointment
 *
 */
class m130923_130751_lf_appointment_service_price2 extends CDbMigration
{

	public function up()
	{
		$this->addColumn('lf_appointment', 'service_price2', 'int');
	}

	public function down()
	{
		$this->dropColumn('lf_appointment', 'service_price2');
	}

}