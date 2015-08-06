<?php

/**
 * Файл класса m141216_000000_DD_458_gtm
 *
 * Добавляет GTM
 *
 * @author  Mikhail Vasilyev <mvasilyev@docdoc.ru>
 * @link    https://docdoc.atlassian.net/browse/DD-458
 * @package migrations
 */
class m141216_000000_DD_458_gtm extends CDbMigration
{

	/**
	 * Применяет миграцию
	 *
	 * @return void
	 */
	public function up()
	{
		$this->addColumn("city", "gtm", "VARCHAR(20) NOT NULL");
		$this->addColumn("city", "diagnostic_gtm", "VARCHAR(20) NOT NULL");

		$this->update("city", ["gtm" => "GTM-5TKX3Z", "diagnostic_gtm" => "GTM-KM7SR3"]);
	}

	/**
	 * Откатывает миграцию
	 *
	 * @return void
	 */
	public function down()
	{
		$this->dropColumn("city", "gtm");
		$this->dropColumn("city", "diagnostic_gtm");
	}
}