<?php

class m140720_000002_fb extends CDbMigration
{

	/**
	 * Применяет миграцию в трансакции
	 *
	 * @return bool
	 */
	public function safeUp()
	{
		$this->createTable(
			"offer",
			array(
				"id"      => "pk",
				"name"    => "VARCHAR(255) NOT NULL",
				"email"   => "VARCHAR(255) NOT NULL",
				"text"    => "TEXT NOT NULL",
				"is_read" => "INT NOT NULL",
			),
			"ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci"
		);

		$this->createTable(
			"contacts",
			array(
				"id"      => "pk",
				"name"    => "VARCHAR(255) NOT NULL",
				"email"   => "VARCHAR(255) NOT NULL",
				"phone"   => "VARCHAR(255) NOT NULL",
				"text"    => "TEXT NOT NULL",
				"is_read" => "INT NOT NULL",
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
		$this->dropTable("offer");
		$this->dropTable("contacts");
	}
}