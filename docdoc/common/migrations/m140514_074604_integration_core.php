<?php

/**
 * Class m140514_074604_integration_core
 *
 * Изменение БД для расписаний
 *
 * @link https://docdoc.megaplan.ru/task/1003666/card/
 *
 */
class m140514_074604_integration_core extends CDbMigration {

	/**
	 * запросы, которые нужно выполнить при миграции
	 */
	public function safeUp(){

		//клиникам добавляем информацию об API
		$this->execute("
			ALTER TABLE `clinic`
				ADD COLUMN `api` VARCHAR(20) NULL COMMENT 'Имя API-интерфейса для клиники' AFTER `diag_settings_id`,
				ADD COLUMN `external_id` VARCHAR(50) NULL COMMENT 'Идентификатор клиники в МИС' AFTER `api`");


		//добавление автоинкрементного PK
		$this->execute("
			ALTER TABLE `doctor_4_clinic`
				ADD COLUMN `id` INT(11) NOT NULL AUTO_INCREMENT AFTER `schedule_step`,
				ADD COLUMN `doc_external_id` VARCHAR(50) NULL AFTER `id`,
				ADD COLUMN `schedule_rule` TEXT NULL AFTER `doc_external_id`,
				DROP PRIMARY KEY,
				ADD PRIMARY KEY (`id`),
				ADD INDEX `doctor_clinic_idx` (`doctor_id` ASC, `clinic_id` ASC)");

		//убираем дефолтные значения для doctor_id b clinic_id
		$this->execute("
			ALTER TABLE `doctor_4_clinic`
				CHANGE COLUMN `doctor_id` `doctor_id` INT(11) NOT NULL ,
				CHANGE COLUMN `clinic_id` `clinic_id` INT(11) NOT NULL");


		//слоты
		$this->execute("
			CREATE TABLE `slot` (
			  `id` bigint(15) NOT NULL AUTO_INCREMENT,
			  `doctor_4_clinic_id` int(11) NOT NULL,
			  `start_time` timestamp NULL,
			  `finish_time` timestamp NULL,
			  `external_id` varchar(50) NOT NULL,
			  PRIMARY KEY (`id`),
			  KEY `doctor_4_clinic_idx` (`doctor_4_clinic_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8");

		//создаем таблицу броней
		$this->execute("
		CREATE TABLE `booking` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `request_id` int(11) unsigned NOT NULL,
		  `slot_id` bigint(15) NOT NULL,
		  `status` tinyint(2) NOT NULL,
		  `date_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
		  `external_id` varchar(50) NOT NULL,
		  PRIMARY KEY (`id`),
		  KEY `request_id_idx` (`request_id`),
		  KEY `slot_id_idx` (`slot_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8
		");

		//создаем историю брони
		$this->execute("
			CREATE TABLE `booking_history` (
			  `id` int(11) NOT NULL,
			  `book_id` int(11) NOT NULL,
			  `status` tinyint(2) NOT NULL,
			  `date_status` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (`id`),
			KEY `book_idx` (`book_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8");


	}

	/**
	 * откат
	 */
	public function safeDown()
	{
		$this->execute("DROP TABLE  IF EXISTS `booking`");
		$this->execute("DROP TABLE  IF EXISTS `booking_history`");
		$this->execute("DROP TABLE  IF EXISTS `slot`");
		$this->execute("
			ALTER TABLE `doctor_4_clinic`
				DROP COLUMN `schedule_rule`,
				DROP COLUMN `doc_external_id`,
				DROP COLUMN `id`,
				DROP PRIMARY KEY,
				ADD PRIMARY KEY (`doctor_id`, `clinic_id`),
				DROP INDEX `doctor_clinic_idx`");

		$this->execute("
			ALTER TABLE `clinic`
				DROP COLUMN `external_id`,
				DROP COLUMN `api`");

	}

}
