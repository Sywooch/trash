<?php

/**
 * Class m150324_131712_DD_1120_add_today_sum
 *
 * Добавление колонок с количеством заявок на сегодня и суммой на сегодня
 *
 */
class m150324_131712_DD_1120_add_today_sum extends CDbMigration
{
	public function up()
	{
		$this->addColumn('clinic_billing', 'today_requests', 'INT(11) DEFAULT NULL');
		$this->addColumn('clinic_billing', 'today_sum', 'INT(11) DEFAULT NULL');

		\Yii::app()
			->db
			->createCommand("UPDATE clinic_billing SET today_requests = agreed_requests, today_sum = agreed_sum")
			->execute();
	}

	public function down()
	{
		$this->dropColumn('clinic_billing', 'today_requests');
		$this->dropColumn('clinic_billing', 'today_sum');
	}
}