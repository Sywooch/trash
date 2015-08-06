<?php

class Payment extends CActiveRecord
{

	public function tableName()
	{
		return 'payment';
	}

	public function rules()
	{
		return array(
			array('user_id, sum, date_from, date_to, discount', 'required'),
			array('user_id', 'numerical', 'integerOnly'=>true),
			array('sum', 'numerical'),
			array('date_from, date_to', 'date', 'format'=>'yyyy-MM-dd HH:mm:ss', 'allowEmpty'=>false),
			array('id, user_id, sum, date, date_from, date_to, discount', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
			'user' => array(self::BELONGS_TO, 'User', 'user_id'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user_id' => 'ID пользователя',
			'sum' => 'Сумма',
			'date' => 'Дата платежа',
			'discount' => 'Скидка',
			'date_from' => 'С',
			'date_to' => 'По',
		);
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('sum',$this->sum);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('date_from',$this->date_from,true);
		$criteria->compare('date_to',$this->date_to,true);
		$criteria->compare('discount',$this->discount);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize' => 50,
			),
		));
	}

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	protected function afterSave()
	{
		parent::afterSave();

		$user = $this->user;
		if ($user) {
			$user->updateParentId();

			Operation::model()->addMoney($this->user_id, $this->sum);
		}
	}

	public function getNewModel($userId, $sum)
	{
		$model = new self;
		$model->user_id = $userId;
		$model->sum = $sum;

		$discount = 15;
		$monthPlus = 12;
		$model->discount = $discount;

		$criteria = new CDbCriteria;
		$criteria->condition = "t.user_id = :userId";
		$criteria->params["userId"] = $userId;
		$criteria->order = "t.id DESC";
		$lastModel = $this->find($criteria);
		if ($lastModel) {
			$from = CDateTimeParser::parse($lastModel->date_to, "yyyy-MM-dd HH:mm:ss");
		} else {
			$from = time();
		}

		$model->date_from = date("Y-m-d H:i:s", $from);
		$model->date_to = date("Y-m-d H:i:s", $from + $monthPlus * 30 * 24 * 60 * 60);

		return $model;
	}

	public function getDate()
	{
		return date("d.m.Y H:i", CDateTimeParser::parse($this->date, "yyyy-MM-dd HH:mm:ss"));
	}
}