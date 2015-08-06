<?php
namespace dfs\docdoc\models;

/**
 * This is the model class for table "queue".
 *
 * @property integer $SIP
 * @property string $startTime
 * @property integer $user_id
 * @property string $asteriskPool
 * @property integer $status
 *
 * @property SipChannelModel $channel
 *
 * @method QueueModel findByPk
 * @method QueueModel find
 * @method QueueModel[] findAll
 *
 */
class QueueModel extends \CActiveRecord
{

	const STATUS_UNREGISTERED = 0;
	const STATUS_REGISTERED = 1;

	const QUEUE_DEFAULT = self::QUEUE_CALLCENTER;
	const QUEUE_CALLCENTER = 'callcenter';
	const QUEUE_PARTNER = 'partnerq';
	const QUEUE_OPINION = 'opinionq';
	const QUEUE_TEST = 'testq';
	const QUEUE_TRASH = 'trashq';

	/**
	 * Названия очередей
	 *
	 * @var array
	 */
	protected static $_queueNames = [
		self::QUEUE_CALLCENTER => 'Колцентр DocDoc',
		self::QUEUE_PARTNER => 'Колцентр Партрёры',
		self::QUEUE_OPINION => 'Сборщик Отзывов',
		self::QUEUE_TEST    => 'Тестовая очередь',
		self::QUEUE_TRASH   => 'Трешовая очередь',
	];

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return QueueModel the static model class
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
		return 'queue';
	}

	/**
	 * Первичный ключ
	 * @return string
	 */
	public function primaryKey()
	{
		return 'SIP';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('SIP, user_id',
				'numerical',
				'integerOnly' => true,
			),
			array('SIP, user_id', 'unique'),
			['asteriskPool', 'safe'],
		);
	}

	/**
	 * Отношения
	 *
	 * @return array
	 */
	public function relations()
	{
		return [
			'channel' => [self::BELONGS_TO, SipChannelModel::class, 'SIP'],
		];
	}

	/**
	 * Получение названия очереди
	 *
	 * @return string
	 */
	public function getQueueName()
	{
		return $this->asteriskPool;
	}

	/**
	 * Регистрация оператора в очереди
	 *
	 * @param string $asteriskPool
	 * @param integer $sip
	 * @param integer $user
	 *
	 * @return QueueModel|null
	 */
	public function register($asteriskPool, $sip, $user)
	{
		$queue = QueueModel::model()->findByPk($sip);

		if (is_null($queue)) {
			return null;
		}

		$queue->user_id = $user;
		$queue->status = self::STATUS_REGISTERED;
		$queue->asteriskPool = $asteriskPool;

		return ($queue->save()) ? $queue : null;
	}

	/**
	 * Выход оператора из очереди
	 *
	 * @return QueueModel|null
	 */
	public function unregister()
	{
		$this->status = self::STATUS_UNREGISTERED;
		$this->user_id = null;

		return ($this->save()) ? $this : null;
	}

	/**
	 * Поиск по пользователю
	 *
	 * @param $user
	 *
	 * @return $this
	 */
	public function byUser($user)
	{
		$this->getDbCriteria()->mergeWith(array(
			'condition' => "user_id = :user_id",
			'params'    => array(':user_id' => $user),
		));
		return $this;
	}

	/**
	 * Только зарегистрированный
	 *
	 * @return $this
	 */
	public function registered()
	{
		$this->getDbCriteria()->mergeWith(array(
			'condition' => "status = :status AND user_id IS NOT NULL",
			'params'    => array(':status' => self::STATUS_REGISTERED),
		));
		return $this;
	}

	/**
	 * выполнить перед сохранением
	 *
	 * @return bool
	 */
	public function beforeSave()
	{
		// Устанавливаем время регистрации в очереди или выхода из нее
		$this->startTime = new \CDbExpression('NOW()');

		return true;
	}

	/**
	 * Получение SIP по идентификатору пользователя
	 *
	 * @param $user
	 *
	 * @return mixed|null|void
	 */
	public static function getSIPByUser($user)
	{
		$queue = QueueModel::model()->findByAttributes(array('user_id' => $user));

		return $queue ? $queue->SIP : null;
	}

	/**
	 * Получение списка всех каналов
	 *
	 * @return array
	 */
	public static function getSIPChannels()
	{
		$data = array();

		$items = self::model()->findAll();
		foreach ($items as $item) {
			$data[] = $item['SIP'];
		}

		return $data;
	}

	/**
	 * Получение названий очередей
	 *
	 * @return array
	 */
	public static function getQueueNames()
	{
		return self::$_queueNames;
	}

	/**
	 * Получение названия очереди
	 */
	public function getName()
	{
		return isset(self::$_queueNames[$this->asteriskPool]) ? self::$_queueNames[$this->asteriskPool] : '';
	}

}