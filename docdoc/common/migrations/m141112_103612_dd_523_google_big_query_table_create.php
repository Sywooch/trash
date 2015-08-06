<?php

class m141112_103612_dd_523_google_big_query_table_create extends CDbMigration
{
	public function up()
	{
		$this->execute("

		CREATE TABLE `google_big_query` (
			`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
			`token` VARCHAR(255) DEFAULT NULL,
			`mtime` TIMESTAMP NULL DEFAULT NULL,
			PRIMARY KEY (`id`),
			UNIQUE KEY `id` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		"
		);

	}

	public function down()
	{
		$this->execute("DROP TABLE  google_big_query;");
	}
}
