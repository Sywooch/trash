<?php

namespace dfs\docdoc\front\controllers\lk;

use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\models\ClinicAdminModel;
use dfs\docdoc\models\MailQueryModel;


/**
 * Class SiteController
 *
 * @package dfs\docdoc\front\controllers\lk
 */
class SiteController extends FrontController
{
	/**
	 * Главная страница
	 */
	public function actionIndex()
	{
		\Yii::app()->request->redirect($this->getMainPageUrl());
	}

	/**
	 * Страница с ошибкой (404)
	 */
	public function actionError()
	{
		$error = \Yii::app()->errorHandler->error;

		if (!$error) {
			$error = [
				'code'    => '404',
				'message' => '',
			];
		}

		if (\Yii::app()->request->isAjaxRequest) {
			echo $error['message'];
		} else {
			$this->render('error', $error);
		}
	}

	/**
	 * Страница "О партнере"
	 */
	public function actionInfo()
	{
		$this->render('info', [
			'clinic' => $this->_clinic,
		]);
	}

	/**
	 * Страница "Настройки"
	 */
	public function actionSettings()
	{
		$this->render('settings', [
			'clinic' => $this->_clinic,
			'admin' => ClinicAdminModel::model()->findByPk(\Yii::app()->user->getState('id')),
		]);
	}

	/**
	 * Изменение пароля
	 */
	public function actionChangePassword()
	{
		$status = false;
		$error = null;

		$request = \Yii::app()->request;

		$currentPassword = $request->getPost('currentPassword');
		$newPassword = $request->getPost('newPassword');
		$repeatPassword = $request->getPost('repeatPassword');

		if (!$currentPassword || !$newPassword) {
			$error = 'Не передан пароль';
		}
		elseif (strcmp($newPassword, $repeatPassword) !== 0) {
			$error = 'Не совпадают новые пароли';
		} else {
			$admin = $this->_admin;

			if (!$admin->checkPassword($currentPassword)) {
				$error = 'Неверно набран текущий пароль';
			} else {
				$admin->setPassword($newPassword);

				$status = $admin->save();

				if ($status && $admin->email) {
					MailQueryModel::model()->sendMailClinicChangePassword($admin, $newPassword);
				}
			}
		}

		$this->renderJsonAnswer($status, $error);
	}

	/**
	 * Действие "Вопрос от клиники"
	 */
	public function actionSendQuestion()
	{
		$status = false;
		$error = null;

		$request = \Yii::app()->request;

		$questionText = $request->getPost('message');

		if ($questionText) {
			$status = MailQueryModel::model()->sendMailClinicQuestion($this->_clinic, $this->_admin, $questionText);
			if (!$status) {
				$error = 'Не возможно отправить сообщение';
			}
		} else {
			$error = 'Не получен текст сообщения';
		}

		$this->renderJsonAnswer($status, $error);
	}

	/**
	 * Действие "Смена клиники"
	 */
	public function actionChangeClinic()
	{
		$request = \Yii::app()->request;

		$clinicId = intval($request->getPost('clinicId'));

		if ($clinicId) {
			foreach ($this->getAdminClinicList() as $c) {
				if ($c->id == $clinicId) {
					\Yii::app()->user->setState('clinicId', $c->id);
					break;
				}
			}
		}

		$request->redirect($this->getMainPageUrl());
	}
}
