<?php

namespace dfs\docdoc\models;

use \CActiveRecord;
use \CDbCriteria;
use \CActiveDataProvider;

/**
 * Файл класса UserModel
 *
 * Модель для работы с пользователями
 *
 * @author  Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @link    https://docdoc.megaplan.ru/task/1003744/card/
 * @package dfs.docdoc.models
 *
 * @property int     $user_id       ID
 * @property string  $user_login    Логин
 * @property string  $user_password Пароль
 * @property int     $user_role     Роль
 * @property string  $user_fname    Имя
 * @property string  $user_lname    Фамилия
 * @property string  $user_mname    Отчество
 * @property string  $user_email    E-mail
 * @property int     $user_status   Статус пользователя
 * @property string  $status        Статус
 * @property string  $phone         Телефон
 * @property string  $skype         Skype
 * @property int     $operator_stream Тип потока заявок для операторов
 *
 * @method UserModel find
 * @method UserModel findByPk
 * @method UserModel[] findAll
 */
class UserModel extends CActiveRecord
{
	const STATUS_ENABLED  = "enable";
	const STATUS_DISABLED = "disable";


	/**
	 * Существующий пароль
	 *
	 * @var string
	 */
	private $_password = "";

	/**
	 * Возвращает имя связанной таблицы базы данных
	 *
	 * @return string
	 */
	public function tableName()
	{
		return 'user';
	}

	public function primaryKey()
	{
		return 'user_id';
	}


	/**
	 * Возвращает правила проверки для атрибутов модели
	 *
	 * @return string[]
	 */
	public function rules()
	{
		return [
			['user_login, user_password, user_role, user_fname, user_lname', 'required'],
			['user_role, user_status, operator_stream', 'numerical', 'integerOnly' => true],
			['user_login, user_password, user_fname, user_lname, user_mname, user_email', 'length', 'max' => 255],
			['status', 'length', 'max' => 7],
			['phone, skype', 'length', 'max' => 50],
			[
				'user_login, user_password, user_fname, user_lname, user_mname, user_email, status, phone, skype',
				'filter',
				'filter' => 'strip_tags'
			],
			[
				'user_id, user_login, user_password, user_role, user_fname, user_lname, user_mname, user_email,
					user_status, status, phone, skype',
				'safe',
				'on' => 'search'
			],
		];
	}

	/**
	 * Возвращает связи между объектами
	 *
	 * @return string[]
	 */
	public function relations()
	{
		return [];
	}

	/**
	 * Возвращает подписей полей
	 *
	 * @return string[]
	 */
	public function attributeLabels()
	{
		return [
			'user_id'       => 'ID',
			'user_login'    => 'Логин',
			'user_password' => 'Пароль',
			'user_role'     => 'Роль',
			'user_fname'    => 'Имя',
			'user_lname'    => 'Фамилия',
			'user_mname'    => 'Отчество',
			'user_email'    => 'E-mail',
			'user_status'   => 'Статус пользователя',
			'status'        => 'Статус',
			'phone'         => 'Телефон',
			'skype'         => 'Skype',
			'operator_stream' => 'Поток заявок для оператора',
		];
	}

	/**
	 * Получает список моделей на основе условий поиска / фильтров.
	 *
	 * @return CActiveDataProvider
	 */
	public function search()
	{
		$criteria = new CDbCriteria;

		$criteria->compare('user_id', $this->user_id);
		$criteria->compare('user_login', $this->user_login, true);
		$criteria->compare('user_password', $this->user_password, true);
		$criteria->compare('user_role', $this->user_role);
		$criteria->compare('user_fname', $this->user_fname, true);
		$criteria->compare('user_lname', $this->user_lname, true);
		$criteria->compare('user_mname', $this->user_mname, true);
		$criteria->compare('user_email', $this->user_email, true);
		$criteria->compare('user_status', $this->user_status);
		$criteria->compare('status', $this->status, true);
		$criteria->compare('phone', $this->phone, true);
		$criteria->compare('skype', $this->skype, true);

		return new CActiveDataProvider($this, [
			'criteria' => $criteria,
		]);
	}

	/**
	 * Возвращает статическую модель указанного класса.
	 *
	 * @param string $className название класса
	 *
	 * @return UserModel
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * Выполняет действия перед сохранением модели
	 *
	 * @return bool
	 */
	protected function beforeSave()
	{
		if ($this->isNewRecord || $this->user_password !== $this->_password) {
			$this->user_password = $this->getUserPasswordHash($this->user_password);
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
		$this->_password = $this->user_password;
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
	 * Полное имя
	 *
	 * @return string
	 */
	public function getFullName()
	{
		return "{$this->user_lname} {$this->user_fname}";
	}

	/**
	 * Активные / неактивные пользователи
	 *
	 * @param string $status
	 *
	 * @return $this
	 */
	public function enabled($status = UserModel::STATUS_ENABLED)
	{
		$this->getDbCriteria()->mergeWith(
			[
				'condition' => $this->getTableAlias() . ".status = :userStatus",
				"params" => [":userStatus" => $status],
			]
		);
		return $this;
	}

	/**
	 * Выборка с ролями
	 *
	 * @param string[] $roles
	 * @return $this
	 */
	public function withRoles(array $roles)
	{
		$criteria = new \CDbCriteria();
		$criteria->join = "INNER JOIN right_4_user r4u ON (r4u.user_id = t.user_id)
			INNER JOIN user_right_dict ud ON (ud.right_id = r4u.right_id)";
		$criteria->addInCondition("ud.code", $roles);

		$this->getDbCriteria()->mergeWith($criteria);
		return $this;
	}
}
