<?php

namespace dfs\docdoc\listInterface;

use dfs\docdoc\models\AreaModel;
use dfs\docdoc\models\CityModel;
use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\models\DiagnosticaModel;
use dfs\docdoc\models\DistrictModel;
use dfs\docdoc\models\PartnerModel;
use dfs\docdoc\models\RegCityModel;
use dfs\docdoc\models\SectorModel;
use dfs\docdoc\models\StationModel;
use dfs\docdoc\models\StreetModel;


/**
 * Формирование списка клиник
 *
 * Возможные значения параметров поиска (пример):
 *    clinicId = [1,2]
 *    city = 1
 *    speciality = 'psihologiya'
 *    diagnostic = 1
 *    regCity = 'dolgoprudnyi'
 *    area = 'cao'
 *    district = 'arbat'
 *    street = 'abelmanovskaya'
 *    station = 'aviamotornaya'
 *    stations = '1,2,3'
 *    partnerId = 1
 *
 *    sort = 'rating'
 *    sortDirection = 'asc'
 *    start = 0
 *    count = 10
 *    limit = 10
 *    page = 1
 *
 *    near = 'strict'
 *    withNearest = true
 *
 *    isClinic = 'yes'
 *    isDiagnostic = null
 *    isDoctor' = 'no'
 *    clinicType = '1,2'
 *
 *
 * @method ClinicModel[] getItems
 *
 * @package dfs\docdoc\listInterface
 */
class ClinicList extends ListInterface
{
	/**
	 * Дефолтные scopes
	 *
	 * @var array
	 */
	protected $scopes = [
		'active' => null,
		'withRating' => null,
	];

	/**
	 * Параметры сортировки
	 *
	 * @var array
	 */
	protected $sorting = [
		'rating' => [
			'title' => 'Рейтингу',
			'direction' => 'desc',
		],
		'reviews' => [
			'title' => 'Отзывам',
			'direction' => 'desc',
		],
		'price' => [
			'title' => 'Стоимости',
			'direction' => 'desc',
		],
		'name' => [
			'direction' => 'asc',
		],
	];

	protected $limit = 10;
	protected $maxLimit = 500;

	/**
	 * Поиск по идентификаторам клиник
	 *
	 * @var int[]
	 */
	protected $clinicIds = null;

	/**
	 * Поиск по городу
	 *
	 * @var CityModel
	 */
	protected $city = null;

	/**
	 * Поиск по специальности
	 *
	 * @var SectorModel
	 */
	protected $speciality = null;

	/**
	 * Поиск по диагностике
	 *
	 * @var DiagnosticaModel
	 */
	protected $diagnostic = null;

	/**
	 * Поиск по станции
	 *
	 * @var StationModel
	 */
	protected $station = null;

	/**
	 * Поиск по станциям
	 *
	 * @var array | null
	 */
	protected $stationIds = null;

	/**
	 * Поиск по городу Подмосковья
	 *
	 * @var RegCityModel
	 */
	protected $regCity = null;

	/**
	 * Поиск по области
	 *
	 * @var AreaModel
	 */
	protected $area = null;

	/**
	 * Поиск по району города
	 *
	 * @var DistrictModel
	 */
	protected $district = null;

	/**
	 * Поиск по улице
	 *
	 * @var StreetModel
	 */
	protected $street = null;

	/**
	 * Выборка для партнёра
	 *
	 * @var PartnerModel
	 */
	protected $partner = null;

	/**
	 * Тип клиник (клиники, частные врачи, диагностические центры)
	 *
	 * @var array
	 */
	protected $clinicTypes = [];


	/**
	 * Специальность
	 *
	 * @return SectorModel
	 */
	public function getSpeciality()
	{
		return $this->speciality;
	}

	/**
	 * Диагностика
	 *
	 * @return DiagnosticaModel
	 */
	public function getDiagnostic()
	{
		return $this->diagnostic;
	}

	/**
	 * Станция
	 *
	 * @return StationModel
	 */
	public function getStation()
	{
		return $this->station;
	}

