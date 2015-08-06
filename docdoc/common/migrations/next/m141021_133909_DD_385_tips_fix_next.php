<?php

/**
 * таблица с подсказоками
 */
class m141021_133909_DD_385_tips_fix_next extends CDbMigration
{
	/**
	 * @return bool|void
	 */
	public function up()
	{
		$this->dropColumn('tips_message', 'message');
	}

	/**
	 * @return bool|void
	 */
	public function down()
	{
		$this->addColumn('tips_message', 'message', 'varchar(250) NOT NULL');
	}
}
