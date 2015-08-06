<?php

/**
 * This is the model class for table "payment_money".
 *
 * The followings are the available columns in table 'payment_money':
 *
 * @property integer $id
 * @property integer $user_id
 * @property float $withdrawal
 * @property string  $text
 * @property string  $date
 *
 * The followings are the available model relations:
 * @property User    $user
 */
class PaymentMoney extends CActiveRecord
{

	public $yesNoFlags = array(
		0 => "Нет",
		1 => "Да"
	);

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'payment_money';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, withdrawal', 'required'),
			array('user_id, is_read', 'numerical', 'integerOnly' => true),
			array('withdrawal', 'numerical'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, user_id, withdrawal, text, date, is_read', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'user' => array(self::BELONGS_TO, 'User', 'user_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'         => 'ID',
			'user_id'    => 'Пользователь',
			'withdrawal' => 'Сумма снятия',
			'text'       => 'Реквизиты',
			'date'       => 'Дата',
			'is_read'    => 'Обработано',
			'balance_personal' => 'Текущий баланс',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('user_id', $this->user_id);
		$criteria->compare('withdrawal', $this->withdrawal);
		$criteria->compare('text', $this->text, true);
		$criteria->compare('date', $this->date, true);
		$criteria->compare('is_read', $this->is_read);

		return new CActiveDataProvider(
			$this, array(
				'criteria' => $criteria,
			)
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 *
	 * @param string $className active record class name.
	 *
	 * @return PaymentMoney the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	public function getDate()
	{
		return date("d.m.Y H:i", CDateTimeParser::parse($this->date, "yyyy-MM-dd HH:mm:ss"));
	}

	public function getCountNotRead()
	{
		$criteria = new CDbCriteria;
		$criteria->condition = "t.is_read = :is_read";
		$criteria->params["is_read"] = 0;
		$count = count($this->findAll($criteria));
		if ($count) {
			return " ({$count})";
		}

		return "";
	}

	public function getReadFlag()
	{
		if (empty($this->yesNoFlags[$this->is_read])) {
			return null;
		}

		return $this->yesNoFlags[$this->is_read];
	}

	public function beforeDelete()
	{
		if ($this->is_read) {
			throw new CHttpException(500, "Невозможно удалить обработанную заявку");
		}

		return parent::beforeDelete();
	}
}
