<?php

namespace dfs\docdoc\api\rest;

use \dfs\docdoc\models\ClinicModel;
use dfs\docdoc\models\DoctorClinicModel;
use dfs\docdoc\models\CityModel;

/**
 * Description of api_v3
 *
 */
class API_v103 extends API_v102
{
	/**
	 * Получение списка клиник
	 *
	 * @return array
	 */
	protected function clinicList()
	{
		$clinics = $this->_clinicList($this->params);

		if ($this->_partner->json_params) {
			$prices = $this->_partner->getGroupPrices();
			foreach ($clinics['ClinicList'] as &$clinic) {
				$clinic['Rewards'] = $this->getClinicRewards($clinic['Id'], $prices);
			}
		}

		return $clinics;
	}

	/**
	 * Получение количества клиник
	 *
	 * @return array
	 */
	protected function clinicCount()
	{
		$params = $this->params;
		if (isset($params['stations'])) {
			$params['stations'] = explode(',', $params['stations']);
		}

		if (isset($params['clinicType'])) {
			$params['clinicType'] = explode(',', $params['clinicType']);
			if (in_array(ClinicModel::TYPE_CLINIC, $params['clinicType'])) {
				$params['isClinic'] = 'yes';
			}
			if (in_array(ClinicModel::TYPE_DOCTOR, $params['clinicType'])) {
				$params['isDoctor'] = 'yes';
			}
			if (in_array(ClinicModel::TYPE_DIAGNOSTIC, $params['clinicType'])) {
				$params['isDiagnostic'] = 'yes';
			}
		}

		$count = \Clinic::getCount($params);
		unset($params['stations']);
		unset($params['diagnostic']);
		$total = \Clinic::getCount($params);

		return array('Total' => $total, 'ClinicSelected' => $count);
	}

	/**
	 * Полная информация по клинике
	 *
	 * @return array
	 */
	protected function clinicView()
	{
		$params = $this->params;
		$data = array();

		$id = intval($params['id']);
		$clinic = new \Clinic($id);

		if (!empty($clinic->data)) {
			$data = $this->clinicViewMapping($clinic->data);
		}

		return array('Clinic' => array($data));
	}

	/**
	 * Маппинг клиники в JSON
	 *
	 * @param array $clinic
	 *
	 * @return array
	 */
	protected function clinicViewMapping($clinic)
	{
		$data = [];

		$data['Id'] = $clinic['id'];
		$data['Name'] = $clinic['name'];
		$data['ShortName'] = $clinic['short_name'];
		$data['RewriteName'] = $clinic['rewrite_name'];
		$data['Url'] = $clinic['url'];
		$data['City'] = $clinic['city'];
		$data['Street'] = $clinic['street'];
		$data['Description'] = $clinic['description'];
		$data['House'] = $clinic['house'];
		$data['Phone'] = '+' . (empty($clinic['asterisk_phone']) ? $clinic['phone'] : $clinic['asterisk_phone']);
		$data['Logo'] = "http://docdoc.ru/upload/kliniki/logo/" . $clinic['logoPath'];

		$sql = "SELECT t1.id
                    FROM doctor t1
                    INNER JOIN doctor_4_clinic t2 ON t2.doctor_id=t1.id and t2.type = " . DoctorClinicModel::TYPE_DOCTOR . "
                    WHERE t1.status=3 AND t2.clinic_id=" . $clinic['id'];
		$result = query($sql);
		$data['Doctors'] = array();
		while ($row = fetch_object($result)) {
			$data['Doctors'][] = $row->id;
		}

		/*
		 * широта и долгота были перепутаны местами в БД
		 * чтобы была совместимость API меняем их местами
		 */
		$data['Longitude'] = $clinic['latitude'];
		$data['Latitude'] = $clinic['longitude'];

		if ($this->_partner->json_params) {
			$data['Rewards'] = $this->getClinicRewards($data['Id']);
		}

		return $data;
	}

	/**
	 * Массив цен для клиники по группам услуг
	 *
	 * @param $clinicId
	 *
	 * @return array
	 */
	protected function getClinicRewards($clinicId)
	{
		$clinic = ClinicModel::model()->findByPk($clinicId);

		$pricesByClinic = $this->_partner->getGroupPrices('ClinicId');
		$pricesByCity = $this->_partner->getGroupPrices('CityId');
		$defaultPrices = $this->_partner->getGroupPrices();

		$clinicPrices = isset($pricesByClinic[$clinic->id]) ? $pricesByClinic[$clinic->id] : [];
		$cityPrices = isset($pricesByCity[$clinic->city_id]) ? $pricesByCity[$clinic->city_id] : [];

		return array_values($clinicPrices + $cityPrices + $defaultPrices[0]);
	}

}
