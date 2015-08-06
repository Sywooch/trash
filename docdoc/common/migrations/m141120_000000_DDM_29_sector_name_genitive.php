<?php

/**
 * Файл класса m141120_000000_DDM_29_sector_name_genitive
 *
 * Название специальности в родительном падеже (в единственном и множественном числе)
 *
 * @author  Mikhail Vasilyev <mvasilyev@docdoc.ru>
 * @link    https://docdoc.atlassian.net/browse/DDM-29
 * @package migrations
 */
class m141120_000000_DDM_29_sector_name_genitive extends CDbMigration
{

	/**
	 * Применяет миграцию
	 *
	 * @return void
	 */
	public function up()
	{
		$this->addColumn("sector", "name_genitive", "VARCHAR(64) NOT NULL AFTER name");
		$this->addColumn("sector", "name_plural_genitive", "VARCHAR(64) NOT NULL AFTER name_plural");

		$this->update(
			"sector",
			[
				"name_genitive"        => "Акушера",
				"name_plural_genitive" => "Акушеров",
			],
			"id = 67"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Аллерголога",
				"name_plural_genitive" => "Аллергологов"
			],
			"id = 68"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Андролога",
				"name_plural_genitive" => "Андрологов"
			],
			"id = 69"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Венеролога",
				"name_plural_genitive" => "Венерологов"
			],
			"id = 70"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Гастроэнтеролога",
				"name_plural_genitive" => "Гастроэнтерологов"
			],
			"id = 71"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Гинеколога",
				"name_plural_genitive" => "Гинекологов"
			],
			"id = 72"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Дерматолога",
				"name_plural_genitive" => "Дерматологов"
			],
			"id = 73"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Диетолога",
				"name_plural_genitive" => "Диетологов"
			],
			"id = 74"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Кардиолога",
				"name_plural_genitive" => "Кардиологов"
			],
			"id = 75"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Косметолога",
				"name_plural_genitive" => "Косметологов"
			],
			"id = 76"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Лора (отоларинголога)",
				"name_plural_genitive" => "Лоров (отоларингологов)"
			],
			"id = 77"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Маммолога",
				"name_plural_genitive" => "Маммологов"
			],
			"id = 78"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Мануального терапевта",
				"name_plural_genitive" => "Мануальных терапевтов"
			],
			"id = 79"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Нарколога",
				"name_plural_genitive" => "Наркологов"
			],
			"id = 80"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Невролога",
				"name_plural_genitive" => "Неврологов"
			],
			"id = 81"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Онколога",
				"name_plural_genitive" => "Онкологов"
			],
			"id = 82"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Ортопеда",
				"name_plural_genitive" => "Ортопедов"
			],
			"id = 83"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Окулиста (офтальмолога)",
				"name_plural_genitive" => "Окулистов (офтальмологов)"
			],
			"id = 84"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Педиатра",
				"name_plural_genitive" => "Педиатров"
			],
			"id = 85"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Пластического хирурга",
				"name_plural_genitive" => "Пластических хирургов"
			],
			"id = 86"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Психолога",
				"name_plural_genitive" => "Психологов"
			],
			"id = 87"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Психотерапевта",
				"name_plural_genitive" => "Психотерапевтов"
			],
			"id = 88"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Психиатра",
				"name_plural_genitive" => "Психиатров"
			],
			"id = 89"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Стоматолога",
				"name_plural_genitive" => "Стоматологов"
			],
			"id = 90"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Терапевта",
				"name_plural_genitive" => "Терапевтов"
			],
			"id = 91"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Трихолога",
				"name_plural_genitive" => "Трихологов"
			],
			"id = 92"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Уролога",
				"name_plural_genitive" => "Урологов"
			],
			"id = 93"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Флеболога",
				"name_plural_genitive" => "Флебологов"
			],
			"id = 94"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Хирурга",
				"name_plural_genitive" => "Хирургов"
			],
			"id = 95"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Эндокринолога",
				"name_plural_genitive" => "Эндокринологов"
			],
			"id = 96"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Дерматовенеролога",
				"name_plural_genitive" => "Дерматовенерологов"
			],
			"id = 97"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Проктолога",
				"name_plural_genitive" => "Проктологов"
			],
			"id = 98"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Репродуктолога (ЭКО)",
				"name_plural_genitive" => "Репродуктологов (ЭКО)"
			],
			"id = 99"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "УЗИ-специалиста",
				"name_plural_genitive" => "УЗИ-специалистов"
			],
			"id = 100"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Массажиста",
				"name_plural_genitive" => "Массажистов"
			],
			"id = 101"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Пульмонолога",
				"name_plural_genitive" => "Пульмонологов"
			],
			"id = 102"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Логопеда",
				"name_plural_genitive" => "Логопедов"
			],
			"id = 103"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Иммунолога",
				"name_plural_genitive" => "Иммунологов"
			],
			"id = 104"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Гомеопата",
				"name_plural_genitive" => "Гомеопатов"
			],
			"id = 105"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Гематолога",
				"name_plural_genitive" => "Гематологов"
			],
			"id = 106"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Анестезиолога",
				"name_plural_genitive" => "Анестезиологов"
			],
			"id = 107"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Нефролога",
				"name_plural_genitive" => "Нефрологов"
			],
			"id = 108"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Гепатолога",
				"name_plural_genitive" => "Гепатологов"
			],
			"id = 109"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Ревматолога",
				"name_plural_genitive" => "Ревматологов"
			],
			"id = 110"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Сексолога",
				"name_plural_genitive" => "Сексологов"
			],
			"id = 111"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Инфекциониста",
				"name_plural_genitive" => "Инфекционистов"
			],
			"id = 112"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Физиотерапевта",
				"name_plural_genitive" => "Физиотерапевтов"
			],
			"id = 113"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Неонатолога",
				"name_plural_genitive" => "Неонатологов"
			],
			"id = 114"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Миколога",
				"name_plural_genitive" => "Микологов"
			],
			"id = 115"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Гинеколога-эндокринолога",
				"name_plural_genitive" => "Гинекологов-эндокринологов"
			],
			"id = 117"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Стоматолога-терапевта",
				"name_plural_genitive" => "Стоматологов-терапевтов"
			],
			"id = 119"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Стоматолога-ортопеда",
				"name_plural_genitive" => "Стоматологов-ортопедов"
			],
			"id = 121"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Стоматолога-пародонтолога",
				"name_plural_genitive" => "Стоматологов-пародонтологов"
			],
			"id = 123"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Стоматолога-ортодонта",
				"name_plural_genitive" => "Стоматологов-ортодонтов"
			],
			"id = 125"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Стоматолога-хирурга",
				"name_plural_genitive" => "Стоматологов-хирургов"
			],
			"id = 127"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Стоматолога-гигиениста",
				"name_plural_genitive" => "Стоматологов-гигиенистов"
			],
			"id = 129"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Стоматолога-имплантолога",
				"name_plural_genitive" => "Стоматологов-имплантологов"
			],
			"id = 131"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Эндоскописта",
				"name_plural_genitive" => "Эндоскопистов"
			],
			"id = 133"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Онкодерматолога",
				"name_plural_genitive" => "Онкодерматологов"
			],
			"id = 135"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Кардиохирурга",
				"name_plural_genitive" => "Кардиохирургов"
			],
			"id = 137"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Нейрохирурга",
				"name_plural_genitive" => "Нейрохирургов"
			],
			"id = 139"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Онкогинеколога",
				"name_plural_genitive" => "Онкогинекологов"
			],
			"id = 141"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Челюстно-лицевого хирурга",
				"name_plural_genitive" => "Челюстно-лицевых хирургов"
			],
			"id = 143"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Сосудистого хирурга",
				"name_plural_genitive" => "Сосудистых хирургов"
			],
			"id = 145"
		);
		$this->update(
			"sector",
			[
				"name_genitive"        => "Семейного врача",
				"name_plural_genitive" => "Семейных врачей"
			],
			"id = 147"
		);
	}

	/**
	 * Откатывает миграцию
	 *
	 * @return void
	 */
	public function down()
	{
		$this->dropColumn("sector", "name_genitive");
		$this->dropColumn("sector", "name_plural_genitive");
	}
}