<?php

/**
 * Файл класса m141211_000000_DD_690_partner_not_merged_requests
 *
 * Добавляет флаг для партнера благодаря которому заявки не склеиваются
 *
 * @author  Mikhail Vasilyev <mvasilyev@docdoc.ru>
 * @link    https://docdoc.atlassian.net/browse/DD-690
 * @package migrations
 */
class m141211_000000_DD_690_partner_not_merged_requests extends CDbMigration
{

	/**
	 * Применяет миграцию
	 *
	 * @return void
	 */
	public function up()
	{
		$this->addColumn("partner", "not_merged_requests", "INT NOT NULL");

		$this->update("partner", ["not_merged_requests" => 1], "id = 16");
		$this->update("partner", ["not_merged_requests" => 1], "id = 171");
		$this->update("partner", ["not_merged_requests" => 1], "id = 185");
	}

	/**
	 * Откатывает миграцию
	 *
	 * @return void
	 */
	public function down()
	{
		$this->dropColumn("partner", "not_merged_requests");
	}
}