<?php
namespace likefifa\models;

use likefifa\models\AdminControllerModel;
use CActiveRecord;
use CDbCriteria;
use Yii;
use CActiveDataProvider;
use CHttpException;

/**
 * Файл класса AdminModel
 *
 * Модель для работы с администраторами
 *
 * @author  Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @link    https://docdoc.megaplan.ru/task/1002402/card/
 * @package models
 *
 * @property int    $id       идентификатор модели
 * @property string $name
 * @property string $login    логин
 * @property string $password пароль
 * @property int    $group_id идентификатор группы
 */
class AdminModel extends CActiveRecord
{

	/**
	 * Соль для пароля
	 *
	 * @var string
	 */
	const SALT = "likefifaSALT";

	/**
	 * Идентификаторы группы администраторов
	 *
	 * @var int
	 */
	const GROUP_ADMIN = 1;

	/**
	 * Идентификаторы группы операторов
	 *
	 * @var int
	 */
	const GROUP_OPERATOR = 2;

	/**
	 * Существующий пароль
	 *
	 * @var string
	 */
	private $_password = "";

	/**
	 * Список групп
	 *
	 * @var string[]
	 */
	public $groupList = array(
		self::GROUP_ADMIN    => "Администратор",
		self::GROUP_OPERATOR => "Оператор"
	);

	/**
	 * Список контроллеров для операторов
	 *
	 * @var string[]
	 */
	private $_operatorControllers = array(
		"index",
		"appointment",
		"masterSearch",
		'master',
		'salon',
	);

	/**
	 * Новый пароль
	 *
	 * @var string
	 */
	public $newPassword = "";

	/**
	 * Получает название таблицы в БД для модели
	 *
	 * @return string
	 */
	public function tableName()
	{
		return 'admin';
	}

