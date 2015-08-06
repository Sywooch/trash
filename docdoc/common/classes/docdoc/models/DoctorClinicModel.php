<?php
namespace dfs\docdoc\models;
use dfs\docdoc\extensions\DateTimeUtils;

/**
 * This is the model class for table "doctor_4_clinic".
 *
 * The followings are the available columns in table 'doctor_4_clinic':
 *
 * @property integer $doctor_id
 * @property integer $clinic_id
 * @property integer $schedule_step
 * @property integer $id
 * @property string $doc_external_id
 * @property string $schedule_rule
 * @property string $has_slots
 * @property string $last_slots_update
 * @property integer $type тип (доктор|кабинет)
 *
 *
 * relations
 *
 * @property ClinicModel $clinic
 * @property DoctorModel $doctor
 * @property SlotModel $slots
 * @property RatingModel[] $ratings
 * @property ApiDoctorModel $apiDoctor
 *
 * @method DoctorClinicModel with
 * @method DoctorClinicModel find
 * @method DoctorClinicModel findByPk
 * @method DoctorClinicModel[] findAll
 * @method DoctorClinicModel[] findAllByPk
 * @method integer deleteAll
 */
class DoctorClinicModel extends \CActiveRecord
{
	/**
	 * Кеш для медианной конвермии
	 *
	 * @var null
	 */
	protected static $medianaConversions = [];

	/**
	 * Кеш для нижнего квантиля
	 *
	 * @var null
	 */
	protected static $lowerQuantiles = [];

	/**
	 * Типы (доктор,кабинет)
	 */
	const TYPE_DOCTOR = 1;
	const TYPE_CABINET = 2;

