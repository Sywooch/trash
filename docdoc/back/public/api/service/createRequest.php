<?php
use dfs\docdoc\models\RequestModel;
use dfs\docdoc\models\PartnerModel;
use dfs\docdoc\models\DoctorModel;
use dfs\docdoc\models\ClinicModel;

require_once dirname(__FILE__) . "/../../lib/php/emailQuery.class.php";
require_once dirname(__FILE__) . "/../../lib/php/request.class.php";
require_once dirname(__FILE__) . "/../../lib/php/models/doctor.class.php";
require_once dirname(__FILE__) . "/../php/commonLib.php";

/**
 * Создаёт заявку через API
 *
 * @param array $params
 *
 * @return array
 */
function createRequest($params = array())
{
	$transaction = \Yii::app()->getDb()->beginTransaction();

	try{
		$data = array();

		if(!count($params)){
			throw new CException("Не переданы параметры");
		}

		/* 	Валидация	 */
		$partnerId = (isset($params['partner'])) ? checkField($params['partner'], "i", 1) : '1';
		$source = (isset($params['source'])) ? checkField($params['source'], "i", 1) : '1';
		$bookId = (isset($params['bookId'])) ? checkField($params['bookId'], "t", '') : '';
		$doctorId = (isset($params['doctor'])) ? $params['doctor'] : 0;
		$clinicId = (isset($params['clinic'])) ? $params['clinic'] : 0;
		$sectorId = (isset($params['speciality'])) ? checkField($params['speciality'], "i", 0) : '0';
		$departure = (isset($params['departure'])) ? checkField($params['departure'], "i", 0) : '0';
		$stations = (isset($params['stations'])) ? explode(',', checkField($params['stations'], "t", [])) : [];
		$scheduleIds = (isset($params['doctorScheduleIds'])) ? $params['doctorScheduleIds'] : [];
		$slotIds = (isset($params['slotIds'])) ? $params['slotIds'] : [];
		$time = (isset($params['time'])) ? $params['time'] : null;
		$client = (isset($params['client'])) ? checkField($params['client'], "t", "") : '';
		$reqType = isset($params['reqType']) ? $params['reqType'] : '3';
		$clientPhone = (isset($params['phone'])) ? $params['phone'] : '';
		$slotId = isset($params['slotId']) ? $params['slotId'] : null;

		if (checkPhone($clientPhone)) {
			$phone = formatPhone4DB($clientPhone);
		} else {
			throw new CException("Неверный номер телефона");
		}

		$clientComment = (isset($params['comment'])) ? checkField($params['comment'], "t", "") : '';
		$ageSelector = (isset($params['age'])) ? checkField($params['age'], "t", "multy") : 'multy';
		$city = (isset($params['city'])) ? checkField($params['city'], "i", 1) : 1;



		if(!$partnerId || empty($client)){
			throw new CException("Не переданы обязательные параметры");
		}

		$sql = "SELECT * FROM request_4_remote_api WHERE request_api_id = '" . $bookId . "'";
		$result = query($sql);

		if (num_rows($result) > 0) {
			throw new CException("Такая бронь уже существует!");
		}

		if(PartnerModel::model()->isMobileApi($partnerId)){
			$enter_point = RequestModel::ENTER_POINT_MOBILE;
		} else {
			if ($doctorId > 0) {
				$enter_point = RequestModel::ENTER_POINT_PARTNER_DOCTOR;
			} elseif ($clinicId > 0) {
				$enter_point = RequestModel::ENTER_POINT_PARTNER_CLINIC;
			} else {
				$enter_point = RequestModel::ENTER_POINT_PARTNER_SEARCH;
			}
		}

		$request = new RequestModel();
		$request->req_created = time();
		$request->client_name = $client;
		$request->client_phone = $phone;

		//sql add
		if (count($slotIds)) {
			$times = explode('-', $slotIds[0]);

			if ($doctorId == 0) {
				$doctorId = $times[0];
			}

			$dateAdmission = $times[1];
			$request->date_admission = $dateAdmission;
		}

		if ($doctorId) {
			$doctorModel = DoctorModel::model()->findByPk($doctorId);

			if (is_null($doctorModel)) {
				throw new CException("Нет такого врача в системе");
			}

			$request->req_doctor_id = $doctorId;

			if (!($clinicId > 0)) {
				$doctorObject = new \Doctor();
				$clinicId = $doctorObject->getDefaultClinicId($doctorId, false);
			}
		}

		if ($clinicId > 0) {
			$clinicModel = ClinicModel::model()->findByPk($clinicId);

			if (is_null($clinicModel)) {
				throw new CException("Нет такой клиники в системе");
			}

			$request->clinic_id = $clinicId;

			$clinic = ClinicModel::model()->findByPk($clinicId);
			$clientComment .= ' ' . $clinic->name;
		}

		if (!empty($time)) {
			$clientComment .= ' Желаемое время приема: ' . date('d.m.Y H:i', $time) . '.';
		}

		if ($sectorId > 0) {
			$request->req_sector_id = $sectorId;
		}

		if (!empty($clientComment)) {
			$request->client_comments = $clientComment;
		}

		//end sql add
		$request->id_city = $city;
		$request->age_selector = $ageSelector;
		$request->req_type = $reqType;
		$request->source_type = $source;
		$request->req_departure = $departure;
		$request->is_hot = 1;
		$request->enter_point = $enter_point;
		$request->partner_id = $partnerId;

		if(!$request->save()){
			throw new CException("Ошибка сохранения заявки из api");
		}

		$requestId = $request->req_id;

		if (isset($slotId) && $slotId) {
			try{
				if(!$request->book($slotId, true)){
                    $bookingErrors = [];

                    foreach($request->getErrors() as $errors){
                        foreach($errors as $error){
                            $bookingErrors[] = $error;
                        }
                    }

                    if (count($bookingErrors)) {
                        $request->addHistory(
                            "При бронировании слота #" . $slotId . " произошли ошибки: " . var_export($bookingErrors, true)
                        );
                    }
                }
			} catch (\Exception $e){
				$request->addHistory("Ошибка при резервировании слота #{$slotId}" . $e->getMessage());
			}
		}

		if ($clinicId > 0) {
			$stations = array();
			$sql =
				"SELECT undegraund_station_id AS id FROM underground_station_4_clinic WHERE clinic_id=" . $clinicId;
			$result = query($sql);

			while ($row = fetch_object($result)) {
				$stations[] = $row->id;
			}

		}

		if (count($stations) > 0) {
			$sqlAdd = array();

			foreach ($stations as $station) {

				if (!empty($station)) {
					$sqlAdd[] = "(" . $requestId . "," . $station . ")";
				}

			}

			if (count($sqlAdd) > 0) {
				$sql = "INSERT INTO request_station VALUES " . implode(',', $sqlAdd);
				if(!query($sql)){
					throw new CException('Ошибка добавления заявки');
				}

			}
		}

		if ($requestId > 0) {
			$sql = "INSERT INTO `request_history` SET
						request_id = " . $requestId . ",
						created = now(),
						action = 3,
						user_id = 0,
						text = 'Заявка создана через API. Партнёр #" . $partnerId . "'";
			$result = query($sql);

			if (!$result) {
				Yii::log($sql);
				throw new CException('Ошибка создания лога');
			}

			if ($source == RequestModel::SOURCE_YANDEX) {
				$scheduleIds = serialize($scheduleIds);
				$sql = "INSERT INTO request_4_remote_api SET
                                request_id = " . $requestId . ",
                                request_api_id = '" . $bookId . "',
                                api_id = " . $partnerId . ",
                                doctor_schedule_ids = '" . $scheduleIds . "'";
				$result = query($sql);
				if (!$result) {
					Yii::log($sql);
					throw new CException('Ошибка создания лога');
				}

				$subject = "[docdoc.ru] Заявка от Яндекса #" . $requestId;
				$message = "<p>Поступила заявка от Яндекса!</p>";
				$message .= "<p>Время создания: " . date('h:i d.m.Y') . "<br>";
				$message .= "ФИО пациента: " . $client . "<br>";
				$message .= "Номер телефона: +" . $phone . "<br>";
				$message .= "ID врача: " . $doctorId . "</p>";

				emailQuery::addMessage([
					"emailTo" => Yii::app()->params['email']['support'],
					"message" => $message,
					"subj" => $subject,
					"priority" => 5
				]);
			}
		}

		$data['status'] = 'success';
		$data['message'] = 'Заявка принята';

		$transaction->commit();
	} catch (Exception $e){
		$data = ['status' => 'error', 'message' => $e->getMessage()];
		$transaction->rollback();
	}

	return array('Response' => $data);
}
