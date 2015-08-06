<?php

class Operation extends CActiveRecord
{

	public function tableName()
	{
		return 'operation';
	}

	public function rules()
	{
		return array(
			array('user_to, user_from, sum', 'required'),
			array('user_to, user_from', 'numerical', 'integerOnly'=>true),
			array('sum', 'numerical'),
			array('id, user_to, user_from, date', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
			'userFrom' => array(self::BELONGS_TO, 'User', 'user_from'),
			'userTo' => array(self::BELONGS_TO, 'User', 'user_to'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user_to' => 'Кому',
			'user_from' => 'От кого',
			'sum' => 'Сумма',
			'date' => 'Дата',
		);
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('user_to',$this->user_to);
		$criteria->compare('user_from',$this->user_from);
		$criteria->compare('sum',$this->sum);
		$criteria->compare('date',$this->date,true);

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

	public function addMoney($userId, $sum, $count = 0)
	{
		$percent = 0;

		if ($count > 8) {
			return false;
		}

		switch ($count) {
			case 0:
				$percent = 10;
				break;
			case 1:
				$percent = 5;
				break;
			case 2:
				$percent = 5;
				break;
			case 3:
				$percent = 5;
				break;
			case 4:
				$percent = 5;
				break;
			case 5:
				$percent = 5;
				break;
			case 6:
				$percent = 5;
				break;
			case 7:
				$percent = 5;
				break;
			case 8:
				$percent = 5;
				break;
		}

		$model = User::model()->findByPk($userId);
		if (!$model || !$model->parent_id) {
			return false;
		}

		$pModel = User::model()->findByPk($model->parent_id);
		if (!$pModel) {
			return false;
		}

		$s = (float) $sum * $percent / 100;
		$pModel->balance_personal = $pModel->balance_personal + $s * (100 - SHOP_PERCENT) / 100;
		$pModel->balance_shop = $pModel->balance_shop + $s * SHOP_PERCENT / 100;
		if (!$pModel->save()) {
			return false;
		}

		$tModel = new self;
		$tModel->user_to = $pModel->id;
		$tModel->user_from = $model->id;
		$tModel->sum = $s;
		if (!$tModel->save()) {
			return false;
		}

		$count++;
		$this->addMoney($pModel->id, $sum, $count);
	}

	public function getDate()
	{
		return date("d.m.Y H:i", CDateTimeParser::parse($this->date, "yyyy-MM-dd HH:mm:ss"));
	}
}
