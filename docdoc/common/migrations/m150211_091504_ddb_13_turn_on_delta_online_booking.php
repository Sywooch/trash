<?php

class m150211_091504_ddb_13_turn_on_delta_online_booking extends CDbMigration
{
	public function up()
	{
		$this->execute('update clinic set online_booking = 1 where id in (193, 1848)');
	}

	public function down()
	{
		$this->execute('update clinic set online_booking = 0 where id in (193, 1848)');
	}
}