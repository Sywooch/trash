<?php

class m141024_145222_dd_359_drop_booking_history_table extends CDbMigration
{
	/**
	 * @return bool|void
	 */
	public function up()
	{
		$this->execute("DROP TABLE booking_history;");
	}

	/**
	 * @return bool|void
	 */
	public function down()
	{
		$this->execute('
			CREATE TABLE `booking_history` (
				`id` INT(11) NOT NULL,
				`book_id` INT(11) NOT NULL,
				`status` TINYINT(2) NOT NULL,
				`date_status` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (`id`),
				KEY `book_idx` (`book_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8'
		);
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}
