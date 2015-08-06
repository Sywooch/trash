<?php

namespace likefifa\models;

use likefifa\models\CityModel;
use CActiveRecord;
use CDbCriteria;
use Yii;
use CActiveDataProvider;
use CException;

/**
 * Файл класса RegionModel
 *
 * Модель для работы с городами
 *
 * @author  Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @link    https://docdoc.megaplan.ru/task/1003365/card/
 * @package models
 *
 * @property int         $id                  идентификатор
 * @property string      $prefix              префикс
 * @property string      $name                название
 * @property string      $name_genitive       название в родительном падеже
 * @property string      $name_prepositional  название в предложном падеже
 * @property int         $is_active           является ли активным
 *
 * @property CityModel[] $cities              модели всех городов региона
 * @property CityModel[] $activeCities        модели активных городов региона
 *
 * @method   RegionModel   findByRewrite()
 * @method   RegionModel   findByPk()
 * @method   RegionModel[] findAll()
 * @method   RegionModel   findByAttributes()
 * @method   RegionModel[] findAllByAttributes()
 * @method   RegionModel   find()
 * @method   RegionModel   active()
 * @method   RegionModel   orderByname()
 */
class RegionModel extends CActiveRecord
{

	/**
	 * Идентификатор Москвы
	 *
	 * @var int
	 */
	const MOSCOW_ID = 1;

	/**
	 * Идентификатор Московской области
	 *
	 * @var int
	 */
	const MO_ID = 2;

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
	 * Получает название таблицы в БД для модели
	 *
	 * @return string
	 */
	public function tableName()
	{
		return 'regions';
	}

	/**
	 * Правила валидации для атрибутов модели
	 *
	 * @return string[]
	 */
	public function rules()
	{
		return array(
			array('name, name_genitive, name_prepositional', 'required'),
			array('is_active', 'numerical', 'integerOnly' => true),
			array('prefix', 'length', 'max' => 8),
			array('name, name_genitive, name_prepositional', 'length', 'max' => 32),
			array(
				'prefix',
				'likefifa\components\application\StringValidator',
				'type' => "prefix"
			),
			array(
				'name, name_genitive, name_prepositional',
				'likefifa\components\application\StringValidator',
				'type' => "russianWord"
			),
			array('id, prefix, name, name_genitive, name_prepositional, is_active', 'safe', 'on' => 'search'),
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
			'cities' => array(
				self::HAS_MANY,
				'likefifa\models\CityModel',
				'region_id'
			),
			'activeCities' => array(
				self::HAS_MANY,
				'likefifa\models\CityModel',
				'region_id',
				'condition' => 'activeCities.is_active = 1',
				'order' => 'activeCities.name',
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
			'prefix'              => 'Префикс',
			'name'                => 'Название',
			'name_genitive'       => 'Название в родительном падеже',
			'name_prepositional'  => 'Название в предложном падеже',
			'is_active'           => 'Является ли регион активным',
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
		$criteria->compare('prefix', $this->prefix, true);
		$criteria->compare('name', $this->name, true);
		$criteria->compare('name_genitive', $this->name_genitive, true);
		$criteria->compare('name_prepositional', $this->name_prepositional, true);
		$criteria->compare('is_active', $this->is_active);

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
	 * @return RegionModel
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
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
		throw new CException("Невозможно удалить регион");
	}

	/**
	 * Получает ссылку на главную страницу региона
	 *
	 * @return string
	 */
	public function getIndexUrl()
	{
		return str_replace("//", "//{$this->prefix}", Yii::app()->params['baseUrl']);
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
			"active"       => array(
				"condition" => "{$alias}.is_active = :is_active",
				"params"    => array("is_active" => 1),
			),
			"orderByName" => array(
				"order" => "{$alias}.name",
			),
		);
	}

	/**
	 * Проверяет, является ли регион Москвой
	 *
	 * @return bool
	 */
	public function isMoscow()
	{
		return $this->id == self::MOSCOW_ID;
	}
}