<?php
use likefifa\components\system\ActiveRecord;
use likefifa\models\CityModel;
use likefifa\extensions\image\Image;

/**
 * This is the model class for table "lf_salons".
 *
 * The followings are the available columns in table 'lf_salons':
 *
 * @property integer                                        $id
 * @property string                                         $name
 * @property string                                         $email
 * @property string                                         $password
 * @property string                                         $phone
 * @property integer                                        $hrs_wd_from
 * @property integer                                        $hrs_wd_to
 * @property integer                                        $hrs_we_from
 * @property integer                                        $hrs_we_to
 * @property double                                         $rating
 * @property int                                            $rating_inner
 * @property float                                          $rating_composite
 * @property string                                         $add_street
 * @property string                                         $add_house
 * @property string                                         $add_korp
 * @property string                                         $add_info
 * @property integer                                        $district_id
 * @property integer                                        $underground_station_id
 * @property string                                         $description
 * @property string                                         $logo
 * @property double                                         $map_lat
 * @property double                                         $map_lng
 * @property string                                         $rewrite_name
 * @property integer                                        $city_id
 * @property integer                                        $is_published
 * @property string                                         $created
 * @property string                                         $phone_numeric
 *
 * The followings are the available model relations:
 * @property LfMaster[]                                     $masters
 * @property LfOpinion[]                                    $opinions
 * @property LfPrice[]                                      $prices
 * @property LfPrice[]                                      $filledPrices
 * @property LfSalonDistrict[]                              $salonDistricts
 * @property LfSalonPhoto[]                                 $photo
 * @property LfSpecialization[]                             $specializations
 * @property DistrictMoscow                                 $district
 * @property CityModel                                      $city
 * @property UndergroundStation                             $undergroundStation
 * @property DistrictMoscow[]                               $departureDistricts
 * @property LfWork[]                                       $works
 * @property LfSalonSpecialization[]                        $spec
 * @property LfService[]                                    $services
 *
 * @method LfSalon findByRewrite
 * @method LfSalon findByPk
 * @method LfSalon find
 * @method LfSalon[] findAll
 * @method LfSalon findByAttributes
 * @method LfSalon[] findAllByAttributes
 * @method LfSalon ordered
 * @method string getRewriteName
 * @method LfPrice[] filledPrices
 */
class LfSalon extends ActiveRecord
{

	/**
	 * Цифра в шапке сайта.
	 * Исходное число
	 *
	 * @var int
	 */
	const START_COUNT = 195;

	/**
	 * Минимальный рейтинг мастера
	 *
	 * @var int
	 */
	const MIN_RATING = 2;

	/**
	 * Прибавление в день
	 * Для шапки
	 *
	 * @var int
	 */
	const PER_DAY = 0.66;

