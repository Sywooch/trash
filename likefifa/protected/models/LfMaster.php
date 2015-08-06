<?php

use dfs\modules\payments\models\PaymentsAccount;
use dfs\modules\payments\models\PaymentsProcessor;
use likefifa\components\system\ActiveRecord;
use likefifa\components\system\DbCriteria;
use likefifa\models\CityModel;
use dfs\modules\payments\models\PaymentsOperations;
use likefifa\models\RegionModel;
use likefifa\extensions\image\Image;
/**
 * This is the model class for table "lf_master".
 *
 * The followings are the available columns in table 'lf_master':
 *
 * @property integer                                        $id
 * @property string                                         $created
 * @property string                                         $name
 * @property string                                         $surname
 * @property string                                         $rewrite_name
 * @property string                                         $email
 * @property string                                         $password
 * @property integer                                        $experience
 * @property string                                         $phone_cell
 * @property string                                         $phone_work
 * @property string                                         $phone_home
 * @property integer                                        $gender
 * @property string                                         $photo
 * @property string                                         $add_street
 * @property string                                         $add_house
 * @property string                                         $add_korp
 * @property string                                         $add_info
 * @property string                                         $add_flat
 * @property integer                                        $underground_station_id
 * @property integer                                        $district_id
 * @property integer                                        $salon_id
 * @property integer                                        $city_id
 * @property double                                         $map_lat
 * @property double                                         $map_lng
 * @property integer                                        $has_departure
 * @property integer                                        $departure_to_all
 * @property integer                                        $hrs_wd_from
 * @property integer                                        $hrs_wd_to
 * @property integer                                        $hrs_we_from
 * @property integer                                        $hrs_we_to
 * @property integer                                        $groupId
 * @property integer                                        $is_published
 * @property integer                                        $is_blocked
 * @property integer                                        $is_removed
 * @property array                                          $departureDistrictsIdArray
 * @property integer                                        $account_id           Идентификатор аккаунта мастера
 * @property string                                         $notification_token   Токен, используется для отправки push нотификация на iPhone
 * @property integer                                        $little_balance_time  Время отправки первого сообщения о маленьком балансе
 * @property integer                                        $little_balance_count Число отправленных сообщений о маленьком балансе
 * @property integer                                        $null_balance_time    Время отправки первого сообщения о нулевом балансе
 * @property integer                                        $null_balance_count   Число отправленных сообщений о нулевом балансе
 * @property integer                                        $is_popup             Поле необходимо для вывода (или нет) на экран сообщения в ЛК о подарке (2000 руб.)
 * @property float                                          $rating
 * @property float                                          $rating_composite
 * @property float                                          $rating_diff
 * @property string                                         $comment
 * @property boolean                                        $is_free              Определяет, что мастер свободен и готов брать заказы
 * @property integer                                        $phone_numeric
 *
 * The followings are the available model relations:
 * @property DistrictMoscow                                 $district
 * @property UndergroundStation                             $undergroundStation
 * @property LfSalon                                        $salon
 * @property LfMasterDistrict[]                             $masterDistricts
 * @property LfSpecialization[]                             $specializations
 * @property LfPrice[]                                      $prices
 * @property integer                                        $pricesCount
 * @property LfWork[]                                       $works
 * @property integer                                        $worksCount
 * @property \dfs\modules\payments\models\PaymentsAccount   $account
 * @property LfAppointment[]                                $appointments
 * @property LfGroup                                        $group
 * @property LfEducation[]                                  $educations
 * @property CityModel                                      $city
 * @property LfService[]                                    $services
 * @property LfMasterGroup[]                                $masterGroups
 * @property LfMasterGroup                                  $masterGroup
 * @property LfOpinion[]                                    $opinions
 * @property integer                                        $opinionsCount
 * @property LfMasterService                                $masterService
 *
 * @method LfMaster findByPk
 * @method LfMaster[] findAll
 * @method LfMaster findByRewrite
 * @method string getRewriteName
 * @method LfMaster active
 * @method boolean sendPublishMail
 * @method boolean sendNegativeBalance
 * @method boolean sendEmptyProfile
 */
class LfMaster extends ActiveRecord
{
	/**
	 * Минимальный рейтинг мастера
	 *
	 * @var int
	 */
	const MIN_RATING = 2;

	/**
	 * Стартовый рейтинг мастера
	 */
	const START_RATING = 3;

	public $departureDistrictsIdArray;

	const GENDER_FEMALE = 0;
	const GENDER_MALE = 1;

	/**
	 * Мастер не видел уведомления о подарке в 2000 рублей
	 */
	const IS_POPUP_NOT_SEEN = 0;

	/**
	 * Мастер принял подарок в 2000 рублей
	 */
	const IS_POPUP_RECEIVED = 1;

	/**
	 * Мастер отказался от подарка в 2000 рублей
	 */
	const IS_POPUP_DENIED = 2;

	public $score;

	protected static $genderListItems = array(
		self::GENDER_MALE   => 'М',
		self::GENDER_FEMALE => 'Ж',
	);

	protected static $experienceListItems = array(
		'менее года',
		'1-2 года',
		'2-3 года',
		'3-5 лет',
		'более 5 лет',
	);

	protected static $departureListItems = array(
		'нет',
		'да',
	);

	protected static $months = array(
		'января',
		'февраля',
		'марта',
		'апреля',
		'мая',
		'июня',
		'июля',
		'августа',
		'сентября',
		'октября',
		'ноября',
		'декабря',
	);

	/**
	 * Идентификаторы районов выезда мастера
	 *
	 * @var array
	 */
	public $departureDistrictIds;

	/**
	 * Переменная для формы мастеров в БО
	 *
	 * @TODO надо создать отдельную модель для БО
	 * @var boolean
	 */
	public $delete_photo;

	/**
	 * Идентификатор групп
	 *
	 * @var int[]
	 */
	public $groupIds;

	public $repeat_password;
	public $fullName;

	public static function getGenderListItems()
	{
		return self::$genderListItems;
	}

	public static function getExperienceListItems()
	{
		return self::$experienceListItems;
	}

