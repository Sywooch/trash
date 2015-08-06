<?php
use likefifa\components\helpers\ListHelper;

class SalonLkController extends FrontendController
{

	public $actions = array(
		'index'       => 'Информация о салоне',
		'address'     => 'Ваш адрес',
		'description' => 'Описание',
		'price'       => 'Услуги и цены',
		'photo'       => 'Фотографии салона',
		//'masters' 		=> 'Мастера салона',
		'password'    => 'Изменить пароль',

	);

	public function actionIndex()
	{
		if (isset(Yii::app()->session['firstTime'])) {
			unset(Yii::app()->session['firstTime']);
			$this->firstTime = 'salon';
		}
		$model = $this->loadModel();
		$model->specIds = $model->getRelationIds('specializations');
		$model->scenario = 'salonLkIndex';

		if (isset($_POST['LfSalon'])) {

			$model->attributes = $_POST['LfSalon'];
			if ($model->save()) {

				if ((isset($_FILES['LfSalon']['name']['logo']) && ($_FILES['LfSalon']['name']['logo'] != ''))) {
					$model->uploadFile();
				}

				if (isset($_POST['redirect_link'])) {
					$redirect_link = $_POST['redirect_link'];
					header("Location: $redirect_link");
				} else {
					$this->redirect(array('address'));
				}

			}
		}
		$this->render('index', compact('model'));
	}

