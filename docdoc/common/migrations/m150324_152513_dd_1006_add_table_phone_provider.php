<?php

class m150324_152513_dd_1006_add_table_phone_provider extends CDbMigration
{
	public function up()
	{
		$this->execute(
			'CREATE TABLE phone_provider
			(
			    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
			    name VARCHAR(255) NOT NULL,
			    enabled TINYINT(1) NOT NULL
			);'
		);

		$this->execute("insert into phone_provider values (1, 'Не распределены', 1)");
		$this->execute("INSERT INTO phone_provider VALUES (2, 'UIS011227', 1)");
		$this->execute("INSERT INTO phone_provider VALUES (3, '3856652', 1)");
		$this->execute("INSERT INTO phone_provider VALUES (4, 'Caravan', 1)");
		$this->execute("INSERT INTO phone_provider VALUES (5, 'ЦетроСеть', 1)");
	}

	public function down()
	{
		$this->dropTable('phone_provider');
	}
}