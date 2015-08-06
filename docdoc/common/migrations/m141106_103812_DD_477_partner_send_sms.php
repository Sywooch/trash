<?php

/**
 * Добавление признака отправки смс для партнера
 */
class m141106_103812_DD_477_partner_send_sms extends CDbMigration
{
	/**
	 * @return bool|void
	 */
	public function up()
	{
		$this->addColumn('partner', 'send_sms', 'tinyint(1) DEFAULT "0"');
	}

	/**
	 * @return bool|void
	 */
	public function down()
	{
		$this->dropColumn('partner', 'send_sms');
	}
}
