<?php

namespace dfs\docdoc\models;

use dfs\docdoc\extensions\AdvancedCActiveRecord;
use dfs\docdoc\validators\CommaSeparatedEmailValidator;
use dfs\docdoc\objects\Coordinate;
use dfs\docdoc\objects\Phone;
use dfs\docdoc\extensions\DateTimeUtils;
use dfs\docdoc\validators\CommaSeparatedPhoneValidator;
use dfs\docdoc\validators\PhoneValidator;
use dfs\docdoc\validators\StringValidator;
use Yii;
use CDbCriteria;

/**
 * This is the model class for table "clinic".
 *
 * The followings are the available columns in table 'clinic':
 *
 * @property integer                   $id
 * @property string                    $created
 * @property integer                   $status
 * @property double                    $rating
 * @property string                    $phone
 * @property string                    $asterisk_phone
 * @property string                    $phone_appointment
 * @property string                    $email
 * @property string                    $url
 * @property string                    $name
 * @property string                    $short_name
 * @property string                    $rewrite_name
 * @property string                    $contact_name
 * @property string                    $text
 * @property string                    $attach
 * @property string                    $note
 * @property string                    $password
 * @property string                    $parent_clinic_id
 * @property string                    $city_id
 * @property string                    $age_selector
 * @property string                    $status_new
 * @property string                    $city
 * @property string                    $street
 * @property integer                   $street_id
 * @property string                    $house
 * @property string                    $aliase
 * @property float                     $latitude
 * @property float                     $longitude
 * @property string                    $description
 * @property string                    $operator_comment
 * @property string                    $logoPath
 * @property string                    $shortDescription
 * @property string                    $isDiagnostic
 * @property string                    $isClinic
 * @property string                    $isPrivatDoctor
 * @property string                    $weekdays_open
 * @property string                    $weekend_open
 * @property string                    $saturday_open
 * @property string                    $sunday_open
 * @property string                    $sort4commerce
 * @property string                    $open_4_yandex
 * @property string                    $schedule_state
 * @property string                    $sendSMS
 * @property string                    $rating_1
 * @property string                    $rating_2
 * @property string                    $rating_3
 * @property string                    $rating_4
 * @property string                    $rating_total
 * @property string                    $settings_id
 * @property string                    $diag_settings_id
 * @property integer                   $show_in_advert
 * @property integer                   $district_id
 * @property string                    $external_id
 * @property boolean                   $online_booking
 * @property float                     $conversion
 * @property int                       $hand_factor
 * @property float                     $admission_cost
 * @property bool                      $validate_phone
 * @property string                    $notify_emails
 * @property string                    $notify_phones
 * @property bool                      $contract_signed
 * @property float                     $rating_show
 * @property string                    $way_on_foot
 * @property string                    $way_on_car
 * @property integer                   $min_price
 * @property integer                   $max_price
 * @property integer                   $count_reviews
 * @property bool                      $scheduleForDoctors
 * @property string                    $email_reconciliation
 * @property integer                   $manager_id
 * @property bool                      $discount_online_diag
 *
 * The followings are the available model relations:
 *
 * @property DoctorModel[]             $doctors
 * @property DoctorClinicModel[]       $clinicDoctors
 * @property StationModel[]            $stations
 * @property DiagnosticaModel[]        $diagnostics
 * @property ClinicAdminModel[]        $admins
 * @property DistrictModel             $district
 * @property ClinicContractModel[]     $tariffs
 * @property ClinicModel               $parentClinic
 * @property ClinicScheduleModel[]     $schedule
 * @property RatingModel[]             $ratings
 * @property ClinicModel[]             $branches
 * @property ClinicPartnerPhoneModel[] $partnerPhones
 * @property CityModel                 $clinicCity
 * @property ClinicPhoneModel[]        $phones
 * @property RequestModel[]            $requests
 * @property ApiClinicModel            $apiClinic
 * @property DiagnosticClinicModel[]   $diagnosticClinics
 * @property ClinicPhotoModel[]        $photos
 *
 * @method ClinicModel find
 * @method ClinicModel with
 * @method ClinicModel findByPk($id)
 * @method ClinicModel[] findAll
 * @method ClinicModel[] findAllByPk
 * @method ClinicModel ordered()
 * @method ClinicModel cache
 *
 */
class ClinicModel extends AdvancedCActiveRecord
{
	/**
	 * Статусы клиники
	 */
	const STATUS_REGISTERED = 1;
	const STATUS_NEW = 2;
	const STATUS_ACTIVE = 3;
	const STATUS_BLOCKED = 4;
	const STATUS_REMOVE = 5;

	/**
	 * Типы клиник
	 */
	const TYPE_CLINIC = 1;
	const TYPE_DIAGNOSTIC = 2;
	const TYPE_DOCTOR = 3;

	/**
	 * Стоимость запись по умолчанию
	 */
	const DEFAULT_ADMISSION_COST = 600;

	/**
	 * При этом сценарии не пересчитывается рейтинг в afterSave
	 */
	const SCENARIO_SKIP_UPDATE_RATING = 'SCENARIO_SKIP_UPDATE_RATING';

	/**
	 * Кеш для средней конверсии
	 *
	 * @var
	 */
	protected static $avgConversion;

	/**
	 * Кеш для медианной конверсии группированной по городам
	 *
	 * @var null
	 */
	protected static $medianaConversions = [];

	/**
	 * Кеш для нижнего квантиля группированному по городам
	 *
	 * @var null
	 */
	protected static $lowerQuantiles = [];

	/**
	 * Уровени цен (значения - это цена с которой начинается уровень)
	 *
	 * @var array
	 */
	protected static $priceLevel = [
		'высокая' => 900,
		'средняя' => 500,
		'низкая' => 0,
	];


	/**
	 * Минимальная стоимость приема (selectPriceMinMax)
	 *
	 * @var string | null
	 */
	public $minPrice = null;

