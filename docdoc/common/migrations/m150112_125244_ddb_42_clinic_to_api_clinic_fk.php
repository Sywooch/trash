<?php

class m150112_125244_ddb_42_clinic_to_api_clinic_fk extends CDbMigration
{
	public function safeUp()
	{
		$this->execute('DROP INDEX external_id_idx ON clinic;');
		$this->execute(
			'ALTER TABLE clinic ADD CONSTRAINT `fk_api_clinic_id`
				FOREIGN KEY (external_id) REFERENCES api_clinic (id) ON UPDATE CASCADE ON DELETE SET NULL; '
		);
	}

	public function down()
	{
		$this->execute("ALTER TABLE `clinic` DROP FOREIGN KEY `fk_api_clinic_id`");
		$this->execute("ALTER TABLE `clinic` ADD INDEX `external_id_idx` (`external_id` ASC)");
	}
}
