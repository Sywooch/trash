<?php

/**
 * m140428_000000_sector_group_id class file.
 *
 * Связь сектора и специализации
 *
 * @author   Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @see      https://docdoc.megaplan.ru/task/1002975/card/
 * @package  migrations
 */
class m140428_000000_sector_group_id extends CDbMigration
{

	/**
	 * Применяет миграцию
	 *
	 * @return void
	 */
	public function up()
	{
		$this->addColumn("sector", "group_id", "INT");
		$this->addForeignKey("sector_group_id", "sector", "group_id", "lf_group", "id");
		$this->update("sector", array("group_id" => "3"), "id = :id", array(":id" => 108));
		$this->update("sector", array("group_id" => "2"), "id = :id", array(":id" => 109));
		$this->update("sector", array("group_id" => "1"), "id = :id", array(":id" => 110));
		$this->update("sector", array("group_id" => "5"), "id = :id", array(":id" => 112));
	}

	/**
	 * Откатывает миграцию
	 *
	 * @return void
	 */
	public function down()
	{
		$this->dropForeignKey("sector_group_id", "sector");
		$this->dropColumn("sector", "group_id");
	}
}