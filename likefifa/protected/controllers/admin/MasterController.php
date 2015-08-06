<?php
use likefifa\models\forms\LfMasterAdminFilter;
use likefifa\models\forms\MasterMailForm;

class MasterController extends BackendController
{
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model = new LfMaster;
		$model->gender = LfMaster::GENDER_FEMALE; // грязный сексизм!
		$model->scenario = 'adminMaster';

		$this->breadcrumbs = array(
			'Мастера' => array('index'),
			'Создание мастера',
		);
		$this->pageTitle = 'Создание мастера';

		if (isset($_POST['LfMaster'])) {
			unset($_POST['LfMaster']['photo']);
			$model->attributes = $_POST['LfMaster'];
			if ($model->save()) {
				$model->applyEducations(isset($_POST['education']) ? $_POST['education'] : array());
				$model->uploadFile();
				$this->saveRedirect();
			}
		}

		$this->render('form', compact('model'));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id the ID of the model to be updated
	 *
	 * @return void
	 */
	public function actionUpdate($id)
	{
		$model = $this->loadModel($id);
		$model->scenario = 'adminMaster';

		$this->breadcrumbs = array(
			'Мастера' => array('index'),
			'Изменение мастера',
		);
		$this->pageTitle = 'Изменение мастера ' . $model->id;

		if (isset($_POST['LfMaster_delete_photo'])) {
			$model->deleteFile('photo');
			$model->photo = null;
		}
		if (isset($_POST['LfMaster'])) {
			unset($_POST['LfMaster']['photo']);
			$model->attributes = $_POST['LfMaster'];

			if ($model->save()) {
				$model->applyEducations(isset($_POST['education']) ? $_POST['education'] : array());
				$model->uploadFile();
				if (empty($_POST["LfMaster_stay-here"])) {
					$this->saveRedirect();
				}

			}
		}

		$mailForm = new MasterMailForm;
		$mailForm->master = $model;

		if(Yii::app()->request->getPost(CHtml::modelName($mailForm)) != null) {
			$mailForm->attributes = Yii::app()->request->getPost(CHtml::modelName($mailForm));
			if($mailForm->validate()) {
				$mailForm->send();
				$mailForm = new MasterMailForm;
				$mailForm->success = true;
			}
		}

		$this->render('form', compact('model', 'mailForm'));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 *
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		/*if(Yii::app()->request->isPostRequest)
		{*/
		// we only allow deletion via POST request
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if (!isset($_GET['ajax'])) {
			$this->saveRedirect();
		}
		/*}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');*/
	}

	/**
	 * Выводит на экран список мастеров
	 */
	public function actionIndex()
	{
		$this->pageTitle = 'Мастера';
		$model = new LfMasterAdminFilter('search');

		CHtml::setModelNameConverter(
			function ($model) {
				return $model->resolveClassName();
			}
		);

		$modelName = CHtml::modelName($model);
		if (isset($_GET[$modelName])) {
			$model->attributes = $_GET[$modelName];
		} else {
			if (($salon_id = Yii::app()->request->getQuery('salon_id')) != null) {
				$model->salon_id = Yii::app()->request->getQuery('salon_id');
			}
		}

		$model->dbCriteria->order = 't.id DESC';

		$this->render(
			'index',
			[
				'model' => $model,
			]
		);
	}

	/**
	 * Изменяет значение атрибута. Используется в гридах.
	 *
	 * @throws CHttpException
	 */
	public function actionEditField()
	{
		$pk = Yii::app()->request->getPost('pk');
		$name = Yii::app()->request->getPost('name');
		$value = Yii::app()->request->getPost('value');

		if ($pk === null || $name === null || $value === null) {
			throw new CHttpException(400);
		}

		// Статус
		if ($name == 'status') {
			if ($value < 2) {
				$name = 'is_published';

				$model = $this->loadModel($pk);
				$model->is_blocked = 0;
				$model->save(true, ['is_blocked']);
			} else {
				$name = 'is_blocked';
				$value = 1;

				$model = $this->loadModel($pk);
				$model->is_published = 0;
				$model->save(true, ['is_published']);
			}
		}

		$model = $this->loadModel($pk);
		$model->$name = $value;
		if(!$model->save(true, [$name])) {
			$str = '';
			foreach($model->errors as $error) {
				$str = implode('<br/>', $error);
			}
			echo $str;
			http_response_code(403);
		}
	}

	/**
	 * @param integer $id
	 *
	 * @return LfMaster
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model = LfMaster::model()->findByPk($id);
		if ($model === null) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}
		return $model;
	}

	/**
	 * Производит пополнение или уменьшение денежных средств мастера
	 *
	 * @throws CHttpException
	 *
	 * @return bool
	 */
	public function actionRecharge()
	{
		$masterIdRecharge = Yii::app()->request->getPost("masterIdRecharge");
		if (!$masterIdRecharge) {
			return false;
		}

		$model = LfMaster::model()->findByPk($masterIdRecharge);
		if (!$model) {
			throw new CHttpException("404", "Не существует мастера с таким идентификаторм");
		}

		$sum = 0;
		$addBalance = Yii::app()->request->getPost("addBalance");
		if ($addBalance) {
			$sum = $addBalance;
		} else {
			$minusBalance = Yii::app()->request->getPost("minusBalance");
			if ($minusBalance) {
				$sum = $minusBalance * -1;
			}
		}

		$model->rechargeBalance((int)$sum);

		echo $model->getBalance();
		return true;
	}

	public function actionDeleteWork($id) {
		$model = LfWork::model()->findByPk($id);
		if($model == null) {
			throw new CHttpException(404);
		}
		$model->delete();
	}

	public function actionSaveWork() {
		$id = Yii::app()->request->getPost('id');
		$specId = Yii::app()->request->getPost('specId');
		$serviceId = Yii::app()->request->getPost('serviceId');

		$model = LfWork::model()->findByPk($id);
		if($model == null) {
			throw new CHttpException(404);
		}
		$model->service_id = $serviceId;
		$model->specialization_id = $specId;
		$model->save(false);
	}
}
