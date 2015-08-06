<?php

/**
 * - Удаление старой таблицы sip_channel (сейчас используется таблица sip_channels)
 * - Добавление id-заявки для входящего звонка
 */
class m141016_084249_DD_374_sip_channels extends CDbMigration
{
	/**
	 * Применяет миграцию
	 *
	 * @return void
	 */
	public function up()
	{
		$this->execute('DROP TABLE IF EXISTS sip_channel');

		$this->addColumn('sip_channels', 'request_id', 'int unsigned NULL');
		$this->addColumn('sip_channels', 'active', 'tinyint(1) unsigned NOT NULL DEFAULT "0"');
	}

	/**
	 * Откатывает миграцию
	 *
	 * @return void
	 */
	public function down()
	{
		$this->dropColumn('sip_channels', 'request_id');
		$this->dropColumn('sip_channels', 'active');
	}
}
