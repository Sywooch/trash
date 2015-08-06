<?php

use dfs\docdoc\models\RequestModel;
use dfs\docdoc\objects\Rejection;


/**
 * Установка нового статуса заявкам (STATUS_NOT_CAME)
 */
class m150129_094118_DD_681_new_request_status extends CDbMigration
{
	public function up()
	{
		$this->execute(
			'UPDATE request SET req_status = :newStatus WHERE req_status = :oldStatus AND reject_reason = :reason',
			[
				'newStatus' => RequestModel::STATUS_NOT_CAME,
				'oldStatus' => RequestModel::STATUS_REJECT,
				'reason' => Rejection::REASON_NOT_COME,
			]
		);
	}

	public function down()
	{
		$this->execute(
			'UPDATE request SET req_status = :newStatus WHERE req_status = :oldStatus AND reject_reason = :reason',
			[
				'newStatus' => RequestModel::STATUS_REJECT,
				'oldStatus' => RequestModel::STATUS_NOT_CAME ,
				'reason' => Rejection::REASON_NOT_COME,
			]
		);
	}
}
