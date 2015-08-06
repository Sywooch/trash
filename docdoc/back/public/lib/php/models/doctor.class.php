<?php

use dfs\docdoc\models\StreetModel;
use dfs\docdoc\models\RatingModel;
use dfs\docdoc\models\RatingStrategyModel;
use dfs\docdoc\models\TipsMessageModel;
use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\models\DoctorModel;
use dfs\docdoc\models\DoctorClinicModel;
use dfs\docdoc\objects\Coordinate;

require_once dirname(__FILE__) . "/../dateTimeLib.php";

class Doctor
{
	/**
	 * Оценка по умолчанию
	 *
	 * @var int
	 */
	const DEFAULT_OPINION = 4;

	public $id = null;
	public $data;

	public static $ratingWords = array(
		1 => 'Плохо',
		2 => 'Ниже среднего',
		3 => 'Нормально',
		4 => 'Хорошо',
		5 => 'Отлично',
	);


	public function __construct($alias = null)
	{
		if (!empty($alias))
			$this->getModel($alias);
	}


	public function setId($id)
	{
		$id = intval($id);

		if ($id > 0)
			$this->id = $id;
	}

	/**
	 * Получение модели врача
	 *
	 * @param $alias
	 * @return array
	 */
	public function getModel($alias)
	{
		$sqlAdd = '';

		$onlineBooking = Yii::app()->params['onlineBooking'] ? 'd4c.has_slots' : '0';

		$sql = "SELECT
					t1.id AS Id, t1.name AS Name, t1.rewrite_name AS Alias,
					t1.departure AS Departure, t1.kids_reception AS KidsReception, t1.price AS Price, t1.special_price AS SpecialPrice,
					t1.text AS Description, t1.text_spec AS TextSpec, t1.text_education AS TextEducation,
					t1.text_course AS TextExtEducationDB, t1.text_experience AS TextExperienceDB,
					t1.text_association AS TextAssociation,
					t1.experience_year AS ExperienceYear,
					t1.rating AS ManualRating, t1.total_rating AS TotalRating,
					t1.status AS Status,
					t2.status AS ClinicStatus,
					t2.latitude AS Latitude, t2.longitude AS Longitude,
					concat(t2.street, ', ', t2.house) AS ClinicAddress, 
					t2.city_id AS CityId,
					t2.id AS ClinicId, t2.name AS ClinicName, t2.asterisk_phone AS PhoneNumber,
					t1.image as Image,
					CASE 
					    WHEN (t1.image IS NULL OR t1.image='') AND t1.sex=1 THEN 'avatar_m.gif'
					    WHEN (t1.image IS NULL OR t1.image='') AND t1.sex=2 THEN 'avatar_w.gif'
					    ELSE CONCAT(t1.id,'_med.jpg')
					END AS MedImg,
					{$onlineBooking} as CanOnlineBooking
				FROM doctor t1
				LEFT JOIN  doctor_4_clinic d4c ON (t1.id = d4c.doctor_id and d4c.type = " . DoctorClinicModel::TYPE_DOCTOR . ")
				LEFT JOIN clinic t2 ON (d4c.clinic_id = t2.id)
				LEFT JOIN api_doctor ad on ad.id = d4c.doc_external_id
				WHERE 
				(	t1.id='" . $alias . "'
					OR
					t1.rewrite_name='$alias'
				)
				AND
				t1.status IN (3,4) " . $sqlAdd . "
				GROUP BY t1.id
				ORDER BY t1.status";

		$data = array();
		$result = query($sql);

		if (num_rows($result) >= 1) {
			$row = fetch_array($result);
			array_push($data, $row);

			$this->data = $data[0];
			$this->id = $data[0]['Id'];

			$this->data['Phone']['Digit'] = $row['PhoneNumber'];
			$this->data['Phone']['Text'] = formatPhone($row['PhoneNumber']);

			$this->data['TextAssociation'] = formatTextField($row['TextAssociation']);
			$this->data['TextExtEducation'] = formatTextField($row['TextExtEducationDB']);
			$this->data['TextExperience'] = formatTextField($row['TextExperienceDB']);

			$this->data['Experience'] = self::getExperience($row['ExperienceYear']);
			$rating = (!empty($row['ManualRating'])) ? $row['ManualRating'] : $row['TotalRating'];
			$this->data['EditedRating'] = self::getRating($rating);
			$this->data['EditedRating2'] = self::getRating($rating * 2);
			$this->data['RatingsByReviews'] = $this->getAvgRatingsByReviews();
			$this->data['Degree'] = $this->getDegree();
			$this->data['ClearDegree'] = str_replace('.', '', trim($this->getDegree()));
			$this->data['SpecList'] = $this->getSpecList();
			$this->data['StationList'] = self::getStationForDoctorById($row['Id']);
			$this->data['ReviewList'] = $this->getReviewList();
			$this->data['EducationList'] = $this->getEducationList();
			$this->data['ClinicList'] = $this->getClinics($this->id);
			$this->data['ClinicCount'] = count($this->data['ClinicList']);

			$spec = array();
			foreach ($this->data['SpecList'] as $item) {
				$spec[] = $item['Name'];
			}
			$this->data['Spec'] = implode(', ', $spec);

			if ($row['Status'] == DoctorModel::STATUS_ACTIVE && $row['ClinicStatus'] == ClinicModel::STATUS_ACTIVE) {
				$tipMessage = TipsMessageModel::model()->findRandomForRecord($this->id);
				if ($tipMessage) {
					$this->data['Tips'] = [
						'Message' => $tipMessage->getMessage(),
						'Color'   => $tipMessage->tips->color,
					];
				}
			}
		}
		return $data;
	}

