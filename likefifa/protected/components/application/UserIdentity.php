<?php

namespace likefifa\components\application;

use likefifa\models\AdminModel;
use CUserIdentity;
use CDbCriteria;

/**
 * Файл класса UserIdentity.
 *
 * Авторизация администраторов
 *
 * @author  Mikhail Vasilyev <mail@itnew.pro>
 * @link    https://docdoc.megaplan.ru/task/1002402/card/
 * @package components
 */
class UserIdentity extends CUserIdentity
{
	private $_id;
	public $userType = 'admin';

	/**
	 * Проверяет логин и пароль
	 *
	 * @return AdminModel|null
	 */
	public function authenticate()
	{
		$criteria = new CDbCriteria;
		$criteria->condition = "login = :login AND password = :password";
		$criteria->params["login"] = $this->username;
		$criteria->params["password"] = AdminModel::getSha1Password($this->password);

		$model = AdminModel::model()->find($criteria);
		if ($model) {
			$this->_id = $model->id;
			$this->errorCode = self::ERROR_NONE;
		} else {
			$this->errorCode = self::ERROR_PASSWORD_INVALID;
		}

		return $this->errorCode == self::ERROR_NONE;
	}

	public function getId()
	{
		return $this->_id;
	}
}