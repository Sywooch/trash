<?php

/**
 * Class m150205_150039_DD_880_return_billing_status
 */
class m150205_150039_DD_880_return_billing_status extends CDbMigration
{
	/**
	 * Вернул статус в биллинге
	 *
	 * @return bool|void
	 */
	public function up()
	{
		// Выставляем заявкам, у которых причина отказа - повторный отказ, статус в биллинге, завершена и снимаем причину отказа
		$this->execute(
			"UPDATE request AS r
				INNER JOIN request_history h ON h.request_id=r.req_id
				SET r.billing_status=1, r.req_status=3, r.reject_reason=0
				WHERE r.reject_reason=42 AND h.text LIKE '%Завершена%'
					AND r.req_created > unix_timestamp('2015-02-01')"
		);
		// Выставляем заявкам, у которых причина отказа - повторный отказ, статус в биллинге, обработана и снимаем причину отказа
		$this->execute(
			"UPDATE request AS r
				INNER JOIN request_history h ON h.request_id=r.req_id
				SET r.billing_status=1, r.req_status=2, r.reject_reason=0
				WHERE r.reject_reason=42 AND h.text LIKE '%Обработана%'
					AND r.req_created > unix_timestamp('2015-02-01')"
		);
	}

}