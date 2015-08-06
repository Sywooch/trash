<?php

/**
 * Class m141003_121122_dd_300_request_add_validation_code
 */
class m141003_121122_dd_300_request_add_validation_code extends CDbMigration
{
	/**
	 * @return bool|void
	 */
	public function safeUp()
	{
		$this->execute('ALTER TABLE clinic ADD validate_phone TINYINT(1) DEFAULT FALSE NOT NULL;');
		$this->execute('ALTER TABLE request ADD validation_code VARCHAR(6) NULL;');
	}

	/**
	 * @return bool|void
	 */
	public function safeDown()
	{
		$this->execute('ALTER TABLE clinic drop COLUMN validate_phone');
		$this->execute('ALTER TABLE request drop COLUMN validation_code');
	}
}
