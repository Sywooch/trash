<?php

/**
 * Class m150219_145043_DD_951_add_needs_to_recalc
 */
class m150219_145043_DD_951_add_needs_to_recalc extends CDbMigration
{
	/**
	 * Добавляем параметр для принудительного пересчета рейтинга для стратегии
	 *
	 * @return bool|void
	 */
	public function up()
	{
		$this->addColumn("rating_strategy", "needs_to_recalc", "TINYINT(1) DEFAULT '0'");

		$this->alterColumn("rating_strategy", "name", "VARCHAR(255) NOT NULL");
	}

	/**
	 * @return bool
	 */
	public function down()
	{
		$this->dropColumn("rating_strategy", "needs_to_recalc");

		$this->alterColumn("rating_strategy", "name", "VARCHAR(20) NOT NULL");
	}

}