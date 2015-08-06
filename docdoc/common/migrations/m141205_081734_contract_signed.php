<?php

/**
 * Class m141205_081734_contract_signed
 */
class m141205_081734_contract_signed extends CDbMigration
{
	/**
	 * Добавляем признак того, что договор с клиникой подписан
	 * Обновляем признак для существующих клиник
	 *
	 * @return bool|void
	 */
	public function up()
	{
		$this->addColumn('clinic', 'contract_signed', 'TINYINT(1) DEFAULT 0 NOT NULL');

		$this->execute(
			"UPDATE clinic
				INNER JOIN clinic_contract t1 ON t1.clinic_id=clinic.id
				SET contract_signed=1
				WHERE clinic.status=3"
		);
	}

	/**
	 * @return bool
	 */
	public function down()
	{
		$this->dropColumn('clinic', 'contract_signed');
	}
}