<?php

namespace dfs\docdoc\front\components;

use CWebUser;


/**
 * Class PartnerUser
 *
 * @package dfs\docdoc\front\components
 *
 * Пользователь клиники для личного кабинета
 *
 */
class PartnerUser extends CWebUser
{
	/**
	 * Переопеределен базовый метод из класса CWebUser, чтобы он не менял идентификатор сессии
	 *
	 * @param mixed $id a unique identifier for the user
	 * @param string $name the display name for the user
	 * @param array $states identity states
	 */
	protected function changeIdentity($id, $name, $states)
	{
		$this->setId($id);
		$this->setName($name);
		$this->loadIdentityStates($states);
	}
}
