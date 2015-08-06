<?php

namespace dfs\docdoc\front\controllers;

use dfs\docdoc\models\ClientModel;
use Yii;
use CController;
use CException;
use CHttpException;
use CActiveForm;
use CHtml;

/**
 * Файл класса ClientController.
 *
 * Контроллер для работы с клиентами
 *
 * @author  Mikhail Vasilyev <mvasilyev@docdoc.ru>
 * @link    https://docdoc.atlassian.net/browse/DD-111
 * @package dfs.docdoc.front.controllers
 */
class ClientController extends CController
{

	/**
	 * Выводит на экран форму с заполнением e-mail после отправления заявки
	 *
	 * @throws CException
	 * @throws CHttpException
	 *
	 * @return void
	 */
	public function actionRequestEmail()
	{
		if (!Yii::app()->getRequest()->getIsAjaxRequest()) {
			throw new CHttpException(400, "Некорректный запрос");
		}

		$this->render("request_email");
	}

	/**
	 * Получает модель по идентификатору
	 *
	 * @param integer $id идентификатор модели
	 *
	 * @throws CException
	 *
	 * @return ClientModel
	 */
	public function loadModel($id)
	{
		$model = ClientModel::model()->findByPk($id);
		if ($model === null) {
			throw new CException('The requested page does not exist.');
		}
		return $model;
	}

	/**
	 * Сохраняет электронную почту клиента
	 *
	 * Выполняет AJAX проверку корректности e-mail адреса
	 * Используется при подачи заявки
	 * Выводит на экран:
	 * 0 - некорректный адрес
	 * 1 - корректный
	 *
	 * @throws CHttpException
	 *
	 * @return void
	 */
	public function actionSaveEmail()
	{
		if (!Yii::app()->getRequest()->getIsAjaxRequest()) {
			throw new CHttpException(400, "Некорректный запрос");
		}

		$id = Yii::app()->request->getPost("clientId");
		$email = Yii::app()->request->getPost("clientEmail");
		if (!$id || !$email) {
			throw new CHttpException(400, "Некорректный запрос");
		}

		$model = $this->loadModel($id);
		$model->setScenario("SAVE_EMAIL");
		$model->email = $email;

		echo ($model->save()) ? 1 : 0;
	}
}