<?php

use dfs\docdoc\models\RequestModel;
use dfs\docdoc\models\RequestHistoryModel;

/**
 * Файл класса m141023_000000_DD_405_zoon_requests
 *
 * Миграция, разлепляющая заявки по партнёру ZOON
 *
 * @author  Mikhail Vasilyev <mvasilyev@docdoc.ru>
 * @link    https://docdoc.atlassian.net/browse/DD-405
 * @package migrations
 */
class m141023_000000_DD_405_zoon_requests extends CDbMigration
{

	/**
	 * Применяет миграцию в транзакции
	 *
	 * @return bool
	 */
	public function safeUp()
	{
		$criteria = new CDbCriteria;
		$criteria->condition = "t.req_created > :req_created";
		$criteria->params["req_created"] = CDateTimeParser::parse("02.10.2014", 'dd.MM.yyyy');

		foreach (RequestModel::model()->byPartner(16)->findAll($criteria) as $request) {
			$requestRecords = $request->request_record;
			if (count($requestRecords) > 1) {
				foreach ($requestRecords as $requestRecord) {
					$this->insert(
						"request",
						[
							"id_city"      => 1,
							"client_phone" => $request->client_phone,
							"req_created"  => CDateTimeParser::parse($requestRecord->crDate, 'yyyy-MM-dd HH:mm:ss'),
							"req_status"   => 1,
							"req_type"     => $request->req_type,
							"kind"         => $request->kind,
							"clinic_id"    => $request->clinic_id,
							"is_hot"       => 1,
							"for_listener" => 1,
							"enter_point"  => "ClinicCall",
							"is_transfer"  => 1,
							"partner_id"   => 16,
						]
					);

					$requestId = Yii::app()->db->getLastInsertId();

					$newRequestRecord = clone $requestRecord;
					$newRequestRecord->record_id = null;
					$newRequestRecord->isNewRecord = true;
					$newRequestRecord->request_id = $requestId;
					if (!$newRequestRecord->save()) {
						return false;
					}

					$newRequestHistory = new RequestHistoryModel;
					$newRequestHistory->request_id = $requestId;
					$newRequestHistory->text = "Автоматическое создание заявки от склеенной заявки ZOON";
					if (!$newRequestHistory->save()) {
						return false;
					}
				}
			}
		}

		return true;
	}
}
