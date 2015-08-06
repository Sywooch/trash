<?php

class m140625_141848_diagnostics_add_column_is_high_prices extends CDbMigration
{
	/**
	 * Добавил колонку для диагностики, показывающую высокая или низкая цена
	 * Проставил мрт и кт высокие цены
	 */
	public function up()
	{
		$this->execute('ALTER TABLE diagnostica ADD is_high_prices boolean DEFAULT false NOT NULL;');
		//kt
		$this->execute('update diagnostica set is_high_prices = 1 where id=19 or parent_id=19');
		//mrt
		$this->execute('update diagnostica set is_high_prices = 1 where id=21 or parent_id=21');
	}

	/**
	 * Убил колонку
	 */
	public function down()
	{
		$this->execute('ALTER TABLE diagnostica drop column is_high_prices;');
	}
}
