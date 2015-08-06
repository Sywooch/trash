<?php

/**
 * Добавление столбцов в таблицу диагностики для клиник
 */
class m150311_090722_DD_1029_clinic_diagnostics extends CDbMigration
{
	public function up()
	{
		$this->dropPrimaryKey('d4c_pk', 'diagnostica4clinic');
		$this->createIndex('d4c_unique_idx', 'diagnostica4clinic', 'diagnostica_id, clinic_id');

		$this->addColumn('diagnostica4clinic', 'id', 'int NOT NULL AUTO_INCREMENT primary key');
	}

	public function down()
	{
		$this->dropColumn('diagnostica4clinic', 'id');
		$this->dropIndex('d4c_unique_idx', 'diagnostica4clinic');

		$this->addPrimaryKey('d4c_pk', 'diagnostica4clinic', 'diagnostica_id, clinic_id');
	}
}
