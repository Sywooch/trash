<?php

/**
 * Удаляет колонку not_main из работ
 *
 * Class m140918_113831_remove_lf_work_not_main
 */
class m140918_113831_remove_lf_work_not_main extends CDbMigration
{
	public function up()
	{
		$this->dropColumn(LfWork::model()->tableName(), 'not_main');
	}

	public function down()
	{
		$this->addColumn("lf_work", "not_main", "INT NOT NULL");
		$this->createIndex("lf_work_not_main", "lf_work", "not_main");
	}
}