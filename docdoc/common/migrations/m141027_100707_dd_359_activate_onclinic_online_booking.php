<?php

class m141027_100707_dd_359_activate_onclinic_online_booking extends CDbMigration
{
	/**
	 * @return bool|void
	 */
	public function up()
	{
		$this->execute('update clinic set online_booking=1 where (id=13 or parent_clinic_id=13) and status=3');
	}

	/**
	 * @return bool|void
	 */
	public function down()
	{
		$this->execute('update clinic set online_booking=0 where id=13 or parent_clinic_id=13');
	}
}