	public $specIds;
	public $departureDistrictIds;
	public $repeat_password;

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className active record class name.
	 *
	 * @return LfSalon the static model class
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
		return 'lf_salons';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('name, password, email', 'required', 'message' => ''),
			array('phone', 'required', 'on' => 'salonLkIndex, register'),
			array('underground_station_id', 'validateMetro', 'on' => 'salonLkAddress'),
			array('add_street, add_house', 'required', 'on' => 'salonLkAddress'),
			array('name, add_street, add_house, add_korp, add_info', 'length', 'max' => 256),
			array('phone, password', 'length', 'max' => 32),
			array('description', 'length', 'max' => 512),
			array('name, phone, description', 'filter', 'filter' => 'strip_tags'),
			array(
				'underground_station_id, district_id, city_id, hrs_wd_from, hrs_wd_to, hrs_we_from, hrs_we_to, is_published, phone_numeric',
				'numerical',
				'integerOnly' => true
			),
			array('map_lat, map_lng, rating, rating_inner', 'numerical'),
			array('rating_composite', 'type', 'type' => 'float'),
			array('email', 'validateEmail'),
			array('email', 'email', 'message' => '', 'fullPattern' => true, 'allowName' => false),
			array('password, repeat_password', 'length', 'min' => 6, 'on' => 'changePassword'),
			array('repeat_password', 'compare', 'compareAttribute' => 'password', 'on' => 'changePassword'),
			array('logo', 'file', 'types' => 'jpg, jpeg, gif, png', 'allowEmpty' => true),
			array('name, email, password', 'safe', 'on' => 'register'),
			array('id, name, map_lat, map_lng, phone_numeric, is_published', 'safe', 'on' => 'search'),
			array('rating', 'default', 'value' => self::MIN_RATING),
			array('rating_inner', 'unique'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'masters'            => array(
				self::HAS_MANY,
				'LfMaster',
				array('salon_id' => 'id'),
				'order' => 'masters.rating DESC'
			),
			'works'              => array(self::HAS_MANY, 'LfWork', array('id' => 'master_id'), 'through' => 'masters'),
			'spec'               => array(self::HAS_MANY, 'LfSalonSpecialization', 'salon_id'),
			'city'               => array(
				self::BELONGS_TO,
				'likefifa\models\CityModel',
				'city_id',
				'together' => true
			),
			'salonDistricts'     => array(self::HAS_MANY, 'LfSalonDistrict', 'salon_id'),
			'departureDistricts' => array(
				self::HAS_MANY,
				'DistrictMoscow',
				array('district_id' => 'id'),
				'through' => 'salonDistricts'
			),
			'prices'             => array(self::HAS_MANY, 'LfPrice', 'salon_id', 'with' => 'service'),
			'filledPrices'       => array(
				self::HAS_MANY,
				'LfPrice',
				'salon_id',
				'with'      => 'service',
				'condition' => 'filledPrices.price IS NOT NULL AND filledPrices.price > 0'
			),
			'services'           => array(
				self::HAS_MANY,
				'LfService',
				array('service_id' => 'id'),
				'through'  => 'filledPrices',
				'together' => true
			),
			'specializations'    => array(
				self::HAS_MANY,
				'lfSpecialization',
				array('specialization_id' => 'id'),
				'through' => 'services'
			),
			'opinions'           => array(
				self::HAS_MANY,
				'LfOpinion',
				'salon_id',
				'condition' => 'opinions.allowed = 1'
			),
			'photo'              => array(
				self::HAS_MANY,
				'LfSalonPhoto',
				'salon_id',
				'together'  => true,
				'condition' => 'photo.image IS NOT NULL'
			),
			'undergroundStation' => array(
				self::BELONGS_TO,
				'UndergroundStation',
				'underground_station_id',
				'together' => true
			),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'                     => 'ID',
			'name'                   => 'Салон',
			'email'                  => 'Электронная почта',
			'is_published'           => 'Опубликован',
			'created'                => 'Дата регистрации',
			'logo'                   => 'Логотип',
			'phone'                  => 'Телефон салона',
			'phone_numeric'          => 'Телефон салона',
			'rating'                 => 'Рейтинг',
			'rating_inner'           => 'Внутренний рейтинг',
			'hrs_wd_from'            => 'с',
			'hrs_wd_to'              => 'до',
			'hrs_we_from'            => 'с',
			'hrs_we_to'              => 'до',
			'delete_logo'            => 'Удалить логотип',
			'add_street'             => 'Улица',
			'add_house'              => 'Дом',
			'add_korp'               => 'Корпус',
			'add_info'               => 'Дополнительная информация',
			'underground_station_id' => 'Станция метро',
			'district_id'            => 'Район',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('name', $this->name, true);

		$criteria->order = 't.id DESC';

		return new CActiveDataProvider(
			"LfSalon", array(
				'criteria'   => $criteria,
				"pagination" => array(
					"pageSize" => 50,
				),
			)
		);
	}

	public function scopes()
	{
		return array(
			'ordered' => array(
				'order' => 'name ASC',
			)
		);
	}

	public function behaviors()
	{
		return array(
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
			[
				'class' => 'likefifa\extensions\image\ImageBehavior',
				'imageAttribute' => 'logo'
			],
		);
	}

	public function beforeSave()
	{
		if ($this->phone) {
			$this->phone_numeric = preg_replace('/\D+/', '', $this->phone);

		}

		$this->updateRating();
		return parent::beforeSave();
	}

