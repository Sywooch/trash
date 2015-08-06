<?php

class m150212_151450_dd_900_parter_add_column_phone_queue extends CDbMigration
{
	public function up()
	{
		$this->execute("ALTER TABLE partner ADD phone_queue VARCHAR(100) default 'partnerq' NULL;");
		$this->execute("update partner set phone_queue = 'callcenter' where name like 'dd.%'");
	}

	public function down()
	{
		$this->execute('ALTER TABLE partner DROP phone_queue;');
	}
}