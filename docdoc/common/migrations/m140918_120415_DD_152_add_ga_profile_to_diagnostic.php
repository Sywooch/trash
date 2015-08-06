<?php

/**
 * Class m140918_120415_DD_152_add_ga_profile_to_diagnostic
 * добавление полей - профиль GA и метрики для диагностики
 */
class m140918_120415_DD_152_add_ga_profile_to_diagnostic extends CDbMigration
{
	/**
	 * @return bool
	 */
	public function up()
	{
		$this->addColumn("city", "diagnostic_site_YA", "VARCHAR(20)");
		$this->addColumn("city", "diagnostic_site_GA", "VARCHAR(20)");
		$this->update('city', ['diagnostic_site_GA' => 'UA-7682182-15'], "rewrite_name = 'msk'");
	}

	/**
	 * @return bool
	 */
	public function down()
	{
		$this->dropColumn("city", "diagnostic_site_YA");
		$this->dropColumn("city", "diagnostic_site_GA");
	}
}