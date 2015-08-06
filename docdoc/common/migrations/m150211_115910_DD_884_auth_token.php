<?php

/**
 * Создание таблицы auth_token
 */
class m150211_115910_DD_884_auth_token extends CDbMigration
{
	public function up()
	{
		$this->createTable(
			'auth_token',
			[
				'id'      => 'pk',
				'token'   => 'VARCHAR(50) NOT NULL',
				'type'    => 'VARCHAR(10) NOT NULL',
				'expired' => 'TIMESTAMP NOT NULL',
				'using'   => 'TINYINT NOT NULL DEFAULT "0"',
				'user_id' => 'INT UNSIGNED DEFAULT NULL',
			],
			'ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8'
		);

		$this->createIndex('auth_token_token_idx', 'auth_token', 'token', true);
		$this->createIndex('auth_token_expired_idx', 'auth_token', 'expired');
	}

	public function down()
	{
		$this->dropTable('auth_token');
	}
}
