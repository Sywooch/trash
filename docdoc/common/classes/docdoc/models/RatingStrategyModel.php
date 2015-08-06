<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 12.09.14
 * Time: 13:20
 */

namespace dfs\docdoc\models;

use CActiveDataProvider;
use CDbCriteria;
use dfs\docdoc\objects\Formula;
use SebastianBergmann\Exporter\Exception;

/**
 * Class RatingStrategyModel
 * @package dfs\docdoc\models
 *
 * @property int id
 * @property string name
 * @property int chance
 * @property string type
 * @property string for_object
 * @property string params
 * @property int needs_to_recalc
 *
 * @method RatingStrategyModel find
 * @method RatingStrategyModel[] findAll
 * @method RatingStrategyModel findByPk
 */
class RatingStrategyModel extends \CActiveRecord
{
	/**
	 * Для любого объекта
	 */
	const FOR_ANY = 0;

	/**
	 * Для Врача
	 */
	const FOR_DOCTOR = 1;

	/**
	 * Для клиники
	 */
	const FOR_CLINIC = 2;

	/**
	 * @return string
	 */
	public function tableName()
	{
		return 'rating_strategy';
	}

	/**
	 * @param string $className
	 * @return static
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * Валидация
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			['id, for_object, needs_to_recalc', 'numerical', 'integerOnly' => true],
			['chance', 'numerical'],
			[
				'id, name',
				'safe',
				'on' => 'search'
			],
			['name, chance, params, type, for_object, needs_to_recalc', 'safe', 'on' => 'insert, update']
		];
	}

	public function attributeLabels()
	{
		return [
			'name'              => 'Название',
			'type'              => 'Тип',
			'for_object'        => 'Для объекта',
			'chance'            => 'Вероятность',
			'needs_to_recalc'   => 'Пересчитать',
			'params'            => 'Формула',
		];
	}

	/**
	 * Список возможных методик рассчета
	 *
	 * @return array
	 */
	public function getStrategies()
	{
		return [
			'default' => 'default',
			'multiply' => 'multiply',
			'formula' => 'formula',
		];
	}

	/**
	 * Список возможных методик рассчета
	 *
	 * @return array
	 */
	public function getForObjects()
	{
		return [
			self::FOR_ANY => 'Для врачей и клиник',
			self::FOR_DOCTOR => 'Для врачей',
			self::FOR_CLINIC => 'Для клиник',
		];
	}

	/**
	 * Список возможных методик рассчета
	 *
	 * @return array
	 */
	public function getForObjectTitle()
	{
		$variants = $this->getForObjects();
		return $variants[$this->for_object];
	}

	/**
	 * Получает список моделей на основе условий поиска / фильтров.
	 *
	 * @return CActiveDataProvider
	 */
	public function search()
	{
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('name', $this->name, true);

		return new CActiveDataProvider(
			$this, array(
				'criteria' => $criteria,
			)
		);
	}

	/**
	 * Перед сохранением
	 *
	 * @return bool
	 */
	public function beforeSave()
	{
		$this->params = json_encode($this->params);

		return parent::beforeSave();
	}

	/**
	 * После селекта
	 */
	public function afterFind()
	{
		$this->params = json_decode($this->params, true);

		parent::afterFind();
	}

	/**
	 * Только активные
	 *
	 * @return $this
	 */
	public function active()
	{
		$this->getDbCriteria()
			->mergeWith(
				[
					'condition' => 'chance > 0'
				]
			);

		return $this;
	}

	/**
	 * Ищет рандомную с учетом вероятности
	 *
	 * @return $this
	 */
	public function random()
	{
		$rand = rand(0, 100);

		$this->getDbCriteria()
			->mergeWith(
				[
					'condition' => 'chance > :rand',
					'params' => [':rand' => $rand],
					'order' => 'chance',
				]
			);

		return $this;
	}

	/**
	 * Ищет рандомную с учетом вероятности
	 *
	 * @param int $type
	 *
	 * @return $this
	 */
	public function byObjectType($type)
	{
		$this->getDbCriteria()->addInCondition('for_object', [self::FOR_ANY, $type]);

		return $this;
	}

