<?php

use likefifa\components\helpers\AppHelper;
use likefifa\extensions\image\Image;

class LkController extends FrontendController
{
	public $actions = array(
		'index'     => 'Личная информация',
		'address'   => 'Ваш адрес',
		'price'     => 'Прайс-лист',
		'education' => 'Образование',
		'works'     => 'Фотографии работ',
		'schedule'  => 'График работы',
		'password'  => 'Изменить пароль',
	);

	/**
	 * Залогиненый мастер
	 *
	 * @return LfMaster
	 */
	protected function loadModel()
	{
		if (!$this->loggedMaster) {
			$this->redirect(array('error'));
		}
		$this->loggedMaster->email = trim($this->loggedMaster->email);
		return $this->loggedMaster;
	}

	/**
	 * Выводит на экран ошибку, что не залогирован мастер
	 */
	public function actionError()
	{
		$this->render('error', compact('model'));
	}

	protected function loadWork($id = null)
	{
		$master = $this->loadModel($id);

		if ($id) {
			$model = LfWork::model()->findByPk($id, 'master_id = ?', array($master->id));
			if ($model === null) {
				throw new CHttpException(404, 'The requested work does not exist.');
			}
		} else {
			$model = new LfWork();
			$model->master_id = $master->id;
		}

		return $model;
	}

	public function actionUpdateAppStatus()
	{
		if (isset($_POST['app_id'])) {
			$data = LfAppointment::model()->findByPk($_POST['app_id']);
			echo $data->statusList[$data->status];
		}
	}

	/**
	 * Производит действие и показ главной страницы ЛК
	 *
	 * return void
	 */
	public function actionIndex()
	{
		if (isset(Yii::app()->session['firstTime'])) {
			unset(Yii::app()->session['firstTime']);
			$this->firstTime = 'master';
		}
		$model = $this->loadModel();

		$model->scenario = 'lkIndex';
		if (isset($_POST['LfMaster'])) {
			$model->attributes = $_POST['LfMaster'];
			if ($model->save()) {
				$model->uploadFile();
				if ($_POST['redirect_link']) {
					$redirect_link = $_POST['redirect_link'];
					header("Location: $redirect_link");
				} else {
					$this->redirect(array('address'));
				}
			}
		}

		$this->render('index', compact('model'));
	}

	/**
	 * Производит действие и показ раздела "Ваш Адрес" в ЛК
	 *
	 * return void
	 */
	public function actionAddress()
	{
		$model = $this->loadModel();
		$model->departureDistrictIds = $model->getRelationIds('departureDistricts');
		$model->scenario = 'lkAddress';
		if (isset($_POST['LfMaster'])) {
			$model->attributes = $_POST['LfMaster'];
			if ($model->undergroundStation && !$model->district && $model->undergroundStation->district) {
				$model->district_id = $model->undergroundStation->district->id;
			}
			if ($_POST['City']) {
				$model->underground_station_id = null;
				$model->district_id = null;
				unset($_POST['LfMaster']['departureDistrictIds']);
			} else {
				$model->city_id = 1;
			}
			if (empty($_POST['LfMaster']['departure_to_all'])) {
				$model->departure_to_all = 0;
			}
			if ($model->save()) {
				if ($_POST['redirect_link']) {
					$redirect_link = $_POST['redirect_link'];
					header("Location: $redirect_link");
				} else {
					$this->redirect(array('price'));
				}
			}
		}

		$this->render('address', compact('model'));
	}

	/**
	 * Производит действие и показ раздела "Прайс-лист" в ЛК
	 *
	 * return void
	 */
	public function actionPrice()
	{
		$model = $this->loadModel();
		$model->departureDistrictIds = $model->getRelationIds('departureDistricts');
		if (isset($_POST['LfMaster'])) {
			if ($model->save()) {
				if ($_POST['redirect_link']) {
					$redirect_link = $_POST['redirect_link'];
					header("Location: $redirect_link");
				} else {
					$this->redirect(array('education'));
				}
			}
		}

		$this->render('price', compact('model'));
	}

