<?php

class m140720_000001_faq extends CDbMigration
{

	/**
	 * Применяет миграцию в трансакции
	 *
	 * @return bool
	 */
	public function safeUp()
	{
		$this->createTable(
			"faq",
			array(
				"id"          => "pk",
				"title"       => "VARCHAR(255) NOT NULL",
				"text"        => "TEXT NOT NULL",
				"sort"        => "INT NOT NULL",
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
		$this->dropTable("faq");
	}
}