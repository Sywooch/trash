<?php

use likefifa\components\system\ActiveRecord;
use likefifa\components\system\DbCriteria;
use likefifa\models\AdminModel;
use dfs\modules\payments\models\PaymentsAccount;
use dfs\modules\payments\models\PaymentsOperations;
use likefifa\models\LfAppointmentFavorite;
use likefifa\models\LfAppointmentLog;

/**
 * This is the model class for table "lf_appointment".
 *
 * The followings are the available columns in table 'lf_appointment':
 *
 * @property integer               $id
 * @property string                $name
 * @property integer               $specialization_id
 * @property integer               $service_id
 * @property string                $phone
 * @property string                $email
 * @property integer               $departure
 * @property string                $address
 * @property integer               $service_price    Стоимость начальная
 * @property integer               $service_price2   Стоимость итоговая
 * @property integer               $status           Статус заказа
 * @property integer               $is_long          Отсылалось или нет уведомление мастеру за 12 часов после в стречи, с напоминанием
 * о необходимости закрыть заявку
 * @property integer               $master_id        Идентификатор мастера
 * @property integer               $salon_id
 * @property string                $changed          Время последнего изменения
 * @property string                $created          Время создания
 * @property integer               $lat
 * @property integer               $lng
 * @property string                $reason
 * @property string                $operator_comment Комментарий оператора
 * @property integer               $control          Дата контроля
 * @property integer               $phone_numeric
 * @property string                $more
 * @property integer               $date
 * @property integer               $time
 * @property string                $service_name
 * @property int                   $admin_id         Идентификатор администратора, редактировавшего заявку
 * @property integer               $underground_station_id
 * @property boolean               $is_viewed
 * @property string                $create_source
 *
 * @property LfSpecialization      $specialization
 * @property LfService             $service
 * @property LfMaster              $master
 * @property LfSalon               $salon
 * @property LfAppointmentLog[]    $logs
 *
 * @property integer               numericCreated
 * @property integer               $numericChanged
 *
 * The followings are the available model relations:
 * @property LfAppointmentFavorite $favorite
 * @property UndergroundStation    $undergroundStation
 * @property AdminModel            $admin
 *
 * @method LfAppointment findByPk
 * @method LfAppointment find
 * @method LfAppointment[] findAll
 * @method LfAppointment[] findAllByAttributes
 */
class LfAppointment extends ActiveRecord
{

	/**
	 * Имя/Фамилия мастера
	 *
	 * @var string
	 */
	public $master_name;

	/**
	 * Название салона
	 *
	 * @var string
	 */
	public $salon_name;

	/**
	 * Телефон мастера
	 *
	 * @var string
	 */
	public $master_tel;

	/**
	 * Телефон салона
	 *
	 * @var string
	 */
	public $salon_tel;

	/**
	 * Не было напоминания о завершении заявки
	 *
	 * @var int
	 */
	const LONG_NONE = 0;

	/**
	 * Первое напонимание. Через LONG_FIRST часов
	 *
	 * @var int
	 */
	const LONG_FIRST = 1;

	/**
	 * Второе напоминание. Через LONG_SECOND часов
	 *
	 * @var int
	 */
	const LONG_SECOND = 24;

	/**
	 * Последнее напоминания о завершении заявки + завершение через LONG_LAST часов
	 *
	 * @var int
	 */
	const LONG_LAST = 72;

	/**
	 * Количество часов, по истечению которых непринятая заявка отклоняется
	 *
	 * @var int
	 */
	const MAX_NEW_TIME = 72;

	/**
	 * Стандартный GA заявки
	 */
	const GA_TYPE = "sign_up";

	const STATUS_NEW = 0;
	const STATUS_PROCESSING_BY_MASTER = 10;
	const STATUS_REJECTED_AFTER_ACCEPTED = 20;
	const STATUS_REJECTED = 30;
	const STATUS_ACCEPTED = 40;
	const STATUS_COMPLETED = 50;
	const STATUS_15_MIN_LEFT = 60;
	const STATUS_REMOVED = 70;

	/**
	 * Источники создания заявки
	 */
	const SOURCE_FRONT = 'front';
	const SOURCE_BO = 'bo';
	public static $sourcesList = [
		self::SOURCE_FRONT => 'Сайт',
		self::SOURCE_BO    => 'БО',
	];

	public $dateFormatted = null;
	public $dateDate = null;
	public $dateTime = null;

	public $reasonText = null;

	/**
	 * Статус в момент загрузки модели
	 *
	 * @var int
	 */
	private $old_status;

	public $from_date;
	public $to_date;
	public $app_name;
	public $oneHourLeft = false;

	public $statusList = [
		self::STATUS_NEW                     => 'Новая заявка',
		self::STATUS_PROCESSING_BY_MASTER    => 'В обработке',
		self::STATUS_REJECTED                => 'Отклонена',
		self::STATUS_ACCEPTED                => 'Принята',
		self::STATUS_REJECTED_AFTER_ACCEPTED => 'Отклонена после принятия',
		self::STATUS_COMPLETED               => 'Завершена',
		self::STATUS_REMOVED                 => 'Удалена',
	];

