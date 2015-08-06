<?php

/**
 * Class m150211_090219_DD_894_add_send_sms_to_clinic
 */
class m150211_090219_DD_894_add_send_sms_to_clinic extends CDbMigration
{
	/**
	 * Признак - отправлять смс в клинику для партнера
	 *
	 * @return bool|void
	 */
	public function up()
	{
		$this->addColumn('partner', 'send_sms_to_clinic', 'tinyint(1) DEFAULT "1" AFTER send_sms');
	}

	/**
	 * @return bool|void
	 */
	public function down()
	{
		$this->dropColumn('partner', 'send_sms_to_clinic');
	}
}