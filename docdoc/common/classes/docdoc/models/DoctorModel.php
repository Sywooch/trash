<?php
namespace dfs\docdoc\models;

use dfs\docdoc\helpers\ImageHelper;
use Gregwar\Image\Image;
use dfs\docdoc\helpers\ActiveRecordHelper;
use Yii;
use CDbCriteria;
use dfs\docdoc\objects\Coordinate;

require_once LIB_PATH . 'php/emailQuery.class.php';

/**
 * This is the model class for table "doctor".
 *
 * The followings are the available columns in table 'doctor':
 * @property integer $id
 * @property integer $clinic_id
 * @property integer $departure
 * @property string $created
 * @property float $rating
 * @property float $rating_education
 * @property float $rating_ext_education
 * @property float $rating_experience
 * @property float $rating_academic_degree
 * @property string $rating_clinic
 * @property string $rating_opinion
 * @property string $total_rating
 * @property string $rating_internal
 * @property string $price
 * @property string $special_price
 * @property string $experience_year
 * @property string $status
 * @property string $view_count
 * @property string $name
 * @property string $rewrite_name
 * @property string $image
 * @property string $phone
 * @property string $phone_appointment
 * @property string $text
 * @property string $text_degree
 * @property string $category_id
 * @property string $degree_id
 * @property string $rank_id
 * @property string $text_education
 * @property string $text_association
 * @property string $text_spec
 * @property string $text_course
 * @property string $text_experience
 * @property string $attach
 * @property string $note
 * @property string $openNote
 * @property string $sex
 * @property string $email
 * @property string $password
 * @property string $interval_appointment
 * @property string $addNumber
 * @property string $schedule_state
 * @property string $doctor_list_state
 * @property integer $kids_reception
 * @property integer $kids_age_from
 * @property integer $kids_age_to
 * @property float $conversion
 * @property integer $update_tips
 *
 * The followings are the available model relations:
 * @property ClinicModel[] $clinics
 * @property DoctorSectorModel[] $doctorSectorExperiences
 * @property SectorModel[] $sectors
 * @property DoctorClinicModel[] $doctorClinics
 * @property DoctorOpinionModel[] $doctorReviews
 * @property CategoryModel $category
 * @property DegreeModel $degree
 * @property RankModel $rank
 * @property EducationModel[] $education
 * @property EducationDoctorModel[] $educationDoctor
 * @property ModerationModel $moderation
 * @property SectorModel[] $visibleSectors
 *
 * @method DoctorModel findByPk
 * @method DoctorModel find
 * @method DoctorModel[] findAll
 * @method DoctorModel with
 * @method DoctorModel cache
 */
class DoctorModel extends \CActiveRecord
{
	const STATUS_REGISTRATION 	= 1;
	const STATUS_NEW 			= 2;
	const STATUS_ACTIVE 		= 3;
	const STATUS_BLOCKED 		= 4;
	const STATUS_ARCHIVE 		= 5;
	const STATUS_MODERATED 		= 6;
	const STATUS_ANOTHER_DOCTOR	= 7;

	/**
	 * При этом сценарии не пересчитывается рейтинг в afterSave
	 */
	const SCENARIO_SKIP_UPDATE_RATING = 'SCENARIO_SKIP_UPDATE_RATING';

	/**
	 * Названия статусов
	 *
	 * @var array
	 */
	protected static $statuses = [
		self::STATUS_REGISTRATION => 'Регистрация',
		self::STATUS_NEW => 'Новый',
		self::STATUS_ACTIVE => 'Активен',
		self::STATUS_BLOCKED => 'Заблокирован',
		self::STATUS_ARCHIVE => 'К удалению',
		self::STATUS_MODERATED => 'На модерации',
		self::STATUS_ANOTHER_DOCTOR => 'Другой врач',
	];

	/**
	 * Кеш для средней конверсии
	 *
	 * @var null
	 */
	protected static $avgConversion = null;

	const FACTOR_RATING_EDUCATION = 0.2;
	const FACTOR_RATING_EXT_EDUCATION = 0.1;
	const FACTOR_RATING_EXPERIENCE = 0.3;
	const FACTOR_RATING_DEGREE = 0.15;
	const FACTOR_RATING_CLINIC = 0.25;
	const FACTOR_RATING_INTERNAL = 5;


