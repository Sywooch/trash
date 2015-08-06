<?php
use dfs\docdoc\models\RatingModel;
use dfs\docdoc\models\RatingStrategyModel;
use dfs\docdoc\models\SectorModel;
use dfs\docdoc\models\DoctorModel;
use dfs\docdoc\models\PartnerModel;
use dfs\docdoc\models\DoctorClinicModel;
use dfs\docdoc\models\ClinicPhotoModel;

class Clinic
{
	public $id;
	public $attributes = array();
	public $data = array();
	public $title;
	public $parentClinicId;
	public $crDate;
	public $shortName;
	public $rewriteName;
	public $URL;
	public $logoPath;

	public $age;
	public $isDiagnostic;
	public $isClinic;
	public $isPrivatDoctor;

	public $asteriskPhone;
	public $phone;
	public $phoneAppointment;
	public $contactName;
	public $email;
	public $description;
	public $shortDescription;
	public $operatorComment;

	public $wayOnFoot;
	public $wayOnCar;

	public $city;
	public $street;
	public $house;
	public $longitude;
	public $latitude;

	public $sortPosition;
	public $status;
	public $yaAPI;
	public $showSchedule;
	public $sendSMS;

	public $branches = array();
	public $schedule = array();
	public $rating = array();

	public $showBilling;
	public $contractId;
	public $diagContractId;
	public $settingsId;
	public $diagSettingsId;
	public $settings = array();
	public $diagSettings = array();
	public $price = array();
	public $lkStartHistoryDate = null;


	function __construct($alias = null)
	{
		if (!empty($alias)) {
			$this->getModel($alias);
		}
	}

	/*
	 * Получение модели врача
	 * @param integer $id
	 * @return array
	 */
	public function getModel($alias)
	{
		$sql = "SELECT
				            t1.id, 
				            t1.parent_clinic_id,  
				            t1.name,  
				            t1.short_name, 
				            t1.rewrite_name,
				            t1.status, 
				            t1.phone, 
				            t1.phone_appointment, 
				            t1.asterisk_phone, 
				            t1.url, 
				            t1.rating, 
				            t1.email,
				            t1.logoPath, 
				            t1.contact_name, 
				            DATE_FORMAT( t1.created,'%d.%m.%Y') AS crDate,
				            t1.longitude, 
				            t1.latitude, 
				            t1.age_selector as age,
				            t1.city, 
				            t1.street,
				            t1.street_id,
				            t1.house, 
				            t1.description, 
				            t1.shortDescription as short_description, 
				            t1.operator_comment, 
				            t1.isDiagnostic, 
				            t1.isClinic, 
				            t1.isPrivatDoctor,
				            t1.sort4commerce as sortPosition, 
				            t1.weekdays_open, t1.weekend_open, t1.saturday_open, t1.sunday_open,
				            t1.open_4_yandex, 
				            t1.schedule_state, 
				            t1.sendSMS, 
				            t1.settings_id, 
				            t1.diag_settings_id,
				            t1.district_id
				    FROM clinic  t1
				    WHERE
				            t1.id='" . $alias . "'
				            OR
				            t1.rewrite_name='$alias'";

		$data = array();
		$result = query($sql);
		if (num_rows($result) > 0) {
			while ($row = fetch_array($result)) {
				$row['Diagnostics'] = $this->getDiagnosticList($row['id']);
				array_push($data, $row);
			}

			$this->data = $data[0];
			$this->id = $data[0]['id'];

		}
		return $data;
	}

	public function setClinic($clinicId)
	{
		$this->id = $clinicId;
	}

	public function setParams($params = array())
	{
		if (is_array($params) && count($params) > 0) {
			foreach ($params as $param => $data) {
				$this->$param = $data;
			}
		}
	}

