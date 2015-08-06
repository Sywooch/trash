<?php

class m140712_000000_core extends CDbMigration
{

	/**
	 * Применяет миграцию в трансакции
	 *
	 * @return bool
	 */
	public function safeUp()
	{
		$this->createTable(
			"user",
			array(
				"id"                => "pk",
				"email"             => "VARCHAR(128) NOT NULL",
				"password"          => "CHAR(40) NOT NULL",
				"name"              => "VARCHAR(128) NOT NULL",
				"skype"             => "VARCHAR(64) NOT NULL",
				"phone"             => "VARCHAR(20) NOT NULL",
				"city"              => "VARCHAR(16) NOT NULL",
				"parent_id"         => "INT NOT NULL",
				"created"           => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
				"balance_personal"  => "FLOAT NOT NULL",
				"balance_shop"      => "FLOAT NOT NULL",
				"is_run_out_childs" => "INT NOT NULL",
				"is_active"         => "INT NOT NULL",
				"type"              => "INT NOT NULL",
				"group_number"      => "INT NOT NULL",
			),
			"ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci"
		);

		$this->createTable(
			"operation",
			array(
				"id"        => "pk",
				"user_from" => "INT NOT NULL",
				"user_to"   => "INT NOT NULL",
				"sum"       => "FLOAT NOT NULL",
				"date"      => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
			),
			"ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci"
		);

		$this->createTable(
			"payment_money",
			array(
				"id"         => "pk",
				"user_id"    => "INT NOT NULL",
				"withdrawal" => "FLOAT NOT NULL",
				"text"       => "TEXT NOT NULL",
				"date"       => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
			),
			"ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci"
		);

		$this->createTable(
			"payment",
			array(
				"id"        => "pk",
				"user_id"   => "INT NOT NULL",
				"sum"       => "FLOAT NOT NULL",
				"date"      => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
				"date_from" => "TIMESTAMP",
				"date_to"   => "TIMESTAMP",
				"discount"  => "INT NOT NULL",
			),
			"ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci"
		);

		$this->createIndex("user_parent_id", "user", "parent_id");
		$this->createIndex("user_is_run_out_childs", "user", "is_run_out_childs");
		$this->createIndex("user_is_active", "user", "is_active");

		$this->addForeignKey("operation_user_from", "operation", "user_from", "user", "id");
		$this->addForeignKey("operation_user_to", "operation", "user_to", "user", "id");

		$this->addForeignKey("payment_money_user_id", "payment_money", "user_id", "user", "id");

		$this->addForeignKey("operation_user_id", "payment", "user_id", "user", "id");

		$this->insert(
			"user",
			array(
				"email"     => "c67dd86ba@cbb270.xx",
				"password"  => UserIdentity::getPassword("vipvip"),
				"name"      => "vip",
				"is_active" => 1,
			)
		);
	}

	/**
	 * Откатывает миграцию в трансакции
	 *
	 * @return bool
	 */
	public function safeDown()
	{

	}
}