	/**
	 * Обновить рейтинг у всех инстансов стратегии
	 *
	 * @param int $type
	 *
	 * @throws \CException
	 */
	public function updateRating($type)
	{
		switch ($type){
			case RatingModel::TYPE_DOCTOR:
				$this->updateDoctorRating();
				break;
			case RatingModel::TYPE_CLINIC:
				$this->updateClinicRating();
				break;
			default:
				throw new \CException('Неизвестный тип');
		}
	}

	/**
	 * Перерасчет всех рейтингов для врачей
	 *
	 * @throws \CException
	 */
	protected function updateDoctorRating()
	{
		if ($this->for_object == self::FOR_CLINIC) {
			return;
		}

		$dataProvider = new \CActiveDataProvider(
			DoctorModel::class,
			[
				'criteria' => [
					'with' => [
						'clinics' => array(
							'select' => false,
							'joinType' => 'INNER JOIN',
						)
					],
					'together' => true,
					'group' => DoctorModel::model()->tableAlias . '.id',
				],
			]
		);

		$iterator = new \CDataProviderIterator($dataProvider, 100);

		foreach ($iterator as $n => $i) {
			/** @var DoctorModel $i */
			$this->saveDoctorRatings($i);
		}
	}

	/**
	 * Перерасчет всех рейтингов для врачей
	 *
	 * @throws \CException
	 */
	protected function updateClinicRating()
	{
		if ($this->for_object == self::FOR_DOCTOR) {
			return;
		}

		$dataProvider = new \CActiveDataProvider(
			ClinicModel::class,
			[
				'criteria' => [
					'order' => 'id',
				],
			]
		);

		$iterator = new \CDataProviderIterator($dataProvider, 100);

		foreach ($iterator as $i) {
			/** @var ClinicModel $i */
			$this->saveRating($i);
		}
	}

	/**
	 * Создает или пересчитывает все рейтинги для объекта
	 *
	 * @param \CActiveRecord $object
	 *
	 * @throws \CException
	 */
	public function saveRatings(\CActiveRecord $object)
	{
		$strategyList = self::model()->findAll();
		foreach ($strategyList as $strategy) {
			$strategy->saveRating($object);
		}
	}

	/**
	 * Создает или пересчитывает рейтинг для определенной стратегии
	 *
	 * @param \CActiveRecord $object
	 * @return bool
	 * @throws \CException
	 */
	public function saveRating(\CActiveRecord $object)
	{
		$type = $this->getObjectType($object);

		$s = RatingModel::model()
			->byStrategy($this->id)
			->byObject($object->getPrimaryKey(), $type)
			->find();

		if (!$s) {
			$s = new RatingModel();
			$s->object_id = $object->getPrimaryKey();
			$s->object_type = $type;
			$s->strategy_id = $this->id;
		}

		$newRatingValue = $this->calcRating($object, $this->name);
		$s->rating_value = $newRatingValue;

		return $s->save();
	}

	/**
	 * Подсчет рейтинга
	 *
	 * @param \CActiveRecord $object
	 * @return float|int
	 */
	public function calcRating(\CActiveRecord $object)
	{
		switch ($this->type) {
			case 'multiply' :
				$rating = $this->calcRatingMultiply($object);
				break;
			case 'formula' :
				$rating = $this->calcRatingFormula($object);
				break;
			case 'default':
			default:
				$rating = $this->calcRatingDefault($object);
				break;
		}

		return $rating;
	}

