<?php

namespace dfs\docdoc\models;

/**
 * This is the model class for table "clinic_admin".
 *
 * The followings are the available columns in table 'clinic_admin':
 *
 * @property integer $clinic_admin_id
 * @property string $email
 * @property string $fname
 * @property string $lname
 * @property string $mname
 * @property string $phone
 * @property string $cell_phone
 * @property string $passwd
 * @property string $admin_comment
 * @property string $status
 *
 * The followings are the available model relations:
 *
 * @property ClinicModel[] $clinics
 *
 * @method ClinicAdminModel findByPk
 * @method ClinicAdminModel findByAttributes
 */
class ClinicAdminModel extends \CActiveRecord
{

	/**
	 * Существующий пароль
	 *
	 * @var string
	 */
	private $_password = "";


	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return ClinicAdminModel the static model class
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
		return 'clinic_admin';
	}

	/**
	 * @return string the associated primary key
	 */
	public function primaryKey()
	{
		return 'clinic_admin_id';
	}

	/**
	 * Зависимости
	 * @return array
	 */
	public function relations()
	{
		return array(
			'clinics' => array(self::MANY_MANY, 'dfs\docdoc\models\ClinicModel',
				'admin_4_clinic(clinic_admin_id, clinic_id)'),
		);
	}

	/**
	 * Поведения
	 *
	 * CAdvancedArBehavior - класс реализующий автоматическое сохранение, удаление отношений MANY_MANY
	 *
	 * @return array
	 */
	public function behaviors()
	{
		return array(
			'CAdvancedArBehavior' => array(
				'class' => 'CAdvancedArBehavior',
			),
		);
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('email, passwd', 'required'),
			array('email', 'unique'),
			array('phone, cell_phone, fname, lname, mname, email', 'length', 'max' => 50),
			array('email', 'email'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array(
				'clinic_admin_id, email, fname, lname, mname, phone, cell_phone, passwd, admin_comment, status',
				'safe',
				'on' => 'search'
			),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'clinic_admin_id' => 'ID',
			'email' => 'E-mail',
			'fname' => 'Фамилия',
			'lname' => 'Имя',
			'mname' => 'Отчество',
			'phone' => 'Номер телефона',
			'cell_phone' => 'Номер мобильного телефона',
			'passwd' => 'Пароль',
			'admin_comment' => 'Комментарий',
			'status' => 'Статус',
		);
	}

	/**
	 * выборка только незаблокированных пользователей
	 *
	 * @return $this
	 */
	public function enabled()
	{
		$this->getDbCriteria()->mergeWith(
			array(
				'condition' => $this->getTableAlias() . '.status = "enable"',
			)
		);

		return $this;
	}

	/**
	 * возвращает массив идентификаторов клиник для
	 * @return int[]
	 */
	public function getClinicIds()
	{
		$clinics = array();

		foreach ($this->clinics as $c) {
			$clinics[] = $c->id;
		};

		return $clinics;
	}

	/**
	 * Возвращает клинику по-умолчанию. Первая клиника в списке клиник
	 * @return null|ClinicModel
	 */
	public function getDefaultClinic()
	{
		return (count($this->clinics)) ? $this->clinics[0] : null;
	}

	/**
	 * Поиск по почтовому ящику
	 *
	 * @param string $email
	 *
	 * @return $this
	 */
	public function byEmail($email)
	{
		$this->getDbCriteria()
			->mergeWith(
				[
					'condition' => $this->getTableAlias() . '.email = :email',
					'params' => [
						':email' => $email,
					]
				]
			);

		return $this;
	}

	/**
	 * Поиск по клинике
	 *
	 * @param int $clinicId
	 *
	 * @return $this
	 */
	public function byClinic($clinicId)
	{
		$this->getDbCriteria()->mergeWith([
			'with' => [
				'clinics' => [
					'joinType' => 'INNER JOIN',
					'select' => '',
					'scopes' => [
						'findByPK' => [$clinicId],
					],
				],
			],
		]);

		return $this;
	}

	/**
	 * Исключить из поиска айдишники
	 *
	 * @param int[] $ids
	 *
	 * @return $this
	 */
	public function exludeIds(array $ids)
	{
		$criteria= new \CDbCriteria;
		$criteria->addNotInCondition($this->primaryKey(), $ids);

		$this->getDbCriteria()
			->mergeWith($criteria);

		return $this;
	}

	/**
	 * Выполняет действия перед сохранением модели
	 *
	 * @return bool
	 */
	protected function beforeSave()
	{
		if ($this->isNewRecord || $this->passwd !== $this->_password) {
			$this->passwd = $this->getUserPasswordHash($this->passwd);
		}

		return parent::beforeSave();
	}

	/**
	 * Вызывается после создания экземпляра модели
	 *
	 * @return void
	 */
	protected function afterFind()
	{
		$this->_password = $this->passwd;
	}


	/**
	 * Получает хэш из пароля
	 *
	 * @param string $password пароль в чистом виде
	 *
	 * @return string
	 */
	public function getUserPasswordHash($password)
	{
		if ($password) {
			return md5($password);
		}

		return null;
	}

	/**
	 * установка пароля
	 *
	 * @param string $password
	 *
	 * @return $this
	 */
	public function setPassword($password)
	{
		$this->passwd = $this->getUserPasswordHash($password);
		$this->_password = $this->passwd;

		return $this;
	}

	/**
	 * Проверка пароля
	 *
	 * @param string $password
	 *
	 * @return bool
	 */
	public function checkPassword($password)
	{
		return $this->passwd === $this->getUserPasswordHash($password);
	}


	/**
	 * ФИО админа
	 *
	 * @return string
	 */
	public function getFullName()
	{
		return "{$this->lname} {$this->fname} {$this->mname}";
	}
}