	/**
	 * Список ИД станций
	 *
	 * @return array|null
	 */
	public function getStationIds()
	{
		return $this->stationIds;
	}

	/**
	 * Городу Подмосковья
	 *
	 * @return RegCityModel
	 */
	public function getRegCity()
	{
		return $this->regCity;
	}

	/**
	 * Область
	 *
	 * @return AreaModel
	 */
	public function getArea()
	{
		return $this->area;
	}

	/**
	 * Район
	 *
	 * @return DistrictModel
	 */
	public function getDistrict()
	{
		return $this->district;
	}

	/**
	 * Улица
	 *
	 * @return StreetModel
	 */
	public function getStreet()
	{
		return $this->street;
	}

	/**
	 * Партнёр
	 *
	 * @return PartnerModel
	 */
	public function getPartner()
	{
		return $this->partner;
	}


	/**
	 * Установка id клиник для поиска
	 *
	 * @param mixed $value
	 *
	 * @return $this
	 */
	public function setClinicId($value)
	{
		$clinicIds = is_array($value) ? $value : explode(',', $value);

		foreach ($clinicIds as $v) {
			$id = intval($v);
			if ($id > 0) {
				$this->clinicIds[$id] = $id;
			}
		}

		return $this;
	}

	/**
	 * Установка специальности для поиска
	 *
	 * @param mixed $value
	 *
	 * @return $this
	 */
	public function setSpeciality($value)
	{
		if ($value instanceof SectorModel) {
			$this->speciality = $value;
		}
		elseif (is_numeric($value)) {
			$this->speciality = SectorModel::model()->findByPk($value);
		} else {
			$this->speciality = SectorModel::model()->byRewriteSpecName($value)->find();
		}

		if (!$this->speciality) {
			$this->errors['speciality'] = 'Speciality not found';
		}

		return $this;
	}

	/**
	 * Установка диагностики для поиска
	 *
	 * @param mixed $value
	 *
	 * @return $this
	 */
	public function setDiagnostic($value)
	{
		if ($value instanceof DiagnosticaModel) {
			$this->diagnostic = $value;
		}
		elseif (is_numeric($value)) {
			$this->diagnostic = DiagnosticaModel::model()->findByPk($value);
		} else {
			$this->diagnostic = DiagnosticaModel::model()->searchByAlias($value)->find();
		}

		if (!$this->diagnostic) {
			$this->errors['diagnostic'] = 'Diagnostic not found';
		}

		return $this;
	}

	/**
	 * Установка города для поиска
	 *
	 * @param mixed $value
	 *
	 * @return $this
	 */
	public function setCity($value)
	{
		if ($value instanceof CityModel) {
			$this->city = $value;
		}
		elseif (is_numeric($value)) {
			$this->city = CityModel::model()->findByPk($value);
		} else {
			$this->city = CityModel::model()->byRewriteName($value)->find();
		}

		if (!$this->city) {
			$this->errors['city'] = 'City not found';
		}

		return $this;
	}

	/**
	 * Установка района для поиска
	 *
	 * @param mixed $value
	 *
	 * @return $this
	 */
	public function setDistrict($value)
	{
		if ($value instanceof DistrictModel) {
			$this->district = $value;
		} else {
			$model = DistrictModel::model()->inCity($this->city->id_city);

			if (is_numeric($value)) {
				$this->district = $model->findByPk($value);
			} else {
				$this->district = $model->searchByAlias($value)->find();
			}
		}

		if (!$this->district) {
			$this->errors['district'] = 'District not found';
		}

		return $this;
	}

	/**
	 * Установка улицы для поиска
	 *
	 * @param mixed $value
	 *
	 * @return $this
	 */
	public function setStreet($value)
	{
		if ($value instanceof StreetModel) {
			$this->street = $value;
		} else {
			$model = StreetModel::model()->inCity($this->city->id_city);

			if (is_numeric($value)) {
				$this->street = $model->findByPk($value);
			} else {
				$this->street = $model->searchByAlias($value)->find();
			}
		}

		if (!$this->street) {
			$this->errors['street'] = 'Street not found';
		}

		return $this;
	}

