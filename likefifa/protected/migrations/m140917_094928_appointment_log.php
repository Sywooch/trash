<?php

/**
 * Создает таблицу логирования заявок
 *
 * Class m140917_094928_appointment_log
 */
class m140917_094928_appointment_log extends CDbMigration
{
	public function up()
	{
		$this->execute(
			"CREATE TABLE `lf_appointment_log` (
		  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
		  `appointment_id` INT(11) NOT NULL,
		  `action` VARCHAR(20) NOT NULL,
		  `data` TEXT NOT NULL,
		  `created` DATETIME NOT NULL,
		  `master_id` INT NULL DEFAULT NULL,
		  `salon_id` INT NULL DEFAULT NULL,
		  `admin_id` INT NULL DEFAULT NULL,
		  PRIMARY KEY (`id`),
		  INDEX `lf_appointment_log_aid_idx` (`appointment_id` ASC),
		  INDEX `lf_appointment_log_master_id_idx` (`master_id` ASC),
		  INDEX `lf_appointment_log_salon_Id_idx` (`salon_id` ASC),
		  INDEX `lf_appointment_log_admin_id_idx` (`admin_id` ASC),
		  CONSTRAINT `lf_appointment_log_aid`
			FOREIGN KEY (`appointment_id`)
			REFERENCES `lf_appointment` (`id`)
			ON DELETE CASCADE
			ON UPDATE CASCADE,
		  CONSTRAINT `lf_appointment_log_master_id`
			FOREIGN KEY (`master_id`)
			REFERENCES `lf_master` (`id`)
			ON DELETE CASCADE
			ON UPDATE CASCADE,
		  CONSTRAINT `lf_appointment_log_salon_Id`
			FOREIGN KEY (`salon_id`)
			REFERENCES `lf_salons` (`id`)
			ON DELETE CASCADE
			ON UPDATE CASCADE,
		  CONSTRAINT `lf_appointment_log_admin_id`
			FOREIGN KEY (`admin_id`)
			REFERENCES `admin` (`id`)
			ON DELETE CASCADE
			ON UPDATE CASCADE);"
		);
	}

	public function down()
	{
		$this->dropTable('lf_appointment_log');
	}
}