	public function getClinic($clinicId)
	{
		$clinicId = intval($clinicId);

		if ($clinicId > 0) {
			$sql = "SELECT
						t1.id, t1.parent_clinic_id,  
						t1.name,  t1.short_name, t1.rewrite_name,
						t1.status, t1.phone, t1.phone_appointment, t1.asterisk_phone, t1.url, 
						t1.rating, t1.email, t1.contact_name, 
						DATE_FORMAT( t1.created,'%d.%m.%Y') AS crDate,
						t1.url, t1.longitude, t1.latitude, t1.age_selector as age,
						t1.city, t1.street, t1.house, t1.description, t1.shortDescription as short_description, t1.operator_comment,
						t1.way_on_foot, t1.way_on_car,
						t1.isDiagnostic, t1.isClinic, t1.isPrivatDoctor,
						t1.sort4commerce as sortPosition, t1.logoPath,
						t1.weekdays_open, t1.weekend_open, t1.saturday_open, t1.sunday_open,
						t1.open_4_yandex, t1.schedule_state, t1.sendSMS, 
						t1.settings_id,
						t1.diag_settings_id
					FROM clinic  t1
					WHERE
						t1.id = $clinicId";
			//echo $sql;
			$result = query($sql);
			if (num_rows($result) == 1) {
				$row = fetch_object($result);
				$this->id = $row->id;
				$this->crDate = $row->crDate;
				$this->parentClinicId = $row->parent_clinic_id;
				$this->title = $row->name;
				$this->shortName = $row->short_name;
				$this->rewriteName = $row->rewrite_name;
				$this->URL = $row->url;
				$this->logoPath = $row->logoPath;

				$this->age = $row->age;
				$this->isDiagnostic = $row->isDiagnostic;
				$this->isClinic = $row->isClinic;
				$this->isPrivatDoctor = $row->isPrivatDoctor;

				$this->asteriskPhone = formatPhone($row->asterisk_phone);
				$this->phone = $row->phone;
				$this->phoneAppointment = $row->phone_appointment;
				$this->contactName = $row->contact_name;
				$this->email = $row->email;
				$this->description = $row->description;
				$this->shortDescription = $row->short_description;
				$this->operatorComment = $row->operator_comment;

				$this->wayOnFoot = $row->way_on_foot;
				$this->wayOnCar = $row->way_on_car;

				$this->city = $row->city;
				$this->street = $row->street;
				$this->house = $row->house;
				$this->longitude = $row->longitude;
				$this->latitude = $row->latitude;

				$this->sortPosition = $row->sortPosition;
				$this->status = $row->status;
				$this->yaAPI = $row->open_4_yandex;
				$this->showSchedule = $row->schedule_state;
				$this->sendSMS = $row->sendSMS;
				$this->settingsId = $row->settings_id;
				$this->diagSettingsId = $row->diag_settings_id;

				$this->schedule = self::getClinicSchedule();
				$this->rating = self::getClinicRating();
				$this->settings = self::getClinicSettings();
				$this->diagSettings = self::getDiagnosticaSettings();

				if (count($this->settings) > 0) {

					$this->showBilling = $this->settings["showBilling"];
					$this->contractId = $this->settings["contractId"];
					$this->price[1] = $this->settings["price1"];
					$this->price[2] = $this->settings["price2"];
					$this->price[3] = $this->settings["price3"];
					$this->lkStartHistoryDate = $this->settings["lkStartHistoryDate"];
				}

				switch ($this->contractId) {
					case 1 :
					{
						// Фикс 800/1200/1500

						if (empty($this->price[1]) || $this->price[1] == 0.00) {
							$this->price[1] = 1500;
						}
						if (empty($this->price[2]) || $this->price[2] == 0.00) {
							$this->price[2] = 1200;
						}
						if (empty($this->price[3]) || $this->price[3] == 0.00) {
							$this->price[3] = 800;
						}
					}
						break;
					case 2 :
					{
						// Фикс 600/1000
						if (empty($this->price[1]) || $this->price[1] == 0.00) {
							$this->price[1] = 1000;
						}
						if (empty($this->price[2]) || $this->price[2] == 0.00) {
							$this->price[2] = 100;
						}
						if (empty($this->price[3]) || $this->price[3] == 0.00) {
							$this->price[3] = 600;
						}

					}
						break;
					default :
						// Фикс 800/1200/1500
						if (empty($this->price[1]) || $this->price[1] == 0.00) {
							$this->price[1] = 1500;
						}
						if (empty($this->price[2]) || $this->price[2] == 0.00) {
							$this->price[2] = 1200;
						}
						if (empty($this->price[3]) || $this->price[3] == 0.00) {
							$this->price[3] = 800;
						}
				}

				if (count($this->diagSettings) > 0) {
					$this->diagContractId = $this->diagSettings["contractId"];
					//					$this -> lkStartHistoryDate = $this-> settings["lkStartHistoryDate"];
				}

			}
		}
	}

	public function getClinicBranches()
	{
		$branchList = array();

		$sql = "SELECT
					t1.id,  t1.name, t1.short_name, t1.rewrite_name, 
					t1.status, t1.phone, t1.phone_appointment, t1.url, t1.rating, t1.email, t1.contact_name,
					t1.age_selector as age, 
					t1.city, t1.street, t1.house, 
					DATE_FORMAT( t1.created,'%d.%m.%Y') AS crDate
				FROM clinic  t1
				WHERE t1.parent_clinic_id = " . $this->id . "
				ORDER BY  t1.created DESC, t1.id";
		$result = query($sql);
		if (num_rows($result) > 0) {
			while ($row = fetch_object($result)) {
				$branch = new Clinic();
				$branch->setParams();
				array_push($branchList, $branch);
			}
		}

		return $branchList;
	}

	public function getSpecList()
	{
		$data = array();

		$sql = "SELECT t1.id AS Id, t1.spec_name AS Name
				    FROM sector t1
				    INNER JOIN doctor_sector t2 ON t2.sector_id=t1.id
				    INNER JOIN doctor_4_clinic t3 ON t3.doctor_id=t2.doctor_id and t3.type = " . DoctorClinicModel::TYPE_DOCTOR . "
				    INNER JOIN doctor t4 ON t4.id=t3.doctor_id
				    WHERE t4.status=3 AND t3.clinic_id=" . $this->id . "
				    GROUP BY t1.id";
		$result = query($sql);
		while ($row = fetch_array($result)) {
			array_push($data, $row);
		}

		return $data;
	}

	static function getSpecListById($id)
	{
		$data = array();

		$sql = "SELECT t1.id AS Id, t1.spec_name AS Name
				    FROM sector t1
				    INNER JOIN doctor_sector t2 ON t2.sector_id=t1.id
				    INNER JOIN doctor_4_clinic t3 ON t3.doctor_id=t2.doctor_id and t3.type = " . DoctorClinicModel::TYPE_DOCTOR . "
				    INNER JOIN doctor t4 ON t4.id=t3.doctor_id
				    WHERE t4.status=3 AND t3.clinic_id=" . $id . "
				    GROUP BY t1.id";
		$result = query($sql);
		while ($row = fetch_array($result)) {
			array_push($data, $row);
		}

		return $data;
	}

