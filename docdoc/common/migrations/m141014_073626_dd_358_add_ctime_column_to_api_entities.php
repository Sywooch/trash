<?php

class m141014_073626_dd_358_add_ctime_column_to_api_entities extends CDbMigration
{
	/**
	 * @return bool|void
	 */
	public function safeUp()
	{
		$sql = "
			ALTER TABLE api_doctor ADD COLUMN ctime TIMESTAMP NOT NULL DEFAULT now();
			ALTER TABLE api_clinic ADD COLUMN ctime TIMESTAMP NOT NULL DEFAULT now();
			ALTER TABLE slot ADD COLUMN ctime TIMESTAMP NOT NULL DEFAULT now();
		";

		$this->execute($sql);
	}

	/**
	 * @return bool|void
	 */
	public function safeDown()
	{
		$sql = "
			ALTER TABLE api_doctor DROP COLUMN ctime;
			ALTER TABLE api_clinic DROP COLUMN ctime;
			ALTER TABLE slot DROP COLUMN ctime;
		";

		$this->execute($sql);
	}
}
