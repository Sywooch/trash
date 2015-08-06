<?php

namespace dfs\docdoc\back\controllers;

use dfs\docdoc\models\ClinicPartnerPhoneModel;
use dfs\docdoc\models\PhoneModel;
use CHttpException;
use Yii;
use CHtml;

/**
 * Файл класса ClinicPartnerPhoneController.
 *
 * Интерфейс добавления партнёрских телефонов в БО
 *
 * @author  Mikhail Vasilyev <mvasilyev@docdoc.ru>
 * @link    https://docdoc.atlassian.net/browse/DD-291
 * @package dfs.docdoc.back.controllers
 */
class ClinicPartnerPhoneController extends BackendController
{

	/**
	 * Создает новую модель.
	 *
	 * @return void
	 */
	public function actionCreate()
	{
		$model = new ClinicPartnerPhoneModel('backend');

		$post = Yii::app()->request->getPost(CHtml::modelName($model));
		if ($post) {
			$model->attributes = $post;
			$model->phone_id = PhoneModel::model()->getIdByNumber($post["phoneNumber"]);

			if ($model->save()) {
				$this->redirect(array("index"));
			}
		}

		$this->breadcrumbs = array(
			'Телефоны партнеров для клиник' => array('index'),
			'Добавление',
		);
		$h1 = "Добавление";

		$this->render("form", array("model" => $model, "h1" => $h1));
	}

	/**
	 * Обновлеет модель.
	 *
	 * @param integer $id         идентификатор клиники
	 * @param integer $partner_id идентификатор партнера
	 *
	 * @return void
	 */
	public function actionUpdate($id, $partner_id)
	{
		$model = $this->loadModel($id, $partner_id);

		$post = Yii::app()->request->getPost(CHtml::modelName($model));
		if ($post) {
			$model->attributes = $post;
			$model->phone_id = PhoneModel::model()->getIdByNumber($post["phoneNumber"]);

			if ($model->save()) {
				// Сохранение филиалов
				$isBranches = Yii::app()->request->getPost("isBranches");
				$branchClinic = Yii::app()->request->getPost("BranchClinic");
				if ($isBranches && $branchClinic) {
					foreach ($branchClinic as $clinic_id => $data) {
						$branchClinicPartnerPhoneModel = ClinicPartnerPhoneModel::model()->findByPk(
							array(
								"clinic_id"  => $clinic_id,
								"partner_id" => $data["partner_id"],
							)
						);
						if (!$branchClinicPartnerPhoneModel) {
							$branchClinicPartnerPhoneModel = new ClinicPartnerPhoneModel;
							$branchClinicPartnerPhoneModel->clinic_id = $clinic_id;
							$branchClinicPartnerPhoneModel->partner_id = $data["partner_id"];
						}
						$branchClinicPartnerPhoneModel->phone_id = PhoneModel::model()->getIdByNumber($data["phone"]);
						$branchClinicPartnerPhoneModel->save();
					}
				}

				$this->redirect(array('index'));
			}
		}

		$this->breadcrumbs = array(
			'Телефоны партнеров для клиник' => array('index'),
			'Редактирование',
		);
		$h1 = "Редактирование";

		$this->render("form", array("model" => $model, "h1" => $h1));
	}

	/**
	 * Удаляет модель
	 *
	 * @param integer $id         идентификатор клиники
	 * @param integer $partner_id идентификатор партнера
	 *
	 * @throws CHttpException
	 *
	 * @return void
	 */
	public function actionDelete($id, $partner_id)
	{
		if (!Yii::app()->request->isPostRequest) {
			throw new CHttpException(400, 'Неверный запрос');
		}

		$this->loadModel($id, $partner_id)->delete();
		$this->redirect(array("index"));
	}

	/**
	 * Список моделей
	 *
	 * @return void
	 */
	public function actionIndex()
	{
		$model = new ClinicPartnerPhoneModel('search');
		$model->unsetAttributes();

		$get = Yii::app()->request->getQuery(CHtml::modelName($model));
		if ($get) {
			$model->attributes = $get;
		}

		$this->render("index", array("model" => $model));
	}

	/**
	 * Получает модель по идентификатору
	 *
	 * @param integer $id         идентификатор клиники
	 * @param integer $partner_id идентификатор партнера
	 *
	 * @throws CHttpException
	 *
	 * @return ClinicPartnerPhoneModel
	 */
	public function loadModel($id, $partner_id)
	{
		$model = ClinicPartnerPhoneModel::model()->findByPk(array("clinic_id" => $id, "partner_id" => $partner_id));
		if (!$model) {
			throw new CHttpException(404, 'Модели с таким идентификатором не существует');
		}
		$model->setScenario('backend');

		return $model;
	}
}
