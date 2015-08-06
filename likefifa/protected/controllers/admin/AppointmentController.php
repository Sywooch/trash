<?php

use likefifa\models\AdminModel;
use likefifa\models\forms\LfAppointmentAdminFilter;
use likefifa\models\LfAppointmentFavorite;

class AppointmentController extends BackendController
{
	public function accessRules()
	{
		return CMap::mergeArray(
			[
				[
					'allow',
					'actions' => [
						'favorite',
						'changeOwner',
						'linkByMaster',
					],
					'users'   => AdminModel::model()->getAdminsForThisController($this->id),
				]
			],
			parent::accessRules()
		);
	}

	/**
	 * Создание заявки
	 *
	 * @param LfAppointment $model
	 */
	public function actionCreate($model = null)
	{
		if ($model == null) {
			$model = new LfAppointment;
			$this->pageTitle = 'Создание заявки';
			$this->breadcrumbs = array(
				'Заявки' => array('index'),
				'Создание',
			);
		}

		$model->scenario = 'admin';
		$model->is_viewed = 1;
		$model->create_source = $model::SOURCE_BO;

		if (isset($_POST['LfAppointment'])) {
			$model->attributes = $_POST['LfAppointment'];
			if ($model->validate() && $model->isNewRecord) {
				Yii::app()->gaTracking->trackEvent('zapis', 'click', 'click on BO');
			}
			if ($model->save()) {
				$this->saveRedirect();
			}
		}
		if (Yii::app()->request->getQuery('master_id')) {
			$model->master_id = Yii::app()->request->getQuery('master_id');
		}

		$logsDataProvider = null;
		if(!$model->isNewRecord) {
			$criteria = new CDbCriteria;
			$criteria->order = 't.created DESC';
			$criteria->compare('appointment_id', $model->id);
			$logsDataProvider = new CActiveDataProvider(
				'likefifa\models\LfAppointmentLog', [
					'criteria' => $criteria,
					'pagination' => false,
				]
			);
		}

		$this->render('form', compact('model', 'logsDataProvider'));
	}

	/**
	 * Изменение заявки
	 *
	 * @param integer $id ID заявки
	 */
	public function actionUpdate($id)
	{
		$model = $this->loadModel($id);
		$model->scenario = 'admin';

		$model->is_viewed = 1;
		$model->save(false, ['is_viewed']);

		$this->pageTitle = 'Заявка № ' . $model->id;
		$this->breadcrumbs = array(
			'Заявки' => array('index'),
			$model->id,
		);

		$oldAttributes = null;
		if ($model->isNewRecord == false) {
			$oldAttributes = $model->attributes;
		}

		if (isset($_POST['LfAppointment'])) {
			$model->attributes = $_POST['LfAppointment'];
			if ($model->save()) {
				$smsSend = $model->checkSendClientSms($oldAttributes);
				if ($smsSend == true) {
					$sms_model = new Sms;
					$sms_model->makeNewSmsForMasterByAdminAndAppointmentId($model->id);
					$sms_model = new Sms;
					$sms_model->makeAcceptedSmsForClientByAppointmentId($model->id);
				}
				$this->saveRedirect();
			}
		}

		$this->actionCreate($model);
	}

	/**
	 * Помечает заявку как удаленную @see LfAppointment::STATUS_REMOVED
	 *
	 * @param integer $id идентификатор заявки
	 *
	 * @throws CHttpException
	 */
	public function actionDelete($id)
	{
		if (Yii::app()->request->isPostRequest) {
			$model = $this->loadModel($id);
			$model->status = LfAppointment::STATUS_REMOVED;
			$model->save(false);

			if (!isset($_GET['ajax'])) {
				$this->saveRedirect();
			}
		} else {
			throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
		}
	}

	/**
	 * Список всех заявок
	 *
	 * @return void
	 */
	public function actionIndex()
	{
		// Изменение мастера у заявки
		$appointmentId = Yii::app()->request->getPost("appointmentId");
		$change = Yii::app()->request->getPost("change");
		if ($appointmentId && $change) {
			$model = LfAppointment::model()->findByPk($appointmentId);
			$master_id = Yii::app()->request->getPost('master_id');
			$salon_id = Yii::app()->request->getPost('salon_id');
			if ($model) {
				$model->change($master_id, $salon_id);
			}
		}

		CHtml::setModelNameConverter(
			function ($model) {
				return $model->resolveClassName();
			}
		);

		$model = new LfAppointmentAdminFilter('search');

		$this->checkMassRemove($model);

		$model->unsetAttributes();
		$modelName = CHtml::modelName($model);
		if (Yii::app()->request->getQuery($modelName)) {
			$model->attributes = Yii::app()->request->getQuery($modelName);
		}

		$model->dbCriteria->order = 't.id DESC';

		$this->render("index", compact("model"));
	}

