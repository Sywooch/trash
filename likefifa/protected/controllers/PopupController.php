<?php 
class PopupController extends FrontendController {

	public function actionMap() {
		$this->renderPartial('map');
	}
    
    public function actionAreas() {
        $areas = AreaMoscow::model()->findAll();
		$districts = DistrictMoscow::model()->findAll();
		
		$areaArr = array();
		foreach($areas as $area){
			$areaArr[$area->id]['area']['name'] = $area->name;
			$areaArr[$area->id]['area']['rewriteName'] = $area->rewrite_name;
			foreach($districts as $district){
				if($area->id == $district->area_moscow_id){
					$areaArr[$area->id]['districts'][$district->id]['name'] = $district->name;
					$areaArr[$area->id]['districts'][$district->id]['rewriteName'] = $district->rewrite_name;
				}
			}
		}
		
		$areaArr = json_encode($areaArr);
        
        $this->renderPartial('areas', compact('areaArr','areas','districts'));
    }
    
    /**
     * Создает новую заявку
	 *
     * @param LfSalon $salon модель салона
     * @param LfMaster $master модель мастера
     * @param int $specialization_id идентификатор специализации
     * @param int $service_id идентификатор услуги
     *
     * @return LfAppointment модель новой заявки
     */
    protected function insertAppointment(LfSalon $salon = null, LfMaster $master = null, $specialization_id = null, $service_id = null) {
        $model = new LfAppointment;    	
    	if (isset($_POST['LfAppointment'])) {
            $model->attributes = $_POST['LfAppointment'];
            if ($salon) {
                $model->salon_id = $salon->id;
            }
            if ($master) {
                $model->master_id = $master->id;
            }
            if (isset($_POST['LfAppointment']['service_id'])) {
                $ser_id = $_POST['LfAppointment']['service_id'];
                if ($ser_id) {
					$price_model = null;
                    if ($salon) {
                        $price_model = LfPrice::model()->findByAttributes(array('service_id' => $ser_id, 'salon_id' => $salon->id ));
                    }
                    if ($master) {
                        $price_model = LfPrice::model()->findByAttributes(array('service_id' => $ser_id, 'master_id' => $master->id ));
                    }
					if($price_model) {
						$model->service_price = $price_model->price;
					}
                    if ( $ser_model = LfService::model()->findByPk( $ser_id ) ) {
                        $model->service_name = $ser_model->name;
                    }
                }
            }
			if ($model->save()) {
				$sms_model_client = new Sms;
				$sms_model_client->makeNewSmsForClientByAppointmentId($model->id, $salon == null && $master == null);

				// Отправляем данные в Google Analytics
				if (($gaReceiver = Yii::app()->request->getPost('gaReceiver')) != null &&
					($gaText = Yii::app()->request->getPost('gaText')) != null
				) {
					Yii::app()->gaTracking->trackEvent('zapis', $gaReceiver, $gaText);
				}
			}
        }
    	else {
    		if ($specialization_id) {
    			$model->specialization_id = $specialization_id;
    		}
    		if ($service_id) {
    			$model->service_id = $service_id;
    		}
    	}
        if (!$model->isNewRecord) {
    		$model->notify();
    	}
    	return $model;
    }

	/**
	 * Дилоговое окно заявки из шапки
	 *
	 * @param string $gaType параметр для GA
	 */
	public function actionAppointment($gaType)
	{
		$type = 'full';
		$model = $this->insertAppointment(null, null, null, null);
		$this->renderPartial(
			!$model->isNewRecord ? 'appointment-success' : 'appointment',
			compact('model', 'gaType', 'type')
		);
	}

	/**
	 * Диалоговое окно заявки для мастера
	 *
	 * @param string $gaType         параметр для GA
	 * @param string $type           тип (full/short)
	 * @param int    $id             идентификатор мастера
	 * @param int    $specialization идентификатор специализации
	 * @param int    $service        идентификатор услуги
	 *
	 * @throws CHttpException
	 */
	public function actionMasterAppointment($gaType, $type, $id, $specialization = null, $service = null)
	{
		$master = LfMaster::model()->findByPk($id);
		if (!$master) {
			throw new CHttpException(404, 'Master not found');
		}

		$model = $this->insertAppointment(null, $master, $specialization, $service);
		$this->renderPartial(
			!$model->isNewRecord ? 'appointment-success' : 'masterAppointment',
			compact('master', 'model', 'type', 'gaType')
		);
	}

	/**
	 * Диалоговое окно заявки для салона
	 *
	 * @param string $gaType               параметр для GA
	 * @param string $type                 тип (full/short)
	 * @param int    $salonId              идентификатор салона
	 * @param int    $masterId             идентификатор мастера
	 * @param int    $specialization       идентификатор специализации
	 * @param int    $service              идентификатор услуги
	 *
	 * @throws CHttpException
	 */
	public function actionSalonAppointment(
		$gaType,
		$type,
		$salonId,
		$masterId = null,
		$specialization = null,
		$service = null
	)
	{
		$salon = LfSalon::model()->findByPk($salonId);
		$master =
			$salon && is_numeric($masterId)
				? LfMaster::model()->findByPk($masterId, 'salon_id = ' . $salon->id)
				: null;

		if (!$salon) {
			throw new CHttpException(404, 'Salon not found');
		}

		$model = $this->insertAppointment($salon, $master, $specialization, $service);
		$this->renderPartial(
			!$model->isNewRecord ? 'appointment-success' : 'salonAppointment',
			compact('salon', 'master', 'model', 'type', 'gaType')
		);
	}
    
