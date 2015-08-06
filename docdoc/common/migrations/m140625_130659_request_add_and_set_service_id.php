<?php

class m140625_130659_request_add_and_set_service_id extends CDbMigration
{
	/**
	 * Колонка с ценой партнера, индекс по партнеру, цены для старых заявок
	 *
	 * @return bool|void
	 */
	public function up()
	{
		$this->execute('update request set date_admission = null where date_admission = 0');

		$this->execute('CREATE INDEX partner_id_index ON request ( partner_id );');
		$this->execute('ALTER TABLE request ADD partner_cost decimal (10,6) DEFAULT 0 NOT NULL;');
		$this->execute(
			'update request set partner_cost = 250
				where req_status != 4
					and date_admission is not null and date_admission != 0
					and partner_id != 0
					and partner_id is not null
					and kind=0;' //доктор
		);

		$this->execute(
			'update request set partner_cost = 50
				where req_status != 4
					and date_admission is not null and date_admission != 0
					and partner_id != 0
					and partner_id is not null
					and kind=1;' //диагностика
	);
	}

	/**
	 * Откат
	 */
	public function down()
	{
		$this->execute('ALTER TABLE request drop column partner_cost;');
		$this->execute('DROP INDEX partner_id_index ON request');
	}
}
