<?php

class m140720_000000_news extends CDbMigration
{

	/**
	 * Применяет миграцию в трансакции
	 *
	 * @return bool
	 */
	public function safeUp()
	{
		$this->createTable(
			"news",
			array(
				"id"          => "pk",
				"title"       => "VARCHAR(255) NOT NULL",
				"description" => "TEXT NOT NULL",
				"text"        => "TEXT NOT NULL",
				"cover"       => "VARCHAR(64) NOT NULL",
				"date"        => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
			),
			"ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci"
		);
	}

	/**
	 * Откатывает миграцию в трансакции
	 *
	 * @return bool
	 */
	public function safeDown()
	{
		$this->dropTable("news");
	}
}