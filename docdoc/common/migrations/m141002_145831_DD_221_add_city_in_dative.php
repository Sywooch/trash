<?php

/**
 * Файл класса m141002_145831_DD_221_add_city_in_dative.
 */
class m141002_145831_DD_221_add_city_in_dative extends CDbMigration
{
	/**
	 * Применяет миграцию
	 *
	 * @return void
	 */
	public function up()
	{
		$this->addColumn('city', 'title_dative', 'VARCHAR(50) NOT NULL AFTER title_prepositional');

		$this->update('city', ['title_dative' => 'Москве'], 'rewrite_name = :alias', [':alias' => 'msk']);
		$this->update('city', ['title_dative' => 'Санкт-Петербургу'], 'rewrite_name = :alias', [':alias' => 'spb']);
		$this->update('city', ['title_dative' => 'Другим'], 'rewrite_name = :alias', [':alias' => 'default']);
		$this->update('city', ['title_dative' => 'Екатеринбургу'], 'rewrite_name = :alias', [':alias' => 'ekb']);
		$this->update('city', ['title_dative' => 'Новосибирску'], 'rewrite_name = :alias', [':alias' => 'nsk']);
		$this->update('city', ['title_dative' => 'Перми'], 'rewrite_name = :alias', [':alias' => 'perm']);
		$this->update('city', ['title_dative' => 'Нижнему Новгороду'], 'rewrite_name = :alias', [':alias' => 'nn']);
		$this->update('city', ['title_dative' => 'Казани'], 'rewrite_name = :alias', [':alias' => 'kazan']);
		$this->update('city', ['title_dative' => 'Самаре'], 'rewrite_name = :alias', [':alias' => 'samara']);
	}

	/**
	 * Откатывает миграцию
	 *
	 * @return void
	 */
	public function down()
	{
		$this->dropColumn('city', 'title_dative');
	}
}