<?php

/**
 * Class m141024_145337_dd_359_drop_booking_slot_constrait
 */
class m141024_145337_dd_359_drop_booking_slot_constrait extends CDbMigration
{
	/**
	 * @return bool|void
	 */
	public function safeUp()
	{
		$this->execute('ALTER TABLE booking DROP FOREIGN KEY booking_slot_fk;');
		$this->execute("ALTER TABLE booking ADD start_time TIMESTAMP DEFAULT NULL NULL;");
		$this->execute("ALTER TABLE booking ADD finish_time TIMESTAMP DEFAULT NULL NULL;");
		$this->execute("ALTER TABLE booking CHANGE slot_id slot_id VARCHAR(255);");
		$this->execute("ALTER TABLE booking MODIFY COLUMN slot_id VARCHAR(255) NULL;");
		$this->execute("ALTER TABLE booking MODIFY slot_id VARCHAR(255) DEFAULT null;");

	}

	/**
	 * @return bool|void
	 */
	public function safeDown()
	{
		$this->execute('ALTER TABLE booking ADD CONSTRAINT `booking_slot_fk` FOREIGN KEY (`slot_id`) REFERENCES `slot` (`id`)');
		$this->execute("ALTER TABLE booking DROP COLUMN start_time;");
		$this->execute("ALTER TABLE booking DROP COLUMN finish_time;");

		$this->execute("ALTER TABLE booking CHANGE slot_id slot_id BIGINT;");
		$this->execute("ALTER TABLE booking MODIFY COLUMN slot_id BIGINT NULL;");
		$this->execute("ALTER TABLE booking MODIFY slot_id BIGINT DEFAULT null;");
	}
}
