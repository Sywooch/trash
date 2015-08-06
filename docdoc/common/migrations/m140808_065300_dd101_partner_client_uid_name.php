<?php

/**
 * Файл класса m140808_065300_dd101_partner_client_uid_name.
 */
class m140808_065300_dd101_partner_client_uid_name extends CDbMigration
{
	/**
	 * Применяет миграцию
	 *
	 * @return void
	 */
	public function up()
	{
		$this->addColumn('partner', 'param_client_uid_name', 'varchar(50) DEFAULT NULL');

		$this->update('partner', [ 'param_client_uid_name' => 'cpamit_uid' ], 'login = :login', [ 'login' => 'admitaddocdoc' ]);
		$this->update('partner', [ 'param_client_uid_name' => 'actionpay' ], 'login = :login', [ 'login' => 'actionpaydocdoc' ]);
	}

	/**
	 * Откатывает миграцию
	 *
	 * @return void
	 */
	public function down()
	{
		$this->dropColumn('partner', 'param_client_uid_name');
	}
}
