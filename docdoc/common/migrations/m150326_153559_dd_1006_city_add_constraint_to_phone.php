<?php

class m150326_153559_dd_1006_city_add_constraint_to_phone extends CDbMigration
{
	public function safeUp()
	{
		$this->execute(
			"ALTER TABLE city
				ADD CONSTRAINT site_phone_fk FOREIGN KEY (site_phone) REFERENCES phone (number)
				ON UPDATE CASCADE;"
		);

		$this->execute('UPDATE city SET opinion_phone = NULL WHERE id_city=3;'); //там пустая строка и не даст повесить fk

		$this->execute(
			"ALTER TABLE city
				ADD CONSTRAINT opinion_phone_fk FOREIGN KEY (opinion_phone) REFERENCES phone (number)
				ON UPDATE CASCADE;"
		);
	}

	public function safeDown()
	{
		$this->execute("ALTER TABLE city DROP FOREIGN KEY site_phone_fk;");
		$this->execute("ALTER TABLE city DROP FOREIGN KEY opinion_phone_fk;");
	}
}