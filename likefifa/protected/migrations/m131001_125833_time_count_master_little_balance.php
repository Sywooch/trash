<?php

/**
 * Задача https://docdoc.megaplan.ru/task/1002059/card/
 * Поля необходимы для отслеживания отправленных СМС сообщений о блансе мастеру
 * Поля использутся методами Sms::makeLittleBalanceSmsForMaster() и Sms::makeNullBalanceSmsForMaster()
 */
class m131001_125833_time_count_master_little_balance extends CDbMigration
{

	public function up()
	{
		$this->addColumn('lf_master', 'little_balance_time', 'int');
		$this->addColumn('lf_master', 'little_balance_count', 'int');
		$this->addColumn('lf_master', 'null_balance_time', 'int');
		$this->addColumn('lf_master', 'null_balance_count', 'int');
	}

	public function down()
	{
		$this->dropColumn('lf_master', 'little_balance_time');
		$this->dropColumn('lf_master', 'little_balance_count');
		$this->dropColumn('lf_master', 'null_balance_time');
		$this->dropColumn('lf_master', 'null_balance_count');
	}

}