	/**
	 * Образование
	 *
	 * @return void
	 */
	public function actionEducation()
	{
		$model = $this->loadModel();

		if (isset($_POST['LfMaster'])) {
			$model->attributes = $_POST['LfMaster'];

			if ($model->save()) {
				$model->applyEducations(isset($_POST['education']) ? $_POST['education'] : array());
				if ($_POST['redirect_link']) {
					$redirect_link = $_POST['redirect_link'];
					header("Location: $redirect_link");
				} else {
					$this->redirect(array('works'));
				}
			}
		}

		$this->render('education', compact('model'));
	}

	/**
	 * Работы
	 *
	 * @return void
	 */
	public function actionWorks()
	{
		$model = $this->loadModel();
		$this->render('works', compact('model'));
	}

	/**
	 * Загружает работы мастера
	 *
	 * @param int $id - идентификатор работы
	 *
	 * @return void
	 */
	public function actionEditWork($id = null)
	{
		$master = $this->loadModel();
		$model = $this->loadWork($id);

		if ($model && $master) {
			if (Yii::app()->request->isPostRequest) {

				$attributes = Yii::app()->request->getPost(CHtml::modelName($model));
				if ($attributes != null) {
					if ($model->isNewRecord) {
						// Новые работы
						foreach ($attributes as $attr) {
							if ($model->isNewRecord) {
								$work = new LfWork;
								$work->master_id = $master->id;
								$work->attributes = $attr;

								if ($work->save()) {
									// Сохранение фотографий при мультиаплоаде
									try {
										$crops = [];
										if (!empty($attr['x1'])) {
											$crops = [
												'x1'     => $attr['x1'],
												'y1'     => $attr['y1'],
												'x2'     => $attr['x2'],
												'y2'     => $attr['y2'],
												'height' => $attr['jcropHeight'],
												'width'  => $attr['jcropWidth'],
											];
										}
										if(!$work->saveImage($attr['tempImage'], $crops)) {
											$work->delete();
										}
										$work->generatePreview();
									} catch(Exception $e) {
										$work->delete();
									}

								}
							}
						}
						$this->redirect(['works']);
					} else {
						// Изменение работы
						$model->is_main = 0;
						$model->attributes = $attributes;
						$model->uploadFile();
						$model->generatePreview();
						$model->save();
						$this->redirect(['works']);
					}
				}
			}
		}
		$this->render('edit-work', compact('master', 'model'));
	}

	/**
	 * Загружает фотографию работы
	 */
	public function actionUploadWork()
	{
		$model = new LfWork();

		$filename = Yii::app()->request->getQuery('qqfile');
		$file_names = explode('.', $filename);
		$extension = strtolower(end($file_names));
		if ($extension != 'jpeg' && $extension != 'jpg' && $extension != 'gif' && $extension != 'png') {
			throw new CHttpException(502, 'Wrong file extension');
		}
		$name = md5(time() . rand(1, 10000)) . '.' . $extension;

		$path = $model->getTempPath() . $name;
		// Загрузка удаленной фотографии (импорт из VK)
		if (Yii::app()->request->getQuery('remote') != null) {
			$imageData = AppHelper::getByUrl($filename);

			file_put_contents($path, $imageData);

		} else {
			if (strpos(strtolower($_SERVER['CONTENT_TYPE']), 'multipart/') === 0) {
				move_uploaded_file($_FILES['qqfile']['tmp_name'], $path);
			} else {
				$input = fopen("php://input", "r");
				$temp = tmpfile();
				$realSize = stream_copy_to_stream($input, $temp);
				fclose($input);

				if ($realSize != $this->getSize()) {
					Yii::app()->end();
				}

				$target = fopen($path, "w");
				fseek($temp, 0, SEEK_SET);
				stream_copy_to_stream($temp, $target);
				fclose($target);
			}
		}

		// Сжимаем изображение, если очень большое
		$imagePath = $model->getTempPath() . $name;
		$is = @getimagesize($imagePath);
		if($is[0] > 2000 || $is[1] > 2000) {
			$image = new Image($imagePath);
			$image->resize(2000, 2000, $is[0] > $is[1] ? Image::WIDTH : Image::HEIGHT);
			$image->save();
		}

		$imageUrl = $model->getTempUrl() . '/' . $name;
		echo CJSON::encode(
			array(
				'success' => true,
				'path'    => $imageUrl,
				'name'    => $name,
			)
		);
	}

