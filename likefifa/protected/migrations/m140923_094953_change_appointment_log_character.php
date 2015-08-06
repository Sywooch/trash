<?php

class m140923_094953_change_appointment_log_character extends CDbMigration
{
	public function up()
	{
		$this->truncateTable('lf_appointment_log');
		$this->execute("ALTER TABLE lf_appointment_log CONVERT TO CHARACTER SET utf8;");
	}

	public function down()
	{
		echo "m140923_094953_change_appointment_log_character does not support migration down.\n";
		return false;
	}
}