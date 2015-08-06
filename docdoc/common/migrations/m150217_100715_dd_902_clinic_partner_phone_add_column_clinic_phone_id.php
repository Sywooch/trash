<?php

class m150217_100715_dd_902_clinic_partner_phone_add_column_clinic_phone_id extends CDbMigration
{
	public function up()
	{
		$this->execute('ALTER TABLE clinic_partner_phone ADD clinic_phone_id INT NULL;');
		$this->execute('ALTER TABLE clinic_partner_phone ADD CONSTRAINT clinic_partner_phone_ibfk_4 FOREIGN KEY (clinic_phone_id) REFERENCES phone (id);');

	}

	public function down()
	{
		$this->dropForeignKey('clinic_partner_phone_ibfk_4' ,'clinic_partner_phone');
		$this->dropColumn('clinic_partner_phone', 'clinic_phone_id');
	}
}