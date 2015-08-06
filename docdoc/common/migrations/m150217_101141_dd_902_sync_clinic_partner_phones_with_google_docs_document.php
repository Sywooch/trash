<?php
use dfs\docdoc\models\ClinicPartnerPhoneModel;
use dfs\docdoc\models\PhoneModel;

class m150217_101141_dd_902_sync_clinic_partner_phones_with_google_docs_document extends CDbMigration
{
	public function safeUp()
	{
		$conn = Yii::app()->db;
		$command = $conn->createCommand("select t.*, p.id as partner_id from docdoc_phones_temp t join partner p on p.login = t.partner_login where type='подменный';");
		$res = $command->queryAll();

		foreach($res as $r){
			if(!$r['partner_id']){
				echo "Партнер {$r['partner_id']} не найден", PHP_EOL;
				continue;
			}

			$phone1 = PhoneModel::model()->createPhone($r['replaced_phone']);
			$phone2 = PhoneModel::model()->createPhone($r['clinic_phone']);

			$cph = ClinicPartnerPhoneModel::model()->byClinicId($r['clinic_id'])->byPartnerId($r['partner_id'])->find();

			if(!$cph){
				echo "не найден clinic_partner_phone clinic_id={$r['clinic_id']} partner_id = {$r['partner_id']}", PHP_EOL;
				$cph = new ClinicPartnerPhoneModel();
				$cph->clinic_id = $r['clinic_id'];
				$cph->partner_id = $r['partner_id'];
			}

			$cph->phone_id = $phone1->id;
			if(!$phone2){
				$a = '';
				$phone2 = PhoneModel::model()->createPhone($r['clinic_phone']);
			}
			$cph->clinic_phone_id = $phone2->id;

			if(!$cph->save()){
				echo 'Ошибка сохранения clinic_partner_phone ' . var_export($cph->getErrors(), true), PHP_EOL;
				return false;
			}
		}
	}

	public function down()
	{
		return true;
	}
}