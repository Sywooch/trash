<?php

namespace dfs\docdoc\back\controllers;

use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\models\DoctorModel;
use dfs\docdoc\models\ModerationModel;
use CHttpException;
use dfs\docdoc\models\SectorModel;
use Yii;

/**
 * Файл класса DoctorController.
 *
 * Контроллер врачей
 *
 * @package dfs.docdoc.back.controllers
 */
class DoctorController extends BackendController
{

	/**
	 * Врачи
	 *
	 * @throws \CException
	 * @throws \CHttpException
	 */
	public function actionDelete()
	{
		if (!Yii::app()->request->isAjaxRequest) {
			throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
		}

		if (Yii::app()->request->getParam('id') && Yii::app()->request->getParam('dublId')) {
			$id = Yii::app()->request->getParam('id');
			$dublId = Yii::app()->request->getParam('dublId');
			$doctor = DoctorModel::model()->findByPk($dublId);
			if (is_null($doctor)) {
				return false;
			}
			return $doctor->deleteAsDublicate($id);
		} else {
			$this->renderPartial('delete');
		}
	}

	/**
	 * Получение врача
	 *
	 * @throws \CHttpException
	 */
	public function actionGetItems()
	{
		if (!Yii::app()->request->isAjaxRequest) {
			throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
		}

		$id = Yii::app()->request->getParam('q');
		$doctor = DoctorModel::model()->findByPk($id);

		if (!is_null($doctor)) {
			echo "{$doctor->name} | {$doctor->id}";
		}
	}

	/**
	 * Модерация измененных полей
	 *
	 * @throws \CException
	 * @throws \CHttpException
	 */
	public function actionModeration()
	{
		if (!Yii::app()->request->isAjaxRequest) {
			throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
		}

		$id = Yii::app()->request->getParam('id');
		$doctor = $id ? DoctorModel::model()->findByPk($id) : null;
		if (!$doctor) {
			throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
		}

		$moderation = ModerationModel::getForRecord($doctor);
		$data = $moderation->data;

		$changes = [];
		if (array_key_exists('name', $data)) {
			$changes['name'] = [
				'name' => 'Имя доктора',
				'old'  => $doctor->name,
				'new'  => $data['name'],
			];
		}
		if (array_key_exists('clinics', $data)) {
			$old = \CHtml::listData($doctor->clinics, 'id', 'name');
			$new = \CHtml::listData(ClinicModel::model()->findAllByPk($data['clinics']), 'id', 'name');
			$changes['clinics'] = [
				'name' => 'Клиники',
				'old'  => implode('<br/>', $old),
				'new'  => implode('<br/>', $new),
			];
		}
		if (array_key_exists('sectors', $data)) {
			$old = \CHtml::listData($doctor->sectors, 'id', 'name');
			$new = \CHtml::listData(SectorModel::model()->findAllByPk($data['sectors']), 'id', 'name');
			$changes['sectors'] = [
				'name' => 'Специальность',
				'old'  => implode('<br/>', $old),
				'new'  => implode('<br/>', $new),
			];
		}
		if (array_key_exists('price', $data)) {
			$changes['price'] = [
				'name' => 'Стоимость приема',
				'old'  => $doctor->price,
				'new'  => $data['price'],
			];
		}
		if (array_key_exists('special_price', $data)) {
			$changes['special_price'] = [
				'name' => 'Спеццена',
				'old'  => $doctor->special_price,
				'new'  => $data['special_price'],
			];
		}
		if (array_key_exists('departure', $data)) {
			$changes['departure'] = [
				'name' => 'Вызов на дом',
				'old'  => $doctor->departure ? 'Да' : 'Нет',
				'new'  => $data['departure'] ? 'Да' : 'Нет',
			];
		}
		if (array_key_exists('kids_reception', $data)) {
			$changes['kids_reception'] = [
				'name' => 'Прием детей',
				'old'  => $doctor->kids_reception ? 'Да' : 'Нет',
				'new'  => $data['kids_reception'] ? 'Да' : 'Нет',
			];
		}
		if (array_key_exists('status', $data)) {
			$changes['status'] = [
				'name' => 'Статус',
				'old'  => DoctorModel::getStatusTitle($doctor->status),
				'new'  => DoctorModel::getStatusTitle($data['status']),
			];
		}

		if ($moderation->is_new) {
			foreach ($changes as &$v) {
				$v['old'] = '';
			}
		}

		$this->renderPartial('moderation', [
				'doctor' => $doctor,
				'changes' => $changes
			]);
	}

	/**
	 * Применение измененний
	 *
	 * @throws \CException
	 * @throws \CHttpException
	 */
	public function actionModerationApply()
	{
		$request = Yii::app()->request;
		if (!$request->isAjaxRequest) {
			throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
		}

		$errorMsg = null;
		$id = $request->getParam('id');
		$doctor = $id ? DoctorModel::model()->findByPk($id) : null;

		if ($doctor) {
			$reset = $request->getParam('reset');
			$apply = $request->getParam('apply');
			$reset = is_array($reset) ? array_keys($reset) : [];
			$apply = is_array($apply) ? array_keys($apply) : [];

			if ($reset || $apply) {
				$moderation = ModerationModel::getForRecord($doctor);
				$moderation->resetFields($reset);
				if (!$moderation->saveWithRecordChanges($doctor, $apply)) {
					$errorMsg = 'Ошибка сохранения';
				}
			} else {
				$errorMsg = 'Не найдены изменения';
			}
		} else {
			$errorMsg = 'Доктор не найден';
		}

		$this->renderJSON([
				'success' => $errorMsg === null,
				'errorMsg' => $errorMsg,
			]);
	}
}
