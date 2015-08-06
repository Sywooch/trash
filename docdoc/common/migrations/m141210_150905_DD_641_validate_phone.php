<?php

/**
 * Устанавливаем валидацию телефона для клиник по-умолчанию
 */
class m141210_150905_DD_641_validate_phone extends CDbMigration
{
	public function up()
	{
		$this->alterColumn('clinic', 'validate_phone', 'tinyint NOT NULL DEFAULT 1');
		$this->update('clinic', [ 'validate_phone' => 1 ]);
	}

	public function down()
	{
		$this->update('clinic', [ 'validate_phone' => 0 ]);
	}
}
