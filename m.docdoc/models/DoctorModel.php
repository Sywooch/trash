<?php

/**
 * Class DoctorModel
 * Врачи
 */
class DoctorModel
{

	private $id;
	private $name;
	private $alias;
	private $rating;
	private $internalRating;
	private $price;
	private $specialPrice;
	private $sex;
	private $img;
	private $opinionCount;
	private $textAbout;
	private $experienceYear;
	private $departure;
	private $category;
	private $clinics;
	private $degree;
	private $rank;
	private $specialities = [];
	private $stations;
	private $clinicModels;
	private $description;
	private $textEducation;
	private $textDegree;
	private $textSpec;
	private $textCourse;
	private $textExperience;
	private $addPhoneNumber;
	private $reviews = [];

	/**
	 * Активность
	 *
	 * @var bool
	 */
	private $_isActive = false;

	/**
	 * @return float
	 */
	public function getRating()
	{
		return number_format($this->rating, 1);
	}

	/**
	 * @param float $rating
	 */
	public function setRating($rating)
	{
		$this->rating = $rating;
	}

	/**
	 * @return string
	 */
	public function getAlias()
	{
		return $this->alias;
	}

	/**
	 * @param string $alias
	 */
	public function setAlias($alias)
	{
		$this->alias = $alias;
	}

	/**
	 * @return string
	 */
	public function getCategory()
	{
		return $this->category;
	}

	/**
	 * @param string $category
	 */
	public function setCategory($category)
	{
		$this->category = $category;
	}

	/**
	 * @return array
	 */
	public function getClinics()
	{
		return $this->clinics;
	}

	/**
	 * @param array $clinics
	 */
	public function setClinics($clinics)
	{
		$this->clinics = $clinics;
	}

	/**
	 * @return string
	 */
	public function getDegree()
	{
		return $this->degree;
	}

	/**
	 * @param string $degree
	 */
	public function setDegree($degree)
	{
		$this->degree = $degree;
	}

	/**
	 * @return int
	 */
	public function getDeparture()
	{
		return $this->departure;
	}

	/**
	 * @param int $departure
	 */
	public function setDeparture($departure)
	{
		$this->departure = $departure;
	}

	/**
	 * @return int
	 */
	public function getExperienceYear()
	{
		return $this->experienceYear;
	}

