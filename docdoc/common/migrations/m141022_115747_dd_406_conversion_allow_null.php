<?php

class m141022_115747_dd_406_conversion_allow_null extends CDbMigration
{
	/**
	 * @return bool|void
	 */
	public function safeUp()
	{
		$this->execute("ALTER TABLE doctor MODIFY conversion DECIMAL(6,3) NULL DEFAULT NULL;");
		$this->execute("ALTER TABLE clinic MODIFY conversion DECIMAL(6,3) NULL DEFAULT NULL;");
		$this->execute("UPDATE doctor SET conversion=NULL WHERE conversion=0;");
		$this->execute("UPDATE clinic SET conversion=NULL WHERE conversion=0;");
	}

	/**
	 * @return bool|void
	 */
	public function safeDown()
	{
		$this->execute("UPDATE doctor SET conversion=0 WHERE conversion IS NULL;");
		$this->execute("UPDATE clinic SET conversion=0 WHERE conversion IS NULL;");
		$this->execute("ALTER TABLE doctor MODIFY conversion DECIMAL(6,3) NOT NULL DEFAULT 0;");
		$this->execute("ALTER TABLE clinic MODIFY conversion DECIMAL(6,3) NOT NULL DEFAULT 0;");
	}
}
