<?php

/**
 * Добавление для партнера поля с параметрами
 */
class m141212_080150_DD_653_partner_prices extends CDbMigration
{
	public function up()
	{
		$this->addColumn('partner', 'json_params', 'TEXT NULL');
	}

	public function down()
	{
		$this->dropColumn('partner', 'json_params');
	}
}
