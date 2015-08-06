<?php

class
LandingController extends FrontendController {
	public function actionIndex() {
		if($this->loggedMaster) $this->redirect(array('/lk/index'));
		if($this->loggedSalon) $this->redirect(array('/salonlk/index'));
		$master = new LfMaster;
		$salon = new LfSalon;
		$master->scenario = 'register';
		$salon->scenario = 'register';
		$groupId = null;

		$service = Yii::app()->request->getQuery('service');
		if (isset($service)) {
			$authIdentity = Yii::app()->eauth->getIdentity($service);
			$authIdentity->redirectUrl = $this->createAbsoluteUrl('landing/index');
			$authIdentity->cancelUrl = $this->createAbsoluteUrl('landind/index');

			if ($authIdentity->authenticate()) {
				$identity = new ServiceMasterIdentity($authIdentity);

				// Успешный вход
				if ($identity->authenticate()) {
					Yii::app()->session['service'] = $identity->getState('service');
					Yii::app()->session['service_id'] = $identity->getState('id');
					Yii::app()->session['service_name'] = $identity->getState('name');
					Yii::app()->session['service_photo'] = $identity->getState('photo');
					Yii::app()->session['service_auth'] = true;
					// Специальный редирект с закрытием popup окна
					$authIdentity->redirect();
				}
				else {
					// Закрываем popup окно и перенаправляем на cancelUrl
					$authIdentity->cancel();
				}
			}
			// Что-то пошло не так, перенаправляем на страницу входа
			$this->redirect(array('landind/index'));
		}

		// Регистрация мастера или салона
		if (isset($_POST['selector'])) {
			$selector = $_POST['selector'];

			if($_POST['selector'] == 'master') {
				$model = $master;
				$model->attributes = $_POST['LfMaster'];
			}

			elseif($_POST['selector'] == 'salon') {
				$model = $salon;
				$model->attributes = $_POST['LfSalon'];
			}

			if ($model->validate(null, false) && $model->save()) {
				if($model === $master) {
					$this->sendNotify($model, true);

					Yii::app()->gaTracking->trackEvent('reg', 'click', 'reg mastera');

					$this->login($model, true, false, true);
					$this->redirect(array('/lk/index'));
				}
				else {
					$this->sendNotify($model, false, true);

					Yii::app()->gaTracking->trackEvent('reg', 'click', 'reg salona');

					$this->login($model, false, true, true);
					$this->redirect(array('/salonlk/index'));
				}
			}
		}
		else {
			$selector = 'master';
		}

		// Социальная авторизация
		if(isset(Yii::app()->session['service_auth'])) {
			if($masterService = LfMasterService::model()->find('service = ? AND user_id = ?', array(Yii::app()->session['service'], Yii::app()->session['service_id']))) {
				$model = $masterService->master;
				$this->login($model, true);
				$this->redirect(array('/lk/index'));
			}

			$this->masterLoginForm = new MasterLoginForm;
			$this->showPopup = true;
		}

		// Социальная регистрация
		if (isset($_POST['new_user'])) {
			$model = $master;
			$model->fullName = Yii::app()->session['service_name'];

			$model->attributes = $_POST['new_user'];
			$model->password = $model->generatePassword();
			if ($model->validate(null, false) && $model->save()) {
				// Сохраняем аватарку
				$photoFile = Yii::app()->session['service_photo'];
				if ($photoFile) {
					$model->saveImageByUrl($photoFile);
				}

				$this->sendNotify($model, true);
				$masterService = new LfMasterService;
				$masterService->master_id = $model->id;
				$masterService->user_id = Yii::app()->session['service_id'];
				$masterService->service = Yii::app()->session['service'];
				$masterService->save();
				$this->login($model, true, false, true);
				echo 'success';
				return;
			} else {
				if ($model->getError('email') ==
					'Такой e-mail уже зарегистрирован. Введите другой адрес или воспользуйтесь формой восстановления пароля.' &&
					$model->getError('phone_cell')
				) {
					echo '4';
				} elseif ($model->getError('email') ==
					'Такой e-mail уже зарегистрирован. Введите другой адрес или воспользуйтесь формой восстановления пароля.'
				) {
					echo '3';
				} elseif ($model->getError('email') && $model->getError('phone_cell')) {
					echo '2';
				} elseif ($model->getError('phone_cell')) {
					echo '1';
				} elseif ($model->getError('email')) {
					echo '0';
				}
				return;
			}
		}

		if(isset($_POST['old_user'])) {
			$this->masterLoginForm->attributes = $_POST['old_user'];
			if ($this->masterLoginForm->validate() && $this->masterLoginForm->login()) {
				$model = LfMaster::model()->findByPk(Yii::app()->user->getId());
				$model->save();
				$masterService = new LfMasterService;
				$masterService->master_id = $model->id;
				$masterService->user_id = Yii::app()->session['service_id'];
				$masterService->service = Yii::app()->session['service'];
				$masterService->save();
				unset(Yii::app()->session['service']);
				unset(Yii::app()->session['service_id']);
				unset(Yii::app()->session['service_name']);
				unset(Yii::app()->session['service_photo']);
				unset(Yii::app()->session['service_auth']);
				echo 'success';
				return;
			}
			else {
				if($this->masterLoginForm->getError('email') && $this->masterLoginForm->getError('password'))
					echo '2';
				elseif($this->masterLoginForm->getError('email'))
				echo '1';
				else
					echo '0';
				return;
			}
		}
		unset(Yii::app()->session['service_auth']);

		$this->render('index', compact('master', 'salon', 'selector'));
	}

	/**
	 * Отправляет письмо на почту
	 *
	 * @param LfMaster|LfSalon $model
	 * @param bool             $master
	 * @param bool             $salon
	 *
	 * @return void
	 */
	protected function sendNotify($model, $master = false, $salon = false)
	{
		$mailer = Yii::app()->mailer;

		$templateName = null;

		$templateContent = $mailer->prepareTemplateContent(
			[
				'userLogin'    => $model->email,
				'userPassword' => $model->password,
				'userEmail'   => $model->email,
			]
		);

		$message = [
			'to' => [
				['email' => $model->email],
			],
		];

		if ($master) {
			$templateName = 'Регистрация';
			$templateContent = CMap::mergeArray(
				$templateContent,
				$mailer->prepareTemplateContent(
					[
						'userName'    => $model->getFullName(),
						'profileLink' => $model->getProfileUrl(true),
						'commonEmail' => Yii::app()->params["commonEmail"],
					]
				)
			);
			$message['global_merge_vars'] = $mailer->prepareTemplateContent(
				[
					'commonEmail' => Yii::app()->params["commonEmail"],
					'profileLink' => $model->getProfileUrl(true),
				]
			);
		}
		if ($salon) {
			$templateName = 'Регистрация салона';
		}

		// Отправляем письмо пользователю
		$result = $mailer->sendTemplate($templateName, $templateContent, $message);
		if (!isset($result[0])) {
			Yii::log('Не удалось отправить письмо регистрации', CLogger::LEVEL_ERROR);
		} else {
			if ($result[0]['reject_reason'] != null) {
				Yii::log(
					'Не удалось отправить письмо регистрации. Причина: ' . $result[0]['reject_reason'],
					CLogger::LEVEL_ERROR
				);
			}
		}
	}

	/**
	 * Авторизует пользователя
	 *
	 * @param LfMaster|LfSalon $model
	 * @param bool             $master
	 * @param bool             $salon
	 * @param bool             $firstTime
	 */
	protected function login($model, $master = false, $salon = false, $firstTime = false)
	{
		unset(Yii::app()->session['service']);
		unset(Yii::app()->session['service_id']);
		unset(Yii::app()->session['service_name']);
		unset(Yii::app()->session['service_photo']);
		unset(Yii::app()->session['service_auth']);
		$identity = new MasterIdentity($model->email, $model->password);
		$identity->authenticate();
		Yii::app()->user->login($identity, 0);
		if ($master) {
			Yii::app()->user->setState('masterLoggedInPublic', true);
		} else {
			Yii::app()->user->setState('salonLoggedInPublic', true);
		}
		if ($firstTime) {
			Yii::app()->session['firstTime'] = $firstTime;
		}
		session_write_close();
	}

	/**
	 * Все мастера, зарегистрировавшиеся до... (получает дату)
	 *
	 * @return string
	 */
	public function getActionDate()
	{
		return $this->_actionDates[date("n", time())];
	}

	/**
	 * Массив дат действия акции
	 *
	 * @var string[]
	 */
	private $_actionDates = array(
		1  => "31 января",
		2  => "28 февраля",
		3  => "31 марта",
		4  => "30 апреля",
		5  => "31 мая",
		6  => "30 июня",
		7  => "31 июля",
		8  => "31 августа",
		9  => "30 сентября",
		10 => "31 октября",
		11 => "30 ноября",
		12 => "31 декабря",
	);
}