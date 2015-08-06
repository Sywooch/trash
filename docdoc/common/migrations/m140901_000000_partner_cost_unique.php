<?php

/**
 * Файл класса m140901_000000_partner_cost_unique.
 *
 * Уникальные индексы для таблицы partner_cost
 *
 * @author   Mikhail Vasilyev <mvasilyev@docdoc.ru>
 * @see      https://docdoc.atlassian.net/browse/DD-21
 * @package  migrations
 */
class m140901_000000_partner_cost_unique extends CDbMigration
{

	/**
	 * @return void
	 */
	public function up()
	{
		$this->execute(
			"ALTER TABLE `partner_cost` ADD UNIQUE INDEX `partner_cost_unique` (`partner_id`, `service_id`);"
		);
	}

	/**
	 * @return void
	 */
	public function down()
	{
		$this->execute("ALTER TABLE `partner_cost` DROP INDEX `partner_cost_unique`;");
	}
}
