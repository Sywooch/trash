<?php

class m150316_153523_dd_1068_api_doctor_change_unique_index extends CDbMigration
{
	public function up()
	{
		$this->execute('DROP INDEX doctor_in_clinic ON api_doctor;');
		$this->execute('CREATE UNIQUE INDEX api_doctor_id_api_clinic_id_api_resource_type_index ON api_doctor (api_doctor_id, api_clinic_id, api_resource_type);');
	}

	public function down()
	{
		$this->execute('DROP INDEX api_doctor_id_api_clinic_id_api_resource_type_index ON api_doctor;');
		$this->execute('CREATE UNIQUE INDEX doctor_in_clinic ON api_doctor (api_doctor_id, api_clinic_id);');
	}
}