<?php

class m140929_000000_pp extends CDbMigration
{

	/**
	 * Применяет миграцию в трансакции
	 *
	 * @return bool
	 */
	public function safeUp()
	{
		$this->addColumn("user", "perfect", "VARCHAR(255) NOT NULL");
		$this->addColumn("user", "payer", "VARCHAR(255) NOT NULL");
	}

	/**
	 * Откатывает миграцию в трансакции
	 *
	 * @return bool
	 */
	public function safeDown()
	{
		$this->dropColumn("user", "perfect");
		$this->dropColumn("user", "payer");
	}
}