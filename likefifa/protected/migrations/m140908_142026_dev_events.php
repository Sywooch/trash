<?php

/**
 * Создает таблицу для хранения событий (график на главной БО)
 *
 * Class m140908_142026_dev_events
 */
class m140908_142026_dev_events extends CDbMigration
{
	public function up()
	{
		$this->createTable(
			'dev_events',
			[
				'id'    => 'pk',
				'value' => 'VARCHAR(50) NOT NULL',
				'date'  => 'DATE NOT NULL',
			]
		);

		$this->insert(
			'admin_controller',
			[
				'name'         => 'События',
				'rewrite_name' => 'devEvents',
				'col_group'    => 'Контент',
				'icon'         => 'calendar',

			]
		);
	}

	public function down()
	{
		$this->dropTable('dev_events');
		$this->delete('admin_controller', 'name = "События" AND rewrite_name = "devEvents"');
	}
}