<?php

/**
 * Добавление адреса для писем на который можно ответить
 */
class m141219_122221_DD_714_mailquery_reply extends CDbMigration
{
	public function up()
	{
		$this->addColumn('mailQuery', 'reply', 'varchar(255) NOT NULL');
	}

	public function down()
	{
		$this->dropColumn('mailQuery', 'reply');
	}
}