    protected function insertAbuse($salonId = null, $masterId = null) {
    	$salon = null;
    	$master = null;
    	
    	if ($salonId && !($salon = LfSalon::model()->findByPk($salonId)))
    		throw new CHttpException(404, 'Salon not found');
    	
    	if ($masterId && !($master = LfMaster::model()->findByPk($masterId)))
    		throw new CHttpException(404, 'Master not found');
    	
    	if (!$salon && !$master)
    		throw new CHttpException(404, 'Master or salon required');
    	 
    	$model = new LfAbuse;
    	$model->type = LfAbuse::TYPE_NO_CONTACT;
    	if ($salon) $model->salon_id = $salon->id;
    	if ($master) $model->master_id = $master->id;
    	
    	if (isset($_POST['LfAbuse'])) {
    		$model->attributes = $_POST['LfAbuse'];
    		$model->save();
    		$model->notify();
    	}
    	 
    	return $model;
    }
    
    public function actionMasterAbuse($id) {
    	$model = $this->insertAbuse(null, $id);
    	$this->renderPartial(!$model->isNewRecord ? 'abuse-success' : 'abuse', compact('model'));
    }
    
    public function actionSalonAbuse($id) {
    	$model = $this->insertAbuse($id, null);
    	$this->renderPartial(!$model->isNewRecord ? 'abuse-success' : 'abuse', compact('model'));
    }

	/**
	 * Принятие заявки
	 *
	 * @param int $status статус
	 * @param int $id     идентификатор
	 *
	 * @return void
	 */
	public function actionApplyAppointment($status, $id) {
    	$model = LfAppointment::model()->findByPk($id);
    	if (isset($_POST['LfAppointment'])) {
    		$model->dateDate = strtotime($_POST['LfAppointment']['date']);
    		$model->dateTime = $_POST['LfAppointment']['time'];
    		$model->status = LfAppointment::STATUS_ACCEPTED;

			if ($model->touch()->save()) {
				$model->makeAcceptedSms();
			}

    		if($model->master)
    			$this->redirect(array('lk/appointment/'.$status));
    		elseif($model->salon)
    			$this->redirect(array('salonlk/appointment/'.$status));
    	}
    	$this->renderPartial('apply-appointment', compact('model'), false, true);
    }

	public function actionCancelAppointment($status, $id)
	{
		$model = LfAppointment::model()->findByPk($id);
		if (isset($_POST['LfAppointment'])) {
			$reason = $model->getReasonListItems($status);
			$model->reason =
				!empty($_POST['LfAppointment']['reasonText']) ? $_POST['LfAppointment']['reasonText']
					: $reason[$_POST['LfAppointment']['reason']];
			$model->status =
				$model->status == LfAppointment::STATUS_ACCEPTED ? LfAppointment::STATUS_REJECTED_AFTER_ACCEPTED
					: LfAppointment::STATUS_REJECTED;
			$model->touch()->save();
			if ($model->master) {
				$this->redirect(array('lk/appointment'));
			} elseif ($model->salon) {
				$this->redirect(array('salonlk/appointment'));
			}
		}
		$this->renderPartial('cancel-appointment', compact('model', 'status'));
	}

    
    public function actionServiceAuth() {
    	$this->renderPartial('service-auth');
    }

	/**
	 * Смена мастера/салона для заявки
	 *
	 * @return void
	 */
	public function actionChangeAppointment() {
		$appointmentId = Yii::app()->request->getQuery("id");
		$this->renderPartial('change_appointment', compact("appointmentId"));
	}

	/**
	 * Изменить баланс мастера
	 *
	 * @param bool $is_addition Добавление ли денег
	 * @param int  $master_id   Идентификатор мастера
	 *
	 * @throws CHttpException
	 *
	 * @return void
	 */
	public function actionRecharge($is_addition = true, $master_id = 0)
	{
		if (!$master_id) {
			throw new CHttpException("404", "Не указан идентификатор мастера");
		}

		$model = LfMaster::model()->findByPk($master_id);
		if (!$model) {
			throw new CHttpException("404", "Мастера с таким идентификатором не существует");
		}

		if ($is_addition) {
			$this->renderPartial('recharge_plus', compact("model"));
		} else {
			$this->renderPartial('recharge_minus', compact("model"));
		}
	}

	/**
	 * Просмотр транзакций у мастера
	 *
	 * @param int  $master_id   Идентификатор мастера
	 *
	 * @throws CHttpException
	 *
	 * @return void
	 */
	public function actionMasterTransactions($master_id = 0)
	{
		if (!$master_id) {
			throw new CHttpException("404", "Не указан идентификатор мастера");
		}

		$model = LfMaster::model()->findByPk($master_id);
		if (!$model) {
			throw new CHttpException("404", "Мастера с таким идентификатором не существует");
		}

		$this->renderPartial('master_transactions', compact("model"));
	}
}