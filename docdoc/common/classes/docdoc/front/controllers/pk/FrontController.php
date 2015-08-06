<?php

namespace dfs\docdoc\front\controllers\pk;

use dfs\docdoc\components\AppController;
use dfs\docdoc\models\PartnerModel;


/**
 * Class FrontController
 *
 * @package dfs\docdoc\front\controllers\pk
 */
class FrontController extends AppController
{
	/**
	 * Текущий партнер
	 *
	 * @var PartnerModel
	 */
	protected $_partner = null;

	/**
	 * Меню
	 *
	 * @var array
	 */
	protected $_menu = [
		'patients' => [
			'Title'    => 'Ваши заявки',
			'URL'      => '/pk/patients',
		],
		'tools' => [
			'Title'    => 'Инструменты',
			'URL'      => '/pk/tools',
		],
		'info' => [
			'Title'    => 'О партнере',
			'URL'      => '/pk/info',
		],
		'settings' => [
			'Title'    => 'Настройки',
			'URL'      => '/pk/settings',
		],
		'logout' => [
			'Title'      => 'Выйти из личного кабинета',
			'URL'        => '/pk/service/logout',
			'Class'      => 'nav_list__link-quit',
		],
	];

	/**
	 * Выполняем перед любым действием
	 *
	 * @param string $action
	 *
	 * @return bool
	 */
	public function beforeAction($action)
	{
		$user = \Yii::app()->user;
		$partnerId = $user->getState('partnerId');

		if ($partnerId) {
			$this->_partner = PartnerModel::model()->findByPk($partnerId);

			if ($this->_partner && !$this->_partner->offer_accepted && !$user->getState('pkRoutedToAcceptOffer')) {
				$user->setState('pkRoutedToAcceptOffer', true);
				\Yii::app()->request->redirect('/pk/acceptOffer');
				return false;
			}
		}

		return true;
	}

	/**
	 * Фильтры
	 *
	 * @return array
	 */
	public function filters()
	{
		return [ 'accessControl' ];
	}

	/**
	 * Возвращает правила доступа для контроллера
	 *
	 * @return array
	 */
	public function accessRules()
	{
		return [
			[ 'allow', 'users' => [ '@' ] ],
			[ 'deny' ],
		];
	}

	/**
	 * Генерация json-ответа, для старых js-скриптов
	 *
	 * @param bool $status
	 * @param string $error
	 * @param string | bool $url
	 */
	protected function renderJsonAnswer($status, $error = null, $url = false)
	{
		$result = [];

		if ($status) {
			$result['status'] = 'success';
			$result['url'] = $url;
		} else {
			$result['status'] = 'error';
			$result['error'] = $error;
		}

		echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);

		foreach (\Yii::app()->log->routes as $route) {
			if($route instanceof \CWebLogRoute) {
				$route->enabled = false;
			}
		}

		\Yii::app()->end();
	}
}
