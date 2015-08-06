<?php

namespace dfs\docdoc\models;

use dfs\docdoc\objects\Phone;


/**
 * This is the model class for table "doctor_opinion".
 *
 * The followings are the available columns in table 'doctor_opinion':
 * @property integer $id
 * @property integer $doctor_id
 * @property string $created
 * @property integer $allowed
 * @property integer $rating_qualification
 * @property integer $age
 * @property string $phone
 * @property integer $rating_attention
 * @property integer $rating_room
 * @property string $name
 * @property string $text
 * @property integer $request_id
 * @property integer $lk_status
 * @property integer $date_publication
 * @property integer $is_fake
 * @property string $author
 * @property integer $rating_color
 * @property string $origin
 * @property string $status
 * @property string $operatorComment
 *
 *
 * relations
 *
 * @property DoctorModel $doctor
 *
 * @method DoctorOpinionModel findByPk
 * @method DoctorOpinionModel[] findAll
 * @method DoctorOpinionModel cache
 */
class DoctorOpinionModel extends \CActiveRecord
{

	/**
	 * Статусы
	 */
	const STATUS_HIDDEN = 0;
	const STATUS_SHOWN = 1;
	const STATUS_BLOCKED = 2;

	/**
	 * Авторы
	 */
	const AUTHOR_GUEST = 'gues';
	const AUTHOR_CONTENT = 'cont';
	const AUTHOR_OPERATOR = 'oper';

	/**
	 * Сценарии
	 */
	// Отзыв, оставленный пациентом на сайте
	const SCENARIO_SITE = 'SCENARIO_SITE';

	const RATING_COLOR_POSITIVE = 1;
	const RATING_COLOR_NEUTRAL = 0;
	const RATING_COLOR_NEGATIVE = -1;

	/**
	 * Оценка по умолчанию
	 *
	 * @var int
	 */
	const DEFAULT_OPINION = 4;

