<?php
/**
 * Created by PhpStorm.
 * User: ndunaev
 * Date: 11.08.14
 * Time: 13:21
 */

namespace dfs\docdoc\filters;


use dfs\docdoc\api\components\ApiUserIdentity;
use Yii;

class PartnerAuthFilter extends \CFilter
{

	/**
	 * Перед выполнением действия в контроллере
	 *
	 * @param \CFilterChain $filterChain
	 *
	 * @return bool
	 */
	public function preFilter($filterChain) {

		if (!isset($_SERVER['PHP_AUTH_USER'])) {
			ApiUserIdentity::showBaseAuthWindow();
		}

		$login = $_SERVER['PHP_AUTH_USER'];
		$password = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : null;

		$identity = new ApiUserIdentity($login, $password);

		if (!$identity->authenticate()) {
			ApiUserIdentity::showBaseAuthWindow();
			exit;
		} else {
			$partnerId = $identity->getId();
			if (Yii::app()->referral->getId() !== $partnerId) {
				Yii::app()->referral->setId($partnerId);
			}
		}

		return true;
	}

	/**
	 * поле выполнения действия в контроллере
	 *
	 * @param \CFilterChain $filterChain
	 */
	public function postFilter($filterChain) {}


} 