	public function actionPassword()
	{
		$model = $this->loadModel();
		$model->setScenario('changePassword');

		if (isset($_POST['LfSalon'])) {
			$model->attributes = $_POST['LfSalon'];

			if ($model->save()) {
				if (isset($_POST['redirect_link'])) {
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

	public function actionPrice()
	{
		$model = $this->loadModel();

		if (isset($_POST['LfSalon'])) {

			$model->applyPrices($_POST['LfSalon']['prices']);
			if (isset($_POST['redirect_link'])) {
				$redirect_link = $_POST['redirect_link'];
				header("Location: $redirect_link");
			} else {
				$this->redirect(array('photo'));
			}
		}

		$this->render('price', compact('model'));
	}

	public function actionDescription()
	{
		$model = $this->loadModel();

		if (isset($_POST['LfSalon'])) {
			$model->attributes = $_POST['LfSalon'];

			if ($model->save()) {
				if (isset($_POST['redirect_link'])) {
					$redirect_link = $_POST['redirect_link'];
					header("Location: $redirect_link");
				} else {
					$this->redirect(array('price'));
				}
			}
		}

		$this->render('description', compact('model'));

	}

	public function actionRules()
	{
		$model = $this->loadModel();
		$this->render('rules', compact('model'));
	}

	public function actionAddress()
	{
		$model = $this->loadModel();
		$model->departureDistrictIds = $model->getRelationIds('departureDistricts');
		$model->scenario = 'salonLkAddress';
		if (isset($_POST['LfSalon'])) {
			$model->attributes = $_POST['LfSalon'];
			if ($model->undergroundStation &&
				!$_POST['LfSalon']['departureDistrictIds'] &&
				$model->undergroundStation->district
			) {
				$_POST['LfSalon']['departureDistrictIds'][] = $model->undergroundStation->district->id;
			}
			if ($_POST['City']) {
				$model->underground_station_id = null;
				unset($_POST['LfSalon']['departureDistrictIds']);
			} else {
				$model->city_id = 1;
			}
			//$model->departureDistricts = isset($_POST['LfMaster']['departureDistrictIds']) ? $_POST['LfMaster']['departureDistrictIds'] : array();

			if ($model->save()) {
				$oldDistrictIds = ListHelper::buildPropList('id', $model->departureDistricts);
				$districtIds =
					!empty($_POST['LfSalon']['departureDistrictIds']) ? $_POST['LfSalon']['departureDistrictIds']
						: array();

				if ($model->city_id == 1) {
					$deleteDistricts =
						LfSalonDistrict::model()->findAll(
							'salon_id = ' .
							$model->id .
							($districtIds ? ' AND district_id NOT IN (' . implode(',', $districtIds) . ')' : '')
						);
				} else {
					$deleteDistricts = LfSalonDistrict::model()->findAll();
				}
				foreach ($deleteDistricts as $district) {
					$district->delete();
				}

				foreach ($districtIds as $districtId) {
					if (!in_array($districtId, $oldDistrictIds)) {
						$district = new LfSalonDistrict;
						$district->salon_id = $model->id;
						$district->district_id = $districtId;
						$district->save();
					}
				}

				if (isset($_POST['redirect_link'])) {
					$redirect_link = $_POST['redirect_link'];
					header("Location: $redirect_link");
				} else {
					$this->redirect(array('description'));
				}

			}
		}
		$this->render('address', compact('model'));
	}

	public function actionMasters()
	{
		$model = $this->loadModel();

		$criteria = array(
			'condition' => array(),
			'params'    => array(),
			'with'      => array(),
			'order'     => 'rating DESC',
			'group'     => 't.id'
		);
		$criteria['condition'] = 'salon_id = ' . $model->id;

		$dataProvider = new CActiveDataProvider('LfMaster', array(
			'criteria'   => $criteria,
			'pagination' => array(
				'pageSize' => 10,
				'pageVar'  => 'page',
			),
		));

		$this->render('masters', compact('model', 'dataProvider'));

	}

	public function actionEditMaster($id = null)
	{
		$salon = $this->loadModel();
		$model = $this->loadMaster($id);
		$model->scenario = 'SalonlkMaster';
		$salonGroups = array();
		foreach ($salon->specializations as $spec) {
			foreach ($spec->group as $group) {
				$salonGroups[$group->id] = $group->name;
			}
		}

		$salonGroups = array_unique($salonGroups);

		if ($model->isNewRecord) {
			$model->gender = $model::GENDER_FEMALE;
		}

		if (isset($_POST['LfMaster'])) {

			$model->attributes = $_POST['LfMaster'];
			$model->underground_station_id = $salon->underground_station_id;
			$model->city_id = $salon->city_id;
			if ($model->save()) {
				$model->scenario = 'SalonlkMaster';
				$model->uploadFile();
				$oldGroupId = $model->group ? $model->group->id : null;
				$groupId = !empty($_POST['group']) ? $_POST['group'] : array();

				$deleteGroups =
					LfMasterGroup::model()->findAll(
						'master_id = ' . $model->id . ($groupId ? ' AND group_id NOT IN (' . $groupId . ')' : '')
					);

				foreach ($deleteGroups as $group) {
					$group->delete();
				}

				if ($groupId !== $oldGroupId) {
					$group = new LfMasterGroup;
					$group->master_id = $model->id;
					$group->group_id = $groupId;
					$group->save();
				}

				$model->applyEducations(isset($_POST['education']) ? $_POST['education'] : array());

				if (isset($_POST['redirect_link'])) {
					$redirect_link = $_POST['redirect_link'];
					header("Location: $redirect_link");
				} else {
					$this->redirect(array('masters'));
				}

			}
		}

		$this->render('edit-master', compact('salon', 'model', 'salonGroups'));

	}

	public function actionEditWork($master_id, $work_id = null)
	{
		$model = $this->loadModel();
		$master = $this->loadMaster($master_id);

		$work = $this->loadWork($master_id, $work_id);

		$work->scenario = $work->id ? 'update' : 'create';

		if (isset($_POST['LfWork'])) {

			$work->attributes = $_POST['LfWork'];
			if ($work->save()) {
				$work->uploadFile();

				if (isset($_POST['redirect_link'])) {
					$redirect_link = $_POST['redirect_link'];
					header("Location: $redirect_link");
				} else {
					$this->redirect(array('editmaster', 'id' => $master_id));
				}
			}
		}

		$this->render('edit-work', compact('master', 'model', 'work'));
	}

	public function actionDeleteWork($master_id, $work_id)
	{
		$model = $this->loadWork($master_id, $work_id);

		if ($model->id) {
			$model->delete();
		}

		if (isset($_POST['redirect_link'])) {
			$redirect_link = $_POST['redirect_link'];
			header("Location: $redirect_link");
		} else {
			$this->redirect(array('editmaster', 'id' => $master_id));
		}
	}

	public function actionPhoto()
	{
		$model = $this->loadModel();

		$masters = $model->masters;
		$works = array();
		foreach ($masters as $master) {
			$works = array_merge($works, $master->works);
		}
		$this->render('photo', compact('model', 'works'));
	}

	public function actionEditPhoto($id = null)
	{
		$salon = $this->loadModel();
		$photo = $this->loadPhoto($id);
		$photo->scenario = $photo->id ? 'update' : 'create';

		if (isset($_POST['LfSalonPhoto'])) {

			$photo->attributes = $_POST['LfSalonPhoto'];
			if ($photo->save()) {

				$photo->uploadFile();

				if (isset($_POST['redirect_link'])) {
					$redirect_link = $_POST['redirect_link'];
					header("Location: $redirect_link");
				} else {
					$this->redirect(array('photo'));
				}
			}
		}

		$this->render('edit-photo', compact('salon', 'photo'));
	}

	public function actionDeletePhoto($id)
	{
		$model = $this->loadPhoto($id);

		if ($model->id) {
			$model->delete();
		}

		if (isset($_POST['redirect_link'])) {
			$redirect_link = $_POST['redirect_link'];
			header("Location: $redirect_link");
		} else {
			$this->redirect(array('photo'));
		}
	}

	public function actionDeleteMaster($id)
	{
		$model = $this->loadMaster($id);

		if ($model->id) {
			$model->delete();
		}

		if (isset($_POST['redirect_link'])) {
			$redirect_link = $_POST['redirect_link'];
			header("Location: $redirect_link");
		} else {
			$this->redirect(array('masters'));
		}
	}

	public function actionAjax()
	{
		if (isset($_POST['LfMaster']) && !empty($_POST['group'])) {
			$model = $this->loadMaster();
			$model->scenario = 'ajaxCreate';
			$model->attributes = $_POST['LfMaster'];
			$model->save();
			$groupId = $_POST['group'];
			$group = new LfMasterGroup;
			$group->master_id = $model->id;
			$group->group_id = $groupId;
			$group->save();
			echo $model->id;
		}
	}

	public function actionAppointment($status = 'new')
	{
		$model = $this->loadModel();
		$appointment = new LfAppointment('custom_search');
		switch ($status) {
			case 'new':
				$search = LfAppointment::STATUS_NEW;
				break;
			case 'apply':
				$search = LfAppointment::STATUS_ACCEPTED;
				break;
			case 'cancel':
				$search = array(LfAppointment::STATUS_REJECTED);
				break;
			case 'completed':
				$search = LfAppointment::STATUS_COMPLETED;
				break;
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
				$appointment->from_date = Yii::app()->request->cookies['from_date']->value;
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
				$appointment->to_date = Yii::app()->request->cookies['to_date']->value;
			}
		}
		if (isset($_POST['app_name'])) {
			Yii::app()->request->cookies['app_name'] = new CHttpCookie('app_name', $_POST['app_name']);
			$appointment->app_name = $_POST['app_name'];
		} else {
			if (isset(Yii::app()->request->cookies['app_name'])) {
				$appointment->app_name = Yii::app()->request->cookies['app_name']->value;
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

		$dataProvider = $appointment->custom_search($search, null, $model->id);
		$itemsCount = $appointment->getItemsCount(null, $model->id);

		if (isset($_POST['date_button'])) {
			$button = $_POST['date_button'];
		} else {
			$button = 'all';
		}

		$this->render('appointment', compact('model', 'appointment', 'dataProvider', 'status', 'button', 'itemsCount'));
	}

	public function actionUpdateStatus()
	{
		$status = $_GET['status'];
		$id = $_GET['id'];
		$appointment = LfAppointment::model()->findByPk($id);
		$act = $_GET['act'];
		switch ($act) {
			case 'apply':
				$appointment->status = LfAppointment::STATUS_ACCEPTED;
				$appointment->touch()->save();
				$this->redirect(array('salonlk/appointment/' . $status));
				break;
			case 'cancel':
				$appointment->status = LfAppointment::STATUS_REJECTED;
				$appointment->touch()->save();
				$this->redirect(array('salonlk/appointment/' . $status));
				break;
			case 'complete':
				$appointment->status = LfAppointment::STATUS_COMPLETED;
				$appointment->touch()->save();
				$this->redirect(array('salonlk/appointment/' . $status));
				break;
		}
	}

	/**
	 * Залогиненый салон
	 *
	 * @return LfSalon
	 */
	protected function loadModel()
	{
		if (!$this->loggedSalon) {
			$this->redirect(array('error'));
		}
		return $this->loggedSalon;
	}

	/**
	 * Выводит на экран ошибку, что не залогирован салон
	 */
	public function actionError()
	{
		$this->render('error', compact('model'));
	}

	protected function loadMaster($id = null)
	{
		$salon = $this->loadModel();

		if ($id) {
			$model = LfMaster::model()->findByPk($id, 'salon_id = ?', array($salon->id));
			if ($model === null) {
				throw new CHttpException(404, 'The requested master does not exist.');
			}
		} else {
			$model = new LfMaster();
			$model->salon_id = $salon->id;
			$model->email = uniqid();
			$model->password = '123';
		}

		return $model;

	}

	/**
	 * Получается работа мастера
	 *
	 * @param int $master_id идентификатор мастера
	 * @param int $work_id   идентификатор работы мастера
	 *
	 * @return LfWork
	 */
	protected function loadWork($master_id, $work_id = null)
	{
		$master = $this->loadMaster($master_id);

		if ($work_id) {
			$model = LfWork::model()->findByPk($work_id, 'master_id = ?', array($master->id));
		} else {
			$model = new LfWork();
			$model->master_id = $master->id;
		}

		if ($model === null) {
			$this->redirect(array('editmaster', 'id' => $master_id));
		}

		return $model;
	}

	protected function loadPhoto($id = null)
	{
		$salon = $this->loadModel();

		if ($id) {
			$model = LfSalonPhoto::model()->findByPk($id, 'salon_id = ?', array($salon->id));
			if ($model === null) {
				throw new CHttpException(404, 'The requested photo does not exist.');
			}
		} else {
			$model = new LfSalonPhoto();
			$model->salon_id = $salon->id;
		}

		return $model;
	}
}