	/**
	 * Удаляет работу мастера
	 *
	 * @param int $id идентификатор работы
	 */
	public function actionDeleteWork($id)
	{
		$model = $this->loadWork($id);

		if ($model->id) {
			$model->delete();
		}

		$this->redirect(array('works'));
	}

	/**
	 * График работы
	 *
	 * @return void
	 */
	public function actionSchedule()
	{
		$model = $this->loadModel();

		if (isset($_POST['LfMaster'])) {
			$model->attributes = $_POST['LfMaster'];

			if ($model->save()) {

				if ($_POST['redirect_link']) {
					$redirect_link = $_POST['redirect_link'];
					header("Location: $redirect_link");
				} else {
					$this->redirect(array('password'));
				}
			}
		}

		$this->render('schedule', compact('model'));
	}

	public function actionPassword()
	{
		$model = $this->loadModel();
		$model->setScenario('changePassword');

		if (isset($_POST['LfMaster'])) {
			$model->attributes = $_POST['LfMaster'];

			if ($model->save()) {
				if ($_POST['redirect_link']) {
					$redirect_link = $_POST['redirect_link'];
					header("Location: $redirect_link");
				} else {
					$this->redirect(array('index'));
				}
			}
		}

		$model->password = '';
		$model->repeat_password = '';

		$this->render('password', compact('model'));
	}

	public function actionRules()
	{
		$model = $this->loadModel();
		$this->render('rules', compact('model'));
	}

	/**
	 * Выводит заявки на экран
	 *
	 * @param string $status тип статуса
	 */
	public function actionAppointment($status = 'new')
	{
		$model = $this->loadModel();
		if ($status == 'new') {
			$this->lkRefresh = true;
		}
		$appointment = new LfAppointment('custom_search');
		switch ($status) {
			case 'new':
				$search =
					[
						LfAppointment::STATUS_NEW,
						LfAppointment::STATUS_PROCESSING_BY_MASTER
					];
				break;
			case 'apply':
				$search = LfAppointment::STATUS_ACCEPTED;
				break;
			case 'cancel':
				$search = [
					LfAppointment::STATUS_REJECTED,
					LfAppointment::STATUS_REJECTED_AFTER_ACCEPTED
				];
				break;
			case 'completed':
				$search = LfAppointment::STATUS_COMPLETED;
				break;
		}
		if (!isset($_GET['page'])) {
			unset(Yii::app()->request->cookies['from_date']);
			unset(Yii::app()->request->cookies['to_date']);
			unset(Yii::app()->request->cookies['app_name']);
			$appointment->from_date = '';
			$appointment->to_date = '';
			$appointment->app_name = '';
		}
		if (isset($_GET['Appointment'])) {
			$appointment->attributes = $_GET['Appointment'];
		}
		if (isset($_POST['from_date'])) {
			Yii::app()->request->cookies['from_date'] = new CHttpCookie('from_date', strtotime($_POST['from_date']));
			$appointment->from_date = strtotime($_POST['from_date']);
			if (empty($_POST['from_date'])) {
				unset(Yii::app()->request->cookies['from_date']);
				$appointment->from_date = '';
			}
		} else {
			if (isset(Yii::app()->request->cookies['from_date'])) {
				$appointment->from_date = Yii::app()->request->cookies['from_date'];
			}
		}
		if (isset($_POST['to_date'])) {
			Yii::app()->request->cookies['to_date'] =
				new CHttpCookie('to_date', (strtotime($_POST['to_date']) + 86339)); // 24 * 60 * 60 - 1
			$appointment->to_date = strtotime($_POST['to_date']) + 86339;
			if (empty($_POST['to_date'])) {
				unset(Yii::app()->request->cookies['to_date']);
				$appointment->to_date = '';
			}
		} else {
			if (isset(Yii::app()->request->cookies['to_date'])) {
				$appointment->to_date = Yii::app()->request->cookies['to_date'];
			}
		}
		if (isset($_POST['app_name'])) {
			Yii::app()->request->cookies['app_name'] = new CHttpCookie('app_name', $_POST['app_name']);
			$appointment->app_name = $_POST['app_name'];
		} else {
			if (isset(Yii::app()->request->cookies['app_name'])) {
				$appointment->app_name = Yii::app()->request->cookies['app_name'];
			}
		}
		if (!empty($_POST['yt1'])) {
			unset(Yii::app()->request->cookies['from_date']);
			unset(Yii::app()->request->cookies['to_date']);
			unset(Yii::app()->request->cookies['app_name']);
			$appointment->from_date = '';
			$appointment->to_date = '';
			$appointment->app_name = '';
		}
		if (!isset($_GET['page'])) {
			$_GET['page'] = 1;
		}
		$dataProvider = $appointment->custom_search($search, $model->id, null, $status);
		$itemsCount = $appointment->getItemsCount($model->id);
		$master_id = $model->id;
		if (isset($_POST['date_button'])) {
			$button = $_POST['date_button'];
		} else {
			$button = 'all';
		}

		$this->render(
			'appointment',
			compact('model', 'appointment', 'dataProvider', 'status', 'button', 'itemsCount', 'master_id')
		);
	}

