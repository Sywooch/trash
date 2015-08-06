<?php

class m150311_145038_dd_952_clinic_remove_column_asterisk_phone_2 extends CDbMigration
{
	public function up()
	{
		$this->dropColumn('clinic', 'asterisk_phone_2');

	}

	public function down()
	{
		$this->addColumn('clinic', 'asterisk_phone_2', 'CHAR(12)');
	}
}