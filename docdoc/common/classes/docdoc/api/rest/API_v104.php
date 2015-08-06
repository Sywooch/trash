<?php
namespace dfs\docdoc\api\rest;

use dfs\docdoc\models\AreaModel;
use dfs\docdoc\models\DistrictModel;
use dfs\docdoc\models\PartnerModel;
use Yii;
use dfs\docdoc\components\DocDocStat;
use	dfs\docdoc\models\CityModel;
use	dfs\docdoc\models\StationModel;
use	dfs\docdoc\models\SectorModel;
use	dfs\docdoc\models\DiagnosticaModel;
use dfs\docdoc\models\DoctorModel;

/**
 * Description of api_v3
 *
 */
class API_v104 extends API_v103
{
	/**
	 * Получение методов
	 *
	 * @return array
	 */
	public function getMethods() {
		$newMethods = [
			'stat'              => 'statView',
			'doctor/by/alias'   => 'doctorByAlias',
			'area'              => 'areaList',
			'district'          => 'districtList',
		];

		return array_merge($newMethods, parent::getMethods());
	}

	/**
	 * Статистика по заявкам, клиникам и врачам
	 *
	 * @return array
	 */
	function statView()
	{
		$stat = new DocDocStat(Yii::app()->params['DocDocStatisticFactor']);

		return [
			'Requests' => $stat->getRequestsCount(),
			'Doctors'  => $stat->getDoctorsCount(),
			'Reviews'  => $stat->getReviewsCount()
		];
	}

	/**
	 * Получение списка городов
	 *
	 * @return array
	 */
	protected function cityList()
	{
		$data = array();

		$cities = CityModel::model()->active()->findAll();

		foreach ($cities as $city) {
			$data[] = [
				'Id'    => $city->id_city,
				'Name'  => $city->title,
				'Alias' => $city->rewrite_name,
				'Phone' => $city->site_phone->getNumber(),
			];
		}

		return array('CityList' => $data);
	}

	/**
	 * маппинг свойств записи для станции в json
	 *
	 * @param StationModel $station
	 *
	 * @return array
	 */
	protected function stationsMapping(StationModel $station)
	{
		$properties = parent::stationsMapping($station);
		$properties['Alias'] = $station->rewrite_name;

		return $properties;
	}

	/**
	 * Маппинг свойств диагностики в JSON
	 *
	 * @param DiagnosticaModel $diagnostic
	 *
	 * @return array
	 */
	protected function diagnosticMapping(DiagnosticaModel $diagnostic)
	{
		$properties = parent::diagnosticMapping($diagnostic);
		$properties['Alias'] = trim($diagnostic->rewrite_name, "/");

		return $properties;
	}

	/**
	 * Маппинг свойт модели sector на json
	 *
	 * @param SectorModel $sector
	 *
	 * @return array
	 */
	protected function specialityMapping($sector)
	{
		$properties = parent::specialityMapping($sector);
		$properties['Alias'] = $sector->rewrite_name;

		return $properties;
	}

	/**
	 * маппинг доктора в json
	 *
	 * @param DoctorModel $row
	 *
	 * @return array
	 */
	protected function doctorMapping($row)
	{
		$properties = parent::doctorMapping($row);
		$properties['Alias'] = $row->rewrite_name;

		$spec = [];
		foreach ($row->sectors as $sector) {
			$spec[] = $this->specialityMapping($sector);
		}
		$properties['Specialities'] = $spec;

		$clinics = $row->getActiveClinics();

		$stations = [];
		if ($clinics) {
			foreach ($clinics[0]->stations as $station) {
				$stations[] = $this->stationsMapping($station);
			}
		}
		$properties['Stations'] = $stations;

		return $properties;
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
		$row = $row[1];
		$properties = static::doctorMapping($row);
		unset($properties['TextEducation']);
		unset($properties['TextAssociation']);
		unset($properties['TextDegree']);
		unset($properties['TextSpec']);
		unset($properties['TextCourse']);
		unset($properties['TextExperience']);
		$properties['TextAbout'] = self::clearText($row->text, true);
		$properties['InternalRating'] = self::clearText($row->rating_internal, true);
		$properties['OpinionCount'] = $row->getOpinionCount();
		return $properties;
	}