	/**
	 * Максимальная стоимость приема (selectPriceMinMax)
	 *
	 * @var string | null
	 */
	public $maxPrice = null;


	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return ClinicModel the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'clinic';
	}

	/**
	 * Зависимости
	 *
	 * @return array
	 */
	public function relations()
	{
		return [
			'doctors' => [
				self::HAS_MANY,
				DoctorModel::class,
				['doctor_id' => 'id'],
				'through' => 'clinicDoctors',
			],
			'tariffs' => [
				self::HAS_MANY,
				ClinicContractModel::class,
				'clinic_id',
			],
			'clinicDoctors' => [
				self::HAS_MANY,
				DoctorClinicModel::class,
				'clinic_id',
				'on' => 'clinicDoctors.type = ' . DoctorClinicModel::TYPE_DOCTOR,
			],
			'stations' => [
				self::MANY_MANY,
				'dfs\docdoc\models\StationModel',
				'underground_station_4_clinic(clinic_id, undegraund_station_id)'
			],
			'diagnostics' => [
				self::MANY_MANY,
				'dfs\docdoc\models\DiagnosticaModel',
				'diagnostica4clinic(clinic_id, diagnostica_id)'
			],
			'diagnosticClinics' => [
				self::HAS_MANY,
				DiagnosticClinicModel::class,
				'clinic_id',
			],
			'admins' => [
				self::MANY_MANY,
				'dfs\docdoc\models\ClinicAdminModel',
				'admin_4_clinic(clinic_id, clinic_admin_id)'
			],
			'district' => [
				self::BELONGS_TO,
				'dfs\docdoc\models\DistrictModel',
				'district_id'
			],
			'parentClinic' => [
				self::BELONGS_TO,
				'dfs\docdoc\models\ClinicModel',
				'parent_clinic_id'
			],
			'branches' => [
				self::HAS_MANY,
				'dfs\docdoc\models\ClinicModel',
				'parent_clinic_id'
			],
			'clinicCity' => [
				self::BELONGS_TO,
				'dfs\docdoc\models\CityModel',
				'city_id'
			],
			'schedule' => [
				self::HAS_MANY,
				'dfs\docdoc\models\ClinicScheduleModel',
				'clinic_id',
				'order' => 'week_day'
			],
			'ratings' => [
				self::HAS_MANY,
				RatingModel::class,
				'object_id',
				'condition' => 'object_type = :type',
				'params' => [':type' => RatingModel::TYPE_CLINIC],
			],
			'partnerPhones' => [
				self::HAS_MANY,
				'dfs\docdoc\models\ClinicPartnerPhoneModel',
				'clinic_id',
			],
			'phones' => [
				self::HAS_MANY,
				ClinicPhoneModel::class,
				'clinic_id',
			],
			'requests' => [
				self::HAS_MANY,
				RequestModel::class,
				'clinic_id',
			],
			'apiClinic' => [
				self::BELONGS_TO,
				ApiClinicModel::class,
				'external_id',
			],
			'photos' => [
				self::HAS_MANY,
				ClinicPhotoModel::class,
				'clinic_id',
			],
		];
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['status,name', 'required'],
			['external_id', 'unique', 'allowEmpty' => true],
			['phone_appointment', 'required', 'on' => 'backend'],
			['status', 'numerical', 'integerOnly' => true],
			['phone, asterisk_phone', PhoneValidator::class],
			['rewrite_name', StringValidator::class, 'type' => "uid",],
			['notify_emails', CommaSeparatedEmailValidator::class],
			['notify_phones', CommaSeparatedPhoneValidator::class],
			['rating', 'numerical'],
			['phone, name, contact_name, email, url, phone_appointment', 'length', 'max' => 512],
			['attach', 'file', 'types' => 'doc, docx, pdf, txt, rtf', 'allowEmpty' => true],
			['url', 'url', 'defaultScheme' => 'http'],
			['email', 'email', 'allowEmpty' => true],
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			[
				'id, created, status, rating, phone, phone_appointment, name, short_name, contact_name, text, email',
				'safe',
				'on' => 'insert, update, search'
			],
			[
				'asterisk_phone',
				'exist',
				'attributeName' => 'number',
				'className' => PhoneModel::class,
				'allowEmpty' => true,
				'message' => 'Подменный телефон не найден',
				'skipOnError' => true,
			],
			[
				'asterisk_phone',
				'exist',
				'attributeName' => 'number',
				'className' => PhoneModel::class,
				'allowEmpty' => true,
				'criteria' => ['with' => 'provider', 'condition' => 'provider.enabled'],
				'message' => 'Подменный телефон не активен',
				'skipOnError' => true,
			],
		];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'created' => 'Дата создания',
			'status' => 'Статус',
			'rating' => 'Рейтинг',
			'email' => 'E-mail',
			'phone' => 'Контактный телефон клиники',
			'phone_appointment' => 'Телефон для записи',
			'name'              => 'Название клиники',
			'contact_name'      => 'Имя контактного лица',
			'text'              => 'Описание',
			'doctors'           => 'Врачи',
			'url'               => 'Сайт клиники',
			'attach'            => 'Приложение',
			'conversion'        => 'Конверсия',
			'hand_factor'       => 'Ручной коэффициент',
			'admission_cost'    => 'Стоимость записи',
			'short_name'        => 'Название клиники',
			'contract_signed'   => 'Договор подписан',
			'city_id'           => 'Город',
		];
	}

	/**
	 * Возвращает список статусов
	 *
	 * @return array
	 */
	public static function getStatusList()
	{
		$status_list = [
			self::STATUS_REGISTERED => 'Регистрация',
			self::STATUS_NEW => 'Новая',
			self::STATUS_ACTIVE => 'Активная',
			self::STATUS_BLOCKED => 'Заблокирована',
			self::STATUS_REMOVE => 'К удалению',
		];

		return $status_list;
	}

	/**
	 * Поис клиник, имеющих тарифы
	 *
	 * @return \CActiveDataProvider
	 */
	public function searchClinicsForBilling()
	{
		$criteria = new \CDbCriteria();
		$criteria->scopes = [
			'withTariffs' => [ContractModel::getContractList()]
		];

		$criteria->together = true;
		$criteria->with = [
			'tariffs.contract' =>  [
				'joinType' => 'INNER JOIN',
				'scopes' => [
					'realContracts' => []
				]
			],
			'clinicCity' => [
				'joinType' => 'INNER JOIN',
			]
		];

		if (!empty($this->id)) {
			$criteria->compare('t.id', $this->id);
		}
		if (empty($_GET['ajax'])) {
			$criteria->order = 't.id';
		}
		if (!empty($this->city_id)) {
			$criteria->compare('t.city_id', $this->city_id);
		}

		if (!empty($this->short_name)) {
			$criteria->condition = 't.short_name LIKE (:clinic_name) ';
			$criteria->params = [':clinic_name' => '%' . $this->short_name . "%"];
		}

		return new \CActiveDataProvider(
			new ClinicModel(),
			[
				'criteria'   => $criteria,
				'pagination' => [
					'pageSize' => 40,
				],
				'sort' => [
					'attributes' => [
						'id',
						'short_name' => [
							'asc'  => 't.name',
							'desc' => 't.name DESC',
						],
						'city_id' => [
							'asc'  => 'clinicCity.title',
							'desc' => 'clinicCity.title DESC',
						],
					],
				],
			]
		);
	}

	/**
	 * Выборка только активных клиник
	 *
	 * @return $this
	 */
	public function active()
	{
		$this->getDbCriteria()->mergeWith(
			[
				'condition' => $this->getTableAlias() . ".status = " . self::STATUS_ACTIVE,
			]
		);

		return $this;
	}

	/**
	 * Проверка активна клиника или нет
	 *
	 * @return bool
	 */
	public function isActive()
	{
		return $this->status == self::STATUS_ACTIVE;
	}

	/**
	 * Выборка только тех клиник, с которыми мы работаем
	 *
	 * @return $this
	 */
	public function relevant()
	{
		$this->getDbCriteria()->mergeWith(
			[
				'condition' => "asterisk_phone IS NOT NULL AND status = " . self::STATUS_ACTIVE,
			]
		);

		return $this;
	}

	/**
	 * Выборка только клиник
	 *
	 * @return $this
	 */
	public function onlyClinic()
	{
		$this->getDbCriteria()->mergeWith(
			[
				'condition' => "isClinic = 'yes' AND isPrivatDoctor = 'no'",
			]
		);

		return $this;
	}

	/**
	 * Выборка клиник и частных врачей
	 *
	 * @return $this
	 */
	public function clinicsAndPrivateDoctors()
	{
		$this->getDbCriteria()->mergeWith(
			[
				'condition' => "isClinic = 'yes' OR isPrivatDoctor = 'yes'",
			]
		);

		return $this;
	}

	/**
	 * Выборка только диагностических центров
	 *
	 * @return $this
	 */
	public function onlyDiagnostic()
	{
		$this->getDbCriteria()->mergeWith(
			[
				'condition' => "isDiagnostic = 'yes' AND isPrivatDoctor = 'no'",
			]
		);

		return $this;
	}

	/**
	 * Исключить частных врачей из выборки
	 *
	 * @return $this
	 */
	public function excludePrivateDoctor()
	{
		$this->getDbCriteria()->mergeWith(
			[
				'condition' => "isPrivatDoctor != 'yes'",
			]
		);

		return $this;
	}

	/**
	 * Выбор по типу клиник
	 *
	 * @param array $types
	 *
	 * @return $this
	 */
	public function searchByClinicType($types)
	{
		$conditions = [];
		$params = [];

		if (!empty($types['isDiagnostic'])) {
			$conditions[] = 't.isDiagnostic = :isDiagnostic';
			$params['isDiagnostic'] = $types['isDiagnostic'];
		}
		if (!empty($types['isClinic'])) {
			$conditions[] = 't.isClinic = :isClinic';
			$params['isClinic'] = $types['isClinic'];
		}
		if (!empty($types['isDoctor'])) {
			$conditions[] = 't.isPrivatDoctor = :isPrivatDoctor';
			$params['isPrivatDoctor'] = $types['isDoctor'];
		}

		if ($conditions) {
			$this->getDbCriteria()->mergeWith([
				'condition' => implode(' OR ', $conditions),
				'params' => $params,
			]);
		}

		return $this;
	}

	/**
	 * Выборка по городу
	 *
	 * @param int $city
	 *
	 * @return $this
	 */
	public function inCity($city = 1)
	{
		$this->getDbCriteria()->mergeWith(
			[
				'condition' => $this->getTableAlias() . ".city_id = :city_id",
				'params'    => [':city_id' => $city],
			]
		);

		return $this;
	}

	/**
	 * Выборка определенных клиник
	 *
	 * @param int[] $clinics
	 *
	 * @return $this
	 */
	public function inClinics($clinics)
	{
		$criteria = new \CDbCriteria();
		$criteria->addInCondition($this->getTableAlias() . '.id', $clinics);
		$this->getDbCriteria()->mergeWith($criteria);

		return $this;
	}

	/**
	 * Поиск по району
	 *
	 * @param int $district_id
	 *
	 * @return $this
	 */
	public function inDistrict($district_id)
	{
		$this->getDbCriteria()->mergeWith(
			[
				'condition' => $this->getTableAlias() . ".district_id = :district_id",
				'params'    => [':district_id' => $district_id],
			]
		);

		return $this;
	}

	/**
	 * Поиск в районах
	 *
	 * @param int[] $districts
	 *
	 * @return ClinicModel
	 */
	public function inDistricts($districts)
	{
		if (count($districts)) {
			$criteria = new \CDbCriteria();
			$criteria->select = 't.*';
			$criteria->with = [
				'district' => [
					'select' => false,
					'joinType' => 'INNER JOIN',
				]
			];
			$criteria->together = true;
			$criteria->addInCondition('district.id', $districts);
			$criteria->group = 't.id';
			$this->getDbCriteria()->mergeWith($criteria);
		}

		return $this;
	}

	/**
	 * Поиск по улице
	 *
	 * @param int $streetId
	 *
	 * @return ClinicModel
	 */
	public function inStreet($streetId)
	{
		$this->getDbCriteria()->mergeWith(
			[
				'condition' => $this->getTableAlias() . '.street_id = :street_id',
				'params'    => [':street_id' => $streetId],
			]
		);

		return $this;
	}

	/**
	 * Поиск по ближайшим улицам
	 *
	 * @param int $streetId
	 *
	 * @return $this
	 */
	public function inNearestStreet($streetId)
	{
		$street = StreetModel::model()->findByPk($streetId);

		$this->getDbCriteria()->mergeWith(
			[
				'condition' => 't.longitude > :left AND t.longitude < :right AND t.latitude > :bottom AND t.latitude < :top',
				'params'    => [
					':left'   => Coordinate::lngPlusDistance($street->bound_left, -1 * StreetModel::DISTANCE_EXTENDED_BOUND),
					':right'  => Coordinate::lngPlusDistance($street->bound_right, StreetModel::DISTANCE_EXTENDED_BOUND),
					':top'    => Coordinate::latPlusDistance($street->bound_top, StreetModel::DISTANCE_EXTENDED_BOUND),
					':bottom' => Coordinate::latPlusDistance($street->bound_bottom, -1 * StreetModel::DISTANCE_EXTENDED_BOUND),
				],
			]
		);

		return $this;
	}

	/**
	 * Поиск ближайших клиник
	 *
	 * @param int $limit
	 * @param boolean $onlyDiagnostic
	 *
	 * @return ClinicModel[]
	 */
	public function nearestClinics($limit = 5, $onlyDiagnostic = false)
	{
		$criteria = new \CDbCriteria();
		$criteria->mergeWith([
			'condition' => 't.id != :clinic_id',
			'params'    => [
				':clinic_id' => $this->id,
			],
		]);

		$criteria->order = 't.rating desc';
		$criteria->limit = $limit;
		$criteria->together = true;

		/**
		 * @var ClinicModel $model
		 */
		$model = ClinicModel::model()
			->byCoordinates($this->latitude, $this->longitude, StreetModel::DISTANCE_EXTENDED_BOUND)
			->active();

		if ($onlyDiagnostic) {
			$model->onlyDiagnostic();
		}

		return $model->findAll($criteria);
	}

	/**
	 * Поиск клиники по координатам
	 *
	 * @param int $lat
	 * @param int $lng
	 * @param int $radius радиус поиска врача от заданной точки в км
	 *
	 * @return $this
	 */
	public function byCoordinates($lat, $lng, $radius = 1)
	{
		$criteria = new \CDbCriteria();
		$criteria->condition = $this->getTableAlias() . ".latitude < :top " .
			" AND " . $this->getTableAlias() . ".latitude > :bottom " .
			" AND  " . $this->getTableAlias() . ".longitude > :left " .
			" AND  " . $this->getTableAlias() . ".longitude < :right";

		$criteria->params = [
			':left'   => Coordinate::lngPlusDistance($lng, -1 * $radius),
			':right'  => Coordinate::lngPlusDistance($lng, $radius),
			':top'    => Coordinate::latPlusDistance($lat, $radius),
			':bottom' => Coordinate::latPlusDistance($lat, -1 * $radius),
		];

		$this->getDbCriteria()->mergeWith($criteria);

		return $this;
	}

	/**
	 * Выборка вместе с филиалами
	 *
	 * @param integer $clinicId идентификатор клиники
	 *
	 * @return ClinicModel
	 */
	public function withBranches($clinicId)
	{
		$this->getDbCriteria()->mergeWith(
			[
				'condition' => $this->getTableAlias() . '.id=:clinic_id OR ' . $this->getTableAlias() . '.parent_clinic_id=:clinic_id',
				'params'    => [':clinic_id' => $clinicId],
			]
		);

		return $this;
	}

	/**
	 * Выборка клиник с активными врачами
	 *
	 * @return $this
	 */
	public function havingActiveDoctors()
	{
		$criteria = new \CDbCriteria();
		$criteria->with = [
			'doctors' => ['joinType' => 'INNER JOIN']
		];
		$criteria->compare('doctors.status', DoctorModel::STATUS_ACTIVE);
		$this->getDbCriteria()->mergeWith($criteria);

		return $this;
	}

	/**
	 * Поиск по ид администратора клиники
	 *
	 * @param integer $adminId идентификатор администратора клиники
	 *
	 * @return $this
	 */
	public function searchByAdminId($adminId)
	{
		$criteria = new \CDbCriteria();
		$criteria->join = "INNER JOIN admin_4_clinic a4c ON t.id = a4c.clinic_id OR t.parent_clinic_id = a4c.clinic_id";
		$criteria->condition = "a4c.clinic_admin_id = :admin";
		$criteria->params = [':admin' => $adminId];
		$this->getDbCriteria()->mergeWith($criteria);

		return $this;
	}

	/**
	 * Поиск по названию клиники
	 *
	 * @param  string $name
	 *
	 * @return $this
	 */
	public function searchByName($name)
	{
		if (!empty($name)) {
			$criteria = new \CDbCriteria();
			$criteria->addSearchCondition('name', $name, true);
			$criteria->addSearchCondition('short_name', $name, true, 'OR');
			$this->getDbCriteria()->mergeWith($criteria);
		}

		return $this;
	}

	/**
	 * Поиск по алиасу
	 *
	 * @param $alias
	 *
	 * @return $this
	 */
	public function searchByAlias($alias)
	{
		$this->getDbCriteria()->mergeWith(
			[
				'condition' => $this->getTableAlias() . '.rewrite_name = :rewrite_name',
				'params'    => [':rewrite_name' => $alias],
			]
		);

		return $this;
	}

	/**
	 * Поиск по станциям метро
	 *
	 * @param array $stations
	 * @param string $near
	 *
	 * @return $this
	 */
	public function searchByStations($stations, $near = 'strict')
	{
		if (count($stations)) {
			$criteria = new \CDbCriteria();
			$criteria->select = 't.*';
			$criteria->together = true;
			$criteria->group = 't.id';

			$withStations = [
				'select' => false,
				'joinType' => 'INNER JOIN',
			];

			if ($near === 'closest' || $near === 'mixed') {
				$withStations['join'] = 'INNER JOIN closest_station cs ON (cs.closest_station_id = stations_stations.undegraund_station_id)';

				if ($near === 'closest') {
					$withStations['condition'] = 'cs.priority <> 0';
				}

				$criteria->addInCondition('cs.station_id', $stations);
				$criteria->order = 'cs.priority, t.id';
			} else {
				$criteria->addInCondition('stations.id', $stations);
			}

			$criteria->with = ['stations' => $withStations];

			$this->getDbCriteria()->mergeWith($criteria);
		}

		return $this;
	}

	/**
	 * Поиск по диагн. исследованиям
	 *
	 * @param $diagnostics
	 * @param $zeroPrice
	 *
	 * @return $this
	 */
	public function searchByDiagnostics($diagnostics, $zeroPrice = true)
	{
		if (count($diagnostics)) {
			$criteria = new \CDbCriteria();
			$criteria->with = [
				'diagnostics' => [
					'select' => false,
					'joinType' => 'INNER JOIN',
				]
			];
			$criteria->together = true;
			if (!$zeroPrice) {
				$criteria->addCondition('diagnostics_diagnostics.price > 0');
			}
			$criteria->addInCondition('diagnostics.id', $diagnostics);
			$criteria->addInCondition('diagnostics.parent_id', $diagnostics, "OR");
			$criteria->group = 't.id';
			$this->getDbCriteria()->mergeWith($criteria);
		}

		return $this;
	}

	/**
	 * Поиск по специальности врача
	 *
	 * @param array $specialities
	 *
	 * @return $this
	 */
	public function searchBySpecialities($specialities)
	{
		if (count($specialities)) {
			$criteria = new \CDbCriteria();
			$criteria->with = [
				'clinicDoctors' => [
					'select' => false,
					'joinType' => 'INNER JOIN',
					'join' => 'INNER JOIN doctor_sector as ds ON (ds.doctor_id = clinicDoctors.doctor_id)',
				]
			];
			$criteria->addInCondition('ds.sector_id', $specialities);

			$this->withDoctors();

			$this->getDbCriteria()->mergeWith($criteria);
		}

		return $this;
	}

	/**
	 * Поиск клиник с активными врачами
	 *
	 * @return $this
	 */
	public function withDoctors()
	{
		$this->getDbCriteria()->mergeWith([
			'with' => [
				'clinicDoctors' => [
					'select' => false,
					'joinType' => 'INNER JOIN',
					'join' => 'INNER JOIN doctor as d ON (d.id = clinicDoctors.doctor_id AND d.status = ' . DoctorModel::STATUS_ACTIVE . ')',
				],
			],
		]);

		return $this;
	}

	/**
	 * Поиск клиник, которые показываются в объявлениях
	 *
	 * @return $this
	 */
	public function shownInAdvertising()
	{
		$this->getDbCriteria()->mergeWith(
			[
				'condition' => "show_in_advert = 1",
			]
		);

		return $this;
	}

	/**
	 * Поиск клиники по названию
	 *
	 * @param string $name
	 *
	 * @return ClinicModel
	 */
	public function byName($name)
	{
		$this->getDbCriteria()->mergeWith(
			[
				'condition' => "name = :name",
				'params'    => [':name' => $name],
			]
		);

		return $this;
	}

	/**
	 * Поиск клиники по идентификатору клиники в МИС
	 *
	 * @param string $external_id
	 *
	 * @return ClinicModel
	 */
	public function byExternalId($external_id)
	{
		$this->getDbCriteria()->mergeWith(
			[
				'condition' => "external_id = :external_id",
				'params'    => [':external_id' => $external_id],
			]
		);

		return $this;
	}

	/**
	 * Исключаем из поиска заданные клиники
	 *
	 * @param array $clinics Идентификаторы клиник
	 *
	 * @return $this
	 */
	public function except($clinics)
	{
		if (count($clinics)) {
			$criteria = new \CDbCriteria();
			$criteria->addNotInCondition('t.id', $clinics);
			$this->getDbCriteria()->mergeWith($criteria);
		}

		return $this;
	}

	/**
	 * Клиники с тарифами
	 *
	 * @param int[] $tariffIds идентификаторы тарифов
	 *
	 * @return $this
	 */
	public function withTariffs(array $tariffIds)
	{
		if (count($tariffIds)) {
			$criteria = new \CDbCriteria();
			$criteria->with = [
				'tariffs' => [
					'joinType' => 'INNER JOIN',
					'scopes' => [
						'byContract' => [$tariffIds]
					]
				]
			];
			$criteria->together = true;

			$this->getDbCriteria()->mergeWith($criteria);
		}

		return $this;
	}

	/**
	 * Поиск по диапазону координат
	 *
	 * @param float $lat1  широта 1 точки
	 * @param float $long1 долгота 1 точки
	 * @param float $lat2  широта 2 точки
	 * @param float $long2 долгота 2 точки
	 *
	 * @return $this
	 */
	public function searchByCoordinates($lat1, $long1, $lat2, $long2)
	{
		$criteria = new \CDbCriteria();
		$criteria->addBetweenCondition('t.longitude', $long1, $long2);
		$criteria->addBetweenCondition('t.latitude', $lat1, $lat2);
		$this->getDbCriteria()->mergeWith($criteria);

		return $this;
	}

	/**
	 * Получаем массив со станциями метро
	 *
	 * @return array
	 */
	public function getStations()
	{
		$data = [];

		$criteria = new \CDbCriteria();
		$criteria->join = 'INNER JOIN underground_station_4_clinic t1 ON t1.undegraund_station_id=t.id';
		$criteria->condition = 't1.clinic_id = :id';
		$criteria->params = [':id' => $this->id];
		$items = StationModel::model()->findAll($criteria);

		foreach ($items as $key => $item) {
			$data[$key]['Id'] = $item->id;
			$data[$key]['Name'] = $item->name;
			$data[$key]['Alias'] = $item->rewrite_name;
			$data[$key]['LineId'] = $item->underground_line_id;
		}

		return $data;
	}

	/**
	 * Станции клиник без повторяющихся названий
	 *
	 * @return StationModel[]
	 */
	public function getUniqueStations()
	{
		$stations = [];

		foreach ($this->stations as $st) {
			if (!isset($stations[$st->name])) {
				$stations[$st->name] = $st;
			}
		}

		return $stations;
	}

	/**
	 * Получение массива с диагностическими исследованиями
	 *
	 * @param int $selectedDiagnostic
	 *
	 * @return array
	 */
	public function getDiagnostics($selectedDiagnostic = null)
	{
		$data = [];

		$criteria = new \CDbCriteria();
		$criteria->with = 'diagnostic';
		if (!is_null($selectedDiagnostic)) {
			$criteria->compare('diagnostic.id', $selectedDiagnostic);
			$criteria->compare('diagnostic.parent_id', $selectedDiagnostic, false, 'OR');
		}
		$criteria->addCondition('t.price > 0');
		$criteria->compare('t.clinic_id', $this->id);
		$criteria->group = 't.diagnostica_id';
		$criteria->order = 'diagnostic.sort, diagnostic.name';
		$items = DiagnosticClinicModel::model()->findAll($criteria);

		foreach ($items as $item) {
			if ($item->diagnostic) {
				$data[] = [
					'Id'            => $item->diagnostica_id,
					'Name'          => $item->diagnostic->name,
					'ReductionName' => $item->diagnostic->reduction_name,
					'Pid'           => $item->diagnostic->parent_id,
					'Price'         => round($item->price),
					'SpecialPrice'  => $item->special_price,
				];
			}
		}

		return $data;
	}

	/**
	 * Получаем объект телефона
	 *
	 * @param $number
	 *
	 * @return Phone
	 */
	public function getPhone($number)
	{
		return new Phone($number);
	}

	/**
	 * Получаем расписание клиники в виде массива
	 *
	 * @return array
	 */
	public function getSchedule()
	{
		$data = [];

		foreach ($this->schedule as $item) {
			$data[$item->week_day] = [
				'Day'       => $item->week_day,
				'StartTime' => $item->start_time,
				'EndTime'   => $item->end_time,
				'DayTitle'  => $item->getWeekDayTitle(),
			];
		}

		return $data;
	}

	/**
	 * Получение массива клиник
	 *
	 * @param \CDbCriteria $criteria
	 *
	 * @return array
	 */
	public static function getItems($criteria)
	{
		$data = [];

		$items = ClinicModel::model()->findAll($criteria);
		foreach ($items as $item) {
			$tmp['Id'] = $item->id;
			$tmp['Title'] = $item->name;
			$tmp['ShortName'] = $item->short_name;
			$tmp['RewriteName'] = $item->rewrite_name;
			$tmp['URL'] = $item->url;
			$tmp['Logo'] = !empty($item->logoPath) ? $item->logoPath : 'logo_default.jpg';
			$tmp['TotalRating'] = $item->rating_total;
			$tmp['Phone'] = (new Phone($item->phone))->prettyFormat('+7 ');
			$tmp['PhoneDigit'] = Phone::strToNumber($item->phone);
			$tmp['AsteriskPhone'] = (new Phone($item->asterisk_phone))->prettyFormat('+7 ');
			$tmp['AsteriskPhoneDigit'] = Phone::strToNumber($item->asterisk_phone);
			$tmp['PhoneAppointment'] = (new Phone($item->phone_appointment))->prettyFormat('+7 ');
			$tmp['PhoneAppointmentDigit'] = Phone::strToNumber($item->phone_appointment);
			$tmp['Age'] = $item->age_selector;
			$tmp['Longitude'] = $item->longitude;
			$tmp['Latitude'] = $item->latitude;
			$tmp['City'] = $item->city;
			$tmp['House'] = $item->house;
			$tmp['Street'] = $item->street;
			$tmp['Description'] = $item->shortDescription;
			$tmp['MetroList'] = $item->getStations();
			$tmp['Schedule'] = array_values($item->getSchedule());

			$metro = [];
			foreach ($tmp['MetroList'] as $m) {
				$metro[] = $m['Name'];
			}
			$tmp['Metro'] = implode(', ', $metro);
			$tmp['Area'] = $item->district ? $item->district->name : null;

			array_push($data, $tmp);
		}

		return $data;
	}


	/**
	 * Получение платных диагностических центров
	 *
	 * @param array $diagnostics
	 * @param array $exceptionIds
	 * @param int   $cityId
	 *
	 * @return $this
	 */
	public function paidItems($diagnostics, $exceptionIds, $cityId)
	{
		$criteria = new \CDbCriteria();
		$criteria->compare('status', self::STATUS_ACTIVE);
		$criteria->compare('show_in_advert', 1);
		$criteria->compare('city_id', $cityId);
		$criteria->compare('isDiagnostic', 'yes');
		if (!empty($diagnostics)) {
			$criteria->join = "INNER JOIN diagnostica4clinic d4c ON d4c.clinic_id = t.id";
			$criteria->addInCondition('d4c.diagnostica_id', $diagnostics);
		}
		$criteria->addNotInCondition('t.id', $exceptionIds);
		$this->getDbCriteria()->mergeWith($criteria);

		return $this;
	}

	/**
	 * Получение случайной клиники из массива моделей с учетом исключенных
	 *
	 * @param mixed $items
	 * @param mixed $exceptions
	 *
	 * @return mixed
	 */
	public static function getRandomItem($items, $exceptions = [])
	{
		if (count($items) > 0) {
			$randNumber = mt_rand(0, count($items) - 1);
			while (in_array($randNumber, $exceptions)) {
				$randNumber = mt_rand(0, count($items));
			}

			return $items[$randNumber];
		}

		return null;
	}

	/**
	 * Получение логотипа клиники
	 *
	 * @param string $file
	 *
	 * @return string
	 */
	public function getLogo($file = 'logo_default.gif')
	{
		$path = 'http://' . Yii::app()->params['hosts']['front'] . '/upload/kliniki/logo/';

		if (!empty($this->logoPath)) {
			$file = $this->logoPath;
		}

		return $path . $file;
	}

	/**
	 * Получение адреса
	 * Если есть в конце точка, то она убирается
	 *
	 * @return string
	 */
	public function getAddress()
	{
		$address = !empty($this->street) ? "{$this->street}, {$this->house}" : "";

		if (!$address) {
			return $address;
		}

		if (mb_substr($address, -1, null, "UTF-8") === ".") {
			$address = mb_substr($address, 0, -1, "UTF-8");
		}

		return $address;
	}

	/**
	 * Получение диагностик с ценами
	 *
	 * @param string $sort
	 *
	 * @return mixed
	 */
	public function getDiagnosticsWithPrices($sort = 'sort')
	{
		$sql = "SELECT d.*, p.name AS parent_name, p.reduction_name AS parent_short_name
				FROM diagnostica d
				  INNER JOIN diagnostica4clinic d4c ON d4c.diagnostica_id=d.id
				  LEFT JOIN diagnostica p ON p.id=d.parent_id
				  WHERE d4c.clinic_id = :clinic_id
				  ORDER BY {$sort}, d.name";

		return \Yii::app()->db
			->createCommand($sql)
			->bindValue(':clinic_id', $this->id)
			->queryAll();
	}

	/**
	 * Телефон для диагностики
	 *
	 * @return Phone
	 */
	public function diagnosticPhone()
	{
		if (!empty($this->asterisk_phone)) {
			$number = $this->asterisk_phone;
		} elseif (!empty($this->phone)) {
			$number = $this->phone;
		} else {
			$command = \Yii::app()->db
				->createCommand()
				->select("number_p")
				->from("clinic_phone")
				->where(
					"clinic_id = :clinic_id",
					[
						":clinic_id" => $this->id,
					]
				)->limit(1);

			$number = $command->queryScalar();
		}

		return new Phone($number);
	}

	/**
	 * Проверка наличия диагностики у клиники
	 *
	 * @param int $diagnosticsId
	 *
	 * @return bool
	 */
	public function hasDiagnostic($diagnosticsId)
	{
		if (empty($diagnosticsId)) {
			return false;
		}
		$dc = DiagnosticClinicModel::model()->findByPk(
			[
				'diagnostica_id' => $diagnosticsId,
				'clinic_id'      => $this->id,
			]
		);

		return (!empty($dc) && !empty($dc->price));
	}

	/**
	 * Лучшие клиники, сортировка по рейтингу
	 *
	 * @return $this
	 */
	public function theBest()
	{
		$this->getDbCriteria()->order = 'rating_total DESC';

		return $this;
	}

	/**
	 * Выборка клиник, которые имеют API
	 *
	 * @return $this
	 */
	public function hasApi()
	{
		$this->getDbCriteria()->mergeWith(
			[
				'condition' => " external_id IS NOT NULL AND external_id <> ''",
			]
		);

		return $this;
	}

	/**
	 * Сортировка по стоимости диагностики
	 *
	 * @param string $direction
	 * @param string $field
	 *
	 * @return $this
	 */
	public function sortByPrice($direction = 'ASC', $field = 'minPrice')
	{
		$criteria = $this->getDbCriteria();
		$criteria->with['diagnostics'] = [
			'select' => false,
			'joinType' => 'INNER JOIN',
		];
		$criteria->select .= ', (CASE
				WHEN diagnostics_diagnostics.special_price > 0
				THEN diagnostics_diagnostics.special_price
				ELSE diagnostics_diagnostics.price
			END) AS sortPrice';
		$criteria->select .= ', MIN(diagnostics_diagnostics.price) AS minPrice';
		$criteria->order = $field . ' ' . ($direction === 'ASC' ? 'ASC' : 'DESC');

		return $this;
	}

	/**
	 * Дефолтная сортировка для диагностики
	 *
	 * @return $this
	 */
	public function sortForCommerce()
	{
		$criteria = $this->getDbCriteria();
		$criteria->with['diagnostics'] = [
			'select' => false,
			'joinType' => 'INNER JOIN',
		];
		$criteria->together = true;
		$criteria->select .= ', (CASE WHEN diagnostics_diagnostics.special_price > 0 THEN 0 ELSE 1 END) as spPriceSort';
		$criteria->order = "t.sort4commerce, spPriceSort, t.id DESC";
		$criteria->group = 't.id';

		return $this;
	}

	/**
	 * Сортировка по степени удаленности от станции
	 *
	 * @param array $stations
	 *
	 * @return $this
	 */
	public function sortByClosestStation($stations = [])
	{
		$criteria = $this->getDbCriteria();
		$criteria->join = '
			INNER JOIN underground_station_4_clinic u4c ON u4c.clinic_id = t.id
			INNER JOIN closest_station cs ON cs.closest_station_id = u4c.undegraund_station_id
		';
		$criteria->addInCondition('cs.station_id', $stations);
		$criteria->order = 'cs.priority, t.sort4commerce';
		$criteria->group = 't.id';

		return $this;
	}

	/**
	 * Сохранение идентификатора в МИС
	 *
	 * @param string $external_id
	 *
	 * @return bool
	 */
	public function saveClinicExternalId($external_id)
	{
		$this->external_id = $external_id;

		return $this->save(true, ['external_id']);
	}


	/**
	 * @param string $phone
	 *
	 * @return ClinicModel $this
	 */
	public function byPhone($phone)
	{
		$c = new \CDbCriteria();
		$c->compare('phone', $phone);

		$c1 = new \CDbCriteria();
		$c1->join = 'LEFT JOIN clinic_phone AS ph ON (ph.clinic_id = id)';
		$c1->condition = 'ph.number_p = :phone';
		$c1->params = [':phone' => $phone];

		$c->mergeWith($c1, 'OR');

		$this->getDbCriteria()
			->mergeWith($c);

		return $this;
	}

	/**
	 * Поиск по подменному телефону
	 *
	 * @param string $replacedPhone
	 *
	 * @return ClinicModel $this
	 */
	public function byReplacedPhone($replacedPhone)
	{
		$this->getDbCriteria()
			->mergeWith(
				[
					'condition' => 'asterisk_phone = :ap',
					'params'    => [':ap'  => $replacedPhone,]
				]
			);

		return $this;
	}

	/**
	 * Поиск по подменному телефону, включая партнерские
	 *
	 * @param string[] $replacedPhone
	 *
	 * @return $this
	 */
	public function byReplacedPhoneWithPartner(array $replacedPhone)
	{
		$this
			->getDbCriteria()
			->distinct = true;

		$this
			->getDbCriteria()
			->mergeWith(
				[
					'with' => [
						'partnerPhones' => [
							'joinType' => 'LEFT JOIN',
							'with'     => [
								'phone' => [
									'joinType' => 'LEFT JOIN'
								],
							]
						],
					]
				]
			);

		$this
			->getDbCriteria()
			->addInCondition('phone.number', $replacedPhone)
			->addInCondition('asterisk_phone', $replacedPhone, 'OR');

		return $this;
	}

	/**
	 * Проверка, что клиника находится в одной группе с заданной
	 *
	 * @param int $clinicId Идентификатор клиники, относительно которой проверяется связь с данной клиникой
	 *
	 * @return bool
	 */
	public function relatedWithClinic($clinicId)
	{
		$model = self::model()->findByPk($clinicId);
		if (is_null($model)) {
			return false;
		}

		return $this->getParentId() === $model->getParentId();
	}

	/**
	 * Получение ид головной клиники
	 *
	 * @return int
	 */
	public function getParentId()
	{
		return $this->parent_clinic_id ?: $this->id;
	}

	/**
	 * Определение тарифа, на основании которого нужно рассчитывать стоимость заявки
	 *
	 * @param RequestModel $request
	 *
	 * @return ClinicContractModel|null
	 */
	public function getRequestContract(RequestModel $request)
	{
		foreach ($this->tariffs as $tariff) {
			if ($tariff->isTariffForRequest($request)) {
				return $tariff;
			}
		}

		//если нет контракта у данной клиники, ищем у родителя
		if ($this->parentClinic !== null) {
			foreach ($this->parentClinic->tariffs as $tariff) {
				if ($tariff->isTariffForRequest($request)) {
					return $tariff;
				}
			}
		}

		return null;
	}

	/**
	 * Минимальная цена приема в клинике
	 *
	 * @return float|null
	 */
	public function getMinPrice()
	{
		return $this->min_price;
	}

	/**
	 * Получение контрактов по типу заявки
	 *
	 * @param int $kind
	 *
	 * @return ClinicContractModel[]
	 */
	public function getContractsByKind($kind)
	{
		$tariffs = [];

		foreach ($this->getClinicContracts() as $tariff) {
			if ($tariff->contract->kind == $kind) {
				$tariffs[$tariff->contract_id] = $tariff;
			}
		}

		return $tariffs;
	}

	/**
	 * Получить контракт клиники (тариф)
	 *
	 * @param int $contractId
	 *
	 * @return ClinicContractModel | null
	 */
	public function getClinicContract($contractId)
	{
		foreach ($this->getClinicContracts() as $tariff) {
			if ($tariff->contract_id == $contractId) {
				return $tariff;
			}
		}

		return null;
	}

	/**
	 * Время работы клиники
	 *
	 * @return array | null
	 */
	public function getScheduleTime()
	{
		return \Yii::app()->db
			->createCommand("
				SELECT DATE_FORMAT(MIN(start_time), '%H:%i') AS start_time, DATE_FORMAT(MAX(end_time), '%H:%i') AS end_time
				FROM clinic_schedule
				WHERE clinic_id = :clinic_id
			")
			->bindValues([':clinic_id' => $this->id])
			->queryRow();
	}

	/**
	 * Проверка работает ли в этот день клиника
	 *
	 * @param int $datetime
	 *
	 * @return bool
	 */
	public function checkScheduleDate($datetime)
	{
		$result = false;

		if ($this->schedule) {
			$day = intval(date('N', $datetime));
			foreach ($this->schedule as $v) {
				$clinicDay = intval($v->week_day);
				if (($clinicDay === 0 && $day < 6) || ($clinicDay > 0 && $day === $clinicDay)) {
					$result = true;
					break;
				}
			}
		} else {
			$result = true;
		}

		return $result;
	}

	/**
	 * Найти ближайшее время работы клиники к нужному времени
	 *
	 * @param int $datetime
	 * @param int | null $interval
	 *
	 * @return int
	 */
	public function getEndWorkTime($datetime, $interval = null)
	{
		if ($interval === null) {
			$interval = \Yii::app()->params['RequestProcessingTimeLimit'];
		}

		$intervals = $this->scheduleIntervals();

		$datetime -= $interval;

		$week = 0;
		$day = date('N', $datetime);
		$sec = date('G', $datetime) * 3600 + date('i', $datetime) * 60 + date('s', $datetime);

		$beginWeekTime = $datetime - (($day - 1) * 86400 + $sec);

		if ($intervals) {
			for ($i = 0; $i < 7; $i++) {
				if (isset($intervals[$day])) {
					$p = $intervals[$day];
					if ($sec > $p[0]) {
						if ($sec > $p[1]) {
							$sec = $p[1];
						}
						break;
					}
				}

				$day--;
				if ($day < 1) {
					$day = 7;
					$week = -1;
				}
				$sec = 86400;
			}
		}

		return $beginWeekTime + (($week * 7 + $day - 1) * 86400 + $sec);
	}

	/**
	 * Получение всех контрактов клиники с учетом родительской клиники
	 *
	 * @return ClinicContractModel[]
	 */
	public function getClinicContracts()
	{
		$data = [];
		foreach ($this->tariffs as $item) {
			$data[$item->contract_id] = $item;
		}

		//добавляем родительские контракты
		if ($this->parentClinic !== null) {
			foreach ($this->parentClinic->tariffs as $item) {
				if (!isset($data[$item->contract_id])) {
					$data[$item->contract_id] = $item;
				}
			}
		}

		return $data;
	}

	/**
	 * Сохранить тарифы для клиники
	 *
	 * @param array $contractIds
	 *
	 * @return bool
	 * @throws \CDbException
	 */
	public function saveTariffs($contractIds)
	{
		$ids = array_combine($contractIds, $contractIds);
		$isChange = false;

		foreach ($this->tariffs as $item) {
			if (isset($ids[$item->contract_id])) {
				unset($ids[$item->contract_id]);
			} else {
				$item->delete();
				$isChange = true;
			}
		}

		foreach ($ids as $contractId) {
			$clinicContract = new ClinicContractModel();
			$clinicContract->clinic_id = $this->id;
			$clinicContract->contract_id = $contractId;
			$clinicContract->save();
			$isChange = true;
		}

		if ($isChange && !empty($clinicContract)) {
			$to = date('Y-m-d H:i:s', strtotime('last day of -1 month 23:59:59'));
			$clinicContract->closeBilling(null, $to);
			$clinicContract->resetBilling(date('Y-m-01'));
		}

		return true;
	}

	/**
	 * Выясняет, является ли клиника проплаченной
	 * Используется для диагностики
	 *
	 * @return bool
	 */
	public function isPayForDiagnostic()
	{
		return $this->asterisk_phone;
	}

	/**
	 * После валидации
	 */
	public function afterValidate()
	{
		\Yii::app()->eventDispatcher->raiseEvent('onPhoneChangeAfterValidate', $this);
	}

	/**
	 * После сохранения
	 */
	public function afterSave()
	{
		\Yii::app()->eventDispatcher->raiseEvent('onPhoneChangeAfterSave', $this);

		if ($this->getScenario() != self::SCENARIO_SKIP_UPDATE_RATING) {
			RatingStrategyModel::model()->saveRatings($this);
		}

		parent::afterSave();
	}

	/**
	 * Получить среднюю конверсию по врачам
	 *
	 * @return float
	 */
	public function getAvgConversion()
	{
		if (is_null(self::$avgConversion)) {
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
	 */
	public function flushAvgConversion()
	{
		self::$avgConversion = null;

		return $this;
	}

	/**
	 * Получение массива с рейтингами
	 *
	 * @return array
	 */
	public function getRatings()
	{
		$data = [];
		foreach ($this->ratings as $rating) {
			$data[$rating->strategy_id] = $rating->rating_value;
		}

		return $data;
	}

	/**
	 * Перед удалением
	 *
	 * @return bool|void
	 */
	public function beforeDelete()
	{
		parent::beforeDelete();

		//удаляю связи на которых нет ключей
		RatingModel::model()->deleteAllByAttributes(
			['object_id' => $this->id, 'object_type' => RatingModel::TYPE_CLINIC]
		);

		return true;
	}

	/**
	 * Находит и получает список клиник по исходному слову
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
		$criteria->addSearchCondition($this->getTableAlias() . '.name', $term);
		$criteria->addSearchCondition($this->getTableAlias() . '.short_name', $term, true, 'OR');

		foreach ($this->findAll($criteria) as $model) {
			$list[] = [
				"id"    => $model->id,
				"value" => $model->name
			];
		}

		return $list;
	}

	/**
	 * Области выбора
	 *
	 * @return array
	 */
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
	 * Рассчитать медианную конверсию
	 *
	 * @return mixed|null
	 */
	public function getMedianaConversion()
	{
		if (!isset(self::$medianaConversions[$this->city_id])) {
			$count = self::model()->inCity($this->city_id)->active()->withConversion()->count();
			$middle = floor($count / 2); //середина

			if ($count % 2) {
				$limit = 1;
				$offset = $middle;
			} else {
				$limit = 2;
				$offset = $middle - 1;
			}

			$offset < 0 && $offset = 0;

			$sql = "select avg(conversion) from (
 						select conversion from clinic
 						where
 							city_id = {$this->city_id}
 							and status = " . self::STATUS_ACTIVE . "
 							and conversion is not null and conversion > 0
						order by conversion
 						limit $offset, $limit) as t";
			$command = $this->getDbConnection()->createCommand($sql);

			self::$medianaConversions[$this->city_id] = (float)$command->queryScalar();
		}

		return self::$medianaConversions[$this->city_id];
	}

	/**
	 * Рассчитать нижний квантиль
	 *
	 * @return mixed|null
	 * @throws \CDbException
	 */
	public function getLowerQuantile()
	{
		if (!isset(self::$lowerQuantiles[$this->city_id])) {
			$count = self::model()->inCity($this->city_id)->active()->withConversion()->count();
			$middle = floor($count / 4); //четверть

			if ($count % 4) {
				$limit = 1;
				$offset = $middle;
				//$sql = "select conversion from clinic limit $middle, 1";
			} else {
				$limit = 2;
				$offset = $middle - 1;
				//$sql = "select avg(conversion) from clinic $middle - 1, 2";
			}

			$offset < 0 && $offset = 0;

			$sql = "SELECT avg(conversion)
						FROM (
			 				SELECT conversion
			 					FROM clinic
			 				WHERE
				 				city_id =  {$this->city_id}
				 				AND status = " . self::STATUS_ACTIVE . "
				 				AND conversion IS NOT NULL AND conversion > 0
			 					ORDER BY conversion
			 					LIMIT $offset, $limit
						) AS t";
			$command = $this->getDbConnection()->createCommand($sql);

			self::$lowerQuantiles[$this->city_id] = (float)$command->queryScalar();
		}

		return self::$lowerQuantiles[$this->city_id];
	}

	/**
	 * С не нулевой конверсией
	 *
	 * @return $this
	 */
	public function withConversion()
	{
		$this->getDbCriteria()
			->mergeWith(
				[
					'condition' => 'conversion is not null and conversion > 0'
				]
			);

		return $this;
	}

	/**
	 * Возвращает конверсию клиники для расчетов
	 *
	 * @return float|mixed|null
	 */
	public function getCalculatedConversion()
	{
		$clinicConversion = $this->conversion;

		if (is_null($clinicConversion)) {
			$clinicConversion = $this->getLowerQuantile();
		} elseif ($clinicConversion == 0) {
			$clinicConversion = $this->getMedianaConversion();
		}

		return $clinicConversion;
	}

	/**
	 * Чистит кеш в статических переменных
	 */
	public function clearStaticVariableCache()
	{
		self::$medianaConversions = [];
		self::$lowerQuantiles = [];
		self::$avgConversion = null;
	}

	/**
	 * Определение принадлежит ли клиника к филиалам данной клиники
	 *
	 * @param int $clinicId
	 *
	 * @return bool
	 */
	public function hasBranch($clinicId)
	{

		if ($clinicId == $this->id) {
			return true;
		}

		if (empty($this->parent_clinic_id)) {
			foreach ($this->branches as $branch) {
				if ($branch->id == $clinicId) {
					return true;
				}
			}
		} else {
			return $this->parentClinic->hasBranch($clinicId);
		}

		return false;
	}

	/**
	 * Получение описания клиники с заменой телефона в тексте
	 *
	 * @param string $replacementPhone
	 *
	 * @return string
	 */
	public function getShortDescription($replacementPhone)
	{
		$pattern = '/(\+7+[0-9\-\(\) ]{16})/';

		return (empty($replacementPhone)) ? $this->shortDescription : preg_replace($pattern, $replacementPhone, $this->shortDescription);
	}

	/**
	 * Является ли клиника диагностическим центром
	 *
	 * @return bool
	 */
	public function isDiagnostic()
	{
		return $this->isDiagnostic == 'yes';
	}

	/**
	 * Является ли клиника клиникой (масло масляное)
	 *
	 * @return bool
	 */
	public function isClinic()
	{
		return $this->isClinic == 'yes';
	}

	/**
	 * Выборка клиник, с которыми подписан договор
	 *
	 * @return $this
	 */
	public function isSignedContract()
	{
		$this->getDbCriteria()
			->mergeWith(
				[
					'condition' => 'contract_signed = 1'
				]
			);

		return $this;
	}

	/**
	 * Геттер для почтовых ящиков
	 *
	 * @return string[]
	 */
	public function getNotifyEmails()
	{
		if ($this->notify_emails === null) {
			return ($this->parentClinic !== null && $this->parentClinic->notify_emails !== null)
				? explode(',', $this->parentClinic->notify_emails)
				: [];
		} else {
			return explode(',', $this->notify_emails);
		}
	}

	/**
	 * Геттер для уведомительных телефонов
	 *
	 * @return string[]
	 */
	public function getNotifyPhones()
	{
		if ($this->notify_phones === null) {
			return [];
		} else {
			return explode(',', $this->notify_phones);
		}
	}

	/**
	 * Поиск клиник, у которых есть заявка за заданный период, но нет тарифа
	 *
	 * @param $from
	 * @param $to
	 *
	 * @return \CActiveDataProvider
	 */
	public function searchClinicsWithoutContracts($from, $to)
	{
		$criteria = new \CDbCriteria();
		$criteria->with = [
			'requests' => [
				'scopes' => [
					'inBilling' => []
				],
				'joinType' => 'INNER JOIN',

			]
		];
		$criteria->together = true;
		$criteria->select = "t.*";
		$criteria->addCondition("requests.request_cost IS NULL");
		$criteria->addBetweenCondition('requests.req_created', strtotime($from), strtotime($to));
		$criteria->group = "requests.req_id, t.id, requests.kind";
		$criteria->order = "t.city_id, t.name";

		return new \CActiveDataProvider(
			new self(),
			[
				'criteria' => $criteria,
				'pagination' => [
					'pageSize' => 1000
				],
			]
		);
	}

	/**
	 * Учитывать рейтинг для клиники
	 *
	 * @return $this
	 */
	public function withRating()
	{
		$this->getDbCriteria()->mergeWith([
			'join' => 'INNER JOIN rating r ON (r.object_id = t.id and r.object_type = :objectType and r.strategy_id = :strategyId)',
			'params' => [
				'strategyId' => \Yii::app()->rating->getId(RatingStrategyModel::FOR_CLINIC),
				'objectType' => RatingModel::TYPE_CLINIC,
			],
		]);

		return $this;
	}

	/**
	 * Сортировка для клиник
	 *
	 * @param string $sort
	 * @param string $sortDirection
	 *
	 * @return $this
	 */
	public function sort($sort, $sortDirection)
	{
		$direction = $sortDirection == 'asc' ? 'asc' : 'desc';
		$criteria = $this->getDbCriteria();
		$alias = $this->getTableAlias();

		switch ($sort) {
			case 'reviews':
				$criteria->order = $alias . '.count_reviews ' . $direction;
				break;
			case 'price':
				$criteria->order = $alias . '.min_price ' . $direction;
				break;
			case 'name':
				$criteria->order = $alias . '.name ' . $direction;
				break;
			case 'rating':
				$criteria->order = ($direction === 'asc' ? 'IFNULL(' . $alias . '.rating_show, 100) ' : $alias . '.rating_show ')
					. $direction .  ', r.rating_value ' . $direction;
				break;
			default:
				$criteria->order = 'r.rating_value ' . $direction;
				break;
		}

		return $this;
	}

	/**
	 * Выборка для Яндекса
	 *
	 * @param string $value
	 *
	 * @return $this
	 */
	public function openForYandex($value)
	{
		$this->getDbCriteria()->mergeWith([
			'condition' => $this->getTableAlias() . '.open_4_yandex = :open_4_yandex',
			'params' => ['open_4_yandex', $value],
		]);

		return $this;
	}

	/**
	 * Добавить в выборку клиник минимальную и максимальную стоимость приёма
	 *
	 * @param PartnerModel $partner
	 *
	 * @return $this
	 */
	public function selectPriceMinMax($partner = null)
	{
		if (!$partner || $partner->use_special_price) {
			$select = 'MIN(CASE WHEN d2.special_price IS NULL THEN d2.price ELSE d2.special_price END) as minPrice, ' .
				'MAX(CASE WHEN d2.special_price IS NULL THEN d2.price ELSE d2.special_price END) as maxPrice';
		} else {
			$select = 'MIN(d2.price) as minPrice, MAX(d2.price) as maxPrice';
		}

		$this->getDbCriteria()->mergeWith([
			'select' => $select,
			'join' =>
				'LEFT JOIN doctor_4_clinic dc2 ON (dc2.clinic_id = t.id AND dc2.type = ' . DoctorClinicModel::TYPE_DOCTOR . ') ' .
				'LEFT JOIN doctor d2 ON (d2.id = dc2.doctor_id AND d2.status = ' . DoctorModel::STATUS_ACTIVE . ')',
		]);

		return $this;
	}

	/**
	 * Конверсия запись->дошел для клиники врача по сайту DD и партнерам dd.* за последние 30 дней
	 *
	 * @link https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=23855152
	 * @return float
	 */
	public function getConversionRecordToVisit()
	{
		$came = RequestModel::model()
			->createdInInterval(strtotime("-30 day"), time())
			->inClinic($this->id)
			->onlyDocDoc()
			->inStatuses([RequestModel::STATUS_CAME])
			->byKind(RequestModel::KIND_DOCTOR)
			->count();

		$record = RequestModel::model()
			->createdInInterval(strtotime("-30 day"), time())
			->inClinic($this->id)
			->onlyDocDoc()
			->inBillingState(RequestModel::BILLING_STATE_RECORD)
			->byKind(RequestModel::KIND_DOCTOR)
			->count();
		return ($record > 0) ? ($came / $record) : 0;
	}

	/**
	 * Конверсия запись->дошел для клиники врача по сайту DD и партнерам dd.* за последние 30 дней
	 *
	 * @link https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=23855152
	 * @return float
	 */
	public function getConversionRequestToRecord()
	{
		$requests = RequestModel::model()
			->createdInInterval(strtotime("-30 day"), time())
			->inClinic($this->id)
			->onlyDocDoc()
			->byKind(RequestModel::KIND_DOCTOR)
			->count();

		$record = RequestModel::model()
			->createdInInterval(strtotime("-30 day"), time())
			->inClinic($this->id)
			->onlyDocDoc()
			->inBillingState(RequestModel::BILLING_STATE_RECORD)
			->byKind(RequestModel::KIND_DOCTOR)
			->count();

		return ($requests > 0) ? ($record / $requests) : 0;
	}

	/**
	 * Сумма биллинга текущий месяц
	 *
	 * @link https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=23855152
	 * @return int
	 */
	public function getCurrentMonthBilling()
	{
		$contracts = $this->getContractsByKind(RequestModel::KIND_DOCTOR);
		if (!count($contracts)) {
			return 0;
		}
		$contract = current($contracts);
		return $contract->getRequestCostInBilling(
			date('Y-m-01'), date('Y-m-d'), 0, $this->id);
	}

	/**
	 * Сумма биллинга предыдущий месяц
	 *
	 * @link https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=23855152
	 * @return int
	 */
	public function getPrevMonthBilling()
	{
		$contracts = $this->getContractsByKind(RequestModel::KIND_DOCTOR);
		if (!count($contracts)) {
			return 0;
		}
		$contract = current($contracts);
		return $contract->getRequestCostInBilling(
			date('Y-m-01', strtotime("first day of last month")), date('Y-m-d', strtotime("last day of last month")), 0, $this->id);
	}

	/**
	 * Количество заявок в биллинге в текущий месяц
	 *
	 * @link https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=23855152
	 * @return int
	 */
	public function getCurrentMonthRequestInBilling()
	{
		$contracts = $this->getContractsByKind(RequestModel::KIND_DOCTOR);
		if (!count($contracts)) {
			return 0;
		}
		$contract = current($contracts);
		return $contract->getRequestNumInBilling(date('Y-m-01'), date('Y-m-d'), 0, $this->id);
	}

	/**
	 * Кол-во заявок текущий месяц
	 *
	 * @link https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=23855152
	 * @return int
	 */
	public function getCurrentMonthRequestNum()
	{
		return RequestModel::model()
			->createdInInterval(strtotime("first day of this month 00:00:00"))
			->inClinic($this->id)
			->byKind(RequestModel::KIND_DOCTOR)
			->count();
	}

	/**
	 * Кол-во заявок за предыдущий месяц
	 *
	 * @link https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=23855152
	 * @return int
	 */
	public function getPrevMonthRequestNum()
	{
		return RequestModel::model()
			->createdInInterval(strtotime("first day of last month 00:00:00"), strtotime("last day of last month 23:59:59"))
			->inClinic($this->id)
			->byKind(RequestModel::KIND_DOCTOR)
			->count();
	}

	/**
	 * Стоимость текущей ступеньки
	 *
	 * @link https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=23855152
	 * @return int
	 */
	public function getCurrentTariffStepCost()
	{
		$contracts = $this->getContractsByKind(RequestModel::KIND_DOCTOR);
		if (!count($contracts)) {
			return 0;
		}

		$contract = current($contracts);
		$step = $contract->getCurrentRule(0, $this->getCurrentMonthRequestInBilling());
		return ($step) ? $step->cost : 0;
	}


	/**
	 * Сколько заявок осталось до следующей ступеньки
	 *
	 * @link https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=23855152
	 * @return int
	 */
	public function getRequestsToNextTariffStep()
	{
		$contracts = $this->getContractsByKind(RequestModel::KIND_DOCTOR);
		if (!count($contracts)) {
			return 999999;
		}

		$contract = current($contracts);
		$num = $contract->getRequestNumInBilling(date('Y-m-01'), date('Y-m-d'), 0);
		$step = $contract->getNextRule(0, $num);

		return ($step) ? ($step->from_num - $num) : 0;
	}

	/**
	 * Сколько заявок в биллинге на текущей ступеньке
	 *
	 * @link https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=23855152
	 * @return int
	 */
	public function getRequestsOnCurrentTariffStep()
	{
		$contracts = $this->getContractsByKind(RequestModel::KIND_DOCTOR);
		if (!count($contracts)) {
			return 0;
		}

		$contract = current($contracts);
		$num = $contract->getRequestNumInBilling(date('Y-m-01'), date('Y-m-d'), 0);
		$step = $contract->getCurrentRule(0, $num);

		return ($step) ? ($num - $step->from_num) : 0;
	}

	/**
	 * Сколько заявок в биллинге на текущей ступеньке
	 *
	 * @link https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=23855152
	 * @return int
	 */
	public function getMonthLimitRequest()
	{
		$contracts = $this->getContractsByKind(RequestModel::KIND_DOCTOR);
		if (!count($contracts)) {
			return 999999;
		}

		$contract = current($contracts);
		return (int)$contract->requestLimits < 1 ? 999999 : $contract->requestLimits;
	}



	/**
	 * Получение названия головной клиники
	 *
	 * @return string
	 */
	public function getParentClinicName()
	{
		return !is_null($this->parentClinic) ? $this->parentClinic->short_name : $this->short_name;
	}

	/**
	 * Получение краткого названия клиники
	 *
	 * @return string
	 */
	public function getShortName()
	{
		return $this->short_name ?: $this->name;
	}

	/**
	 * ИД администратора клиники
	 *
	 * return ClinicAdminModel
	 */
	public function getAdmin()
	{
		$admins = $this->admins ?: ($this->parentClinic ? $this->parentClinic->admins : null);

		return $admins ? $admins[0] : null;
	}

	/**
	 * Сформировать фейковые слоты на основе расписания работы клиники
	 *
	 * @param string $date
	 * @param int    $interval
	 * @param int    $offset
	 * @param int    $currentTime
	 *
	 * @return array
	 */
	public function getSlots($date, $interval = 30, $offset = 0, $currentTime = 0)
	{
		if (!$currentTime) {
			$currentTime = time();
		}

		$datetime = $date ? strtotime($date) : $currentTime;
		$day = date('d-m-Y', $datetime);

		$timeNow = date('H', $currentTime) * 60 + date('i', $currentTime);
		$dayDiff = ($datetime - mktime(0, 0, 0, date('m', $currentTime), date('d', $currentTime), date('Y', $currentTime))) / 86400;

		$currentWorkTime = $this->getWorkTime($datetime);

		$workBreak = $dayDiff > 0 ? ($currentTime <= $this->getEndWorkTime($datetime, 0)) : ($currentTime >= $currentWorkTime);

		$slots = [];

		$period = $this->getWorkTimeByDay($datetime);

		if ($period) {
			$time = DateTimeUtils::timeToSec($period['start_time']) / 60;
			$timeEnd = DateTimeUtils::timeToSec($period['end_time']) / 60;

			$hour = intval($time / 60);
			$startTime = sprintf('%02d:%02d', $hour, ($time - $hour * 60));

			$workTimeOffset = date('G', $currentWorkTime) * 60 + date('i', $currentWorkTime) + $offset / 60;

			$time += $interval;
			while ($time <= $timeEnd) {
				$hour = intval($time / 60);
				$finishTime = sprintf('%02d:%02d', $hour, ($time - $hour * 60));

				$slots[$day][] = [
					'start_time'  => $startTime,
					'finish_time' => $finishTime,
					'active' => $workBreak ?
						($dayDiff > 0 ? true : ($timeNow + 7200 / 60 < ($time - $interval))) :
						($time - $interval) >= $workTimeOffset,
				];

				$startTime = $finishTime;
				$time += $interval;
			}
		}

		return $slots;
	}

	/**
	 * Возрващает дату работы клиники(проверяет является ли переданная дата рабочей, и если нет то высчитывает)
	 *
	 * @param integer $datetime
	 *
	 * @return mixed
	 */
	public function getWorkTime($datetime)
	{
		$intervals = $this->scheduleIntervals();

		$week = 0;
		$day = date('N', $datetime);
		$sec = date('G', $datetime) * 3600 + date('i', $datetime) * 60 + date('s', $datetime);

		$beginWeekTime = $datetime - (($day - 1) * 86400 + $sec);

		if ($intervals) {
			for ($i = 0; $i < 7; $i++) {
				if (isset($intervals[$day])) {
					$p = $intervals[$day];
					if ($sec < $p[0]) {
						$sec = $p[0];
						break;
					} else {
						if ($sec < $p[1]) {
							break;
						}
					}
				}

				$day++;
				if ($day > 7) {
					$day = 1;
					$week = +1;
				}
				$sec = 0;
			}
		}

		return $beginWeekTime + (($week * 7 + $day - 1) * 86400 + $sec);
	}

	protected function scheduleIntervals()
	{
		$intervals = [];

		if ($this->schedule) {
			foreach ($this->schedule as $v) {
				$day = intval($v->week_day);
				$beginTime = DateTimeUtils::timeToSec($v->start_time);
				$endTime = DateTimeUtils::timeToSec($v->end_time);

				if ($day === 0) {
					for ($i=1; $i < 6; $i++) {
						if (!isset($intervals[$i])) {
							$intervals[$i] = [ $beginTime, $endTime ];
						}
					}
				} else {
					$intervals[$day] = [ $beginTime, $endTime ];
				}
			}
		}

		return $intervals;
	}

	/**
	 * Время работы для заданного дня
	 *
	 * @param int $datetime
	 *
	 * @return array
	 */
	protected function getWorkTimeByDay($datetime)
	{
		$day = intval(date('N', $datetime));

		foreach ($this->schedule as $v) {
			$clinicDay = intval($v->week_day);
			if (($clinicDay === 0 && $day < 6) || ($clinicDay > 0 && $day === $clinicDay)) {
				return [
					'start_time' => $v->start_time,
					'end_time' => $v->end_time,
				];
			}
		}

		return null;
	}

	/**
	 * Обновление рейтинга для всех клиник
	 */
	public function updateRatingShow()
	{
		$sql = 'UPDATE clinic as c
				JOIN (
					SELECT dc.clinic_id as clinic_id, AVG(d.total_rating) as rating
					FROM doctor_4_clinic as dc
						INNER JOIN doctor as d ON (d.id = dc.doctor_id AND d.status = :doctorStatus)
					WHERE dc.type = :type AND d.total_rating > 0
					GROUP BY dc.clinic_id
				) as t ON (c.id = t.clinic_id)
			SET c.rating_show = t.rating * 2';

		$this->getDbConnection()->createCommand($sql)->execute([
			'doctorStatus' => DoctorModel::STATUS_ACTIVE,
			'type' => DoctorClinicModel::TYPE_DOCTOR,
		]);
	}

	/**
	 * Количество отзывов клиники
	 *
	 * @return int
	 */
	public function getCountReviews()
	{
		// Ограничиваем количество отзывов до 980, т.к. когда отзывов больше 999, цифра не вмещается в дизайн
		return $this->count_reviews > 980 ? 980 : $this->count_reviews;
	}

	/**
	 * Тип клиники
	 *
	 * @return string
	 */
	public function getTypeOfInstitution()
	{
		$specCount = SectorModel::model()
			->cache(86400)
			->byClinic($this->id)
			->count();

		if ($this->isDiagnostic === 'yes') {
			$type = ($specCount > 5 ? 'многопрофильный ' : '') . 'медицинский центр';
		} else {
			$type = ($specCount > 5 ? 'многопрофильная ' : 'медицинская ') . 'клиника';
		}

		return $type;
	}

	/**
	 * Первичная стоимость приёма
	 *
	 * @return string
	 */
	public function getPriceLevel()
	{
		$minPrice = (int) $this->min_price;

		foreach (self::$priceLevel as $level => $price) {
			if ($minPrice >= $price) {
				return $level;
			}
		}

		return null;
	}

	/**
	 * Телефон для клиники выводиммый на сайте
	 *
	 * @return Phone
	 */
	public function getClinicPhone()
	{
		return $this->asterisk_phone ? new Phone($this->asterisk_phone) : null;
	}

	/**
	 * Обновление количества отзывов и цен для клиник при изменении врача
	 *
	 * @param int $doctorId
	 */
	public static function updateDoctor($doctorId)
	{
		$db = Yii::app()->getDb();

		// Устанавливаем количество отзывов для всех клиник
		$db->createCommand(
			'UPDATE clinic as c
				JOIN doctor_4_clinic AS dc ON (dc.clinic_id = c.id AND dc.type = :type)
			SET c.count_reviews = (
				SELECT COUNT(do.id)
				FROM doctor_opinion as do
					INNER JOIN doctor_4_clinic as dc2 ON (dc2.doctor_id = do.doctor_id AND dc2.type = 1)
				WHERE do.allowed = 1 AND do.status = "enable" AND dc2.clinic_id = dc.clinic_id
			)
			WHERE dc.doctor_id = :doctorId'
		)
			->execute([
				'type' => DoctorClinicModel::TYPE_DOCTOR,
				'doctorId' => $doctorId,
			]);

		// Устанавливаем минимальную и максимальную цены для всех клиник
		$db->createCommand(
			'UPDATE clinic as c
				JOIN (
					SELECT dc.clinic_id as clinic_id, MIN(d.price) as min_price, MAX(d.price) as max_price
					FROM doctor_4_clinic as dc
						INNER JOIN doctor_4_clinic as dc2 ON (dc2.doctor_id = :doctorId AND dc.clinic_id = dc2.clinic_id AND dc.type = :type)
						INNER JOIN doctor as d ON (d.id = dc.doctor_id AND d.status = :status)
					WHERE dc.type = :type
					GROUP BY dc.clinic_id
				) as t ON (c.id = t.clinic_id)
			SET c.min_price = t.min_price, c.max_price = t.max_price'
		)
			->execute([
				'status' => DoctorModel::STATUS_ACTIVE,
				'type' => DoctorClinicModel::TYPE_DOCTOR,
				'doctorId' => $doctorId,
			]);
	}

	/**
	 * Сохранение цен по исследованиям
	 *
	 * @param $diagnostics
	 *
	 * @return bool
	 */
	public function saveDiagnostics($diagnostics)
	{
		$transaction = Yii::app()->getDb()->beginTransaction();

		/**
		 * @var DiagnosticClinicModel $item
		 */
		try {
			DiagnosticClinicModel::model()->deleteAllByAttributes(['clinic_id' => $this->id]);

			foreach ($diagnostics as $item) {
				$item->clinic_id = $this->id;
				if (!$item->save()) {
					throw new \CException("Не удалось сохранить диагностику");
				}
			}
			$transaction->commit();
		} catch (\CException $e) {
			$transaction->rollback();
			return false;
		}

		return true;
	}

	/**
	 * Выборка диагностических центров с онлайн-записью
	 *
	 * @return $this
	 */
	public function hasDiscountOnOnline()
	{
		$this->getDbCriteria()
			->mergeWith([
				'condition' => "{$this->getTableAlias()}.discount_online_diag > 0",
			]);

		return $this;
	}

	/**
	 * Получение максимальной скидки на онлайн-запись по диагностике
	 *
	 * @param null $diagnosticId
	 *
	 * @return int
	 */
	public static function getMaxDiscountOnlineDiag($diagnosticId = null)
	{
		$clinics = self::model()
			->cache(3600)
			->active()
			->withTariffs([ContractModel::TYPE_DIAGNOSTIC_ONLINE])
			->searchByDiagnostics([$diagnosticId])
			->findAll([
				'order' => 'discount_online_diag desc',
				'limit' => 1,
			]);

		return (int)$clinics[0]->discount_online_diag;
	}
}
