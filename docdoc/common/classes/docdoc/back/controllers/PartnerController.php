<?php

namespace dfs\docdoc\back\controllers;

use dfs\docdoc\helpers\PasswordHelper;
use dfs\docdoc\models\CityModel;
use dfs\docdoc\models\PartnerModel;
use dfs\docdoc\models\MailQueryModel;
use CHttpException;
use Yii;
use CHtml;

/**
 * Файл класса PartnerController.
 *
 * Контроллер партнеров
 *
 * @author  Mikhail Vasilyev <mvasilyev@docdoc.ru>
 * @link    https://docdoc.atlassian.net/browse/DD-21
 * @package dfs.docdoc.back.controllers
 */
class PartnerController extends BackendController
{

	/**
	 * Создает новую модель.
	 *
	 * @return void
	 */
	public function actionCreate()
	{
		$model = new PartnerModel(PartnerModel::SCENARIO_ADMIN);

		$post = Yii::app()->request->getPost(CHtml::modelName($model));

		if ($post) {
			if (!empty($post['login']) && empty($post['password'])) {
				$post['password'] = PasswordHelper::generate(8);
			}

			$model->attributes = $post;

			$transaction = Yii::app()->db->beginTransaction();

			try {
				if ($model->save()) {
					$model->updatePartnerPhones(Yii::app()->request->getPost("partnerPhonesList"));
					if (!empty($post['login'])) {
						$this->sendActivateMail($model, $post['password']);
					}

					$transaction->commit();
					$this->redirect(["index"]);
				}
			} catch (\Exception $e) {
				$transaction->rollback();
			}
		}

		$this->breadcrumbs = [
			'Партнеры' => ['index'],
			'Добавление партнера',
		];

		$h1 = "Добавление партнера";

		$this->render(
			"form",
			[
				"model"             => $model,
				"h1"                => $h1,
				"partnerPhonesList" => $this->_getPartnerPhonesList($model)
			]
		);
	}

	/**
	 * Обновлеет конкретную модель.
	 *
	 * @param integer $id идентификатор модели, которая будет редактироваться
	 *
	 * @return void
	 */
	public function actionUpdate($id)
	{
		$model = $this->loadModel($id);

		$post = Yii::app()->request->getPost(CHtml::modelName($model));

		if ($post) {
			$loginPrev = $model->login;

			// Если заведён новый логин, то генерируем пароль, если его не заполнили
			if (!$loginPrev && empty($post['password'])) {
				$post['password'] = PasswordHelper::generate(8);
			}

			$model->attributes = $post;

			$transaction = Yii::app()->db->beginTransaction();

			try {
				if ($model->save()) {
					$model->updatePartnerPhones(Yii::app()->request->getPost("partnerPhonesList"));
					if (!$loginPrev) {
						$this->sendActivateMail($model, $post['password']);
					}

					$transaction->commit();
					$this->redirect(['index']);
				}
			} catch (\Exception $e) {
				$transaction->rollback();
			}
		}

		$this->breadcrumbs = [
			'Партнеры' => ['index'],
			'Редактирование партнера',
		];

		$h1 = "Редактирование партнера №{$model->id}";

		$this->render(
			"form",
			[
				"model"             => $model,
				"h1"                => $h1,
				"partnerPhonesList" => $this->_getPartnerPhonesList($model)
			]
		);
	}

	/**
	 * Получает массив для редактирования телефонов
	 *
	 * @param PartnerModel $model
	 *
	 * @return array
	 */
	private function _getPartnerPhonesList($model)
	{
		$visible = [];
		$invisible = [];

		$partnerPhones = $model->phones;
		if (!$partnerPhones) {
			if (!$model->city_id) {
				foreach (CityModel::model()->active()->ordered()->findAll() as $city) {
					$visible[] = [
						"city" => $city,
						"phone" => ""
					];
				}
			} else {
				$visible[] = [
					"city" => $model->city,
					"phone" => ""
				];
				foreach (CityModel::model()->ordered()->active()->notInIds([$model->city_id])->findAll() as $city) {
					$invisible[] = [
						"city" => $city,
						"phone" => ""
					];
				}
			}
		} else {
			$notIn = [];

			foreach ($partnerPhones as $partnerPhone) {
				$visible[] = [
					"city"  => $partnerPhone->city,
					"phone" => $partnerPhone->phone->getPhone()->prettyFormat('+7 ')
				];
				$notIn[] = $partnerPhone->city_id;
			}
			foreach (CityModel::model()->ordered()->active()->notInIds($notIn)->findAll() as $city) {
				$invisible[] = [
					"city"  => $city,
					"phone" => ""
				];
			}
		}

		return [
			"visible"   => $visible,
			"invisible" => $invisible
		];
	}

	/**
	 * Удаляет модель
	 *
	 * @param integer $id идентификатор модели, которая будет удаляться
	 *
	 * @throws CHttpException
	 *
	 * @return void
	 */
	public function actionDelete($id)
	{
		if (!Yii::app()->request->isPostRequest) {
			throw new CHttpException(400, 'Неверный запрос');
		}

		$this->loadModel($id)->delete();
		$this->redirect(["index"]);
	}

	/**
	 * Список моделей
	 *
	 * @return void
	 */
	public function actionIndex()
	{
		$model = new PartnerModel('search');
		$model->unsetAttributes();

		$get = Yii::app()->request->getQuery(CHtml::modelName($model));

		if ($get) {
			$model->attributes = $get;
		}

		$this->render("index", ['model' => $model]);
	}

	/**
	 * Получает модель по идентификатору
	 *
	 * @param int $id идентификатор модели, которая будет загружаться
	 *
	 * @throws CHttpException
	 *
	 * @return PartnerModel
	 */
	public function loadModel($id)
	{
		$model = PartnerModel::model()->findByPk($id);

		if (!$model) {
			throw new CHttpException(404, 'Партнера с данным идентификатором не существует');
		}

		$model->setScenario(PartnerModel::SCENARIO_ADMIN);

		return $model;
	}


	/**
	 * Отправка уведомления об активации партнёра
	 *
	 * @param PartnerModel $partner
	 * @param string $password
	 *
	 * @return bool
	 */
	protected function sendActivateMail($partner, $password)
	{
		if ($partner->contact_email && $partner->login && $password) {
			return MailQueryModel::model()->createMail('partner_activate', $partner->contact_email, [
				'partner' => $partner,
				'password' => $password,
			]);
		}

		return false;
	}
}
