<?php

class m140819_092513_change_active_cities extends CDbMigration
{
	public function up()
	{
		$this->execute('UPDATE cities SET is_active = 1');
	}

	public function down()
	{
		return false;
	}
}