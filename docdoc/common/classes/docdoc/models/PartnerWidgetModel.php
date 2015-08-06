<?php
namespace dfs\docdoc\models;

use dfs\docdoc\validators\JsonValidator;

/**
 * Модель для таблицы partner
 *
 * @property integer $id
 * @property integer $partner_id
 * @property string $widget
 * @property string json_config
 * @property string $is_used
 *
 * @property PartnerModel $partner
 *
 * @method PartnerWidgetModel find
 * @method PartnerWidgetModel[] findAll
 * @method PartnerWidgetModel findByPk
 * @method PartnerWidgetModel findByAttributes
 */
class PartnerWidgetModel extends \CActiveRecord
{
	private $_widgetList = [
		'ClinicList' => "Список клиник",
		'DoctorList' => "Список врачей",
		'Request' => "Заявка на перезвон",
		'Search' => "Поиск клиники / врача",
		'Button' => "Кнопка записи",
		'ClinicListMedportal' => "Список клиник для medportal.ru",
	];

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return PartnerWidgetModel the static model class
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
		return 'partner_widget';
	}

	/**
	 * Первичный ключ
	 * @return string
	 */
	public function primaryKey()
	{
		return 'id';
	}

	/**
	 * Отношения
	 *
	 * @return array
	 */
	public function relations()
	{
		return [
			'partner' => [self::BELONGS_TO, PartnerModel::class, 'partner_id'],
		];
	}


	/**
	 * Правила валидации для атрибутов модели
	 *
	 * @return string[]
	 */
	public function rules()
	{
		return [
			['partner_id, widget, json_config', 'required'],
			['json_config', JsonValidator::class],
			[
				'is_used',
				'safe',
				'on' => 'insert, update',
			],
		];
	}

	/**
	 * Получение конфига для вижета
	 *
	 * @return array
	 */
	public function getConfig()
	{
		return json_decode($this->json_config, 1);
	}

	/**
	 * Выборка только используемых конфигов
	 *
	 * @return $this
	 */
	public function used()
	{
		$this->getDbCriteria()->mergeWith(
			array(
				'condition' => "is_used = 1",
			)
		);
		return $this;
	}

	/**
	 * Выборка только используемых конфигов
	 *
	 * @param int $partnerId
	 * @return $this
	 */
	public function byPartner($partnerId)
	{
		$this->getDbCriteria()->mergeWith(
			[
				'condition' => $this->getTableAlias() . ".partner_id = :partner_id",
				'params' => [':partner_id' => $partnerId],
			]
		);
		return $this;
	}

	/**
	 * Выборка только используемых конфигов
	 *
	 * @param string $widgetName
	 * @return $this
	 */
	public function byWidget($widgetName)
	{
		$this->getDbCriteria()->mergeWith(
			[
				'condition' => $this->getTableAlias() . ".widget = :widget",
				'params' => [':widget' => $widgetName],
			]
		);
		return $this;
	}

	/**
	 * Список виджетов в системе
	 *
	 * @return array
	 */
	public function getWidgetList()
	{
		return $this->_widgetList;
	}

	/**
	 * Поиск
	 *
	 * @return \CActiveDataProvider
	 */
	public function search()
	{
		$criteria = new \CDbCriteria;
		$criteria->compare('t.partner_id', $this->partner_id);

		return new \CActiveDataProvider(
			$this,
			array(
				'criteria' => $criteria,
			)
		);
	}

}
