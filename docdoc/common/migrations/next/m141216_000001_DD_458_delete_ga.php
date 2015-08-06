<?php

/**
 * Файл класса m141216_000001_DD_458_delete_ga
 *
 * Удаляет GA
 *
 * @author  Mikhail Vasilyev <mvasilyev@docdoc.ru>
 * @link    https://docdoc.atlassian.net/browse/DD-458
 * @package migrations
 */
class m141216_000001_DD_458_delete_ga extends CDbMigration
{

	/**
	 * Применяет миграцию
	 *
	 * @return void
	 */
	public function up()
	{
		$this->dropColumn("city", "site_GA");
		$this->dropColumn("city", "diagnostic_site_GA");
	}

	/**
	 * Откатывает миграцию
	 *
	 * @return void
	 */
	public function down()
	{
		$this->addColumn("city", "site_GA", "VARCHAR(20)");
		$this->addColumn("city", "diagnostic_site_GA", "VARCHAR(20)");

		$this->update("city", ["site_GA" => null, "diagnostic_site_GA" => null]);
		$this->update("city", ["site_GA" => "UA-7682182-11", "diagnostic_site_GA" => "UA-7682182-15"], "id_city = 1");
		$this->update("city", ["site_GA" => "UA-7682182-17", "diagnostic_site_GA" => null], "id_city = 2");
	}
}