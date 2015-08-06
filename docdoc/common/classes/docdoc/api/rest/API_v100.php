<?php

namespace dfs\docdoc\api\rest;

use dfs\docdoc\api\BaseAPI;
use	dfs\docdoc\models\CityModel;
use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\models\DiagnosticClinicModel;
use dfs\docdoc\models\PartnerModel;
use dfs\docdoc\models\RequestModel;
use	dfs\docdoc\models\SectorModel;
use	dfs\docdoc\models\DoctorModel;
use dfs\docdoc\models\DiagnosticaModel;
use dfs\docdoc\models\StationModel;
use dfs\docdoc\listInterface\ClinicList;

// DocDoc REST API v1.0

class API_v100 extends BaseAPI
{
	const LIMIT_DOCTORS = 500;
	const LIMIT_CLINICS = 500;


	public $log = 'rest_api.log';

	/** @var PartnerModel */
	protected $_partner = null;

	public function __construct(array $params = [])
	{
		parent::__construct($params);

		if (isset($this->params['partnerId'])) {
			$this->_partner = PartnerModel::model()->findByPk($this->params['partnerId']);
		}
	}
	/**
	 * Запуск методов API
	 * @return string
	 */
	public function run()
	{
		header("Content-type: text/json; charset=utf-8");
		return parent::run();
	}

	/**
	 * Получение методов
	 *
	 * @return array
	 */
	public function getMethods() {
		$methods = parent::getMethods();
		$newMethods = [
			'city'          => 'cityList',
			'speciality'    => 'specialityList',
			'diagnostic'    => 'diagnosticList',
			'metro'         => 'metroList',
			'doctor/list'   => 'doctorList',
			'clinic/list'   => 'clinicList',
			'doctor/count'  => 'doctorCount',
			'clinic/count'  => 'clinicCount',
			'doctor'        => 'doctorView',
			'clinic'        => 'clinicView',
			'review'        => 'reviewList',
			'request'       => 'requestCreate',
			'review/create' => 'reviewCreate',
		];

		return array_merge($newMethods, $methods);
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

		foreach ($cities as $i => $city) {
			$data[$i]['Id'] = $city->id_city;
			$data[$i]['Name'] = $city->title;
		}

		return array('CityList' => $data);
	}

	/**
	 * Получает список станций метро
	 *
	 * @return string[]
	 */
	protected function metroList()
	{
		$params = $this->params;
		$stations = array();

		if (!empty($params['city'])) {
			$stations = StationModel::model()
				->inCity($params['city'])
				->with('undergroundLine')
				->findAll();
		}

		$data = array();
		foreach ($stations as $station) {
			$data[] = $this->stationsMapping($station);
		}
		return array('MetroList' => $data);
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
		$line = $station->undergroundLine;
		return [
			'Id'        => $station->id,
			'Name'      => $station->name,
			'LineName'  => $line ? $line->name : '',
			'LineColor' => $line ? $line->color : '',
			'CityId'    => $line ? $line->city_id : '',
		];
	}

	/**
	 * маппинг станций, полученный старым методом
	 * back/public/lib/php/models/doctor.class.php -> getStationForDoctorById()
	 *
	 * @param array $stations
	 *
	 * @return array
	 */
	protected function stationsMappingOld(array $stations)
	{
		$st = [];

		foreach ($stations as $s) {
			$st[] = [
				'doctor_id' => $s['doctor_id'],
				'Id'        => $s['Id'],
				'Name'      => $s['Name'],
				'Alias'     => $s['Alias'],
				'LineId'    => $s['LineId'],
				'LineName'  => $s['LineName'],
				'LineColor' => $s['LineColor'],
				'CityId'    => $s['CityId'],
			];
		}

		return $st;
	}


	/**
	 * Получение списка специальностей
	 */
	protected function specialityList()
	{
		$sectors = SectorModel::model()
			->active()
			->simple()
			->ordered()
			->cache(3600)
			->findAll();
		$data = [];
		foreach ($sectors as $sector) {
			$data[] = $this->specialityMapping($sector);
		}
		return array('SpecList' => $data);
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
		return [
			'Id' => $sector->id,
			'Name' => $sector->name,
		];
	}