	/**
	 * Установка города Подмосковья для поиска
	 *
	 * @param mixed $value
	 *
	 * @return $this
	 */
	public function setRegCity($value)
	{
		if ($value instanceof RegCityModel) {
			$this->regCity = $value;
		} else {
			$model = RegCityModel::model()->inCity($this->city->id_city);

			if (is_numeric($value)) {
				$this->regCity = $model->findByPk($value);
			} else {
				$this->regCity = $model->searchByAlias($value)->find();
			}
		}

		if ($this->regCity) {
			$this->stationIds = $this->regCity->getStationIds();
		} else {
			$this->errors['regCity'] = 'RegCity not found';
		}

		return $this;
	}

	/**
	 * Установка области для поиска
	 *
	 * @param mixed $value
	 *
	 * @return $this
	 */
	public function setArea($value)
	{
		if ($value instanceof AreaModel) {
			$this->area = $value;
		}
		elseif (is_numeric($value)) {
			$this->area = AreaModel::model()->findByPk($value);
		} else {
			$this->area = AreaModel::model()->searchByAlias($value)->find();
		}

		if ($this->area) {
			$this->stationIds = $this->area->getStationIds();
		} else {
			$this->errors['area'] = 'Area not found';
		}

		return $this;
	}

	/**
	 * Установка станции для поиска
	 *
	 * @param mixed $value
	 *
	 * @return $this
	 */
	public function setStation($value)
	{
		if ($value instanceof StationModel) {
			$this->station = $value;
		} else {
			$model = StationModel::model()->inCity($this->city->id_city);

			if (is_numeric($value)) {
				$this->station = $model->findByPk($value);
			} else {
				$this->station = $model->searchByAlias($value)->find();
			}
		}

		if (!$this->station) {
			$this->errors['station'] = 'Station not found';
		}

		return $this;
	}

	/**
	 * Установка нескольких станций для поиска
	 *
	 * @param mixed $value
	 *
	 * @return $this
	 */
	public function setStationIds($value)
	{
		$stationsIds = is_array($value) ? $value : explode(',', $value);

		foreach ($stationsIds as $v) {
			$id = intval($v);
			if ($id > 0) {
				$this->stationIds[$id] = $id;
			}
		}

		return $this;
	}

	/**
	 * Установка типа клиники для поиска
	 *
	 * @param mixed $value
	 *
	 * @return $this
	 */
	public function setClinicType($value)
	{
		$types = explode(',', $value);

		if (in_array(ClinicModel::TYPE_CLINIC, $types)) {
			$this->clinicTypes['isClinic'] = 'yes';
		}
		if (in_array(ClinicModel::TYPE_DIAGNOSTIC, $types)) {
			$this->clinicTypes['isDiagnostic'] = 'yes';
		}
		if (in_array(ClinicModel::TYPE_DOCTOR, $types)) {
			$this->clinicTypes['isDoctor'] = 'yes';
		}

		return $this;
	}

	/**
	 * Установка партнёра
	 *
	 * @param mixed $value
	 *
	 * @return $this
	 */
	public function setPartner($value)
	{
		if ($value instanceof PartnerModel) {
			$this->partner = $value;
		} else {
			$this->partner = PartnerModel::model()->findByPk($value);
		}

		if (!$this->partner) {
			$this->errors['partner'] = 'Partner not found';
		}

		return $this;
	}


	/**
	 * Инициализация
	 */
	protected function init()
	{
		$this->city = \Yii::app()->city->getCity();
	}

