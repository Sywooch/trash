<?php

class m141013_103329_dd_358_doctor_4_clinic_foreign_key_to_api_doctor extends CDbMigration
{
	/**
	 * @return bool|void
	 */
	public function up()
	{
		//устанавливаю внешний ключ на данные из апи
		$this->execute("ALTER TABLE doctor_4_clinic ADD CONSTRAINT doctor_4_clinic_external_fk FOREIGN KEY (doc_external_id) REFERENCES api_doctor (id) ON DELETE SET NULL");
	}

	/**
	 * @return bool|void
	 */
	public function down()
	{
		//удаляю внешний ключ
		$this->execute("ALTER TABLE doctor_4_clinic DROP FOREIGN KEY doctor_4_clinic_external_fk");
	}
}