	/**
	 * @param int $experienceYear
	 */
	public function setExperienceYear($experienceYear)
	{
		$this->experienceYear = $experienceYear;
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param int $id
	 */
	public function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * @return string
	 */
	public function getImg()
	{
		return $this->img;
	}

	/**
	 * @param string $img
	 */
	public function setImg($img)
	{
		$this->img = $img;
	}

	/**
	 * @return float
	 */
	public function getInternalRating()
	{
		return $this->internalRating;
	}

	/**
	 * @param float $internalRating
	 */
	public function setInternalRating($internalRating)
	{
		$this->internalRating = $internalRating;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * @return int
	 */
	public function getOpinionCount()
	{
		return $this->opinionCount;
	}

	/**
	 * @param int $opinionCount
	 */
	public function setOpinionCount($opinionCount)
	{
		$this->opinionCount = $opinionCount;
	}

	/**
	 * @return float
	 */
	public function getPrice()
	{
		return empty($this->price) ? 0 : $this->price;
	}

	/**
	 * @param float $price
	 */
	public function setPrice($price)
	{
		$this->price = $price;
	}

	/**
	 * @return string
	 */
	public function getRank()
	{
		return $this->rank;
	}

	/**
	 * @param string $rank
	 */
	public function setRank($rank)
	{
		$this->rank = $rank;
	}

	/**
	 * @return int
	 */
	public function getSex()
	{
		return $this->sex;
	}

	/**
	 * @param int $sex
	 */
	public function setSex($sex)
	{
		$this->sex = $sex;
	}

	/**
	 * @return float
	 */
	public function getSpecialPrice()
	{
		return $this->specialPrice;
	}

	/**
	 * @param float $specialPrice
	 */
	public function setSpecialPrice($specialPrice)
	{
		$this->specialPrice = $specialPrice;
	}

	/**
	 * @return SpecialityModel[]
	 */
	public function getSpecialities()
	{
		return $this->specialities;
	}

	/**
	 * @param SpecialityModel[] $specialities
	 */
	public function setSpecialities($specialities)
	{
		$this->specialities = $specialities;
	}

	/**
	 * @return MetroModel[]
	 */
	public function getStations()
	{
		return $this->stations;
	}

	/**
	 * @param MetroModel[] $stations
	 */
	public function setStations($stations)
	{
		$this->stations = $stations;
	}

	/**
	 * @return string
	 */
	public function getTextAbout()
	{
		return $this->textAbout;
	}

	/**
	 * @param string $textAbout
	 */
	public function setTextAbout($textAbout)
	{
		$this->textAbout = $textAbout;
	}

	/**
	 * @return ClinicModel[]
	 */
	public function getClinicModels()
	{
		return $this->clinicModels;
	}

	/**
	 * @param ClinicModel[] $clinicModels
	 */
	public function setClinicModels($clinicModels)
	{
		$this->clinicModels = $clinicModels;
	}

	/**
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * @param string $description
	 */
	public function setDescription($description)
	{
		$this->description = $description;
	}

	/**
	 * @return string
	 */
	public function getTextCourse()
	{
		return $this->textCourse;
	}

	/**
	 * @param string $textCourse
	 */
	public function setTextCourse($textCourse)
	{
		$this->textCourse = $textCourse;
	}

	/**
	 * @return string
	 */
	public function getTextDegree()
	{
		return $this->textDegree;
	}

	/**
	 * @param string $textDegree
	 */
	public function setTextDegree($textDegree)
	{
		$this->textDegree = $textDegree;
	}

	/**
	 * @return string
	 */
	public function getTextEducation()
	{
		return $this->textEducation;
	}

	/**
	 * @param string $textEducation
	 */
	public function setTextEducation($textEducation)
	{
		$this->textEducation = $textEducation;
	}

	/**
	 * @return string
	 */
	public function getTextExperience()
	{
		return $this->textExperience;
	}

	/**
	 * @param string $textExperience
	 */
	public function setTextExperience($textExperience)
	{
		$this->textExperience = $textExperience;
	}

	/**
	 * @return string
	 */
	public function getTextSpec()
	{
		return $this->textSpec;
	}

	/**
	 * @param string $textSpec
	 */
	public function setTextSpec($textSpec)
	{
		$this->textSpec = $textSpec;
	}

	/**
	 * @return string
	 */
	public function getAddPhoneNumber()
	{
		return $this->addPhoneNumber;
	}

	/**
	 * @param string $addPhoneNumber
	 */
	public function setAddPhoneNumber($addPhoneNumber)
	{
		$this->addPhoneNumber = $addPhoneNumber;
	}

	/**
	 * @return ReviewModel[]
	 */
	public function getReviews()
	{
		return $this->reviews;
	}

	/**
	 * @param ReviewModel[] $reviews
	 */
	public function setReviews($reviews)
	{
		$this->reviews = $reviews;
	}

	/**
	 * @return float
	 */
	public function getAllRatingQlf()
	{
		$rating = 0;
		foreach ($this->getReviews() as $review) {
			$rating += $review->getRatingQlf();
		}

		if (!$rating) {
			return 0;
		}

		return $rating / count($this->getReviews());
	}

	/**
	 * @return float
	 */
	public function getAllRatingAtt()
	{
		$rating = 0;
		foreach ($this->getReviews() as $review) {
			$rating += $review->getRatingAtt();
		}

		if (!$rating) {
			return 0;
		}

		return $rating / count($this->getReviews());
	}

	/**
	 * @return float
	 */
	public function getAllRatingRoom()
	{
		$rating = 0;
		foreach ($this->getReviews() as $review) {
			$rating += $review->getRatingRoom();
		}

		if (!$rating) {
			return 0;
		}

		return $rating / count($this->getReviews());
	}

	/**
	 * Получение словесной оценки
	 *
	 * @return string
	 */
	public function getRatingInWord()
	{
		$rating = 0;
		foreach ($this->getReviews() as $review) {
			$rating += $review->getSummaryRating();
		}

		if (!$rating) {
			return "";
		}

		$rating /= count($this->getReviews());

		$rating = round($rating);

		if (isset(ReviewModel::$ratingWords[$rating])) {
			return ReviewModel::$ratingWords[$rating];
		}

		return "";
	}

	/**
	 * @return string
	 */
	public function getAllSpecialityString()
	{
		$specialities = [];
		$specialitiesNotSimple = [];

		foreach ($this->getSpecialities() as $spec) {
			if ($spec->isSimple()) {
				$specialities[] = $spec->getName();
			} else {
				$specialitiesNotSimple[] = $spec->getName();
			}
		}

		if ($specialities) {
			return implode(', ', $specialities);
		} else {
			return implode(', ', $specialitiesNotSimple);
		}
	}

	/**
	 * @return string
	 */
	public function getAllStationsString()
	{
		$stations = [];

		if ($this->stations) {
			foreach ($this->stations as $station) {
				$stations[] = $station->getName();
			}
		}

		return implode(', ', $stations);
	}

	/**
	 * @return string
	 */
	public function getFullAddress()
	{
		$address = "";

		$clinics = $this->getClinicModels();
		if (count($clinics) > 0) {
			$address .= $clinics[0]->getStreet() . ", " . $clinics[0]->getHouse();
		}

		return $address;
	}

	/**
	 * Первый идентификатор клиники
	 *
	 * @return int|null
	 */
	public function getFirstClinicId()
	{
		return count($this->getClinics()) > 0 ? $this->getClinics()[0] : null;
	}

	/**
	 * Основная клиника доктора
	 *
	 * @return ClinicModel | null
	 */
	public function getFirstClinic()
	{
		return $this->clinicModels ? $this->clinicModels[0] : null;
	}

	/**
	 * Выводит месторасположение
	 * Если есть метро - выводится метро
	 * Если нет метро - выводится улица (для регионов)
	 *
	 * @return string
	 */
	public function getLocation()
	{
		$stationNames = array();
		if ($this->getStations()) {
			foreach ($this->getStations() as $station) {
				$stationNames[] = "м. " . $station->getName();
			}
		}

		if ($stationNames) {
			return implode(", ", array_unique($stationNames));
		}

		return $this->getFullAddress();
	}

	/**
	 * Устанавливает активность
	 *
	 * @param bool $isActive активность
	 *
	 * @return void
	 */
	public function setActive($isActive)
	{
		$this->_isActive = $isActive;
	}

	/**
	 * Получает активность
	 *
	 * @return int
	 */
	public function isActive()
	{
		return $this->_isActive;
	}
}