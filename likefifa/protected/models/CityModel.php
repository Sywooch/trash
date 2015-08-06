<?php

namespace likefifa\models;

use likefifa\components\system\ActiveRecord;
use likefifa\models\RegionModel;
use UndergroundLine;
use UndergroundStation;
use CActiveRecord;
use CDbCriteria;
use Yii;
use CActiveDataProvider;
use CException;

/**
 * Файл класса CityModel
 *
 * Модель для работы с городами
 *
 * @author  Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @link    https://docdoc.megaplan.ru/task/1003365/card/
 * @package models
 *
 * @property int                  $id                  идентификатор
 * @property int                  $region_id           идентификатор региона
 * @property string               $rewrite_name        абривиатура URL
 * @property string               $name                название
 * @property string               $name_genitive       название в родительном падеже
 * @property string               $name_prepositional  название в предложном падеже
 * @property int                  $is_active           является ли город активным
 * @property int                  $has_underground     есть ли у города метро
 *
 * @property RegionModel          $region              модель региона
 * @property UndergroundLine[]    $undergroundLines    модели веток метро
 * @property UndergroundStation[] $undergroundStations модели станций метро
 * @property UndergroundStation[] $nearStations        ближайшие станции метро
 *
 * @method   CityModel            findByRewrite()
 * @method   CityModel            findByPk()
 * @method   CityModel[]          findAll()
 * @method   CityModel            findByAttributes()
 * @method   CityModel[]          findAllByAttributes()
 * @method   CityModel            find()
 * @method   CityModel            active()
 * @method   CityModel            orderByName()
 * @method   CityModel            withUndergroundStation()
 */
class CityModel extends ActiveRecord
{

	/**
	 * Идентификатор Москвы
	 *
	 * @var int
	 */
	const MOSCOW_ID = 1;

	/**
	 * Флаги активности
	 *
	 * @var string[]
	 */
	public static $activeFlags = array(
		0 => "Не активно",
		1 => "Активно",
	);

	/**
	 * Флаги наличия станций метро
	 *
	 * @var string[]
	 */
	public static $undergroundStationsFlags = array(
		0 => "Нет",
		1 => "Есть",
	);

	/**
	 * Получает название таблицы в БД для модели
	 *
	 * @return string
	 */
	public function tableName()
	{
		return 'cities';
	}

	/**
	 * Правила валидации для атрибутов модели
	 *
	 * @return string[]
	 */
	public function rules()
	{
		return array(
			array('region_id, rewrite_name, name, name_genitive, name_prepositional', 'required'),
			array('region_id, is_active, has_underground', 'numerical', 'integerOnly' => true),
			array('rewrite_name, name, name_genitive, name_prepositional', 'length', 'max' => 32),
			array(
				'rewrite_name',
				'likefifa\components\application\StringValidator',
				'type' => "rewriteName"
			),
			array(
				'name, name_genitive, name_prepositional',
				'likefifa\components\application\StringValidator',
				'type' => "russianWord"
			),
			array(
				'id, region_id, rewrite_name, name, name_genitive, name_prepositional, is_active',
				'safe',
				'on' => 'search'
			),
		);
	}

	/**
	 * Связи с другими моделями
	 *
	 * @return string[]
	 */
	public function relations()
	{
		return array(
			'region'              => array(
				self::BELONGS_TO,
				'likefifa\models\RegionModel',
				'region_id'
			),
			'undergroundLines'    => array(
				self::HAS_MANY,
				'UndergroundLine',
				'city_id'
			),
			'undergroundStations' => array(
				self::HAS_MANY,
				'UndergroundStation',
				array('id' => 'underground_line_id'),
				'through' => 'undergroundLines'
			),
			'nearStations' => array(
				self::MANY_MANY,
				'UndergroundStation',
				'city_near_stations(city_id, station_id)',
				'order' => 'priority ASC',
			),
		);
	}

	/**
	 * Названия меток для атрибутов
	 *
	 * @return string[]
	 */
	public function attributeLabels()
	{
		return array(
			'id'                  => 'ID',
			'region_id'           => 'Регион',
			'rewrite_name'        => 'Абривиатура URL',
			'name'                => 'Название',
			'name_genitive'       => 'Название в родительном падеже',
			'name_prepositional'  => 'Название в предложном падеже',
			'is_active'           => 'Является ли город активным',
			'has_underground'     => 'Есть ли у города метро',
		);
	}

	/**
	 * Поиск в списке городов в БО
	 *
	 * @return CActiveDataProvider
	 */
	public function search()
	{
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('region_id', $this->region_id);
		$criteria->compare('rewrite_name', $this->rewrite_name, true);
		$criteria->compare('name', $this->name, true);
		$criteria->compare('name_genitive', $this->name_genitive, true);
		$criteria->compare('name_prepositional', $this->name_prepositional, true);
		$criteria->compare('is_active', $this->is_active);
		$criteria->compare('has_underground', $this->has_underground);

		return new CActiveDataProvider(
			$this, array(
				'criteria'   => $criteria,
				'pagination' => array(
					'pageSize' => 50,
				),
			)
		);
	}

	/**
	 * Получает модель класса
	 *
	 * @param string $className название класса
	 *
	 * @return CityModel
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * Получает название региона
	 *
	 * @return string
	 */
	public function getRegionName()
	{
		$model = $this->region;
		if ($model) {
			return $model->name;
		}

		return null;
	}

	/**
	 * Получает название флага активности
	 *
	 * @return string
	 */
	public function getActiveFlag()
	{
		return self::$activeFlags[$this->is_active];
	}

	/**
	 * Выполняется перед удалением модели
	 *
	 * @throws CException
	 */
	protected function beforeDelete()
	{
		throw new CException("Невозможно удалить город");
	}

	/**
	 * Возвращает список поведений модели
	 *
	 * @return string[]
	 */
	public function behaviors()
	{
		return array(
			'CArRewriteBehavior' => array(
				'class' => 'application.extensions.CArRewriteBehavior',
			),
		);
	}

	/**
	 * Получает название флага наличия метро
	 *
	 * @return string
	 */
	public function getUndergroundFlag()
	{
		return self::$undergroundStationsFlags[$this->has_underground];
	}

	/**
	 * Получает модель по абривиатуры URL
	 *
	 * @param string $rewriteName абривиатура URL
	 *
	 * @return CityModel
	 */
	public function getModelByRewriteName($rewriteName = null)
	{
		if (!$rewriteName) {
			return null;
		}

		$criteria = new CDbCriteria;
		$criteria->condition = "t.rewrite_name = :rewrite_name OR t.id = :rewrite_name";
		$criteria->params["rewrite_name"] = $rewriteName;

		return $this->find($criteria);
	}

	/**
	 * Является ли город Москвой
	 *
	 * @return bool
	 */
	public function isMoscow()
	{
		return $this->id == self::MOSCOW_ID;
	}

	/**
	 * Возвращает декларацию названных областей
	 *
	 * @return string[]
	 */
	public function scopes()
	{
		$alias = $this->getTableAlias();

		return array(
			"active"                 => array(
				"condition" => "{$alias}.is_active = :is_active",
				"params"    => array("is_active" => 1),
			),
			"orderByName"           => array(
				"order" => "{$alias}.name",
			),
			"withUndergroundStation" => array(
				"condition" => "{$alias}.has_underground = :has_underground",
				"params"    => array("has_underground" => 1),
			),
		);
	}
}