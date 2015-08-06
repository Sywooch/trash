<?php

use dfs\models\DistrictModel;

/**
 * Class ApiDto
 */
class ApiDto
{
	/**
	 * @var RESTClient
	 */
	private $rest;
	private $restPathPrefix;
	private $limit;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->rest = new RESTClient();
		$this->rest->initialize(['server' => 'https://' . Yii::app()->params->rest_api_login . ':' . Yii::app()->params->rest_api_password . '@' . Yii::app()->params->rest_api_domain]);
		$this->restPathPrefix = 'api/rest/' . Yii::app()->params->rest_api_version . '/json';
		$this->limit = Yii::app()->params->page_size;
	}

	/**
	 * Получение статистики
	 *
	 * @return StatModel
	 */
	public function getStats()
	{
		$stat =
			$this->rest->get(
				$this->restPathPrefix . '/stat/city/' . Yii::app()->city->getModel()->getId() . '/',
				[],
				'json',
				true,
				86400
			);

		$statModel = new StatModel();
		$statModel->setDoctors($stat->Doctors);
		$statModel->setRequests($stat->Requests);
		$statModel->setReviews($stat->Reviews);

		return $statModel;
	}

	/**
	 * Получение списка городов
	 *
	 * @return CityModel[]
	 */
	public function getCityList()
	{
		$cityList = $this->rest->get(
			$this->restPathPrefix . '/city/',
			[],
			'json',
			true,
			86400
		);

		$data = [];

		foreach ($cityList->CityList as $city) {
			$cityModel = new CityModel();
			$cityModel->setId($city->Id);
			$cityModel->setName($city->Name);
			$cityModel->setAlias($city->Alias);
			$cityModel->setPhone($city->Phone);

			$data[] = $cityModel;
		}

		return $data;
	}

	/**
	 * Город по id
	 *
	 * @return CityModel
	 */
	public function getCityById($id)
	{
		$cityList = $this->getCityList();

		foreach ($cityList as $city) {
			if ($city->getId() == $id) {
				return $city;
			}
		}

		return null;
	}

	/**
	 * Список станций метро
	 *
	 * @return array ['а'=>MetroModel[],...]
	 */
	public function getMetroList()
	{
		$metroList =
			$this->rest->get(
				$this->restPathPrefix . '/metro/city/' . Yii::app()->city->getModel()->getId() . '/',
				[],
				'json',
				true,
				86400
			);

		$data = [];

		foreach ($metroList->MetroList as $metro) {
			$firstLetter = mb_substr($metro->Name, 0, 1, 'UTF-8');

			if (!isset($data[$firstLetter])) {
				$data[$firstLetter] = [];
			}

			$data[$firstLetter][] = $this->_hydrateMetro($metro);
		}

		return $data;
	}

	private function _hydrateMetro($metro)
	{
		$metroModel = new MetroModel();
		$metroModel->setId($metro->Id);
		$metroModel->setCityId($metro->CityId);
		$metroModel->setName($metro->Name);
		$metroModel->setLineName($metro->LineName);
		$metroModel->setLineColor($metro->LineColor);
		$metroModel->setAlias($metro->Alias);

		return $metroModel;
	}

	/**
	 * Полуает список районов
	 *
	 * @return array
	 */
	public function getDistrictList()
	{
		$districtList =
			$this->rest->get(
				$this->restPathPrefix . '/district/city/' . Yii::app()->city->getModel()->getId() . '/',
				[],
				'json',
				true,
				86400
			);

		$data = [];

		foreach ($districtList->DistrictList as $district) {
			$firstLetter = mb_substr($district->Name, 0, 1, 'UTF-8');

			if (!isset($data[$firstLetter])) {
				$data[$firstLetter] = [];
			}

			$data[$firstLetter][] = $this->_hydrateDistrict($district);
		}

		return $data;
	}

	/**
	 * Генерирует и возвращает модель района
	 *
	 * @param object $district объект с данными о районе, полученные через API
	 *
	 * @return DistrictModel
	 */
	private function _hydrateDistrict($district)
	{
		$districtModel = new DistrictModel();
		$districtModel->setId($district->Id);
		$districtModel->setName($district->Name);
		$districtModel->setAlias($district->Alias);

		return $districtModel;
	}

	/**
	 * Метро по id
	 *
	 * @param int $id
	 *
	 * @return MetroModel|null
	 */
	public function getMetroById($id)
	{
		$metroAll = $this->getMetroList();

		foreach ($metroAll as $letter => $metroList) {
			/**
			 * @var MetroModel[] $metroList
			 */
			foreach ($metroList as $item) {
				if ($item->getId() == $id) {
					return $item;
				}
			}
		}

		return null;
	}

	/**
	 * Метро по alias
	 *
	 * @param int $alias
	 *
	 * @return MetroModel|null
	 */
	public function getMetroByAlias($alias)
	{
		$metroAll = $this->getMetroList();

		foreach ($metroAll as $letter => $metroList) {
			/**
			 * @var MetroModel[] $metroList
			 */
			foreach ($metroList as $item) {
				if ($item->getAlias() == $alias) {
					return $item;
				}
			}
		}

		return null;
	}

	/**
	 * Район по id
	 *
	 * @param integer $id идентификатор района
	 *
	 * @return DistrictModel|null
	 */
	public function getDistrictById($id)
	{
		$districtAll = $this->getDistrictList();

		foreach ($districtAll as $letter => $districtList) {
			/**
			 * @var DistrictModel[] $districtList
			 */
			foreach ($districtList as $item) {
				if ($item->getId() == $id) {
					return $item;
				}
			}
		}

		return null;
	}

	/**
	 * Район по alias
	 *
	 * @param integer $alias абривиатура URL
	 *
	 * @return DistrictModel|null
	 */
	public function getDistrictByAlias($alias)
	{
		$districtAll = $this->getDistrictList();

		foreach ($districtAll as $letter => $districtList) {
			/**
			 * @var DistrictModel[] $districtList
			 */
			foreach ($districtList as $item) {
				if ($item->getAlias() == $alias) {
					return $item;
				}
			}
		}

		return null;
	}

	/**
	 * Список специальностей
	 *
	 * @return array ['а'=>SpecialityModel[],...]
	 */
	public function getSpecialityList()
	{
		$specialityList =
			$this->rest->get(
				$this->restPathPrefix . '/speciality/city/' . Yii::app()->city->getModel()->getId() . '/onlySimple/0',
				[],
				'json',
				true,
				86400
			);

		$data = [];

		foreach ($specialityList->SpecList as $spec) {
			$firstLetter = mb_substr($spec->Name, 0, 1, 'UTF-8');

			if (!isset($data[$firstLetter])) {
				$data[$firstLetter] = [];
			}

			$specModel = $this->_hydrateSpeciality($spec);

			$data[$firstLetter][] = $specModel;
		}

		return $data;
	}

	/**
	 * Генерирует и получает модель специальности
	 *
	 * @param object $spec объект с данными о специальности, полученные через API
	 *
	 * @return SpecialityModel
	 */
	private function _hydrateSpeciality($spec)
	{
		$specModel = new SpecialityModel();
		$specModel->setId($spec->Id);
		$specModel->setName($spec->Name);
		$specModel->setNameGenitive($spec->NameGenitive);
		$specModel->setNamePlural($spec->NamePlural);
		$specModel->setNamePluralGenitive($spec->NamePluralGenitive);
		$specModel->setSimple($spec->IsSimple);
		if (isset($spec->Alias)) {
			$specModel->setAlias($spec->Alias);
		}

		return $specModel;
	}

	/**
	 * Специальность по id
	 *
	 * @param int $id
	 *
	 * @return SpecialityModel|null
	 */
	public function getSpecialityById($id)
	{
		$specialityAll = $this->getSpecialityList();

		foreach ($specialityAll as $letter => $specialityList) {
			/**
			 * @var SpecialityModel[] $specialityList
			 */
			foreach ($specialityList as $item) {
				if ($item->getId() == $id) {
					return $item;
				}
			}
		}

		return null;
	}

	/**
	 * Специальность по alias
	 *
	 * @param int $alias
	 *
	 * @return SpecialityModel|null
	 */
	public function getSpecialityByAlias($alias)
	{
		$specialityAll = $this->getSpecialityList();

		foreach ($specialityAll as $letter => $specialityList) {
			/**
			 * @var SpecialityModel[] $specialityList
			 */
			foreach ($specialityList as $item) {
				if ($item->getAlias() == $alias) {
					return $item;
				}
			}
		}

		return null;
	}

	/**
	 * Список врачей
	 *
	 * @param int    $specialityID
	 * @param int    $stationsID
	 * @param int    $districtId Идентификатор района
	 * @param int    $areaId     Идентификатор округа
	 * @param string $order
	 * @param string $direction
	 * @param int    $page
	 * @param string $typeSearch Тип поиска
	 * @param int|null $nadom
	 * @param int|null $deti
	 * @return array ['count' => int, 'doctors' => DoctorModel[]]
	 */
	public function getDoctors(
		$specialityID = null,
		$stationsID = null,
		$districtId = null,
		$areaId = null,
		$order = null,
		$direction = 'asc',
		$page = 1,
		$typeSearch = null,
		$nadom = null,
		$deti = null
	)
	{
		$startFrom = $page < 2 ? 0 : (($page - 1) * $this->limit);

		$urlParams = "";

		$specialityID = !$specialityID ? 0 : $specialityID;
		$urlParams .= '/speciality/' . $specialityID;

		if ($typeSearch == 'landing') {
			$urlParams .= "/type/{$typeSearch}";
		}

		if ($stationsID) {
			$stationsID = !$stationsID ? 0 : $stationsID;
			$urlParams .= '/stations/' . $stationsID;
		} else {
			$districtId = !$districtId ? 0 : $districtId;
			$urlParams .= '/district/' . $districtId;
		}

		if ($areaId) {
			$urlParams .= "/area/{$areaId}";
		}

		if ($order) {
			$direction = $direction == 'asc' ? '' : '-';
			$urlParams .= '/near/extra/order/' . $direction . $order;
		}

		if($deti !== null){
			$urlParams .= '/deti/' . $deti;
		}

		if($nadom !== null){
			$urlParams .= '/na-dom/' . $nadom;
		}

		$doctors = $this->rest->get(
			$this->restPathPrefix .
			'/doctor/list/start/' .
			$startFrom .
			'/count/' .
			$this->limit .
			'/city/' .
			Yii::app()->city->getModel()->getId() .
			$urlParams,
			[],
			'json',
			true,
			60 * 60
		);

		$data = [];

		foreach ($doctors->DoctorList as $doctor) {
			$clinics = [];
			foreach ($doctor->Clinics as $clinic) {
				$clinics[] = $this->getClinicById($clinic);
			}

			$data[] = $this->_hydrateDoctor($doctor, $clinics);
		}

		return ['count' => $doctors->Total, 'doctors' => $data];
	}

	/**
	 * Список всех врачей
	 *
	 * @param int $page номер страницы
	 *
	 * @return array ['count' => int, 'doctors' => DoctorModel[]]
	 */
	public function getDoctorsAll($page)
	{
		$startFrom = $page < 2 ? 0 : (($page - 1) * $this->limit);

		$doctors = $this->rest->get(
			$this->restPathPrefix .
			'/doctor/list/start/' .
			$startFrom .
			'/count/' .
			$this->limit .
			'/city/' .
			Yii::app()->city->getModel()->getId() .
			'/',
			[],
			'json',
			true,
			60 * 60
		);

		$data = [];

		foreach ($doctors->DoctorList as $doctor) {
			$clinics = [];
			foreach ($doctor->Clinics as $clinic) {
				$clinics[] = $this->getClinicById($clinic);
			}

			$data[] = $this->_hydrateDoctor($doctor, $clinics);
		}

		return ['count' => $doctors->Total, 'doctors' => $data];
	}

	/**
	 * @param stdClass $doctor
	 * @param array    $clinics
	 * @param array    $reviews
	 *
	 * @return DoctorModel
	 */
	private function _hydrateDoctor(stdClass $doctor, $clinics = [], $reviews = [])
	{
		$doctorModel = new DoctorModel();
		$doctorModel->setId($doctor->Id);
		$doctorModel->setName($doctor->Name);
		$doctorModel->setAlias($doctor->Alias);
		$doctorModel->setRating($doctor->Rating);

		$doctorModel->setPrice($doctor->Price);
		$doctorModel->setSpecialPrice($doctor->SpecialPrice);
		$doctorModel->setImg($doctor->Img);
		$doctorModel->setExperienceYear($doctor->ExperienceYear);
		$doctorModel->setDeparture($doctor->Departure);
		$doctorModel->setCategory($doctor->Category);
		$doctorModel->setClinics($doctor->Clinics);
		$doctorModel->setDegree($doctor->Degree);
		$doctorModel->setRank($doctor->Rank);
		$doctorModel->setClinicModels($clinics);
		$doctorModel->setReviews($reviews);


		if (isset($doctor->InternalRating)) {
			$doctorModel->setInternalRating($doctor->InternalRating);
		}

		if (isset($doctor->Sex)) {
			$doctorModel->setSex($doctor->Sex);
		}

		if (isset($doctor->OpinionCount)) {
			$doctorModel->setOpinionCount($doctor->OpinionCount);
		}

		if (isset($doctor->setTextAbout)) {
			$doctorModel->setTextAbout($doctor->setTextAbout);
		}

		if (isset($doctor->Stations)) {
			$stations = [];
			foreach ($doctor->Stations as $metro) {
				$stations[] = $this->_hydrateMetro($metro);
			}

			$doctorModel->setStations($stations);
		}


		if (isset($doctor->Specialities)) {
			$specialities = [];
			foreach ($doctor->Specialities as $spec) {
				$specialities[] = $this->_hydrateSpeciality($spec);
			}

			$doctorModel->setSpecialities($specialities);
		}


		//for detail page
		if (isset($doctor->Description)) {
			$doctorModel->setDescription($doctor->Description);
		}

		if (isset($doctor->TextEducation)) {
			$doctorModel->setTextEducation($doctor->TextEducation);
		}

		if (isset($doctor->TextDegree)) {
			$doctorModel->setTextDegree($doctor->TextDegree);
		}

		if (isset($doctor->TextSpec)) {
			$doctorModel->setTextSpec($doctor->TextSpec);
		}

		if (isset($doctor->TextCourse)) {
			$doctorModel->setTextCourse($doctor->TextCourse);
		}

		if (isset($doctor->TextExperience)) {
			$doctorModel->setTextExperience($doctor->TextExperience);
		}

		if (isset($doctor->AddPhoneNumber)) {
			$doctorModel->setAddPhoneNumber($doctor->AddPhoneNumber);
		}

		if (isset($doctor->isActive)) {
			$doctorModel->setActive($doctor->isActive);
		}

		return $doctorModel;
	}

	/**
	 * Полная информация о враче по alias
	 *
	 * @param string $alias
	 *
	 * @return DoctorModel|null
	 */
	public function getDoctorByAlias($alias)
	{
		$doctor = $this->rest->get(
			$this->restPathPrefix .
			'/doctor/by/alias/' . $alias,
			[],
			'json',
			true,
			60 * 60
		);

		if (isset($doctor->Doctor[0]->Id)) {
			$doctorData = $doctor->Doctor[0];

			$clinics = [];
			foreach ($doctorData->Clinics as $clinic) {
				$clinics[] = $this->getClinicById($clinic);
			}

			$reviews = $this->getReviews($doctorData->Id);

			return $this->_hydrateDoctor($doctorData, $clinics, $reviews);
		} else {
			return null;
		}
	}

	/**
	 * Полная информация о враче по id
	 *
	 * @param int $id
	 *
	 * @return DoctorModel|null
	 */
	public function getDoctorById($id)
	{
		$doctor = $this->rest->get(
			$this->restPathPrefix .
			'/doctor/' . $id,
			[],
			'json',
			true,
			60 * 60
		);

		if (isset($doctor->Doctor[0]->Id)) {
			$doctorData = $doctor->Doctor[0];

			$clinics = [];
			foreach ($doctorData->Clinics as $clinic) {
				$clinics[] = $this->getClinicById($clinic);
			}

			$reviews = $this->getReviews($doctorData->Id);

			return $this->_hydrateDoctor($doctorData, $clinics, $reviews);
		} else {
			return null;
		}
	}

	/**
	 * Клиника по id
	 *
	 * @param int $id
	 *
	 * @return ClinicModel|null
	 */
	public function getClinicById($id)
	{
		$clinic = $this->rest->get(
			$this->restPathPrefix .
			'/clinic/' . $id,
			[],
			'json',
			true,
			86400
		);

		if (isset($clinic->Clinic[0]->Id)) {
			$clinicData = $clinic->Clinic[0];

			$clinicModel = new ClinicModel();
			$clinicModel->setId($clinicData->Id);
			$clinicModel->setName($clinicData->Name);
			$clinicModel->setShortName($clinicData->ShortName);
			$clinicModel->setRewriteName($clinicData->RewriteName);
			$clinicModel->setCity($clinicData->City);
			$clinicModel->setStreet($clinicData->Street);
			$clinicModel->setDescription($clinicData->Description);
			$clinicModel->setHouse($clinicData->House);
			$clinicModel->setPhone($clinicData->Phone);
			$clinicModel->setLogo($clinicData->Logo);
			if (!empty($clinicData->Doctors)) { // временный костыль, пока API не починили
				$clinicModel->setDoctors($clinicData->Doctors);
			}
			$clinicModel->setLongitude($clinicData->Longitude);
			$clinicModel->setLatitude($clinicData->Latitude);

			return $clinicModel;
		} else {
			return null;
		}
	}

	/**
	 * Отзывы о враче
	 *
	 * @param int $doctorId
	 *
	 * @return ReviewModel[]
	 */
	public function getReviews($doctorId)
	{
		$reviews = $this->rest->get(
			$this->restPathPrefix .
			'/review/doctor/' . $doctorId,
			[],
			'json',
			true,
			60 * 60
		);

		$data = [];

		foreach ($reviews->ReviewList as $review) {
			$reviewModel = new ReviewModel();
			$reviewModel->setId($review->Id);
			$reviewModel->setClient($review->Client);
			$reviewModel->setRatingQlf($review->RatingQlf);
			$reviewModel->setRatingAtt($review->RatingAtt);
			$reviewModel->setRatingRoom($review->RatingRoom);
			$reviewModel->setText($review->Text);
			$reviewModel->setDate(new \DateTime($review->Date));
			$reviewModel->setDoctorId($review->DoctorId);

			$data[] = $reviewModel;
		}

		return $data;
	}

	/**
	 * Отправка заявки
	 *
	 * @param \RequestModel $request
	 *
	 * @return \RequestModel
	 */
	public function sendRequest(\RequestModel $request)
	{
		$data = json_encode([
			'name' => $request->getName(),
			'phone' => $request->getPhone(),
			'doctor' => $request->getDoctor(),
			'comment' => $request->getComment(),
			'clinic' => $request->getClinic()
		]);

		$this->rest->set_header('Content-Type', 'application/json');
		$requestResult = $this->rest->post($this->restPathPrefix . '/request', $data, 'json');

		$request->setMessage($requestResult->Response->message);
		$request->setStatus($requestResult->Response->status);

		return $request;
	}

	/**
	 * Получение списка округов
	 *
	 * @return AreaModel[]
	 */
	public function getAreaList()
	{
		$areaList = $this->rest->get(
			$this->restPathPrefix . '/area/',
			[],
			'json',
			true,
			86400
		);

		$data = [];
		foreach ($areaList->AreaList as $area) {
			$cityModel = new AreaModel();
			$cityModel->setId($area->Id);
			$cityModel->setName($area->Name);
			$cityModel->setAlias($area->Alias);
			$cityModel->setFullName($area->FullName);

			$data[] = $cityModel;
		}

		return $data;
	}

	/**
	 * Получения Округа по алиясу
	 *
	 * @param string $alias
	 *
	 * @return CityModel|null
	 */
	public function getAreaByAlias($alias)
	{
		return $this->find($this->getAreaList(), "alias", $alias);
	}

	/**
	 * Метод поиска по обьекту
	 *
	 * @param object[] $list
	 * @param string   $method
	 * @param mixed    $param
	 *
	 * @return object|null
	 */
	private function find(array $list, $method, $param)
	{
		$method = ucfirst($method);
		foreach ($list as $val) {
			if ($val->{"get{$method}"}() == $param) {
				return $val;
			}
		}

		return null;
	}
} 
