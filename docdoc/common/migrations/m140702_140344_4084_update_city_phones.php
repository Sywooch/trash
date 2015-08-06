<?php

/**
 * Файл класса m140702_140344_4084_update_city_phones.
 *
 * Добавление в таблицу city 4 новых поля из конфига
 */
class m140702_140344_4084_update_city_phones extends CDbMigration
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
	}
}