<?php

namespace dfs\docdoc\models;

use CActiveDataProvider, CDbCriteria, CException;
use dfs\docdoc\extensions\AdvancedCActiveRecord;
use dfs\docdoc\objects\Phone;

/**
 * This is the model class for table "city".
 *
 * The followings are the available columns in table 'city':
 *
 * @property int    $id_city
 * @property string $title
 * @property string $title_genitive
 * @property string $title_prepositional
 * @property string $title_dative
 * @property string $rewrite_name
 * @property string $long
 * @property string $lat
 * @property string $prefix
 * @property int    $has_diagnostic
 * @property int    $has_mobile
 * @property int    $is_active
 * @property int    $search_type
 * @property Phone  $site_phone
 * @property Phone  $site_office
 * @property Phone  $opinion_phone
 * @property string $site_YA
 * @property string $gtm
 * @property string $diagnostic_site_YA
 * @property string $diagnostic_gtm
 * @property int    $time_zone
 *
 * The followings are the available model relations:
 *
 * @method CityModel[] findAll
 * @method CityModel find
 * @method CityModel findByPk
 * @method CityModel cache
 */
class CityModel extends AdvancedCActiveRecord
{
	/**
	 * Идентификатор Москвы
	 */
	const MOSCOW_ID = 1;

	/**
	 * Флаги вывода города на экран
	 *
	 * @var string[]
	 */
	public $activeFlags = array(
		0 => "Нет",
		1 => "Да",
	);

	/**
	 * Типы поиска (для клиники, врачей)
	 *
	 * @var string[]
	 */
	public $searchTypes = array(
		self::SEARCH_TYPE_DISTRICT => "Поиск по району",
		self::SEARCH_TYPE_METRO_SELECT => "Поиск по метро",
		self::SEARCH_TYPE_METRO_MAP => "Поиск по карте метро",
	);

	/**
	 * поиск по карте метро на сайте
	 */
	const SEARCH_TYPE_METRO_MAP = 3;

	/**
	 * поиск по селекту метро на сайте
	 */
	const SEARCH_TYPE_METRO_SELECT = 2;

	/**
	 * поиск по селекту райнов на сайте
	 */
	const SEARCH_TYPE_DISTRICT = 1;

	/**
	 * Не активный город
	 */
	const STATUS_NOT_ACTIVE = 0;
	/**
	 * Активный город
	 */
	const STATUS_ACTIVE = 1;

