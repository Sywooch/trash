<?php

class m150327_094608_dd_1006_clinic_to_phone_add_fk extends CDbMigration
{
	public function safeUp()
	{
		//вставляю в спарвочник телефоны которых нет
		$this->execute(
			"INSERT IGNORE INTO phone (number, model_name) SELECT t.asterisk_phone, 'clinic' FROM (
				SELECT c.id,c.name,c.status, c.asterisk_phone, p.number FROM clinic c
				LEFT JOIN phone p ON p.number  = c.asterisk_phone
				WHERE p.id IS NULL AND c.asterisk_phone IS NOT NULL) t; "
		);

		//убираю пустые строки
		$this->execute(
			"UPDATE clinic SET asterisk_phone= NULL WHERE asterisk_phone = ''; "
		);

		$this->execute(
			"ALTER TABLE clinic
				ADD CONSTRAINT asterisk_phone_fk FOREIGN KEY (asterisk_phone) REFERENCES phone (number)
				ON UPDATE CASCADE;"
		);
	}

	public function down()
	{
		$this->dropForeignKey('asterisk_phone_fk', 'clinic');
	}
}