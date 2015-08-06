<?php

class m130927_125524_lfAppointment_is_long extends CDbMigration
{
	
	public function up()
	{
		$this->addColumn('lf_appointment', 'is_long', 'int NOT NULL');
	}

	public function down()
	{
		$this->dropColumn('lf_appointment', 'is_long');
	}

}