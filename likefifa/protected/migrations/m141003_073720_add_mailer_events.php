<?php

/**
 * Создает таблицу для рассылок
 */
class m141003_073720_add_mailer_events extends CDbMigration
{
	public function up()
	{
		$this->createTable(
			'mailer_history',
			[
				'id' => 'pk',
				'master_id' => 'INT(11) NULL DEFAULT NULL',
				'salon_id' => 'INT(11) NULL DEFAULT NULL',
				'type' => 'TINYINT(1) NOT NULL',
				'created' => 'DATETIME NOT NULL',
			]
		);
		$this->addForeignKey(
			'mailer_history_master',
			'mailer_history',
			'master_id',
			'lf_master',
			'id',
			'cascade',
			'cascade'
		);
		$this->addForeignKey(
			'mailer_history_salon',
			'mailer_history',
			'salon_id',
			'lf_salons',
			'id',
			'cascade',
			'cascade'
		);
	}

	public function down()
	{
		$this->dropTable('mailer_history');
	}
}