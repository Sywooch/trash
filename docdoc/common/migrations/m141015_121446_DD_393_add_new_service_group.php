<?php

/**
 * Class m141015_121446_DD_393_add_new_service_group
 *
 */
class m141015_121446_DD_393_add_new_service_group extends CDbMigration
{
	/**
	 * Добавляем новую группу - Флюрография
	 * Добавляем привязку к диагностике 116 Флюрография
	 * @return bool|void
	 */
	public function up()
	{
		$this->insert('contract_group', ['id' => 6, 'name' => 'Флюрография', 'kind' => 1]);
		$this->insert('contract_group_service', ['contract_group_id' => 6, 'service_id' => 116]);
	}

	/**
	 * @return bool|void
	 */
	public function down()
	{
		$this->delete('contract_group_service', 'contract_group_id = 6 and service_id = 116');
		$this->delete('contract_group', 'id = 6');
	}
}