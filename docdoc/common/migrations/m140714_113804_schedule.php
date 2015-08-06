<?php

/**
 * Файл класса m140714_113804_schedule
 *
 * Удаление станции Краснопресненская из района Зябликово
 */
class m140714_113804_schedule extends CDbMigration
{
	/**
	 * Применяет миграцию
	 *
	 * @return void
	 */
	public function up()
	{
		//добавляем столбец с временем последней загрузки дянных
		$this->execute(
			"ALTER TABLE `doctor_4_clinic`
				ADD COLUMN `last_slots_update` timestamp NULL COMMENT 'время последней загрузки слотов'
		");
		$this->createIndex("last_slots_update_idx", "doctor_4_clinic", "last_slots_update");

		//удаляем неиспользуемые столбцы
		$this->dropColumn("clinic", "api_url");
		$this->dropColumn("clinic", "api_login");
		$this->dropColumn("clinic", "api_password");

		$this->execute(
			"CREATE TABLE `api_clinic` (
				`id` VARCHAR(50) NOT NULL,
				`name` VARCHAR(45) NOT NULL,
				`phone` CHAR(11) NULL,
				`city` VARCHAR(20) NULL,
				`is_merged` TINYINT(1) NULL DEFAULT 0,
				PRIMARY KEY (`id`),
				INDEX `is_merged_idx` (`is_merged` ASC)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Информация о клиниках, получаемая из интеграционного шлюза'"
		);

		$this->execute(
			"CREATE TABLE `api_doctor` (
				`id` INT(10) NOT NULL AUTO_INCREMENT,
				`api_doctor_id` VARCHAR(50) NOT NULL,
				`name` VARCHAR(255) NULL,
				`is_merged` TINYINT(1) NULL DEFAULT 0,
				`api_clinic_id` VARCHAR(50) NULL,
				PRIMARY KEY (`id`),
				INDEX `doctor_in_clinic` (`api_clinic_id` ASC, `api_doctor_id` ASC),
				INDEX `api_clinic_id_fk` (`api_clinic_id` ASC),
				INDEX `is_merged_idx` (`is_merged` ASC),
				CONSTRAINT `api_clinic_id_fk` FOREIGN KEY (`api_clinic_id`) REFERENCES `api_clinic` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Информация о крачах, получаемая из интеграционного шлюза'"
		);


		//интерфейса для сопоставления клиник и врачей пока нет, поэтому руками проставляем ID клиник
		$this->execute("UPDATE clinic SET external_id='onclinic_1' WHERE id=13 LIMIT 1");
		$this->execute("UPDATE clinic SET external_id='onclinic_2' WHERE id=230 LIMIT 1");
		$this->execute("UPDATE clinic SET external_id='onclinic_4' WHERE id=231 LIMIT 1");
		$this->execute("UPDATE clinic SET external_id='onclinic_5' WHERE id=232 LIMIT 1");
		$this->execute("UPDATE clinic SET external_id='onclinic_7' WHERE id=233 LIMIT 1");
		$this->execute("UPDATE clinic SET external_id='wikimed_1' WHERE id=86 LIMIT 1");

	}

	/**
	 * Откатывает миграцию
	 *
	 * @return void
	 */
	public function down()
	{
		$this->execute(
			"ALTER TABLE `clinic`
				ADD COLUMN `api_url` VARCHAR(255) NULL DEFAULT NULL COMMENT 'URL API-интерфейса для клиники' ,
				ADD COLUMN `api_login` VARCHAR(50) NULL DEFAULT NULL COMMENT 'логин для авторизации в МИС',
				ADD COLUMN `api_password` VARCHAR(50) NULL DEFAULT NULL COMMENT 'пароль для авторизации в МИС'
		");

		$this->dropColumn("doctor_4_clinic", "last_slots_update");

		$this->dropTable('api_doctor');
		$this->dropTable('api_clinic');

		//интерфейса для сопоставления клиник и врачей пока нет, поэтому руками проставляем ID клиник
		$this->execute("UPDATE clinic SET external_id = null WHERE id IN (13, 230, 231, 232, 233)");

	}
}