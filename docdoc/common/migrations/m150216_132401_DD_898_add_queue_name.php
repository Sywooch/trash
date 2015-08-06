<?php

/**
 * Class m150216_132401_DD_898_add_queue_name
 */
class m150216_132401_DD_898_add_queue_name extends CDbMigration
{
	/**
	 * Добавляем поле - название очереди
	 *
	 * @return bool|void
	 */
	public function up()
	{
		$this->addColumn('request', 'queue', 'VARCHAR(15) DEFAULT NULL');
	}

	/**
	 * @return bool|void
	 */
	public function down()
	{
		$this->dropColumn('request', 'queue');
	}

}