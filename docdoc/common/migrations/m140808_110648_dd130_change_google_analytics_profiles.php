<?php

/**
 * Файл класса m140808_110648_dd130_change_google_analytics_profiles.
 */
class m140808_110648_dd130_change_google_analytics_profiles extends CDbMigration
{
	/**
	 * Применяет миграцию
	 *
	 * @return void
	 */
	public function up()
	{
		$this->update('city', [ 'site_GA' => null ]);

		$this->update('city', [ 'site_GA' => 'UA-7682182-11' ], 'rewrite_name = "msk"');
		$this->update('city', [ 'site_GA' => 'UA-7682182-17' ], 'rewrite_name = "spb"');
	}

	/**
	 * Откатывает миграцию
	 *
	 * @return void
	 */
	public function down()
	{
	}
}