	/**
	 *
	 * Метод получает массив (день недели (0-7), время начала работы, время окончания работы клиники)
	 */
	public function getClinicSchedule()
	{
		$clinicSchedule = array();

		$sql = "SELECT
					t1.id,  t1.week_day, t1.start_time, t1.end_time
				FROM clinic_schedule  t1
				WHERE t1.clinic_id = " . $this->id;
		$result = query($sql);
		if (num_rows($result) > 0) {
			while ($row = fetch_object($result)) {
				array_push($clinicSchedule, array($row->week_day, $row->start_time, $row->end_time));
			}
		}

		return $clinicSchedule;
	}

	/**
	 *
	 * Метод получает массив рейтингов клиники
	 */
	public function getClinicRating()
	{
		$clinicRating = array();

		$sql = "SELECT
					t1.rating_1, 
					t1.rating_2,
					t1.rating_3,
					t1.rating_4,
					t1.rating_total
				FROM clinic  t1
				WHERE t1.id = " . $this->id;
		$result = query($sql);
		if (num_rows($result) == 1) {
			$row = fetch_object($result);
			$clinicRating = array(
				"r1"     => $row->rating_1,
				"r2"     => $row->rating_2,
				"r3"     => $row->rating_3,
				"r4"     => $row->rating_4,
				"rTotal" => $row->rating_total
			);

		}

		return $clinicRating;
	}

	/**
	 *
	 * Метод получает массив настроек клиники
	 */
	public function getClinicSettings()
	{
		$clinicSettings = array();

		if ($this->settingsId > 0) {
			$sql = "SELECT
						t1.contract_id,  t1.show_billing, t1.price_1, t1.price_2, t1.price_3 , t1.lk_start_history_date 
					FROM clinic_settings  t1
					WHERE t1.settings_id = " . $this->settingsId;
			//echo $sql; 
			$result = query($sql);
			if (num_rows($result) == 1) {
				$row = fetch_object($result);
				$clinicSettings = array(
					"contractId"         => $row->contract_id,
					"showBilling"        => $row->show_billing,
					"price1"             => ($row->price_1 != 0.00 && !empty($row->price_1)) ? $row->price_1 : "",
					"price2"             => ($row->price_2 != 0.00 && !empty($row->price_2)) ? $row->price_2 : "",
					"price3"             => ($row->price_3 != 0.00 && !empty($row->price_3)) ? $row->price_3 : "",
					"lkStartHistoryDate" => $row->lk_start_history_date
				);
			}
		}

		return $clinicSettings;
	}

	/**
	 *
	 * Метод получает массив настроек диагностики
	 */
	public function getDiagnosticaSettings()
	{
		$clinicSettings = array();

		if ($this->diagSettingsId > 0) {
			$sql = "SELECT
						t1.contract_id,  t1.show_billing, t1.price, t1.lk_start_history_date 
					FROM diagnostica_settings  t1
					WHERE t1.settings_id = " . $this->diagSettingsId;
			//echo $sql;
			$result = query($sql);
			if (num_rows($result) == 1) {
				$row = fetch_object($result);
				$clinicSettings = array(
					"contractId"         => $row->contract_id,
					"showBilling"        => $row->show_billing,
					"price"              => ($row->price != 0.00 && !empty($row->price)) ? $row->price : "",
					"lkStartHistoryDate" => $row->lk_start_history_date
				);
			}
		}

		return $clinicSettings;
	}

	/**
	 * Метод получает цену по специальности
	 *
	 * @param int $specializationId Специализация
	 *
	 * @return int Цена
	 */
	public function getPrice4Specizlization($specializationId)
	{
		switch ((int)$specializationId) {
			case 86 :
				return $this->price[1];
			case 90 :
				return $this->price[2];
			default:
				return $this->price[3];
		}
	}