	/**
	 * @param $id
	 *
	 * @return LfAppointment
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model = LfAppointment::model()->resetScope()->findByPk($id);
		if ($model === null) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}
		return $model;
	}

	/**
	 * Выводит на экран данные по конкретному мастеру или салону для создания заявки в БО
	 * Телефон и список услуг
	 *
	 * @param int    $id   идентификатор мастера
	 * @param string $type master | salon
	 *
	 * @throws CHttpException
	 *
	 * @return void
	 */
	public function actionMasterData($id, $type = 'master')
	{
		$model = null;
		if ($type == 'master') {
			$model = LfMaster::model()->findByPk($id);
		} else {
			$model = LfSalon::model()->findByPk($id);
		}
		if (!$model) {
			throw new CHttpException("404", "Мастера или салона с таким идентификатором не существует.");
		}

		$json = array(
			"phone"    => $type == 'master' ? $model->phone_cell : $model->phone,
			"services" => CHtml::dropDownList("LfAppointment[service_id]", null, $model->getServiceListItems()),
		);

		echo json_encode($json);
	}

	/**
	 * Выводит на экран цену услуги
	 *
	 * @throws CHttpException
	 *
	 * @return void
	 */
	public function actionServicePrice($id, $serviceId, $type = 'master')
	{
		$model = null;
		if ($type == 'master') {
			$model = LfMaster::model()->findByPk($id);
		} else {
			$model = LfSalon::model()->findByPk($id);
		}
		if (!$model) {
			throw new CHttpException("404", "Мастера или салона с таким идентификатором не существует.");
		}

		$price = $model->getPriceForService($serviceId);
		if (!$price) {
			throw new CHttpException("500", "Не существует цены для данной услуги");
		}

		echo $price->price;
	}

	public function actionFavorite($id)
	{
		$model = $this->loadModel($id);
		$user = AdminModel::model()->findByAttributes(
			[
				'login' => Yii::app()->user->id
			]
		);

		$check = LfAppointmentFavorite::model()->findByAttributes(
			[
				'appointment_id' => $model->id,
				'admin_id'       => $user->id,
			]
		);

		if ($check != null) {
			$check->delete();
			echo 0;
		} else {
			$fav = new LfAppointmentFavorite;
			$fav->admin_id = $user->id;
			$fav->appointment_id = $model->id;
			$fav->save(false);
			echo 1;
		}
	}

	/**
	 * Смена мастера/салона для заявки
	 *
	 * @return void
	 */
	public function actionChangeOwner()
	{
		$appointmentId = Yii::app()->request->getQuery('id');

		$mastersData = [];
		$masters = Yii::app()->db->createCommand()
			->select('id, name, surname')
			->from('lf_master')
			->where('is_blocked = 0 AND is_published = 1')
			->order("name")
			->queryAll();
		foreach ($masters as $master) {
			$mastersData[$master['id']] = $master["name"] . " " . $master["surname"];
		}

		$salonsData = [];
		$salons = Yii::app()->db->createCommand()
			->select('id, name')
			->from('lf_salons')
			->where('is_published = 1')
			->order("name")
			->queryAll();
		foreach ($salons as $salon) {
			$salonsData[$salon['id']] = $salon['name'];
		}

		$this->renderPartial('change_owner', compact('appointmentId', 'mastersData', 'salonsData'));
	}

	/**
	 * Возвращает объединенный комментарий для тултипа грида
	 *
	 * @param LfAppointment $model
	 *
	 * @return string
	 */
	public function getMergedCommend($model)
	{
		$text = "";
		if (!empty($model->operator_comment)) {
			$text = 'Комментарий оператора: ' . $model->operator_comment;
		}

		if (!empty($model->reason)) {
			$text .= (!empty($text) ? '<br/>' : '') . 'Комментарий мастера: ' . $model->reason;
		}

		return $text ?
			'<i class="fa fa-file-text-o" data-html="true" data-toggle="tooltip" title="' .
			CHtml::encode($text) .
			'"></i>' : '';
	}

	/**
	 * Генерирует ссылку на подбор мастера
	 *
	 * @param LfAppointment $data
	 *
	 * @return array
	 */
	public function getAppointmentUrl($data)
	{
		$params = [
			'appointment_id' => $data->id,
			'query'          => $data->service_id ? $data->service->name : '',
			'service'        => $data->service_id,
			'specialization' => $data->specialization_id,
		];
		if ($data->underground_station_id != null) {
			$params['stations'] = $data->underground_station_id;
			$params['metro-suggest'] = $data->undergroundStation->name;
		}
		return Yii::app()->createUrl('/admin/masterSearch', $params);
	}

	public function actionLinkByMaster($appointment_id, $master_id)
	{
		$model = $this->loadModel($appointment_id);
		$model->master_id = $master_id;
		$model->save();
		$this->redirect(['index']);
	}

	/**
	 * @param CActiveRecord $model
	 *
	 * @return boolean
	 */
	protected function checkMassRemove($model)
	{
		$massRemove = Yii::app()->request->getPost('massRemove', false);
		$ids = Yii::app()->request->getPost('ids', []);
		if(!$massRemove || count($ids) == 0) {
			return false;
		}

		$criteria = new CDbCriteria;
		$criteria->addInCondition('t.id', $ids);
		$data = $model->findAll($criteria);
		foreach($data as $row) {
			$row->status = LfAppointment::STATUS_REMOVED;
			$row->saveAttributes(['status']);
		}

		echo CJSON::encode(['status' => 'success']);
		Yii::app()->end();

		return true;
	}
}
