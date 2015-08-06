<?php

/**
 * Удаление пробелов вначале и конце названий
 */
class m141217_132935_DD_641_trim_name extends CDbMigration
{
	public function up()
	{
		$this->execute('UPDATE diagnostica SET name = TRIM(name)');
	}

	public function down()
	{
	}
}