	/**
	 * Получение списка врачей и их параметров
	 *
	 * @return array
	 */
	protected function doctorList()
	{
		$params = $this->params;
		$type = '';

		if (isset($params['near'])) {
			switch ($params['near']) {
				case 'strict':
					$params['nearest'] = false;
					$params['best'] = false;
					break;
				case 'mixed':
					$type = 'nearest';
					$params['nearest'] = false;
					break;
				case 'extra':
					$params['nearest'] = true;
					$params['best'] = true;
					break;
			}
		}

		if (!empty($params['stations'])) {
			$params['stations'] = explode(',', $params['stations']);
		} else {
			unset($params['stations']);
		}

		if (isset($params['typeSearch']) && $params['typeSearch'] == 'landing') {
			$params['clinics'] = Yii::app()->getParams()['clinicsForLanding'];
		}

		if (isset($params['order'])) {
			$orderType = str_replace('-', '', $params['order']);
			switch ($orderType) {
				case 'rating':
					$orderField = 'sort_rating';
					break;
				case 'name':
				case 'price':
				case 'rating_internal':
					$orderField = 't.' . $orderType;
					break;
				case 'experience':
					$orderField = 'experience';
					break;
				case 'distance':
					$orderField = 'distance';
					break;
				default:
					$orderField = 't.rating_internal';
					break;
			}
			$params['order'] = $orderField . ' ' . (strpos($params['order'], '-') === 0 ? 'desc' : 'asc');
		}

		$params['count'] = $this->getCountDoctorsWithLimit($params);

		$this->params = $params;

		$items = DoctorModel::model()->findItems($params, $type);

		$doctors = [];
		foreach ($items as $item) {
			$doctors[] = $this->doctorListMapping($item);
		}

		$count = intval(DoctorModel::model()->searchItemsParams($params, $type, [], true)->count());
		if ($count < count($items)) {
			$count = count($items);
		}

		return [
			'Total'         => $count,
			'DoctorList'    => $doctors,
		];
	}

	/**
	 * Маппинг параметров для заявки
	 *
	 * @return array
	 * @throws \Exception
	 */
	protected function requestCreate()
	{
		//нельзя передавать врача, не передав клинику
		//так как врач может работать в нескольких клиниках, нам нужно знать в какую именно его записывать
		$doctor = isset($this->params['data']->doctor) ? $this->params['data']->doctor : null;
		$clinic = isset($this->params['data']->clinic) ? $this->params['data']->clinic : null;
		if (!is_null($doctor) && is_null($clinic)) {
			return ["Response" => $this->getError("Не передана клиника")];
		}

		return parent::requestCreate();
	}

	/**
	 * Поиск доктора по альясу
	 *
	 * @return array
	 */
	protected function doctorByAlias()
	{
		$params = $this->params;

		$alias = $params['alias'];

		$data = [];

		$doctor = DoctorModel::model()
			->withoutAnother()
			->byRewriteName($alias)
			->find();

		if ($doctor) {
			$data[0] = $this->doctorMapping($doctor);
		}

		return array('Doctor' => $data);
	}

	/**
	 * Получение округов Москвы
	 *
	 * @return array
	 */
	protected function areaList()
	{
		$data = [];

		$areas = AreaModel::model()->findAll();
		foreach ($areas as $area) {
			$data[] = $this->areaMapping($area);
		}

		return ['AreaList' => $data];
	}

	/**
	 * Маппинг данных об округе
	 *
	 * @param AreaModel $area
	 * @return array
	 */
	protected function areaMapping(AreaModel $area)
	{
		$attr = [
			'Id'        => $area->id,
			'Alias'     => $area->rewrite_name,
			'Name'      => $area->name,
			'FullName'  => $area->full_name,
		];
		return $attr;
	}

	/**
	 * Получение районов
	 *
	 * @return array
	 */
	protected function districtList()
	{
		$params = $this->params;
		$data = [];

		$districts = DistrictModel::model();
		if (!empty($params['city'])) {
			$districts->inCity($params['city']);
			if (!empty($params['area'])) {
				$districts->inArea($params['area']);
			}
		}
		$districts = $districts->findAll();
		foreach ($districts as $district) {
			$data[] = $this->districtMapping($district);
		}

		return ['DistrictList' => $data];
	}

	/**
	 * Маппинг данных о районе
	 *
	 * @param DistrictModel $district
	 * @return array
	 */
	protected function districtMapping(DistrictModel $district)
	{
		$attr = [
			'Id'        => $district->id,
			'Alias'     => $district->rewrite_name,
			'Name'      => $district->name,
		];
		if (!is_null($district->area)) {
			$attr['Area'] = $this->areaMapping($district->area);
		}

		return $attr;
	}

	/**
	 * Получение списка клиник
	 *
	 * @return array
	 */
	protected function clinicList()
	{
		$params = $this->params;

		$params['selectPrice'] = true;
		$params['selectSpecialities'] = true;

		return $this->_clinicList($params);
	}
}
