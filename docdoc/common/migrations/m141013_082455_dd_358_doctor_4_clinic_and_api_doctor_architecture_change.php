<?php

class m141013_082455_dd_358_doctor_4_clinic_and_api_doctor_architecture_change extends CDbMigration
{
	/**
	 * @return bool|void
	 */
	public function safeUp()
	{
		//избавляюсь от пустых строк чтобы воткнуть внешний ключ
		$this->execute("UPDATE doctor_4_clinic SET doc_external_id = NULL WHERE doc_external_id='';");

		//заменяю ид доктора на первичный ключ из доктор_4_клиник_из_апи на наш доктор_4_клиник
		$this->execute("UPDATE doctor_4_clinic
							SET doc_external_id = (SELECT id
												   FROM api_doctor
												   WHERE api_doctor_id = doc_external_id and api_clinic_id = (select external_id from clinic where id=doctor_4_clinic.clinic_id))
							WHERE doc_external_id IS NOT NULL");


		//грохаю не уникальный индекс
		$this->execute("DROP INDEX external_id_idx ON doctor_4_clinic;");

		//колонка должна быть числом для внешнего ключа
		$this->execute("ALTER TABLE doctor_4_clinic MODIFY doc_external_id INTEGER;");

		//создаю уникальный индекс для "один к одному"
		$this->execute("CREATE UNIQUE INDEX external_id_idx ON doctor_4_clinic (doc_external_id);");

		//меняю обычный индекс на уникальный для докто№клиника из апи
		$this->execute('DROP INDEX doctor_in_clinic ON api_doctor;');
		$this->execute('CREATE UNIQUE INDEX `doctor_in_clinic` ON api_doctor (`api_clinic_id`,`api_doctor_id`)');
	}

	/**
	 * @return bool|void
	 */
	public function safeDown()
	{
		//удаляю уникальный, создаю обычный индекс
		$this->execute("DROP INDEX external_id_idx ON doctor_4_clinic;");
		$this->execute("ALTER TABLE doctor_4_clinic MODIFY doc_external_id VARCHAR(55);");
		$this->execute("CREATE INDEX external_id_idx ON doctor_4_clinic (doc_external_id);");

		//заменяю ид доктора на первичный ключ из доктор_4_клиник_из_апи на наш доктор_4_клиник
		$this->execute("UPDATE doctor_4_clinic
							SET doc_external_id = (SELECT api_doctor_id
												   FROM api_doctor
												   WHERE id = doc_external_id)
							WHERE doc_external_id IS NOT NULL");

		//меняю уникальный на обычный индекс
		$this->execute('DROP INDEX doctor_in_clinic ON api_doctor;');
		$this->execute('CREATE INDEX `doctor_in_clinic` ON api_doctor (`api_clinic_id`,`api_doctor_id`)');
	}
}