	/**
	 * Формирование параметров для поиска
	 *
	 * @return $this
	 */
	public function buildParams()
	{
		$p = $this->params;

		if (!empty($p['clinicId'])) {
			$this->setClinicId($p['clinicId']);
		}

		if (!empty($p['speciality'])) {
			$this->setSpeciality($p['speciality']);
		}

		if (!empty($p['diagnostic'])) {
			$this->setDiagnostic($p['diagnostic']);
		}

		if (!empty($p['city'])) {
			$this->setCity($p['city']);
		}

		if ($this->city) {
			if (!empty($p['district'])) {
				$this->setDistrict($p['district']);
			}

			if (!empty($p['street'])) {
				$this->setStreet($p['street']);
			}

			if (!empty($p['regCity'])) {
				$this->setRegCity($p['regCity']);
			}

			if (!empty($p['area'])) {
				$this->setArea($p['area']);
			}

			if (!empty($p['station'])) {
				$this->setStation($p['station']);
			}
		}

		if (!empty($p['stations'])) {
			$this->setStationIds($p['stations']);
		}

		if (!empty($p['partnerId'])) {
			$this->setPartner($p['partnerId']);
		}

		$this->clinicTypes = [
			'isDiagnostic' => isset($p['isDiagnostic']) ? $p['isDiagnostic'] : null,
			'isClinic' => isset($p['isClinic']) ? $p['isClinic'] : null,
			'isDoctor' => isset($p['isDoctor']) ? $p['isDoctor'] : null,
		];

		if (!empty($p['clinicType'])) {
			$this->setClinicType($p['clinicType']);
		}

		$near = $this->getParam('near');
		if ($near === 'closest' || $near === 'mixed') {
			$this->sortDefault = null;
		}

		return parent::buildParams();
	}

	/**
	 * Формирование scopes для запроса данных
	 *
	 * @return $this
	 */
	protected function buildScopes()
	{
		if ($this->clinicIds) {
			$this->scopes['inClinics'] = [$this->clinicIds];
		}

		if ($this->city) {
			$this->scopes['inCity'] = [$this->city->id_city];
		}

		if ($this->speciality) {
			$this->scopes['searchBySpecialities'] = [[$this->speciality->id]];
		}

		if ($this->diagnostic) {
			$this->scopes['searchByDiagnostics'] = [[$this->diagnostic->id], false];
		}

		if ($this->district) {
			$this->scopes['inDistrict'] = [$this->district->id];
		}

		if ($this->street) {
			$this->scopes['inStreet'] = [$this->street->street_id];
		}

		if ($this->station) {
			$this->scopes['searchByStations'] = [[$this->station->id]];
		}
		elseif ($this->stationIds !== null) {
			$this->scopes['searchByStations'] = [$this->stationIds ?: [0], $this->getParam('near')];
		}

		if ($this->clinicTypes) {
			$this->scopes['searchByClinicType'] = [$this->clinicTypes];
		}

		if ($this->getParam('withDoctors')) {
			$this->scopes['withDoctors'] = [];
		}

		if ($this->getParam('open_4_yandex')) {
			$this->scopes['openForYandex'] = [$this->getParam('open_4_yandex')];
		}

		if ($this->getParam('selectPrice')) {
			$this->scopes['selectPriceMinMax'] = [$this->getParam('checkUseSpecialPriceForPartner') ? $this->partner : null];
		}

		$this->scopes['sort'] = $this->sort ? [$this->sort, $this->sortDirection] : ['default', $this->sortDirection];

		return parent::buildScopes();
	}

	/**
	 * Загрузка данных
	 *
	 * @return $this
	 */
	public function loadData()
	{
		$this->buildScopes();

		$this->count = intval($this->getModel()->count([
			'distinct' => true,
			'together' => true,
			'scopes' => $this->scopes,
		]));

		$this->items = $this->findClinics($this->scopes, $this->limit, $this->offset);

		if ($this->page == 1 && $this->count < $this->limit) {
			if ($this->getParam('withNearest')) {
				$this->addItems($this->findNearestClinics($this->limit - $this->count));
			}
		}

		return $this;
	}