	/**
	 * Названия оценок
	 *
	 * @var array
	 */
	public static $ratingWords = [
		1 => 'Плохо',
		2 => 'Ниже среднего',
		3 => 'Нормально',
		4 => 'Хорошо',
		5 => 'Отлично',
	];


	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 * @return DoctorOpinionModel the static model class
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
		return 'doctor_opinion';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array(
				'name, doctor_id',
				'required',
			),
			array(
				'phone',
				'filter',
				'filter' => array(Phone::class, 'strToNumber')
			),
			array(
				'doctor_id, allowed, rating_qualification, rating_attention, rating_room, age, request_id',
				'numerical',
				'integerOnly' => true,
			),
			array(
				'rating_qualification, rating_attention, rating_room',
				'in',
				'range' => range(1, 5),
			),
			array('name, text',
				'filter',
				'filter' => 'strip_tags'
			),
			array(
				'name, phone, doctor_id, text, rating_qualification, rating_attention, rating_room',
				'safe',
				'on' => array(self::SCENARIO_SITE),
			),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return [
			'doctor' => [ self::BELONGS_TO, DoctorModel::class, 'doctor_id' ],
		];
	}

	/**
	 * @return bool
	 */
	protected function beforeSave()
	{
		if ($this->getScenario() === self::SCENARIO_SITE) {
			$this->allowed = self::STATUS_HIDDEN;
			$this->author = self::AUTHOR_GUEST;
		}

		if ($this->doctor_id) {
			DoctorModel::model()->updateByPk($this->doctor_id, [ 'update_tips' => 1 ]);
		}

		return parent::beforeSave();
	}

	/**
	 * После сохранения
	 */
	protected function afterSave()
	{
		parent::afterSave();

		ClinicModel::updateDoctor($this->doctor_id);
	}

	/**
	 * Поиск опубликованных отзывов
	 *
	 * @return $this
	 */
	public function allowed()
	{
		$this->getDbCriteria()->mergeWith([
				'condition' => "t.allowed = 1 and t.status='enable'"
			]);
		return $this;
	}

	/**
	 * Поиск по доктору
	 *
	 * @param int $doctorId
	 *
	 * @return $this
	 */
	public function byDoctor($doctorId)
	{
		$this->getDbCriteria()
			->mergeWith(array(
				'condition' => 'doctor_id = :doctor',
				'params'    => array(':doctor' => $doctorId),
			));

		return $this;
	}

	/**
	 * Поиск по клинике и её филиалам
	 *
	 * @param int  $clinicId
	 * @param bool $withBranch
	 *
	 * @return $this
	 */
	public function byClinic($clinicId, $withBranch = false)
	{
		$this->getDbCriteria()->mergeWith([
			'together' => true,
			'with' => [
				'doctor' => [
					'select' => '',
					'joinType' => 'INNER JOIN',
					'with' => [
						'clinics' => [
							'joinType' => 'INNER JOIN',
							'condition' => 'clinics.id = :clinic_id' . ($withBranch ? ' OR clinics.parent_clinic_id = :clinic_id' : ''),
							'params' => [
								'clinic_id' => $clinicId,
							],
						],
					],
				],
			],
		]);

		return $this;
	}

	/**
	 * Поиск положительных, нейтральных или отрицательных отзывов
	 *
	 * @param array $types
	 *
	 * @return $this
	 */
	public function inRatingColor(array $types)
	{
		$this->getDbCriteria()->addInCondition('t.rating_color', $types);

		return $this;
	}

	/**
	 * Выборка отзывов, созданных в интервале времени
	 *
	 * @param string      $from
	 * @param string|null $to
	 *
	 * @return $this
	 */
	public function createdInInterval($from, $to = null)
	{
		$this->getDbCriteria()->mergeWith(
			array(
				'condition' => "created >= :from_time",
				'params'    => array(
					":from_time" => $from,
				)
			)
		);

		if (!is_null($to)) {
			$this->getDbCriteria()->mergeWith(
				array(
					'condition' => "created <= :to_time",
					'params'    => array(
						":to_time" => $to,
					)
				)
			);
		}

		return $this;
	}

	/**
	 * Средний рейтинг для врача
	 *
	 * @param $doctorId
	 *
	 * @return float
	 */
	public function getAverageRating($doctorId)
	{
		$ratings = \Yii::app()->db
			->createCommand("SELECT
				SUM(rating_qualification) as rating_qualification,
				SUM(rating_attention) as rating_attention,
				SUM(rating_room) as rating_room,
				COUNT(*) as num
				FROM doctor_opinion
				WHERE allowed = 1 and status='enable' AND doctor_id = :doctorId
			")
			->bindValue("doctorId", $doctorId)
			->queryRow();

		$rating = 0;

		if ($ratings['num'] > 0) {
			$rating =  ($ratings['rating_qualification'] + $ratings['rating_attention'] + $ratings['rating_room']) / 3 / $ratings['num'];
		}
		return $rating;
	}

	/**
	 * Общий рейтинг
	 *
	 * @return float
	 */
	public function getTotalRating()
	{
		return ($this->rating_attention + $this->rating_qualification + $this->rating_room) / 3;
	}

	/**
	 * Получение среднего рейтинга по отзывам врача
	 *
	 * @param int $doctorId
	 *
	 * @return array
	 */
	public function getAvgRatingsByReviewsForDoctor($doctorId)
	{
		$command = $this->dbConnection->createCommand(
			'SELECT
				ROUND(AVG(t.rating_attention), 1) AS RatAttention,
				ROUND(AVG(t.rating_qualification), 1) AS RatQualification,
				ROUND(AVG(t.rating_room), 1) AS RatRoom
			FROM doctor_opinion as t
			WHERE
				t.doctor_id = :doctorId AND
				t.allowed = 1 AND
				t.status = "enable" AND
				t.origin <> "editor"'
		);

		$data = $command->queryRow(true, [":doctorId" => $doctorId]);

		$data['RatAttention'] = $data['RatAttention'] ? floatval($data['RatAttention']) : self::DEFAULT_OPINION;
		$data['RatQualification'] = $data['RatQualification'] ? floatval($data['RatQualification']) : self::DEFAULT_OPINION;
		$data['RatRoom'] = $data['RatRoom'] ? floatval($data['RatRoom']) : self::DEFAULT_OPINION;
		$data['RatTotal'] = ($data['RatAttention'] + $data['RatQualification'] + $data['RatRoom']) / 3;

		return $data;
	}

	/**
	 * Получение среднего рейтинга по отзывам врачей клиники
	 *
	 * @param int $clinicId
	 *
	 * @return array
	 */
	public function getAvgRatingsByReviewsForClinic($clinicId)
	{
		$command = $this->dbConnection->createCommand(
			'SELECT
				ROUND(AVG(t.rating_attention), 1) AS RatAttention,
				ROUND(AVG(t.rating_qualification), 1) AS RatQualification,
				ROUND(AVG(t.rating_room), 1) AS RatRoom
			FROM doctor_opinion as t
				INNER JOIN doctor_4_clinic as dc ON (dc.doctor_id = t.doctor_id AND dc.type = 1)
			WHERE
				dc.clinic_id = :clinicId AND
				t.allowed = 1 AND
				t.status = "enable" AND
				t.origin <> "editor"'
		);

		$data = $command->queryRow(true, [':clinicId' => $clinicId]);

		$data['RatAttention'] = $data['RatAttention'] ? floatval($data['RatAttention']) : self::DEFAULT_OPINION;
		$data['RatQualification'] = $data['RatQualification'] ? floatval($data['RatQualification']) : self::DEFAULT_OPINION;
		$data['RatRoom'] = $data['RatRoom'] ? floatval($data['RatRoom']) : self::DEFAULT_OPINION;
		$data['RatTotal'] = ($data['RatAttention'] + $data['RatQualification'] + $data['RatRoom']) / 3;

		return $data;
	}

	/**
	 * Получение словесной оценки
	 *
	 * @param float $rating
	 *
	 * @return string
	 */
	public static function getRatingInWord($rating)
	{
		$r = round($rating);
		$r = $r > 0 ? $r : 1;

		return isset(self::$ratingWords[$r]) ? self::$ratingWords[$r] : null;
	}
}