	// Получение списка врачей
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

		$data = array();
		$count = 0;

		$params['count'] = $this->getCountDoctorsWithLimit($params);

		$doctors = \Doctor::getItems($params);
		$k = 0;
		foreach ($doctors as $item) {
			$data[$k]['Id'] = $item['Id'];
			$data[$k]['Name'] = $item['Name'];
			if (!empty($item['Alias'])) {
				$data[$k]['Alias'] = $item['Alias'];
			}
			if (!empty($item['rating'])) {
				$data[$k]['Rating'] = $item['ManualRating'];
			} else {
				$data[$k]['Rating'] = $item['TotalRating'];
			}

			$data[$k]['InternalRating'] = $item['RatingInternal'];
			$data[$k]['Price'] = $item['Price'];
			$data[$k]['SpecialPrice'] = $this->_partner && $this->_partner->use_special_price ? $item['SpecialPrice'] : null;
			$data[$k]['Sex'] = ($item['Sex'] == 2) ? 1 : 0;
			$data[$k]['Img'] = "http://docdoc.ru/img/doctorsNew/" . $item['SmallImg'];
			$data[$k]['OpinionCount'] = $this->reviewCountByDoctorId($item['Id']);
			$data[$k]['TextAbout'] = checkField($item['Description'], "t", '');
			if (!empty($item['ExperienceYear'])) {
				$data[$k]['ExperienceYear'] = date('Y') - $item['ExperienceYear'];
			} else {
				$data[$k]['ExperienceYear'] = 0;
			}
			$data[$k]['Departure'] = $item['Departure'];
			$data[$k]['Category'] = $item['category'];
			$data[$k]['Degree'] = $item['degree'];
			$data[$k]['Rank'] = $item['rank'];
			$data[$k]['Specialities'] = $item['Specialities'];
			$data[$k]['Stations'] = $this->stationsMappingOld($item['Stations']);

			$count = \Doctor::getCount($params);

			$k++;
		}

