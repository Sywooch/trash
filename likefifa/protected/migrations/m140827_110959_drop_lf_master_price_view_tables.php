<?php

/**
 * Удаляет таблицы lf_master_price_view
 *
 * Class m140827_110959_drop_lf_master_price_view_tables
 */
class m140827_110959_drop_lf_master_price_view_tables extends CDbMigration
{
	public function up()
	{
		$tables = Yii::app()->db->createCommand('SHOW TABLES')->queryColumn();
		foreach ($tables as $table) {
			if(strstr($table, 'lf_master_price_view_')) {
				$this->execute("DROP VIEW " . $table);
			}
		}
	}

	public function down()
	{
		echo "m140827_110959_drop_lf_master_price_view_tables does not support migration down.\n";
		return false;
	}
}