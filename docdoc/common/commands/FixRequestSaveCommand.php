<?php
use dfs\common\components\console\Command;
use dfs\docdoc\models\RequestRecordModel;


/**
 * Исправление бага в БД
 * см. коммит 25dd38c
 *
 */
class FixRequestSaveCommand extends Command
{
	/**
	 *
	 * @return void
	 */
	public function actionFix()
	{
		$came = \Yii::app()->db
			->createCommand("SELECT rh.*  FROM request_history rh
			INNER JOIN request r ON rh.request_id = r.req_id
			WHERE r.req_status = 3 AND req_created > UNIX_TIMESTAMP('2015-01-31')
			AND rh.text LIKE 'Установлен признак пациент дошёл%'
			GROUP BY r.req_id
			")->queryAll();


		foreach ($came as $r) {

			$record = str_replace(['Установлен признак пациент дошёл в аудиозаписи (', ')'], '', $r['text']);
			$this->log($record);
			$rr = RequestRecordModel::model()
				->findByAttributes(['record' => $record, 'isVisit' => 'no']);

			if ($rr) {
				$this->log("Потерян влаг приема у записи {$rr->record_id} для заявки {$rr->request_id}");
				$rr->updateByPk($rr->record_id, ['isVisit' => 'yes']);
			}
		}
	}
} 