	/*
	 * Получение списка клиник
	 * @param array $params = (
	 *      city            - id города
	 *      IsDiagnostic    - признак "диагностический центр"
	 *      isClinic        - признак "клиника"
	 *      open_4_yandex   - отображается в поиске Яндекса
	 *      withDoctors     - выводить с учетом присутствия активных врачей
	 *      diagnostic      - id диагностики
	 *      stations        - массив станций метро
	 *      near            - признак поиска по ближайшим станциям
	 *      start
	 *      count
	 *      limit
	 * )
	 *
	 * @return array
	 */
	static function getItems($params)
	{
		$sqlAdd = "1=1";
		$addJoin = "";
		$addSelect = "";
		$limit = "";
		$order = '';

		if (!isset($params['city']) || empty($params['city'])) {
			$params['city'] = 1;
		}

		$sqlAdd .= " AND t1.status = 3 "; // Только активные

		if (count($params) > 0) {

			if (isset($params['isDiagnostic']) || isset($params['isClinic']) || isset($params['isDoctor'])) {
				$sqlAdd .= " AND (0 ";
				if (isset($params['isDiagnostic'])) {
					$sqlAdd .= "OR t1.isDiagnostic = '{$params['isDiagnostic']}' ";
				}
				if (isset($params['isClinic'])) {
					$sqlAdd .= "OR t1.isClinic = '{$params['isClinic']}' ";
				}
				if (isset($params['isDoctor'])) {
					$sqlAdd .= "OR t1.isPrivatDoctor = '{$params['isDoctor']}' ";
				}
				$sqlAdd .= ")";
			}

			if (isset($params['open_4_yandex'])) {
				$sqlAdd .= " AND t1.open_4_yandex = '" . $params['open_4_yandex'] . "' ";
			}

			$speciality = isset($params['speciality']) ? intval($params['speciality']) : 0;

			if (isset($params['withDoctors']) || $speciality > 0) {
				$addJoin .= " INNER JOIN doctor_4_clinic t2 ON t2.clinic_id=t1.id and t2.type = " . DoctorClinicModel::TYPE_DOCTOR . " ";
				$addJoin .= " INNER JOIN doctor t3 ON t3.id=t2.doctor_id ";
				$sqlAdd .= " AND t3.status=3 ";
			}

			if ($speciality > 0) {
				$addJoin .= ' INNER JOIN doctor_sector ds ON (ds.doctor_id = t2.doctor_id) ';
				$sqlAdd .= " AND ds.sector_id = {$speciality} ";
			}

			if (isset($params['diagnostic']) && intval($params['diagnostic']) > 0) {
				$subSQL =
					"SELECT id FROM diagnostica WHERE id=" . $params['diagnostic'] . " OR parent_id=" .
					$params['diagnostic'];
				$sqlAdd .= " AND t2.diagnostica_id IN (" . $subSQL . ") ";
				$addJoin .= " LEFT JOIN diagnostica4clinic t2 ON (t2.clinic_id = t1.id) ";
			}

			if (isset($params['partnerId'])) {
				$partner_id = intval($params['partnerId']);
				$addSelect = ", ph.number as ReplacementPhone ";
				$addJoin .= " LEFT JOIN clinic_partner_phone cl_p_ph
					ON cl_p_ph.clinic_id = t1.id and cl_p_ph.partner_id = $partner_id";
				$addJoin .= " LEFT JOIN phone ph ON ph.id = cl_p_ph.phone_id ";
			}

			if (isset($params['stations'])) {
				if (!isset($params['near'])) {
					$params['near'] = 'strict';
				}

				if (count($params['stations']) > 0) {

					$params['stations'] = array_map(
						function ($v) {
							return (int)$v;
						},
						$params['stations']
					);

					$addJoin .= " INNER JOIN underground_station_4_clinic t4 ON (t4.clinic_id = t1.id) ";
					if ($params['near'] == 'strict') {

						$sqlAdd .= " AND t4.undegraund_station_id IN (" . implode(',', $params['stations']) . ")";
					} elseif ($params['near'] == 'closest') {
						$addJoin .= " INNER JOIN closest_station t5 ON (t5.closest_station_id = t4.undegraund_station_id) ";
						$sqlAdd .=
							" AND t5.station_id IN (" . implode(',', $params['stations']) . ") AND t5.priority<>0";
						$order .= " ORDER BY t5.priority";
					} elseif ($params['near'] == 'mixed') {
						$addJoin .= " LEFT JOIN closest_station t5 ON (t5.closest_station_id = t4.undegraund_station_id) ";
						$sqlAdd .= " AND t5.station_id IN (" . implode(',', $params['stations']) . ") ";
						$order .= " ORDER BY t5.priority";
					}
				}
			}

			if (isset($params['count'])) {
				$count = intval($params['count']);
				$start = isset($params['start']) ? (int)($params['start']) : 0;
				$limit = " LIMIT " . $start . ", " . $count;
			}elseif(isset($params['limit'])){
				$limit = " LIMIT " . $limit;
			}
		}

		if (isset($params['city'])) {
			$sqlAdd .= " AND t1.city_id = " . intval($params['city']) . " ";
		}

		if (!empty($params['street'])) {
			$sqlAdd .= ' AND t1.street_id = ' . intval($params['street']);
		}

		if (isset($params['order'])) {
			if ($params['order'] === 'name') {
				$order = ' ORDER BY t1.name asc';
			}
		}

		if (!$order) {
			$order = ' ORDER BY r.rating_value desc';
		}

		if (isset($params['selectPrice'])) {
			$addJoin .= ' LEFT JOIN doctor_4_clinic dc ON (dc.clinic_id = t1.id and dc.type = ' . DoctorClinicModel::TYPE_DOCTOR . ') ';
			$addJoin .= ' LEFT JOIN doctor d ON (d.id = dc.doctor_id AND d.status = ' . DoctorModel::STATUS_ACTIVE . ') ';

			if(isset($params['checkUseSpecialPriceForPartner']) && isset($params['partnerId'])){
				$partner_id = $params['partnerId'];

				$partner = PartnerModel::model()->findByPk($partner_id);

				if($partner && $partner->use_special_price){
					$addSelect .= ', MIN(CASE WHEN d.special_price IS NULL THEN d.price ELSE d.special_price END) as MinPrice';
					$addSelect .= ', MAX(CASE WHEN d.special_price IS NULL THEN d.price ELSE d.special_price END) as MaxPrice';
				} else {
					$addSelect .= ', MIN(d.price) as MinPrice';
					$addSelect .= ', MAX(d.price) as MaxPrice';
				}
			} else {
				$addSelect .= ', MIN(CASE WHEN d.special_price IS NULL THEN d.price ELSE d.special_price END) as MinPrice';
				$addSelect .= ', MAX(CASE WHEN d.special_price IS NULL THEN d.price ELSE d.special_price END) as MaxPrice';
			}
		}

		$strategyId = (int)Yii::app()->rating->getId(RatingStrategyModel::FOR_CLINIC);

		$sql = "SELECT
					t1.id AS Id, t1.name AS Name, t1.short_name AS ShortName, t1.rewrite_name AS RewriteName,
					t1.url AS URL, t1.longitude AS Longitude, t1.latitude AS Latitude,
					t1.city AS City, t1.street AS Street, t1.street_id AS StreetId, t1.house AS House,
					t1.description AS Description, t1.weekdays_open AS WeekdaysOpen, t1.weekend_open AS WeekendOpen,
					t1.shortDescription AS ShortDescription,
					t1.isDiagnostic AS IsDiagnostic,
					t1.isClinic AS isClinic,
					t1.isPrivatDoctor AS IsDoctor,
					CASE
						WHEN t1.asterisk_phone IS NULL THEN t1.asterisk_phone
						ELSE t1.phone
					END AS Phone, t1.phone_appointment AS PhoneAppointment,
					t1.logoPath, t1.schedule_state AS ScheduleState,
					t1.district_id AS DistrictId,
					t1.email AS Email " . $addSelect . "
				FROM clinic t1
				" . $addJoin . "
				join rating r on r.object_id = t1.id and r.object_type = " . RatingModel::TYPE_CLINIC . " and r.strategy_id = " . $strategyId . "
				WHERE " . $sqlAdd . "
				GROUP BY t1.id "
			. $order . " " . $limit;

		$result = query($sql);
		$data = array();

		while ($row = fetch_array($result)) {
			$row['Logo'] = "http://docdoc.ru/upload/kliniki/logo/" . $row['logoPath'];
			if ($row['IsDiagnostic'] == 'yes') {
				$row['Diagnostics'] = self::getDiagnosticList($row['Id']);
			}
			$row['Stations'] = self::getStationList($row['Id']);
			if (isset($params['selectSpecialities'])) {
				$row['Specialities'] = self::getSpecialityList($row['Id']);
			}
			$row['Id'] = (int)$row['Id'];
			$data[] = $row;
		}

		return $data;
	}

