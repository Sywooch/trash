<?php

use dfs\docdoc\models\RequestRecordModel;
use dfs\docdoc\models\RequestModel;

/**
 * @package migrations
 */
class m150324_000001_DD_1151_mrict extends CDbMigration
{

	/**
	 * Применяет миграцию в транзакции
	 *
	 * @return bool
	 */
	public function up()
	{
		$requests = \Yii::app()->db->createCommand("SELECT * FROM request
			INNER JOIN request_history ON (request.req_id = request_history.request_id)
			WHERE partner_id=647 AND text LIKE '%. Добавлен аудиофайл'
		")->queryAll();

		foreach ($requests as $request) {

			$records = Yii::app()->db
				->createCommand("SELECT * FROM request_record WHERE request_id={$request['req_id']} ORDER BY record_id")->queryAll();

			foreach ($records as $i => $r) {
				if ($i == 0) {
					continue;
				}

				$record = RequestRecordModel::model()->findByPk($r['record_id']);
				if ($record->type == RequestRecordModel::TYPE_UNDEFINED) {
					$r = RequestModel::saveByRecord($record);
					echo "Создана новая заявка " . $r->req_id . PHP_EOL;
				}
			}

		}

		return true;
	}
}