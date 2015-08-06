<?php

class UserIdentity extends CUserIdentity
{
	public $errorClass = "";
	const SALT = "mmmkaluga";

	public function authenticate()
	{
		if ($this->username === 'admin' && $this->password === 'admin') {
			$this->errorCode = self::ERROR_NONE;
		} else {
			$this->errorCode = self::ERROR_PASSWORD_INVALID;
		}

		return $this->errorCode == self::ERROR_NONE;
	}

	public function authenticateUser()
	{
		if (!$this->username) {
			$this->errorClass = "email-empty";
		} else {
			if (!$this->password) {
				$this->errorClass = "password-empty";
			} else {
				$criteria = new CDbCriteria;
				$criteria->condition = "t.email = :emain";
				$criteria->params["email"] = $this->username;
				$model = User::model()->find("email = :email", array(":email" => $this->username));
				if (!$model) {
					$this->errorClass = "email-not-exist";
				} else {
					if ($model->password !== self::getPassword($this->password)) {
						$this->errorClass = "password-wrong";
					}
				}
			}
		}

		return !$this->errorClass;
	}

	public static function getPassword($password)
	{
		return sha1($password . self::SALT);
	}
}