	/**
	 * Получение списка клиник
	 *
	 * @param int  $doctorId
	 *
	 * @return array
	 */
	static function getClinics($doctorId)
	{
		$data = array();

		$sql = "SELECT
					DISTINCT t1.id, t1.name,
					t1.status AS ClinicStatus,
					t1.latitude AS Latitude, t1.longitude AS Longitude,
					concat(t1.street, ', ', t1.house) AS ClinicAddress, 
					t1.city_id AS CityId
				FROM clinic t1
				INNER JOIN doctor_4_clinic t2 ON t2.clinic_id=t1.id and t2.type = " . DoctorClinicModel::TYPE_DOCTOR . "
				WHERE t1.status != " . ClinicModel::STATUS_BLOCKED . " AND t2.doctor_id=" . $doctorId;
		$result = query($sql);
		while ($row = fetch_array($result)) {
			$row['StationList'] = self::getStationByClinicId($row['id']);

			array_push($data, $row);
		}

		return $data;
	}


	/**
	 * Получение опыта работы
	 *
	 * @param $year
	 * @return string
	 */
	static function getExperience($year)
	{
		if (!empty($year)) {
			$years = date('Y') - $year;
			return $years ? $years . ' ' . RussianTextUtils::caseForNumber($years, array('год', 'года', 'лет')) : 'нет';
		} else
			return 'нет';
	}

	/**
	 * Получение списка отзывов
	 * @return array
	 */
	public function getReviewList()
	{
		$sql = "SELECT
					created AS Created,
					DATE_FORMAT(t1.created, '%d.%m.%Y') AS CrDate,
					name AS Name, text AS Review, age AS Age,
					rating_attention AS RatAttention, rating_qualification AS RatQualification, 
					rating_room AS RatRoom
				FROM doctor_opinion t1
				WHERE 
					allowed=1
					AND
					status='enable'
					AND 
					doctor_id=" . $this->id . "
				ORDER BY created DESC";

		$data = array();
		$result = query($sql);
		while ($row = fetch_array($result)) {
			$dateArray = explode(".", $row['CrDate']);
			if (count($dateArray) == 3) {
				$row['FormatedDate'] =
					$dateArray[0] . " " . getRusMonth(intval($dateArray[1]), 'genitive') . " " . $dateArray[2];
			}
			$totalRating = ($row['RatAttention'] + $row['RatQualification'] + $row['RatRoom']) / 3;
			$row['RatInWord'] = self::getRatingInWord($totalRating);
			array_push($data, $row);
		}

		return $data;
	}

	/**
	 * Получение среднего рейтинга по отзывам врача
	 * @return array
	 */
	public function getAvgRatingsByReviews()
	{
		$data = array();

		$sql = "SELECT
					ROUND(AVG(rating_attention), 1) AS RatAttention,
					ROUND(AVG(rating_qualification), 1) AS RatQualification,
					ROUND(AVG(rating_room), 1) AS RatRoom
				FROM doctor_opinion t1
				WHERE
					doctor_id={$this->id}
					AND
					allowed=1
					AND
					status='enable'
					AND
					origin <> 'editor'";

		$result = query($sql);
		$row = fetch_array($result);

		$row['RatAttention'] = $row['RatAttention'] ? : self::DEFAULT_OPINION;
		$row['RatQualification'] = $row['RatQualification'] ? : self::DEFAULT_OPINION;
		$row['RatRoom'] = $row['RatRoom'] ? : self::DEFAULT_OPINION;

		$data = $row;
		$totalRating = ($data['RatAttention'] + $data['RatQualification'] + $data['RatRoom']) / 3;
		$data['RatInWord'] = self::getRatingInWord($totalRating);

		return $data;
	}

	/**
	 * Получение словесной оценки
	 *
	 * @param float $rating
	 *
	 * @return string
	 */
	public static function getRatingInWord($rating)
	{
		$rating = round($rating);
		$rating = ($rating > 0) ? $rating : 1;
		$rating = self::$ratingWords[$rating];

		return $rating;
	}


	/**
	 * Получение списка образований
	 *
	 * @return array
	 */
	public function getEducationList()
	{
		$sql = "SELECT
					t1.education_id as id, t1.year, 
					t2.title, t2.type
				FROM education_4_doctor t1, education_dict t2
				WHERE
					t1.education_id = t2.education_id
					AND t1.doctor_id = " . $this->id . "
				ORDER BY t1.year";

		$data = array();
		$result = query($sql);
		while ($row = fetch_array($result))
			array_push($data, $row);

		return $data;
	}


	/**
	 *
	 * Получение списка специальностей для врача
	 *
	 * @return array
	 */
	public function getSpecList()
	{
		$sql = "SELECT t0.id AS Id, t0.name As Name, LOWER(t0.name) As LowerName, t0.rewrite_name AS Alias
				FROM sector t0
				INNER JOIN doctor_sector AS t1 ON t1.sector_id=t0.id
				WHERE t1.doctor_id=" . $this->id . "
				GROUP BY t0.id";

		$data = array();
		$result = query($sql);
		$i = 0;
		while ($row = fetch_array($result)) {
			$data[$i]['Id'] = $row['Id'];
			$data[$i]['Name'] = $row['Name'];
			$data[$i]['Alias'] = $row['Alias'];
			$data[$i]['NameInGenitive'] = $this->nameInGenitive($row['LowerName'], true);
			$i++;
		}

		return $data;
	}

	public function nameInGenitive($word, $many = false)
	{
		return $this->parseWords($word, $many, 'wordGenitive');
	}

