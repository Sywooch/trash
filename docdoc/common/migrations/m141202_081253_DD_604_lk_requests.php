<?php

/**
 * Добавление новых полей в заявки
 */
class m141202_081253_DD_604_lk_requests extends CDbMigration
{
	public function up()
	{
		$this->addColumn('request', 'date_billing', 'datetime DEFAULT NULL');
		$this->addColumn('request', 'processing_time', 'int NOT NULL DEFAULT "0"');

		$this->createIndex('date_billing_key', 'request', 'date_billing');
	}

	public function down()
	{
		$this->dropColumn('request', 'date_billing');
		$this->dropColumn('request', 'processing_time');

		$this->dropIndex('date_billing_key', 'request');
	}
}
