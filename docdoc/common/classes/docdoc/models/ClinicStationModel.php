<?php
namespace dfs\docdoc\models;

/**
 * модель для таблицы underground_station_4_clinic
 *
 * @property integer $undegraund_station_id
 * @property integer $clinic_id
 * @property integer $distance
 *
 * @property ClinicModel $clinic
 * @property StationModel $station
 *
 * @method ClinicStationModel find
 *
 */
class ClinicStationModel extends \CActiveRecord
{

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return ClinicStationModel the static model class
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
		return 'underground_station_4_clinic';
	}

	/**
	 * Отношения
	 *
	 * @return array
	 */
	public function relations()
	{
		return array(
			'station' => [
				self::BELONGS_TO,
				\dfs\docdoc\models\StationModel::class,
				'undegraund_station_id',
			],
			'clinic' => [
				self::BELONGS_TO,
				\dfs\docdoc\models\ClinicModel::class,
				'clinic_id',
			],
		);

	}

	/**
	 * Поиск по клинике
	 *
	 * @param $clinicId
	 * @return $this
	 */
	public function byClinic($clinicId)
	{
		$this
			->getDbCriteria()
			->mergeWith([
				'condition' => 'clinic_id = :clinic',
				'params'    => [':clinic' => $clinicId],
			]);

		return $this;
	}

	/**
	 * Поиск по станции
	 *
	 * @param $stationId
	 * @return $this
	 */
	public function byStation($stationId)
	{
		$this
			->getDbCriteria()
			->mergeWith([
				'condition' => 'undegraund_station_id = :station',
				'params'    => [':station' => $stationId],
			]);

		return $this;
	}

	/**
	 * @return bool|void
	 */
	public function beforeSave()
	{
		parent::beforeSave();

		if (!is_null($this->station) && !is_null($this->clinic)) {
			$this->distance = $this->station->calcDistanceToClinic($this->clinic);
		}

		return true;
	}
}