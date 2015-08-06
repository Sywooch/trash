<?php
use dfs\docdoc\models\RequestModel;

/**
 * @package migrations
 */
class m150302_000000_DD_967_change_diagnostic_online extends CDbMigration
{
	/**
	 * Применяет миграцию
	 *
	 * @return void
	 */
	public function safeUp()
	{
		$this->execute("UPDATE request SET billing_status = " . RequestModel::BILLING_STATUS_PAID . "
			WHERE billing_status=" . RequestModel::BILLING_STATUS_YES . " AND date_billing < '2015-02-01'");
	}

	/**
	 * Применяет down
	 *
	 * @return void
	 */
	public function safeDown()
	{
		$this->execute("UPDATE request SET billing_status = " . RequestModel::BILLING_STATUS_YES . "
			WHERE billing_status=" . RequestModel::BILLING_STATUS_PAID . " AND date_billing < '2015-02-01'");
	}
}