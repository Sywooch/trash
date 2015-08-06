<?php
use likefifa\models\LoginHistory;

/**
 * This is the model class for table "lf_opinion".
 *
 * The followings are the available columns in table 'lf_opinion':
 *
 * @property integer       $id
 * @property integer       $allowed
 * @property integer       $master_id
 * @property integer       $rating
 * @property string        $name
 * @property string        $email
 * @property string        $text
 * @property integer       $created
 * @property integer       $salon_id
 * @property string        $tel
 * @property string        $advantages
 * @property string        $disadvantages
 * @property integer       $quality
 * @property integer       $ratio
 * @property integer       $is_more
 * @property integer       $yes
 * @property integer       $no
 * @property string        $ip
 * @property string        $sid
 * @property string        $ua
 * @property string        $ga            кука Google Analytics
 * @property integer       $appointment_id
 * @property integer       $warning_level помечает, является ли отзыв подозрительным
 *
 * The followings are the available model relations:
 * @property LfMaster      $master
 * @property LfSalon       $salon
 * @property LfAppointment $appointment
 *
 */
class LfOpinion extends CActiveRecord
{
	/**
	 * Отзыв не подозрителен
	 */
	const WARNING_NONE = 0;

	/**
	 * Отзыв возможно подозрителен
	 */
	const WARNING_POSSIBLE = 1;

	/**
	 * Отзыв очень подозрителен
	 */
	const WARNING_TOP = 2;

	protected $allowedNames = [
		-1 => 'Заблокирован',
		0  => 'Новый',
		1  => 'Опубликован',
	];

	/**
	 * Значения цена/качество
	 *
	 * @var array
	 */
	private $ratioValues = array(
		0 => "Нет ответа",
		1 => "Непонятно, за что можно было отдать такие деньги",
		2 => "Это должно стоить дешевле",
		3 => "Цена немного завышена",
		4 => "Это стоило того!",
		5 => "За такое никаких денег не жалко!",
	);

	/**
	 * Значения результата
	 *
	 * @var array
	 */
	private $ratingValues = array(
		0 => "Нет ответа",
		1 => "Это было ужасно",
		2 => "Я недовольна результатом",
		3 => "Так себе",
		4 => "Я довольна результатом",
		5 => "Восхитительно! Лучше не бывает!",
	);

	/**
	 * Значения качества обслуживания
	 *
	 * @var array
	 */
	private $qualityValues = array(
		0 => "Нет ответа",
		1 => "Ужасное обслуживание!",
		2 => "Могло бы быть и лучше",
		3 => "В целом нормально",
		4 => "Я всем довольна",
		5 => "Я в восторге! Мастер – прелесть!",
	);

	/**
	 * Получает значение цена/качество
	 *
	 * @param int $key ключ массива $this->ratioValues
	 *
	 * @return string
	 */
	public function getRatioValue($key)
	{
		return $this->ratioValues[$key];
	}

	/**
	 * Получает значение результата
	 *
	 * @param int $key ключ массива $this->ratingValues
	 *
	 * @return string
	 */
	public function getRatingValue($key)
	{
		return $this->ratingValues[$key];
	}

	/**
	 * Получает значение качества обслуживания
	 *
	 * @param int $key ключ массива $this->qualityValues
	 *
	 * @return string
	 */
	public function getQualityValue($key)
	{
		return $this->qualityValues[$key];
	}

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className active record class name.
	 *
	 * @return LfOpinion the static model class
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
		return 'lf_opinion';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('name, tel, text', 'required'),
			array(
				'allowed, master_id, created, salon_id, rating, quality, ratio, is_more, yes, no, appointment_id, warning_level',
				'numerical',
				'integerOnly' => true
			),
			array('name, email, tel, ip, sid, ua', 'length', 'max' => 256),
			array('text, advantages, disadvantages, meta_keywords, meta_description, ga', 'safe'),
			array('id, allowed, master_id, salon_id, rating, name, email, text', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'master'      => array(self::BELONGS_TO, 'LfMaster', 'master_id'),
			'salon'       => array(self::BELONGS_TO, 'LfSalon', 'salon_id'),
			'appointment' => array(self::BELONGS_TO, 'LfAppointment', 'appointment_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'             => 'ID',
			'allowed'        => 'Опубликован',
			'master_id'      => 'Мастер',
			'salon_id'       => 'Салон',
			'rating'         => 'Результат',
			'quality'        => 'Качество',
			'ratio'          => 'Соотношение',
			'name'           => 'Имя',
			'email'          => 'Email',
			'text'           => 'Отзыв',
			'tel'            => 'Телефон',
			'advantages'     => 'Достоинства',
			'disadvantages'  => 'Недостатки',
			'is_more'        => 'Пойдет еще',
			'yes'            => 'Да',
			'no'             => 'Нет',
			'created'        => 'Дата создания',
			'ip'             => 'IP',
			'sid'            => 'SID',
			'ua'             => 'UA',
			'appointment_id' => 'Заявка',
		);
	}

