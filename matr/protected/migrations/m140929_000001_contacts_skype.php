<?php

class m140929_000001_contacts_skype extends CDbMigration
{

	/**
	 * Применяет миграцию в трансакции
	 *
	 * @return bool
	 */
	public function safeUp()
	{
		$this->addColumn("contacts", "skype", "VARCHAR(255) NOT NULL");
	}

	/**
	 * Откатывает миграцию в трансакции
	 *
	 * @return bool
	 */
	public function safeDown()
	{
		$this->dropColumn("contacts", "skype");
	}
}