	protected function parseWords($words, $many, $callback)
	{
		return preg_replace_callback(
			'/([a-zа-яё]+)/u',
			function ($matches) use ($callback, $many) {
				return $this->{$callback}($matches[1], $many);
			},
			$words
		);
	}

	protected function replaceEnding($word, $endings)
	{
		foreach ($endings as $endingFrom => $endingTo) {
			if (preg_match('/' . $endingFrom . '$/u', $word)) {
				return preg_replace('/' . $endingFrom . '$/u', $endingTo, $word);
			}
		}
	}

	protected function wordGenitive($word, $many)
	{
		if (!$many) {
			return $this->replaceEnding($word, array(
					'р' => 'ра',
					'г' => 'га',
					'т' => 'та',
					'д' => 'да',
					'ий' => 'ого',
					'ый' => 'ого',
				));
		} else {
			return $this->replaceEnding($word, array(
					'р' => 'ров',
					'г' => 'гов',
					'т' => 'тов',
					'д' => 'дов',
					'ий' => 'их',
					'ый' => 'ых',
				));
		}

	}


	/**
	 * Получение списка станций метро
	 *
	 * @return array
	 */
	public function getStationList()
	{
		$sql = "SELECT t1.id AS Id, t1.name AS Name, t1.rewrite_name AS Alias
				FROM underground_station t1
				INNER JOIN underground_station_4_clinic t2 ON t2.undegraund_station_id=t1.id
				INNER JOIN doctor_4_clinic t3 ON (t3.clinic_id=t2.clinic_id and t3.type = " . DoctorClinicModel::TYPE_DOCTOR . ")
				WHERE t3.doctor_id=" . $this->id . "
				GROUP BY Alias
				ORDER BY name";

		$data = array();
		$result = query($sql);
		while ($row = fetch_array($result))
			array_push($data, $row);

		return $data;
	}

	/**
	 * Получение отредактированного рейтинга
	 *
	 * @param $rating
	 * @return array
	 */
	static function getRating($rating)
	{
		$data = array();

		$data[0]['Value'] = str_replace(',', '.', round($rating, 1));
		$data[0]['BeforeDot'] = (int)$data[0]['Value'];
		$data[0]['AfterDot'] = ($data[0]['Value'] - $data[0]['BeforeDot']) * 10;

		return $data;
	}

	/**
	 * Получение рейтинга врача
	 *
	 * @return float
	 */
	public function getManualRating()
	{
		if (empty($this->data['ManualRating'])) {
			return str_replace(',', '.', round($this->data['TotalRating'], 1));
		} else
			return $this->data['ManualRating'];
	}

	/**
	 * Получение данных об ученой степени
	 *
	 * @return string
	 */
	public function getDegree()
	{
		return self::getDegreeById($this->id);
	}

	static function getDegreeById($id)
	{
		$sql = "SELECT t1.text_degree, t2.title AS category, t3.title AS degree, t4.title AS rank
				FROM doctor t1
				LEFT JOIN category_dict t2 ON t2.category_id=t1.category_id
				LEFT JOIN degree_dict t3 ON t3.degree_id=t1.degree_id
				LEFT JOIN rank_dict t4 ON t4.rank_id=t1.rank_id
				WHERE t1.id=" . $id . "
				";
		$result = query($sql);
		$item = fetch_object($result);

		$result = [];
		if ($item->category) {
			$result[] = $item->category;
		}
		if ($item->degree) {
			$result[] = $item->degree;
		}
		if ($item->rank) {
			$result[] = $item->rank;
		}

		return count($result) > 0 ? implode(', ', $result) : $item->text_degree;
	}

	/**
	 * Получение отзывов
	 *
	 * @return array
	 */
	public function getReviews()
	{
		$data = array();

		if ($this->id > 0) {
			$sql = "SELECT
						id, created, name, text, age,
						rating_attention, rating_qualification, rating_room
					FROM doctor_opinion t1
					WHERE 
						allowed=1 
						AND 
						status='enable'
						AND 
						doctor_id = " . $this->id;

			$data = array();
			$result = query($sql);
			while ($row = fetch_array($result))
				array_push($data, $row);
		}

		return $data;

	}

	static function getReviewsById($id)
	{
		$data = array();

		if ($id > 0) {
			$sql = "SELECT
						id, created, name, text, age,
						rating_attention, rating_qualification, rating_room
					FROM doctor_opinion t1
					WHERE 
						allowed=1 
						AND 
						status='enable'
						AND 
						doctor_id = " . $id;

			$data = array();
			$result = query($sql);
			while ($row = fetch_array($result))
				array_push($data, $row);
		}

		return $data;

	}