		return array('Total' => $count, 'DoctorList' => $data);
	}

	// Получение количества врачей
	protected function doctorCount()
	{
		$params = $this->params;

		$count = \Doctor::getCount($params);
		$total = \Doctor::getCount(array('city' => $params['city']));

		return array('Total' => $total, 'DoctorSelected' => $count);
	}

	/**
	 * Получение информации о враче
	 *
	 * @return array
	 */
	protected function doctorView()
	{
		$params = $this->params;
		$data = array();

		$id = intval($params['id']);

		if ($id > 0) {
			$doctor = DoctorModel::model()->withoutAnother()->findByPk($id);
			if ($doctor) {
				$data[0] = $this->doctorMapping($doctor);
			}
		}

		return array('Doctor' => $data);
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
		$data = array();

		$data['Id'] = $row->id;
		$data['Name'] = $row->name;
		$data['Rating'] = !empty($row->rating) ? $row->rating : $row->total_rating;
		$data['Sex'] = ($row->sex == 2) ? 1 : 0;
		$data['Img'] = "http://docdoc.ru/img/doctorsNew/" . $row->id . "_small.jpg";
		$data['AddPhoneNumber'] = (string)$row->addNumber;
		$data['Category'] = $row->getCategory();
		$data['Degree'] = $row->getDegree();
		$data['Rank'] = $row->getRank();

		$data['Description'] = self::clearText($row->text);
		$education = $row->getEducation();
		$educationText = '';
		if (!empty($education)) {
			$educationText = "<ul><li>" . implode('</li><li>', $education) . "</li></ul>";
		}

		$data['TextEducation'] = $educationText ?: self::clearText($row->text_education);
		$data['TextAssociation'] = self::clearText($row->text_association);
		$data['TextDegree'] = self::clearText($row->text_degree);
		$data['TextSpec'] = self::clearText($row->text_spec);
		$data['TextCourse'] = self::clearText($row->text_course);
		$data['TextExperience'] = self::clearText($row->text_experience);

		$data['ExperienceYear'] = $row->experience_year ? date('Y') - $row->experience_year : 0;

		$data['Price'] = (int)$row->price;
		$data['SpecialPrice'] = $this->_partner && $this->_partner->use_special_price ? (int)$row->special_price : 0;
		$data['Departure'] = (int)$row->departure;

		return $data;
	}

	// Получение списка отзывов
	protected function reviewList()
	{
		$params = $this->params;
		$data = array();
		$sqlAdd = " t1.allowed = 1 "; // Только активные

		if (isset($params['doctor']) && !empty ($params['doctor'])) {
			$sqlAdd .= " AND t1.doctor_id = " . intval($params['doctor']) . " ";

			$sql = "SELECT
                        t1.id, t1.doctor_id, t1.request_id,
                        t1.name as client, t1.phone, t1.age,
                        t1.rating_qualification, t1.rating_attention, t1.rating_room, t1.rating_color,
                        t1.allowed, t1.lk_status, t1.is_fake, t1.author,
                        DATE_FORMAT( t1.created,'%d.%m.%Y') AS crDate,
                        t1.date_publication AS pubDate,
                        t1.text,
                        t1.status, t1.origin,
                        t2.name as doctor
                    FROM doctor_opinion  t1
                    LEFT JOIN doctor t2  ON (t2.id = t1.doctor_id)
                    LEFT JOIN clinic t3  ON (t3.id = t2.clinic_id)
                    WHERE " . $sqlAdd . "
                    ORDER BY t1.created DESC, t1.id";
			//echo $sql;

			$result = query($sql);
			if (num_rows($result) > 0) {
				$k = 0;
				while ($row = fetch_object($result)) {
					$data[$k]['Id'] = $row->id;
					$data[$k]['Client'] = checkField($row->client, "t", '');
					$data[$k]['RatingQlf'] = $row->rating_qualification;
					$data[$k]['RatingAtt'] = $row->rating_attention;
					$data[$k]['RatingRoom'] = $row->rating_room;
					$data[$k]['Text'] = checkField($row->text, "t", '');
					$data[$k]['Date'] = $row->crDate;
					$data[$k]['DoctorId'] = $row->doctor_id;
					$k++;
				}
			}
		}
		return array('ReviewList' => $data);
	}

	/**
	 * Получение списка процедур
	 * @return array
	 */
	protected function diagnosticList()
	{
		$diagnostics = DiagnosticaModel::model()
			->onlyParents()
			->with('childs')
			->findAll(['order' => 't.id']);

		$data = array();
		$k = 0;
		foreach ($diagnostics as $diagnostic) {
			$data[$k] = $this->diagnosticMapping($diagnostic);

			if (count($diagnostic->childs)) {
				$j = 0;
				foreach ($diagnostic->childs as $subDiag) {
					$data[$k]['SubDiagnosticList'][$j] = $this->diagnosticMapping($subDiag);
					$j++;
				}
			} else {
				$data[$k]['SubDiagnosticList'] = array();
			}
			$k++;
		}

		return array('DiagnosticList' => $data);
	}

	/**
	 * Маппинг свойств диагностики в JSON
	 *
	 * @param DiagnosticaModel $diagnostic
	 *
	 * @return array
	 */
	protected function diagnosticMapping(DiagnosticaModel $diagnostic) {

		$data = [];
		$data['Id'] = $diagnostic->id;
		$data['Name'] = $diagnostic->name;


		return $data;
	}

	// Получение списка диагностических центров
	protected function clinicList()
	{
		return $this->_clinicList($this->params);
	}

	/**
	 * @param array $params
	 *
	 * @return array
	 */
	protected function _clinicList($params)
	{
		$clinicList = new ClinicList();

		if ($this->_partner) {
			$params['checkUseSpecialPriceForPartner'] = true;
		}

		$clinicList
			->setCache(3600)
			->setLimit($clinicList->getMaxLimit())
			->setParams($params)
			->buildParams();

		if (!$clinicList->hasErrors()) {
			$clinicList->loadData();
		}

		$data = [];

		foreach ($clinicList->getItems() as $clinic) {
			$data[] = $this->clinicMapping($clinic, $params);
		}

		$count = $clinicList->getCount();

		return array('Total' => $count, 'ClinicList' => $data);
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
		$item = $this->clinicStruct($clinic, $params);

		$item['Longitude'] = $clinic->latitude;
		$item['Latitude'] = $clinic->longitude;

		unset($item['StreetId']);
		unset($item['DistrictId']);

		return $item;
	}

	/**
	 * Структура клиник
	 *
	 * @param ClinicModel $clinic
	 * @param array $params
	 *
	 * @return array
	 */
	protected function clinicStruct($clinic, $params)
	{
		$partnerPhone = isset($clinic->partnerPhones[0]) ? $clinic->partnerPhones[0] : null;

		$item = [
			'Id' => (int) $clinic->id,
			'Name' => $clinic->name,
			'ShortName' => $clinic->short_name,
			'RewriteName' => $clinic->rewrite_name,
			'URL' => $clinic->url,
			'Longitude' => $clinic->longitude,
			'Latitude' => $clinic->latitude,
			'City' => $clinic->city,
			'Street' => $clinic->street,
			'StreetId' => $clinic->street_id,
			'House' => $clinic->house,
			'Description' => $clinic->description,
			'WeekdaysOpen' => $clinic->weekdays_open,
			'WeekendOpen' => $clinic->weekend_open,
			'ShortDescription' => $clinic->shortDescription,
			'IsDiagnostic' => $clinic->isDiagnostic,
			'isClinic' => $clinic->isClinic,
			'IsDoctor' => $clinic->isPrivatDoctor,
			'Phone' => $clinic->asterisk_phone ? $clinic->phone : null,
			'PhoneAppointment' => $clinic->phone_appointment,
			'logoPath' => $clinic->logoPath,
			'ScheduleState' => $clinic->schedule_state,
			'DistrictId' => $clinic->district_id,
			'Email' => $clinic->email,
		];

		if (isset($params['partnerId'])) {
			$item['ReplacementPhone'] = $partnerPhone && $partnerPhone->phone ? $partnerPhone->phone->number : null;
		}

		if (isset($params['selectPrice'])) {
			$item['MinPrice'] = $clinic->minPrice;
			$item['MaxPrice'] = $clinic->maxPrice;
		}

		$item['Logo'] = 'http://docdoc.ru/upload/kliniki/logo/' . $clinic->logoPath;

		if ($clinic->isDiagnostic == 'yes') {
			$useSpecialPrice = $this->_partner && $this->_partner->use_special_price;
			$diagnostics = DiagnosticClinicModel::model()->findAllForClinic($item['Id']);
			$data = [];
			foreach ($diagnostics as $d) {
				$data[] = [
					'Id' => $d['id'],
					'Name' => ($d['parentName'] ? $d['parentName'] . ' ' : '') . $d['name'],
					'Price' => $d['price'],
					'SpecialPrice' => $useSpecialPrice ? $d['special_price'] : 0,
				];
			}
			$item['Diagnostics'] = $data;
		}

		$stations = StationModel::model()->findAllForClinic($item['Id']);
		$data = [];
		foreach ($stations as $s) {
			$data[] = [
				'Id' => $s['id'],
				'Name' => $s['name'],
				'LineName' => $s['lineName'],
				'LineColor' => $s['lineColor'],
				'CityId' => $s['cityId'],
			];
		}
		$item['Stations'] = $data;

		if (isset($params['selectSpecialities'])) {
			$specialities = SectorModel::model()->findAllForClinic($item['Id']);
			$data = [];
			foreach ($specialities as $speciality) {
				$data[] = [
					'Id' => $speciality->id,
					'Name' => $speciality->name,
					'Alias' => $speciality->rewrite_name,
				];
			}
			$item['Specialities'] = $data;
		}

		return $item;
	}

	// Получение кол-ва клиник
	protected function clinicCount()
	{
		$params = $this->params;

		$count = \Clinic::getCount($params);
		$total = \Clinic::getCount(array('city' => $params['city']));

		return array('Total' => $total, 'ClinicSelected' => $count);
	}

	protected function clinicView()
	{
		$params = $this->params;
		$data = array();

		$id = intval($params['id']);
		$clinic = new \Clinic($id);

		$clinic = $clinic->data;
		if (!empty($clinic)) {
			if ($clinic['isDiagnostic'] == 'yes') {
				$data['Id'] = $clinic['id'];
				$data['Name'] = $clinic['name'];
				$data['ShortName'] = $clinic['short_name'];
				$data['RewriteName'] = $clinic['rewrite_name'];
				$data['Url'] = $clinic['url'];

				/*
				 * широта и долгота были перепутаны местами в БД
				 * чтобы была совместимость API меняем их местами
				 */
				$data['Longitude'] = $clinic['latitude'];
				$data['Latitude'] = $clinic['longitude'];

				$data['City'] = $clinic['city'];
				$data['Street'] = $clinic['street'];
				$data['Description'] = $clinic['description'];
				$data['House'] = $clinic['house'];
				$data['WeekdaysOpen'] = $clinic['weekdays_open'];
				$data['WeekendOpen'] = $clinic['weekend_open'];
				if (!empty($clinic['asterisk_phone'])) {
					$data['Phone'] = '+' . $clinic['asterisk_phone'];
				} else {
					$data['Phone'] = '+' . $clinic['phone'];
				}
				$data['Logo'] = "http://docdoc.ru/upload/kliniki/logo/" . $clinic['logoPath'];

				if(!$this->_partner || ($this->_partner && !$this->_partner->use_special_price)){
					foreach($clinic['Diagnostics'] as &$d){
						$d['SpecialPrice'] = 0;
					}
				}

				$data['Diagnostics'] = $clinic['Diagnostics'];
			}
		}

		return array('Clinic' => array($data));
	}

	/**
	 * Ко-во отзывов для конкретного врача
	 *
	 * @param $id
	 *
	 * @return string
	 */
	protected function reviewCountByDoctorId($id)
	{
		return DoctorModel::model()
			->findByPk($id)
			->getOpinionCount();
	}

	protected function educationListByDoctorId($id)
	{
		$resultArr = array();

		$id = intval($id);

		$sql = "SELECT
                        t1.education_id as id, t1.year,
                        t2.title, t2.type
                FROM education_4_doctor t1, education_dict t2
                WHERE
                        t1.education_id = t2.education_id
                        AND t1.doctor_id = " . $id . "
                ORDER BY t1.year";
		$result = query($sql);
		if (num_rows($result) > 0) {
			$k = 0;
			while ($row = fetch_object($result)) {
				$resultArr[$k]['Id'] = $row->id;
				$resultArr[$k]['Year'] = $row->year;
				$resultArr[$k]['Title'] = $row->title;
				$k++;
			}
		}

		return $resultArr;
	}

	protected function reviewCreate()
	{

		if (!empty ($this->params['data'])) {
			$request = $this->params['data'];
			$params['doctor'] = $request->doctorId;
			$params['client'] = $request->reviewer;
			$params['phone'] = $request->reviewPhone;
			$params['opinion'] = $request->review;
			$params['ratingQualification'] = $request->ratingProfessional;
			$params['ratingRoom'] = $request->ratingBedside;
			$params['ratingAttention'] = $request->ratingRatio;
			require_once dirname(__FILE__) . "/../../service/createOpinion.php";

			$data = createOpinion($params);
		} else {
			$data = $this->getError('Не получены данные об отзыве');
		}

		return $data;
	}

	/**
	 * Создани заявки
	 * @return array|bool|void
	 */
	protected function requestCreate()
	{
		if (empty($this->params['data'])) {
			$data = $this->getError('Не получены данные о заявке');
		} else {
			$transaction = \Yii::app()->getDb()->beginTransaction();

			try {
				$model = $this->_createRequest();
				$transaction->commit();
				$data['status'] = 'success';
				$data['message'] = 'Заявка принята';
			} catch (\Exception $e){
				$transaction->rollback();
				$data = $this->getError($e->getMessage());
			}
		}

		return ["Response" => $data];
	}

	/**
	 * Создание заявки
	 *
	 * @return RequestModel
	 * @throws \Exception
	 */
	protected function _createRequest()
	{
		$request = $this->params['data'];
		$model = new RequestModel();
		$model->client_name = isset($request->name) ? $request->name : null;
		$model->client_phone = isset($request->phone) ? $request->phone : null;
		$model->client_comments = isset($request->comment) ? $request->comment : null;
		$model->req_departure = isset($request->departure) ? $request->departure : null;
		$model->req_sector_id = isset($request->speciality) ? $request->speciality : null;
		$model->req_doctor_id = isset($request->doctor) ? $request->doctor : null;
		$model->clinic_id = isset($request->clinic) ? $request->clinic : null;
		$model->id_city = isset($request->city) ? $request->city : null;
		$model->age_selector = isset($request->age) ? $request->age : null;
		$model->partner_id  = $this->_partner->id;
		$model->source_type = RequestModel::SOURCE_PARTNER;
		$model->date_admission = !empty($request->dateAdmission) ? strtotime($request->dateAdmission) : null;

		$diagnosticsId = isset($request->diagnostics) ? $request->diagnostics : null;
		$subDiagnosticsId = isset($request->subdiagnostics) ? $request->subdiagnostics : null;
		$model->diagnostics_id = $subDiagnosticsId ?: $diagnosticsId;

		$slotId = isset( $request->slot) ?  $request->slot : null;

		$scenario = RequestModel::SCENARIO_PARTNER;
		if (!empty($model->diagnostics_id) && !empty($model->date_admission)) {
			$scenario = RequestModel::SCENARIO_DIAGNOSTIC_ONLINE;
			$model->req_doctor_id = null;
		}

		$model->setScenario($scenario);

		if(PartnerModel::model()->isMobileApi($model->partner_id)){
			$model->enter_point = RequestModel::ENTER_POINT_MOBILE;
		} else {
			if ($model->req_doctor_id > 0) {
				$model->enter_point = RequestModel::ENTER_POINT_PARTNER_DOCTOR;
			} elseif ($model->clinic_id > 0) {
				$model->enter_point = RequestModel::ENTER_POINT_PARTNER_CLINIC;
			} else {
				$model->enter_point = RequestModel::ENTER_POINT_PARTNER_SEARCH;
			}
		}

		if ($model->req_doctor_id > 0) {
			$doctorModel = DoctorModel::model()->findByPk($model->req_doctor_id);
			if (is_null($doctorModel)) {
				throw new \CException("Нет такого врача в системе");
			}
		}

		if(!$model->save()){
			$msg = "Ошибка сохранения заявки из api";

			if ($model->hasErrors()) {
				$firstMsg = current($model->getErrors());
				$msg = $firstMsg[0];
			}
			throw new \CException($msg);
		}

		$model->addHistory("Заявка создана через API. Партнёр #" . $model->partner_id);

		if (isset($slotId) && $slotId) {
			try{
				if(!$model->book($slotId, true)){
					$bookingErrors = [];

					foreach($model->getErrors() as $errors){
						foreach($errors as $error){
							$bookingErrors[] = $error;
						}
					}

					if (count($bookingErrors)) {
						$model->addHistory(
							"При резервировании слота #" . $slotId . " произошли ошибки: " . var_export($bookingErrors, true)
						);
					}
				}
			} catch (\Exception $e){
				$model->addHistory("Ошибка при резервировании слота #{$slotId}" . $e->getMessage());
			}
		}

		return $model;
	}

	/**
	 * Запрашиваемое количество докторов учитывая лимит
	 *
	 * @param $params
	 *
	 * @return int
	 */
	protected function getCountDoctorsWithLimit($params)
	{
		$limit = isset($params['count']) ? intval($params['count']) : self::LIMIT_DOCTORS;

		return ($limit < 1 || $limit > self::LIMIT_DOCTORS) ? self::LIMIT_DOCTORS : $limit;
	}

}
