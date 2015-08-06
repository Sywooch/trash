<?php

namespace dfs\modules\payments\migrations;
use dfs\modules\payments\models\PaymentsAccount;

/**
 * m140130_000000_balance_amount class file.
 *
 * Добавляет поле "баланс" для таблицы payments_account
 * Выполняет пересчет нового поля
 *
 * @author   Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @see      https://docdoc.megaplan.ru/task/1003070/card/
 * @package  modules.payments.migrations
 */
class m140130_000000_balance_amount extends \CDbMigration
{

	/**
	 * Применяет миграцию
	 *
	 * @return void
	 */
	public function up()
	{
		$this->addColumn('payments_account', 'amount', 'BIGINT NOT NULL');
		$this->createIndex("payments_account_amount", "payments_account", "amount");

		/**
		 * @var PaymentsAccount[] $paymentsAccounts
		 */
		$paymentsAccounts = PaymentsAccount::model()->findAll();
		if ($paymentsAccounts) {
			foreach ($paymentsAccounts as $model) {
				$model->amount = $model->amount_fake + $model->amount_real;
				$model->save();
			}
		}
	}

	/**
	 * Откатывает миграцию
	 *
	 * @return void
	 */
	public function down()
	{
		$this->dropIndex("payments_account_amount", "payments_account");
		$this->dropColumn('payments_account', 'amount');
	}
}