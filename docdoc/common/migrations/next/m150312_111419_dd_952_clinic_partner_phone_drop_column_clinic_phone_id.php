<?php

class m150312_111419_dd_952_clinic_partner_phone_drop_column_clinic_phone_id extends CDbMigration
{
	public function up()
	{
		$this->dropForeignKey('clinic_partner_phone_ibfk_4', 'clinic_partner_phone');
		$this->dropColumn('clinic_partner_phone', 'clinic_phone_id');
	}

	public function down()
	{
		$this->addColumn('clinic_partner_phone', 'clinic_phone_id', 'int(11)');
		$this->addForeignKey('clinic_partner_phone_ibfk_4', 'clinic_partner_phone', 'clinic_phone_id', 'phone', 'id');
	}
}