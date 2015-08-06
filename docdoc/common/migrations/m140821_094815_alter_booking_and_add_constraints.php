<?php

/**
 * Class m140821_094815_alter_booking_and_add_constraints
 */
class m140821_094815_alter_booking_and_add_constraints extends CDbMigration
{
	/**
	 * Применяет
	 *
	 * @return bool|void
	 */
	public function up()
	{
		$this->execute("ALTER TABLE booking MODIFY COLUMN date_created TIMESTAMP NOT NULL;");
		$this->execute("ALTER TABLE booking ADD CONSTRAINT booking_request_fk FOREIGN KEY ( request_id ) REFERENCES request ( req_id );");
		$this->execute("ALTER TABLE booking ADD CONSTRAINT booking_slot_fk FOREIGN KEY ( slot_id ) REFERENCES slot ( id );");
	}

	/**
	 * Откатывает
	 *
	 * @return bool|void
	 */
	public function down()
	{
		$this->execute("ALTER TABLE booking MODIFY COLUMN date_created TIMESTAMP NULL;");
		$this->execute("ALTER TABLE booking DROP FOREIGN KEY booking_slot_fk;");
		$this->execute("ALTER TABLE booking DROP FOREIGN KEY booking_request_fk;");
	}
}
