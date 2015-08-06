<?php

/**
 * Файл класса m140819_134941_dd147_moderation.
 */
class m140819_134941_dd147_moderation extends CDbMigration
{
	/**
	 * Применяет миграцию
	 *
	 * @return void
	 */
	public function up()
	{
		$this->createTable(
			'moderation',
			[
				'id' => 'pk',
				'entity_class' => 'varchar(50) NOT NULL',
				'entity_id' => 'bigint(20) NOT NULL',
				'data' => 'text',
			],
			'ENGINE=InnoDB DEFAULT CHARSET=utf8'
		);
		$this->createIndex('entity_key', 'moderation', 'entity_class, entity_id', true);
	}

	/**
	 * Откатывает миграцию
	 *
	 * @return void
	 */
	public function down()
	{
		$this->dropTable('moderation');
	}
}