	/**
	 * Найти ближайшие клиники
	 *
	 * @param int $limit
	 *
	 * @return ClinicModel[]
	 */
	public function findNearestClinics($limit)
	{
		if ($limit !== null && $limit < 1) {
			return [];
		}

		$scopes = $this->scopes;

		$scopes['except'] = [$this->getItemIds()];

		// Убираем параметры для поиска по любым гео-данным, выбираем по всему городу
		unset($scopes['inClinics']);
		unset($scopes['searchByStations']);
		unset($scopes['inDistrict']);
		unset($scopes['inDistricts']);
		unset($scopes['inStreet']);
		unset($scopes['inNearestStreet']);

		$stationIds = $this->stationIds ?: ($this->station ? [$this->station->id] : null);
		$nearestStationIds = null;

		if ($this->speciality && $stationIds) {
			$nearestStationIds = StationModel::model()->getNearestStationIds($stationIds, 20);
		}

		if ($nearestStationIds) {
			$scopes['searchByStations'] = [$nearestStationIds];
		}
		elseif ($this->district) {
			$scopes['inDistricts'] = [$this->district->getNeighborDistrictIds()];
		}
		elseif ($this->street) {
			$scopes['inNearestStreet'] = [$this->street->street_id];
		}
		else {
			return [];
		}

		return $this->findClinics($scopes, $limit);
	}

	/**
	 * Найти лучшие клиники
	 *
	 * @param int $limit
	 *
	 * @return ClinicModel[]
	 */
	public function findBestClinics($limit)
	{
		if ($limit !== null && $limit < 1) {
			return [];
		}

		$scopes = $this->scopes;

		$scopes['except'] = [$this->getItemIds()];
		$scopes['sort'] = ['rating', 'desc'];

		// Убираем параметры для поиска по любым гео-данным, выбираем по всему городу
		unset($scopes['inClinics']);
		unset($scopes['searchByStations']);
		unset($scopes['inDistrict']);
		unset($scopes['inDistricts']);
		unset($scopes['inStreet']);
		unset($scopes['inNearestStreet']);

		return $this->findClinics($scopes, $limit);
	}

	/**
	 * Выборка клиник
	 *
	 * @param array $scopes
	 * @param int   $limit
	 * @param int | null  $offset
	 *
	 * @return ClinicModel[]
	 */
	protected function findClinics($scopes, $limit, $offset = null)
	{
		$with = [];

		if ($this->partner) {
			$with['partnerPhones'] = [
				'scopes' => [
					'byPartnerId' => $this->partner->id,
				],
				'with' => 'phone',
			];
		}

		return $this->getModel()->findAll([
			'select' => 't.*',
			'together' => true,
			'group' => 't.id',
			'with' => $with,
			'scopes' => $scopes,
			'limit' => $limit,
			'offset' => $offset,
		]);
	}

	/**
	 * Модель клиники
	 *
	 * @return ClinicModel
	 */
	protected function getModel()
	{
		$model = ClinicModel::model();

		if ($this->cacheDuration !== null) {
			$model->cache($this->cacheDuration);
		}

		return $model;
	}

	/**
	 * Параметры из которых формируется ссылка
	 *
	 * @return array
	 */
	public function getUrlParams()
	{
		return [
			'spec' => $this->speciality ? $this->speciality->rewrite_spec_name : null,
			'area' => $this->area ? $this->area->rewrite_name : null,
			'district' => $this->district ? $this->district->rewrite_name : null,
			'city' => $this->regCity ? $this->regCity->rewrite_name : null,
			'street' => $this->street ? $this->street->rewrite_name : null,
			'station' => $this->station ? $this->station->rewrite_name : null,
			'order' => $this->sort,
			'direction' => $this->sortDirection,
			'page' => null,
		];
	}

	/**
	 * Сформировать ссылку
	 *
	 * @param array $params
	 *
	 * @return string
	 */
	public function createUrl(array $params = [])
	{
		$current = $this->getUrlParams();

		$params = array_replace($current, $params);

		$link = '/clinic';

		if ($params['spec'] && $params['station']) {
			$params['spec'] .= '/' . $params['station'];
			$params['station'] = null;
		}
		elseif ($params['area'] && $params['district']) {
			$params['area'] .= '/' . $params['district'];
			$params['district'] = null;
		}

		foreach ($params as $k => $v) {
			if ($v) {
				$link .= "/$k/$v";
			}
		}

		return $link;
	}
}
