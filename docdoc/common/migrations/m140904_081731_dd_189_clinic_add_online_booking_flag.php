<?php

class m140904_081731_dd_189_clinic_add_online_booking_flag extends CDbMigration
{
	/**
	 * @return bool|void
	 */
	public function up()
	{
		$this->execute('ALTER TABLE clinic ADD online_booking TINYINT(1) DEFAULT FALSE NOT NULL;');
	}

	/**
	 * @return bool|void
	 */
	public function down()
	{
		$this->execute('ALTER TABLE clinic DROP online_booking;');
	}
}
