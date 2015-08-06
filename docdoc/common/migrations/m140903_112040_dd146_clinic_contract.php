<?php

use dfs\docdoc\models\ContractModel;
use \dfs\docdoc\models\RequestModel;

/**
 * Файл класса m140903_112040_dd146_clinic_contract.
 */
class m140903_112040_dd146_clinic_contract extends CDbMigration
{
	/**
	 * Применяет миграцию
	 *
	 * @return void
	 */
	public function up()
	{
		$this->createIndex('billing_status_key', 'request', 'billing_status');
		$this->createIndex('clinic_id_key', 'request', 'clinic_id');

		$this->delete('clinic_contract');

		$this->execute('
			INSERT INTO clinic_contract (clinic_id, contract_id)
				SELECT c.id, cs.contract_id
				FROM clinic c
					INNER JOIN clinic_settings cs ON (cs.settings_id = c.settings_id AND contract_id != 0)
				WHERE c.parent_clinic_id = 0
		');

		$this->execute('
			INSERT INTO clinic_contract (clinic_id, contract_id)
				SELECT c.id, ds.contract_id
				FROM clinic c
					INNER JOIN diagnostica_settings ds ON (ds.settings_id = c.diag_settings_id AND contract_id != 0)
				WHERE c.parent_clinic_id = 0
		');

		$this->update('clinic_contract', [ 'contract_id' => ContractModel::TYPE_DOCTOR_RECORD ], 'contract_id = ' . ContractModel::TYPE_DOCTOR_CALL);

		// Проставлять BILLING_STATUS_REFUSE для отклоненных ранее клиниками заявок
		$this->update('request', [
				'billing_status' => RequestModel::BILLING_STATUS_REFUSED,
				'request_cost' => null,
				'partner_cost' => null,
			], 'lk_status IN (3, 6)');
	}

	/**
	 * Откатывает миграцию
	 *
	 * @return void
	 */
	public function down()
	{
		$this->dropIndex('billing_status_key', 'request');
		$this->dropIndex('clinic_id_key', 'request');
	}
}