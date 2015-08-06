<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 05.09.14
 * Time: 12:25
 */

namespace dfs\docdoc\api\rest;

use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\models\DistrictModel;
use dfs\docdoc\models\DoctorClinicModel;
use dfs\docdoc\models\DoctorModel;
use dfs\docdoc\models\SlotModel;
use dfs\docdoc\models\SectorModel;
use dfs\docdoc\models\StreetModel;
use dfs\docdoc\objects\Coordinate;
use dfs\docdoc\models\CityModel;
use dfs\docdoc\models\StationModel;
use CHttpException;

/**
 * Class API_v105
 *
 * @package dfs\docdoc\api\rest
 */
class API_v105 extends API_v104
{
	/**
	 * Получение методов
	 *
	 * @return array
	 */
	public function getMethods()
	{
		$methods = parent::getMethods();
		$methods['slot/list'] = 'slotList';
		$methods['street'] = 'streetList';
		$methods['nearestStation'] = 'nearestStationList';
		$methods['nearDistricts'] = 'nearDistricts';

		return $methods;
	}

	/**
	 * Метод, который возвращает список клиник c онлайн записью
	 *
	 * @param int $doctorId
	 * @return array возвращает массив идентификаторов клиник
	 */
	protected function getBookingClinicsIds($doctorId)
	{
		static $clinics = null;

		if (is_null($clinics)) {
			$sql = "SELECT doctor_id, clinic_id
					FROM doctor_4_clinic d4c
					JOIN clinic c ON c.id = d4c.clinic_id
					LEFT JOIN api_clinic ac on ac.id = c.external_id
					WHERE (has_slots and c.online_booking and ac.enabled)
						and d4c.type = " . DoctorClinicModel::TYPE_DOCTOR;

			$result = query($sql);
			$clinics = [];

			while ($row = fetch_array($result)) {
				$id = $row['doctor_id'];

				if (!isset($clinics[$id])) {
					$clinics[$id] = [];
				}

				$clinics[$id][] = (int)$row['clinic_id'];
			}
		}

		return isset($clinics[$doctorId]) ? $clinics[$doctorId] : [];
	}

	/**
	 * Слоты по доктору
	 *
	 * @return array
	 */
	protected function slotList()
	{
		$params = $this->params;

		$doctorId = $params['doctor'];
		$clinicId = $params['clinic'];
		$startDate = $params['from'];
		$endDate = $params['to'];

		if (!strtotime($startDate) || !strtotime($endDate)) {
			return $this->getError('Неверный формат даты');
		}

		$dc = DoctorClinicModel::model()->findDoctorClinic($doctorId, $clinicId);

		$data = [];

		if($dc && $dc->has_slots && $dc->clinic->online_booking && $dc->apiDoctor && $dc->apiDoctor->enabled){

			$slots = SlotModel::model()
				->forDoctorInClinic($dc->id)
				->inInterval($startDate, $endDate)
				->activeSlots()
				->findAll();

			$data = array_map(
				function (SlotModel $x) {
					return $this->slotMapping($x);
				},
				$slots
			);
		}

		return ['SlotList' => $data];
	}

	/**
	 * Мапинг слота
	 *
	 * @param SlotModel $slot
	 * @return array
	 */
	protected function slotMapping(SlotModel $slot)
	{
		return [
			'Id' => $slot->external_id,
			'StartTime' => $slot->start_time,
			'FinishTime' => $slot->finish_time,
		];
	}

	/**
	 * Получение списка врачей и их параметров
	 *
	 * @return array
	 *
	 * @throws \Exception
	 */
	protected function doctorList()
	{
		if (isset($this->params['lat']) || isset($this->params['lng'])) {

			if (!isset($this->params['lat'])) {
				return $this->getError("Отсутствует параметр lat");
			}

			if (!isset($this->params['lng'])) {
				return $this->getError("Отсутствует параметр lng");
			}

			if (!isset($this->params['radius'])) {
				$this->params['radius'] = Coordinate::MAX_RADIUS;
			}

			$coord = new Coordinate($this->params['lat'], $this->params['lng']);
			if (!$coord->isValid()) {
				return $this->getError("Некорректное значение координаты");
			}

			if (!is_numeric($this->params['radius'])) {
				return $this->getError("Некорректное значение радиуса");
			}
		} elseif (isset($this->params['order']) && strpos($this->params['order'], 'distance') !== false) {
			unset($this->params['order']);
		}

		return parent::doctorList();
	}

	/**
	 * маппинг доктора в списке в json
	 *
	 * @param array $row [typeSearch, DoctorModel]
	 *
	 * @return array
	 */
	protected function doctorListMapping($row)
	{
		$properties = parent::doctorListMapping($row);
		$properties['Extra'] = $row[0];

		if($this->_partner && !$this->_partner->show_watermark){
			$properties['Img'] = "http://" . \Yii::app()->params['hosts']['front'] . "/img/doctorsNew/" . $row[1]->id . ".110x150.jpg";
		}

		return $properties;
	}

