<?php

/**
 * Class m140618_134244_clinic_district
 *
 */
class m140618_134244_clinic_district extends CDbMigration
{
	/**
	 * Добавление района клинике
	 */
	public function safeUp()
	{
		//добавляем столбец
		$this->execute("ALTER TABLE clinic ADD COLUMN district_id int(11) NULL after `show_in_advert`");

		$this->execute("
		ALTER TABLE `clinic`
			ADD INDEX `fk_district_id_idx` (`district_id` ASC);
		ALTER TABLE `clinic`
		ADD CONSTRAINT `fk_district_id`
		  FOREIGN KEY (`district_id`)
		  REFERENCES `district` (`id`)
		  ON DELETE SET NULL
		  ON UPDATE CASCADE");

		//для всех клиник проставляем район по метро
		$this->execute(
			"update clinic SET district_id = (
							SELECT d.id
							FROM district d
							INNER JOIN district_has_underground_station dhus ON (dhus.id_district = d.id)
							INNER JOIN underground_station us ON (us.id = dhus.id_station)
							INNER JOIN underground_station_4_clinic us4c ON (us4c.undegraund_station_id = us.id)
								WHERE us4c.clinic_id = clinic.id
							LIMIT 1
							)
					"
		);

		//создаем таблицу с ближайшими районами
		$this->execute("
			CREATE TABLE `closest_district` (
				`district_id`  INT(11) NOT NULL,
				`closest_district_id` INT(11) NOT NULL,
				`priority` TINYINT(3) DEFAULT NULL,
				PRIMARY KEY (`district_id`, `closest_district_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8
		");

		//внешние ключи для closest_district
		$this->execute("
			ALTER TABLE `closest_district`
				ADD INDEX `fk_district_id_idx` (`district_id` ASC);
			ALTER TABLE `closest_district`
			ADD CONSTRAINT `fk_district_id`
				FOREIGN KEY (`district_id`)
				REFERENCES `district` (`id`)
				ON DELETE CASCADE
				ON UPDATE CASCADE
		");

		$this->execute("
			ALTER TABLE `closest_district`
				ADD INDEX `fk_closest_district_id_idx` (`closest_district_id` ASC);
			ALTER TABLE `closest_district`
			ADD CONSTRAINT `fk_closest_district_id`
				FOREIGN KEY (`closest_district_id`)
				REFERENCES `district` (`id`)
				ON DELETE CASCADE
				ON UPDATE CASCADE
		  ");

		//заполняем эту таблицу на основании ближайших метро
		$this->execute("
		INSERT INTO closest_district (district_id, closest_district_id, priority)
			SELECT district_id, closest_district_id, priority FROM (
				SELECT us.name as us_src_name, dhus1.id_district as district_id , us1.name as us_name,
					cs.priority, d.name as district_name, d.id as closest_district_id
					FROM underground_station us
					INNER JOIN closest_station cs ON (us.id = cs.station_id)
					INNER JOIN underground_station us1 ON (cs.closest_station_id = us1.id)
					INNER JOIN district_has_underground_station dhus1 ON (dhus1.id_station = us.id)
					INNER JOIN district_has_underground_station dhus ON (dhus.id_station = us1.id)
					INNER JOIN district d ON (d.id = dhus.id_district)
					ORDER BY us.name, cs.priority
			) as t GROUP BY t.district_id, t.closest_district_id
		");
	}

	public function safeDown()
	{
		$this->execute("DROP TABLE IF EXISTS `closest_district`");

		$this->execute("ALTER TABLE `clinic` DROP FOREIGN KEY `fk_district_id`");
		$this->execute(
			"ALTER TABLE `clinic`
						DROP COLUMN `district_id`"
		);
	}
}