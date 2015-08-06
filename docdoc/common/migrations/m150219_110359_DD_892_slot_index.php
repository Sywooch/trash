<?php

/**
 * Добавление индексов в таблицу слотов
 */
class m150219_110359_DD_892_slot_index extends CDbMigration
{
	public function up()
	{
		$this->createIndex('slot_time_interval_idx', 'slot', 'start_time, finish_time');
		$this->createIndex('slot_external_id_idx', 'slot', 'external_id');
	}

	public function down()
	{
		$this->dropIndex('slot_time_interval_idx', 'slot');
		$this->dropIndex('slot_external_id_idx', 'slot');
	}
}
