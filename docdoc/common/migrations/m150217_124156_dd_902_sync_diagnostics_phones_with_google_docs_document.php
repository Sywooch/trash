<?php
use dfs\docdoc\models\ClinicModel;

class m150217_124156_dd_902_sync_diagnostics_phones_with_google_docs_document extends CDbMigration
{
	public function safeUp()
	{
		$conn = Yii::app()->db;
		$command = $conn->createCommand("select t.*  from docdoc_phones_temp t join clinic c on c.id = t.clinic_id where type='Диагностика' or type='диагностика';");
		$res = $command->queryAll();

		foreach($res as $r){

			$clinic = ClinicModel::model()->findByPk($r['clinic_id']);

			$log = '';

			if($clinic->asterisk_phone != $r['replaced_phone']){
				$log .= 'asterisk_phone(' . $clinic->asterisk_phone . ') != ' . $r['replaced_phone'];
				$clinic->asterisk_phone = $r['replaced_phone'];
			}

			if($clinic->phone != $r['clinic_phone']){
				$log = 'phone(' . $clinic->phone . ') != ' . $r['clinic_phone'];
				$clinic->phone = $r['clinic_phone'];
			}

			if($log){
				$log = 'клиника ' . $clinic->id . ' ' . $log;
				echo $log, PHP_EOL;
			}
		}
	}

	public function down()
	{
		return true;
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}