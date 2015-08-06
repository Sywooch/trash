<?php

/**
 * Сверка по клиникам
 */
class m150317_125906_DD_1030_clinic_reconciliation extends CDbMigration
{
	public function up()
	{
		$this->addColumn('clinic', 'email_reconciliation', 'varchar(255) DEFAULT NULL');
	}

	public function down()
	{
		$this->dropColumn('clinic', 'email_reconciliation');
	}
}
