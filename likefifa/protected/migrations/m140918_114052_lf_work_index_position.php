<?php

/**
 * Добавляет колонку index_position для работ
 *
 * Class m140918_114052_lf_work_index_position
 */
class m140918_114052_lf_work_index_position extends CDbMigration
{
	public function up()
	{
		$this->addColumn(LfWork::model()->tableName(), 'index_position', "TINYINT(1) NULL DEFAULT NULL COMMENT 'Позиция работы на главной. До 10 - Москва, после 10 - МО'");
		$this->createIndex('lf_work_index_position', LfWork::model()->tableName(), 'index_position');
	}

	public function down()
	{
		$this->dropColumn(LfWork::model()->tableName(), 'index_position');
	}
}