	/**
	 * Мапинг докторов
	 *
	 * @param DoctorModel $row
	 * @return array
	 */
	protected function doctorMapping($row)
	{
		$data = parent::doctorMapping($row);
		$data['Id'] = (int)$row->id;
		$data['Clinics'] = $this->getClinicIds(intval($row->id));
		$data['BookingClinics'] = $this->getBookingClinicsIds((int)$row->id);
		$data['isActive'] = $row->isActive();

		if($this->_partner && !$this->_partner->show_watermark){
			$data['Img'] = "http://" . \Yii::app()->params['hosts']['front'] . "/img/doctorsNew/" . $row->id . ".110x150.jpg";
		}

		return $data;
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
		$data['StreetId'] = $clinic['street_id'];
		$data['Description'] = $clinic['description'];
		$data['House'] = $clinic['house'];
		$data['Phone'] = '+' . (empty($clinic['asterisk_phone']) ? $clinic['phone'] : $clinic['asterisk_phone']);
		$data['Logo'] = "http://docdoc.ru/upload/kliniki/logo/" . $clinic['logoPath'];
		$data['DistrictId'] = $clinic['district_id'];

		$sql = "SELECT t1.id
                    FROM doctor t1
                    INNER JOIN doctor_4_clinic t2 ON t2.doctor_id=t1.id
                    WHERE t1.status=3 AND t2.clinic_id=" . $clinic['id'];
		$result = query($sql);
		$data['Doctors'] = array();
		while ($row = fetch_object($result)) {
			$data['Doctors'][] = $row->id;
		}

		$data['Longitude'] = $clinic['longitude'];
		$data['Latitude'] = $clinic['latitude'];

		if ($this->_partner->json_params) {
			$data['Rewards'] = $this->getClinicRewards($data['Id']);
		}

		return $data;
	}

	/**
	 * Маппинг клиник в JSON
	 *
	 * @param ClinicModel $clinic
	 * @param array $params
	 *
	 * @return array
	 */
	protected function clinicMapping($clinic, $params)
	{
		return $this->clinicStruct($clinic, $params);
	}

	/**
	 * Получение списка специальностей
	 *
	 * @return array
	 */
	protected function specialityList()
	{
		$sectorModel = SectorModel::model()->active()->ordered()->cache(3600);

		if (!isset($this->params['onlySimple']) || $this->params['onlySimple'] != 0) {
			$sectorModel->simple();
		}

		if (!empty($this->params['city'])) {
			$sectorModel->inCity($this->params['city']);
		}

		$data = [];
		foreach ($sectorModel->findAll() as $sector) {
			$data[] = $this->specialityMapping($sector);
		}

		return [ 'SpecList' => $data ];
	}

	/**
	 * Маппинг свойств модели sector на json
	 *
	 * @param \dfs\docdoc\models\SectorModel $sector
	 *
	 * @return array
	 */
	protected function specialityMapping($sector)
	{
		$properties = parent::specialityMapping($sector);
		$properties['NameGenitive'] = $sector->name_genitive;
		$properties['NamePlural'] = $sector->name_plural;
		$properties['NamePluralGenitive'] = $sector->name_plural_genitive;
		$properties['IsSimple'] = boolval(!$sector->is_double);

		return $properties;
	}

	/**
	 * Получение списка улиц города
	 *
	 * @return array
	 */
	protected function streetList()
	{
		$params = $this->params;

		$cityId = isset($params['city']) ? intval($params['city']) : 0;
		if (!$cityId) {
			return $this->getError('Не указан город');
		}

		$streets = StreetModel::model()
			->inCity($cityId)
			->findAll(['order' => 't.title']);

		$data = [];
		foreach ($streets as $street) {
			$data[] = $this->streetMapping($street);
		}

		return ['StreetList' => $data];
	}

	/**
	 * Маппинг модели улицы
	 *
	 * @param StreetModel $street
	 *
	 * @return array
	 */
	protected function streetMapping($street)
	{
		$item = [
			'Id' => $street->street_id,
			'CityId' => $street->city_id,
			'Title' => $street->title,
			'RewriteName' => $street->rewrite_name,
		];

		return $item;
	}

	/**
	 * Получение списка городов
	 *
	 * @return array
	 */
	protected function cityList()
	{
		$data = [];

		$cities = CityModel::model()->active()->findAll();

		foreach ($cities as $city) {
			$data[] = $this->cityMapping($city);
		}

		return array('CityList' => $data);
	}

	/**
	 * Маппинг для города
	 *
	 * @param CityModel $city модель города
	 *
	 * @return array
	 */
	protected function cityMapping($city)
	{
		return [
			'Id'        => $city->id_city,
			'Name'      => $city->title,
			'Alias'     => $city->rewrite_name,
			'Phone'     => $city->site_phone->getNumber(),
			'Latitude'  => $city->lat,
			'Longitude' => $city->long,
		];
	}

	/**
	 * Получает список станций метро
	 *
	 * @return string[]
	 */
	protected function nearestStationList()
	{
		$params = $this->params;
		$stations = array();

		if (!empty($params['id'])) {
			$stations = StationModel::model()
				->near([$params['id']])
				->findAll();
		}

		$data = array();
		foreach ($stations as $station) {
			$data[] = $this->stationsMapping($station);
		}
		return array('StationList' => $data);
	}

	/**
	 * Получает ближайшие районы
	 *
	 * @return array
	 *
	 * @throws CHttpException
	 */
	protected function nearDistricts()
	{
		$params = $this->params;
		if (empty($params['id'])) {
			throw new CHttpException(404, "Не указан идентификатор района");
		}

		$model = DistrictModel::model()->findByPk($params['id']);
		if (!$model) {
			throw new CHttpException(404, "Идентификатор задан неверно");
		}

		$data = [];

		foreach ($model->getClosestDistricts(!empty($params['limit']) ? $params['limit'] : 0) as $closestDistrict) {
			$data[] = $this->districtMapping($closestDistrict);
		}

		return ['DistrictList' => $data];
	}
}
