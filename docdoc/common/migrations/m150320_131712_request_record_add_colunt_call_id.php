<?php

class m150320_131712_request_record_add_colunt_call_id extends CDbMigration
{
	public function up()
	{
		$this->execute('ALTER TABLE request_record ADD external_call_id VARCHAR(255) DEFAULT null NULL;');
	}

	public function down()
	{
		$this->dropColumn('request_record', 'external_call_id');
	}
}