<?php

/**
 * Class m141013_073159_api_clinic_add_column_enabled
 */
class m141013_073159_dd_358_api_clinic_and_api_doctor_add_column_enabled extends CDbMigration
{
	/**
	 * @return bool|void
	 */
	public function safeUp()
	{
		$this->execute('ALTER TABLE api_clinic ADD enabled TINYINT(1) DEFAULT TRUE NOT NULL;');
		$this->execute('CREATE INDEX enabled_index ON api_clinic (enabled);');

		$this->execute('ALTER TABLE api_doctor ADD enabled TINYINT(1) DEFAULT TRUE NOT NULL;');
		$this->execute('CREATE INDEX enabled_index ON api_doctor (enabled);');
	}

	/**
	 * @return bool|void
	 */
	public function safeDown()
	{
		$this->execute('ALTER TABLE api_clinic drop COLUMN enabled;');
		$this->execute('ALTER TABLE api_doctor drop COLUMN enabled;');
	}
}
