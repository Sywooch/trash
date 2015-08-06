<?php

class m141122_000001_aaa extends CDbMigration
{

	public function safeUp()
	{
		$this->createTable(
			"activation_codes",
			array(
				"id"        => "pk",
				"code"      => "VARCHAR(255) NOT NULL",
				"is_active" => "INT NOT NULL",
			),
			"ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci"
		);
	}

	public function safeDown()
	{
		$this->dropTable("activation_codes");
	}
}