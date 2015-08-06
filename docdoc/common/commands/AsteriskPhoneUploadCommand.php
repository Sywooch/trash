<?php
/**
 * Created by PhpStorm.
 * User: atyutyunnikov
 * Date: 13.02.15
 * Time: 17:04
 */

use dfs\common\components\console\Command;
use dfs\docdoc\models\CityModel;
use dfs\docdoc\models\QueueModel;
use dfs\docdoc\models\PartnerModel;
use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\objects\Phone;
use dfs\docdoc\models\ClinicPartnerPhoneModel;

/**
 * Загрузка телефонов в базы данных астерисков
 *
 * Class AsteriskPhoneUploadCommand
 */
class AsteriskPhoneUploadCommand extends Command
{
	/**
	 * @param array $args
	 *
	 * @return integer
	 */
	public function run($args)
	{
		$phones = [];
		$phones = CMap::mergeArray($phones, $this->collectCityPhones());
		$phones = CMap::mergeArray($phones, $this->collectPartnerPhones());
		$phones = CMap::mergeArray($phones, $this->collectClinicPhones());
		$phones = CMap::mergeArray($phones, $this->collectClinicPartnerPhones());

		$sql = "INSERT INTO phone_rules (phone, type, params, ctime) VALUES ";
		$time = date('c');
		$values = [];

		foreach ($phones as $phone => $params) {
			$values[] = "('$phone', '{$params['type']}', '{$params['params']}', '$time')";
		}

		$sql .= implode(',', $values);

		$sql .= " ON DUPLICATE KEY UPDATE type=VALUES(type), params = VALUES(params);";

		$config = Yii::app()->params['asterisk']['db_servers'];

		foreach ($config as $serverName => $c) {
			try {
				$connection = new CDbConnection($c['connectionString'], $c['username'], $c['password']);
				$transaction = $connection->beginTransaction();

				try {
					$command = $connection->createCommand($sql);
					$command->execute();
					$transaction->commit();

					$this->log("Сервер $serverName: запрос успешно выполнен");
				} catch (Exception $e) {
					$transaction->rollback();
				}
			} catch (Exception $e) {
				$this->log("Сервер $serverName: " . $e->getMessage(), CLogger::LEVEL_ERROR);
			}
		}

		$this->log('Работа скрипта закончена');
	}

	/**
	 * Собирает телефоны партнеров
	 *
	 * @return array
	 */
	protected function collectPartnerPhones()
	{
		$phones = [];

		$partners = PartnerModel::model()->withPhones()->findAll();

		foreach ($partners as $p) {
			foreach($p->phones as $phone){
				$phones[$phone->phone->number] = [
					'type' => 'cc',
					'params' => $p->phone_queue
				];
			}
		}

		return $phones;
	}

	/**
	 * Собирает телефоны города и сборщиков
	 *
	 * @return array
	 */
	protected function collectCityPhones()
	{
		$phones = [];

		$cities = CityModel::model()->findAll();

		foreach ($cities as $city) {
			$city->site_phone->isValid() && $phones[$city->site_phone->getNumber()] = ['type' => 'cc', 'params' => QueueModel::QUEUE_CALLCENTER];
			$city->site_office->isValid() && $phones[$city->site_office->getNumber()] = ['type' => 'cc', 'params' => QueueModel::QUEUE_CALLCENTER];
			$city->opinion_phone->isValid() && $phones[$city->opinion_phone->getNumber()] = ['type' => 'cc', 'params' => QueueModel::QUEUE_OPINION];
		}

		return $phones;
	}

	/**
	 * @return array
	 */
	protected function collectClinicPhones()
	{
		$phones = [];

		$dataProvider = new CActiveDataProvider(
			ClinicModel::class,
			[
				'criteria' => [
					'with' => [
						'phones' => [
							'joinType' => 'left join'
						]
					]
				]
			]
		);

		/** @var ClinicModel[] $clinicIterator */
		$clinicIterator = new CDataProviderIterator($dataProvider, 500);

		foreach ($clinicIterator as $clinic) {

			if($clinic->asterisk_phone && $clinic->phone){
				$astPhone = new Phone($clinic->asterisk_phone);
				$clinicPhone = new Phone($clinic->phone);

				if($astPhone->isValid() && $clinicPhone->isValid()){
					$phones[$astPhone->getNumber()] = [
						'type' => 'phone',
						'params' => $clinic->phone
					];
				}
			}

			if($clinic->phones) {
				foreach($clinic->phones as $p){
					$p = new Phone($p->number_p);

					if($p->isValid()){
						$phones[$p->getNumber()] = [
							'type' => 'cc',
							'params' => QueueModel::QUEUE_CALLCENTER
						];
					}
				}
			}

			$mainClinicPhone = $clinic->phone;

			if(!$mainClinicPhone && $clinic->parentClinic){
				$mainClinicPhone = $clinic->parentClinic->phone;
			}

			if($mainClinicPhone){
				$p = new Phone($mainClinicPhone);

				if($p->isValid()){
					$phones["cid{$clinic->id}"] = [
						'type' => 'phone',
						'params' => $p->getNumber(),
					];
				}
			}
		}

		return $phones;
	}

	/**
	 * Сбор подменных телефонов для партнеров в клиниках
	 *
	 * @return array
	 */
	public function collectClinicPartnerPhones()
	{
		$phones = [];

		$clinicPartnerPhones = ClinicPartnerPhoneModel::model()->with(['clinic' => ['condition' => "clinic.phone is not null or clinic.phone != ''"]])->findAll();

		foreach($clinicPartnerPhones as $cpp){
			$phones[$cpp->phone->number] = [
				'type' => 'phone',
				'params' => $cpp->clinic->phone,
			];
		}

		return $phones;
	}
}