	public function actionUpdateStatus()
	{
		$status = $_GET['status'];
		$id = $_GET['id'];
		$act = $_GET['act'];
		$appointment = LfAppointment::model()->findByPk($id);

		if (!$appointment) {
			$this->redirect(array('lk/appointment/' . $status));
		}

		switch ($act) {
			case 'apply':
				$appointment->status = LfAppointment::STATUS_ACCEPTED;
				$appointment->touch()->save();
				$this->redirect(array('lk/appointment/' . $status));
				break;
			case 'cancel':
				$appointment->status = LfAppointment::STATUS_REJECTED;
				$appointment->touch()->save();
				$this->redirect(array('lk/appointment/' . $status));
				break;
			case 'complete':
				$appointment->status = LfAppointment::STATUS_COMPLETED;
				if (isset($_GET['service_price2'])) {
					$appointment->service_price2 = $_GET['service_price2'];
				}
				$appointment->touch()->save();
				$this->redirect(array('lk/appointment/' . $status));
				break;
		}
	}

	/**
	 * Пополнение счёта. Нужно передать параметр $amount
	 */
	public function actionTopUp()
	{
		$model = $this->loadModel();
		if (isset($_GET['amount'])) {
			$amount = (int)$_GET['amount'];
		}

		if (isset($_POST['amount'])) {
			$amount = (int)$_POST['amount'];
		}

		if (!isset($amount) || $amount <= 0) {
			throw new CHttpException(404, 'неверный запрос');
		}

		$invoice = $model->createInvoice($amount);
		$this->redirect($invoice->getProcessorUrl());
	}

	/**
	 * Выводит страницу "Как пополнить баланс"
	 *
	 * @return void
	 */
	public function actionBalance()
	{
		$model = $this->loadModel();
		$this->render('balance', compact("model"));
	}

	/**
	 * История счета
	 */
	public function actionPayments()
	{
		$model = $this->loadModel();

		$criteria = new CDbCriteria;
		$criteria->addCondition('account_from = :account_from');
		$criteria->params = [
			':account_from' => $model->getAccount()->id,
		];
		$criteria->order = 'create_date DESC';

		$dataProvider = new CActiveDataProvider(
			'\likefifa\models\lf\LfPaymentsOperations', [
				'criteria'   => $criteria,
				'pagination' => [
					'pageSize' => 20,
				]
			]
		);
		$this->render('payments', compact('model', 'dataProvider'));
	}

	/**
	 * Ищет в базе станцию метро в соответствии с линией и названией станции
	 *
	 * @param $line
	 * @param $station
	 */
	public function actionFindMetroByString($line, $station)
	{
		$line = trim(str_replace("линия", "", $line));
		$station = trim(str_replace("метро", "", $station));

		$criteria = new CDbCriteria;
		$criteria->with = [
			'undergroundLine',
			'district' => [
				'joinType' => 'LEFT JOIN',
			]
		];
		$criteria->compare('t.name', $station, true);
		$criteria->compare('undergroundLine.name', $line, true);

		$model = UndergroundStation::model()->find($criteria);

		if ($model != null) {
			echo $model->id;
		}
	}

	/**
	 * Изменяет статус "готов принимать заказы"
	 *
	 * @param $status
	 */
	public function actionChangeFreeStatus($status)
	{
		$model = $this->loadModel();
		$model->is_free = $status;
		$model->save();
	}
}