	/*
	 * Получает список станций метро
	 *
	 * @param int $id идентификатор клиники
	 *
	 * @return string[]
	 */
	static function getStationList($id)
	{
		$data = array();

		$id = intval($id);

		$sql = "
			SELECT
				t1.id, t1.name,
				t3.name AS lineName, t3.color AS lineColor, t3.city_id
			FROM underground_station t1
			LEFT JOIN underground_station_4_clinic t2 ON t2.undegraund_station_id = t1.id
			LEFT JOIN underground_line t3 ON t3.id = t1.underground_line_id
			WHERE t2.clinic_id = {$id}
			GROUP BY t1.rewrite_name
			ORDER BY t1.name
		";
		$result = query($sql);
		if (num_rows($result) > 0) {
			$k = 0;
			while ($row = fetch_object($result)) {
				$data[$k]['Id'] = $row->id;
				$data[$k]['Name'] = $row->name;
				$data[$k]['LineName'] = $row->lineName;
				$data[$k]['LineColor'] = $row->lineColor;
				$data[$k]['CityId'] = $row->city_id;
				$k++;
			}
		}

		return $data;
	}

	/*
	 * Получение списка процедур
	 * @param integer $id
	 * @return array
	 */
	static function getDiagnosticList($id)
	{
		$resultArr = array();

		$id = intval($id);

		$sql = "SELECT
					t2.id,
					CASE
					WHEN t3.name IS NOT NULL
						THEN CONCAT(t3.name,' ',t2.name)
						ELSE t2.name
					END AS name,
					t1.price, t1.special_price
				FROM diagnostica4clinic t1
				LEFT JOIN diagnostica t2 ON t2.id=t1.diagnostica_id
				LEFT JOIN diagnostica t3 ON t3.id=t2.parent_id
				WHERE t1.clinic_id=" . $id;
		$result = query($sql);

		if (num_rows($result) > 0) {
			$k = 0;
			while ($row = fetch_object($result)) {
				$resultArr[$k]['Id'] = $row->id;
				$resultArr[$k]['Name'] = $row->name;
				$resultArr[$k]['Price'] = $row->price;
				$resultArr[$k]['SpecialPrice'] = $row->special_price;
				$k++;
			}
		}

		return $resultArr;
	}

	/*
	 * Получает список специальностей для клиники
	 *
	 * @param int $id идентификатор клиники
	 *
	 * @return string[]
	 */
	static function getSpecialityList($id)
	{
		$data = SectorModel::model()
			->cache(86400)
			->byClinic($id)
			->findAll([ 'order' => 't.id' ]);

		$result = [];
		foreach ($data as $speciality) {
			$result[] = [
				'Id' => $speciality->id,
				'Name' => $speciality->name,
				'Alias' => $speciality->rewrite_name,
			];
		}

		return $result;
	}

