<?php

namespace dfs\docdoc\front\controllers;

use dfs\docdoc\models\MailQueryModel;
use dfs\docdoc\models\PartnerModel;


/**
 * Class AffiliateController
 *
 * @package dfs\docdoc\front\controllers
 */
class AffiliateController extends FrontController
{
	public $layout = 'landing';


	/**
	 * Страница лендинга для партнёров
	 */
	public function actionIndex()
	{
		$this->render('index');
	}

	/**
	 * Заявка на создание нового партнёра
	 */
	public function actionCreate()
	{
		$request = \Yii::app()->request;

		$partner = new PartnerModel(PartnerModel::SCENARIO_LANDING);

		$contacts = $request->getPost('contacts');

		$result = false;

		if ($contacts) {
			if (strpos($contacts, '@') === false) {
				$partner->contact_phone = $contacts;
			} else {
				$partner->contact_email = $contacts;
			}

			$partner->name = 'Новый партнёр';
			$partner->city_id = \Yii::app()->city->getCityId();

			$result = $partner->save();

			if ($result) {
				MailQueryModel::model()->createMail('partner_new_affiliate', \Yii::app()->params['email']['affiliate'], ['partner' => $partner]);

				if ($partner->contact_email) {
					MailQueryModel::model()->createMail('partner_new', $partner->contact_email, ['partner' => $partner]);
				}
			}
		} else {
			$partner->addError('contacts', 'Необходимо указать телефон или email');
		}

		if ($request->isAjaxRequest) {
			$this->renderJSON([
				'success' => $result,
				'error'  => $this->buildErrorMessageByRecord($partner),
			]);
		} else {
			$request->redirect('/affiliate');
		}
	}
}
