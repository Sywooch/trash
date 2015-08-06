<?php


namespace likefifa\components\system;

use CWebUser;
use likefifa\models\LoginHistory;
use MasterIdentity;
use Yii;

class WebUser extends CWebUser
{
	/**
	 * @var MasterIdentity
	 */
	public $identity = null;

	/**
	 * Авторизует пользователя
	 *
	 * @param MasterIdentity $identity
	 * @param int           $duration
	 *
	 * @return bool
	 */
	public function login($identity, $duration = 0)
	{
		$this->identity = $identity;
		return parent::login($identity, $duration);
	}

	/**
	 * Метод выполняется после авторизации пользователя
	 *
	 * @param bool $fromCookie
	 */
	public function afterLogin($fromCookie)
	{
		if($this->identity instanceof MasterIdentity) {
			// Логируем авторизацию
			$log = new LoginHistory();
			$log->type = $this->identity->userType;
			$log->entity_id = $this->id;
			$log->date = date('Y-m-d H:i:s');
			$log->ip = $_SERVER["REMOTE_ADDR"];
			$log->ua = $_SERVER['HTTP_USER_AGENT'];
			$log->ga = Yii::app()->gaTracking->getUserId();
			$log->save();
		}

		if(isset($this->identity->userType)) {
			$this->setState('__type', $this->identity->userType);
		}

		parent::afterLogin($fromCookie);
	}

	public function getType() {
		return $this->getState('__type');
	}
} 