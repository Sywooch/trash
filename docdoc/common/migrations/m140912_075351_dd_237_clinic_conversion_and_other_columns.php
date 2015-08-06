<?php

/**
 * Class m140912_075351_dd_237_clinic_conversion_and_other_columns
 */
class m140912_075351_dd_237_clinic_conversion_and_other_columns extends CDbMigration
{
	/**
	 * @return bool|void
	 */
	public function safeUp()
	{
		$this->execute("ALTER TABLE clinic ADD conversion decimal (6,3) DEFAULT 0 NOT NULL;");
		$this->execute("ALTER TABLE clinic ADD hand_factor real DEFAULT 0 NOT NULL;");
		$this->execute("ALTER TABLE clinic ADD admission_cost decimal (8,5) DEFAULT 0 NOT NULL;");
	}

	/**
	 * @return bool|void
	 */
	public function safeDown()
	{
		$this->execute("ALTER TABLE clinic DROP conversion;");
		$this->execute("ALTER TABLE clinic DROP hand_factor;");
		$this->execute("ALTER TABLE clinic DROP admission_cost;");
	}
}
