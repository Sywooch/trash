<?php

/**
 * TTL для смсок
 */
class m141223_151423_DD_545_sms_ttl extends CDbMigration
{
	public function up()
	{
		$this->addColumn('SMSQuery', 'ttl', 'int');
	}

	public function down()
	{
		$this->dropColumn('SMSQuery', 'ttl');
	}
}