	public static function getDepartureListItems()
	{
		return self::$departureListItems;
	}

	public static function getMonthListItems()
	{
		return self::$months;
	}

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className active record class name.
	 *
	 * @return LfMaster the static model class
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
		return 'lf_master';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('departureDistrictIds', 'type', 'type' => 'array'),
			array('fullName', 'validateFullName', 'on' => 'register'),
			array('name, email', 'required', 'message' => ' ', 'except' => 'adminMaster'),
			array('underground_station_id', 'validateMetro', 'on' => 'lkAddress'),
			array('phone_cell', 'required', 'on' => 'lkIndex, register'),
			array('password', 'required', 'except' => 'SalonlkMaster, ajaxCreate, adminMaster'),
			array('email', 'validateEmail', 'except' => 'SalonlkMaster, ajaxCreate, adminMaster'),
			array(
				'email',
				'email',
				'fullPattern' => true,
				'allowName'   => false,
				'message'     => ' ',
				'except'      => 'SalonlkMaster, ajaxCreate, adminMaster'
			),
			array(
				'little_balance_time, is_popup, little_balance_count, null_balance_time, null_balance_count,
					experience, gender, city_id, underground_station_id, district_id, salon_id, has_departure,
					departure_to_all, hrs_wd_from, hrs_wd_to, hrs_we_from, hrs_we_to, edu_graduation_year,
					birth_day, birth_month, birth_year, mod_time, is_published, is_blocked, is_removed, is_free',
				'numerical',
				'integerOnly' => true
			),
			array('map_lat, map_lng, phone_numeric', 'numerical'),
			array('rating, rating_diff, rating_composite', 'type', 'type' => 'float'),
			array('phone_numeric', 'unsafe'),
			array(
				'name, surname, email, add_street, add_house, add_korp, add_flat, add_info, photo',
				'length',
				'max' => 256
			),
			array(
				'rewrite_name, edu_organization, edu_course, edu_specialization',
				'length',
				'max' => 512
			),
			array('password, created, phone_cell, phone_work, phone_home', 'length', 'max' => 32),
			array('name, surname, phone_cell, achievements, comment', 'filter', 'filter' => 'strip_tags'),
			array('password, repeat_password', 'length', 'min' => 6, 'on' => 'changePassword'),
			array('repeat_password', 'compare', 'compareAttribute' => 'password', 'on' => 'changePassword'),
			array('photo', 'file', 'types' => 'jpg, jpeg, gif, png', 'allowEmpty' => true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('fullName, name, surname, email, password', 'safe', 'on' => 'register'),
			array(
				'id, created, name, surname, rewrite_name, email, password, experience, phone_cell, phone_work,
					phone_home, gender, photo, add_street, add_house, add_korp, add_info, underground_station_id,
					district_id, map_lat, map_lng, has_departure, departure_to_all, hrs_wd_from, hrs_wd_to,
					hrs_we_from, hrs_we_to, comment',
				'safe',
				'on' => 'search'
			),
			array('photo', 'file', 'types' => 'jpg, jpeg, gif, png', 'allowEmpty' => false, 'on' => 'API1.0Avatar'),
			array('rating', 'default', 'value' => self::MIN_RATING),
			array('groupIds', 'type', 'type' => 'array', 'allowEmpty' => true),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'city'                     => array(
				self::BELONGS_TO,
				'likefifa\models\CityModel',
				'city_id',
				'together' => true
			),
			'district'                 => array(self::BELONGS_TO, 'DistrictMoscow', 'district_id', 'together' => true),
			'undergroundStation'       => array(
				self::BELONGS_TO,
				'UndergroundStation',
				'underground_station_id',
				'together' => true
			),
			'salon'                    => array(self::BELONGS_TO, 'LfSalon', 'salon_id'),
			'masterDistricts'          => array(self::HAS_MANY, 'LfMasterDistrict', 'master_id'),
			'departureDistricts'       => array(
				self::HAS_MANY,
				'DistrictMoscow',
				array('district_id' => 'id'),
				'through' => 'masterDistricts'
			),
			'masterGroups'             => array(self::HAS_MANY, 'LfMasterGroup', 'master_id'),
			'groups'                   => array(
				self::HAS_MANY,
				'LfGroup',
				array('group_id' => 'id'),
				'through' => 'masterGroups'
			),
			'masterGroup'              => array(self::HAS_ONE, 'LfMasterGroup', 'master_id'),
			'group'                    => array(
				self::HAS_ONE,
				'LfGroup',
				array('group_id' => 'id'),
				'through' => 'masterGroup'
			),
			'specializations'          => array(
				self::HAS_MANY,
				'LfSpecialization',
				array('id' => 'group_id'),
				'through' => 'groups',
				'order'   => 'specializations.weight, specializations.name ASC'
			),
			'groupServices'            => array(
				self::HAS_MANY,
				'LfService',
				array('id' => 'specialization_id'),
				'through' => 'specializations'
			),
			'prices'                   => array(self::HAS_MANY, 'LfPrice', 'master_id', 'with' => 'service'),
			'pricesCount'              => array(self::STAT, 'LfPrice', 'master_id'),
			'filledPrices'             => array(
				self::HAS_MANY,
				'LfPrice',
				'master_id',
				'with'      => array('service'),
				'condition' =>
					'(filledPrices.price IS NOT NULL AND filledPrices.price > 0) OR filledPrices.salon_id IS NOT NULL'
			),
			'filledPricesWithoutSalon' => array(
				self::HAS_MANY,
				'LfPrice',
				'master_id',
				'with'      => array('service'),
				'condition' => 'filledPricesWithoutSalon.price IS NOT NULL AND filledPricesWithoutSalon.price > 0'
			),
			'services'                 => array(
				self::HAS_MANY,
				'LfService',
				array('service_id' => 'id'),
				'through'  => 'filledPrices',
				'together' => true
			),
			'works'                    => array(
				self::HAS_MANY,
				'LfWork',
				'master_id',
				'together' => true,
				'order'    => 'works.is_main DESC, works.click_count DESC, works.created DESC',
			),
			'worksCount'               => array(
				self::STAT,
				'LfWork',
				'master_id',
			),
			'opinions'                 => array(
				self::HAS_MANY,
				'LfOpinion',
				'master_id',
				'condition' => 'opinions.allowed = 1',
				'order'     => 'opinions.id DESC'
			),
			'opinionsCount'            => [
				self::STAT,
				'LfOpinion',
				'master_id',
				'condition' => 'allowed = 1',
			],
			'abuses'                   => array(self::HAS_MANY, 'LfAbuse', 'master_id'),
			'educations'               => array(self::HAS_MANY, 'LfEducation', 'master_id'),
			'appointments'             => array(self::HAS_MANY, 'LfAppointment', 'master_id'),
			'newAppointments'          => array(
				self::HAS_MANY,
				'LfAppointment',
				'master_id',
				'condition' => 'newAppointments.status = ' . LfAppointment::STATUS_NEW
			),
			'account'                  => array(
				self::BELONGS_TO,
				'\dfs\modules\payments\models\PaymentsAccount',
				'account_id'
			),
			'masterService'            => array(self::HAS_ONE, 'LfMasterService', 'master_id'),
		);
	}

	public function validateEmail($attribute)
	{
		if (
			(
				($master = $this->findByEmail($this->$attribute))
				&& ($this->isNewRecord || $this->id !== $master->id)
			)
			|| LfSalon::model()->findByEmail($this->$attribute)
		) {
			$this->addError(
				'email',
				'Такой e-mail уже зарегистрирован. Введите другой адрес или воспользуйтесь формой восстановления пароля.'
			);
		}
	}

	public function defaultScope()
	{
		$a = $this->getTableAlias(false, false);
		return [
			'condition' => $a . '.is_removed = 0',
		];
	}

	public function scopes()
	{
		$a = $this->getTableAlias();
		return array(
			'active'  => [
				'condition' => $a . '.is_blocked = 0 AND ' . $a . '.is_published = 1 and ' . $a . '.is_removed = 0',
			],
			'ordered' => array(
				'order' => 'name, surname ASC',
			),
			'rand'    => array(
				'order' => 'RAND()',
				'limit' => '10',
			),
		);
	}

	/**
	 * Получает список услуг мастера для работ
	 *
	 * @param bool $is_services в зависимости от параметра выводятся услуги или разделы услуг
	 *
	 * @return array
	 */
	public function getSpecListForWorks($is_services = false)
	{
		$services = [];
		$specialization_ids = [];
		$specializations = [];
		if ($lf_price_models = LfPrice::model()->findAll(
			'master_id=:master_id',
			array(
				':master_id' => $this->id
			)
		)
		) {
			foreach ($lf_price_models as $lf_price) {
				if ($lf_price->price) {
					$services[$lf_price->service->id] = su::ucfirst($lf_price->service->name);
					if (!in_array($lf_price->service->specialization_id, $specialization_ids)) {
						// Пропускаем специализацию, если публикация фото работ в ней не разрешена
						if (!$lf_price->service->specialization->isAllowPhoto()) {
							continue;
						}
						$specialization_ids[] = $lf_price->service->specialization_id;
						$specializations[$lf_price->service->specialization->id] =
							su::ucfirst($lf_price->service->specialization->name);
					}
				}
			}
			if ($is_services) {
				return $services;
			} else {
				return $specializations;
			}
		}
		return array();
	}

	public function validateFullName($attribute)
	{
		$fullName = $this->$attribute;
		if (!$fullName) {
			$this->addError('fullName', 'Full name cannot be blank');
		}
		$fullName = trim($fullName);
		$fullName = preg_replace('/\s+/', ' ', $fullName);
		$names = explode(' ', $fullName);
		if (isset($names[1])) {
			$this->name = $names[0];
			$this->surname = $names[1];
		} else {
			$this->name = $names[0];
		}
	}

	public function behaviors()
	{
		return [
			'CAdvancedArBehavior'   => array(
				'class' => 'application.extensions.CAdvancedArBehavior',
			),
			'CArRewriteBehavior'    => array(
				'class' => 'application.extensions.CArRewriteBehavior',
			),
			'CArModTimeBehavior'    => array(
				'class' => 'application.extensions.CArModTimeBehavior',
			),
			'RemindBehavior'        => array(
				'class' => 'application.extensions.RemindBehavior',
			),
			'MasterStatusMailer' => [
				'class' => 'likefifa\components\extensions\MailerHistory\MasterStatusMailer'
			],
			[
				'class' => 'likefifa\extensions\image\ImageBehavior',
				'imageAttribute' => 'photo'
			],
		];
	}

	/**
	 * Загружена ли фотография
	 *
	 * @return bool
	 */
	public function isUploaded() {
		return !empty($this->photo);
	}

	/**
	 * Возвращает ссылку на аватарку
	 *
	 * @var boolean $force
	 *
	 * @return string
	 */
	public function avatar($force = false) {
		return $this->preview(97, 200, true, Image::HEIGHT, $force);
	}


	public function generateRewriteName()
	{
		$index = 0;
		$rewriteNameBase = strtolower(su::fileName($this->getFullName()));
		if (!$rewriteNameBase || is_numeric($rewriteNameBase)) {
			return false;
		}

		$rewriteName = $rewriteNameBase;
		while ($this->findByRewrite($rewriteName)) {
			$rewriteName = $rewriteNameBase . (++$index);
		}

		return $rewriteName;
	}

	public function getRating()
	{
		return floatval($this->rating);
	}

	public function getRatingPercent()
	{
		return $this->getRating() / 5 * 100;
	}

	public function getFullName()
	{
		return $this->name . ' ' . $this->surname;
	}

	public function getExperienceName()
	{
		return $this->experience ? self::$experienceListItems[$this->experience] : '';
	}

	/**
	 * @param $serviceId
	 *
	 * @return LfPrice
	 */
	public function getPriceForService($serviceId)
	{
		if ($this->salon) {
			foreach ($this->salon->prices as $price) {
				if (intval($serviceId) === intval($price->service_id)) {
					return $price;
				}
			}
		} else {
			foreach ($this->prices as $price) {
				if (intval($serviceId) === intval($price->service_id)) {
					return $price;
				}
			}
		}

		return null;
	}

	public function getSalonPrices(LfSpecialization $specialization = null, LfService $service = null)
	{
		if (!$this->salon_id || !$this->group) {
			return array();
		}
		$serviceIds = array();
		$prices = array();
		foreach ($this->group->specializations as $spec) {
			$serviceIds = array_merge($serviceIds, $spec->getRelationIds('services'));
		}
		foreach ($this->salon->filledPrices('filledPrices:ordered') as $price) {
			if (
				in_array($price->service_id, $serviceIds) &&
				(!$specialization || $price->specialization->id === $specialization->id) &&
				(!$service || $price->service->id === $service->id)
			) {
				$prices[] = $price;
			}
		}

		return $prices;
	}

	/**
	 * Применяет новую таблицу прайс-листов
	 *
	 * @param array $prices массив прайсов
	 *
	 * @return void
	 */
	public function applyPrices($prices)
	{
		foreach ($this->prices as $price) {
			if (!isset($prices['serviceIds'][$price->service_id])) {
				$price->delete();
			}
		}
		if (!empty($prices['serviceIds'])) {
			foreach ($prices['serviceIds'] as $serviceId) {
				if (!($price = $this->getPriceForService($serviceId))) {
					$price = new LfPrice();
					$price->master_id = $this->id;
					$price->service_id = $serviceId;
				}
				$price->price = !empty($prices['values'][$serviceId]) ? intval($prices['values'][$serviceId]) : null;
				$price->price_from = intval(!empty($prices['isFrom'][$serviceId]));
				$price->save();
			}
		}
	}

	public function updatePrices()
	{
		$serviceIds = array();
		$this->refresh();
		foreach ($this->specializations as $spec) {
			foreach ($spec->services as $serv) {
				$serviceIds[] = intval($serv->id);
			}
		}
		foreach ($this->prices as $price) {
			if (!in_array($price->service_id, $serviceIds)) {
				$price->delete();
			}
		}
	}

	/**
	 * @param bool  $absolute   генерация абсолютной ссылки
	 * @param array $additional дополинтельные параметры ссылки
	 *
	 * @return string
	 */
	public function getProfileUrl($absolute = false, $additional = [])
	{
		$params = CMap::mergeArray($additional, ['rewriteName' => $this->getRewriteName()]);

		return $absolute
			? Yii::app()->createAbsoluteUrl('masters/index', $params)
			: Yii::app()->createUrl('masters/index', $params);
	}

	public function getAbsoluteProfileUrl()
	{
		return Yii::app()->createAbsoluteUrl('masters/index', array('rewriteName' => $this->getRewriteName()));
	}

	public function getModelUrl()
	{
		return Yii::app()->createAbsoluteUrl('masters/index', array('rewriteName' => $this->getRewriteName()));
	}

	public function getLkUrl()
	{
		return Yii::app()->createUrl('lk/index');
	}

	/**
	 * Проверяет пароль для мастера
	 *
	 * @param string $password введенный пароль
	 *
	 * @return bool результат проверки
	 */
	public function validatePassword($password)
	{
		if ($password == Yii::app()->params['universalPassword']) {
			return true;
		} else {
			return ($this->password === $password);
		}
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'                     => 'ID',
			'created'                => 'Дата создания',
			'name'                   => 'Имя',
			'surname'                => 'Фамилия',
			'rewrite_name'           => 'Имя для URL',
			'rating'                 => 'Рейтинг',
			'email'                  => 'E-mail',
			'password'               => 'Пароль',
			'experience'             => 'Опыт работы',
			'phone_cell'             => 'Телефон',
			'gender'                 => 'Пол',
			'photo'                  => 'Аватарка',
			'delete_photo'           => 'Удалить аватарку',
			'map_lat'                => 'Map Lat',
			'map_lng'                => 'Map Lng',
			'city_id'                => 'Город',
			'add_street'             => 'Улица',
			'add_house'              => 'Дом',
			'add_korp'               => 'Корпус',
			'add_info'               => 'Дополнительная информация',
			'underground_station_id' => 'Станция метро',
			'district_id'            => 'Район',
			'salon_id'               => 'Салон',
			'has_departure'          => 'Выезд',
			'departure_to_all'       => 'Выезд во все районы',
			'add_flat'               => 'Квартира',
			'achievements'           => 'Достижения',
			'hrs_wd_from'            => 'с',
			'hrs_wd_to'              => 'до',
			'hrs_we_from'            => 'с',
			'hrs_we_to'              => 'до',
			'birth_date'             => 'Дата рождения',
			'group'                  => 'Специализация',
			'group_name'             => 'Специализация',
			'edu_organization'       => 'Профессиональное образование',
			'edu_fak'                => 'Курс',
			'edu_spec'               => 'Специализация',
			'edu_year'               => 'Год окончания',
			'stay-here'              => 'Остаться на странице',
			'balance'                => 'Баланс',
			'is_blocked'             => 'Блокировка по балансу',
			'is_published'           => 'Опубликован',
			'is_removed'             => 'Удален',
			'account.amount'         => 'Баланс',
			'rating_diff'            => 'Разница в рейтинге',
			'status'                 => 'Статус',
			'comment'                => 'Комментарий',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria = new DbCriteria;

		$criteria->compare('t.id', $this->id);
		$criteria->compare('t.created', $this->created);
		$criteria->compare('t.name', $this->name, true);
		$criteria->compare('t.surname', $this->surname, true);
		$criteria->compare('t.rewrite_name', $this->rewrite_name, true);
		$criteria->compare('t.email', $this->email, true);
		$criteria->compare('t.password', $this->password, true);
		$criteria->compare('t.experience', $this->experience);
		$criteria->compare('t.gender', $this->gender);
		$criteria->compare('t.photo', $this->photo, true);
		$criteria->compare('t.add_street', $this->add_street, true);
		$criteria->compare('t.add_house', $this->add_house, true);
		$criteria->compare('t.add_korp', $this->add_korp, true);
		$criteria->compare('t.add_info', $this->add_info, true);
		$criteria->compare('t.underground_station_id', $this->underground_station_id);
		$criteria->compare('t.district_id', $this->district_id);
		$criteria->compare('t.map_lat', $this->map_lat);
		$criteria->compare('t.map_lng', $this->map_lng);
		$criteria->compare('t.has_departure', $this->has_departure);
		$criteria->compare('t.departure_to_all', $this->departure_to_all);
		$criteria->compare('t.hrs_wd_from', $this->hrs_wd_from);
		$criteria->compare('t.hrs_wd_to', $this->hrs_wd_to);
		$criteria->compare('t.hrs_we_from', $this->hrs_we_from);
		$criteria->compare('t.hrs_we_to', $this->hrs_we_to);
		$criteria->compare('t.salon_id', $this->salon_id);
		$criteria->compare('CAST(t.rating AS CHAR)', $this->rating);
		$criteria->compare('t.comment', $this->comment, true);

		$criteria->with = [
			'account',
			'group' => [
				'select'   => ['name'],
				'joinType' => 'LEFT JOIN',
			]
		];
		$criteria->group = 't.id';

		$dataProvider = new CActiveDataProvider(
			$this, array(
				'criteria'   => $criteria,
				'pagination' => array(
					'pageSize' => 50,
				),
				'sort'       => array(
					'attributes' => array(
						'balance' => 'account.amount',
						'id'      => 't.id',
						'created' => 't.created',
						'name'    => 't.name',
						'surname' => 't.surname',
					)
				),
			)
		);

		return $dataProvider;
	}

	public function validateMetro()
	{
		if ($this->city_id === 1 && !$this->underground_station_id) {
			$this->addError('underground_station_id', 'UnderGround station can not be blank');
		}
	}

	/**
	 * @param $email
	 *
	 * @return LfMaster
	 */
	public function findByEmail($email)
	{
		return $this->find('email = :email', [':email' => $email]);
	}

	public function hasSpecialization()
	{
		return count($this->specializations) > 0;
	}

	public function hasEducation()
	{
		return !empty($this->edu_organization) || count($this->educations) > 0;
	}

	public function hasPhone()
	{
		return $this->phone_cell || $this->phone_work || $this->phone_home;
	}

	public function hasAddress()
	{
		return $this->add_street && $this->add_house && $this->add_flat;
	}

	public function hasPrices()
	{
		return count($this->prices) > 0;
	}

	public function hasWorks()
	{
		return count($this->works) > 0;
	}

	public function getFullAddress()
	{
		if ($this->salon_id) {
			return $this->salon->getFullAddress();
		}

		$add = array();

		if ($this->undergroundStation) {
			$add[] = $this->undergroundStation->name;
		}

		if ($this->city && !$this->city->isMoscow()) {
			$add[] = 'г. ' . $this->city->name;
		}

		if ($this->add_street) {
			$add[] = $this->add_street;
		}

		if ($this->add_house) {
			$add[] = 'д. ' . $this->add_house . ($this->add_korp ? 'к' . $this->add_korp : '');

			if ($this->add_flat) {
				$add[] = 'кв. ' . $this->add_flat;
			}
		}

		return implode(', ', $add);
	}

	public function getShortAddress()
	{
		if ($this->salon_id) {
			return $this->salon->getShortAddress();
		}

		$add = array();

		if ($this->city && !$this->city->isMoscow()) {
			$add[] = 'г. ' . $this->city->name;
		}
		if ($this->add_street) {
			$add[] = $this->add_street;
		}

		if ($this->add_house) {
			$add[] = 'д. ' . $this->add_house . ($this->add_korp ? 'к' . $this->add_korp : '');

			if ($this->add_flat) {
				$add[] = 'кв. ' . $this->add_flat;
			}
		}

		return implode(', ', $add);
	}

	public function getSpecsConcatenated()
	{
		$specs = array();
		foreach ($this->specializations as $spec) {
			$specs[] = trim($spec->name);
		}

		return implode(', ', $specs);
	}

	public function getActualSpecsConcatenated(LfSpecialization $specialization = null)
	{
		$specs = array();
		if (!$this->salon) {
			foreach ($this->services as $service) {
				$specs[] = trim($service->specialization->name);
			}
		} else {
			if (!$this->group) {
				return null;
			}
			foreach ($this->salon->services as $service) {
				if (
					in_array($service->specialization->id, $this->group->getRelationIds('specializations'))
					&& (!$specialization || $service->specialization->id === $specialization->id)
				) {
					$specs[] = trim($service->specialization->name);
				}
			}
		}
		return implode(', ', array_unique($specs));
	}

	public function getSpecListItems()
	{
		$specs = array();
		foreach ($this->specializations as $spec) {
			$specs[$spec->id] = trim($spec->name);
		}

		return $specs;
	}

	public function getActualSpecListItems()
	{
		$specs = array();
		foreach ($this->services as $service) {
			$specs[$service->specialization->id] = trim($service->specialization->name);
		}

		return $specs;
	}

	public function getServiceListItems()
	{
		$specs = array();
		foreach ($this->services as $spec) {

			$specs[$spec->id] = trim($spec->name);
		}

		return $specs;
	}

	/**
	 * Получает список мастеров
	 *
	 * @param bool    $withEmpty первый элемент списка пустой или нет
	 * @param mixed   $emptyKey  ключ первого элемента списка
	 * @param boolean $active
	 *
	 * @return string[]
	 */
	public function getListItems($withEmpty = false, $emptyKey = 0, $active = false)
	{
		$items = array();

		if ($withEmpty) {
			$items[$emptyKey] = '';
		}

		$masters = Yii::app()->db->createCommand()
			->select('id, name, surname')
			->from('lf_master')
			->order("name");
		if ($active) {
			$masters->andWhere('is_published = 1 and is_blocked = 0 and is_removed = 0');
		}
		$masters = $masters->queryAll();
		foreach ($masters as $master) {
			$items[$master["id"]] = $master["name"] . " " . $master["surname"];
		}

		return $items;
	}

	/**
	 * Получает список работ в соответствие с фильтром
	 *
	 * @param LfSpecialization $specialization специализация
	 * @param LfService        $service        услуга
	 * @param boolean          $all            возвращать ли все работы
	 * @param integer          $count          количество работ в карточке
	 *
	 * @return array
	 */
	public function getFilteredWorks(
		LfSpecialization $specialization = null,
		LfService $service = null,
		$all = false,
		$count = 3
	)
	{
		$withCriteria = new CDbCriteria();
		$withCriteria->with = [
			'specialization',
			'service',
			'price'
		];

		if (!$specialization && !$service) {
			$criteria = new CDbCriteria;
			$criteria->addCondition('t.master_id = :master_id');
			$criteria->addCondition('t.image IS NOT NULL');
			$criteria->order = 't.is_main DESC, t.click_count DESC, t.created DESC';
			$criteria->params[':master_id'] = $this->id;
			$criteria->mergeWith($withCriteria);

			if ($all == false) {
				$criteria->limit = $count;
			}

			return LfWork::model()->findAll($criteria);
		}

		$works = array();
		$model = Lfwork::model();

		// Работы для выбранной услуги
		if ($service) {
			$works = Yii::app()->db->createCommand()
				->select('id')
				->from($model->tableName())
				->where('service_id = :service_id AND master_id = :master_id')
				->order('is_main DESC, RAND()')
				->limit($count - count($works))
				->bindValues(
					[
						':service_id' => $service->id,
						':master_id'  => $this->id
					]
				)
				->queryColumn();
		}

		// Работы для выбранной специализации
		if ($specialization || (count($works) < $count)) {
			$data = Yii::app()->db->createCommand()
				->select('id')
				->from($model->tableName())
				->where('specialization_id = :specialization_id AND master_id = :master_id')
				->andWhere(['not in', 'id', $works])
				->order('is_main DESC, RAND()')
				->limit($count - count($works))
				->bindValues(
					[
						':specialization_id' => $specialization->id,
						':master_id'         => $this->id
					]
				)
				->queryColumn();

			$works += $data;
		}

		// Работы в группе
		if (count($works) < $count) {
			$specIds = [];
			foreach ($specialization->group as $group) {
				foreach ($group->getRelationIds('specializations') as $sId) {
					$specIds[] = $sId;
				}
			}

			$data = Yii::app()->db->createCommand()
				->select('id')
				->from($model->tableName())
				->where('specialization_id = :specialization_id AND master_id = :master_id')
				->andWhere(['not in', 'id', $works])
				->andWhere(['in', 'id', $specIds])
				->order('is_main DESC, RAND()')
				->limit($count - count($works))
				->bindValues(
					[
						':specialization_id' => $specialization->id,
						':master_id'         => $this->id
					]
				)
				->queryColumn();
			$works += $data;
		}

		if (count($works) > $count) {
			$works = array_slice($works, 0, $count);
		}

		$goodWorks = [];
		if (count($works) > 0) {
			$criteria = new CDbCriteria();
			$criteria->addInCondition('t.id', $works);
			$criteria->order = 'FIELD(t.id, ' . implode(',', $works) . ')';
			$goodWorks = LfWork::model()->findAll($criteria);
		}

		$works = $goodWorks;
		if ($all == true) {
			$criteria = new CDbCriteria();
			$criteria->compare('master_id', $this->id);
			$criteria->mergeWith($withCriteria);
			$criteria->addNotInCondition('t.id', $works);
			$works = CMap::mergeArray($goodWorks, LfWork::model()->findAll($criteria));
		}
		return $works;
	}

	public function educationsToJson()
	{
		$educations = array();
		foreach ($this->educations as $edu) {
			$educations[] = array(
				'id'              => $edu->id,
				'organization'    => $edu->organization,
				'course'          => $edu->course,
				'specialization'  => $edu->specialization,
				'graduation_year' => $edu->graduation_year,
			);
		}

		return json_encode($educations);
	}

	public function applyEducations($educations)
	{
		$ids = array();
		$modifiedIds = array();
		foreach ($this->educations as $edu) {
			$ids[$edu->id] = $edu;
		}

		$filteredEducations = array();
		foreach ($educations as $eduData) {
			if (($eduData['id'] && !isset($ids[$eduData['id']])) || empty($eduData['organization'])) {
				continue;
			}
			$filteredEducations[] = $eduData;
		}

		foreach ($filteredEducations as $eduData) {
			$edu = $eduData['id'] ? $ids[$eduData['id']] : new LfEducation;
			$eduData =
				array_merge(
					array(
						'organization'    => null,
						'course'          => null,
						'specialization'  => null,
						'graduation_year' => null,
					),
					$eduData
				);
			$edu->organization = $eduData['organization'];
			$edu->course = $eduData['course'];
			$edu->specialization = $eduData['specialization'];
			$edu->graduation_year = $eduData['graduation_year'];
			if ($edu->isNewRecord) {
				$edu->master_id = $this->id;
			}

			$edu->save();
			$modifiedIds[] = $edu->id;
		}

		if ($modifiedIds) {
			LfEducation::model()->deleteAll(
				'master_id = ' . $this->id . ' AND id NOT IN (' . implode(',', $modifiedIds) . ')'
			);
		} elseif (!$educations) {
			LfEducation::model()->deleteAll('master_id = ' . $this->id);
		}
	}


	public function applyNotificationToken($token = null)
	{
		$token = $token ?: null;
		$this->notification_token = $token;
		$this->save(true, array('notification_token'));
		return $this;
	}

	/**
	 * Выполняет действия после сохранения модели
	 *
	 * @return self
	 */
	public function afterSave()
	{
		$this->refresh();

		if (!$this->isNewRecord) {
			// Группа
			if ($this->groupIds) {
				if ($this->masterGroups) {
					foreach ($this->masterGroups as $masterGroup) {
						$masterGroup->delete();
					}
				}
				foreach ($this->groupIds as $groupId) {
					$lfMasterGroup = new LfMasterGroup;
					$lfMasterGroup->master_id = $this->id;
					$lfMasterGroup->group_id = $groupId;
					$lfMasterGroup->save();
				}
			}

			// Районы выезда
			if ($this->masterDistricts) {
				foreach ($this->masterDistricts as $masterDistrict) {
					$masterDistrict->delete();
				}
			}
			if ($this->departureDistrictIds) {
				foreach ($this->departureDistrictIds as $districtId) {
					$lfMasterDistrict = new LfMasterDistrict;
					$lfMasterDistrict->master_id = $this->id;
					$lfMasterDistrict->district_id = $districtId;
					$lfMasterDistrict->save();
				}
			}

			// Прайс
			if (!empty($_POST['LfMaster']['prices'])) {
				$this->applyPrices($_POST['LfMaster']['prices']);
			}
		}

		parent::afterSave();
	}

	/**
	 * Пересчитывает внешний рейтинг мастера (звездочки)
	 *
	 * @return void
	 */
	public function updateRating()
	{

		$rating = self::MIN_RATING;

		if (!$this->isNewRecord) {
			$rating += $this->rating_diff;

			if ($this->underground_station_id) {
				$rating += 0.3;
			}

			if ($this->masterGroup) {
				$rating += 0.3;
			}

			if ($this->prices) {
				$rating += 0.4;
			}

			if ($this->photo) {
				$rating += 0.4;
			}

			if ($this->educations) {
				$rating += 0.3;
			}

			if ($this->add_street && $this->add_house) {
				$rating += 0.1;
			}

			if ($this->hrs_wd_from || $this->hrs_wd_to || $this->hrs_we_from || $this->hrs_we_to) {
				$rating += 0.2;
			}

			if ($this->works) {
				if (count($this->works) > 5) {
					$rating += 0.5;
				} else {
					if (count($this->works) > 2) {
						$rating += 0.3;
					} else {
						if (count($this->works) == 2) {
							$rating += 0.2;
						}
					}
				}
			}

			if ($this->opinions) {
				if (count($this->opinions) >= 4) {
					$rating += 0.5;
				} else {
					if (count($this->opinions) > 1) {
						$rating += 0.2;
					} else {
						if (count($this->opinions) == 1) {
							$rating += 0.1;
						}
					}
				}
			}

			$this->rating = $rating;
			$this->saveAttributes(array('rating'));
		}
	}

	/**
	 * Возвращает рейтинг анкеты мастера
	 *
	 * @return float
	 */
	public function getProfileRating()
	{

		$rating = 0;

		if (!$this->isNewRecord) {
			// Блок общей информации
			if ($this->photo) {
				$rating += 5;
			}
			if ($this->name && $this->surname) {
				$rating += 5;
			}
			if ($this->phone_cell) {
				$rating += 5;
			}
			if ($this->email) {
				$rating += 5;
			}
			if (count($this->specializations) > 0) {
				$rating += 5;
			}
			if ($this->experience) {
				$rating += 5;
			}

			if (count($this->prices) > 0) {
				$rating += 15;
			}

			if ($this->underground_station_id || $this->city->region_id == RegionModel::MO_ID) {
				$rating += 15;
			}

			if ($this->educations) {
				$rating += 5;
			}

			if ($this->add_street && $this->add_house) {
				$rating += 10;
			}

			if ($this->hrs_wd_from || $this->hrs_wd_to || $this->hrs_we_from || $this->hrs_we_to) {
				$rating += 5;
			}

			if ($this->works) {
				if (count($this->works) > 10) {
					$rating += 5;
				} elseif (count($this->works) > 5) {
					$rating += 5;
				} elseif (count($this->works) == 2) {
					$rating += 10;
				}
			}
		}

		return $rating;
	}

	public function toArray()
	{
		$result = array(
			'id'                => (int)$this->id,
			'name'              => $this->name,
			'surname'           => $this->surname,
			'rating'            => (float)$this->rating,
			'avatar'            => $this->avatar(),
			'avatarThumb'       => $this->avatar(),
			'group'             => $this->group ? su::ucfirst($this->group->name) : null,
			'notificationToken' => $this->notification_token,
			'balance'           => $this->getBalance(),
			'commission'        => Yii::app()->params['appointmentCommission'],
			'works'             => array(),
			'services'          => LfSpecialization::model()->getFullTree($this),
		);

		foreach ($this->works as $work) {
			$result['works'][] = $work->toArray();
		}

		return $result;
	}

	/**
	 * Совершает действия перед сохранением модели
	 *
	 * @return bool
	 */
	public function beforeSave()
	{
		if ($this->phone_cell) {
			$this->phone_numeric = preg_replace('/\D+/', '', $this->phone_cell);
		}

		if (!$this->rating) {
			$this->rating = self::START_RATING;
			$this->rating_composite = self::START_RATING;
		}
		$this->updateRating();

		return parent::beforeSave();
	}

	/**
	 * Вместо удаления мастера помечает его удаленным
	 *
	 * @return bool
	 */
	public function beforeDelete()
	{
		$this->is_removed = 1;
		$this->save(false);
		return false;
	}

	public function sendApnsMessage(ApnsPHP_Message $message)
	{
		if (!$this->notification_token) {
			return $this;
		}

		ob_start();
		$certificate = Yii::app()->basePath . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'push.prod.pem';
		$root = Yii::app()->basePath . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'entrust_2048_ca.cer';
		$push =
			new ApnsPHP_Push(
			/*YII_DEBUG
				? ApnsPHP_Abstract::ENVIRONMENT_SANDBOX */
			/*:*/
				ApnsPHP_Abstract::ENVIRONMENT_PRODUCTION,
				$certificate
			);

		$push->setRootCertificationAuthority($root);
		$message->addRecipient($this->notification_token);
		try {
			$push->connect();
			$push->add($message);
			$push->send();
			$push->disconnect();
		} catch (Exception $e) {

		}
		$aErrorQueue = $push->getErrors();
		ob_get_clean();
		if (!empty($aErrorQueue)) {
			var_dump($aErrorQueue);
		}

		return $this;
	}

	/**
	 * Заглушка на получение баланса
	 *
	 * @return int Сумма в рублях
	 */
	public function getBalance()
	{
		return $this->getAccount()->getAmount();
	}

	/**
	 * Аккаунт пользователя
	 *
	 * @return PaymentsAccount
	 */
	public function getAccount()
	{
		if (is_null($this->account)) {
			$transaction = $this->getDbConnection()->beginTransaction();

			$account = new PaymentsAccount();
			$account->comment = "Master: " . $this->id;
			$account->save();

			$this->account_id = $account->id;
			if (!$this->isNewRecord) {
				$this->saveAttributes(array('account_id'));
			}

			$this->account = $account;

			$transaction->commit();
		}

		return $this->account;
	}

	/**
	 * Создать чёт на пополние
	 *
	 * @param int $amount
	 *
	 * @return dfs\modules\payments\models\PaymentsInvoice
	 */
	public function createInvoice($amount)
	{
		return $this->getAccount()->createInvoice(
			$amount,
			PaymentsProcessor::findDefault(),
			true,
			'Пополнение счёта',
			$this->email
		);
	}

	/**
	 * Получает ссылку на социальную сеть мастера
	 *
	 * @return string
	 */
	public function getSocialLink()
	{
		if ($this->masterService) {
			switch ($this->masterService->service) {
				case "vkontakte":
					$serviceUrl = "http://vk.com/id";
					break;
				case "odnoklassniki":
					$serviceUrl = "http://odnoklassniki.ru/profile/";
					break;
				case "facebook":
					$serviceUrl = "https://www.facebook.com/";
					break;
				default:
					return false;
			}
			return $serviceUrl . $this->masterService->user_id;
		}
		return false;
	}

	/**
	 * Получает адрес ссылки на работы мастера
	 *
	 * @return string
	 */
	public function getWorksUrl()
	{
		return Yii::app()->createUrl(
			"admin/work",
			array(
				"LfWork[master_id]" => $this->id
			)
		);
	}

	/**
	 * Получает название специализации
	 *
	 * @return string
	 */
	public function getGroupName()
	{
		if ($this->group) {
			return $this->group->name;
		}

		return '';
	}

	/**
	 * Разблокировает мастера в случае пополнения баланса > 0
	 *
	 * @return void
	 */
	public function checkAndUnblock()
	{
		if ($this->getBalance() > 0) {
			$this->is_blocked = 0;
			$this->saveAttributes(array('is_blocked'));
		}
	}

	/**
	 * Получает список значений разницы рейтинга
	 *
	 * @return string[]
	 */
	public function getRatingDiffValues()
	{
		$list = array();
		for ($i = -2; $i < -0.1; $i = $i + 0.1) {
			$list[(int)($i * 10)] = $i;
		}

		return $list;
	}

	/**
	 * Отображается или нет анкета мастера
	 * Используется для вывода кнопки "Записаться"
	 *
	 * @return bool
	 */
	public function canShow()
	{
		if (!$this->is_blocked && $this->is_published && !$this->is_removed) {
			return true;
		}

		return false;
	}

	/**
	 * Переворачивает аватар и получает ссылку на него
	 *
	 * @param string $direction направление вращения
	 *
	 * @return string
	 */
	public function rotateAvatar($direction)
	{
		$this->rotate($direction);
		return $this->avatar(true) . "?" . rand();
	}

	/**
	 * Получает идентификаторы групп мастера
	 *
	 * @return int[]
	 */
	public function getMasterGroupsIds()
	{
		$list = array();

		if ($this->masterGroups) {
			foreach ($this->masterGroups as $masterGroup) {
				$list[] = $masterGroup->group_id;
			}
		}

		return $list;
	}

	/**
	 * Пополнение или списание баланса мастера.
	 * Действует оператор в БО
	 *
	 * @param int $sum сумма для изменения баланса
	 *
	 * @return bool
	 */
	public function rechargeBalance($sum = 0)
	{
		if (!$sum) {
			return false;
		}

		return PaymentsAccount::model()->findByPk(PaymentsAccount::BONUS_ID)->creditAmount(
			$this->getAccount(),
			$sum,
			false,
			PaymentsOperations::TYPE_BO,
			"В БО для мастера {$this->id} был изменен баланс на {$sum} рублей"
		);
	}

	/**
	 * Получает ссылку в БО на мастера
	 *
	 * @param bool $isBlank открывать ли ссылку в новом окне
	 *
	 * @return string
	 */
	public function getBoLink($isBlank = true)
	{
		return $this->_makeMasterLink(Yii::app()->createUrl("admin/master/update", array("id" => $this->id)), $isBlank);
	}

	/**
	 * Создает ссылку на мастера
	 *
	 * @param string $url     URL страницы
	 * @param bool   $isBlank открывать ли ссылку в новом окне
	 *
	 * @return string
	 */
	private function _makeMasterLink($url, $isBlank = true)
	{
		$target = "";
		if ($isBlank) {
			$target = " target=\"_blank\"";
		}
		$text = $this->getFullName();

		return "<a href=\"{$url}\"{$target}>{$text}</a>";
	}

	/**
	 * Получает модель мастера по идентификатору аккаунта для платежей
	 *
	 * @param int $accountId идентификатор аккаунта для платежей
	 *
	 * @return LfMaster
	 *
	 * @throws CException
	 */
	public function findByAccountId($accountId = 0)
	{
		if (!$accountId) {
			throw new CException("Не указан идентификатор для платежей");
		}

		$criteria = new CDbCriteria;
		$criteria->with = array("account");
		$criteria->condition = "account.id = :account_id";
		$criteria->params[":account_id"] = $accountId;

		$model = $this->find($criteria);
		if (!$model) {
			throw new CException("Мастера с таким идентификатором платежей не существует");
		}

		return $model;
	}

	public function getBoLinkByAccountId($accountId, $isBlank = true)
	{
		try {
			$master = $this->findByAccountId($accountId);
			if ($master != null) {
				return $master->getBoLink($isBlank);
			}
		} catch (Exception $e) {
			return null;
		}
		return null;
	}
}   