	/**
	 * Подсчет рейтинга multiply
	 *
	 * @param \CActiveRecord $object
	 * @return float|int
	 */
	public function calcRatingMultiply(\CActiveRecord $object)
	{
		$rating = 0;

		if ($object instanceof DoctorClinicModel && $object->isDoctor()) {
			//КВ * КК * РК * СО (для врачей)
			$cl = $object->clinic;
			$doctorConversion = $object->doctor->conversion;

			if(is_null($doctorConversion) || (float)$doctorConversion == 0){
				$doctorConversion = $object->getLowerQuantile();
			} elseif($doctorConversion == 1) {
				$doctorConversion = $object->getMedianaConversion();
			}

			$clinicConversion = $cl->getCalculatedConversion();
			$handFactor = $cl->hand_factor ?: 1;
			$admissionCost = (float)$cl->admission_cost ?: ClinicModel::DEFAULT_ADMISSION_COST;

			$rating = $doctorConversion * $clinicConversion * $handFactor * $admissionCost;
		} elseif ($object instanceof ClinicModel) {
			$clinicConversion = $object->getCalculatedConversion();

			$handFactor = $object->hand_factor ?: 1;
			$admissionCost = (float)$object->admission_cost ?: ClinicModel::DEFAULT_ADMISSION_COST;

			$rating = $clinicConversion * $handFactor * $admissionCost;
		}

		return $rating;
	}

	/**
	 * Подсчет стратегии formula
	 *
	 * @param \CActiveRecord $object
	 * @return float|int
	 * @throws \Exception
	 */
	public function calcRatingFormula(\CActiveRecord $object)
	{
		$formula = new Formula($this->params);

		if ($object instanceof DoctorClinicModel) {
			foreach ($formula->getVariables('doctor') as $v) {

				try {
					$value = $object->doctor->$v;
				} catch (\Exception $e) {
					throw new \Exception("Расчет параметра doctor.{$v} не реализован. Не найден метод get{$v} или свойство {$v} ");
				}

				$formula->set('doctor', $v, $value);
			}

			foreach ($formula->getVariables('clinic') as $v) {
				try {
					$value = $object->clinic->$v;
				} catch (\Exception $e) {
					throw new \Exception("Расчет параметра clinic.{$v} не реализован. Не найден метод get{$v} или свойство {$v} ");
				}

				$formula->set('clinic', $v, $value);
			}
		}

		if ($object instanceof ClinicModel) {
			foreach ($formula->getVariables('clinic') as $v) {
				try {
					$value = $object->$v;
				} catch (\Exception $e) {
					throw new \Exception("Расчет параметра clinic.{$v} не реализован. Не найден метод get{$v} или свойство {$v} ");
				}

				$formula->set('clinic', $v, $value);
			}
		}

		$rating = $formula->evaluate();
		return $rating;
	}

	/**
	 * Подсчет дефолтного рейтинга
	 *
	 * @param \CActiveRecord $object
	 * @return float|int
	 */
	public function calcRatingDefault(\CActiveRecord $object)
	{
		$rating = 0;

		if ($object instanceof DoctorClinicModel && $object->isDoctor()) {
			$doctor = $object->doctor;
			$rating = (round($doctor->rating_internal, 2) + 1) - $doctor->experience_year * 0.000001 - 0.0000000000001 * $doctor->id;
		} elseif ($object instanceof ClinicModel) {
			$rating = round($object->rating_total, 2) - 0.000000001 * $object->id;
		}

		return $rating;
	}

	/**
	 * Тип объекта
	 *
	 * @param \CActiveRecord $object
	 * @return int
	 * @throws \CException
	 */
	public function getObjectType(\CActiveRecord $object)
	{
		if ($object instanceof DoctorClinicModel && $object->isDoctor()) {
			$type = RatingModel::TYPE_DOCTOR;
		} elseif ($object instanceof ClinicModel) {
			$type = RatingModel::TYPE_CLINIC;
		} else {
			throw new \CException('Неизвестный тип объекта');
		}

		return $type;
	}

	/**
	 * Пересчет рейтингов для врача в каждой клинике
	 *
	 * @param DoctorModel $doctor
	 *
	 * @throws \CException
	 */
	public function saveDoctorRatings(DoctorModel $doctor)
	{
		foreach($doctor->doctorClinics as $d4c){
			$this->saveRating($d4c);
		}
	}

	/**
	 * Выборка только тех стратегий, по которым нужно пересчитать рейтинги
	 *
	 * @return $this
	 */
	public function needsToRecalc()
	{
		$this->getDbCriteria()
			->mergeWith(
				[
					'condition' => 'needs_to_recalc = 1',
				]
			);

		return $this;
	}
} 