	/*
	 * Получение списка клиник про id врача
	 * @param integer $id
	 * @return array
	 */
	static function getItemsByDoctorId($id = 0)
	{
		$sql = "SELECT t1.id AS Id, t1.name AS Name
				FROM clinic t1
				INNER JOIN doctor_4_clinic t2 ON t2.clinic_id=t1.id and t2.type = " . DoctorClinicModel::TYPE_DOCTOR . "
				WHERE t2.doctor_id=" . $id;
		//echo $sql;
		$result = query($sql);
		$data = array();
		$i = 0;
		while ($row = fetch_array($result)) {
			$data[$i] = $row;
			$i++;
		}

		return $data;
	}

	/*
	 * Количество врачей в выборке
	 * @param array $params
	 * @return integer
	 */
	static function getCount($params)
	{
		$sqlAdd = "1=1";
		$addJoin = "";

		if (!isset($params['city']) || empty($params['city'])) {
			$params['city'] = 1;
		}

		$sqlAdd .= " AND t1.status = 3 "; // Только активные

		if (count($params) > 0) {

			if (isset($params['isDiagnostic']) || isset($params['isClinic']) || isset($params['isDoctor'])) {
				$sqlAdd .= " AND (0 ";
				if (isset($params['isDiagnostic'])) {
					$sqlAdd .= "OR t1.isDiagnostic = '{$params['isDiagnostic']}' ";
				}
				if (isset($params['isClinic'])) {
					$sqlAdd .= "OR t1.isClinic = '{$params['isClinic']}' ";
				}
				if (isset($params['isDoctor'])) {
					$sqlAdd .= "OR t1.isPrivatDoctor = '{$params['isDoctor']}' ";
				}
				$sqlAdd .= ")";
			}

			if (isset($params['open_4_yandex'])) {
				$sqlAdd .= " AND t1.open_4_yandex = '" . $params['open_4_yandex'] . "' ";
			}

			$speciality = isset($params['speciality']) ? intval($params['speciality']) : 0;

			if (isset($params['withDoctors']) || $speciality > 0) {
				$addJoin .= " INNER JOIN doctor_4_clinic t2 ON t2.clinic_id=t1.id and t2.type = " . DoctorClinicModel::TYPE_DOCTOR . " ";
				$addJoin .= " INNER JOIN doctor t3 ON t3.id=t2.doctor_id ";
				$sqlAdd .= " AND t3.status=3 ";
			}

			if ($speciality > 0) {
				$addJoin .= ' INNER JOIN doctor_sector ds ON (ds.doctor_id = t2.doctor_id) ';
				$sqlAdd .= " AND ds.sector_id = {$speciality} ";
			}

			if (isset($params['diagnostic']) && intval($params['diagnostic']) > 0) {
				$subSQL =
					"SELECT id FROM diagnostica WHERE id=" . $params['diagnostic'] . " OR parent_id=" .
					$params['diagnostic'];
				$sqlAdd .= " AND t2.diagnostica_id IN (" . $subSQL . ") ";
				$addJoin .= " LEFT JOIN diagnostica4clinic t2 ON (t2.clinic_id = t1.id) ";
			}

			if (isset($params['stations'])) {
				if (!isset($params['near'])) {
					$params['near'] = 'strict';
				}

				if (count($params['stations']) > 0) {

					$params['stations'] = array_map(
						function ($v) {
							return (int)$v;
						},
						$params['stations']
					);

					$addJoin .= " INNER JOIN underground_station_4_clinic t4 ON (t4.clinic_id = t1.id) ";
					if ($params['near'] == 'strict') {
						$sqlAdd .= " AND t4.undegraund_station_id IN (" . implode(',', $params['stations']) . ")";
					} elseif ($params['near'] == 'closest') {
						$addJoin .= " INNER JOIN closest_station t5 ON (t5.closest_station_id = t4.undegraund_station_id) ";
						$sqlAdd .=
							" AND t5.station_id IN (" . implode(',', $params['stations']) . ") AND t5.priority<>0";
					} elseif ($params['near'] == 'mixed') {
						$addJoin .= " LEFT JOIN closest_station t5 ON (t5.closest_station_id = t4.undegraund_station_id) ";
						$sqlAdd .= " AND t5.station_id IN (" . implode(',', $params['stations']) . ") ";
					}
				}
			}

		}

		if (isset($params['city'])) {
			$sqlAdd .= " AND t1.city_id = " . intval($params['city']) . " ";
		}

		if (!empty($params['street'])) {
			$sqlAdd .= ' AND t1.street_id = ' . intval($params['street']);
		}

		$sql = "SELECT
				    DISTINCT t1.id
				FROM clinic t1
				 " . $addJoin . "
				WHERE " . $sqlAdd;

		//echo $sql;
		$result = query($sql);

		return num_rows($result);
	}

	/**
	 *
	 * Метод сохранения расписания клиники
	 */
	public function saveSchedule($params)
	{
		if ($this->id > 0) {
			$sql = "DELETE FROM clinic_schedule WHERE clinic_id = " . $this->id;
			$result = query($sql);

		}
	}

