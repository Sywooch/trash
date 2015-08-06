<?php

/**
 * Добавляет поле created для lf_work
 *
 * Class m140829_083247_lf_work_created
 */
class m140829_083247_lf_work_created extends CDbMigration
{
	public function up()
	{
		$this->addColumn('lf_work', 'created', 'DATETIME NOT NULL');
	}

	public function down()
	{
		$this->dropColumn('lf_work', 'created');
	}
}