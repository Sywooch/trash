<?php

class m141014_135916_dd_358_booking_external_id_nullable extends CDbMigration
{
	/**
	 * @return bool|void
	 */
	public function up()
	{
		$this->execute("alter table booking modify external_id varchar(50)");
	}

	public function down()
	{
		//не обязательно
	}
}