	/**
	 * Правила валидации для атрибутов модели
	 *
	 * @return string[]
	 */
	public function rules()
	{
		return array(
			array('login, password, group_id, name', 'required'),
			array('group_id', 'numerical', 'integerOnly' => true),
			array('login', 'length', 'max' => 128),
			array('password, newPassword', 'length', 'max' => 40),
			array('name', 'length', 'max' => 64),
			array('id, login, password, group_id, name', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * Связи с другими моделями
	 *
	 * @return string[]
	 */
	public function relations()
	{
		return array();
	}

	/**
	 * Названия меток для атрибутов
	 *
	 * @return string[]
	 */
	public function attributeLabels()
	{
		return array(
			'id'          => 'ID',
			'login'       => 'Логин',
			'password'    => 'Пароль',
			'group_id'    => 'Группа',
			'name'        => 'Имя',
			"newPassword" => "Новый пароль",
		);
	}

	/**
	 * Поиск в списке администраторов в БО
	 *
	 * @return CActiveDataProvider
	 */
	public function search()
	{
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('login', $this->login, true);
		$criteria->compare('password', $this->password, true);
		$criteria->compare('group_id', $this->group_id);
		$criteria->compare('name', $this->name, true);

		return new CActiveDataProvider($this, array('criteria' => $criteria));
	}

	/**
	 * Получает модель класса
	 *
	 * @param string $className название класса
	 *
	 * @return AdminModel
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * Вызывается после создания экземпляра модели
	 *
	 * @return void
	 */
	protected function afterFind()
	{
		$this->_password = $this->password;
	}

	/**
	 * Получает модель по имени администратора
	 *
	 * @return AdminModel
	 *
	 * @throws CHttpException
	 */
	public static function getModel()
	{
		$criteria = new CDbCriteria;
		$criteria->condition = "login = :login";
		$criteria->params[":login"] = Yii::app()->user->name;

		$model = self::model()->find($criteria);
		if ($model === null) {
			throw new CHttpException(404, 'Данные для администратора были потеряны! Пожалуйста перелогиньтесь.');
		}

		return $model;
	}

	/**
	 * Вызывается перед сохранением модели
	 *
	 * @return bool
	 */
	protected function beforeSave()
	{
		$this->login = trim($this->login);

		if ($this->newPassword) {
			$this->password = $this->newPassword;
		}

		if ($this->isNewRecord || $this->password !== $this->_password) {
			$this->password = self::getSha1Password($this->password);
		}

		return parent::beforeSave();
	}

	/**
	 * Получает список меню для БО
	 *
	 * @return string[]
	 */
	public function getItemsForBoMenu()
	{
		$list = [];

		foreach ($this->getAvailableControllers() as $model) {
			if ($model->col_group == null) {
				$list[] = [
					"label"   => '<span class="hidden-sm text">' . $model->name . '</span>',
					"url"     => ["/admin/{$model->rewrite_name}"],
					"visible" => true,
					'icon' => 'fa fa-' . $model->icon,
				];
			} else {
				if (!array_key_exists($model->col_group, $list)) {
					$list[$model->col_group] = [
						"label"   => '<span class="hidden-sm text">' . $model->col_group . '</span>',
						'items' => []
					];
				}
				$list[$model->col_group]['icon'] = 'fa fa-align-left';
				$list[$model->col_group]['items'][] = [
					"label"   => '<span class="hidden-sm text">' . $model->name . '</span>',
					"url"     => array("/admin/{$model->rewrite_name}"),
					"visible" => true,
					'icon' => 'fa fa-' . $model->icon
				];
			}
		}

		return $list;
	}

	/**
	 * Получает список доступных контроллеров для администратора
	 *
	 * @return AdminControllerModel[]
	 */
	public function getAvailableControllers()
	{
		$criteria = new CDbCriteria;
		$criteria->order = "t.sort";
		$criteria->order = '!ISNULL(col_group), sort asc';

		if ($this->group_id == self::GROUP_OPERATOR) {
			$criteria->addInCondition("t.rewrite_name", $this->_operatorControllers);
		}

		return AdminControllerModel::model()->findAll($criteria);
	}

	/**
	 * Получает хэш строку из пароля
	 *
	 * @param string $password незашифрованный пароль
	 *
	 * @return string
	 */
	public static function getSha1Password($password)
	{
		return sha1($password . self::SALT);
	}

	/**
	 * Получает список администраторов для текущего контроллера
	 *
	 * @param string $controller название контроллера
	 *
	 * @return string[]
	 */
	public function getAdminsForThisController($controller)
	{
		$list = array();

		$controllerName = str_replace("admin/", "", $controller);

		foreach ($this->findAll() as $model) {
			switch ($model->group_id) {
				case self::GROUP_ADMIN:
					$list[] = $model->login;
					break;
				case self::GROUP_OPERATOR:
					if (in_array($controllerName, $this->_operatorControllers)) {
						$list[] = $model->login;
					}
					break;
			}
		}

		return $list;
	}

	/**
	 * Получает название группы
	 *
	 * @return string
	 */
	public function getGroupName()
	{
		if (empty($this->groupList[$this->group_id])) {
			return null;
		}

		return $this->groupList[$this->group_id];
	}

	/**
	 * Вызывается перед удалением модели
	 *
	 * @throw CHttpException
	 */
	protected function beforeDelete()
	{
		throw new CHttpException(500, "Невозможно удалить администратора");
	}

	/**
	 * Определяет, является ли администратор оператором
	 *
	 * @return bool
	 */
	public function isOperator()
	{
		return $this->group_id == self::GROUP_OPERATOR;
	}

	/**
	 * Определяет, обладает ли администратор полным доступом
	 *
	 * @return bool
	 */
	public function isFullAccess()
	{
		return $this->group_id == self::GROUP_ADMIN;
	}

	/**
	 * Получает список операторов
	 *
	 * @param bool $withEmpty первый элемент списка пустой
	 *
	 * @return string[]
	 */
	public function getOperatorList($withEmpty = false)
	{
		$list = array();
		if ($withEmpty) {
			$list[null] = null;
		}

		$criteria = new CDbCriteria;
		$criteria->condition = "t.group_id = :group_id";
		$criteria->params["group_id"] = self::GROUP_OPERATOR;
		$criteria->order = "t.name";

		foreach ($this->findAll($criteria) as $model) {
			$list[$model->id] = $model->name;
		}

		return $list;
	}
}