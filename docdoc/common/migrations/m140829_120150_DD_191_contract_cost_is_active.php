<?php

/**
 * Class m140829_120150_DD_191_contract_cost_is_active
 */
class m140829_120150_DD_191_contract_cost_is_active extends CDbMigration
{
	/**
	 * Добавляем поле is_active
	 *
	 * @return bool|void
	 */
	public function up()
	{
		$this->addColumn("clinic_contract_cost", "is_active", "TINYINT(1) NOT NULL DEFAULT 1");
		$this->createIndex("is_active", "clinic_contract_cost", "is_active");
	}

	/**
	 * Откатывает миграцию
	 *
	 * @return void
	 */
	public function down()
	{
		$this->dropColumn("clinic_contract_cost", "is_active");
	}
}