<?php

/**
 * Файл класса m141118_000000_DD_548_sector_name_plural
 *
 * Название специальности во множественном числе
 *
 * @author  Mikhail Vasilyev <mvasilyev@docdoc.ru>
 * @link    https://docdoc.atlassian.net/browse/DD-548
 * @package migrations
 */
class m141118_000000_DD_548_sector_name_plural extends CDbMigration
{

	/**
	 * Применяет миграцию
	 *
	 * @return void
	 */
	public function up()
	{
		$this->addColumn("sector", "name_plural", "VARCHAR(64) NOT NULL AFTER name");

		$this->update("sector", ["name_plural" => "Акушеры"], "id = 67");
		$this->update("sector", ["name_plural" => "Аллергологи"], "id = 68");
		$this->update("sector", ["name_plural" => "Андрологи"], "id = 69");
		$this->update("sector", ["name_plural" => "Венерологи"], "id = 70");
		$this->update("sector", ["name_plural" => "Гастроэнтерологи"], "id = 71");
		$this->update("sector", ["name_plural" => "Гинекологи"], "id = 72");
		$this->update("sector", ["name_plural" => "Дерматологи"], "id = 73");
		$this->update("sector", ["name_plural" => "Диетологи"], "id = 74");
		$this->update("sector", ["name_plural" => "Кардиологи"], "id = 75");
		$this->update("sector", ["name_plural" => "Косметологи"], "id = 76");
		$this->update("sector", ["name_plural" => "Лоры (отоларингологи)"], "id = 77");
		$this->update("sector", ["name_plural" => "Маммологи"], "id = 78");
		$this->update("sector", ["name_plural" => "Мануальные терапевты"], "id = 79");
		$this->update("sector", ["name_plural" => "Наркологи"], "id = 80");
		$this->update("sector", ["name_plural" => "Неврологи"], "id = 81");
		$this->update("sector", ["name_plural" => "Онкологи"], "id = 82");
		$this->update("sector", ["name_plural" => "Ортопеды"], "id = 83");
		$this->update("sector", ["name_plural" => "Окулисты (офтальмологи)"], "id = 84");
		$this->update("sector", ["name_plural" => "Педиатры"], "id = 85");
		$this->update("sector", ["name_plural" => "Пластические хирурги"], "id = 86");
		$this->update("sector", ["name_plural" => "Психологи"], "id = 87");
		$this->update("sector", ["name_plural" => "Психотерапевты"], "id = 88");
		$this->update("sector", ["name_plural" => "Психиатры"], "id = 89");
		$this->update("sector", ["name_plural" => "Стоматологи"], "id = 90");
		$this->update("sector", ["name_plural" => "Терапевты"], "id = 91");
		$this->update("sector", ["name_plural" => "Трихологи"], "id = 92");
		$this->update("sector", ["name_plural" => "Урологи"], "id = 93");
		$this->update("sector", ["name_plural" => "Флебологи"], "id = 94");
		$this->update("sector", ["name_plural" => "Хирурги"], "id = 95");
		$this->update("sector", ["name_plural" => "Эндокринологи"], "id = 96");
		$this->update("sector", ["name_plural" => "Дерматовенерологи"], "id = 97");
		$this->update("sector", ["name_plural" => "Проктологи"], "id = 98");
		$this->update("sector", ["name_plural" => "Репродуктологи (ЭКО)"], "id = 99");
		$this->update("sector", ["name_plural" => "УЗИ-специалисты"], "id = 100");
		$this->update("sector", ["name_plural" => "Массажисты"], "id = 101");
		$this->update("sector", ["name_plural" => "Пульмонологи"], "id = 102");
		$this->update("sector", ["name_plural" => "Логопеды"], "id = 103");
		$this->update("sector", ["name_plural" => "Иммунологи"], "id = 104");
		$this->update("sector", ["name_plural" => "Гомеопаты"], "id = 105");
		$this->update("sector", ["name_plural" => "Гематологи"], "id = 106");
		$this->update("sector", ["name_plural" => "Анестезиологи"], "id = 107");
		$this->update("sector", ["name_plural" => "Нефрологи"], "id = 108");
		$this->update("sector", ["name_plural" => "Гепатологи"], "id = 109");
		$this->update("sector", ["name_plural" => "Ревматологи"], "id = 110");
		$this->update("sector", ["name_plural" => "Сексологи"], "id = 111");
		$this->update("sector", ["name_plural" => "Инфекционисты"], "id = 112");
		$this->update("sector", ["name_plural" => "Физиотерапевты"], "id = 113");
		$this->update("sector", ["name_plural" => "Неонатологи"], "id = 114");
		$this->update("sector", ["name_plural" => "Микологи"], "id = 115");
		$this->update("sector", ["name_plural" => "Гинекологи-эндокринологи"], "id = 117");
		$this->update("sector", ["name_plural" => "Стоматологи-терапевты"], "id = 119");
		$this->update("sector", ["name_plural" => "Стоматологи-ортопеды"], "id = 121");
		$this->update("sector", ["name_plural" => "Стоматологи-пародонтологи"], "id = 123");
		$this->update("sector", ["name_plural" => "Стоматологи-ортодонты"], "id = 125");
		$this->update("sector", ["name_plural" => "Стоматологи-хирурги"], "id = 127");
		$this->update("sector", ["name_plural" => "Стоматологи-гигиенисты"], "id = 129");
		$this->update("sector", ["name_plural" => "Стоматологи-имплантологи"], "id = 131");
		$this->update("sector", ["name_plural" => "Эндоскописты"], "id = 133");
		$this->update("sector", ["name_plural" => "Онкодерматологи"], "id = 135");
		$this->update("sector", ["name_plural" => "Кардиохирурги"], "id = 137");
		$this->update("sector", ["name_plural" => "Нейрохирурги"], "id = 139");
		$this->update("sector", ["name_plural" => "Онкогинекологи"], "id = 141");
		$this->update("sector", ["name_plural" => "Челюстно-лицевые хирурги"], "id = 143");
		$this->update("sector", ["name_plural" => "Сосудистые хирурги"], "id = 145");
		$this->update("sector", ["name_plural" => "Семейные врачи"], "id = 147");
	}

	/**
	 * Откатывает миграцию
	 *
	 * @return void
	 */
	public function down()
	{
		$this->dropColumn("sector", "name_plural");
	}
}