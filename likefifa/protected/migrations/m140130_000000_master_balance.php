<?php

use dfs\modules\payments\migrations\m140130_000000_balance_amount;

/**
 * m140130_000000_master_balance class file.
 *
 * Добавляет поле "баланс" для таблицы payments_account
 * Выполняет пересчет нового поля
 *
 * Добавляет поле-флаг "блокировка баланса" для таблицы lf_master
 *
 * @author   Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @see      https://docdoc.megaplan.ru/task/1003070/card/
 * @package  migrations
 */
class m140130_000000_master_balance extends m140130_000000_balance_amount
{

	/**
	 * Применяет миграцию
	 *
	 * @return void
	 */
	public function up()
	{
		$this->addColumn('lf_master', 'is_blocked', 'INT NOT NULL');
		$this->createIndex("lf_master_is_blocked", "lf_master", "is_blocked");

		return parent::up();
	}

	/**
	 * Откатывает миграцию
	 *
	 * @return void
	 */
	public function down()
	{
		$this->dropIndex("lf_master_is_blocked", "lf_master");
		$this->dropColumn('lf_master', 'is_blocked');

		return parent::down();
	}
}