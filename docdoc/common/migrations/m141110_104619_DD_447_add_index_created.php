<?php

/**
 * Class m141110_104619_DD_447_add_index_created
 */
class m141110_104619_DD_447_add_index_created extends CDbMigration
{
	/**
	 * @return bool|void
	 */
	public function up()
	{
		$this->createIndex("created_idx", "doctor_opinion", "created");
	}

	/**
	 * @return bool|void
	 */
	public function down()
	{
		$this->dropIndex('created_idx', 'doctor_opinion');
	}

}