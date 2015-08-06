<?php

namespace dfs\docdoc\reports;

use dfs\docdoc\models\PartnerModel;
use dfs\docdoc\objects\google\calls\MissedCallsRaw;
use dfs\docdoc\objects\call\Provider;
use dfs\docdoc\models\ClinicModel;

/**
 * Class MissedCallsRawReport
 * @package dfs\docdoc\reports
 */
class MissedCallsRawReport extends BigQueryReport
{
	/**
	 * @var ClinicModel[]
	 */
	private $_clinics = [];

	/**
	 * Получение модели
	 *
	 * @return \dfs\docdoc\objects\google\BigQuery|MissedCallsRaw
	 */
	public function getBqModel()
	{
		return new MissedCallsRaw();
	}

	/**
	 * Парсинг json данных
	 *
	 * @param array $data
	 * @return array
	 * @throws \Exception
	 */
	public function parse(array $data)
	{
		$clinics = $this->_clinics;

		if (!$clinics) {
			throw new \Exception('Необходимо инициализировать список клиник');
		}

		$rows = [];

		// Собираем данные
		foreach ($data as $record) {
			$replacedPhones = explode('<br>', $record['did']);
			$clinicId = null;
			$partner = null;

			if ($record['contact_category_name'] == 'trunks' || $record['scenario_name'] == 'trunks') {
				$name = 'КЦ';
			} else {
				$name = 'Клиника не определена';
				foreach ($replacedPhones as $phone) {
					if ($clinic = $this->_getClinicByReplacedPhone($phone, $clinics)) {
						$clinicId = $clinic->id;
						$name = $clinic->name;
						$partner = $this->getPartner($phone, $clinic);
						break;
					}
				}
			}

			$rows[] = array_merge($record, [
				'clinic_id'    => $clinicId,
				'clinic_name'  => $name,
				'clinic_phone' => implode(', ', $replacedPhones),
				'partner_id'   => !is_null($partner) ? $partner->id : null,
				'partner_name' => !is_null($partner) ? $partner->name : null,
			]);
		}

		return $rows;
	}

	/**
	 * Получение модели партнера
	 *
	 * @param $phone
	 * @param $clinic
	 *
	 * @return PartnerModel|null
	 */
	public function getPartner($phone, $clinic)
	{
		if (!empty($clinic->partnerPhones)) {
			foreach ($clinic->partnerPhones as $partnerPhone) {
				if ($partnerPhone->phone->number == $phone) {
					return $partnerPhone->partner;
				}
			}
		}

		return null;
	}

	/**
	 * Парсинг списка всех подменных телефонов
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	public static function getReplacedPhones($data)
	{
		$phones = [];

		foreach ($data as $row) {
			$phones = array_merge(explode('<br>', $row['did']), $phones);
		}

		return array_unique($phones);
	}

	/**
	 * Возвращает клинику из списка моделей по подменному номеру
	 *
	 * @param string $phone
	 * @param ClinicModel[] $clinics
	 *
	 * @return ClinicModel|null
	 */
	protected function _getClinicByReplacedPhone($phone, array $clinics)
	{
		foreach ($clinics as $clinic) {
			if ($clinic->asterisk_phone == $phone) {
				return $clinic;
			}

			foreach ($clinic->partnerPhones as $partnerPhone) {
				if ($partnerPhone->phone->number == $phone) {
					return $clinic;
				}
			}
		}

		return null;
	}

	/**
	 * Получение данных о звонках
	 *
	 * @return array
	 */
	public function loadCalls()
	{
		$data = [];

		foreach (Provider::getAll() as $account) {
			if (get_class($account) == 'dfs\docdoc\objects\call\Uiscom') {
				$records = $account->getFailedLogs();
				$data = array_merge($data, $records);
			}
		}

		// Выборка списка клиник по подменным номерам
		$phones = self::getReplacedPhones($data);
		$clinics = ClinicModel::model()
			->byReplacedPhoneWithPartner($phones)
			->findAll();
		$this->setClinics($clinics);

		return $this->parse($data);
	}

	/**
	 * Генерация отчета
	 */
	public function generate()
	{
		foreach ($this->loadCalls() as $item) {
			$row = [
				'direction' => 'IN',
				'date' => $item['start_time'],
				'caller' => $item['ani'],
				'phone_number' => $item['ani'],
				'called_number' => $item['clinic_phone'],
				'contact_name' => $item['contact_name'],
				'category_name' => $item['contact_category_name'],
				'duration' => $item['duration'],
				'response_duration' => $item['forwarding_duration'],
				'tariff_duration' => $item['tariff_duration'],
				'scenario_name' => $item['scenario_name'],
				'cost' => $item['cost'],
				'clinic_id' => $item['clinic_id'],
				'clinic_name' => $item['clinic_name'],
				'partner_id' => $item['partner_id'],
				'partner_name' => $item['partner_name'],
			];

			$this->addData($row);
		}
	}

	/**
	 * @param ClinicModel[] $clinics Массив клиник id, name с ключами в виде номеров телефона
	 *
	 * @return $this
	 */
	public function setClinics(array $clinics)
	{
		$this->_clinics = $clinics;

		return $this;
	}

}