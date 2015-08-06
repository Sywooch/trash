<?php

class m150316_090922_dd_1068_api_doctors_add_column_resource_type extends CDbMigration
{
	public function up()
	{
		$this->execute('ALTER TABLE api_doctor ADD api_resource_type VARCHAR(50) DEFAULT \'\' NOT NULL;');
		$this->execute("UPDATE api_doctor SET api_resource_type='doctor'");
	}

	public function down()
	{
		$this->dropColumn('api_doctor', 'api_resource_type');
	}
}