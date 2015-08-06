<?php

class m150114_081128_dd_793_delete_bad_api_clinics extends CDbMigration
{
	public function up()
	{
		$this->execute("delete from api_clinic where id in ('chudodoct_1', 'chudodoct_2')");
	}

	public function down()
	{

	}
}
