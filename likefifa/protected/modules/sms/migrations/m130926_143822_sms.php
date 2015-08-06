<?php

namespace dfs\modules\sms\migrations;

/**
 * Class m130926_143822_sms
 *
 * Создает таблицу для хранения СМС
 *
 * @author Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @date 20.11.2013
 *
 * @see https://docdoc.megaplan.ru/task/1002497/card/
 */
class m130926_143822_sms extends CDbMigration
{

	/**
	 * Создание таблицы sms
	 *
	 * @return void
	 */
	public function up()
	{
		$this->createTable(
			'sms',
			array(
				'id' => 'pk',
				'number' => 'string NOT NULL',
				'send_time' => 'int',
				'message' => 'text',
				'status' => 'int',
			),
			'ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci'
		);
	}

	/**
	 * Удаление таблицы sms
	 *
	 * @return void
	 */
	public function down()
	{
		$this->dropTable('sms');
	}
}