	public function behaviors()
	{
		return array(
			'CArModTimeBehavior' => array(
				'class' => 'application.extensions.CArModTimeBehavior',
			)
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('allowed', $this->allowed);
		$criteria->compare('master_id', $this->master_id);
		$criteria->compare('salon_id', $this->salon_id);
		//$criteria->compare('rating',$this->rating);
		$criteria->compare('name', $this->name, true);
		$criteria->compare('email', $this->email, true);
		$criteria->compare('text', $this->text, true);
		$criteria->compare('ip', $this->ip, true);
		$criteria->compare('sid', $this->sid, true);
		$criteria->compare('ua', $this->ua, true);
		$criteria->compare('appointment_id', $this->appointment_id);
		$criteria->order = 't.id DESC';

		return new CActiveDataProvider($this, array(
			'criteria'   => $criteria,
			'pagination' => array(
				'pageSize' => 50,
			),
		));
	}

	public function getCreated()
	{
		return date('d.m.Y H:i:s', $this->created);
	}

	/**
	 * Получает дату создания отзыва в формте дд.мм.гггг
	 *
	 * @return string
	 */
	public function getDate()
	{
		if ($this->created) {
			return date("d.m.Y", $this->created);
		}
		return null;
	}

	/**
	 * Получает время создания отзыва в формте чч:мм
	 *
	 * @return string
	 */
	public function getTime()
	{
		if ($this->created) {
			return date("H:i", $this->created);
		}
		return null;
	}

	public function getAllowed()
	{
		return $this->allowedNames[$this->allowed];
	}

	public function getAllowedListItems()
	{
		return $this->allowedNames;
	}

	/**
	 * Вызывается перед сохранением модели
	 *
	 * @return bool
	 */
	protected function beforeSave()
	{
		if ($this->isNewRecord) {
			$this->ip = $_SERVER["REMOTE_ADDR"];
			$this->sid = session_id();
			$this->ua = $_SERVER['HTTP_USER_AGENT'];

			$appointment = $this->findLinkedAppointment();
			if ($appointment) {
				$this->appointment_id = $appointment->id;
			}

			$this->warning_level = $this->checkWarning();
		}

		return parent::beforeSave();
	}

	/**
	 * Проверяет, является ли письмо подозрительным
	 *
	 * @return integer
	 */
	public function checkWarning()
	{
		$warning = self::WARNING_NONE;

		// Если ID юзера совпадает с ID мастера - однозначно фейк
		if ($this->master_id && Yii::app()->user->isGuest == false && Yii::app()->user->id == $this->master_id) {
			return self::WARNING_TOP;
		}

		$warningCriteria = new CDbCriteria;

		$warningCriteria->addCondition('t.ga = :ga OR (t.ip = :ip AND t.ua = :ua)');
		$warningCriteria->params = [
			':ga' => $this->ga,
			':ip' => $this->ip,
			':ua' => $this->ua,
		];

		// Проверяем первый уровень (повторный комментарий)
		$criteria = clone $warningCriteria;
		if (!$this->isNewRecord) {
			$criteria->addCondition('t.id != :id');
			$criteria->params[':id'] = $this->id;
		}

		if ($this->salon_id != null) {
			$criteria->addCondition('t.salon_id = :salon_id');
			$criteria->params[':salon_id'] = $this->salon_id;
		}

		if ($this->master_id != null) {
			$criteria->addCondition('t.master_id = :master_id');
			$criteria->params[':master_id'] = $this->master_id;
		}
		if (self::model()->count($criteria) > 0) {
			$warning = self::WARNING_POSSIBLE;
		}

		// Проверяем второй уровень (была авторизация такого же мастера)
		$criteria = clone $warningCriteria;

		if ($this->salon_id != null) {
			$criteria->addCondition('t.type = :type and t.entity_id = :entity_id');
			$criteria->params[':entity_id'] = $this->salon_id;
			$criteria->params[':type'] = LoginHistory::TYPE_SALON;
		}

		if ($this->master_id != null) {
			$criteria->addCondition('t.type = :type and t.entity_id = :entity_id');
			$criteria->params[':entity_id'] = $this->master_id;
			$criteria->params[':type'] = LoginHistory::TYPE_MASTER;
		}
		if (LoginHistory::model()->count($criteria) > 0) {
			$warning = self::WARNING_TOP;
		}

		return $warning;
	}

	/**
	 * Пытается найти заявку, которая может быть связана с отзывом
	 *
	 * @return LfAppointment
	 */
	protected function findLinkedAppointment()
	{
		$criteria = new CDbCriteria;
		$criteria->addCondition('t.phone = :phone');
		$criteria->params = array(":phone" => $this->tel);

		if ($this->master_id) {
			$criteria->addCondition('t.master_id = :master_id');
			$criteria->params[':master_id'] = $this->master_id;
		} else {
			if ($this->salon_id) {
				$criteria->addCondition('t.salon_id = :salon_id');
				$criteria->params[':salon_id'] = $this->salon_id;
			}
		}

		$criteria->order = "t.id DESC";
		return LfAppointment::model()->find($criteria);
	}
}