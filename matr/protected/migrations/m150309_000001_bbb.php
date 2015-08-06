<?php

class m150309_000001_bbb extends CDbMigration
{

	public function safeUp()
	{
		$this->insert(
			"user",
			array(
				"email"     => "c67dd86ba@cbb270.xxx",
				"password"  => UserIdentity::getPassword("vipvip"),
				"name"      => "vip",
				"is_active" => 1,
				"type" => 1
			)
		);

		$this->insert(
			"user",
			array(
				"email"     => "c67dd86ba@cbb270s.xx",
				"password"  => UserIdentity::getPassword("vipvip"),
				"name"      => "vip",
				"is_active" => 1,
				"type" => 2
			)
		);
	}

	public function safeDown()
	{

	}
}