<?php

/**
 * Class m140825_121543_DD_52_related_specialty
 *
 */
class m140825_121543_DD_52_related_specialty extends CDbMigration
{

	/**
	 * Добавление сущности - связанные специальности, добавление признака двойная специальность
	 *
	 * @return bool|void
	 */
	public function up()
	{
		$this->execute("
			CREATE TABLE `related_specialty` (
				`specialty_id` INT(11) NOT NULL,
				`related_specialty_id` INT(11) NOT NULL,
				PRIMARY KEY (`specialty_id`, `related_specialty_id`),
				CONSTRAINT `specialty_id_fk` FOREIGN KEY (`specialty_id`) REFERENCES `sector` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
				CONSTRAINT `related_specialty_id_fk` FOREIGN KEY (`related_specialty_id`) REFERENCES `sector` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
			) ENGINE=InnoDB DEFAULT CHARSET=utf8
		");

		$this->execute("ALTER TABLE sector ADD COLUMN `is_double` TINYINT(1) NOT NULL DEFAULT 0");

		$this->createIndex("is_double_idx", "sector", "is_double");
	}

	public function down()
	{
		$this->execute("DROP TABLE IF EXISTS `related_specialty`");

		$this->execute("ALTER TABLE `sector` DROP COLUMN `is_double`");
	}

}