	public static $statusIcons = [
		self::STATUS_NEW                     => 'circle-thin',
		self::STATUS_PROCESSING_BY_MASTER    => 'eye',
		self::STATUS_REJECTED_AFTER_ACCEPTED => 'asterisk',
		self::STATUS_REJECTED                => 'asterisk',
		self::STATUS_ACCEPTED                => 'female',
		self::STATUS_COMPLETED               => 'check',
		self::STATUS_REMOVED                 => 'times',
	];

	protected $reasonList = array(
		array(
			'Не договорились о времени',
			'Клиент передумал',
			'Не дозвонился до клиента',
			'Другое:'
		),
		array(
			'Клиент не пришел',
			'Другое:'
		),
	);

	public function getReasonListItems($status)
	{
		return $status == 'new' ? $this->reasonList[0] : $this->reasonList[1];
	}

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className active record class name.
	 *
	 * @return LfAppointment the static model class
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
		return 'lf_appointment';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('name, phone', 'required'),
			array('specialization_id, service_id', 'required', 'except' => 'admin'),
			array(
				'specialization_id, is_long, admin_id, service_id, departure, date, dateDate, dateTime, status,
					master_id, salon_id, phone_numeric, underground_station_id',
				'numerical',
				'integerOnly' => true
			),
			['control', 'default', 'value' => 0],
			array('status', 'numerical', 'min' => self::STATUS_NEW, 'max' => self::STATUS_REMOVED),
			array(
				'name, control, phone, service_name, service_price, service_price2, email, operator_comment,
								master_tel, salon_tel, master_name, salon_name',
				'length',
				'max' => 256
			),
			array('created, address, more', 'length', 'max' => 1024),
			array('name, phone, address, email, more, dateFormatted, reason', 'filter', 'filter' => 'strip_tags'),
			array('underground_station_id', 'default', 'value' => null),
			['is_viewed', 'default', 'value' => 0],
			['create_source', 'default', 'value' => self::SOURCE_FRONT],
			array(
				'id, name, specialization_id, service_id, phone, email, departure, address, date, time',
				'safe',
				'on' => 'search'
			),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'specialization'     => array(self::BELONGS_TO, 'LfSpecialization', 'specialization_id'),
			'service'            => array(self::BELONGS_TO, 'LfService', 'service_id'),
			'master'             => array(self::BELONGS_TO, 'LfMaster', 'master_id'),
			'salon'              => array(self::BELONGS_TO, 'LfSalon', 'salon_id'),
			'admin'              => array(
				self::BELONGS_TO,
				'likefifa\models\AdminModel',
				'admin_id'
			),
			'favorite'           => [self::HAS_ONE, 'likefifa\models\LfAppointmentFavorite', 'appointment_id'],
			'undergroundStation' => array(
				self::BELONGS_TO,
				'UndergroundStation',
				'underground_station_id',
				'together' => true
			),
			'logs'               => array(self::HAS_MANY, 'likefifa\models\LfAppointmentLog', 'appointment_id'),
		);
	}

	/**
	 * Не показывает удаленные заявки
	 *
	 * @return array
	 */
	public function defaultScope()
	{
		$alias = $this->getTableAlias(false, false);
		return [
			'condition' => $alias . '.status != :removed_status',
			'params'    => [
				':removed_status' => self::STATUS_REMOVED,
			]
		];
	}

	/**
	 * @param $service_id
	 * @param $master_id
	 *
	 * @return int
	 */
	public function getMasterPrice($service_id, $master_id)
	{
		$masterPrice =
			LfPrice::model()->findByAttributes(array('service_id' => $service_id, 'master_id' => $master_id));
		if ($masterPrice) {
			return $masterPrice->price;
		}
		return null;
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'                => '№',
			'name'              => 'Клиент',
			'specialization_id' => 'Specialization',
			'service_id'        => 'Услуга',
			'phone'             => 'Телефон клиента',
			'status'            => 'Статус',
			'email'             => 'Email',
			'departure'         => 'Выезд',
			'address'           => 'Address',
			'created'           => 'Дата создания',
			'date'              => 'Дата приема',
			'reason'            => 'Комментарий мастера',
			'master_id'         => 'Мастер',
			'salon_id'          => 'Салон',
			'salon_name'        => 'Салон',
			'master_tel'        => 'Телефон мастера',
			'admin_id'          => 'Оператор',
			'control'           => 'Дата контроля',
			'service_name'      => 'Услуги',
			'service_price'     => 'Цена нач.',
			'service_price2'    => 'Цена кон.',
			'is_long'           => 'Заявка ещё не закрыта',
			'operator_comment'  => 'Комментарий оператора',
			'is_viewed'         => 'Просмотрена',
			'create_source'     => 'Источник заявки',
		);
	}

	public function behaviors()
	{
		return [
			'CArModTimeBehavior'          => [
				'class' => 'application.extensions.CArModTimeBehavior',
			],
			'AppointmentLogBehavior' => [
				'class' => 'likefifa\components\extensions\AppointmentLogBehavior',
			],
		];
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria = new DbCriteria;

		$criteria->with = array(
			'master'  => [
				'joinType' => 'LEFT JOIN',
			],
			"salon"   => [
				'joinType' => 'LEFT JOIN',
			],
			'service' => [
				'select'   => ['name'],
				'joinType' => 'LEFT JOIN',
			]
		);
		$criteria->together = true;
		$criteria->compare('t.id', $this->id);
		$criteria->compare('t.name', $this->name);

		if ($this->date) {
			$date = CDateTimeParser::parse($this->date, 'dd.MM.yyyy');
			$criteria->compare('t.date>', $date);
			$criteria->compare('t.date<', $date + 60 * 60 * 24);
		}

		// Если фильтр по статусу и фильтруется на удаленные - сбрасываем default scope
		if ($this->status && $this->status == self::STATUS_REMOVED) {
			$this->resetScope(true);
		}

		$criteria->compare('t.status', $this->status);

		$criteria->compare('t.specialization_id', $this->specialization_id);
		$criteria->compare('t.service_id', $this->service_id);
		$criteria->compare('t.phone_numeric', $this->phone, true);
		$criteria->compare('t.service_name', $this->service_name, true);
		$criteria->compare('t.email', $this->email, true);
		$criteria->compare('t.departure', $this->departure);
		$criteria->compare('t.address', $this->address, true);
		$criteria->compare('master.phone_numeric', $this->master_tel, true);
		$criteria->compare('salon.phone_numeric', $this->salon_tel, true);
		$criteria->compare('master.surname', $this->master_name, true);
		$criteria->compare('salon.name', $this->salon_name, true);
		$criteria->compare('t.admin_id', $this->admin_id);
		$criteria->compare('t.is_viewed', $this->is_viewed);
		$criteria->compare('t.create_source', $this->create_source);

		return new CActiveDataProvider(
			$this, array(
				'criteria'   => $criteria,
				'pagination' => array(
					'pageSize' => 50,
				),
				'sort'       => array(
					'attributes' => array(
						'id'               => 't.id',
						'created'          => 't.created',
						'date'             => 't.date',
						'master_name'      => 'master.name',
						'master_tel'       => 'master.phone_cell',
						'salon_tel'        => 'salon.phone',
						'name'             => 't.name',
						'phone'            => 't.phone',
						'service_name'     => 't.service_name',
						'service_price2'   => 't.service_price2',
						'reason'           => 't.reason',
						'operator_comment' => 't.operator_comment',
						'status'           => 't.status',
						'control'          => 't.control',
						'departure'        => 't.departure',
						'salon_name'       => 'salon.name',
					)
				),
			)
		);
	}

	/**
	 * Получает модель для вывода заявок в ЛК
	 *
	 * @param        int          /array $status статус
	 * @param int    $master_id   идентификатор мастера
	 * @param int    $salon_id    идентификатор салона
	 * @param string $status_type тип статуса
	 *
	 * @return CArrayDataProvider
	 */
	public function custom_search($status = null, $master_id = null, $salon_id = null, $status_type = 'new')
	{
		$criteria = new CDbCriteria();
		if ($status_type == 'new') {
			if (!empty($this->from_date) && empty($this->to_date)) {
				$from_date = date("Y-m-d H:i:s", $this->from_date);
				$criteria->condition = "created >= '$from_date'";
			} elseif (!empty($this->to_date) && empty($this->from_date)) {
				$to_date = date("Y-m-d H:i:s", $this->to_date);
				$criteria->condition = "created <= '$to_date'";
			} elseif (!empty($this->to_date) && !empty($this->from_date)) {
				$from_date = date("Y-m-d H:i:s", $this->from_date);
				$to_date = date("Y-m-d H:i:s", $this->to_date);
				$criteria->condition = "created >= '$from_date ' and changed <= '$to_date'";
			}
		} elseif ($status_type == 'apply') {
			if (!empty($this->from_date) && empty($this->to_date)) {
				$criteria->condition = "date >= '$this->from_date'";
			} elseif (!empty($this->to_date) && empty($this->from_date)) {
				$criteria->condition = "date <= '$this->to_date'";
			} elseif (!empty($this->to_date) && !empty($this->from_date)) {
				$criteria->condition = "date >= '$this->from_date' and date <= '$this->to_date'";
			}
		} else {
			if (!empty($this->from_date) && empty($this->to_date)) {
				$from_date = date("Y-m-d H:i:s", $this->from_date);
				$criteria->condition = "changed >= '$from_date'";
			} elseif (!empty($this->to_date) && empty($this->from_date)) {
				$to_date = date("Y-m-d H:i:s", $this->to_date);
				$criteria->condition = "changed <= '$to_date'";
			} elseif (!empty($this->to_date) && !empty($this->from_date)) {
				$from_date = date("Y-m-d H:i:s", $this->from_date);
				$to_date = date("Y-m-d H:i:s", $this->to_date);
				$criteria->condition = "changed >= '$from_date ' and changed <= '$to_date'";
			}
		}
		if (!empty($this->app_name)) {
			$criteria->compare('name', $this->app_name, true);
		}
		if ($status !== null) {
			$criteria->compare('status', $status);
		}
		if ($master_id) {
			$criteria->compare('master_id', $master_id);
		} elseif ($salon_id) {
			$criteria->compare('salon_id', $salon_id);
		}
		$criteria->order = 'changed DESC';
		$data = $this->findAll($criteria);
		$dataProvider = new CArrayDataProvider(
			$data, array(
				'pagination' => array(
					'pageSize' => 20,
					'pageVar'  => 'page',
				),
			)
		);

		return $dataProvider;
	}

	public function getItemsCount($master_id = null, $salon_id = null)
	{
		$itemsCount = array();

		$criteria = new CDbCriteria();
		if ($master_id) {
			$criteria->compare('master_id', $master_id);
		} elseif ($salon_id) {
			$criteria->compare('salon_id', $salon_id);
		}

		$status = array(self::STATUS_NEW);
		$criteria->compare('status', $status);
		$new = $this->findAll($criteria);

		$criteria = new CDbCriteria();
		if ($master_id) {
			$criteria->compare('master_id', $master_id);
		} elseif ($salon_id) {
			$criteria->compare('salon_id', $salon_id);
		}
		$status = array(self::STATUS_ACCEPTED, self::STATUS_PROCESSING_BY_MASTER);
		$criteria->compare('status', $status);
		$apply = $this->findAll($criteria);

		$criteria = new CDbCriteria();
		if ($master_id) {
			$criteria->compare('master_id', $master_id);
		} elseif ($salon_id) {
			$criteria->compare('salon_id', $salon_id);
		}
		$status = array(self::STATUS_REJECTED, self::STATUS_REJECTED_AFTER_ACCEPTED);
		$criteria->compare('status', $status);
		$cancel = $this->findAll($criteria);

		$criteria = new CDbCriteria();
		if ($master_id) {
			$criteria->compare('master_id', $master_id);
		} elseif ($salon_id) {
			$criteria->compare('salon_id', $salon_id);
		}
		$status = self::STATUS_COMPLETED;
		$criteria->compare('status', $status);
		$completed = $this->findAll($criteria);

		$itemsCount['Новые'] = count($new);
		$itemsCount['Принятые'] = count($apply);
		$itemsCount['Отклоненные'] = count($cancel);
		$itemsCount['Завершенные'] = count($completed);

		return $itemsCount;
	}

	/**
	 * Получает время, до которого будет жить заявка
	 *
	 * @return int секунды
	 */
	public function getRejectedTime()
	{
		return $this->getTimeoutWithNightTime($this->getAutoRejectedTimeout());
	}

	/**
	 * Получает время, по наступлении которого заявка покраснеет
	 *
	 * @return int секунды
	 */
	public function getRedAlarmTime()
	{
		return $this->getTimeoutWithNightTime($this->getRedAlarmTimeout());
	}

	/**
	 * Получает количество секунд, в течение
	 * которых мастер может принять заявку
	 *
	 * @return int секунды, например 2 часа (2*60*60)
	 */
	private function getAutoRejectedTimeout()
	{
		return Yii::app()->params['AppointmentsAutoRejectedTimeout'];
	}

	/**
	 * Получает количество секунд до конца таймаута,
	 * в течение которого заявка считается горящей
	 *
	 * @return int секунды, например 15 минут (15*60)
	 */
	private function getRedAlarmTimeout()
	{
		return Yii::app()->params['AppointmentsRedAlarmTimeout'];
	}

	/**
	 * Получает время жизни заявки (время "жизни", горящей "горящей")
	 * + дополнительное время, если заявка была откравлена в ночное время
	 * (до 9:00 или после 21:00)
	 *
	 * @param int $extraTime время "жизни" заявки в секундах (2*60*60 / 15*60)
	 *
	 * @return int
	 */
	private function getTimeoutWithNightTime($extraTime)
	{
		$h = date("H", $this->numericCreated);
		$difference = 0;
		if ($h < 9) {
			$difference = (9 - $h) * 3600;
		} elseif ($h > 20) {
			$difference = (24 - $h + 9) * 3600;
		}
		$lifeTime = $this->numericCreated + $difference + $extraTime;
		return $lifeTime;
	}

	/**
	 * Отправляет сообщение на почту
	 */
	public function notify()
	{
		if ($this->master) {
			$newAppointmentCount = (int)$this->custom_search(self::STATUS_NEW, $this->master->id)->totalItemCount;
			$message = new ApnsPHP_Message;
			$message->setBadge($newAppointmentCount);
			$message->setSound('default');
			$message->setCustomProperty('push.id', $this->id);
			$message->setText(
				'Поступила новая заявка ' . ($this->service ? $this->service->name : $this->specialization->name)
			);
			$this->master->sendApnsMessage($message);
		}

	}

	/**
	 * @return string array
	 */
	public function getTimes()
	{
		$times = array();
		$times[5 * 60 * 60 + 30 * 60] = '5:30';
		for ($h = 6; $h < 24; $h++) {
			$times[$h * 60 * 60] = $h . ':00';
			$times[$h * 60 * 60 + 30 * 60] = $h . ':30';
		}
		$times[0] = '0:00';
		return $times;
	}

	public function getTime()
	{
		// обратная совместимость со старым вариантом записи времени
		if ($this->time) {
			$times = $this->getTimes();
			return isset($times[$this->time / 1800]) ? $times[$this->time / 1800] : null;
		}

		return $this->date ? date('H:i', $this->date) : null;
	}

	public function getDate()
	{
		return $this->date ? date('d.m.Y', $this->date) : null;
	}

	public function getFullDate()
	{
		$date = $this->getDate();
		if (!$date) {
			return null;
		}

		$time = $this->getTime();
		if ($time) {
			$date .= ' в ' . $time;
		}

		return $date;
	}

	/**
	 * @return int
	 */
	public function getNumericCreated()
	{
		return strtotime($this->created);
	}

	/**
	 * @return int
	 */
	public function getNumericChanged()
	{
		return $this->changed ? strtotime($this->changed) : null;
	}

	public function setNumericChanged($value)
	{
		if (is_numeric($value)) {
			$value = date('Y-m-d H:i:s', (int)$value);
		}
		$this->changed = $value;
		return $value;
	}

	public function touch()
	{
		$this->numericChanged = time();
		return $this;
	}

	/**
	 * Вызывается перед валидацией модели
	 *
	 * @return bool
	 */
	protected function beforeValidate()
	{
		if (
			!$this->specialization_id
			&& $this->service_id
		) {
			$service = LfService::model()->findByPk($this->service_id);
			if ($service) {
				$this->specialization_id = $service->specialization_id;
			}
		}

		return parent::beforeValidate();
	}

	/**
	 * Выполняется после сохранения модели
	 *
	 * return void
	 */
	protected function afterSave()
	{
		if ($this->isNewRecord) {
			$smsModelMaster = new Sms;
			$smsModelMaster->makeNewSmsForMasterByAppointmentId($this->id);
		}

		parent::afterSave();
	}

	/**
	 * Выполняется перед сохранением модели
	 *
	 * @return bool
	 */
	public function beforeSave()
	{
		if ($this->isNewRecord) {
			$this->touch();

			if ($this->departure && $this->address) {
				try {
					$result = Yii::app()->geocoder->geocode($this->address);
					list($this->lat, $this->lng) = $result->getCoordinates();
				} catch (Exception $e) {
					// we failed to get result
				}
			}
		}

		if ($this->phone) {
			$this->phone_numeric = preg_replace('/\D+/', '', $this->phone);
		}

		if ($this->dateDate) {
			$this->date = (int)$this->dateDate + (int)$this->dateTime;
		}

		if ($this->reasonText) {
			$this->reason = $this->reasonText;
		}

		if (!$this->service_price2) {
			$this->service_price2 = $this->service_price;
		}

		if ($this->_isReduceBalance()) {
			$this->reduceBalance(floor((int)$this->service_price2 * Yii::app()->params['appointmentCommission']));
		}

		if(!$this->isNewRecord && $this->status == self::STATUS_ACCEPTED && $this->old_status == self::STATUS_NEW)
		$this->_remove_clones();

		return parent::beforeSave();
	}

	/**
	 * Ищет заявки-клоны и удаляет их
	 */
	private function _remove_clones()
	{
		$data = LfAppointment::model()->findAll(
			'DATE(t.created) = :date AND t.phone = :phone AND t.service_id = :service_id AND id != :id',
			[
				':date'       => date('Y-m-d'),
				':phone'      => $this->phone,
				':service_id' => $this->service_id,
				':id'         => $this->id,
			]
		);
		foreach ($data as $model) {
			$model->status = self::STATUS_REMOVED;
			$model->saveAttributes(['status']);
		}
	}

	/**
	 * Проверяет, можно ли списывать баланс
	 *
	 * @return bool
	 */
	private function _isReduceBalance()
	{
		return $this->status == self::STATUS_COMPLETED
		&& $this->old_status != $this->status
		&& $this->service_price2
		&& Yii::app()->getModule('payments')->isActive();
	}

	/**
	 * This method is invoked after each record is instantiated by a find method.
	 * The default implementation raises the {@link onAfterFind} event.
	 * You may override this method to do postprocessing after each newly found record is instantiated.
	 * Make sure you call the parent implementation so that the event is raised properly.
	 */
	public function afterFind()
	{
		$this->old_status = $this->status;

		parent::afterFind();
	}

	/**
	 * Списывает баланс со счета мастера и отправляет СМС
	 *
	 * @param int  $sum       сумма списания
	 * @param bool $isSendSms флаг. Отправлять ли смс
	 *
	 * @return bool
	 */
	private function reduceBalance($sum, $isSendSms = true)
	{
		$oldBalance = $this->master->getAccount()->getAmount();
		if ($this->master) {
			if (
			$this->master->getAccount()->creditAmount(
				PaymentsAccount::model()->findByPk(PaymentsAccount::SYSTEM_ID),
				$sum,
				null,
				PaymentsOperations::TYPE_COMMISSION,
				'Завершен заказ номер №' . $this->id
			)
			) {
				if($this->master->getAccount()->getAmount() < 0 && $oldBalance > 0) {
					$this->master->sendNegativeBalance();
				}

				if ($isSendSms) {
					$sms_completed = new Sms;
					$sms_completed->makeCompletedSmsForClientByAppointmentId($this->id);
					$sms_minus_balance = new Sms;
					$sms_minus_balance->makeMinusBalanceSmsForMasterByAppointmentId($this->id, $sum);
				}
				return true;
			}
		}
		return false;
	}

	public function isNew()
	{
		return (int)$this->status === self::STATUS_NEW;
	}

	public function hasTimeout()
	{
		return in_array(
			(int)$this->status,
			array(
				self::STATUS_NEW,
				self::STATUS_PROCESSING_BY_MASTER,
			)
		);
	}

	public function isRejected()
	{
		return in_array(
			(int)$this->status,
			array(
				self::STATUS_REJECTED
			)
		);
	}

	public function isAccepted()
	{
		return (int)$this->status === self::STATUS_ACCEPTED;
	}

	public function isCompleted()
	{
		return (int)$this->status === self::STATUS_COMPLETED;
	}

	/**
	 * Цена заявки по прейскуранту
	 *
	 * @return LfPrice|null
	 */
	public function getPrice()
	{
		if (!$this->service) {
			return null;
		} elseif ($this->salon) {
			return $this->salon->getPriceForService($this->service->id);
		} elseif ($this->master) {
			return $this->master->getPriceForService($this->service->id);
		}

		return null;
	}

	/**
	 * Получает цену услуги
	 * Если указа конечная цена - то ее, если нет конечной цены - то цену услуги
	 *
	 * @return int
	 */
	public function getPriceFormatted()
	{
		if ($this->service_price2) {
			return (int)$this->service_price2;
		}

		if ($this->service_price) {
			return (int)$this->service_price;
		}

		$price = $this->getPrice();
		if ($price) {
			return (int)$price->price;
		}

		return null;
	}

	public function toArray()
	{
		return array(
			'id'             => (int)$this->id,
			'created'        => $this->numericCreated < 0 ? null : $this->numericCreated,
			'changed'        => $this->numericChanged < 0 ? null : $this->numericChanged,
			'date'           => $this->date ? (int)$this->date : null,
			//'dateFormatted' => $this->getFullDate(),
			'status'         => (int)$this->status,
			'reason'         => $this->reason,
			'specialization' => $this->specialization_id ? (int)$this->specialization_id : null,
			'service'        => $this->service_id ? (int)$this->service_id : null,
			'name'           => $this->name,
			'phone'          => $this->phone,
			'email'          => $this->email,
			'departure'      => (bool)$this->departure,
			'address'        => $this->address ?: null,
			'price'          => $this->getPriceFormatted(),
			'lat'            => (float)$this->lat,
			'lng'            => (float)$this->lng,
		);
	}

	/**
	 * Получает название услуги
	 * Если есть услуга - то ее, если нет то название сервиса
	 *
	 * @return string
	 */
	public function getServiceName()
	{
		if ($this->service_name) {
			return $this->service_name;
		} else {
			if ($this->service) {
				return $this->service->name;
			} else {
				if ($this->specialization) {
					return $this->specialization->name;
				}
			}
		}

		return null;
	}

	/**
	 * Получает отправителя для GA
	 *
	 * @param string $gaType строка для GA
	 *
	 * @return string
	 */
	public function getGaReceiver($gaType)
	{
		if (self::GA_TYPE == $gaType) {
			return "click";
		}
		$array = explode("-", $gaType);
		return $array[0];
	}

	/**
	 * Получает текст для GA
	 *
	 * @param string $gaType строка для GA
	 *
	 * @return string
	 */
	public function getGaText($gaType)
	{
		if ((self::GA_TYPE === $gaType) || !$gaType) {
			return "common";
		} else {
			if ($gaType === "wish_too") {
				return str_replace("_", " ", $gaType);
			} else {
				$array = explode("-", $gaType);
				return str_replace("_", " ", $array[1]);
			}
		}
	}

	/**
	 * Автоматическое завершение заявки
	 *
	 * @return bool
	 */
	public function automaticCompletion()
	{
		$this->status = self::STATUS_COMPLETED;
		$this->saveAttributes(array('status'));

		if ($this->service_price) {
			$sum = $this->service_price * Yii::app()->params["appointmentCommission"];
			return $this->reduceBalance($sum, false);
		}

		return false;
	}

	/**
	 * Отправляет сообщения о завершении заявки первый раз через {@link self::LONG_FIRST} часов
	 *
	 * @return array массив с идентификаторами заявок, по которым были отправлены напоминания
	 */
	public function sendRemindersForFirstTime()
	{
		$sendAppointmentIds = array();

		$criteria = new CDbCriteria;
		$criteria->condition =
			"created > :created AND date < :timeMax AND date > :timeMin AND status = :status AND is_long = :is_long";
		$criteria->params = array(
			":created" => Yii::app()->params["remindersTimeFrom"],
			":status"  => LfAppointment::STATUS_ACCEPTED,
			":timeMin" => time() - (self::LONG_SECOND - 1) * 3600,
			":timeMax" => time() - self::LONG_FIRST * 3600,
			":is_long" => self::LONG_NONE,
		);
		$appointments = LfAppointment::model()->findAll($criteria);
		if ($appointments) {
			foreach ($appointments as $model) {
				$sms = new Sms;
				if ($sms->makeSmsForOverdue($model)) {
					$model->is_long = self::LONG_FIRST;
					$model->save();
					$sendAppointmentIds[] = $model->id;
				}
			}
		}

		return $sendAppointmentIds;
	}

	/**
	 * Отправляет сообщения о завершении заявки второй раз через {@link self::LONG_SECOND} часов
	 *
	 * @return array массив с идентификаторами заявок, по которым были отправлены напоминания
	 */
	public function sendRemindersForSecondTime()
	{
		$sendAppointmentIds = array();

		$criteria = new CDbCriteria;
		$criteria->condition =
			"created > :created AND date < :timeMax AND date > :timeMin AND status = :status AND is_long < :is_long";
		$criteria->params = array(
			":created" => Yii::app()->params["remindersTimeFrom"],
			":status"  => LfAppointment::STATUS_ACCEPTED,
			":timeMin" => time() - (self::LONG_LAST - 1) * 3600,
			":timeMax" => time() - self::LONG_SECOND * 3600,
			":is_long" => self::LONG_SECOND,
		);
		$appointments = LfAppointment::model()->findAll($criteria);
		if ($appointments) {
			foreach ($appointments as $model) {
				$sms = new Sms;
				if ($sms->makeSmsForOverdue($model)) {
					$model->is_long = self::LONG_SECOND;
					$model->save();
					$sendAppointmentIds[] = $model->id;
				}
			}
		}

		return $sendAppointmentIds;
	}

	/**
	 * Отправляет сообщения о завершении заявки последний раз через {@link self::LONG_LAST} часов
	 * Завершение заявки
	 *
	 * @return array массив с идентификаторами заявок, по которым были отправлены напоминания
	 */
	public function sendRemindersForLastTime()
	{
		$sendAppointmentIds = array();

		$criteria = new CDbCriteria;
		$criteria->condition = "created > :created AND date < :time AND status = :status AND is_long < :is_long";
		$criteria->params = array(
			":created" => Yii::app()->params["remindersTimeFrom"],
			":status"  => LfAppointment::STATUS_ACCEPTED,
			":time"    => time() - self::LONG_LAST * 3600,
			":is_long" => self::LONG_LAST,
		);
		$appointments = LfAppointment::model()->findAll($criteria);
		if ($appointments) {
			foreach ($appointments as $model) {
				$sms = new Sms;
				if ($sms->makeSmsForOverdueLast($model)) {
					$model->is_long = self::LONG_LAST;
					$model->save();
					$model->automaticCompletion();
					$sendAppointmentIds[] = $model->id;
				}
			}
		}

		return $sendAppointmentIds;
	}

	/**
	 * Получает статус заявки
	 *
	 * @return string
	 */
	public function getStatus()
	{
		return $this->statusList[$this->status];
	}

	/**
	 * Возвращает иконку статуса заявки
	 */
	public function getStatusIcon()
	{

		return self::$statusIcons[$this->status];
	}

	/**
	 * Получает конечную цену
	 *
	 * @param bool $rub отображать или нет рубли
	 *
	 * @return string
	 */
	public function getServicePrice2($rub = false)
	{
		if ($this->status == self::STATUS_COMPLETED) {
			if ($this->service_price2) {
				$price = $this->service_price2;
				if ($rub) {
					$price .= "&nbsp;руб.";
				}
				return $price;
			}
		}

		return "";
	}

	/**
	 * Получает цену
	 *
	 * @param bool $rub отображать или нет рубли
	 *
	 * @return string
	 */
	public function getServicePrice($rub = false)
	{
		if ($this->service_price) {
			$price = $this->service_price;
			if ($rub) {
				$price .= "&nbsp;руб.";
			}
			return $price;
		}

		return "";
	}

	/**
	 * Получает название салона
	 *
	 * @return string
	 */
	public function getSalonName()
	{
		if ($this->salon) {
			return Yii::app()->controller->renderPartial(
				"_change",
				array(
					"controller"   => "salon",
					"id"           => $this->id,
					"rewrite_name" => $this->salon->rewrite_name,
					"fullName"     => $this->salon->name,
					'is_free'      => null,
					'model'        => null,
				),
				true
			);
		}

		return null;
	}

	/**
	 * Получает имя мастера
	 *
	 * @return string
	 */
	public function getMasterName()
	{
		if ($this->master) {
			return Yii::app()->controller->renderPartial(
				"_change",
				array(
					"controller"   => "master",
					"id"           => $this->id,
					"rewrite_name" => $this->master->rewrite_name,
					"fullName"     => $this->master->getFullName(),
					'is_free'      => $this->master->is_free,
					'model'        => $this->master
				),
				true
			);
		}
		return null;
	}

	/**
	 * Переводит заявку на другого мастера или салон
	 *
	 * @param integer $master_id
	 * @param integer $salon_id
	 *
	 * @return bool
	 */
	public function change($master_id, $salon_id)
	{
		$transaction = $this->dbConnection->beginTransaction();

		$modelNew = clone $this;
		$this->status = self::STATUS_REJECTED;

		if ($this->save(false)) {
			$criteria = new CDbCriteria;
			$criteria->condition = "service_id = :service_id";
			$criteria->params = array(
				":service_id" => $this->service_id,
			);

			if ($master_id != null) {
				$modelNew->master_id = $master_id;
				$modelNew->salon_id = null;
				$criteria->condition .= " AND master_id = :id";
				$criteria->params[':id'] = $master_id;
			} elseif ($salon_id != null) {
				$modelNew->salon_id = $salon_id;
				$modelNew->master_id = null;
				$criteria->condition .= " AND salon_id = :id";
				$criteria->params[':id'] = $salon_id;
			} else {
				return false;
			}

			$priceModel = LfPrice::model()->find($criteria);
			if ($priceModel) {
				$modelNew->service_price = $priceModel->price;
			}

			$modelNew->created = null;
			$modelNew->status = self::STATUS_NEW;
			$modelNew->id = null;
			$modelNew->isNewRecord = true;
			if ($modelNew->save(false)) {
				$sms_model_client = new Sms;
				$sms_model_client->makeNewSmsForMasterByAdminAndAppointmentId($modelNew->id);

				$transaction->commit();
				return true;
			}
		}

		$transaction->rollback();
		return false;
	}

	/**
	 * Получает телефон салона
	 *
	 * @return string
	 */
	public function getSalonPhone()
	{
		if ($this->salon) {
			return $this->salon->phone;
		}

		return "";
	}

	/**
	 * Получает телефон мастера
	 *
	 * @return string
	 */
	public function getMasterPhone()
	{
		if ($this->master) {
			return $this->master->phone_cell;
		}

		return "";
	}

	/**
	 * Получает дату создания
	 *
	 * @return string
	 */
	public function getCreated()
	{
		if ($this->created) {
			$timestamp = CDateTimeParser::parse($this->created, 'yyyy-MM-dd HH:mm:ss');
			if ($timestamp) {
				return
					"<strong>" .
					date("H:i", $timestamp) .
					"</strong> " .
					date("d.m.y", $timestamp);
			}
		}

		return "не указано";
	}

	/**
	 * Определяет, первая ли заявка у мастера
	 *
	 * @return bool
	 */
	public function isFirst()
	{
		$criteria = new CDbCriteria;
		$criteria->condition = "master_id = :master_id";
		$criteria->params[":master_id"] = $this->master_id;

		return ($this->count($criteria) <= 1);
	}

	/**
	 * Получает первую заявку по идентификатору мастера
	 *
	 * @param int $masterId идентификатор мастера
	 *
	 * @return self
	 */
	public function getFirstByMasterId($masterId)
	{
		$criteria = new CDbCriteria;
		$criteria->condition = "t.master_id = :master_id";
		$criteria->params[":master_id"] = $masterId;
		$criteria->order = "t.created";

		return $this->find($criteria);
	}

	/**
	 * Определяет, старше ли одного дня заявка
	 *
	 * @return bool
	 */
	public function isOlderThanOneday()
	{
		return time() > CDateTimeParser::parse($this->created, "yyyy-MM-dd HH:mm:ss") + 24 * 3600;
	}

	/**
	 * Создает СМС после принятия заявки
	 *
	 * @return void
	 */
	public function makeAcceptedSms()
	{
		$smsModelClient = new Sms;
		$smsModelClient->makeAcceptedSmsForClientByAppointmentId($this->id);
		$smsModelMaster = new Sms;
		$smsModelMaster->makeAcceptedSmsForMasterByAppointmentId($this->id);
		$smsModelMaster = new Sms;
		$smsModelMaster->makeDelayedAcceptedSmsForMasterByAppointmentId($this->id);
		$smsModelMaster = new Sms;
		$smsModelMaster->makeDelayedAcceptedSmsForClientByAppointmentId($this->id);

		if ($this->isFirst()) {
			$smsModelMasterFirst = new Sms;
			$smsModelMasterFirst->makeAcceptedFirstSms($this);
		}
	}

	/**
	 * Получает имя контролера
	 *
	 * @return string
	 */
	public function getAdminName()
	{
		if ($this->admin_id) {
			return $this->admin->name;
		}

		return null;
	}

	/**
	 * Сделать ли редактором заявки текущего оператора
	 *
	 * @return bool
	 */
	public function isCurrentAdmin()
	{
		return ($this->isNewRecord || !$this->admin_id) && AdminModel::getModel()->isOperator();
	}

	/**
	 * Проверяет, отправить ли смс клиенту при смене мастера
	 *
	 * @param array|null $oldAttributes
	 *
	 * @return bool
	 */
	public function checkSendClientSms($oldAttributes)
	{
		if ($oldAttributes != null && $this->date != null && $oldAttributes['master_id'] != $this->master_id) {
			return true;
		}
		return false;
	}
}