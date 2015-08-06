<?php

/**
 * Class m141218_122305_DD_694_change_table_engine
 */
class m141218_122305_DD_694_change_table_engine extends CDbMigration
{
	/**
	 * Конвертируем таблицы MyIsam
	 *
	 * @return bool|void
	 */
	public function up()
	{
		$this->execute('ALTER TABLE article_section ENGINE = INNODB');
		$this->execute('ALTER TABLE clinic_address ENGINE = INNODB');
		$this->execute('ALTER TABLE clinic_settings ENGINE = INNODB');
		$this->execute('ALTER TABLE closest_station ENGINE = INNODB');
		$this->execute('ALTER TABLE log_sms ENGINE = INNODB');
		$this->execute('ALTER TABLE request_history ENGINE = INNODB');
		$this->execute('ALTER TABLE schedule_day_pool ENGINE = INNODB');
	}

	/**
	 * @return bool|void
	 */
	public function down()
	{
		$this->execute('ALTER TABLE article_section ENGINE = MYISAM');
		$this->execute('ALTER TABLE clinic_address ENGINE = MYISAM');
		$this->execute('ALTER TABLE clinic_settings ENGINE = MYISAM');
		$this->execute('ALTER TABLE closest_station ENGINE = MYISAM');
		$this->execute('ALTER TABLE log_sms ENGINE = MYISAM');
		$this->execute('ALTER TABLE request_history ENGINE = MYISAM');
		$this->execute('ALTER TABLE schedule_day_pool ENGINE = MYISAM');
	}
}