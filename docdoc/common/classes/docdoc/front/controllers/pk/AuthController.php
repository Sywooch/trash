<?php

namespace dfs\docdoc\front\controllers\pk;

use dfs\docdoc\models\PartnerModel;
use dfs\docdoc\models\MailQueryModel;
use dfs\docdoc\models\CityModel;
use dfs\docdoc\front\components\PkUserIdentity;
use dfs\docdoc\helpers\PasswordHelper;


/**
 * Class AuthController
 *
 * @package dfs\docdoc\front\controllers\pk
 */
class AuthController extends FrontController
{
	/**
	 * Дефолтный layout
	 *
	 * @var string
	 */
	public $layout = 'simple';

	/**
	 * Список городов
	 *
	 * @var array
	 */
	protected $_cities = [];

	/**
	 * Выполняем перед любым действием
	 *
	 * @param string $action
	 *
	 * @return bool
	 */
	public function beforeAction($action)
	{
		$this->_cities = CityModel::model()->active()->findAll([ 'order' => 't.title' ]);

		return parent::beforeAction($action);
	}

	/**
	 * Возвращает правила доступа для контроллера
	 *
	 * @return array
	 */
	public function accessRules()
	{
		return [
			[ 'allow' ]
		];
	}

	/**
	 * Страница авторизации
	 */
	public function actionAuth()
	{
		$this->render('auth');
	}

	/**
	 * Страница востановления пароля для партнёра
	 */
	public function actionRecoveryPassword()
	{
		$this->render('recoveryPassword');
	}

	/**
	 * Авторизация
	 */
	public function actionLogin()
	{
		$status = false;

		$request = \Yii::app()->request;
		$user = \Yii::app()->user;

		$login = $request->getPost('login');
		$password = $request->getPost('password');

		if ($login && $password) {
			$identity = new PkUserIdentity($login, $password);

			if ($identity->authenticate()) {
				$cookieLifeTime = $user->allowAutoLogin ? 3600 * 24 * 30 : 0;
				$status = $user->login($identity, $cookieLifeTime);
			} else {
				$user->setFlash('error', 'Авторизоваться не удалось. Проверьте логин/пароль');
			}
		} else {
			$user->setFlash('error', 'Вы заполнили не все поля формы');
		}

		$request->redirect($status ? $user->returnUrl : $user->loginUrl);
	}

	/**
	 * Logout
	 */
	public function actionLogout()
	{
		\Yii::app()->user->logout();
		\Yii::app()->request->redirect(\Yii::app()->user->loginUrl);
	}

	/**
	 * Востановление пароля
	 */
	public function actionRecovery()
	{
		$status = false;

		$request = \Yii::app()->request;
		$user = \Yii::app()->user;

		$email = $request->getPost('email');

		if ($email) {
			$partner = PartnerModel::model()->findByAttributes([ 'contact_email' => $email ]);

			if ($partner) {
				$password = PasswordHelper::generate(8);

				$partner->setPassword($password);

				$status = $partner->save();

				if ($status) {
					MailQueryModel::model()->sendMailPartnerRecovery($email, $password);
					$user->setFlash('success', 'На указанный e-mail адрес отправлено письмо с временным паролем');
				}
			} else {
				$user->setFlash('error', 'Неверный e-mail адрес');
			}
		} else {
			$user->setFlash('error', 'Не введен e-mail адрес');
		}

		$request->redirect($status ? $user->loginUrl : '/pk/recoveryPassword');
	}
}
