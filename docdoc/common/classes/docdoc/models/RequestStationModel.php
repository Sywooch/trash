<?php

namespace dfs\docdoc\models;

/**
 * Файл класса RequestStationModel.
 *
 * Модель для таблицы "request_station"
 *
 * @author  Mikhail Vasilyev <mvasilyev@docdoc.ru>
 * @link    https://docdoc.atlassian.net/browse/DD-428
 * @package dfs.docdoc.models
 *
 * @property integer $request_id идентификатор заявки
 * @property integer $station_id идентификатор станции метро
 *
 * @method RequestStationModel   find
 * @method RequestStationModel   findByPk
 * @method RequestStationModel[] findAll
 */
class RequestStationModel extends \CActiveRecord
{

	/**
	 * Возвращает имя связанной таблицы базы данных
	 *
	 * @return string
	 */
	public function tableName()
	{
		return 'request_station';
	}

	/**
	 * Возвращает правила проверки для атрибутов модели
	 *
	 * @return array
	 */
	public function rules()
	{
		return array(
			[
				'request_id, station_id',
				'required'
			],
			[
				'request_id, station_id',
				'numerical',
				'integerOnly' => true
			],
		);
	}

	/**
	 * Возвращает статическую модель указанного класса.
	 *
	 * @param string $className название класса
	 *
	 * @return RequestStationModel
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}
}
