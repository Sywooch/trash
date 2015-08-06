<?php

namespace dfs\modules\sms\models;

use CActiveRecord;
use dfs\modules\payments\PaymentsModule;
use Yii;
use CDbCriteria;
use CActiveDataProvider;
use CException;

/**
 * Абстрактный класс для работы с SMS
 *
 * @property integer $id
 * @property integer $number
 * @property integer $send_time
 * @property integer $status
 * @property string  $message
 *
 * @method Sms[] findAll
 *
 */
class Sms extends CActiveRecord
{
	/**
	 * Новое сообщение
	 *
	 * @var integer
	 */
	const STATUS_NEW = 0;

	/**
	 * Сообщение отправленно
	 *
	 * @var integer
	 */
	const STATUS_SENT = 1;

	/**
	 * Не удалось отпарвитьс общение
	 *
	 * @var integer
	 */
	const STATUS_FAILED = 2;

	/**
	 * Сообщение проигнорированно
	 *
	 * @var integer
	 */
	const STATUS_IGNORED = 3;

	/**
	 * Сообщение просрочено
	 *
	 * @var integer
	 */
	const STATUS_TIMEOUT = 4;

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className active record class name.
	 *
	 * @return Sms the static model class
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
		return 'sms';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('number, send_time', 'required'),
			array('status, send_time', 'numerical', 'integerOnly' => true),
			array('number', 'length', 'max' => 12),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, number, send_time', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array();
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'        => 'ID',
			'number'    => 'Телефон',
			'send_time' => 'Время отправки',
			'status'    => 'Статус отправки',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * @return \CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('number', $this->number);
		$criteria->compare('send_time', $this->send_time);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}

	/**
	 * Устанавливает время с 9 до 21
	 *
	 * @param bool|int $time время
	 *
	 * @return int время для отправки СМС
	 */
	protected function getDayTime($time = false)
	{
		if (!$time) {
			$time = time();
		}
		$h = date("H", $time);
		$m = date("i", $time);
		if ($h < 9) {
			$time += (9 - $h) * 3600 - $m * 60;
		} elseif ($h > 20) {
			$time += (24 - $h + 9) * 3600 - $m * 60;
		}
		return $time;
	}

	/**
	 * Удаляет из номера телефона все лишние символы
	 *
	 * @param string $phone_cell неотформатированный номер телефона
	 *
	 * @return string номер телефона (79109100000)
	 */
	protected function getFormatPhone($phone_cell)
	{
		return (string)preg_replace('/[^0-9]/', '', $phone_cell);
	}

	/**
	 * Сохраняет SMS в БД
	 *
	 * @param string   $destination_address телефон, куда отправлять
	 * @param string   $message             текст сообщения
	 * @param bool|int $send_time           время отправки СМС
	 *
	 * @return bool результат операции
	 */
	protected function saveSms($destination_address = "", $message = '', $send_time = false)
	{
		if ($destination_address) {
			$sms_db_count =
				Yii::app()->db->createCommand()->select('COUNT(id)')->from('sms')->where(
					'number=:number AND send_time>:one_hour',
					array(':number' => $destination_address, ':one_hour' => time() - 3600)
				)->queryRow();
			if ($message) {
				$this->number = $destination_address;
				$this->send_time = $send_time;
				$this->message = $message;
				if (!$sms_db_count || $sms_db_count['COUNT(id)'] < 10) {
					$this->status = self::STATUS_NEW;
				} else {
					$this->status = self::STATUS_IGNORED;
				}
				return $this->save();
			}
		}
		return false;
	}

	/**
	 * Если отключена монитизация, СМС не сохраняется в базу
	 *
	 * @param array $attributes list of attributes that need to be saved. Defaults to null,
	 *                          meaning all attributes that are loaded from DB will be saved.
	 *
	 * @return boolean whether the attributes are valid and the record is inserted successfully.
	 * @throws CException if the record is not new
	 */
	public function insert($attributes = null)
	{
		/**
		 * @var PaymentsModule $payments
		 */
		$payments = Yii::app()->getModule('payments');
		if (!$payments->isActive()) {
			return true;
		}

		return parent::insert($attributes);
	}

	/**
	 * Проверяем можно ли отправлять СМС или нет
	 *
	 * @return bool
	 */
	public function canSend()
	{
		// Если белый список пустой - отправляем всем.
		if (empty(Yii::app()->params['devPhones'])) {
			return true;
		}

		// Если отсутствует в разрешённых, не отправляем
		if (!in_array($this->number, Yii::app()->params['devPhones'])) {
			return false;
		}

		return true;
	}
}