<?php

class User extends CActiveRecord
{

	public $yesNoFlags = array(
		0 => "Нет",
		1 => "Да"
	);

	const MAX_CHILDS = 3;

	public $remember;
	public $balanceAdd = 0;

	public $balanceList = array(
		30 => "30$ за 1 квартал (3 месяца). Скидна 5% на все товары.",
		60 => "60$ за 2 квартала (6 месяцев). Скидна 10% на все товары.",
		90 => "90$ за 3 квартала (9 месяцев). Скидна 15% на все товары.",
	);

	public $balanceMonthList = array(
		30 => 3,
		60 => 6,
		90 => 9,
	);

	public function tableName()
	{
		return 'user';
	}

	public function rules()
	{
		return array(
			array('email, password, name', 'required'),
			array('parent_id, is_run_out_childs, is_active', 'numerical', 'integerOnly' => true),
			array('balance_personal, balance_shop', 'numerical'),
			array('email, name', 'length', 'max' => 128),
			array('password', 'length', 'max' => 40),
			array('skype, city', 'length', 'max' => 64),
			array('phone', 'length', 'max' => 20),
			array(
				'id, email, password, name, skype, phone, city, balance, parent_id, created, balance_personal, balance_shop',
				'safe',
				'on' => 'search'
			),
			array('email', 'unique', 'message' => 'Пользователь с таким E-mail адресом уже существует.'),
			array('email', "email"),
		);
	}