	/**
	 * Максимальное количество дней выводимых в расписании врача
	 */
	const COUNT_DAYS_FOR_SCHEDULE = 21;


	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 * @return DoctorClinicModel the static model class
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
		return 'doctor_4_clinic';
	}

	/**
	 * Правила валидации
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			[
				'doctor_id, clinic_id, schedule_step',
				'numerical',
				'integerOnly' => true
			],
			[
				'doctor_id, clinic_id',
				'required',
			],
			['schedule_rule, doc_external_id',
				'filter',
				'filter' => 'strip_tags'
			],
			[
				'schedule_step, doc_external_id, schedule_rule',
				'safe',
				'on' => [
					'insert', 'update'
				]
			],
			[
				'doctor_id, clinic_id',
				'safe',
				'on' => 'insert'
			],
			//нельзя изменять doctor_id, clinic_id при update
			[
				'doctor_id, clinic_id',
				'unsafe',
				'on' => [
					'update'
				]
			],
			[
				'schedule_step, schedule_rule',
				'safe',
				'on' => 'updateRules'
			],
			[
				'doctor_id',
				'exist',
				'attributeName' => 'id',
				'className'     => DoctorModel::class
			],
			[
				'clinic_id',
				'exist',
				'attributeName' => 'id',
				'className'     => ClinicModel::class
			],
			[
				'doc_external_id',
				'exist',
				'attributeName' => 'id',
				'className'     => ApiDoctorModel::class,
			],
			[
				'doc_external_id',
				'unique',
			],
		];
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return [
			'clinic'    => [
				self::BELONGS_TO, 'dfs\docdoc\models\ClinicModel', 'clinic_id'
			],
			'doctor'    => [
				self::BELONGS_TO, 'dfs\docdoc\models\DoctorModel', 'doctor_id'
			],
			'slots'     => [
				self::HAS_MANY, 'dfs\docdoc\models\SlotModel', 'doctor_4_clinic_id'
			],
			'ratings'   => [
				self::HAS_MANY,
				RatingModel::class,
				'object_id',
				'condition' => 'object_type = :type',
				'params'    => [':type' => RatingModel::TYPE_DOCTOR],
			],
			'apiDoctor' => [
				self::BELONGS_TO,
				ApiDoctorModel::class,
				'doc_external_id'
			]
		];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'id'              => 'ID',
			'doctor_id'       => 'Доктор',
			'clinic_id'       => 'клиника',
			'schedule_step'   => 'Длительность приема',
			'doc_external_id' => 'Идентификатор врача в клинике',
			'schedule_rule'   => 'Правило заполнения расписания врача',
			'has_slots'       => 'Есть слоты',
		];
	}

	/**
	 * Перед валидацией
	 *
	 * @return bool|void
	 */
	public function beforeValidate()
	{
		parent::beforeDelete();
		//чтобы всякие '' и 0 не лезли и не вызывали ошибки уникальности на уровне базы
		if (!$this->doc_external_id) {
			$this->doc_external_id = null;
		}

		return true;
	}

	/**
	 * @param int $doctor_id
	 * @param int $clinic_id
	 *
	 * @return DoctorClinicModel|null
	 */
	public function findDoctorClinic($doctor_id, $clinic_id)
	{
		return $this->findByAttributes(
			[
				'doctor_id' => $doctor_id,
				'clinic_id' => $clinic_id,
				'type' => self::TYPE_DOCTOR
			]
		);
	}

	/**
	 * Выбор врача в клинике по имени
	 *
	 * @param int    $clinicId
	 * @param string $name
	 * @param bool   $like
	 *
	 * @return DoctorClinicModel
	 */
	public function inClinicByName($clinicId, $name, $like = false)
	{
		$criteria = new \CDbCriteria();
		$criteria->condition = "t.clinic_id = :clinic_id and type = :type";
		$criteria->params = [':clinic_id' => $clinicId, ':type' => self::TYPE_DOCTOR];
		$criteria->with = [
			'doctor' => [
				'joinType' => 'INNER JOIN',
				'scopes'   => [
					'byName' => [$name, $like]
				]
			]
		];

		$this
			->getDbCriteria()
			->mergeWith($criteria);

		return $this;
	}

	/**
	 * Сохранение слотов из графика работы врача
	 *
	 * @param array $events
	 */
	public function saveSlotsFromSchedule($events)
	{
		$slots = [];
		$slotSec = $this->schedule_step * 60;

		//вычисляем слоты из интевалов
		foreach ($events as $e) {
			$start = strtotime($e['start']);

			$slot_num = floor((strtotime($e['end']) - $start) / $slotSec);

			for ($i = 0; $i < $slot_num; $i++) {

				$slots[date('Y-m-d H:i:s', $start)] = [
					'start_time'  => date('Y-m-d H:i:s', $start),
					'finish_time' => date('Y-m-d H:i:s', $start + $slotSec),
				];

				$start = $start + $slotSec;
			}
		}

		$this->saveSlots($slots, false);
	}

	/**
	 * Загрузка слотов для врачей в клиниках
	 *
	 * @param int[] $docInClinicIds
	 */
	public function loadSlotsFromApi($docInClinicIds)
	{
		//получаем информацию по врачам в клиниках
		$doctors = self::model()
			->with('clinic')
			->findAllByPk($docInClinicIds);

		foreach ($doctors as $doctor) {
			$slots = $doctor->loadSlots();
			$doctor->saveSlotsFromApi($slots);
		}
	}

	/**
	 * Загрузка слотов для врача в клинике
	 *
	 * @param string $from
	 * @param string $to
	 * @throws \CException
	 *
	 * @return array
	 */
	public function loadSlots($from = null, $to = null)
	{
		if (!$this->apiDoctor) {
			throw new \CException('Доктор не загружен из апи');
		}

		return $this->apiDoctor->loadSLots($from, $to);
	}

	/**
	 * Сохранение массива слотов, полученного из МИС клиники
	 *
	 * @link https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=12156945
	 *
	 * $apiSlots = [
	 *          [
	 *             "resourceId" => "id_клиники_в_мис#id_доктора_в_мис",
	 *             "slotId" => "id_клиники_в_мис#id_слота_в_клинике",
	 *              "attributes" => [
	 *                    "from" => "2013-01-01 10:00",
	 *                    "to" => "2013-01-01 10:30",
	 *               ]
	 *         ],
	 * ]
	 *
	 * @param \StdClass[] $apiSlots
	 *
	 * @return array
	 */
	public function saveSlotsFromApi($apiSlots)
	{
		$slots = [];

		foreach ($apiSlots as $slot) {
			list($clinicId, $doctorId) = explode("#", $slot->resourceId);

			$apiDoctor = $this->apiDoctor;

			if ($apiDoctor && $apiDoctor->api_doctor_id === $doctorId && $apiDoctor->api_clinic_id == $clinicId) {
				$from = date('Y-m-d H:i:00', strtotime($slot->attributes->from));

				$slots[$from] = [
					'doctor_4_clinic_id' => $this->id,
					'start_time'         => $from,
					'finish_time'        => date('Y-m-d H:i:00', strtotime($slot->attributes->to)),
					'external_id'        => $slot->slotId
				];
			}
		}

		return $this->saveSlots($slots);
	}


	/**
	 * Сохранение слотов
	 *
	 * @param array $slots
	 * @param bool $checkExternalId проверять ли на совпадения дат и внешнего идентификатора
	 *
	 * @return array
	 */
	public function saveSlots($slots, $checkExternalId = true)
	{
		$result = [
			'total'  => count($slots),
			'delete' => 0,
			'exists' => 0,
			'inLast' => 0,
			'new'    => 0,
		];

		//получаем существующие действующие слоты
		$exists_slots = SlotModel::model()
			->forDoctorInClinic($this->id)
			->activeSlots()
			->findAll();

		//TODO ПРОРАБОТАТЬ УДАЛЕНИЕ ЗАБУКАННОГО СЛОТА: СЛОТ ЗАБУКАН, И ИЗМЕНИЛОСЬ РАСПИСАНИЕ ВРАЧА

		$now = date('Y-m-d H:i');
		$has_slots = false;

		if (!empty($exists_slots)) {
			foreach ($exists_slots as $slot) {
				//если такого слота нет среди существующих - удаляем
				if (!isset($slots[$slot->start_time])) {
					$slot->delete();
					$result['delete']++;
				} elseif ($slots[$slot->start_time]['finish_time'] !== $slot->finish_time) {
					//если время окончания у существующего слота другое
					$slot->delete();
					$result['delete']++;
				} elseif ($checkExternalId && $slots[$slot->start_time]['external_id'] != $slot->external_id) {
					$slot->delete();
					$result['delete']++;
				} else {
					//если точно такой слот уже есть, нового не сохраняем
					unset($slots[$slot->start_time]);
					$result['exists']++;

					if($slot->start_time > $now){
						$has_slots = true;
					}

				}
			}
		}

		foreach ($slots as $params) {

			//нельзя изменять слоты в прошлом
			if ($params['start_time'] < $now) {
				$result['inLast']++;
				continue;
			}

			$slot = new SlotModel();
			$slot->setAttributes($params);
			$slot->doctor_4_clinic_id = $this->id;
			$slot->save();
			$result['new']++;
		}

		//есть слоты ставлю если есть хоть один слот у которого start_time > $now
		!$has_slots && $has_slots = (bool)$result['new'];

		$this->has_slots = $has_slots;
		$this->last_slots_update = date('Y-m-d H:i:s');
		$this->save();

		return $result;
	}

	/**
	 * Сохранение правил заполнения расписания доктора в клинике
	 *
	 * Отдельно сохраняется schedule_step + все параметры сериализутся и сохраняются в schedule_rule.
	 * Данные из schedule_rule используются для постройния формы заполнения расписания.
	 *
	 * @param $rules
	 *
	 * @return bool
	 */
	public function saveScheduleRules($rules)
	{
		$this->setScenario('updateRules');

		//хрен знает, что в этом массиве придет
		foreach ($rules as $k => $v) {
			$rules[$k] = htmlspecialchars(strip_tags($v));
		}

		$this->attributes = $rules;
		$this->schedule_rule = serialize($rules);

		return $this->save();
	}

	/**
	 * Получение массива параметров для автогенерации расписания
	 *
	 * @return string[]|null
	 */
	public function getScheduleRules()
	{
		if (!empty($this->schedule_rule)) {
			return unserialize($this->schedule_rule);
		}

		return null;
	}

	/**
	 * Выборка только врачей, у которых есть ID в МИС
	 *
	 * @param bool $with
	 * @return DoctorClinicModel
	 */
	public function withExternalId($with = true)
	{
		if ($with) {
			$condition = $this->getTableAlias() . ".doc_external_id IS NOT NULL";
		} else {
			$condition = $this->getTableAlias() . ".doc_external_id IS NULL";
		}

		$this->getDbCriteria()->mergeWith([
			'condition' => $condition,
		]);

		return $this;
	}

	/**
	 * Возвращает массив с соответствием идентификатора доктора у нас и идентификатора в МИС
	 * для всех докторов в клинике $clinicId
	 *
	 * @param int $clinicId
	 *
	 * @return array
	 */
	public function getDocExternalIds($clinicId)
	{
		//выбираем идентификаторы
		$criteria = new \CDbCriteria();
		$criteria->select = "doctor_id, doc_external_id ";
		$criteria->with = [
			'clinic' => [
				//для всех филиалов данной клиники
				'condition' => 'clinic.id=:id',
				'params'    => [':id' => $clinicId],
				'joinType'  => 'INNER JOIN',
			]];
		//врачей, у которых есть иентификатор в МИС
		$criteria->scopes = "withExternalId";


		$docIds = self::model()
			->findAll($criteria);

		$ids = [];
		foreach ($docIds as $d) {
			$ids[$d->doc_external_id] = $d->doctor_id;
		}

		return $ids;
	}

	/**
	 * Сохранение идентификатора в МИС
	 *
	 * @param int $docExternalId
	 *
	 * @return bool
	 */
	public function saveExternalId($docExternalId)
	{
		$this->doc_external_id = $docExternalId;

		return $this->save();
	}

	/**
	 * Склеивание нашего врача и врача, загруженного из МИС
	 *
	 * @param ApiDoctorModel $apiDoctor
	 *
	 * @return bool
	 */
	public function mergeWithApiDoctor(ApiDoctorModel $apiDoctor)
	{
		$clinic = $apiDoctor->getClinic();
		if ($clinic === null) {
			return false;
		}

		//от имени отделяем толькот фамилию и имя
		$n = explode(" ",  str_replace(".", "", $apiDoctor->name));
		$name = count($n) != 3 ? $apiDoctor->name : $n[0] . " " . $n[1];

		$doctors = DoctorClinicModel::model()
			->inClinicByName($clinic->id, $name, true)
			->withExternalId(false)
			->findAll();

		if (!count($doctors)) {
			return false;
		}

		$doctorInClinic = $doctors[0];
		//если болье одного врача, ищем четкое совпадение
		if (count($doctors) > 1) {
			foreach ($doctors as $d) {
				if (mb_strtolower($d->doctor->name) === mb_strtolower($apiDoctor->name) && $d->doctor->isActive()) {
					$doctorInClinic = $d;
				}
			}
		}

		return $doctorInClinic->saveExternalId($apiDoctor->id);
	}

	/**
	 * Получение активных врачей, расписание которых нужно обновить
	 *
	 * @param string $date
	 *
	 * @return DoctorClinicModel
	 */
	public function needUpdate($date)
	{
		$this->getDbCriteria()->mergeWith([
			'condition' => "last_slots_update IS NULL OR last_slots_update < :date",
			'params'    => [
				':date' => $date,
			],
			'with'      => [
				'apiDoctor' => [
					'select'    => false,
					'joinType'  => 'INNER JOIN',
					'condition' => 'apiDoctor.enabled = 1',
					'with' => [
						'api_clinic' => [
							'select' => false,
							'joinType'  => 'INNER JOIN',
							'condition' => 'api_clinic.enabled = 1',
						]
					]
				]
			]
		]);

		return $this;
	}

	/**
	 * Получение массива с рейтингами
	 *
	 * @return array
	 */
	public function getRatings()
	{
		$data = [];

		foreach ($this->ratings as $rating) {
			$data[$rating->strategy_id] = $rating->rating_value;
		}

		return $data;
	}

	/**
	 * Перед удалением
	 *
	 * @return bool|void
	 */
	public function beforeDelete()
	{
		parent::beforeDelete();

		//удаляю связи на которых нет ключей
		RatingModel::model()->deleteAllByAttributes(
			['object_id' => $this->id, 'object_type' => RatingModel::TYPE_DOCTOR]
		);

		return true;
	}

	/**
	 * Рассчитать медианную конверсию
	 *
	 * @return mixed|null
	 */
	public function getMedianaConversion()
	{
		$cityId = $this->clinic->city_id;

		if(!isset(self::$medianaConversions[$cityId])){
			$cr = new \CDbCriteria();

			$cr->with = [
				'doctor' => [
					'joinType' => 'INNER JOIN',
					'select' => false,
					'condition' => 'doctor.status = :status and doctor.conversion is not null and doctor.conversion > 0',
					'params' => [':status' => DoctorModel::STATUS_ACTIVE]
				],
				'clinic' => [
					'joinType' => 'INNER JOIN',
					'select' => false,
					'condition' => 'clinic.city_id = :city_id',
					'params' => [':city_id' => $cityId]
				]
			];

			$count = self::model()->count($cr);

			$middle = floor($count / 2); //середина

			if($count % 2){
				$limit = 1;
				$offset = $middle;
			} else {
				$limit = 2;
				$offset = $middle - 1;
			}

			$offset < 0 && $offset = 0;

			$status = DoctorModel::STATUS_ACTIVE;

			$sql = "SELECT avg(conversion)
						FROM (
			 				SELECT d.conversion
			 					FROM doctor d
				 				JOIN doctor_4_clinic dc ON dc.doctor_id = d.id and dc.type = " . self::TYPE_DOCTOR . "
				 				JOIN clinic c ON c.id = dc.clinic_id
			 				WHERE
				 				c.city_id = $cityId
				 				AND d.status = $status
				 				AND d.conversion IS NOT NULL AND d.conversion > 0
			 					ORDER BY d.conversion
			 				LIMIT $offset, $limit
						) t";

			$command = $this->getDbConnection()->createCommand($sql);

			self::$medianaConversions[$cityId] = (float)$command->queryScalar();
		}

		return self::$medianaConversions[$cityId];
	}

	/**
	 * Рассчитать нижний квантиль
	 *
	 * @return mixed|null
	 * @throws \CDbException
	 */
	public function getLowerQuantile()
	{
		$cityId = $this->clinic->city_id;

		if(!isset(self::$lowerQuantiles[$cityId])){
			$cr = new \CDbCriteria();

			$cr->with = [
				'doctor' => [
					'joinType' => 'INNER JOIN',
					'select' => false,
					'condition' => 'doctor.status = :status and doctor.conversion is not null and doctor.conversion > 0',
					'params' => [':status' => DoctorModel::STATUS_ACTIVE]
				],
				'clinic' => [
					'joinType' => 'INNER JOIN',
					'select' => false,
					'condition' => 'clinic.city_id = :city_id',
					'params' => [':city_id' => $cityId]
				]
			];

			$count = self::model()->count($cr);

			$middle = floor($count / 4); //четверть

			if($count % 4){
				$limit = 1;
				$offset = $middle;
			} else {
				$limit = 2;
				$offset = $middle - 1;
			}

			$offset < 0 && $offset = 0;

			$status = DoctorModel::STATUS_ACTIVE;

			$sql = "select avg(conversion) from (
						select d.conversion from doctor d
							join doctor_4_clinic dc on dc.doctor_id = d.id and dc.type = " . self::TYPE_DOCTOR . "
							join clinic c on c.id = dc.clinic_id
						where
							c.city_id = $cityId
							and d.status=$status
							and d.conversion is not null and d.conversion > 0
						order by d.conversion
						limit $offset, $limit) t";

			$command = $this->getDbConnection()->createCommand($sql);

			self::$lowerQuantiles[$cityId] = (float)$command->queryScalar();
		}

		return self::$lowerQuantiles[$cityId];
	}

	/**
	 * Чистит кеш в статических переменных
	 */
	public function clearStaticVariableCache()
	{
		self::$medianaConversions = [];
		self::$lowerQuantiles = [];
	}

	/**
	 * Обновление поля has_slots для всех у кого нет расписания на now() + 20 мин
	 *
	 * @return int
	 * @throws \CDbException
	 */
	public function updateHasSlots()
	{
		$command = $this->getCommandBuilder()->createSqlCommand(
			'UPDATE doctor_4_clinic SET has_slots = FALSE WHERE has_slots
				AND NOT exists (
					SELECT * FROM slot WHERE doctor_4_clinic_id = doctor_4_clinic.id AND start_time > :start_time);'
		);

		$res = $command->execute(['start_time' => date('c', strtotime('+ 20 min'))]);
		return $res;
	}

	/**
	 * Получение слотов врача в клинике
	 *
	 * @param string $from
	 * @param string $to
	 * @param bool   $onlyAvailable только доступные для бука слоты
	 *
	 * @return SlotModel[]
	 */
	public function getSlots($from, $to = null, $onlyAvailable = false)
	{
		$slots = SlotModel::model()
			->forDoctorInClinic($this->id)
			->inInterval($from, $to);

		if ($onlyAvailable) {
			$slots->activeSlots();
		}
		$slots->ordered();

		return $slots->findAll();
	}


	/**
	 * Получить даты, на которые есть слоты у врача
	 *
	 * @param string $from
	 * @param string $to
	 * @param bool   $onlyAvailable только доступные для бука слоты
	 *
	 * @return SlotModel[]
	 */
	public function getSlotDates($from, $to = null, $onlyAvailable = false)
	{
		$slots = SlotModel::model()
			->forDoctorInClinic($this->id)
			->inInterval($from, $to)
			->groupByDate();
		if ($onlyAvailable) {
			$slots->activeSlots();
		}
		$slots->ordered();

		return $slots->findAll();
	}

	/**
	 * Поиск по типам
	 *
	 * @param int[] $types
	 *
	 * @return $this
	 */
	public function inTypes(array $types)
	{
		$criteria = new \CDbCriteria();
		$criteria->addInCondition('type', $types);

		$this->getDbCriteria()->mergeWith($criteria);

		return $this;
	}

	/**
	 * Является ли объект доктором
	 *
	 * @return bool
	 */
	public function isDoctor()
	{
		return $this->type == self::TYPE_DOCTOR;
	}

	/**
	 * Формирование расписания для врачей
	 *
	 * @param int[] $doctorIds
	 * @param int   $days
	 *
	 * @return array
	 */
	public function getDoctorsSchedule($doctorIds, $days)
	{
		$ids = [];
		foreach ($doctorIds as $id) {
			$ids[] = intval($id);
		}

		$currentTime = mktime(0, 0, 0);

		$sql = 'SELECT dc.doctor_id, dc.clinic_id, s.start_time, s.finish_time, s.external_id
			FROM slot as s
				INNER JOIN doctor_4_clinic dc ON (s.doctor_4_clinic_id = dc.id AND dc.type = :type AND dc.has_slots = 1)
				INNER JOIN clinic c ON (c.id = dc.clinic_id AND c.scheduleForDoctors = 1)
			WHERE dc.doctor_id IN (' . implode(', ', $ids) . ') AND
				s.start_time >= :startTime AND s.start_time < :endTime
			ORDER BY s.start_time';

		$slots = $this->getDbConnection()->createCommand($sql)->queryAll(true, [
			'type' => self::TYPE_DOCTOR,
			'startTime' => date('c', $currentTime),
			'endTime' => date('c', $currentTime + $days * 86400),
		]);

		$schedule = [];

		foreach ($slots as $slot) {
			$beginTime = strtotime($slot['start_time']);
			$endTime = strtotime($slot['finish_time']);

			$day = date('Y-m-d', $beginTime);

			$doctorId = $slot['doctor_id'];
			$clinicId = $slot['clinic_id'];

			if (!isset($schedule[$doctorId][$clinicId][$day])) {
				$schedule[$doctorId][$clinicId][$day] = [
					'BeginTime' => $beginTime,
					'EndTime' => $endTime,
					'DoctorId' => $doctorId,
					'ClinicId' => $clinicId,
					'Date' => $day,
				];
			} else {
				$sch = &$schedule[$doctorId][$clinicId][$day];

				if ($sch['BeginTime'] > $beginTime) {
					$sch['BeginTime'] = $beginTime;
				}

				if ($sch['EndTime'] < $endTime) {
					$sch['EndTime'] = $endTime;
				}
			}
		}

		return $schedule;
	}

	/**
	 * Форматирование расписания для врача
	 *
	 * @param array $schedule
	 * @param int $days
	 *
	 * @return array
	 */
	static public function formatScheduleForDoctor($schedule, $days)
	{
		static $month = ['Янв', 'Фев', 'Март', 'Апр', 'Май', 'Июнь', 'Июль', 'Авг', 'Сен', 'Окт', 'Нояб', 'Дек'];

		$df = \Yii::app()->dateFormatter;

		$result = [];
		$datetime = mktime(0, 0, 0);

		$firstActiveDay = 0;
		$lastActiveDay = 0;
		for ($d = 1; $d <= $days; $d++) {
			$day = date('Y-m-d', $datetime);

			$item = [
				'Day' => $df->format('ccc, d', $datetime) . ' ' . $month[date('m', $datetime) - 1],
				'Work' => 0,
			];

			$datetime += 86400;

			if (!empty($schedule[$day])) {
				$scDay = $schedule[$day];
				$item['ClinicId'] = $scDay['ClinicId'];
				$item['DoctorId'] = $scDay['DoctorId'];
				$item['Date'] = $scDay['Date'];
				if (($scDay['EndTime'] - $scDay['BeginTime']) >= 10800) {
					$item['Work'] = 1;
					$item['Begin'] = date('H:i', $scDay['BeginTime']);
					$item['End'] = $scDay['EndTime'] > $datetime ? '24:00' : date('H:i', $scDay['EndTime']);
					$lastActiveDay = $d;
					if (!$firstActiveDay) {
						$firstActiveDay = $d;
					}
				}
			}

			$result[] = $item;
		}

		if ($lastActiveDay < 7) {
			$lastActiveDay = 7;
			$firstActiveDay = 1;
		}
		elseif ($firstActiveDay > $days - 7) {
			$firstActiveDay = $days > 7 ? $days - 7 : 1;
		}

		return array_slice($result, $firstActiveDay - 1, $lastActiveDay);
	}
}
