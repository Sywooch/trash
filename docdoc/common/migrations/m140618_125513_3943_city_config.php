<?php

/**
 * Файл класса m140618_125513_3943_city_config.
 *
 * Добавление в таблицу city 4 новых поля из конфига
 */
class m140618_125513_3943_city_config extends CDbMigration
{
	/**
	 * Применяет миграцию
	 *
	 * @return void
	 */
	public function up()
	{
		$data = array(
			'msk' => array(
				'site_phone'  => '74952367276',
				'site_office' => '74955653293',
				'site_YA'     => '11631337',
				'site_GA'     => 'UA-7682182-11'
			),
			'spb' => array(
				'site_phone'  => '78123856652',
				'site_office' => '78123856652',
				'site_YA'     => '19018384',
				'site_GA'     => 'UA-7682182-17'
			)
		);

		$this->addColumn("city", "site_phone", "CHAR(12)");
		$this->addColumn("city", "site_office", "CHAR(12)");
		$this->addColumn("city", "site_YA", "VARCHAR(20)");
		$this->addColumn("city", "site_GA", "VARCHAR(20)");

		foreach ($data as $prefix => $values)
		{
			$this->update('city', $values, 'rewrite_name = :name', array('name' => $prefix));
		}
	}

	/**
	 * Откатывает миграцию
	 *
	 * @return void
	 */
	public function down()
	{
		$this->dropColumn("city", "site_phone");
		$this->dropColumn("city", "site_office");
		$this->dropColumn("city", "site_YA");
		$this->dropColumn("city", "site_GA");
	}
}