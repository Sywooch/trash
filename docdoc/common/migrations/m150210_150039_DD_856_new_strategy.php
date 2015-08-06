<?php

/**
 * Class m150210_150039_DD_856_new_strategy
 */
class m150210_150039_DD_856_new_strategy extends CDbMigration
{
	public function up()
	{
		$this->execute(
			"ALTER TABLE rating_strategy CHANGE COLUMN params params TEXT"
		);

		$this->addColumn('rating_strategy', 'type', 'varchar(255)');
		$this->addColumn('rating_strategy', 'for_object', 'tinyint DEFAULT 0');

		$this->execute("UPDATE rating_strategy SET type = name");
	}

	public function down()
	{
		$this->dropColumn('rating_strategy', 'type');
		$this->dropColumn('rating_strategy', 'for_object');
	}
}