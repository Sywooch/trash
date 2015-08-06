<?php

/**
 * Class m141218_122416_DD_694_drop_srbac_tables
 */
class m141218_122416_DD_694_drop_srbac_tables extends CDbMigration
{
	/**
	 * Удалаяем неиспользуемые таблицы
	 *
	 * @return bool|void
	 */
	public function up()
	{
		$this->execute('DROP TABLE IF EXISTS srbac_assignments');
		$this->execute('DROP TABLE IF EXISTS srbac_itemchildren');
		$this->execute('DROP TABLE IF EXISTS srbac_items');
	}
}