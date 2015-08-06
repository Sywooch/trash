<?php

class m141210_121828_dd_583_mobile_phone_to_notify_phones_rename extends CDbMigration
{
	public function up()
	{
		$this->execute('ALTER TABLE clinic DROP phone_mobile;');
		$this->execute('ALTER TABLE clinic ADD notify_phones VARCHAR(255) DEFAULT null NULL;');
	}

	public function down()
	{
		$this->execute('ALTER TABLE clinic drop column notify_phones;');
		$this->execute('ALTER TABLE clinic ADD phone_mobile VARCHAR(12) DEFAULT null NULL;');
	}
}