	/**
	 * Количество отзывов доктора
	 *
	 * @var int
	 */
	protected $countReviews = null;


	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className
	 * @return DoctorModel the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'doctor';
	}


	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			//array('phone', 'PhoneValidator'),
			['status, name', 'required'],
			[
				'clinic_id, status, view_count, departure, price, experience_year',
				'numerical',
				'integerOnly' => true
			],
			['rating', 'numerical', 'min' => 0, 'max' => 5],
			['price', 'numerical', 'min' => 0],
			['experience_year', 'numerical', 'min' => 1961],
			['name, text, text_education, text_association, text_course', 'filter', 'filter' => 'strip_tags'],
			[
				'name, text, text_education, text_association, text_course',
				'filter',
				'filter' => 'htmlspecialchars'
			],
			['name, rewrite_name, phone, text_degree, text_spec', 'length', 'max' => 512],
			['image', 'file', 'types' => 'jpg, gif, png', 'allowEmpty' => true],
			['attach', 'file', 'types' => 'doc, docx, pdf, txt, rtf', 'allowEmpty' => true],
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			[
				'id, clinic, price, experience_year, undergroundStations, created, rating, status, view_count, name, departure',
				'safe',
				'on' => 'search'
			],
		];
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return [
			'clinics' => [
				self::HAS_MANY,
				ClinicModel::class,
				['clinic_id' => 'id'],
				'through' => 'doctorClinics',
			],
			'doctorClinics' => [
				self::HAS_MANY,
				DoctorClinicModel::class,
				'doctor_id',
				'on' => 'doctorClinics.type = ' . DoctorClinicModel::TYPE_DOCTOR,
			],
			'sectors' => [
				self::MANY_MANY,
				SectorModel::class,
				'doctor_sector(doctor_id,sector_id)'
			],
			'visibleSectors' => [
				self::MANY_MANY,
				SectorModel::class,
				'doctor_sector(doctor_id,sector_id)',
				'condition' => 'visibleSectors.hidden_in_menu = 0'
			],
			'undergroundStations' => [
				self::MANY_MANY,
				UndergroundStationModel::class,
				'doctor_address(doctor_id,underground_station_id)'
			],
			'doctorReviews' => [
				self::HAS_MANY,
				DoctorOpinionModel::class,
				'doctor_id'
			],
			'category' => [
				self::BELONGS_TO,
				CategoryModel::class,
				'category_id'
			],
			'degree' => [
				self::BELONGS_TO,
				DegreeModel::class,
				'degree_id'
			],
			'rank' => [
				self::BELONGS_TO,
				RankModel::class,
				'rank_id'
			],
			'education' => [
				self::MANY_MANY,
				EducationModel::class,
				'education_4_doctor(doctor_id, education_id)'
			],
			'educationDoctor' => [
				self::HAS_MANY,
				EducationDoctorModel::class,
				'doctor_id'
			],
			'moderation' => [
				self::HAS_ONE,
				ModerationModel::class,
				'entity_id',
				'condition' => 'entity_class = "DoctorModel"'
			],
		];
	}

	public function scopes()
	{
		return array_merge(
			parent::scopes(),
			[
				'ordered' => [
					'order' => $this->getTableAlias() . '.name ASC',
				],
			]
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'text_education' => 'Образование',
			'text_association' => 'Ассоциации врачей',
			'created' => 'Дата регистрации',
			'departure' => 'Возможен выезд на дом',
			'rating' => 'Рейтинг',
			'status' => 'Статус',
			'view_count' => 'Количество просмотров',
			'name' => 'Имя',
			'phone' => 'Телефон врача',
			'phone_appointment' => 'Телефон для записи',
			'text' => 'О докторе',
			'text_degree' => 'Научная степень',
			'text_spec' => 'Специализация',
			'text_course' => 'Курсы повышения квалификации',
			'academicDegree' => 'Учёная степень',
			'priceRange' => 'Диапазон цен',
			'education' => 'Образование',
			'image' => 'Фотография',
			'sectors' => 'Направления',
			'price' => 'Стоимость первого приёма',
			'undergroundStations' => 'Станции метро',
			'clinic' => 'Клиника',
			'clinic_id' => 'Клиника',
			'attach' => 'Приложение',
			'experience_year' => 'Год начала практики',
			'rewrite_name' => 'Алиас для ЧПУ',
			'conversion' => 'Конверсия',
		];
	}

	/**
	 * Действия перед удалением
	 *
	 * @return bool
	 */
	public function beforeDelete()
	{
		if ($this->status == self::STATUS_ACTIVE) {
			return false;
		}

		parent::beforeDelete();

		// Удаляем связи
		DoctorSectorModel::model()->deleteAllByAttributes(['doctor_id' => $this->id]);
		DoctorClinicModel::model()->deleteAllByAttributes(['doctor_id' => $this->id, 'type' => DoctorClinicModel::TYPE_DOCTOR]);
		EducationDoctorModel::model()->deleteAllByAttributes(['doctor_id' => $this->id]);

		return true;
	}

	/**
	 * Действия после удаления
	 *
	 * @return bool|void
	 */
	public function afterDelete()
	{
		parent::afterDelete();

		(new LogBackUserModel())->deleteDoctorLog($this);
		ClinicModel::updateDoctor($this->id);

		return true;
	}

	/**
	 * Возвращает дефолтную клинику для врача
	 * @return ClinicModel|null
	 */
	public function getDefaultClinic()
	{
		foreach ($this->clinics as $clinic) {
			if ($clinic->status != ClinicModel::STATUS_BLOCKED) {
				return $clinic;
			}
		}

		return null;
	}

	/**
	 * Все активные клиники врача
	 *
	 * @return ClinicModel[]
	 */
	public function getActiveClinics()
	{
		$clinics = [];

		foreach ($this->clinics as $clinic) {
			if ($clinic->status != ClinicModel::STATUS_BLOCKED) {
				$clinics[] = $clinic;
			}
		}

		return $clinics;
	}

	/**
	 * Возвращает дефолтную специализацию для врача
	 * @return SectorModel|null
	 */
	public function getDefaultSector()
	{
		if (!empty($this->sectors)) {
			return $this->sectors[0];
		}

		return null;
	}

	/**
	 * Получение рейтинга врача, который показывается на сайте
	 *
	 * @return float
	 */
	public function getDoctorRating()
	{
		$rating = (!empty($this->rating)) ? $this->rating : $this->total_rating;
		return  round($rating * 2, 1);
	}


	/**
	 * Поиск врача по имени
	 *
	 * @param string $name
	 * @param bool   $like
	 * @return $this
	 */
	public function byName($name, $like = false)
	{
		$criteria = new CDbCriteria();
		$criteria->condition = $like ? $this->getTableAlias() . ".name LIKE (:name)" : $this->getTableAlias() . ".name = :name";
		$criteria->params = [':name' => $like ? $name . '%' : $name];
		$this->getDbCriteria()->mergeWith($criteria);

		return $this;
	}

	/**
	 * Поиск врача с не пустым опытом
	 *
	 * @return $this
	 */
	public function withExperience()
	{
		$this->getDbCriteria()->mergeWith([
				'condition' => $this->getTableAlias() . ".experience_year > 0",
			]);
		return $this;
	}

	/**
	 * Выборка врача в указанных клиниках
	 *
	 * @param int[] $clinics
	 *
	 * @return $this
	 */
	public function inClinics($clinics) {

		$criteria = new \CDbCriteria();

		$criteria->with = [
			'clinics' => [
				'select' => false,
				'joinType' => 'INNER JOIN',
			],
		];
		$criteria->addInCondition('clinics.id', $clinics);
		$criteria->together = true;
		$this
			->getDbCriteria()
			->mergeWith($criteria);

		return $this;
	}

	/**
	 * Выборка врачей на станции
	 *
	 * @param int[] $stations
	 *
	 * @return $this
	 */
	public function atStations($stations) {

		$criteria = new \CDbCriteria();
		$criteria->with = [
			'clinics' => [
				'select' => false,
				'joinType' => 'INNER JOIN',
				'with' => [
					'stations' => [
						'joinType' => 'INNER JOIN',
					]
				]
			],
		];

		$criteria->addInCondition('stations.id', $stations);
		$criteria->together = true;

		$this
			->getDbCriteria()
			->mergeWith($criteria);

		return $this;
	}

	/**
	 * Выборка по статусам
	 *
	 * @param $statuses
	 *
	 * @return $this
	 */
	public function inStatuses(array $statuses)
	{
		$this->getDbCriteria()->addInCondition($this->getTableAlias() . '.status', $statuses);

		return $this;
	}

	/**
	 * Выборка по станциям метро
	 *
	 * @param int[] $stations
	 *
	 * @return DoctorModel $this
	 */
	public function inStations(array $stations)
	{
		if (count($stations) > 0) {
			$criteria = new \CDbCriteria();

			$criteria->with = [
				'clinics' => [
					'select' => false,
					'joinType' => 'INNER JOIN',
				],
				'clinics.stations' => [
					'select' => false,
					'joinType' => 'INNER JOIN',
					'scopes'   => ['findAllByPk' => [$stations]]
				]
			];
			$criteria->together = true;

			$this
				->getDbCriteria()
				->mergeWith($criteria);
		}

		return $this;
	}

	/**
	 * Поиск по специальности
	 *
	 * @param integer $speciality
	 *
	 * @return DoctorModel $this
	 */
	public function bySpeciality($speciality)
	{
		$criteria = new \CDbCriteria();
		$criteria->condition = 'sectors.id = :speciality';
		$criteria->params = [':speciality' => $speciality];
		$criteria->with = [
			'sectors' => [
				'select' => false,
				'joinType' => 'INNER JOIN',
			],
		];
		$criteria->together = true;

		$this
			->getDbCriteria()
			->mergeWith($criteria);

		return $this;
	}

	/**
	 * Поиск врачей которые нуждаются в обновлении подсказок
	 *
	 * @param bool $need
	 *
	 * @return $this
	 */
	public function byUpdateTips($need = true)
	{
		$this->getDbCriteria()->mergeWith([
				'condition' => $this->getTableAlias() . '.update_tips = ' . ($need ? 1 : 0),
			]);

		return $this;
	}

	/**
	 * Поиск по округу
	 *
	 * @param int $area
	 *
	 * @return $this
	 */
	public function inArea($area)
	{
		$criteria = new \CDbCriteria();

		$criteria->with = [
			'clinics' => [
				'select' => false,
				'joinType' => 'INNER JOIN',
			],
			'clinics.district' => [
				'select' => false,
				'joinType' => 'INNER JOIN',
				'scopes'   => ['inArea' => $area]
			]
		];
		$criteria->together = true;

		$this
			->getDbCriteria()
			->mergeWith($criteria);

		return $this;
	}

	/**
	 * Поиск по району
	 *
	 * @param array $districts
	 * @return $this
	 */
	public function inDistricts($districts)
	{
		$criteria = new \CDbCriteria();

		$criteria->with = [
			'clinics' => [
				'select' => false,
				'joinType' => 'INNER JOIN',
			]
		];
		$criteria->addInCondition('clinics.district_id', $districts);
		$criteria->together = true;

		$this
			->getDbCriteria()
			->mergeWith($criteria);

		return $this;
	}

	/**
	 * Поиск врача по улице
	 *
	 * @param int $streetId
	 *
	 * @return $this
	 */
	public function inStreet($streetId)
	{
		$criteria  = new CDbCriteria();
		$criteria->with = [
			'clinics' => [
				'select' => false,
				'joinType' => 'INNER JOIN',
				'scopes' => [
					'inStreet' => [$streetId]
				]
			]
		];
		$criteria->together = true;

		$this->getDbCriteria()->mergeWith($criteria);

		return $this;
	}

	/**
	 * Группировка по врачам
	 *
	 * @return $this
	 */
	public function groupByDoctor()
	{
		$criteria = new \CDbCriteria();
		$criteria->group = $this->getTableAlias() . ".id";

		$this
			->getDbCriteria()
			->mergeWith($criteria);

		return $this;
	}

	/**
	 * Получение кол-ва отзывов
	 *
	 * @return integer
	 */
	public function getOpinionCount()
	{
		return (int)DoctorOpinionModel::model()
			->byDoctor($this->id)
			->allowed()
			->count();
	}

	/**
	 * Получение категории
	 *
	 * @return string
	 */
	public function getCategory()
	{
		return !is_null($this->category) ? $this->category->title : '';
	}

	/**
	 * Получение ученой степени
	 *
	 * @return string
	 */
	public function getDegree()
	{
		return !is_null($this->degree) ? $this->degree->title : '';
	}

	/**
	 * Получение звания
	 *
	 * @return string
	 */
	public function getRank()
	{
		return !is_null($this->rank) ? $this->rank->title : '';
	}

	/**
	 * Получение категории, ученой степени и звания врача
	 *
	 * @return string
	 */
	public function getAwards()
	{
		$awards = [];

		if ($this->category) {
			$awards[] = $this->category->title;
		}
		if ($this->degree) {
			$awards[] = $this->degree->title;
		}
		if ($this->rank) {
			$awards[] = $this->rank->title;
		}

		return implode(', ', $awards);
	}

	/**
	 * Получение образования
	 *
	 * @return array
	 */
	public function getEducation()
	{
		$data = [];
		foreach ($this->educationDoctor as $item) {
			$data[] = "{$item->education->title} ({$item->year} г.)";
		}

		return $data;
	}

	/**
	 * Получение массива со специальностями
	 *
	 * @return array
	 */
	public function getSpecialities()
	{
		$data = [];
		foreach ($this->sectors as $item) {
			$data[] = [
				'Id' => $item->id,
				'Name' => $item->name,
			];
		}
		return $data;
	}

	/**
	 * Переносим неудаляемые элементы к другому врачу
	 *
	 * @param int $doctorId
	 */
	private function moveRelations($doctorId)
	{
		$requests = RequestModel::model()->byDoctor($this->id)->findAll();

		// Переносим заявки к другому врачу
		RequestModel::model()->updateAll(
			['req_doctor_id' => $doctorId],
			'req_doctor_id = :id',
			[':id' => $this->id]
		);

		// Записываем изменения в историю заявок
		foreach ($requests as $request) {
			$history = new RequestHistoryModel();
			$history->request = $request;
			$history->addLog("Система изменила врача на оригинального, т.к. предыдущий врач id = {$this->id} был дубликатом");
		}

		// Переносим отзывы к другому врачу
		DoctorOpinionModel::model()->updateAll(
			['doctor_id' => $doctorId],
			'doctor_id = :id',
			[':id' => $this->id]
		);
	}

	/**
	 * Удаление врача, как дубликата
	 *
	 * @param $doctorId
	 *
	 * @return bool
	 * @throws \CDbException
	 */
	public function deleteAsDublicate($doctorId)
	{
		$transaction = self::model()->dbConnection->beginTransaction();
		try {
			$this->moveRelations($doctorId);
			if ($this->delete()) {
				$transaction->commit();
				return true;
			} else {
				$transaction->rollback();
				return false;
			}
		} catch (\Exception $e) {
			$transaction->rollback();
			return false;
		}
	}

	/**
	 * Адрес фотографии врача
	 *
	 * @param string $type small|sq|med
	 *
	 * @return string
	 */
	public function getImg($type = 'small')
	{
		$img  = null;
		$sex_suffix =  ($this->sex == 1) ? 'm' : 'w';

		switch ($type) {
			case "small":
				$img = (empty($this->image)) ? "avatar_{$sex_suffix}_small.gif" :  $this->id . '_small.jpg';
				break;
			case 'sq':
				$img = (empty($this->image)) ? "avatar_{$sex_suffix}_small.gif" :  $this->id . '.jpg';
				break;
			case 'med':
				$img = (empty($this->image)) ? "avatar_{$sex_suffix}.gif" :  $this->id . '_med.jpg';
				break;
		}

		return  'https://' . Yii::app()->params['hosts']['front'] . "/img/doctorsNew/" . $img;

	}

	/**
	 * Выборка только активных врачей
	 *
	 * @return $this
	 */
	public function active()
	{
		$criteria = new \CDbCriteria();
		$criteria->condition =  $this->getTableAlias() . ".status = :doctor_status";
		$criteria->params = [":doctor_status" => DoctorModel::STATUS_ACTIVE];

		$criteria->with = [
			'clinics' => [
				'select' => false,
				'joinType' => 'INNER JOIN',
				'scopes' => ['active', 'clinicsAndPrivateDoctors'],
			]
		];

		$this
			->getDbCriteria()
			->mergeWith($criteria);

		return $this;
	}

	/**
	 * выборка врачей в городе
	 *
	 * @param integer $cityId
	 * @return $this
	 */
	public function inCity($cityId)
	{
		$criteria = new \CDbCriteria();
		$criteria->with = [
			'clinics'  => [
				'joinType' => 'INNER JOIN',
				'scopes'   => ['inCity' => $cityId],
			]
		];
		$criteria->together = true;

		$this
			->getDbCriteria()
			->mergeWith($criteria);

		return $this;
	}

	/**
	 * выборка врачей в городе
	 *
	 * @param integer $sectorId
	 * @return $this
	 */
	public function inSector($sectorId)
	{
		$criteria = new \CDbCriteria();
		$criteria->with = [
			'sectors'  => [
				'select' => false, // Нельзя убирать, а то потом выборка кривая получается
				'joinType' => 'INNER JOIN',
				'condition'   => 'sectors.id = :sector_id',
				'params' => [':sector_id' => $sectorId],
			]
		];
		$criteria->together = true;

		$this
			->getDbCriteria()
			->mergeWith($criteria);

		return $this;
	}

	/**
	 * Поиск по альясу
	 * 
	 * @param string $rewriteName
	 *
	 * @return $this
	 */
	public function byRewriteName($rewriteName)
	{
		$this->getDbCriteria()
			->mergeWith(
				[
					'condition' => $this->getTableAlias() . '.rewrite_name = :rn',
					'params' => [':rn' => $rewriteName]
				]
			);

		return $this;
	}

	/**
	 * Врачи с выездом
	 *
	 * @param bool $departure
	 *
	 * @return $this
	 */
	public function withDeparture($departure = true)
	{
		$this->getDbCriteria()
			->mergeWith(
				[
					'condition' => 'departure = :departure',
					'params' => [':departure' => ($departure) ? 1 : 0 ],
				]
			);

		return $this;
	}

	/**
	 * Поиск врача по координатам
	 *
	 * @param int $lat
	 * @param int $lng
	 * @param int $radius радиус поиска врача от заданной точки в км
	 * @return $this
	 */
	public function byCoordinates($lat, $lng, $radius)
	{
		$criteria  = new CDbCriteria();
		$criteria->with = [
			'clinics' => [
				'select' => false,
				'scopes' => [
					'byCoordinates' => [$lat, $lng, $radius]
				]
			]
		];

		$this->getDbCriteria()->mergeWith($criteria);

		return $this;
	}

	/**
	 * Получить название статуса
	 *
	 * @param int $status
	 *
	 * @return string | null
	 */
	public static function getStatusTitle($status)
	{
		return isset(self::$statuses[$status]) ? self::$statuses[$status] : null;
	}

	/**
	 * Исключаем из поиска заданных врачей
	 *
	 * @param int[] $doctors
	 *
	 * @return $this
	 */
	public function except($doctors)
	{
		if (count($doctors)) {
			$criteria = new \CDbCriteria();
			$criteria->addNotInCondition('t.id', $doctors);
			$this
				->getDbCriteria()
				->mergeWith($criteria);
		}
		return $this;
	}

	/**
	 * Получение моделей докторов
	 *
	 * @param array  $params
	 * @param string $type
	 * @param int    $countOnPage
	 *
	 * @return DoctorModel[]
	 */
	public function getItems($params = [], $type = '', $countOnPage = 10)
	{
		$result = [];
		foreach ($this->findItems($params, $type, $countOnPage) as $item) {
			$result[] = $item[1];
		}

		return $result;
	}

	/**
	 * Получение массива с типами поиска и моделей докторов
	 *
	 * @param array  $params
	 * @param string $type
	 * @param int    $countOnPage
	 *
	 * @return array [searchType, DoctorModel]
	 */
	public function findItems($params = [], $type = '', $countOnPage = 10)
	{
		$result = [];
		$data = $this->searchItemsParams($params, $type)->findAll();
		foreach ($data as $doctor) {
			$result[] = [null, $doctor];
		}

		if (empty($params['start'])) {
			if (!empty($params['count']) && intval($params['count']) < $countOnPage) {
				$countOnPage = intval($params['count']);
			}

			$params['start'] = 0;
			$params['count'] = $countOnPage - count($data);

			if ($params['count'] > 0) {
				// Находим врачей на ближайших станциях метро
				if (!empty($params['nearest']) && (!empty($params['stations']) || !empty($params['district']))) {
					$nearestDoctors = self::model()
						->searchItemsParams($params, 'nearest', $data)
						->findAll();
					foreach ($nearestDoctors as $doctor) {
						$result[] = ['geo', $doctor];
					}
					$data = array_merge($data, $nearestDoctors);
					$params['count'] = $countOnPage - count($data);
				}

				// Находим лучших врачей
				if ($params['count'] > 0 && !empty($params['best']) && !empty($params['speciality'])) {
					$bestDoctors = self::model()
						->searchItemsParams($params, 'best', $data)
						->findAll();
					foreach ($bestDoctors as $doctor) {
						$result[] = ['best', $doctor];
					}
				}
			}
		}

		return $result;
	}

	/**
	 * Получение моделей докторов
	 * params: city, speciality, stations, clinics, except, order, start, count
	 *
	 * @param array         $params
	 * @param string        $type
	 * @param DoctorModel[] $exceptItems
	 * @param bool          $isCount
	 *
	 * @return DoctorModel
	 */
	public function searchItemsParams($params = [], $type = '',  $exceptItems = [], $isCount = false)
	{
		switch ($type) {
			case 'nearest':
				if (!empty($params['stations'])) {
					$params['stations'] = array_merge($params['stations'], StationModel::model()->getNearestStationIds($params['stations'], 20));
				}
				if (!empty($params['district'])) {
					$district = DistrictModel::model()->findByPk($params['district']);
					if (!is_null($district)) {
						$params['district'] = $district->getNeighborDistrictIds();
					}
				}
				break;

			case 'best':
				$params['order'] = 't.rating DESC';
				unset($params['stations']);
				break;
		}

		if (isset($params['city'])) {
			$this->inCity($params['city']);
		}
		if (!empty($params['speciality']) && (int)$params['speciality']) {
			$this->bySpeciality($params['speciality']);
		}
		if (!empty($params['stations'])) {
			$this->inStations($params['stations']);
			$params['area'] = null;
			$params['district'] = null;
		}
		if (!empty($params['district'])) {
			$params['district'] = is_array($params['district']) ? $params['district'] : [$params['district']];
			$this->inDistricts($params['district']);
			$params['area'] = null;
		}
		if (!empty($params['area'])) {
			$this->inArea($params['area']);
		}
		if (isset($params['clinics'])) {
			$this->inClinics($params['clinics']);
		}
		if (isset($params['deti'])) {
			$this->withKidsReception($params['deti']);
		}
		if (isset($params['na-dom'])) {
			$this->withDeparture($params['na-dom']);
		}
		if (!empty($params['street'])) {
			$this->inStreet($params['street']);
		}

		if (isset($params['lat']) && isset($params['lng']) && isset($params['radius'])) {
			$this->byCoordinates($params['lat'], $params['lng'], $params['radius']);
		}

		$except = isset($params['except']) && is_array($params['except']) ? $params['except'] : [];
		foreach ($exceptItems as $item) {
			$except[] = $item->id;
		}
		if ($except) {
			$this->except($except);
		}

		$criteria = new \CDbCriteria();

		$selectString = ['*'];
		if (!$isCount) {
			$selectString[] = 'IF(t.experience_year > 0, ' . date('Y') . ' - t.experience_year, 0) as experience';
			$selectString[] = 'CASE WHEN t.rating = 0 THEN t.total_rating ELSE t.rating END as sort_rating';
			$criteria->order = isset($params['order']) ? $params['order'] : 't.rating_internal DESC';
			if (isset($params['start'])) {
				$criteria->offset = $params['start'];
			}
			if (isset($params['count'])) {
				$criteria->limit = $params['count'];
			}
		}

		if (isset($params['lat']) && isset($params['lng'])) {
			$selectString[] = "SQRT(POW((clinics.latitude-{$params['lat']}), 2) + POW((clinics.longitude-{$params['lng']}), 2)) AS distance";
		}

		$criteria->select = $selectString;
		$criteria->group = 't.id';

		$this->getDbCriteria()->mergeWith($criteria);

		return $this->active();
	}

	/**
	 * Ближайшие доктора
	 *
	 * @param int $limit
	 *
	 * @return DoctorModel[]
	 */
	public function nearestDoctors($limit = 5)
	{
		$clinic = $this->getDefaultClinic();
		if (!$clinic) {
			return [];
		}

		$sectorIds = [0]; //если у доктора нет специальности, чтобы sectors.id IN ( не падал

		foreach ($this->sectors as $sector) {
			$sectorIds[] = $sector->id;
		}

		$criteria = new \CDbCriteria();
		$criteria->with = [
			'clinics' => [
				'select' => false,
				'joinType' => 'INNER JOIN',
			],
			'sectors' => [
				'select' => false,
				'joinType' => 'INNER JOIN',
				'condition' => 'sectors.id IN (' . implode(', ', $sectorIds) . ')'
			]
		];
		$criteria->mergeWith([
				'condition' => 't.id != :doctor_id AND clinics.longitude > :left AND clinics.longitude < :right AND clinics.latitude > :bottom AND clinics.latitude < :top',
				'params'    => [
					':doctor_id' => $this->id,
					':left' => Coordinate::lngPlusDistance($clinic->longitude, -1 * StreetModel::DISTANCE_EXTENDED_BOUND),
					':right' => Coordinate::lngPlusDistance($clinic->longitude, StreetModel::DISTANCE_EXTENDED_BOUND),
					':top' => Coordinate::latPlusDistance($clinic->latitude, StreetModel::DISTANCE_EXTENDED_BOUND),
					':bottom' => Coordinate::latPlusDistance($clinic->latitude, -1 * StreetModel::DISTANCE_EXTENDED_BOUND),
				],
			]);
		$criteria->order = 't.rating desc';
		$criteria->limit = $limit;
		$criteria->together = true;

		return DoctorModel::model()
			->active()
			->findAll($criteria);
	}

	/**
	 * Вычисление суммарного рейтинга
	 *
	 * @return string
	 */
	public function calcTotalRating()
	{
		$totalRating = [
			$this->rating_education * self::FACTOR_RATING_EDUCATION,
			$this->rating_ext_education * self::FACTOR_RATING_EXT_EDUCATION,
			$this->rating_experience * self::FACTOR_RATING_EXPERIENCE,
			$this->rating_academic_degree * self::FACTOR_RATING_DEGREE,
			$this->rating_clinic * self::FACTOR_RATING_CLINIC,
		];
		$totalRating = array_sum($totalRating);

		if ($this->rating_opinion > 0) {
			$totalRating = $totalRating + (int)$this->rating_opinion;
		}

		$totalRating = $totalRating > 5 ? 5 : $totalRating;
		$totalRating = $totalRating < 0 ? 0 : $totalRating;

		return $totalRating;
	}

	/**
	 * Вычисление внутреннего рейтинга
	 *
	 * @return float|mixed|string
	 */
	public function calcInternalRating()
	{
		$rating = !empty($this->rating) ? $this->rating : $this->total_rating;

		// Рейтинг клиники (максимальный, если несколько)
		$clinicRatings = [];
		foreach ($this->clinics as $clinic) {
			$clinicRatings[] = $clinic->rating_total;
		}
		if (count($clinicRatings) > 0) {
			$clinicRating = max($clinicRatings);

			// Формула вычисления окончательного рейтинга
			$rating = $clinicRating * self::FACTOR_RATING_INTERNAL + $rating;
		}

		return $rating;
	}

	/**
	 * Перед сохранением
	 */
	public function beforeSave()
	{
		$this->update_tips = 1;

		$this->total_rating = $this->calcTotalRating();
		$this->rating_internal = $this->calcInternalRating();

		return parent::beforeSave();
	}

	/**
	 * После сохранения
	 */
	public function afterSave()
	{
		parent::afterSave();

		if($this->getScenario() != self::SCENARIO_SKIP_UPDATE_RATING){
			$this->updateRatings();
		}

		ClinicModel::updateDoctor($this->id);
	}

	/**
	 * Обновить все рейтинги
	 */
	public function updateRatings()
	{
		foreach (RatingStrategyModel::model()->findAll() as $strategy) {
			$strategy->saveDoctorRatings($this);
		}
	}

	/**
	 * Получить среднюю конверсию по врачам
	 *
	 * @return float
	 */
	public function getAvgConversion()
	{
		if(is_null(self::$avgConversion)){
			$criteria = new \CDbCriteria();
			$criteria->select = 'avg(conversion)';

			$command = $this->getCommandBuilder()
				->createFindCommand($this->getTableSchema(), $criteria, $this->getTableAlias());

			self::$avgConversion = $command->queryScalar();
		}

		return self::$avgConversion;
	}

	/**
	 * Чистка кеша средней конверсии
	 *
	 * @return $this
	 */
	public function flushAvgConversion()
	{
		self::$avgConversion = null;
		return $this;
	}

	/**
	 * Установить клиники
	 *
	 * @param int[] $clinicIds
	 * @throws \CDbException
	 */
	public function setClinics(array $clinicIds)
	{
		$doctorClinicsIds = array_map(
			function (ClinicModel $x) {
				return $x->id;
			},
			$this->clinics
		);

		//удаляю, которых нету в массиве
		foreach(array_diff($doctorClinicsIds, $clinicIds) as $clinicId){
			if($cl = DoctorClinicModel::model()->findDoctorClinic($this->id, $clinicId)){
				$cl->delete();
			}
		}

		//добавляю, которых нет в релейшонах
		foreach(array_diff($clinicIds, $doctorClinicsIds) as $clinicId){
			$cl = new DoctorClinicModel();
			$cl->doctor_id = $this->id;
			$cl->clinic_id = $clinicId;
			$cl->type = DoctorClinicModel::TYPE_DOCTOR;
			$cl->save();
		}

		//перегружаю связи если они были уже вызваны
		if($this->hasRelated('clinics')) {
			$this->getRelated('clinics', true);
		}

		if($this->hasRelated('doctorClinics')){
			$this->getRelated('doctorClinics', true);
		}

		$this->updateRatings();
	}

	/**
	 * Получение количества отзывов врача
	 *
	 * @return string
	 */
	public function countReviews()
	{
		if ($this->countReviews === null) {
			$this->countReviews = (int) DoctorOpinionModel::model()
				->allowed()
				->byDoctor($this->id)
				->count();
		}

		return $this->countReviews;
	}

	/**
	 * Добавляет в выборку врачей принимающих или нет детей
	 *
	 * @param bool $isKids является ли врач детским
	 *
	 * @return DoctorModel
	 */
	public function withKidsReception($isKids = true)
	{
		$criteria = new CDbCriteria;
		$criteria->condition =  $this->getTableAlias() . ".kids_reception = :kids_reception";
		$criteria->params["kids_reception"] = $isKids;

		$this->getDbCriteria()->mergeWith($criteria);

		return $this;
	}

	/**
	 * Находит и получает список врачей по исходному слову
	 * Используется для autocomplete
	 *
	 * @param string $term искомое совпадение
	 *
	 * @return array
	 */
	public function getListByTerm($term)
	{
		$list = [];

		$criteria = new CDbCriteria;
		$criteria->condition = "t.name LIKE :name";
		$criteria->params["name"] = "{$term}%";

		foreach ($this->findAll($criteria) as $model) {
			foreach ($model->clinics as $clinic) {
				$list[] = [
					"id" => $model->id,
					"value" => "{$model->name} ({$clinic->name}) - {$model->getStatusTitle($model->status)}",
					"clinicId" => $clinic->id
				];
			}

		}

		return $list;
	}

	/**
	 * Получает названия клиник
	 *
	 * @return string
	 */
	public function getClinicNames()
	{
		$clinicNames = "";

		foreach ($this->getActiveClinics() as $clinic) {
			$clinicNames .= "{$clinic->name}, ";
		}

		if ($clinicNames) {
			$clinicNames = substr($clinicNames, 0, -2);
		}

		return $clinicNames;
	}

	/**
	 * Получение списка специальностей
	 *
	 * @return array
	 */
	public function getSpecialityNames()
	{
		$list = [];

		foreach ($this->sectors as $sector) {
			$list[] = $sector->name;
		}

		return $list;
	}

	/**
	 * Получение стажа
	 *
	 * @return int
	 */
	public function getExperience()
	{
		return !empty($this->experience_year) ? date('Y') - $this->experience_year : 0;
	}

	/**
	 * Проверяет, активен ли врач
	 *
	 * @return bool
	 */
	public function isActive()
	{
		if ($this->status != self::STATUS_ACTIVE) {
			return false;
		}

		foreach ($this->clinics as $clinic) {
			if (
				$clinic->status == $clinic::STATUS_ACTIVE
				&& ($clinic->isClinic == "yes" || $clinic->isPrivatDoctor == "yes")
			) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Исключает из выборки других врачей
	 *
	 * @return DoctorModel
	 */
	public function withoutAnother()
	{
		$criteria = new CDbCriteria();
		$criteria->condition = $this->getTableAlias() . ".status != :status";
		$criteria->params = ["status" => DoctorModel::STATUS_ANOTHER_DOCTOR];

		$this->getDbCriteria()->mergeWith($criteria);

		return $this;
	}

	/**
	 * Получает список врачей по указанным станциям метро + ближайшим в случае малого количества
	 *
	 *
	 * @todo ПЕРЕДЕЛАТЬ НА КЛАСС, КОТОРЫЙ ДЕЛАЕТ МАКС ПО ПОЛУЧЕНИЮ СПИСКА И УДАЛИТЬ ЭТОТ МЕТОД
	 *
	 * @param int[] $stations идентификаторы станций метро
	 * @param int   $limit    лимит записей
	 *
	 * @return DoctorModel[]
	 */
	public function findAllForStationsWithClosest($stations, $limit = 10)
	{
		$closestModel = clone $this;

		$this->atStations($stations);
		$list = $this->findAll();

		if (count($list) >= $limit) {
			return $list;
		}

		$notInIds = [];
		foreach ($list as $item) {
			$notInIds[] = $item->id;
		}

		$criteria = new CDbCriteria();
		$criteria->with = [
			'clinics'                          => [
				'select' => false,
				'joinType' => 'INNER JOIN',
			],
			'clinics.stations'                 => [
				'select' => false,
				'joinType' => 'INNER JOIN',
			],
			'clinics.stations.closestStations' => [
				'select' => false,
				'joinType' => 'INNER JOIN',
			],
		];

		$criteria->limit = $limit - count($list);
		$criteria->addInCondition("closestStations.station_id", $stations);
		$closestModel->getDbCriteria()->mergeWith($criteria, "OR");

		$criteria = new CDbCriteria();
		$criteria->addNotInCondition("t.id", $notInIds);
		$closestModel->getDbCriteria()->mergeWith($criteria);
		if ($closestModel->getDbCriteria()->order === "rating_internal DESC") {
			$closestModel->getDbCriteria()->order = "closestStations.priority, t.rating_internal DESC";
		}

		$list = array_merge($list, $closestModel->active()->findAll());

		return $list;
	}

	/**
	 * Получает список врачей по указанным районам + ближайшим в случае малого количества
	 *
	 * @todo ПЕРЕДЕЛАТЬ НА КЛАСС, КОТОРЫЙ ДЕЛАЕТ МАКС ПО ПОЛУЧЕНИЮ СПИСКА И УДАЛИТЬ ЭТОТ МЕТОД
	 *
	 * @param int[] $districts идентификаторы районов
	 * @param int   $limit     лимит записей
	 *
	 * @return DoctorModel[]
	 */
	public function findAllForDistrictsWithClosest($districts, $limit = 10)
	{
		$closestModel = clone $this;

		$this->inDistricts($districts);
		$list = $this->findAll();

		if (count($list) >= $limit) {
			return $list;
		}

		$notInIds = [];
		foreach ($list as $item) {
			$notInIds[] = $item->id;
		}

		$criteria = new CDbCriteria();
		$criteria->with = [
			'clinics'                           => [
				'select' => false,
				'joinType' => 'INNER JOIN',
			],
			'clinics.district'                  => [
				'select' => false,
				'joinType' => 'INNER JOIN',
			],
			'clinics.district.closestDistricts' => [
				'select' => false,
				'joinType' => 'INNER JOIN',
			],
		];

		$criteria->limit = $limit - count($list);
		$criteria->addInCondition("closestDistricts.district_id", $districts);
		$closestModel->getDbCriteria()->mergeWith($criteria, "OR");

		$criteria = new CDbCriteria();
		$criteria->addNotInCondition("t.id", $notInIds);
		$closestModel->getDbCriteria()->mergeWith($criteria);
		if ($closestModel->getDbCriteria()->order === "rating_internal DESC") {
			$closestModel->getDbCriteria()->order = "closestDistricts.priority, t.rating_internal DESC";
		}

		$list = array_merge($list, $closestModel->active()->findAll());

		return $list;
	}

	/**
	 * Выборка по стажу
	 *
	 * @param int $exp
	 *
	 * @return $this
	 */
	public function experienceMore($exp)
	{
		$expYear = date('Y') - $exp;

		$criteria = new CDbCriteria();
		$criteria->condition = "experience_year <= :year AND experience_year IS NOT NULL AND experience_year <> 0";
		$criteria->params = ["year" => $expYear];

		$this->getDbCriteria()->mergeWith($criteria);

		return $this;
	}

	/**
	 * Выборка врачей, для которых нужно обновить rating_experience
	 *
	 * @return $this
	 */
	public function needToUpdateRatingExperience()
	{
		$criteria = new CDbCriteria();
		$criteria->condition = $this->getTableAlias() . ".rating_experience < 5";
		$this->getDbCriteria()->mergeWith($criteria);

		return $this->experienceMore(15);
	}

	/**
	 * Признак есть у врача фото или нет
	 *
	 * @link https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=23855152
	 * @return bool
	 */
	public function getPhotoExists()
	{
		return !empty($this->image);
	}


	/**
	 * Средняя оценка за отзывы "Отлично"
	 *
	 * @link https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=23855152
	 * @return bool
	 */
	public function getHasExcellentReviews()
	{
		$rating = DoctorOpinionModel::model()->getAverageRating($this->id);
		return ($rating > 4.8);
	}

	/**
	 * Есть ли у врача скидка
	 *
	 * @link https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=23855152
	 * @return bool
	 */
	public function getHasDiscount()
	{
		return !empty($this->special_price);
	}

	/**
	 * адрес страницы с карточкой врача
	 *
	 * @return string
	 */
	public function getUrl()
	{
		return "http://" .	Yii::app()->params['hosts']['front'] . "/doctor/" . $this->rewrite_name;
	}

	/**
	 * Учитывать рейтинг для доктора
	 *
	 * @return $this
	 */
	public function withRating()
	{
		$this->getDbCriteria()->mergeWith([
			'with' => [
				'doctorClinics' => [
					'select' => false,
					'join' => 'INNER JOIN rating r ON (r.object_id = doctorClinics.id AND r.object_type = :objectType and r.strategy_id = :strategyId)',
					'params' => [
						'strategyId' => \Yii::app()->rating->getId(RatingStrategyModel::FOR_DOCTOR),
						'objectType' => RatingModel::TYPE_DOCTOR,
					],
				],
			],
		]);

		return $this;
	}

	/**
	 * Есть ли у врача онлайн запись в клинику
	 *
	 * @param int $clinicId
	 *
	 * @return bool
	 */
	public function canOnlineBooking($clinicId)
	{
		if (!Yii::app()->params['onlineBooking']) {
			return false;
		}

		foreach ($this->doctorClinics as $dc) {
			if ($dc->clinic_id == $clinicId) {
				return $dc->has_slots;
			}
		}


		return true;
	}

	/**
	 * Получить список всех фотографий фрача с параметрами, который должны быть на жестом диске
	 *
	 * @return array
	 */
	public function getImageList()
	{
		return [
			'/' . $this->id . '_original_cropped.jpg' => [
				'with_watermark' => false,
			],
			'/' . $this->id . '_med.jpg' => [
				'with_watermark' => true,
				'size' => ['w' => 160, 'h' => 218]
			],
			'/' . $this->id . '_small.jpg' => [
				'with_watermark' => true,
				'size' => ['w' => 110, 'h' => 150]
			],
			'/1x1/' . $this->id . '.jpg' => [
				'with_watermark' => true,
				'size' => ['w' => 73, 'h' => 100]
			],
			//без логотипа
			'/' . $this->id . '.160x218.jpg' => [
				'with_watermark' => false,
				'size' => ['w' => 160, 'h' => 218]
			],
			'/' . $this->id . '.110x150.jpg' => [
				'with_watermark' => false,
				'size' => ['w' => 110, 'h' => 150]
			],
			'/' . $this->id . '.73x100.jpg' => [
				'with_watermark' => false,
				'size' => ['w' => 73, 'h' => 100]
			],
		];
	}

	/**
	 * Сохранение всех фотографий из одной оригинальной
	 *
	 * @param string $imageName имя оригинальной картинки
	 * @param int $x координата х - откуда обрезать
	 * @param int $y координата y - откуда обрезать
	 * @param int $w ширина обреза
	 * @param int $h длина обреза
	 * @param bool $watermarkAtRight слева или справа расположить логотип докдок
	 *
	 * @return bool
	 * @throws \Exception
	 */
	public function saveImage($imageName = null, $x = 0, $y = 0, $w = 160, $h = 218, $watermarkAtRight = false)
	{
		$weight = 160;
		$height = 218;

		is_null($imageName) && $imageName = $this->id . ".jpg";

		$uploadPath = \Yii::app()->params['path']['upload'];
		$originalImageName = $uploadPath . "/doctor/" . $imageName;

		$this->image = $imageName;

		if (!$this->save(true, ['image'])) {
			return false;
		}

		$originalImage = Image::open($originalImageName);
		$originalImage->crop($x, $y, $w, $h);

		foreach($this->getImageList() as $name => $params){
			$fullName = $uploadPath . '/doctor' . $name;

			$image = clone $originalImage;

			if($params['with_watermark']){
				$image->cropResize($weight, $height);
				$image->save($fullName);

				ImageHelper::addDocDocLogo($fullName, $watermarkAtRight);
				$image = Image::open($fullName);
			}

			if(isset($params['size'])){
				$image->cropResize($params['size']['w'], $params['size']['h']);
			}

			$image->save($fullName);

			chmod($fullName, FILE_MODE);
		}

		return true;
	}

	/**
	 * Удаление фотографии и ее копий всех размеров
	 *
	 * @return bool
	 */
	public function deleteImage()
	{
		$originalIMage = DIRECTORY_SEPARATOR . $this->image;
		$this->image = null;

		if (!$this->save(true, ['image'])) {
			return false;
		}

		$images = array_keys($this->getImageList());
		$images[] = $originalIMage;

		$uploadPath = \Yii::app()->params['path']['upload'] . '/doctor';

		foreach($images as $image){
			$fullPath = $uploadPath  . $image;
			unlink($fullPath);
		}

		return true;
	}


	/**
	 * Изменить клиники доктора
	 *
	 * @param ClinicModel[] $newClinics
	 *
	 * @return bool
	 */
	public function saveRelationClinics($newClinics)
	{
		$result = true;

		$diff = ActiveRecordHelper::arrayRecordsDiff($newClinics, $this->getRelated('clinics', true));

		if ($diff['add']) {
			foreach ($diff['add'] as $item) {
				$dc = new DoctorClinicModel();
				$dc->doctor_id = $this->id;
				$dc->clinic_id = $item->id;
				$dc->type = DoctorClinicModel::TYPE_DOCTOR;
				if (!$dc->save()) {
					return false;
				}
			}
		}

		if ($diff['delete']) {
			$result = DoctorClinicModel::model()->deleteAllByAttributes([
				'doctor_id' => $this->id,
				'clinic_id' => array_keys($diff['delete']),
				'type' => DoctorClinicModel::TYPE_DOCTOR,
			]);
		}

		return $result;
	}

	/**
	 * Изменить специальности доктора
	 *
	 * @param SectorModel[] $newSectors
	 *
	 * @return bool
	 */
	public function saveRelationSectors($newSectors)
	{
		$result = true;

		$diff = ActiveRecordHelper::arrayRecordsDiff($newSectors, $this->getRelated('sectors', true));

		if ($diff['add']) {
			foreach ($diff['add'] as $item) {
				$ds = new DoctorSectorModel();
				$ds->doctor_id = $this->id;
				$ds->sector_id = $item->id;
				if (!$ds->save()) {
					return false;
				}
			}
		}

		if ($diff['delete']) {
			$result = DoctorSectorModel::model()->deleteAllByAttributes([
				'doctor_id' => $this->id,
				'sector_id' => array_keys($diff['delete']),
			]);
		}

		return $result;
	}
}