	public function relations()
	{
		return array(
			'payments'   => array(self::HAS_MANY, 'Payment', 'user_id'),
			'operations' => array(self::HAS_MANY, 'Operation', 'user_to'),
			'operationsFrom' => array(self::HAS_MANY, 'Operation', 'user_from'),
			'paymentsMoney'   => array(self::HAS_MANY, 'PaymentMoney', 'user_id'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'id'                => 'ID',
			'email'             => 'E-mail',
			'password'          => 'Пароль',
			'name'              => 'Имя',
			'skype'             => 'Skype',
			'phone'             => 'Телефон',
			'city'              => 'Город',
			'balance_personal'  => 'личные $',
			'balance_shop'      => 'на покупки $',
			'parent_id'         => 'Вышестоящее звено',
			'remember'          => 'Запомнить',
			'created'           => "Дата создания",
			'is_run_out_childs' => 'Может иметь потомков',
			'is_active'         => 'Активирован',
		);
	}

	public function search()
	{
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('email', $this->email, true);
		$criteria->compare('password', $this->password, true);
		$criteria->compare('name', $this->name, true);
		$criteria->compare('skype', $this->skype, true);
		$criteria->compare('phone', $this->phone, true);
		$criteria->compare('city', $this->city, true);
		$criteria->compare('balance_personal', $this->balance_personal);
		$criteria->compare('balance_shop', $this->balance_shop);
		$criteria->compare('parent_id', $this->parent_id);
		$criteria->compare('is_run_out_childs', $this->is_run_out_childs);
		$criteria->compare('is_active', $this->is_active);

		return new CActiveDataProvider(
			$this, array(
				'criteria'   => $criteria,
				'pagination' => array(
					'pageSize' => 50,
				),
			)
		);
	}

	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	public function makePassword()
	{
		$chars = "qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP";
		$max = 10;
		$size = strLen($chars) - 1;
		$password = null;
		while ($max--) {
			$password .= $chars[rand(0, $size)];
		}

		return $password;
	}

	function sendFirstMail($password)
	{
		require_once(__DIR__ . "/../vendors/phpmailer/class.phpmailer.php");

		$text = "
			<p>{$this->name}, Вы успешно зарегистрировались на сайте!</p>
			<p>&nbsp;</p>
			<p>
				Данные для входа в личный кабинет:
				<br />E-mail: {$this->email}
				<br />Пароль: {$password}
			</p>
		";

		$mail = new PHPMailer();
		$mail->From = EMAIL_FROM;
		$mail->FromName = "Квартал 500";
		$mail->AddAddress($this->email, $this->email);
		$mail->IsHTML(true);
		$mail->Subject = "Регистрация";
		$mail->Body = $text;

		return $mail->Send();
	}

	function sendRecoveryMail($password)
	{
		require_once(__DIR__ . "/../vendors/phpmailer/class.phpmailer.php");

		$text = "
			<p>
				Данные для входа в личный кабинет:
				<br />E-mail: {$this->email}
				<br />Пароль: {$password}
			</p>
		";

		$mail = new PHPMailer();
		$mail->From = EMAIL_FROM;
		$mail->FromName = "Квартал 500";
		$mail->AddAddress($this->email, $this->email);
		$mail->IsHTML(true);
		$mail->Subject = "Восстановление пароля";
		$mail->Body = $text;

		return $mail->Send();
	}

	public function getUserId()
	{
		if (Yii::app()->user->isGuest) {
			return 0;
		}

		$criteria = new CDbCriteria();
		$criteria->condition = "t.email = :email";
		$criteria->params["email"] = Yii::app()->user->name;

		$model = User::model()->find($criteria);
		if (!$model) {
			return 0;
		}

		return $model->id;
	}

	public function isPayments()
	{
		if ($this->payments) {
			return true;
		}

		return false;
	}

	public function getDiscount()
	{
		if (!$this->payments) {
			return 0;
		}

		$discount = 0;
		foreach ($this->payments as $payment) {
			if ($payment->discount > $discount) {
				$discount = $payment->discount;
			}
		}

		return $discount;
	}

	public function getStatus()
	{
		$criteria = new CDbCriteria;
		$criteria->condition = "t.user_id = :user_id";
		$criteria->params["user_id"] = $this->id;
		$criteria->order = "t.date_to DESC";
		$model = Payment::model()->find($criteria);
		if (!$model) {
			return false;
		}

		$to = CDateTimeParser::parse($model->date_to, "yyyy-MM-dd HH:mm:ss");
		if ($to < time()) {
			return false;
		}

		return date("d.m.Y", $to);
	}

	public static function login($post = array())
	{
		$identity = new UserIdentity($post["email"], $post["password"]);
		if (!$identity->authenticateUser()) {
			return $identity->errorClass;
		}

		$remember = false;
		if ($post["remember"]) {
			$remember = 60 * 60 * 24 * 30;
		}
		Yii::app()->user->login($identity, $remember);

		return null;
	}

	public function getTreeHtml($parentId = 0)
	{
		$html = "";

		if (!$parentId) {
			$parentId = $this->id;
		}

		$criteria = new CDbCriteria;
		$criteria->condition = "t.parent_id = :parent_id AND t.is_active = :is_active";
		$criteria->params["parent_id"] = $parentId;
		$criteria->params["is_active"] = 1;

		$childs = $this->findAll($criteria);
		if ($childs) {
			$html .= "<ul>";
			foreach ($childs as $child) {
				$html .= "<li>";
				$html .= "{$child->name} (ID <strong>{$child->id}</strong>)";
				$html .= $this->getTreeHtml($child->id);
				$html .= "</li>";
			}

			$html .= "</ul>";
		}

		return $html;
	}

	public function getCreated()
	{
		return date("d.m.Y H:i", CDateTimeParser::parse($this->created, "yyyy-MM-dd HH:mm:ss"));
	}

	public function getReferral()
	{
		if ($this->is_active && $this->getStatus()) {
			return Yii::app()->createAbsoluteUrl("site/index", array("invite_user" => $this->id));
		}

		return null;
	}

	public function getCountChilds()
	{
		$criteria = new CDbCriteria;
		$criteria->condition = "t.parent_id = :parent_id AND t.is_active = :is_active";
		$criteria->params["parent_id"] = $this->id;
		$criteria->params["is_active"] = 1;

		return count($this->findAll($criteria));
	}

	public function updateParentId()
	{
		if ($this->is_active) {
			return false;
		}

		$this->is_active = 1;
		$this->parent_id = $this->getNewParentId();

		return $this->save();
	}

	public function getNewParentId()
	{
		if ($this->parent_id) {
			$model = $this->findByPk($this->parent_id);
			if ($model) {
				$count = $model->getCountChilds();
				if ($count < self::MAX_CHILDS) {
					if ($count == self::MAX_CHILDS - 1) {
						$model->is_run_out_childs = 1;
					}
					if ($model->save()) {
						return $model->id;
					}
				}
			}
		}

		$criteria = new CDbCriteria;
		$criteria->condition = "t.is_active = :is_active AND t.is_run_out_childs = :is_run_out_childs";
		$criteria->params["is_active"] = 1;
		$criteria->params["is_run_out_childs"] = 0;
		$criteria->order = "t.id";
		$model = $this->find($criteria);
		if ($model) {
			$count = $model->getCountChilds();
			if ($count < self::MAX_CHILDS) {
				if ($count == self::MAX_CHILDS - 1) {
					$model->is_run_out_childs = 1;
				}
				if ($model->save()) {
					return $model->id;
				}
			}
		}

		return 1;
	}

	public function getActiveFlag()
	{
		if (empty($this->yesNoFlags[$this->is_active])) {
			return null;
		}

		return $this->yesNoFlags[$this->is_active];
	}

	public function beforeDelete()
	{
		if ($this->id == 1) {
			throw new CHttpException(500, "Невозможно удалить базавого пользователя");
		}

		$criteria = new CDbCriteria;
		$criteria->condition = "t.parent_id = :parent_id";
		$criteria->params["parent_id"] = $this->id;
		$users = $this->findAll($criteria);
		foreach ($users as $model) {
			$model->parent_id = $this->parent_id;
			$model->save();
		}

		foreach ($this->payments as $model) {
			$model->delete();
		}
		foreach ($this->operations as $model) {
			$model->delete();
		}
		foreach ($this->operationsFrom as $model) {
			$model->delete();
		}
		foreach ($this->paymentsMoney as $model) {
			$model->delete();
		}

		return parent::beforeDelete();
	}
}