	/**
	 * Получение списка врачей
	 *
	 * @param array $params
	 * @param bool $withPager
	 * @return array
	 */
	static function getItems($params = array(), $withPager = false)
	{
		$sqlAdd = "";
		$addJoin = "";
		$order = [];
		$limit = "";
		$select = "";

		$detailed = isset($params['detailed']) ? $params['detailed'] : true;
		$startPage = isset($params['page']) ? (int)$params['page'] : 1;
		$status = isset($params['status']) ? implode(',', $params['status']) : null;
		$step = 10;

		$ratingStrategyId = (int)Yii::app()->rating->getId(RatingStrategyModel::FOR_DOCTOR);

		if (isset($params['orderType']) && isset($params['orderDir'])) {

			$orderDir = strtolower($params['orderDir']) === 'asc' ? 'ASC' : 'DESC';

			switch ($params['orderType']) {
				case 'price':
					$orderCond = 'RealPrice';
					break;

				case 'experience':
					$orderCond = 'CaseExperienceYear';
					break;

				case 'rating':
					$orderCond = 'SortRating';
					break;

				case 'rating_internal':
				default:
					$orderCond = 'RatingInternal';
					break;
			}

			$orderCond = $orderCond . ' ' . $orderDir;

			array_push($order, $orderCond);

		} else {
			$order = ['r.rating_value desc'];
		}

		if (isset($params['city'])) {
			$sqlAdd .= " t2.city_id = " . intval($params['city']) . " ";
		} else {
			$sqlAdd .= " t2.city_id = 1 ";
		}

		if (is_null($status)) {
			$sqlAdd .= " AND t1.status = 3
					AND (t2.isClinic = 'yes' OR t2.isPrivatDoctor = 'yes') "; // Только активные
		} else {
			$sqlAdd .= " AND t1.status IN ({$status}) AND t1.status != " . DoctorModel::STATUS_ANOTHER_DOCTOR . " ";
		}

		//по умолчанию показываю только врачей для активных клиник,
		//но если передан "левый" статус, то показываю и заблокированных врачей врачей
		$clinicStatus = isset($params['clinicStatus']) ? $params['clinicStatus'] : ClinicModel::STATUS_ACTIVE;

		if ($clinicStatus == ClinicModel::STATUS_ACTIVE) {
			$sqlAdd .= ' AND t2.status = ' . ClinicModel::STATUS_ACTIVE;
		} else {
			$sqlAdd .= ' AND t2.status in ( ' . ClinicModel::STATUS_ACTIVE . ', ' . ClinicModel::STATUS_BLOCKED . ')';
		}

		if (count($params) > 0) {

			if (isset($params['searchWord'])) {
				$searchWord = strtoupper($params['searchWord']);
				$sqlAdd .= " AND (UPPER(t1.name) LIKE '%$searchWord%'
					         OR UPPER(t1.text) LIKE '%$searchWord%'
					         OR UPPER(t1.text_degree) LIKE '%$searchWord%'
					         OR UPPER(t1.text_education) LIKE '%$searchWord%'
					         OR UPPER(t1.text_association) LIKE '%$searchWord%'
					         OR UPPER(t4.name) LIKE '%$searchWord%')";
				$addJoin .= " INNER JOIN doctor_sector t3 ON (t3.doctor_id = t1.id) ";
				$addJoin .= " INNER JOIN sector t4 ON (t4.id = t3.sector_id) ";
			}

			if (isset($params['departure'])) {
				$sqlAdd .= " AND t1.departure=1";
			}

			$onlineBookingSql = "
					d4c.has_slots = 1
					";

			if (isset($params['booking']) && $params['booking']) {
				$sqlAdd .= " AND " . $onlineBookingSql;
			}

			if (isset($params['kidsReception'])) {
				$sqlAdd .= " AND t1.kids_reception=1";
			}

			if (isset($params['speciality']) && intval($params['speciality']) > 0) {
				$sqlAdd .= " AND t3.sector_id = " . intval($params['speciality']) . " ";
				$addJoin .= " INNER JOIN doctor_sector t3 ON (t3.doctor_id = t1.id) ";
			}

			if (isset($params['stations'])) {

				if (!isset($params['near']) || !in_array($params['near'], ['strict', 'mixed', 'closest'])) {
					$params['near'] = 'strict';
				}

				if (count($params['stations']) > 0) {

					$params['stations'] = array_map(function ($v) {
							return (int)$v;
						}, $params['stations']);

					$stations = implode(',', $params['stations']);
					$addJoin .= " INNER JOIN underground_station_4_clinic t4 ON (t4.clinic_id = t2.id) ";

					if ($params['near'] == 'closest') {
						$select .= " , MIN(t5.priority) AS sortByStations";
						$addJoin .= " INNER JOIN closest_station t5 ON (t5.closest_station_id = t4.undegraund_station_id) ";
						$sqlAdd .= " AND t5.station_id IN (" . $stations . ") ";

						if (isset($params['orderType'])) {
							array_push($order, "sortByStations");
						} else {
							$order = ['sortByStations'];
						}
					} elseif ($params['near'] == 'mixed') {
						$select .= " , MIN(t5.priority) AS sortByStations";
						$addJoin .= " LEFT JOIN closest_station t5 ON (t5.closest_station_id = t4.undegraund_station_id) ";
						$sqlAdd .= " AND t5.station_id IN (" . $stations . ") ";
						array_push($order, "sortByStations");
					} else {
						$sqlAdd .= " AND t4.undegraund_station_id IN (" . $stations . ") ";
					}
				}
			}

			//поиск по району делаем через принадлежность клинки к району
			if (isset($params['district_id'])) {
				if (!isset($params['near'])) {
					$params['near'] = 'strict';
				}

				if ($params['near'] == 'strict') {
					$sqlAdd  .= " AND t2.district_id = " . (int)$params['district_id'];
				} elseif ($params['near'] == 'closest') {
					$addJoin .= " INNER JOIN closest_district t7 ON t2.district_id = t7.closest_district_id ";
					$sqlAdd .= " AND t7.district_id = " . (int)$params['district_id'] . " AND t7.priority > 0 ";
					if (isset($params['orderType'])) {
						array_push($order, "t7.priority");
					}
					else {
						$order = ['t7.priority'];
					}

				} elseif ($params['near'] == 'mixed') {
					$addJoin .= " LEFT JOIN closest_district t7 ON t2.district_id = t7.closest_district_id ";
					$sqlAdd .= " AND t7.district_id = " . (int)$params['district_id'] ;
					array_push($order, "t7.priority");
				}
			}

			if (isset($params['regCityAlias']) || isset($params['areaAlias'])) {
				$addJoin .= " LEFT JOIN underground_station_4_clinic t4 ON (t4.clinic_id = t2.id) ";
				if (isset($params['regCityAlias'])) {
					$addJoin .= " INNER JOIN underground_station_4_reg_city us4rc ON (us4rc.station_id=t4.undegraund_station_id) ";
					$addJoin .= " INNER JOIN reg_city rc ON (rc.id=us4rc.reg_city_id) ";
					$sqlAdd .= " AND rc.rewrite_name='" . $params['regCityAlias'] . "'";
					array_push($order, "us4rc.sort");
				} elseif (isset($params['areaAlias'])) {
					$addJoin .= " INNER JOIN area_underground_station aus ON (aus.station_id=t4.undegraund_station_id) ";
					$addJoin .= " INNER JOIN area_moscow am ON (am.id=aus.area_id) ";
					$sqlAdd .= " AND am.rewrite_name='" . $params['areaAlias'] . "'";
				}
			}

			if (isset($params['street_id'])) {
				$street = StreetModel::model()->findByPk($params['street_id']);
				$left = Coordinate::lngPlusDistance($street->bound_left, -1 * StreetModel::DISTANCE_EXTENDED_BOUND);
				$right = Coordinate::lngPlusDistance($street->bound_right, StreetModel::DISTANCE_EXTENDED_BOUND);
				$top = Coordinate::latPlusDistance($street->bound_top, StreetModel::DISTANCE_EXTENDED_BOUND);
				$bottom = Coordinate::latPlusDistance($street->bound_bottom, -1 * StreetModel::DISTANCE_EXTENDED_BOUND);
				$sqlAdd .= " AND (t2.longitude > {$left} AND t2.longitude < {$right} AND t2.latitude > {$bottom} AND t2.latitude < {$top})";
			}

			if ((isset($params['start']) || isset($params['count'])) && !$withPager) {
				if (isset($params['start']) && !isset($params['count'])) {
					$limit = " LIMIT " . intval($params['start']) . ", 18446744073709551615"; //взято из оф документации ибо неможет офсетить без лимита наш любимый мускуль http://dev.mysql.com/doc/refman/5.6/en/select.html#id4651990
				} elseif (!isset($params['start']) && isset($params['count'])) {
					$limit = " LIMIT " . intval($params['count']);
				} else {
					$limit = " LIMIT " . intval($params['start']) . ", " . intval($params['count']);
				}
			}

			if (isset($params['clinicId'])) {
				if ($params['clinicId'] > 0) {
					$sqlAdd .= " AND d4c.clinic_id=" . $params['clinicId'];
				}
			}

			if (isset($params['clinicIds']) && count($params['clinicIds'])) {
				$params['clinicIds'] = implode(',', $params['clinicIds']);
				$sqlAdd .= " AND d4c.clinic_id IN ({$params['clinicIds']})";
			}

			if (isset($params['exceptionIds'])) {
				$ids = implode(',', $params['exceptionIds']);
				$sqlAdd .= " AND t1.id NOT IN ($ids)";
			}
		}