	/**
	 * Валидатор метро
	 */
	public function validateMetro()
	{
		if ($this->city_id === 1 && !$this->underground_station_id) {
			$this->addError('underground_station_id', 'UnderGround station can not be blank');
		}
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
	 * Возвращает ссылку на логотип
	 *
	 * @var boolean $force
	 *
	 * @return string
	 */
	public function avatar($force = false) {
		return $this->preview(147, 200, true, Image::HEIGHT, $force);
	}

	public function getFullName()
	{
		return $this->name;
	}

	public function getLkUrl()
	{
		return Yii::app()->createUrl('salonlk/index');
	}

	public function generateRewriteName()
	{
		$index = 0;
		$rewriteNameBase = strtolower(su::fileName($this->name));
		if (!$rewriteNameBase || is_numeric($rewriteNameBase)) {
			return false;
		}

		$rewriteName = $rewriteNameBase;
		while ($this->findByRewrite($rewriteName)) {
			$rewriteName = $rewriteNameBase . (++$index);
		}

		return $rewriteName;
	}

	/**
	 * Получает список салонов
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
			$items[$emptyKey] = 'Нет салона';
		}

		$salons = Yii::app()->db->createCommand()
			->select('id, name')
			->from('lf_salons')
			->order("name");
		if ($active) {
			$salons->andWhere('is_published = 1');
		}
		$salons = $salons->queryAll();
		foreach ($salons as $salon) {
			$items[$salon["id"]] = $salon["name"];
		}

		return $items;
	}

	public function getSpecListItems()
	{
		$specs = array();
		foreach ($this->specializations as $spec) {
			$specs[$spec->id] = trim($spec->name);
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

	public function getRating()
	{
		return floatval($this->rating);
	}

	public function getRatingPercent()
	{
		return $this->getRating() / 5 * 100;
	}

	public function getSpecsConcatenated()
	{
		$specs = array();
		foreach ($this->specializations as $spec) {
			$specs[] = trim($spec->name);
		}

		return implode(', ', $specs);
	}

	public function getSpecsSplitted()
	{
		$specs = array();
		foreach ($this->specializations as $spec) {
			$specs[] = trim($spec->name);
		}
		if (count($specs) < 6) {
			return array(implode(', ', $specs), '');
		} else {
			return array(
				implode(', ', array_slice($specs, 0, 6)),
				implode(', ', array_slice($specs, 6)),
			);
		}
	}

	public function getFullAddress()
	{
		if (!$this->add_street) {
			return false;
		}

		$add = array();

		if ($this->undergroundStation) {
			$add[] = $this->undergroundStation->name;
		}

		if ($this->city && $this->city_id != 1) {
			$add[] = 'г. ' . $this->city->name;
		}

		$add[] = $this->add_street;

		if ($this->add_house) {
			$add[] = 'д. ' . $this->add_house . ($this->add_korp ? 'к' . $this->add_korp : '');

		}

		return implode(', ', $add);
	}

	public function getShortAddress()
	{
		if (!$this->add_street) {
			return false;
		}

		$add = array();

		if ($this->city && $this->city_id != 1) {
			$add[] = 'г. ' . $this->city->name;
		}

		$add[] = $this->add_street;

		if ($this->add_house) {
			$add[] = 'д. ' . $this->add_house . ($this->add_korp ? 'к' . $this->add_korp : '');

		}

		return implode(', ', $add);
	}

	/**
	 * Возвращает работы мастеров салона
	 *
	 * @param LfSpecialization $specialization
	 * @param LfService        $service
	 * @param bool             $all
	 *
	 * @return LfWork[]
	 */
	public function getFilteredWorks(LfSpecialization $specialization = null, LfService $service = null, $all = false)
	{
		$masters_id = $this->getRelationIds('masters');
		$criteria = new CDbCriteria;
		$criteria->addInCondition('t.master_id', $masters_id);;
		if ($all == false) {
			$criteria->limit = 3;
		}
		if ($specialization) {
			$criteria->compare('t.specialization_id', $specialization->id);
		}
		if ($service) {
			$criteria->compare('t.service_id', $service->id);
		}

		return LfWork::model()->findAll($criteria);
	}

	public function getFilteredSpecsConcatenated(LfSpecialization $specialization = null)
	{
		$specs = array();
		foreach ($this->specializations as $spec) {
			if (
			(!$specialization || $spec->id === $specialization->id)
			) {
				$specs[] = trim($spec->name);
			}
		}

		return implode(',', $specs);
	}

	public function getProfileUrl($absolute = false)
	{
		return $absolute ? Yii::app()->createAbsoluteUrl(
			'salons/index',
			array('rewriteName' => $this->getRewriteName())
		) : Yii::app()->createUrl('salons/index', array('rewriteName' => $this->getRewriteName()));
	}

	public function getAbsoluteModelUrl()
	{
		return Yii::app()->createAbsoluteUrl('salons/index', array('rewriteName' => $this->getRewriteName()));
	}

	public function getModelUrl()
	{
		return Yii::app()->createUrl('salons/index', array('rewriteName' => $this->getRewriteName()));
	}

	public function findByEmail($email)
	{
		return $this->find('email = :email', compact('email'));
	}

	public function validateEmail($attribute)
	{
		if (
			(
				($salon = $this->findByEmail($this->$attribute))
				&& ($this->isNewRecord || $this->id !== $salon->id)
			)
			|| LfMaster::model()->findByEmail($this->$attribute)
		) {
			$this->addError(
				'email',
				'Такой e-mail уже зарегистрирован. Введите другой адрес или воспользуйтесь формой восстановления пароля.'
			);
		}
	}

	/**
	 * Проверяет пароль для салона
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
					$price->salon_id = $this->id;
					$price->service_id = $serviceId;
				}

				$price->price = !empty($prices['values'][$serviceId]) ? intval($prices['values'][$serviceId]) : null;
				$price->price_from = intval(!empty($prices['isFrom'][$serviceId]));
				$price->save();

			}
		}

	}

	public function getPriceForService($serviceId)
	{
		foreach ($this->prices as $price) {
			if (intval($serviceId) === intval($price->service_id)) {
				return $price;
			}
		}
		return 0;
	}

	public function getSplittedDescription()
	{
		$description = str_replace("\r\n", "\n", $this->description);
		$description = explode("\n", $description);
		if (count($description) < 2) {
			return array(implode('<br/>', $description), '');
		} else {
			return array(
				implode('<br/>', array_slice($description, 0, 1)),
				implode('<br/>', array_slice($description, 1)),
			);
		}
	}

	public function getPriceSalonFormatted($service_id, $salon_id)
	{
		$model =
			LfPrice::model()->findBySql(
				" SELECT price FROM lf_price WHERE service_id = '$service_id' AND salon_id = '$salon_id' "
			);
		if ($model["price"]) {
			return number_format($model["price"], 0, '.', ' ');
		}
		return 0;
	}

	/**
	 * Получает дату регистрации в формате "d.m.Y H:i"
	 *
	 * @return string
	 */
	public function getCreated()
	{
		$timestamp = CDateTimeParser::parse($this->created, "yyyy-MM-dd HH:mm:ss");
		if ($timestamp) {
			return date("d.m.y", $timestamp);
		}

		return "не определено";
	}

	/**
	 * Получает адрес ссылки на фото салона
	 *
	 * @return string
	 */
	public function getSalonPhotoUrl()
	{
		return Yii::app()->createUrl(
			"admin/salonPhoto",
			array(
				"SalonPhoto[salon_id]" => $this->id
			)
		);
	}

	/**
	 * Получает всех мастеров по данному салону
	 *
	 * @return CActiveDataProvider
	 */
	public function getDataProviderMasters()
	{
		$criteria = new CDbCriteria;
		$criteria->condition = "salon_id = :salon_id";
		$criteria->params = array(":salon_id" => $this->id);

		return new CActiveDataProvider("LfMaster", compact("criteria"));
	}

	/**
	 * Пересчитывает рейтинг салона
	 *
	 * @return void
	 */
	public function updateRating()
	{

		$rating = self::MIN_RATING;

		if (!$this->isNewRecord) {
			if ($this->name) {
				$rating += 0.2;
			}

			if ($this->phone) {
				$rating += 0.2;
			}

			if ($this->underground_station_id) {
				$rating += 0.2;
			}

			if ($this->add_street) {
				$rating += 0.2;
			}

			if ($this->prices) {
				$rating += 0.2;
			}

			if ($this->email) {
				$rating += 0.2;
			}

			if ($this->photo) {
				$rating += 0.3;
			}

			if ($this->description) {
				$rating += 0.3;
			}

			if (($this->hrs_wd_from && $this->hrs_wd_to) || ($this->hrs_we_from && $this->hrs_we_to)) {
				$rating += 0.2;
			}

			if (count($this->works) >= 2 && count($this->works) <= 5) {
				$rating += 0.2;
			} else {
				if (count($this->works) > 5) {
					$rating += 0.5;
				}
			}

			if (count($this->masters) >= 1 && count($this->masters) <= 3) {
				$rating += 0.4;
			}

			if (count($this->masters) > 5) {
				$rating += 0.5;
			}

			$this->rating = $rating;
			$this->rating_composite =
				!empty($this->rating_inner) ? 1 / $this->rating_inner + $this->rating / 999999 : $this->rating / 999999;

			$this->saveAttributes(['rating']);
		}
	}
}