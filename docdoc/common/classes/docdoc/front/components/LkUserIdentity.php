<?php
namespace dfs\docdoc\front\components;
use CUserIdentity;
use dfs\docdoc\models\AuthTokenModel;
use dfs\docdoc\models\ClinicAdminModel;
use dfs\docdoc\models\ClinicModel;

/**
 * Class LkUserIdentity
 *
 * @package dfs\docdoc\front\components
 *
 *  Идентификация пользователя личного кабинета клиники
 *
 */
class LkUserIdentity extends CUserIdentity
{

	/**
	 * Идентификация пользователя в ЛК
	 *
	 * @return bool
	 */
	public function authenticate()
	{
		$user = ClinicAdminModel::model()
			->enabled()
			->findByAttributes(array('email' => $this->username));

		if ($user === null) {
			$this->errorCode = self::ERROR_USERNAME_INVALID;
			return !$this->errorCode;
		}

		$masterPassword = \Yii::app()->params['lk_master_password'];

		if (($masterPassword && strcmp($this->password, $masterPassword) === 0) || $user->checkPassword($this->password)) {
			$this->authenticateUser($user);
		} else {
			$this->errorCode = self::ERROR_PASSWORD_INVALID;
		}

		return !$this->errorCode;
	}

	/**
	 * Идентификация пользователя по токену
	 *
	 * @param AuthTokenModel $token
	 *
	 * @return bool
	 */
	public function authenticateByToken(AuthTokenModel $token)
	{
		$user = ClinicAdminModel::model()
			->enabled()
			->findByPk($token->user_id);

		if (!$user) {
			$this->errorCode = self::ERROR_USERNAME_INVALID;
			return false;
		}

		$this->username = $user->email;

		$this->authenticateUser($user);

		$token->using = intval($token->using) + 1;
		$token->save();

		return true;
	}

	/**
	 * Установка всех значений пользователя
	 *
	 * @param ClinicAdminModel $user
	 */
	protected function authenticateUser(ClinicAdminModel $user)
	{
		$clinic = $user->getDefaultClinic();

		$this->setState('id', $user->clinic_admin_id);
		$this->setState('clinics', $user->getClinicIds());
		$this->setState('clinicId', $clinic ? $clinic->id : null);
		$this->setState('login', $user->email);

		//эти поля могут быть null. В этом случае они не сохранятся в сессии. Поэтому приводим их к строке
		$this->setState('userFirstName', (string)$user->fname);
		$this->setState('userLastName', (string)$user->lname);
		$this->setState('phone', (string)$user->phone);

		$this->errorCode = self::ERROR_NONE;
	}
}
