<?php

/**
 * Добавление новых полей, тексты как добраться пешком и на машине
 */
class m150210_121703_DD_483_clinic_add_columns extends CDbMigration
{
	public function up()
	{
		$this->addColumn('clinic', 'min_price', 'INT DEFAULT NULL');
		$this->addColumn('clinic', 'max_price', 'INT DEFAULT NULL');
		$this->addColumn('clinic', 'count_reviews', 'INT NOT NULL DEFAULT "0"');

		// Устанавливаем количество отзывов для всех клиник
		$this->execute('UPDATE clinic as c SET c.count_reviews = (
			SELECT COUNT(do.id)
			FROM doctor_4_clinic as dc
				INNER JOIN doctor_opinion as do ON (do.doctor_id = dc.doctor_id AND do.allowed = 1 and do.status = "enable")
			WHERE dc.clinic_id = c.id AND dc.type = 1
		)');

		// Устанавливаем минимальную и максимальную цены для всех клиник
		$this->execute('UPDATE clinic as c
			JOIN (
					 SELECT dc.clinic_id as clinic_id, MIN(d.price) as min_price, MAX(d.price) as max_price
					 FROM doctor_4_clinic as dc
						 INNER JOIN doctor as d ON (d.id = dc.doctor_id AND d.status = 3)
					 WHERE dc.type = 1 AND d.price > 0
					 GROUP BY dc.clinic_id
				 ) as t ON (c.id = t.clinic_id)
			SET c.min_price = t.min_price, c.max_price = t.max_price');

		$this->createIndex('clinic_min_price', 'clinic', 'min_price');
		$this->createIndex('clinic_count_reviews', 'clinic', 'count_reviews');
	}

	public function down()
	{
		$this->dropIndex('clinic_min_price', 'clinic');
		$this->dropIndex('clinic_count_reviews', 'clinic');

		$this->dropColumn('clinic', 'min_price');
		$this->dropColumn('clinic', 'max_price');
		$this->dropColumn('clinic', 'count_reviews');
	}
}