		if (count($order) > 0)
			$order = implode(", ", $order);
		else
			$order = " RatingInternal DESC";

		$onlineBooking = Yii::app()->params['onlineBooking'] ? 'd4c.has_slots' : '0';

		$sql = "SELECT
					t1.id AS Id, t1.created AS CreationDate, 
					t1.name AS Name, t1.rewrite_name AS Alias,
					t1.image as Image,
					CASE 
					    WHEN (t1.image IS NULL OR t1.image='') AND t1.sex=1 THEN 'avatar_m_small.gif'
					    WHEN (t1.image IS NULL OR t1.image='') AND t1.sex=2 THEN 'avatar_w_small.gif'
					    ELSE CONCAT(t1.id,'_small.jpg')
					END AS SmallImg,
					CASE 
					    WHEN (t1.image IS NULL OR t1.image='') AND t1.sex=1 THEN 'avatar_m_small.gif'
					    WHEN (t1.image IS NULL OR t1.image='') AND t1.sex=2 THEN 'avatar_w_small.gif'
					    ELSE CONCAT(t1.id,'.jpg')
					END AS SqImg,
					CASE 
					    WHEN (t1.image IS NULL OR t1.image='') AND t1.sex=1 THEN 'avatar_m.gif'
					    WHEN (t1.image IS NULL OR t1.image='') AND t1.sex=2 THEN 'avatar_w.gif'
					    ELSE CONCAT(t1.id,'_med.jpg')
					END AS MedImg,
					t1.sex AS Sex,
					CASE WHEN t1.rating = 0 THEN t1.total_rating ELSE t1.rating END AS SortRating,
					CASE WHEN t1.special_price IS NULL THEN t1.price ELSE t1.special_price END AS RealPrice,
					CASE WHEN t1.experience_year IS NULL OR t1.experience_year = '' THEN 0 ELSE YEAR(now()) - t1.experience_year END AS CaseExperienceYear,
					t1.total_rating AS TotalRating, t1.rating AS ManualRating,
					t1.rating_education AS RatingEdu, t1.rating_ext_education AS RatingExtEdu,
					t1.text_association AS TextAssociation,
					t1.rating_experience AS RatingExperience, t1.rating_academic_degree AS RatingDegree,
					t1.rating_clinic AS RatingClinic, t1.rating_opinion AS RatingReview,
					t1.rating_internal AS RatingInternal,
					t1.price AS Price, t1.special_price AS SpecialPrice,
					t1.experience_year AS ExperienceYear, t1.status AS Status,				  
					t1.addNumber, t1.text AS Description, t1.experience_year, t1.departure AS Departure,
					cdict.title AS category, ddict.title AS degree, rdict.title AS rank,
					t2.id AS ClinicId, t2.name AS ClinicName, t2.asterisk_phone AS PhoneNumber,
					concat(t2.street, ', ', t2.house) AS ClinicAddress,
					t2.latitude AS Latitude, t2.longitude AS Longitude,
					{$onlineBooking} as CanOnlineBooking,
					t2.status AS ClinicStatus {$select}
				FROM doctor  t1
				INNER JOIN doctor_4_clinic d4c ON (d4c.doctor_id=t1.id and d4c.type = " . DoctorClinicModel::TYPE_DOCTOR . ")
				INNER JOIN clinic t2 ON (d4c.clinic_id = t2.id)" . $addJoin . "
				LEFT JOIN category_dict cdict ON cdict.category_id=t1.category_id
				LEFT JOIN degree_dict ddict ON ddict.degree_id=t1.degree_id
				LEFT JOIN rank_dict rdict ON rdict.rank_id=t1.rank_id
				INNER JOIN rating r on (r.strategy_id = {$ratingStrategyId} and r.object_id = d4c.id and r.object_type = " . RatingModel::TYPE_DOCTOR .  ")
				WHERE " . $sqlAdd . "
				GROUP BY Id
				ORDER BY " . $order . "
				" . $limit;