	public function getClinicXML()
	{
		$xml = "";
		if ($this->id > 0) {
			$xml .= "<Clinic id=\"" . $this->id . "\">";
			$xml .= "<CrDate>" . $this->crDate . "</CrDate>";
			$xml .= "<ParentClinicId>" . $this->parentClinicId . "</ParentClinicId>";
			$xml .= "<Title><![CDATA[" . $this->title . "]]></Title>";
			$xml .= "<ShortName><![CDATA[" . $this->shortName . "]]></ShortName>";
			$xml .= "<RewriteName><![CDATA[" . $this->rewriteName . "]]></RewriteName>";
			$xml .= "<URL><![CDATA[" . $this->URL . "]]></URL>";
			//$xml .= "<Rating>".$this -> rating."</Rating>";
			$xml .= "<Phone digits=\"" . formatPhone4DB($this->phone) . "\">" . $this->phone . "</Phone>";
			$xml .=
				"<AsteriskPhone digits=\"" .
				formatPhone4DB($this->asteriskPhone) .
				"\">" .
				formatPhone($this->asteriskPhone) .
				"</AsteriskPhone>";
			$xml .= "<PhoneAppointment>" . $this->phoneAppointment . "</PhoneAppointment>";
			$xml .= "<ContactName><![CDATA[" . $this->contactName . "]]></ContactName>";
			$xml .= "<Description><![CDATA[" . $this->description . "]]></Description>";
			$xml .= "<ShortDescription><![CDATA[" . $this->shortDescription . "]]></ShortDescription>";
			$xml .= "<OperatorComment><![CDATA[" . $this->operatorComment . "]]></OperatorComment>";
			$xml .= "<Email>" . $this->email . "</Email>";

			$xml .= "<Longitude>" . $this->longitude . "</Longitude>";
			$xml .= "<Latitude>" . $this->latitude . "</Latitude>";
			$xml .= "<Age>" . $this->age . "</Age>";
			$xml .= "<IsDiagnostic>" . $this->isDiagnostic . "</IsDiagnostic>";
			$xml .= "<IsClinic>" . $this->isClinic . "</IsClinic>";
			$xml .= "<IsPrivatDoctor>" . $this->isPrivatDoctor . "</IsPrivatDoctor>";
			$xml .= "<City>" . $this->city . "</City>";
			$xml .= "<House>" . $this->house . "</House>";
			$xml .= "<Street>" . $this->street . "</Street>";
			$xml .= "<SortPosition>" . $this->sortPosition . "</SortPosition>";
			$xml .= "<LogoPath>" . $this->logoPath . "</LogoPath>";

			$xml .= "<YaAPI>" . $this->yaAPI . "</YaAPI>";
			$xml .= "<ShowSchedule>" . $this->showSchedule . "</ShowSchedule>";
			$xml .= "<SendSMS>" . $this->sendSMS . "</SendSMS>";

			$xml .= "<WayOnFoot><![CDATA[" . $this->wayOnFoot . "]]></WayOnFoot>";
			$xml .= "<WayOnCar><![CDATA[" . $this->wayOnCar . "]]></WayOnCar>";

			if (count($this->schedule) > 0) {
				$xml .= "<Schedule>";
				foreach ($this->schedule as $schLine) {
					if (count($schLine) == 3) {
						$xml .= "<Element id=\"" . $schLine[0] . "\">";
						if (preg_match("/\d{2}:\\d{2}/", $schLine[1], $matches)) {
							$xml .= "<StartTime>" . $matches[0] . "</StartTime>";
						}
						if (preg_match("/\d{2}:\\d{2}/", $schLine[2], $matches)) {
							$xml .= "<EndTime>" . $matches[0] . "</EndTime>";
						}
						$xml .= "</Element>";
					}
				}
				$xml .= "</Schedule>";
			}

			if (count($this->rating) > 0) {
				$xml .= "<Rating>";
				$xml .= "<Rating id='0' value='" . $this->rating["r1"] . "'/>";
				$xml .= "<Rating id='1' value='" . $this->rating["r2"] . "'/>";
				$xml .= "<Rating id='2' value='" . $this->rating["r3"] . "'/>";
				$xml .= "<Rating id='3' value='" . $this->rating["r4"] . "'/>";
				$xml .= "<Rating id='total' value='" . $this->rating["rTotal"] . "'/>";

				$xml .= "</Rating>";
			}

			if (count($this->settings) > 0) {
				$xml .= "<Settings>";
				foreach ($this->settings as $key => $data) {
					$xml .= "<" . $key . ">" . $data . "</" . $key . ">";
				}
				$xml .= "</Settings>";
			}

			if (count($this->diagSettings) > 0) {
				$xml .= "<DiagSettings>";
				foreach ($this->diagSettings as $key => $data) {
					$xml .= "<" . $key . ">" . $data . "</" . $key . ">";
				}
				$xml .= "</DiagSettings>";
			}

			$photos = ClinicPhotoModel::model()->byClinic($this->id)->findAll();
			if ($photos) {
				$xml .= "<Photos>";
				foreach ($photos as $photo) {
					$xml .= '<Element id="' . $photo->img_id . '">';
					$xml .= "<ImgPath>" . $photo->imgPath . "</ImgPath>";
					$xml .= "<Url>" . $photo->getUrl() . "</Url>";
					$xml .= "<Description>" . $photo->description . "</Description>";
					$xml .= "</Element>";
				}
				$xml .= "</Photos>";
			}

			$xml .= "</Clinic>";
		}

		return $xml;
	}
}

/**
 *
 * Функция получает массив данных справочника контраков
 */
