<?php

/**
 * Добавляет для работ поля для сохранения координат кропинга
 */
class m141008_112146_add_work_crop extends CDbMigration
{
	public function up()
	{
		$this->addColumn(LfWork::model()->tableName(), 'crop_coordinates', 'VARCHAR(255) NULL DEFAULT NULL');
	}

	public function down()
	{
		$this->dropColumn(LfWork::model()->tableName(), 'crop_coordinates');
	}
}