<?php

/**
 * Class m140912_075047_dd_237_doctor_conversion
 */
class m140912_075047_dd_237_doctor_conversion extends CDbMigration
{
	/**
	 * @return bool|void
	 */
	public function up()
	{
		$this->execute("ALTER TABLE doctor ADD conversion decimal (6,3) DEFAULT 0 NOT NULL;");
	}

	/**
	 * @return bool|void
	 */
	public function down()
	{
		$this->execute("ALTER TABLE doctor DROP conversion;");
	}
}
