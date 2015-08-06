<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 14.07.14
 * Time: 10:29
 */

namespace dfs\docdoc\models;

/**
 * Class CallLogModel
 *
 * Модель для логов телефонии
 *
 * @property int    $id
 * @property string $ext_id
 * @property string $start_time
 * @property string $duration
 * @property string $ani
 * @property string $did
 * @property string $tariff_duration
 * @property float  $tariff
 * @property float  $cost
 * @property int    $application_type_id
 * @property string $sort
 *
 * @method CallLogModel find
 * @method CallLogModel[] findAll
 */
class CallLogModel extends \CActiveRecord
{
	/**
	 * Отклонение в секундах от даты записи при поиске в логе
	 */
	const DELTA_TOLERANCE = 120;

	/**
	 * @param string $className
	 *
	 * @return CallLogModel
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string
	 */
	public function tableName()
	{
		return 'call_log';
	}

	/**
	 * @return string
	 */
	public function primaryKey()
	{
		return 'id';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			[
				'ext_id, start_time, start_time, duration, ani, did, tariff_duration, tariff, cost, application_type_id, sort',
				'safe',
				'on' => [
					'insert'
				]
			],
			['ext_id', 'unique']

		];
	}

	/**
	 * @param string $phone
	 *
	 * @return CallLogModel
	 */
	public function equalsCallerPhone($phone)
	{
		$this->getDbCriteria()
			->mergeWith(
				[
					'condition' => 'ani = :ani',
					'params'    => [':ani' => $phone]
				]
			);
		return $this;
	}

	/**
	 * @param string $startDate
	 * @param string $endDate
	 *
	 * @return $this
	 */
	public function between($startDate, $endDate)
	{
		$this->getDbCriteria()
			->mergeWith(
				[
					'condition' => 'start_time between :start_date and :end_date',
					'params'    => [':start_date' => $startDate, ':end_date' => $endDate]
				]
			);
		return $this;
	}

	/**
	 * Ищет заменяемый телефон в логах
	 *
	 * @param string $fromPhone
	 * @param string $toPhone
	 * @param string $time
	 *
	 * @return null|string
	 */
	public static function getReplacedPhone($fromPhone, $toPhone, $time)
	{
		$replacedPhone = null;

		$endDate = $time;
		$startDate = date('Y-m-d H:i:s', strtotime($endDate) - self::DELTA_TOLERANCE);

		$find = self::model()
			->equalsCallerPhone($fromPhone)
			->between($startDate, $endDate)
			->findAll(['order' => 'start_time desc']);

		foreach ($find as $row) {

			if ($row->did !== $toPhone) {
				$replacedPhone = $row->did;
				break;
			}
		}

		return $replacedPhone;
	}

	/**
	 * @return mixed
	 */
	public function getMaxStartTime()
	{
		$cr = $this->getDbCriteria();
		$criteria = clone $cr;
		$criteria->select = 'max(start_time)';

		$command = $this->getCommandBuilder()
			->createFindCommand($this->getTableSchema(), $criteria, $this->getTableAlias());

		$startTime = $command->queryScalar();


		return $startTime;
	}
} 