	/**
	 * дефолтный rewrite_name, использовать например для астериска и номера 8800*
	 */
	const DEFAULT_REWRITE_NAME = 'default';

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return CityModel the static model class
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
		return 'city';
	}

	/**
	 * @return string имя первичного ключа
	 */
	public function primaryKey()
	{
		return 'id_city';
	}

	/**
	 * Возвращает правила проверки для атрибутов модели
	 *
	 * @return string[]
	 */
	public function rules()
	{
		return array(
			array('title, title_genitive, title_prepositional, title_dative, rewrite_name', 'required'),
			array('is_active, has_diagnostic, has_mobile, search_type, time_zone', 'numerical', 'integerOnly' => true),
			array('title, title_genitive, title_prepositional, title_dative, rewrite_name', 'length', 'max' => 50),
			array('long, lat, site_YA, gtm, diagnostic_site_YA, diagnostic_gtm', 'length', 'max' => 20),
			array('prefix', 'length', 'max' => 8),
			array(
				'title, title_genitive, title_prepositional, rewrite_name, long, lat, prefix, site_YA, gtm,
					diagnostic_site_YA, diagnostic_gtm',
				'filter',
				'filter' => 'strip_tags'
			),
			array(
				'id_city, title, rewrite_name, long, lat, prefix, is_active',
				'safe',
				'on' => 'search'
			),
			array(
				'title, title_genitive, title_prepositional, title_dative',
				'dfs\docdoc\validators\StringValidator',
				'type' => "russian_word"
			),
			array('rewrite_name', 'dfs\docdoc\validators\StringValidator', 'type' => "latinCharacters"),
			array('prefix', 'dfs\docdoc\validators\StringValidator', 'type' => "prefix"),
			array('long, lat', 'numerical', 'numberPattern' => '/^[+-]?((\d+(\.\d*)?)|(\.\d+))([Ee][+-]?\d+)?$/'),
			array(
				'site_phone, site_office, opinion_phone',
				'dfs\docdoc\validators\PhoneValidator',
				'allowEmpty' => false
			),
			array(
				'site_phone, opinion_phone',
				'exist',
				'attributeName' => 'number',
				'className' => PhoneModel::class,
				'allowEmpty' => false,
				'message' => 'Телефон не найден',
			),
			array(
				'site_phone, opinion_phone',
				'exist',
				'attributeName' => 'number',
				'className' => PhoneModel::class,
				'allowEmpty' => false,
				'criteria' => ['with' => 'provider', 'condition' => 'provider.enabled'],
				'message' => 'Телефон не активен',
				'skipOnError' => true,
			),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id_city'             => 'ID',
			'title'               => 'Город',
			'title_genitive'      => 'Название города в родительном падеже',
			'title_prepositional' => 'Название города в предложном падеже',
			'title_dative'        => 'Название города в дательном падеже',
			'rewrite_name'        => 'Имя',
			'long'                => 'Широта',
			'lat'                 => 'Долгота',
			'prefix'              => 'Префикс',
			'is_active'           => 'Выводить на экран',
			'search_type'         => 'Тип поиска',
			'has_diagnostic'      => 'Наличие диагностики',
			'has_mobile'          => 'Наличие мобильной версии сайта',
			'site_phone'          => 'Телефон колцентра',
			'site_office'         => 'Офисный телефон',
			'site_YA'             => 'Yandex metrika',
			'gtm'                 => 'GTM',
			'diagnostic_site_YA'  => 'Yandex metrika для диагностики',
			'diagnostic_gtm'      => 'GTM для диагностики',
			'opinion_phone'       => 'Телефон для сбора отзывов',
			'time_zone'           => 'Разница времени с Москвой, часов',
		);
	}

	/**
	 * Получает список моделей на основе условий поиска / фильтров.
	 *
	 * @return CActiveDataProvider
	 */
	public function search()
	{
		$criteria = new CDbCriteria;

		$criteria->compare('id_city', $this->id_city);
		$criteria->compare('title', $this->title, true);
		$criteria->compare('rewrite_name', $this->rewrite_name, true);
		$criteria->compare('long', $this->long, true);
		$criteria->compare('lat', $this->lat, true);
		$criteria->compare('prefix', $this->prefix, true);
		$criteria->compare('site_phone', $this->site_phone, true);
		$criteria->compare('site_office', $this->site_office, true);
		$criteria->compare('site_YA', $this->site_YA, true);
		$criteria->compare('gtm', $this->gtm, true);
		$criteria->compare('diagnostic_site_YA', $this->diagnostic_site_YA, true);
		$criteria->compare('diagnostic_gtm', $this->diagnostic_gtm, true);
		$criteria->compare('opinion_phone', $this->opinion_phone, true);

		if ($this->is_active === null) {
			$criteria->compare('is_active', $this->is_active);
		}

		return new CActiveDataProvider($this, array(
				'criteria' => $criteria,
			));
	}

	/**
	 * Вызывается перед удалением модели
	 *
	 * @throws CException
	 */
	protected function beforeDelete()
	{
		throw new CException("Невозможно удалить город");
	}

	/**
	 * Выполнение действий после выборки
	 *
	 * Вместо телефонов создаем объект Phone
	 */
	public function afterFind()
	{
		$this->site_phone = new Phone($this->site_phone);
		$this->site_office = new Phone($this->site_office);
		$this->opinion_phone = new Phone($this->opinion_phone);

		parent::afterFind();
	}

	/**
	 * После валидации
	 */
	public function afterValidate()
	{
		\Yii::app()->eventDispatcher->raiseEvent('onPhoneChangeAfterValidate', $this);

		parent::afterValidate();
	}

	/**
	 * После сохранения
	 */
	public function afterSave()
	{
		\Yii::app()->eventDispatcher->raiseEvent('onPhoneChangeAfterSave', $this);

		parent::afterSave();
	}

	/**
	 * Возвращает связи между объектами
	 *
	 * @return string[]
	 */
	public function relations()
	{
		return [];
	}

	/**
	 * Показывае, октивен ли город
	 *
	 * @return string
	 */
	public function getActiveFlag()
	{
		if (!empty($this->activeFlags[$this->is_active])) {
			return $this->activeFlags[$this->is_active];
		}

		return "";
	}

	/**
	 * Получает список городов
	 *
	 * @return string[]
	 */
	public function getCityList()
	{
		$list = array();

		foreach ($this->findAll() as $model) {
			$list[$model->id_city] = $model->title;
		}

		return $list;
	}


	/**
	 * Получает список городов с нулевым значением (Любой регион)
	 *
	 * @return string[]
	 */
	public function getCityListWithAny()
	{
		return array_merge(array(0 => "Любой город"), $this->getCityList());
	}

	/**
	 * Поиск по префиксу
	 *
	 * @param $prefix
	 * @return $this
	 */
	public function searchByPrefix($prefix)
	{
		$this->getDbCriteria()->mergeWith(array(
				'condition' => "prefix = :prefix",
				'params'    => array(':prefix' => $prefix),
			));

		return $this;
	}

	/**
	 * Поиск активных городов
	 *
	 * @return $this
	 */
	public function active()
	{
		$this->getDbCriteria()->mergeWith(array(
			'condition' => "is_active = :status",
			'params'    => array(':status' => self::STATUS_ACTIVE),
		));

		return $this;
	}

	/**
	 * Выборка по rewrite_name
	 *
	 * @param string $rewriteName
	 *
	 * @return $this
	 */
	public function byRewriteName($rewriteName)
	{
		$this->getDbCriteria()->mergeWith(array(
			'condition' => "rewrite_name = :rewrite_name",
			'params'    => array(':rewrite_name' => $rewriteName),
		));

		return $this;
	}

	/**
	 * Поиск городов, в которых есть диагностика
	 *
	 * @return $this
	 */
	public function hasDiagnostic()
	{
		$this->getDbCriteria()->mergeWith(array(
			'condition' => "has_diagnostic = 1",
		));

		return $this;
	}

	/**
	 * Ищет город по rewrite_name и если не находит ищет дефолтный
	 *
	 * @param $rewrite_name
	 *
	 * @return $this
	 * @throws CException
	 */
	public function findCity($rewrite_name)
	{
		$city = $this->findByAttributes(['rewrite_name' => $rewrite_name]);

		if(!$city){
			$city = $this->defaultCity()->find();
		}

		if(!$city){
			throw new CException('Город не найден');
		}

		return $city;
	}

	/**
	 * поиск дефолтного города
	 * @return $this
	 */
	public function defaultCity()
	{
		$this->getDbCriteria()
			->mergeWith(
				[
					'condition' => 'rewrite_name=:rewrite_name and is_active=:is_active',
					'params' => [
						':rewrite_name' => self::DEFAULT_REWRITE_NAME,
						':is_active' => 0,
					]
				]
			);

		return $this;
	}

	/**
	 * Поиск по имени
	 *
	 * @param string $name
	 * @return $this
	 */
	public function byName($name)
	{
		$this->getDbCriteria()
			->mergeWith(
				[
					'condition' => 'title = :name',
					'params' => [':name' => $name],
				]
			);

		return $this;
	}

	/**
	 * Сортировка по имени
	 *
	 * @return $this
	 */
	public function ordered()
	{
		$this->getDbCriteria()
			->mergeWith(
				[
					'order' => $this->getTableAlias() . '.title ASC',
				]
			);

		return $this;
	}

	/**
	 * Убирает из выборки указанные ID
	 *
	 * @param int[] $ids
	 *
	 * @return CityModel
	 */
	public function notInIds($ids)
	{
		$criteria = new CDbCriteria();
		$criteria->addNotInCondition("t.id_city", $ids);

		$this->getDbCriteria()->mergeWith($criteria);

		return $this;
	}

	/**
	 * Поиск по телефону для сбора отзывов
	 *
	 * @param string $phone
	 *
	 * @return $this
	 */
	public function byOpinionPhone($phone)
	{
		$this->getDbCriteria()
			->mergeWith(
				[
					'condition' => 'opinion_phone = :op_ph',
					'params' => [':op_ph' => $phone]
				]
			);

		return $this;
	}

	/**
	 * Поиск по телефону на сайте
	 *
	 * @param string $phone
	 *
	 * @return $this
	 */
	public function bySitePhone($phone)
	{
		$this->getDbCriteria()
			->mergeWith(
				[
					'condition' => 'site_phone = :site_ph',
					'params' => [':site_ph' => $phone]
				]
			);

		return $this;
	}

	/**
	 * Проверяет, является ли город Москвой
	 *
	 * @return bool
	 */
	public function isMoscow()
	{
		return $this->id_city == self::MOSCOW_ID;
	}
}
