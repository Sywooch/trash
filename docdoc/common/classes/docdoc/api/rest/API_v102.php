<?php

namespace dfs\docdoc\api\rest;

use dfs\docdoc\models\DoctorClinicModel;
use dfs\docdoc\models\DoctorModel;

/**
 * Description of api_v2
 *
 * @author Aleksandr Nikulin
 */
class API_v102 extends API_v101
{
	/**
	 * маппинг доктора в json
	 *
	 * @param DoctorModel $row
	 *
	 * @return array
	 */
	protected function doctorMapping($row)
	{
		$data = parent::doctorMapping($row);
		$data['Id'] = (int)$row->id;
		$data['Clinics'] = $this->getClinicIds(intval($row->id));

		return $data;
	}

	/**
	 * Получение списка врачей и их параметров
	 *
	 * @return array
	 */
	protected function doctorList()
	{
		$params = $this->params;

		if (isset($params['stations'])) {
			if(empty($params['stations'])){
				unset($params['stations']);
			} else {
				$params['stations'] = explode(',', $params['stations']);
			}
		}

		$params['count'] = $this->getCountDoctorsWithLimit($params);

		$data = array();

		$doctors = \Doctor::getItems($params);
		$count = \Doctor::getCount($params);

		$k = 0;
		foreach ($doctors as $item) {
			$data[$k] = $this->oldDoctorMapping($item);
			$k++;
		}

		return array('Total' => $count, 'DoctorList' => $data);
	}

	/**
	 * Мапинг массива доктор
	 *
	 * @param array $item
	 */
	protected function oldDoctorMapping(array $item)
	{
		$data['Id'] = $item['Id'];
		$data['Name'] = $item['Name'];

		if (!empty($item['Alias'])) {
			$data['Alias'] = $item['Alias'];
		}

		$data['Rating'] = round($item['SortRating'], 2);
		$data['InternalRating'] = $item['RatingInternal'];
		$data['Price'] = $item['Price'];
		$data['SpecialPrice'] = $this->_partner && $this->_partner->use_special_price ? $item['SpecialPrice'] : null;
		$data['Sex'] = ($item['Sex'] == 2) ? 1 : 0;
		$data['Img'] = "http://docdoc.ru/img/doctorsNew/" . $item['SmallImg'];
		$data['OpinionCount'] = $this->reviewCountByDoctorId($item['Id']);
		$data['TextAbout'] = checkField($item['Description'], "t", '');
		if (!empty($item['ExperienceYear'])) {
			$data['ExperienceYear'] = date('Y') - $item['ExperienceYear'];
		} else {
			$data['ExperienceYear'] = 0;
		}
		$data['Departure'] = $item['Departure'];
		$data['Category'] = $item['category'];
		$data['Clinics'] = $this->getClinicIds(intval($item['Id']));
		$data['Degree'] = $item['degree'];
		$data['Rank'] = $item['rank'];
		$data['Specialities'] = $item['Specialities'];
		$data['Stations'] = $this->stationsMappingOld($item['Stations']);

		return $data;
	}

	/**
	 * Метод, который возвращает список клиник
	 *
	 * @return array    возвращает массив клиник
	 */
	protected function getClinics()
	{
		static $clinics;
		if (is_null($clinics)) {
			$sql = "SELECT doctor_id, clinic_id
					FROM doctor_4_clinic where type = " . DoctorClinicModel::TYPE_DOCTOR;
			$result = query($sql);
			$clinics = array();

			while ($row = fetch_array($result)) {
				$doctorId = $row['doctor_id'];
				if (!isset($clinics[$doctorId])) {
					$clinics[$doctorId] = array();
				}
				$clinics[$doctorId][] = (int)$row['clinic_id'];
			}
		}
		return $clinics;
	}

	/**
	 * Метод, который возввращает массив идентификаторов клиник для конкретного врача
	 *
	 * @param int $id идентификатор доктора
	 *
	 * @return int[]
	 */
	protected function getClinicIds($id)
	{
		$clinics = $this->getClinics();
		return isset($clinics[$id]) ? $clinics[$id] : array();
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

		$clinic = $clinic->data;
		if (!empty($clinic)) {
			if ($clinic['isClinic'] == 'yes') {
				$data['Id'] = $clinic['id'];
				$data['Name'] = $clinic['name'];
				$data['ShortName'] = $clinic['short_name'];
				$data['RewriteName'] = $clinic['rewrite_name'];
				$data['Url'] = $clinic['url'];
				$data['City'] = $clinic['city'];
				$data['Street'] = $clinic['street'];
				$data['Description'] = $clinic['description'];
				$data['House'] = $clinic['house'];
				if (!empty($clinic['asterisk_phone'])) {
					$data['Phone'] = '+' . $clinic['asterisk_phone'];
				} else {
					$data['Phone'] = '+' . $clinic['phone'];
				}
				$data['Logo'] = "http://docdoc.ru/upload/kliniki/logo/" . $clinic['logoPath'];

				$sql = "SELECT t1.id
                        FROM doctor t1
                        INNER JOIN doctor_4_clinic t2 ON t2.doctor_id=t1.id and t2.type = " . DoctorClinicModel::TYPE_DOCTOR . "
                        WHERE t1.status=3 AND t2.clinic_id=" . $clinic['id'];
				$result = query($sql);
				while ($row = fetch_object($result)) {
					$data['Doctors'][] = $row->id;
				}
				/*
				 * широта и долгота были перепутаны местами в БД
				 * чтобы была совместимость API меняем их местами
				 */
				$data['Longitude'] = $clinic['latitude'];
				$data['Latitude'] = $clinic['longitude'];
			}
		}

		return array('Clinic' => array($data));
	}
}
