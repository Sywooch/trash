<?php

namespace dfs\docdoc\front\controllers\pk;

use dfs\docdoc\models\ServiceModel;
use dfs\docdoc\models\PartnerCostModel;
use dfs\docdoc\models\MailQueryModel;


/**
 * Class SiteController
 *
 * @package dfs\docdoc\front\controllers\pk
 */
class SiteController extends FrontController
{
	public function actionError()
	{
		$this->render('error');
	}

	/**
	 * Страница "О партнере"
	 */
	public function actionInfo()
	{
		$this->render(
			'info',
			[
				'partner'      => $this->_partner,
				'partnerCosts' => PartnerCostModel::model()->withCity()->findAllForPartner($this->_partner->id),
			]
		);
	}

	/**
	 * Страница "Настройки"
	 */
	public function actionSettings()
	{
		$this->render('settings', [
			'partner' => $this->_partner,
		]);
	}

	/**
	 * Страница с офертой
	 */
	public function actionAcceptOffer()
	{
		$this->layout = 'simple';
		$this->render('acceptOffer');
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
			$error = 'Новый пароль не совпадает c введенным повторно';
		} else {
			$partner = $this->_partner;

			if (!$partner->checkPassword($currentPassword)) {
				$error = "Неверно введен пароль для {$partner->login} $currentPassword";
			} else {
				$partner->setPassword($newPassword);

				$status = $partner->save();

				if ($status && $request->getParam('sendToEmail') && $partner->contact_email) {
					MailQueryModel::model()->sendMailPartnerChangePassword($partner, $newPassword);
				}
			}
		}

		$this->renderJsonAnswer($status, $error);
	}

	/**
	 * Действие "Вопрос от партнера"
	 */
	public function actionSendQuestion()
	{
		$status = false;
		$error = null;

		$request = \Yii::app()->request;

		$questionText = $request->getPost('message');

		if ($questionText) {
			$status = MailQueryModel::model()->sendMailPartnerQuestion($this->_partner, $questionText);
			if (!$status) {
				$error = 'Не возможно отправить сообщение';
			}
		} else {
			$error = 'Не получен текст сообщения';
		}

		$this->renderJsonAnswer($status, $error);
	}

	/**
	 * Действие "Акцепт оферты"
	 */
	public function actionAcceptOfferApply()
	{
		$status = false;
		$user = \Yii::app()->user;

		$offerAcceptedTimestamp = date('Y-m-d H:i:s');
		$xRealIp = isset($_SERVER['HTTP_X_REAL_IP']) ? $_SERVER['HTTP_X_REAL_IP'] : '0.0.0.0';
		$remoteAddress = $_SERVER['REMOTE_ADDR'];

		if ((!$xRealIp && !$remoteAddress) || !preg_match("/^\d+\.\d+\.\d+\.\d+$/", $xRealIp) || !preg_match("/^\d+\.\d+\.\d+\.\d+$/", $remoteAddress)) {
			$user->setFlash(
				'error',
				"Не удалось определить IP адрес ($xRealIp, $remoteAddress) или он передан неверно. Пожалуйста, попробуйте, принять оферту с другого устройства"
			);
		} else {
			$partner = $this->_partner;

			$partner->offer_accepted = 1;
			$partner->offer_accepted_timestamp = $offerAcceptedTimestamp;
			$partner->offer_accepted_from_addresses = "X-R: $xRealIp, R-ADDR: $remoteAddress";

			$status = $partner->save();

			if (!$status) {
				$user->setFlash('error', 'Внутренняя ошибка сервиса. Пожалуйста, свяжитесь с администратором');
			}
		}

		\Yii::app()->request->redirect($status ? $user->returnUrl : '/pk/acceptOffer');
	}
}
