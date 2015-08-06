<?php

use \dfs\docdoc\models\RequestModel;
use dfs\docdoc\objects\Rejection;


/**
 * Class m140926_062513_DD_276_request_partner_status
 */
class m140926_062513_DD_276_request_partner_status extends CDbMigration
{
	/**
	 * Применяет миграцию
	 *
	 * @return bool|void
	 */
	public function up()
	{
		// мега-костыль! без него не хочет добавляться еще одно поле в таблицу
		$this->dropIndex('kind', 'request');
		$this->dropIndex('type', 'request');
		$this->dropIndex('client_phone', 'request');
		$this->dropIndex('add_client_phone_index', 'request');

		$this->addColumn('request', 'partner_status', 'tinyint NOT NULL DEFAULT 0');
		$this->createIndex('partner_status_index', 'request', 'partner_status');

		// востанавливаем удаленные индексы
		$this->createIndex('kind', 'request', 'kind');
		$this->createIndex('type', 'request', 'req_type');
		$this->createIndex('client_phone', 'request', 'client_phone');
		$this->createIndex('add_client_phone_index', 'request', 'add_client_phone');

		// Ставим заявкам статус "Подтверждено"
		$this->update('request', [ 'partner_status' => RequestModel::PARTNER_STATUS_ACCEPT ], 'date_admission IS NOT NULL AND date_admission > 0');

		// Ставим заявкам статус "Отклонено"
		$rejectReasons = [ Rejection::REASON_SPAM, Rejection::REASON_TEST ];
		$rejectConditions = '(req_status = ' . RequestModel::STATUS_REJECT . ' AND reject_reason IN (' . implode(', ', $rejectReasons) . '))';
		$rejectConditions .= ' OR (req_status = ' . RequestModel::STATUS_REMOVED . ')';
		$this->update('request', [ 'partner_status' => RequestModel::PARTNER_STATUS_REJECT ], $rejectConditions);
	}

	/**
	 * Откат миграции
	 *
	 * @return bool|void
	 */
	public function down()
	{
		// мега-костыль! поле еще и удаляться не хочет без этого
		$this->dropIndex('kind', 'request');
		$this->dropIndex('type', 'request');
		$this->dropIndex('client_phone', 'request');
		$this->dropIndex('add_client_phone_index', 'request');

		$this->dropIndex('partner_status_index', 'request');
		$this->dropColumn('request', 'partner_status');

		// востанавливаем удаленные индексы
		$this->createIndex('kind', 'request', 'kind');
		$this->createIndex('type', 'request', 'req_type');
		$this->createIndex('client_phone', 'request', 'client_phone');
		$this->createIndex('add_client_phone_index', 'request', 'add_client_phone');
	}
}