<?php

/**
 * m141002_105752_DD_60_add_index
 * человеческие название тарифов
 */
class m141002_105752_DD_60_add_index extends CDbMigration
{
	/**
	 * @return bool|void
	 */
	public function up()
	{
		$this->execute("ALTER TABLE `diagnostica`
			ADD INDEX `rewrite_name_idx` (`rewrite_name` ASC);
		");

		$this->execute("ALTER TABLE `SMSQuery`
			ADD INDEX `status_idx` (`status` ASC);
		");
	}

	/**
	 * @return bool|void
	 */
	public function down()
	{
		$this->dropIndex('rewrite_name_idx', 'diagnostica');
		$this->dropIndex('status_idx', 'SMSQuery');
	}
}