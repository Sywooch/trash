<?php

/**
 * m140415_000000_lf_group_rewrite_name class file.
 *
 * Добавляет абривиатуры для специализаций
 *
 * @author   Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @see      https://docdoc.megaplan.ru/task/1003491/card/
 * @package  migrations
 */
class m140415_000000_lf_group_rewrite_name extends CDbMigration
{

	/**
	 * Применяет миграцию
	 *
	 * @return void
	 */
	public function safeUp()
	{
		$this->addColumn("lf_group", "rewrite_name", "VARCHAR(64) NOT NULL");

		$this->update("lf_group", array("rewrite_name" => "visagiste"), "id = :id", array(":id" => 1));
		$this->update("lf_group", array("rewrite_name" => "hairdresser"), "id = :id", array(":id" => 2));
		$this->update("lf_group", array("rewrite_name" => "nail-service"), "id = :id", array(":id" => 3));
		$this->update("lf_group", array("rewrite_name" => "masseur"), "id = :id", array(":id" => 4));
		$this->update("lf_group", array("rewrite_name" => "cosmetologist"), "id = :id", array(":id" => 5));
		$this->update("lf_group", array("rewrite_name" => "piercing-tattoos"), "id = :id", array(":id" => 6));
	}

	/**
	 * Откатывает миграцию
	 *
	 * @return void
	 */
	public function safeDown()
	{
		$this->dropColumn("lf_group", "rewrite_name");
	}
}