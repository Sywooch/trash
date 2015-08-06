<?php

/**
 * Задача https://docdoc.megaplan.ru/task/1002071/card/
 * Таблица для хранения сообщений рассылок мастерам по e-mail
 */
class m131007_112022_messages extends CDbMigration
{
	
	public function up()
	{
		$this->createTable(
			'messages',
			array(
				'id' => 'pk',
				'message' => 'text',
				'send_time' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
				'type' => 'int',
				'email' => 'VARCHAR (512)',
				'master_id' => 'int',
			),
			'ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci'
		);
	}

	public function down()
	{
		$this->dropTable('messages');
	}

}