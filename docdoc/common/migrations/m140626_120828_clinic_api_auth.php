<?php

class m140626_120828_clinic_api_auth extends CDbMigration
{
	/**
	 * добавление полей, которые используются для авторизации в МИС клиники
	 */
	public function safeUp()
	{
		//удаляем связь доктор-клиника, у которых нет доктора
		$withoutDoc = Yii::app()
			->db
			->createCommand("
				SELECT d4c.doctor_id
				FROM
					doctor_4_clinic d4c
				LEFT JOIN doctor d ON d.id = d4c.doctor_id
				WHERE d.id IS NULL")
			->queryColumn();

		if (count($withoutDoc)) {
			Yii::app()
				->db
				->createCommand()
				->delete(
					'doctor_4_clinic',
					array('in', 'doctor_id', $withoutDoc)
				);
		}

		//удаление связи доктор-клиника, у которых нет клиники
		$withoutClinic = Yii::app()
			->db
			->createCommand("
				SELECT d4c.clinic_id
				FROM
					doctor_4_clinic d4c
				LEFT JOIN clinic c ON (c.id = d4c.clinic_id)
				WHERE c.id IS NULL")
			->queryColumn();

		if (count($withoutClinic)) {
			Yii::app()
				->db
				->createCommand()
				->delete(
					'doctor_4_clinic',
					array('in', 'clinic_id', $withoutClinic)
				);
		}

		//накладываем внешние ключи
		$this->execute(
			"ALTER TABLE `doctor_4_clinic`
				ADD CONSTRAINT `fk_doctor`
					FOREIGN KEY (`doctor_id`)
					REFERENCES `doctor` (`id`)
					ON DELETE CASCADE
					ON UPDATE CASCADE,
				ADD CONSTRAINT `fk_clinic`
					FOREIGN KEY (`clinic_id`)
					REFERENCES `clinic` (`id`)
					ON DELETE RESTRICT
					ON UPDATE CASCADE
		");

		$this->execute(
			"ALTER TABLE `clinic`
				CHANGE COLUMN `api` `api_url` VARCHAR(255) NULL DEFAULT NULL COMMENT 'URL API-интерфейса для клиники' ,
				ADD COLUMN `api_login` VARCHAR(50) NULL DEFAULT NULL COMMENT 'логин для авторизации в МИС' AFTER `district_id`,
				ADD COLUMN `api_password` VARCHAR(50) NULL DEFAULT NULL COMMENT 'пароль для авторизации в МИС' AFTER `api_login`
		");

		$this->execute(
			"ALTER TABLE `clinic`
				ADD INDEX `external_id_idx` (`external_id` ASC)
		");

		$this->execute(
			"ALTER TABLE `doctor_4_clinic`
				ADD INDEX `external_id_idx` (`doc_external_id` ASC)
		");

		$this->execute(
			"ALTER TABLE `clinic`
				ADD INDEX `name_idx` (`name`(100) ASC)
		");

		$this->execute(
			"ALTER TABLE `doctor`
				ADD INDEX `name_idx` (`name`(100) ASC)
		");
	}

	/**
	 * откат
	 */
	public function safeDown()
	{
		$this->execute("
			ALTER TABLE `doctor_4_clinic`
				DROP FOREIGN KEY `fk_doctor`
		");

		$this->execute("
			ALTER TABLE `doctor_4_clinic`
				DROP FOREIGN KEY `fk_clinic`
		");

		$this->execute("
			ALTER TABLE `clinic`
				CHANGE COLUMN `api_url` `api` VARCHAR(20) NULL DEFAULT NULL COMMENT 'Имя API-интерфейса для клиники' ,
				DROP COLUMN `api_login`,
				DROP COLUMN `api_password`
		");

		$this->execute("
			ALTER TABLE `clinic`
				DROP INDEX `external_id_idx`
		");

		$this->execute("
			ALTER TABLE `doctor_4_clinic`
				DROP INDEX `external_id_idx`
		");

		$this->execute("
			ALTER TABLE `clinic`
				DROP INDEX `name_idx`
		");

		$this->execute("
			ALTER TABLE `doctor`
				DROP INDEX `name_idx`
		");
	}
}