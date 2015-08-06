<?php

/**
 * @author Aleksey Parshukov, <aparshukov@docdoc.ru>
 * @task 3425
 *
 *       Тестовая миграция проверить работу системы
 */
class m140329_055617_3425_example extends CDbMigration
{
	/**
	 * This method contains the logic to be executed when applying this migration.
	 * Child classes may implement this method to provide actual migration logic.
	 * @return boolean Returning false means, the migration will not be applied.
	 */
	public function up()
	{
		echo __CLASS__ . " migration up.\n";
		return parent::up();
	}

	/**
	 * This method contains the logic to be executed when removing this migration.
	 * Child classes may override this method if the corresponding migrations can be removed.
	 * @return boolean Returning false means, the migration will not be applied.
	 */
	public function down()
	{
		echo __CLASS__ . " migration down.\n";
		return parent::down();
	}
}