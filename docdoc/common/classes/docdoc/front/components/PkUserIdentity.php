<?php

namespace dfs\docdoc\front\components;

use CUserIdentity;
use dfs\docdoc\models\PartnerModel;

/**
 * Class PkUserIdentity
 *
 * @package dfs\docdoc\front\components
 *
 *  Идентификация пользователя для партнёрского кабинета
 *
 */
class PkUserIdentity extends CUserIdentity
{
	/**
	 * Идентификация пользователя в ПК
	 *
	 * @return bool
	 */
	public function authenticate()
	{
		$partner = PartnerModel::model()->byLoginOrEmail($this->username)->find();

		$masterPassword = \Yii::app()->params['pk_master_password'];

		if ($partner) {
			if (($masterPassword && strcmp($this->password, $masterPassword) === 0) || $partner->checkPassword($this->password)) {
				$this->setState('partnerId', $partner->id);
				$this->errorCode = self::ERROR_NONE;
			} else {
				$this->errorCode = self::ERROR_PASSWORD_INVALID;
			}
		} else {
			$this->errorCode = self::ERROR_USERNAME_INVALID;
		}

		return !$this->errorCode;
	}
}
