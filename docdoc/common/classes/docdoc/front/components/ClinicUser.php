<?php
namespace dfs\docdoc\front\components;
use CWebUser;

/**
 * Class ClinicUser
 *
 * @package dfs\docdoc\front\components
 *
 * Пользователь клиники для личного кабинета
 *
 */
class ClinicUser extends CWebUser
{


	/**
	 * Переопеределен базовый метод из класса CWebUser, чтобы он не менял идентификатор сессии
	 * проблема здесь https://docdoc.megaplan.ru/task/1003896/card/
	 *
	 * Changes the current user with the specified identity information.
	 * This method is called by {@link login} and {@link restoreFromCookie}
	 * when the current user needs to be populated with the corresponding
	 * identity information. Derived classes may override this method
	 * by retrieving additional user-related information. Make sure the
	 * parent implementation is called first.
	 *
	 * @param mixed $id a unique identifier for the user
	 * @param string $name the display name for the user
	 * @param array $states identity states
	 */
	protected function changeIdentity($id,$name,$states)
	{
		//\Yii::app()->getSession()->regenerateID(true);
		$this->setId($id);
		$this->setName($name);
		$this->loadIdentityStates($states);
	}

	/**
	 * Геттер для идентификатора клиники
	 *
	 * @return int
	 */
	public function getClinicId()
	{
		return $this->getState('clinicId');
	}

	/**
	 * Геттер для идентификатора залогиненного юзера
	 *
	 * @return int
	 */
	public function getUserId()
	{
		return $this->getState('id');
	}
}
