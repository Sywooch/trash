<?php

namespace dfs\docdoc\models;

/**
 * This is the model class for table "slot".
 *
 * The followings are the available columns in table 'slot':
 *
 * @property integer           $id
 * @property integer           $doctor_4_clinic_id
 * @property string            $start_time
 * @property string            $finish_time
 * @property string            $external_id
 *
 * The followings are the available model relations:
 * @property BookingModel[]    $booking
 * @property DoctorClinicModel $doctorClinic
 *
 * @method SlotModel findByPk
 * @method SlotModel[] findAll
 * @method SlotModel find
 * @method int count
 */
class SlotModel extends \CActiveRecord
{

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return SlotModel the static model class
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
		return 'slot';
	}

	/**
	 * @return string имя первичного ключа
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
		return array(
			array(
				'doctor_4_clinic_id',
				'numerical',
				'integerOnly' => true,
			),
			array(
				'doctor_4_clinic_id, start_time, finish_time',
				'required',
				'on' => array(
					'insert'
				)
			),
			array(
				'external_id',
				'safe',
				'on' => 'insert'
			),
			//разрешаем изменять время при update
			array(
				'start_time, finish_time',
				'safe',
				'on' => 'update'
			),
			//запрещаем изменять внешний ID и привязку к доктору и клинике при update
			array(
				'external_id, doctor_4_clinic_id',
				'unsafe',
				'on' => 'update'
			),
		);
	}

	/**
	 *  relations
	 *
	 * @return array
	 */
	public function relations()
	{
		return [
			'booking'      => [
				self::HAS_MANY,
				BookingModel::class,
				'slot_id'
			],
			'doctorClinic' => [
				self::BELONGS_TO,
				DoctorClinicModel::class,
				'doctor_4_clinic_id'
			],
		];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'                 => 'ID',
			'doctor_4_clinic_id' => 'ID доктора в клинике',
			'start_time'         => 'Время начала приема',
			'finish_time'        => 'Время окончания приема',
			'external_id'        => 'ID слота в МИС',
		);
	}

	/**
	 * Выборка слотов определенного доктора в определенной клинике
	 *
	 * @param int $doctor_4_clinic_id
	 *
	 * @return SlotModel
	 */
	public function forDoctorInClinic($doctor_4_clinic_id)
	{
		$this->getDbCriteria()->mergeWith(
			array(
				'condition' => "doctor_4_clinic_id = :doctor_4_clinic_id",
				'params'    => array(
					":doctor_4_clinic_id" => $doctor_4_clinic_id,
				)
			)
		);

		return $this;
	}

	/**
	 * Группировка слотов по дате
	 *
	 * @return SlotModel
	 */
	public function groupByDate()
	{
		$criteria = new \CDbCriteria();
		$criteria->select = $this->getTableAlias() . ".*, DATE_FORMAT(start_time, '%Y-%m-%d') as start_time_date";
		$criteria->group = "start_time_date";

		$this->getDbCriteria()->mergeWith($criteria);

		return $this;
	}

	/**
	 * Выборка слотов в интервале от $start_time до конца или до $finish_time
	 *
	 * @param string      $start_time
	 * @param string|null $finish_time
	 *
	 * @return SlotModel
	 */
	public function inInterval($start_time, $finish_time = null)
	{
		$this->getDbCriteria()->mergeWith(
			array(
				'condition' => "start_time >= :start_time",
				'params'    => array(
					":start_time" => $start_time . "",
				)
			)
		);

		if (!is_null($finish_time)) {
			$this->getDbCriteria()->mergeWith(
				array(
					'condition' => "finish_time <= :finish_time",
					'params'    => array(
						":finish_time" => $finish_time,
					)
				)
			);
		}

		return $this;
	}

	/**
	 * Выборка рабочих слотов
	 *
	 * @param string|null $finish_time
	 *
	 * @return SlotModel
	 */
	public function activeSlots($finish_time = null)
	{
		//в реальных датах
		$this->inInterval(date('Y-m-d H:i:s'), $finish_time);

		$this->getDbCriteria()
			->mergeWith(
				[
					'condition' => 'external_id not in (select slot_id from booking where status in (' . implode(',', BookingModel::model()->getSuccessStatuses()) . '))'
				]
			);

		return $this;
	}

	/**
	 * Выбирает слоты и возвращает интервалы времени, в которые принимает врач.
	 *
	 * return array(
	 *      array(
	 *             'start' => '2014-01-01 10:00:00',
	 *             'end' => '2014-01-01 19:00:00'
	 *      )
	 * )
	 *
	 * @param SlotModel[] $slots
	 * @return array
	 */
	public static function groupIntervals($slots)
	{
		$intervals = array();
		$num = -1;
		foreach ($slots as $s) {

			if (isset($intervals[$num]) && $s->start_time === $intervals[$num]['end']) {
				$intervals[$num]['end'] = $s->finish_time;
			} else {
				$num++;
				$intervals[$num] = array('start' => $s->start_time, 'end' => $s->finish_time);
			}
		}

		return $intervals;
	}

	/**
	 * Можно ли букать на этот слот
	 *
	 * @param int|null $requestId если надо исключить заявку. например при апдейте букинга
	 *
	 * @return bool
	 */
	public function isAvailable($requestId = null)
	{
		$b = BookingModel::model()
			->inStatus(BookingModel::model()->getSuccessStatuses())
			->bySlot($this->external_id);

		!is_null($requestId) && $b->requestNotIn([$requestId]);
		$bookingExists = $b->exists();

		return !$bookingExists;
	}

	/**
	 * Поиск по внешнему идентификатору
	 *
	 * @param string $external_id
	 *
	 * @return SlotModel
	 */
	public function byExternalId($external_id)
	{
		$this->getDbCriteria()->mergeWith(
			array(
				'condition' => "external_id = :external_id",
				'params'    => array(':external_id' => $external_id),
			)
		);
		return $this;
	}

	/**
	 * Сортировка по дате
	 *
	 * @return SlotModel
	 */
	public function ordered()
	{
		$this->getDbCriteria()->mergeWith(
			array(
				'order' => $this->getTableAlias() . ".start_time",
			)
		);
		return $this;
	}
}
