<?php

class m141224_125620_dd_703_doctor_4_clinic_add_column_type extends CDbMigration
{
	public function safeUp()
	{
		//add column
		$this->execute('ALTER TABLE doctor_4_clinic ADD type TINYINT DEFAULT 1 NOT NULL;');

		//удаляю внешние ключи,без этого нельзя удалить индекс doctor_clinic_idx
		$this->execute('ALTER TABLE doctor_4_clinic DROP FOREIGN KEY fk_clinic;');
		$this->execute('ALTER TABLE doctor_4_clinic DROP FOREIGN KEY fk_doctor;');

		//удаляю индекс doctor_clinic_idx
		$this->execute('DROP INDEX doctor_clinic_idx ON doctor_4_clinic;');

		//удаляю с концами индекс,потому что он итак будет в fk_clinic
		$this->execute('DROP INDEX clinic_idx ON doctor_4_clinic;');

		//удалить дубли чтобы повесить уникальность
		$this->execute('DELETE FROM doctor_4_clinic
							WHERE id NOT IN (SELECT *
								 FROM (SELECT MIN(n.id)
										FROM doctor_4_clinic n
										GROUP BY n.doctor_id, n.clinic_id) x);');

		//новый индекс с колнокой "тип" + добавил уникальность
		$this->execute('CREATE UNIQUE INDEX doctor_id_clinic_id_type_index ON doctor_4_clinic (doctor_id, clinic_id, type);');

		//возвращяю внешние ключи на место
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
	}

	public function safeDown()
	{
		$this->execute('ALTER TABLE doctor_4_clinic DROP FOREIGN KEY fk_clinic;');
		$this->execute('ALTER TABLE doctor_4_clinic DROP FOREIGN KEY fk_doctor;');
		$this->execute('DROP INDEX doctor_id_clinic_id_type_index ON doctor_4_clinic;');
		$this->execute('ALTER TABLE doctor_4_clinic DROP COLUMN type;');

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

		$this->execute("ALTER TABLE `doctor_4_clinic` ADD INDEX `doctor_clinic_idx` (`doctor_id` ASC, `clinic_id` ASC)");
		$this->execute("ALTER TABLE `doctor_4_clinic` ADD INDEX `clinic_idx` (`clinic_id`)");
	}
}