		$data = [];
		$activeDoctorIds = [];

		if ($withPager) {
			list($sql, $pages, $total) = pagerArr($sql, $startPage, $step);
		}

		$result = query($sql);
		if (num_rows($result) > 0) {
			while ($row = fetch_array($result)) {
				if ($detailed) {
					$row['Phone']['Digit'] = $row['PhoneNumber'];
					$row['Phone']['Text'] = formatPhone($row['PhoneNumber']);
					$row['Specialities'] = self::getSpecialityListById($row['Id']);
					$row['Stations'] = self::getStationForDoctorById($row['Id']);
					$rating = (!empty($row['ManualRating'])) ? $row['ManualRating'] : $row['TotalRating'];
					$row['EditedRating'] = self::getRating($rating);
					$row['EditedRating2'] = self::getRating($rating * 2);
					$row['Experience'] = self::getExperience($row['ExperienceYear']);
					$row['ReviewsCount'] = self::getReviewsCount($row['Id']);
					$row['ClinicList'] = self::getClinics($row['Id'], !empty($params['schedule']));
					$row['ClinicCount'] = count($row['ClinicList']);

					$spec = [];
					foreach ($row['Specialities'] as $item) {
						$spec[] = $item['Name'];
					}
					$row['Spec'] = implode(', ', $spec);

					if ($row['Status'] == DoctorModel::STATUS_ACTIVE && $row['ClinicStatus'] == ClinicModel::STATUS_ACTIVE) {
						$activeDoctorIds[] = $row['Id'];
					}
				}
				$data[$row['Id']] = $row;
			}

			if (!empty($params['schedule']) && \Yii::app()->params['doctorScheduleEnabled']) {
				$days = DoctorClinicModel::COUNT_DAYS_FOR_SCHEDULE;

				$schedule = DoctorClinicModel::model()->getDoctorsSchedule(array_keys($data), $days);

				foreach ($data as &$doctor) {
					$doctorId = $doctor['Id'];
					foreach ($doctor['ClinicList'] as &$clinic) {
						$clinic['Schedule'] = empty($schedule[$doctorId][$clinic['id']]) ?
							null :
							DoctorClinicModel::formatScheduleForDoctor($schedule[$doctorId][$clinic['id']], $days);
					}
				}
			}

			$tips = TipsMessageModel::model()->findRandomForRecords($activeDoctorIds);
			foreach ($tips as $tipMessage) {
				$data[$tipMessage->record_id]['Tips'] = [
					'Message' => $tipMessage->getMessage(),
					'Color'   => $tipMessage->tips->color,
				];
			}
		}
		if ($withPager)
			return ['data' => array_values($data), 'pages' => $pages, 'total' => $total];
		else
			return array_values($data);
	}

	/**
	 * Метод, который возвращает число отзывов для всех докторов
	 *
	 * @return array
	 */
	private static function getReviewsCountList()
	{
		static $resultArr;
		if (is_null($resultArr)) {
			$sql = "
				SELECT
				doctor_id,
				Count(do.id) AS 'ReviewsCount'
				FROM doctor_opinion do
				WHERE
				do.allowed = 1
				GROUP BY doctor_id";
			$result = query($sql);
			$resultArr = array();
			while ($row = fetch_array($result)) {
				$resultArr[$row['doctor_id']] = (int) $row['ReviewsCount'];
			}
		}
		return $resultArr;
	}

	/**
	 * Метод возвращает число отзывов для доктора
	 *
	 * @param  int $id                       идентификатор доктора
	 * @return int                           число отзывов
	 */
	private static function getReviewsCount($id)
	{
		$count = 0;
		$reviewsCount = self::getReviewsCountList();
		if (array_key_exists($id, $reviewsCount)) {
			$count = $reviewsCount[$id];
		}
		return $count;
	}

	/**
	 * Метод, который возвращает массив специалистов и их специализаций с идентификаторами
	 *
	 * @return array
	 */
	private static function getSpecialityList()
	{
		static $resultArr;
		if (is_null($resultArr)) {
			$sql = "
				SELECT
					t2.doctor_id,
					t1.id,
					t1.name AS 'title'
				FROM sector t1
				INNER JOIN doctor_sector t2
				WHERE t2.sector_id = t1.id AND t1.hidden_in_menu = 0";
			$result = query($sql);
			$resultArr = fetch_all($result);
		}
		return $resultArr;
	}

	/**
	 * Метод, который возвращает массив специализаций для конкретного врача
	 *
	 * @param int $id                 идентификатор докотора
	 * @return array
	 */
	static function getSpecialityListById($id)
	{
		$resultArray = array();
		foreach (self::getSpecialityList() as $speciality) {
			if ($speciality['doctor_id'] == $id) {
				$item = array('Id' => $speciality['id'], 'Name' => $speciality['title']);
				array_push($resultArray, $item);
			}
		}
		return $resultArray;
	}

	/**
	 * Метод, который возвращает массив из станций метро с параметрами для конкретного доктора
	 *
	 * @param  int $id идентификатор врача
	 *
	 * @return string[]
	 */
	public static function getStationForDoctorById($id)
	{
		//кешируем запрос на 24 часа
		return Yii::app()
			->db
			->cache(86400)
			->createCommand()
			->select("
				t1.doctor_id,
				t2.distance,
				t3.id AS Id,
				t3.name AS Name,
				t3.rewrite_name AS Alias,
				t4.id AS LineId,
				t4.name AS LineName,
				t4.color AS LineColor,
				t4.city_id AS CityId
			")
			->from("doctor_4_clinic t1")
			->join("underground_station_4_clinic t2", "t2.clinic_id = t1.clinic_id")
			->leftJoin("underground_station t3", "t3.id = undegraund_station_id")
			->leftJoin("underground_line t4", "t4.id = t3.underground_line_id")
			->where("t1.doctor_id = :doctor_id and t1.type = :type", array(":doctor_id" => $id, ':type' => DoctorClinicModel::TYPE_DOCTOR))
			->group("Alias")
			->order("t3.name")
			->queryAll();
	}

	/**
	 * Метод, который возвращает массив из станций метро с параметрами для клиники
	 *
	 * @param  int $id идентификатор клиники
	 *
	 * @return string[]
	 */
	public static function getStationByClinicId($id)
	{
		//кешируем запрос на 24 часа
		return Yii::app()
			->db
			->cache(86400)
			->createCommand()
			->select("
				t1.distance,
				t2.id AS Id,
				t2.name AS Name,
				t2.rewrite_name AS Alias,
				t3.id AS LineId,
				t3.name AS LineName,
				t3.color AS LineColor
			")
			->from("underground_station_4_clinic t1")
			->leftJoin("underground_station t2", "t2.id = t1.undegraund_station_id")
			->leftJoin("underground_line t3", "t3.id = t2.underground_line_id")
			->where("t1.clinic_id = :clinic_id", array(":clinic_id" => $id))
			->group("Alias")
			->order("t2.name")
			->queryAll();
	}

	static function getCount($params)
	{

		$sqlAdd = "";
		$addJoin = "";

		if (isset($params['city'])) {
			$sqlAdd .= " t2.city_id = " . intval($params['city']) . " ";
		} else {
			$sqlAdd .= " t2.city_id = 1 ";
		}

		$sqlAdd .= " AND t1.status = 3
					AND t2.status = 3
					AND (t2.isClinic = 'yes' OR t2.isPrivatDoctor = 'yes') "; // Только активные

		if (count($params) > 0) {

			if (isset($params['searchWord'])) {
				$searchWord = strtoupper($params['searchWord']);
				$sqlAdd .= " AND (UPPER(t1.name) LIKE '%$searchWord%'
							 OR UPPER(t1.text) LIKE '%$searchWord%'
							 OR UPPER(t1.text_degree) LIKE '%$searchWord%'
							 OR UPPER(t1.text_education) LIKE '%$searchWord%'
							 OR UPPER(t1.text_association) LIKE '%$searchWord%'
							 OR UPPER(t4.name) LIKE '%$searchWord%')";
				$addJoin .= " INNER JOIN doctor_sector t3 ON (t3.doctor_id = t1.id) ";
				$addJoin .= " INNER JOIN sector t4 ON (t4.id = t3.sector_id) ";
			}

			if (isset($params['departure'])) {
				$sqlAdd .= " AND t1.departure=1";
			}

			if (isset($params['speciality']) && intval($params['speciality']) > 0) {
				$sqlAdd .= " AND t3.sector_id = " . intval($params['speciality']) . " ";
				$addJoin .= " INNER JOIN doctor_sector t3 ON (t3.doctor_id = t1.id) ";
			}

			if (isset($params['stations'])) {

				if (!isset($params['near']))
					$params['near'] = 'strict';

				if (count($params['stations']) > 0) {

					$params['stations'] = array_map(function($v) {return (int)$v;}, $params['stations']);

					$stations = implode(',', $params['stations']);
					$addJoin .= " INNER JOIN underground_station_4_clinic t4 ON (t4.clinic_id = t2.id) ";
					if ($params['near'] == 'strict') {
						$sqlAdd .= " AND t4.undegraund_station_id IN (" . $stations . ") ";
					} elseif ($params['near'] == 'closest') {
						$addJoin .= " INNER JOIN closest_station t5 ON (t5.closest_station_id = t4.undegraund_station_id) ";
						$sqlAdd .= " AND t5.station_id IN (" . $stations . ") AND t5.priority ";
					} elseif ($params['near'] == 'mixed') {
						$addJoin .= " LEFT JOIN closest_station t5 ON (t5.closest_station_id = t4.undegraund_station_id) ";
						$sqlAdd .= " AND t5.station_id IN (" . $stations . ") ";
					}
				}
			}

			//поиск по району делаем через принадлежность клинки к району
			if (isset($params['district_id'])) {
				if (!isset($params['near']))
					$params['near'] = 'strict';

				if ($params['near'] == 'strict') {
					$sqlAdd  .= " AND t2.district_id = " . (int)$params['district_id'];
				} elseif ($params['near'] == 'closest') {
					$addJoin .= " INNER JOIN closest_district t7 ON t2.district_id = t7.closest_district_id ";
					$sqlAdd .= " AND t7.district_id = " . (int)$params['district_id'] . " AND t7.priority > 0 ";
					if (isset($params['orderType']))
						array_push($order, "t7.priority");
					else
						$order = array('t7.priority');

				} elseif ($params['near'] == 'mixed') {
					$addJoin .= " LEFT JOIN closest_district t7 ON t2.district_id = t7.closest_district_id ";
					$sqlAdd .= " AND t7.district_id = " . (int)$params['district_id'] ;
					array_push($order, "t7.priority");
				}
			}

			if (isset($params['regCityAlias']) || isset($params['areaAlias'])) {
				$addJoin .= " LEFT JOIN underground_station_4_clinic t4 ON (t4.clinic_id = t2.id) ";
				if (isset($params['regCityAlias'])) {
					$addJoin .= " INNER JOIN underground_station_4_reg_city us4rc ON (us4rc.station_id=t4.undegraund_station_id) ";
					$addJoin .= " INNER JOIN reg_city rc ON (rc.id=us4rc.reg_city_id) ";
					$sqlAdd .= " AND rc.rewrite_name='" . $params['regCityAlias'] . "'";
				} elseif (isset($params['areaAlias'])) {
					$addJoin .= " INNER JOIN area_underground_station aus ON (aus.station_id=t4.undegraund_station_id) ";
					$addJoin .= " INNER JOIN area_moscow am ON (am.id=aus.area_id) ";
					$sqlAdd .= " AND am.rewrite_name='" . $params['areaAlias'] . "'";
				}
			}

			if (isset($params['clinicId'])) {
				if ($params['clinicId'] > 0) {
					$sqlAdd .= " AND d4c.clinic_id=" . $params['clinicId'];
				}
			}

			if (isset($params['exceptionIds'])) {
				$ids = implode(',', $params['exceptionIds']);
				$sqlAdd .= " AND t1.id NOT IN ($ids)";
			}
		}

		$sql = "SELECT
					t1.id
				FROM doctor  t1
				INNER JOIN doctor_4_clinic d4c ON (d4c.doctor_id=t1.id and d4c.type = " . DoctorClinicModel::TYPE_DOCTOR . ")
				INNER JOIN clinic t2 ON (d4c.clinic_id = t2.id)" . $addJoin . "
				WHERE " . $sqlAdd . "
				GROUP BY Id";
		$result = query($sql);

		return num_rows($result);
	}


	static function getSpecialities($cityId = 1)
	{
		$data = array();

		$sql = "SELECT t0.id AS Id, t0.name As Name, t0.rewrite_name AS Alias
				FROM sector t0
				LEFT JOIN doctor_sector AS t1 ON t1.sector_id=t0.id
				LEFT JOIN doctor_4_clinic AS t2 ON (t2.doctor_id=t1.doctor_id and t2.type = " . DoctorClinicModel::TYPE_DOCTOR . ")
				LEFT JOIN clinic AS t3 ON t3.id=t2.clinic_id
				LEFT JOIN doctor AS t4 ON t4.id=t1.doctor_id
				WHERE t4.status=3
					AND t3.status=3
					AND t3.city_id=" . $cityId . "
				GROUP BY t0.id";

		$result = query($sql);

		while ($row = fetch_array($result)) {
			array_push($data, $row);
		}

		return $data;

	}

	/**
	 * Получает станции метро по идентификатору города
	 *
	 * @param int $cityId идентификатор города
	 *
	 * @return string[]
	 */
	static function getStations($cityId = 1)
	{
		$data = array();

		$sql = "
			SELECT t1.id AS Id, t1.name AS Name, t1.rewrite_name AS Alias
			FROM underground_station t1
			LEFT JOIN underground_line t2 ON t2.id = t1.underground_line_id
			WHERE t2.city_id = {$cityId}
			GROUP BY t1.name
			ORDER BY name
		";
		$result = query($sql);
		$i = 0;
		while ($row = fetch_array($result)) {
			$data[$i] = $row;
			$i++;
		}

		return $data;
	}

	static function getSpec($id)
	{

		$sql = "SELECT id, name, rewrite_name AS alias, rewrite_spec_name AS specAlias, clinic_seo_title AS clinicTitle
				FROM sector
				WHERE id=" . $id . "
				ORDER BY name";

		$result = query($sql);

		$row = fetch_object($result);

		return $row;
	}

	/**
	 * Получает наиболее подходящий идентификатор клиники для врача
	 * Поскольку у обычно врача много клиник.
	 * Это кастыльная функция, которая помогает закрыть места, где клиники почемуто не оказалось.
	 * Но при этом вызывает ошибку.
	 *
	 * @param int  $doctorId Идентификатор врача
	 * @param bool $warn     Записать предупреждение
	 *
	 * @return int
	 */
	public function getDefaultClinicId($doctorId, $warn = true) {
		if ($warn) {
			trigger_error("Empty clinic id for doctor: {$doctorId}", E_USER_WARNING);
		}

		$result = query("
			SELECT clinic.id
			FROM clinic
				JOIN doctor_4_clinic dc ON (dc.clinic_id=clinic.id and dc.type = " . DoctorClinicModel::TYPE_DOCTOR . ")
			WHERE
				dc.doctor_id = {$doctorId} AND clinic.status != " . ClinicModel::STATUS_BLOCKED . "
			LIMIT 1");
		if ($result) {
			$row = fetch_array($result);
			if ($row) {
				return (int) $row['id'];
			}
		}

		if ($warn) {
			trigger_error("clinic not found for doctor: {$doctorId}", E_USER_ERROR);
		}
		return null;
	}
}
