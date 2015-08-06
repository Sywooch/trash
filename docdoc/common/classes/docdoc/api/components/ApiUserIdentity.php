<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 30.07.14
 * Time: 14:29
 */

namespace dfs\docdoc\api\components;

use dfs\docdoc\models\PartnerModel;

/**
 * Аутентификации для партнера
 *
 * Class ApiUserIdentity
 *
 * @package dfs\docdoc\api\components
 */
class ApiUserIdentity extends \CUserIdentity
{
	/**
	 * Реализация аутентификации для партнера
	 *
	 * @return bool
	 */
	public function authenticate()
	{
		$user = PartnerModel::model()
			->byLogin($this->username)
			->find();


		if(!$user) {
			$this->errorCode = self::ERROR_USERNAME_INVALID;
			return !$this->errorCode;
		}

		$config = \Yii::app()->params;

		if(
			!$user->checkPasswordForEquals($this->password) &&
			(isset($config['pk_master_password']) && strcmp($this->password, $config['pk_master_password']) !== 0)
		) {
			$this->errorCode = self::ERROR_PASSWORD_INVALID;
		} else {
			$this->setState('id', $user->id);
			$this->setState('login', $user->login);

			$this->errorCode = self::ERROR_NONE;
		}

		return !$this->errorCode;
	}

	/**
	 * Родительский возвращает username по дефолту. мне нужен id
	 *
	 * @return mixed|string
	 */
	public function getId()
	{
		return $this->getState('id');
	}

	/**
	 * Открывает окно ввода логинов
	 */
	public static function showBaseAuthWindow()
	{
		header('WWW-Authenticate: Basic realm="Restricted"');
		header('HTTP/1.0 401 Unauthorized');
		die('401 Authorization Required');
	}

} 