function getContractTypeDict($type = array('isClinic' => 'yes', 'isDiagnostic' => 'no'))
{
	$data = array();

	$sqlAdd = " 1 = 1 ";
	if ($type['isClinic'] == 'yes') {
		$sqlAdd .= " AND isClinic = 'yes' ";
	} else {
		$sqlAdd .= " AND isClinic = 'no' ";
	}

	if ($type['isDiagnostic'] == 'yes') {
		$sqlAdd .= " AND isDiagnostic = 'yes' ";
	} else {
		$sqlAdd .= " AND isDiagnostic = 'no' ";
	}

	$sql = "SELECT
					t1.contract_id as id, 
					t1.title
				FROM contract_dict  t1
				WHERE  
				" . $sqlAdd . "
				ORDER BY t1.title";
	$result = query($sql);
	if (num_rows($result) > 0) {

		while ($row = fetch_array($result)) {
			array_push($data, $row);
		}
	}

	return $data;
}

/**
 *
 * Функция получает XML структуру справочника контраков
 */
function getContractTypeDictXML()
{
	$xml = "";

	$sql = "SELECT
					t1.contract_id as id, 
					t1.title
				FROM contract_dict  t1
				ORDER BY t1.title";
	$result = query($sql);
	if (num_rows($result) > 0) {
		$xml .= "<ContractDict>";
		while ($row = fetch_object($result)) {
			$xml .= "<Element id=\"" . $row->id . "\">" . $row->title . "</Element>";
		}
		$xml .= "</ContractDict>";
	}

	return $xml;
}

/**
 *
 * Функция получает XML структуру справочника контраков
 */
function getClinicLisFromArrayXML($clinicList = array())
{
	$xml = "";
	$str = "";
	$clinicListWithBranches = array();

	if (count($clinicList) > 0) {
		foreach ($clinicList as $clinic) {
			$str .= intval($clinic) . ",";
		}
		$str .= rtrim($str, ",");
	}

	if (strlen($str) > 0) {
		$sql = "SELECT
						t1.id as id, 
						t1.parent_clinic_id as parent_id,
						t1.name
					FROM clinic t1
					WHERE 
						t1.id IN (" . $str . ")
						OR 
						t1.parent_clinic_id IN (" . $str . ")
					GROUP BY t1.id";
		$result = query($sql);
		if (num_rows($result) > 0) {
			$xml .= "<ClinicList>";
			while ($row = fetch_object($result)) {
				$xml .=
					"<Element id=\"" .
					$row->id .
					"\" parentId=\"" .
					$row->parent_id .
					"\">" .
					$row->name .
					"</Element>";
			}
			$xml .= "</ClinicList>";
		}
	}

	return $xml;
}

/**
 * Получает список клиник в зависимости от входящих параметров
 */
function getClinicLisByParams($params = array())
{
	$data = array();

	$sqlAdd = "";

	if (count($params) > 0) {
		if (isset($params['city']) && !empty ($params['city'])) {
			$sqlAdd .= " AND t1.city_id = " . $params['city'] . " ";
		}

		if (isset($params['title']) && !empty ($params['title'])) {
			$sqlAdd .=
				" AND ( LOWER(t1.name) LIKE  '%" .
				strtolower($params['title']) .
				"%' OR LOWER(t1.short_name) LIKE  '%" .
				strtolower($params['title']) .
				"%' ) ";
		}

		if (isset($params['status']) && !empty ($params['status'])) {
			$sqlAdd .= " AND t1.status = " . $params['status'] . " ";
		}

		if (isset($params['type']) && !empty ($params['type'])) {
			switch ($params['type']) {
				case 'clinic' :
					$sqlAdd .= " AND t1.isClinic = 'yes' ";
					break;
				case 'center' :
					$sqlAdd .= " AND t1.isDiagnostic = 'yes' ";
					break;
				case 'privatDoctor' :
					$sqlAdd .= " AND t1.isPrivatDoctor = 'yes' ";
					break;
			}
		}

		if (isset($params['branch']) && $params['branch']) {

		} else {
			if (isset($params['branch']) && !$params['branch']) {
				// без филиалов
				$sqlAdd .= " AND t1.parent_clinic_id = 0 ";
			}
		}

		if (isset($params['clinicNotInList']) && count($params['clinicNotInList']) > 0) {
			$str = "";
			foreach ($params['clinicNotInList'] as $item) {
				$str .= intval($item) . ",";
			}
			$str .= rtrim($str, ",");
			$sqlAdd .= " AND t1.id NOT IN (" . $str . ") ";
		}
	} else {
		$sqlAdd .= " 1 = 0 ";
	}

	$sql = "	SELECT
						t1.id, t1.parent_clinic_id, t1.name, t1.short_name, t1.rewrite_name, t1.status, 
						t1.phone, t1.phone_appointment, t1.asterisk_phone, t1.url, t1.email, t1.contact_name, 
						t1.rating, t1.rating_total, 
						t1.age_selector as age, 
						DATE_FORMAT( t1.created,'%d.%m.%Y') AS crDate,
						t1.isDiagnostic, t1.isClinic, t1.isPrivatDoctor
					FROM clinic  t1
					WHERE 1 =1 " . $sqlAdd . "
					ORDER BY t1.name ASC";
	//echo $sql;

	$result = query($sql);
	if (num_rows($result) > 0) {
		while ($row = fetch_array($result)) {
			array_push($data, $row);
		}
	}

	return $data;
}

