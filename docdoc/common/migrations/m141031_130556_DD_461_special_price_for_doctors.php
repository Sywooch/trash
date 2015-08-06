<?php

/**
 * Class m141031_130556_DD_461_special_price_for_doctors
 *
 * Файл с условиями:
 * https://docs.google.com/a/docdoc.ru/spreadsheets/d/18I6HzhqopkyldIbFHLK3SujB58u0vku4pJ-fQHK8Sd0/edit#gid=0
 */
class m141031_130556_DD_461_special_price_for_doctors extends CDbMigration
{
	/**
	 * Проставляем спеццену 550р для врачей
	 */
	public function safeUp()
	{
		$this->execute("
			UPDATE doctor t1
			INNER JOIN doctor_4_clinic t2 ON t2.doctor_id=t1.id
			SET t1.special_price=550
			WHERE
				t2.clinic_id IN (
					# Викимед
					86,
					# Добромед кроме бульвара дмитрия донского
					1592, 1595, 1596, 1597, 1598, 1599, 1600, 1601, 1602, 1603, 1605, 1606, 1607, 1608, 2564,
					# Евромедпрестиж
					154, 291, 292,
					# МедЦентрСервис
					44, 250, 251, 252, 253, 254, 255, 256, 257, 258, 259, 260, 2275, 2276,
					# МЦ в марьино
					904,
					# он клиник
					13, 230, 231, 232, 233, 234,
					# чудо доктор
					55, 249
				)
				AND t1.id NOT IN (693, 3081, 49, 3018, 1240, 30, 29, 3162)
				AND t1.status = 3

				-- не будем сбрасывать если цена уже ниже
				AND (t1.special_price > 550 OR t1.special_price IS NULL)
				AND t1.price> 550
		");

		// для см
		$this->execute("
			UPDATE doctor t1
			INNER JOIN doctor_4_clinic t2 ON t2.doctor_id=t1.id
			INNER JOIN doctor_sector t3 ON t3.doctor_id=t1.id
			SET t1.special_price=550
			WHERE
				t2.clinic_id IN (
					# см
					46, 235, 236, 237, 238, 240, 241, 351, 775, 1861, 1939
				)
				AND t3.sector_id IN (72,93,71,98,77,73,78)
				AND t1.id NOT IN (4884,5822,8442,706,7284,6490,12175,463,12207,2687,1577,799,813,943,940,3557,934,906,895,874)
				AND t1.status = 3

				-- не будем сбрасывать если цена уже ниже
				AND (t1.special_price > 550 OR t1.special_price IS NULL)
				AND t1.price> 550
		");
	}

	public function safeDown()
	{
		return true;
	}
}