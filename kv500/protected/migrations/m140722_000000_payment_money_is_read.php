<?php

class m140722_000000_payment_money_is_read extends CDbMigration
{

	/**
	 * Применяет миграцию в трансакции
	 *
	 * @return bool
	 */
	public function safeUp()
	{
		$this->addColumn("payment_money", "is_read", "INT NOT NULL");
	}

	/**
	 * Откатывает миграцию в трансакции
	 *
	 * @return bool
	 */
	public function safeDown()
	{
		$this->dropColumn("payment_money", "is_read");
	}
}