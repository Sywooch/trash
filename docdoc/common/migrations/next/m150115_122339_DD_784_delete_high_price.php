<?php

/**
 * Class m150115_122339_DD_784_high_price_for_mrt
 */
class m150115_122339_DD_784_high_price_for_mrt extends CDbMigration
{
	/**
	 * Удаляем неиспользуемый признак is_high_prices
	 *
	 * @return bool|void
	 */
	public function up()
	{
		$this->dropColumn('diagnostica', 'is_high_prices');
	}

}