<?php

class m141209_131953_dd_301_clinic_add_column_notify_emails extends CDbMigration
{
	public function up()
	{
		$this->execute('ALTER TABLE clinic ADD notify_emails VARCHAR(255) DEFAULT null NULL;');
	}

	public function down()
	{
		$this->execute('ALTER TABLE clinic drop column notify_emails;');
	}
}
