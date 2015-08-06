<?php

class m150211_134857_dd_888_request_add_column_expire_time extends CDbMigration
{
	public function up()
	{
		$this->execute('ALTER TABLE request ADD expire_time DATETIME DEFAULT null NULL;');
		$this->execute('CREATE INDEX expire_time_index ON request (expire_time);');
	}

	public function down()
	{
		$this->execute('ALTER TABLE request DROP expire_time;');
	}
}