<?php
use dfs\docdoc\models\RequestModel;
use dfs\docdoc\models\RequestHistoryModel;

include_once dirname(__FILE__) . "/smsQuery.class.php";
require_once dirname(__FILE__) . "/validate.php";


class request
{
	public $id;
	public $status;
	public $attributes = array();

	protected $LKStatus = array(
		'ADMISSION' => 1,
		'VISIT' => 2,
		'REJECT' => 3,
		'CANCEL' => 4,
		'ACCEPT' => 5,
		'COMPLETE' => 6
	);

	// Источники
	const SOURCE_SITE       = 1;
	const SOURCE_PHONE      = 2;
	const SOURCE_PARTNER    = 3;
	const SOURCE_YANDEX     = 4;
	const SOURCE_IPHONE     = 5;

	// Статусы
	const STATUS_NEW                = 0;
	const STATUS_PROCESS            = 1;
	const STATUS_RECORD             = 2;
	const STATUS_CAME               = 3;
	const STATUS_REMOVED            = 4;
	const STATUS_REJECT             = 5;
	const STATUS_ACCEPT             = 6;
	const STATUS_CALL_LATER         = 7;
	const STATUS_REJECT_BY_PARTNER  = 8;
	const STATUS_RECALL             = 10;

	/**
	 * Виды заявок
	 */
	const KIND_DOCTOR       = 0;
	const KIND_DIAGNOSTICS  = 1;
	const KIND_ANALYSIS     = 2;

	/*	Определяем начальное состояние самого себя	*/
	function __construct()
	{
		$this->id = 0;
		$this->attributes['id'] = 0;
		$this->attributes['RequestId'] = $this->attributes['Request'] = 0;


		$this->attributes['Doctor']['name'] = "";
		$this->attributes['Doctor']['id'] = 0;

		$this->attributes['Sector']['name'] = "";
		$this->attributes['Sector']['id'] = 0;

		$this->attributes['Clinic']['name'] = "";
		$this->attributes['Clinic']['address'] = "";
		$this->attributes['Clinic']['id'] = 0;
		$this->attributes['Clinic']['sendSMS'] = 'yes';

		$this->attributes['CrDate'] = "";
		$this->attributes['CrTime'] = "";

		$this->attributes['ClientName'] = "";
		$this->attributes['Client']['name'] = "";
		$this->attributes['Client']['id'] = 0;
		$this->attributes['Client']['phone'] = "";
		$this->attributes['Client']['phoneDig'] = "";
		$this->attributes['ClientPhone'] = "";

		$this->attributes['IsGoHome'] = 0;
		$this->attributes['AgeSelector'] = "";

		$this->attributes['AppointmentStatus'] = "";
		$this->attributes['DateAdmission'] = "";
		$this->attributes['AppointmentDate'] = "";
		$this->attributes['AppointmentDateTime'] = "";
		$this->attributes['AppointmentTime']['Hour'] = "";
		$this->attributes['AppointmentTime']['Min'] = "";

		$this->attributes['CallLater'] = "";
		$this->attributes['CallLaterDate'] = "";
		$this->attributes['CallLaterTime'] = "";
		$this->attributes['RemainTime'] = "";

		$this->attributes['Owner']['Name'] = "";
		$this->attributes['Owner']['Id'] = 0;


		$this->attributes['Status'] = "";
		$this->attributes['LKStatus'] = "";
		$this->attributes['SMSstatus'] = "";
		$this->attributes['Type'] = "";
		$this->attributes['Source'] = "";
		$this->attributes['CityId'] = "";
		$this->attributes['RejectReason'] = null;
		$this->attributes['Kind'] = "";

		$this->attributes['ClientComment'] = "";

		$this->attributes['Records'] = array();
		$this->attributes['AnotherRequest'] = array();
	}


	/*	Список клиник. в которых принимает врач  */
	function getClinic4Doctor()
	{

	}

	/*	Записи разговоров  */
	function getAudioList()
	{
		if (!empty($this->id)) {
			$sql = "SELECT
	   						t1.record_id as id, 
							t1.request_id, t1.record,
							t1.crDate AS date_created,
							DATE_FORMAT( t1.crDate,'%d.%m.%Y') AS crDate,
							DATE_FORMAT( t1.crDate,'%d.%m.%Y %H.%i') AS crDateTime,
							t1.duration, t1.comments as note, 
							t1.isOpinion, 
							t1.isAppointment,
							t1.isVisit
						FROM request_record t1
						WHERE 
							t1.request_id = " . $this->id . "
						ORDER BY record";
			//echo $sql."<br/>";
			$result = query($sql);
			if (num_rows($result) > 0) {
				while ($row = fetch_object($result)) {
					$record = array();
					$record['Id'] = $row->id;
					$record['RequestId'] = $row->request_id;
					$record['Path'] = $row->record;
					$record['IsOpinion'] = $row->isOpinion;
					$record['IsAppointment'] = $row->isAppointment;
					$record['IsVisit'] = $row->isVisit;
					$record['DateCreated'] = $row->date_created;
					$record['CrDate'] = $row->crDate;
					$record['CrDateTime'] = $row->crDateTime;
					array_push($this->attributes['Records'], $record);
				}
			}

		}
	}


	/*	Записи разговоров  */
	public function getRequestWithThisPhoneNumber()
	{
		if (!empty($this->id) && $this->attributes['Client']['phoneDig']) {
			$sql = "SELECT
	   					t1.req_id as id, 
	   					t1.req_created,
	   					t1.req_status as status, 
	   					t1.req_sector_id
					FROM request t1
					WHERE 
							t1.client_phone = '" . $this->attributes['Client']['phoneDig'] . "'
						AND
						t1.req_id <> " . $this->id . "
					ORDER BY req_created DESC";
			//echo $sql."<br/>";
			$result = query($sql);
			if (num_rows($result) > 0) {
				while ($row = fetch_object($result)) {
					$line = array();
					$line['Id'] = $row->id;
					$line['Status'] = $row->status;
					$line['SectorId'] = $row->req_sector_id;
					$line['CrDate'] = date("d.m.Y", $row->req_created);
					$line['CrDateTime'] = date("H:i", $row->req_created);
					array_push($this->attributes['AnotherRequest'], $line);
				}
			}

		}
	}


	/*	Лог действий  */
	function getLogHistory()
	{

	}

	/**
	 * Загрузка данных по заявке
	 *
	 * @param int $id идентификатор заявки
	 *
	 * @throw CHttpException
	 */
	function getRequest($id)
	{
		$id = intval($id);

		if ($id > 0) {
			$sql = "SELECT
							t1.req_id as id, 
							t1.clinic_id, 
							t1.client_name, t1.client_phone, t1.add_client_phone,
							t1.req_created, 
							t1.req_status as status, 
							t1.lk_status as LKStatus,
							t1.req_type, t1.req_sector_id,
							t1.clientId, t1.call_later_time,t1.req_departure as isGoHome,
							t1.req_doctor_id as doctor_id, t2.name as doctor, t1.req_sector_id, 
							t1.req_user_id as owner, t3.user_lname, t3.user_fname, t3.user_email,
							t4.name as sector,
							t1.date_admission, t1.appointment_status, t2.status as doctorStatus, 
							cl.id as clinicId, cl.name as clinic, cl.sendSMS, 
							concat (cl.street, ', ', cl.house) as clinicAddress,
							t1.client_comments, t1.age_selector, t1.status_sms, t1.id_city,
							t1.reject_reason, t1.source_type, t1.kind
						FROM request  t1
						LEFT JOIN doctor t2 ON (t2.id = t1.req_doctor_id)
						LEFT JOIN `user` t3 ON (t3.user_id = t1.req_user_id)
						LEFT JOIN `clinic` cl ON (cl.id = t1.clinic_id)
						LEFT JOIN sector t4 ON (t4.id = t1.req_sector_id)
						WHERE 
							req_id = " . $id;
			$result = query($sql);
			if (num_rows($result) == 1) {
				$row = fetch_object($result);

				$this->id = $row->id;
				$this->status = $row->status;
				$this->LKStatus = $row->LKStatus;

				$this->attributes['id'] = $row->id;
				$this->attributes['Request'] = $row->id;
				$this->attributes['RequestId'] = $row->id;

				$this->attributes['Doctor']['name'] = $row->doctor;
				$this->attributes['Doctor']['id'] = $row->doctor_id;
				$this->attributes['Doctor']['status'] = $row->doctorStatus;

				$this->attributes['Sector']['name'] = $row->sector;
				$this->attributes['Sector']['id'] = $row->req_sector_id;

				$this->attributes['Clinic']['name'] = $row->clinic;
				$this->attributes['Clinic']['id'] = $row->clinic_id;
				$this->attributes['Clinic']['address'] = $row->clinicAddress;
				$this->attributes['Clinic']['sendSMS'] = $row->sendSMS;

				$this->attributes['CrDate'] = date("d.m.Y", $row->req_created);
				$this->attributes['CrTime'] = date("H:i", $row->req_created);

				$this->attributes['ClientName'] = $row->client_name;
				$this->attributes['Client']['name'] = $row->client_name;
				$this->attributes['Client']['id'] = $row->clientId;
				$this->attributes['Client']['phone'] = $row->client_phone;
				$this->attributes['Client']['addPhone'] = $row->add_client_phone;
				$this->attributes['Client']['phoneDig'] = formatPhone4DB($row->client_phone);
				$this->attributes['ClientPhone'] = formatPhone($row->client_phone);
				$this->attributes['AddClientPhone'] = formatPhone($row->add_client_phone);

				$this->attributes['IsGoHome'] = $row->isGoHome;
				$this->attributes['AgeSelector'] = $row->age_selector;

				$this->attributes['AppointmentStatus'] = $row->appointment_status;
				$this->attributes['DateAdmission'] = $row->date_admission;
				if (!empty($row->date_admission)) {
					$this->attributes['DateAdmission'] = $row->date_admission;
					$this->attributes['AppointmentDate'] = date("d.m.Y", $row->date_admission);
					$this->attributes['AppointmentDateTime'] = date("d.m.Y H:i", $row->date_admission);
					$this->attributes['AppointmentTime']['Data'] = date("H:i", $row->date_admission);
					$this->attributes['AppointmentTime']['Hour'] = date("H", $row->date_admission);
					$this->attributes['AppointmentTime']['Min'] = date("i", $row->date_admission);
				} else {
					$this->attributes['AppointmentDate'] = null;
				}

				if (!empty($row->call_later_time)) {
					$this->attributes['CallLater'] = $row->call_later_time;
					$this->attributes['CallLaterDate'] = date("d.m.Y", $row->call_later_time);
					$this->attributes['CallLaterTime']['Data'] = date("H:i", $row->call_later_time);
					$this->attributes['CallLaterTime']['Hour'] = date("H", $row->call_later_time);
					$this->attributes['CallLaterTime']['Min'] = date("i", $row->call_later_time);
					$this->attributes['RemainTime'] = (time() - $row->call_later_time);
				} else {
					$this->attributes['CallLater'] = null;
				}

				$this->attributes['Owner']['Name'] = $row->user_lname . " " . $row->user_fname;
				$this->attributes['Owner']['Id'] = $row->owner;
				$this->attributes['Author'] = 'oper';

				$this->attributes['Status'] = $row->status;
				$this->attributes['LKStatus'] = $row->LKStatus;
				$this->attributes['SMSstatus'] = $row->status_sms;
				$this->attributes['Type'] = $row->req_type;
				$this->attributes['Source'] = $row->source_type;
				$this->attributes['CityId'] = $row->id_city;
				$this->attributes['RejectReason'] = $row->reject_reason;
				$this->attributes['Kind'] = $row->kind;

				$this->attributes['ClientComment'] = $row->client_comments;
			} else {
				throw new CHttpException(404, "Заявки с данным идентификатором не существует");
			}
		}
	}

	/*	Пстроение XML дерева	*/
	public function getXMLtree()
	{
		$xml = "";

		if (count($this->attributes) > 0) {
			$xml .= "<Request  id='" . $this->attributes['id'] . "'>";
			foreach ($this->attributes as $tagName => $data) {
				if (is_array($data)) {
					if (isset($data['name']) && isset($data['id'])) {
						$xml .= "<" . $tagName . " id=\"" . $data['id'] . "\">" . $data['name'] . "</" . $tagName . ">";
					} else {
						$xml .= "<" . $tagName;
						foreach ($data as $subNode => $subData) {
							if (!is_array($subData)) {
								$xml .= " " . $subNode . "=\"" . $subData . "\" ";
							}
						}
						$xml .= "/>";
					}
				} else {
					$xml .= "<" . $tagName . ">";
					$xml .= $data;
					$xml .= "</" . $tagName . ">";
				}
			}
			$xml .= "</Request>";
		}

		return $xml;
	}


	public function setStatus($status)
	{
		if (!empty($this->id)) {
			$request = RequestModel::model()->findByPk($this->id);
			$request->saveStatus($status);
		}
	}


	public function setLKStatus($status)
	{
		$status = intval($status);

		if (!empty($this->id)) {
			$sql = "UPDATE `request` 
				SET 
				 	lk_status = " . $status . "
				WHERE req_id=" . $this->id;

			query($sql);
		}
	}


	/**
	 *
	 * Функция инкапсулирует в себе разнообпразные условия для проверки состояния заявки
	 */
	protected function chkCondition($condition, $params)
	{
		switch ($condition) {
			case 'isTransfer' :
			{
				if (isset($params['isTransfer']) && $params['isTransfer'] == 1)
					return true;
				else
					return false;
			}

			case 'callLater' :
			{
				if (isset($params['callLater']) && !empty($params['callLater']))
					return true;
				else
					return false;
			}

			case 'isReject' :
			{
				if (isset($params['isReject']) && $params['isReject'] == 1)
					return true;
				else
					return false;
			}

			case 'appointment' :
			{
				if (
					(isset($params['appointment']) && !empty($params['appointment']))
					||
					!empty($this->attributes['AppointmentDate'])
				)
					return true;
				else
					return false;
			}

			case 'appStatus' :
			{
				if (
					(isset($params['appStatus']) && $params['appStatus'] == 1)
					||
					$this->attributes['AppointmentStatus'] == 1
				)
					return true;
				else
					return false;
			}

			case 'LKReject' :
			{
				if (isset($params['isReject']) && $params['isReject'] == 1)
					return true;
				else
					return false;
			}
		}
	}

	/**
	 * Назначение оператора
	 * @param $userId
	 */
	public function assignUser($userId)
	{
		$owner = $this->attributes['Owner']['Id'] ? null : $userId;
		$newStatus = null;

		switch ($this->attributes['Status']) {
			case self::STATUS_NEW:
				$newStatus = self::STATUS_ACCEPT;
				break;
			case self::STATUS_RECALL:
				$newStatus = self::STATUS_PROCESS;
				if ($this->attributes['Owner']['Id'] != $userId) {
					$owner = $userId;
				}
				break;
			case self::STATUS_CALL_LATER:
				$newStatus = self::STATUS_ACCEPT;
				break;
		}

		if ($owner !== null || $newStatus !== null) {
			$request = RequestModel::model()->findByPk($this->id);
			$request->setScenario(RequestModel::SCENARIO_OPERATOR);
			if ($owner !== null) {
				$request->req_user_id = $owner;
				$request->is_hot = 0;
				$request->save(true, [ 'req_user_id', 'is_hot']);
			}
			if ($newStatus !== null) {
				$request->saveStatus($newStatus);
			}
		}
	}



	/**
	 * основной метод изменения состояния заявки
	 *
	 * @param array $params
	 * @param $userId
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function changeStatus($params = array(), $userId)
	{
		$userId = intval($userId);

		$currState = $this->attributes['Status'];

		switch ($currState) {
			/*	Новая 0 -> Принята 6	*/
			case 0 : //ГОТОВО
			{
				$request = RequestModel::model()->findByPk($this->id);
				if ($request !== null) {
					$request->saveRequestUser($userId);
				}
				$newState = 6;
				$newStatusMsg = "Принята";
			}
				break;

			/*	Принята 6 -> в обработке 1 -> отказ 5 -> перезвонить 7 -> Принята 6*/
			case 6 :
			{
				$newState = 6;
				$newStatusMsg = "Принята";

				if ($this->chkCondition("isTransfer", $params)) {
					$newState = 1;
					$newStatusMsg = "В обработке";
				}

				if ($this->chkCondition("callLater", $params)) {
					$newState = 7;
					$newStatusMsg = "Перезвонить";
				}

				//ГОТОВО
				if ($this->chkCondition("isReject", $params)) {
					$newState = 5;
					$newStatusMsg = "Отказ";
				}

			}
				break;

			/*	В обработке 1 -> отказ 5 -> перезвонить 7 -> Принята 6 -> Обработана 2 -> В обработке 1	*/
			case 1 :
			{
				$newState = 1;
				$newStatusMsg = "В обработке";


				if (!($this->chkCondition("isTransfer", $params))) {
					$newState = 6;
					$newStatusMsg = "Принята";
				}

				if (
					$this->chkCondition("appointment", $params)
					&&
					$this->chkCondition("isTransfer", $params)
				) {
					$newState = 2;
					$newStatusMsg = "Обработана";
				}

				if ($this->chkCondition("callLater", $params)) {
					$newState = 7;
					$newStatusMsg = "Перезвонить";
				}

				//ГОТОВО
				if ($this->chkCondition("isReject", $params)) {
					$newState = 5;
					$newStatusMsg = "Отказ";
				}

			}
				break;


			/*	Обработана 2 -> отказ 5 -> перезвонить 7 -> Завершена 3 -> В обработке 1 -> Принята 6 -> Обработана 2	*/
			case 2 :
			{
				$newState = 2;
				$newStatusMsg = "Обработана";

				if (!$this->chkCondition("isTransfer", $params)) {
					$newState = 6;
					$newStatusMsg = "Принята";
				}

				if (
					!$this->chkCondition("appointment", $params)
					&&
					$this->chkCondition("isTransfer", $params)
				) {
					$newState = 1;
					$newStatusMsg = "В обработке";
				}

				if (
					$this->chkCondition("appointment", $params)
					&&
					$this->chkCondition("isTransfer", $params)
					&&
					$this->chkCondition("appStatus", $params)
				) {
					$newState = 3;
					$newStatusMsg = "Завершена";
				}

				if ($this->chkCondition("callLater", $params)) {
					$newState = 7;
					$newStatusMsg = "Перезвонить";
				}

				//ГОТОВО
				if ($this->chkCondition("isReject", $params)) {
					$newState = 5;
					$newStatusMsg = "Отказ";
				}

			}
				break;


			/*	Завершена 3 -> Обработана 2 -> отказ 5 -> перезвонить 7 -> Завершена 3 -> В обработке 1 -> Принята 6	*/
			case 3 :
			{
				$newState = 3;
				$newStatusMsg = "Завершена";


				if (!$this->chkCondition("isTransfer", $params)) {
					$newState = 6;
					$newStatusMsg = "Принята";
				}

				if (
					!$this->chkCondition("appointment", $params)
					&&
					$this->chkCondition("isTransfer", $params)
				) {
					$newState = 1;
					$newStatusMsg = "В обработке";
				}

				if (
					$this->chkCondition("appointment", $params)
					&&
					$this->chkCondition("isTransfer", $params)
					&&
					!$this->chkCondition("appStatus", $params)
				) {
					$newState = 2;
					$newStatusMsg = "Обработана";
				}

				if ($this->chkCondition("callLater", $params)) {
					$newState = 7;
					$newStatusMsg = "Перезвонить";
				}

				//ГОТОВО
				if ($this->chkCondition("isReject", $params)) {
					$newState = 5;
					$newStatusMsg = "Отказ";
				}
				//echo $newState; exit;


				if ($this->LKStatus == 3 && $newState == 3)
					$this->changeLKStatus(array('LKstatus' => 5));


			}
				break;


			/*	 Отказ 5 -> В обработке */
			case 5 :
				/*
				 * Только администратор может изменить статус
				 */
				if (!$this->chkCondition("isReject", $params)) {
					if ($params['user']->checkRight4userByCode(array('ADM', 'SOP'))) {
						$newState = self::STATUS_PROCESS;
						$newStatusMsg = "В обработке";
					} else {
						throw new Exception("Недостаточно прав для снятия отказа");
					}
				}

				break;

			/*	 Перезвонить 7 -> Завершена 3 -> Обработана 2 -> отказ 5 -> перезвонить 7 -> Завершена 3 -> В обработке 1 -> Принята 6 */
			case 7 :
			{
				$newState = 7;
				$newStatusMsg = "Перезвонить";

				if (!$this->chkCondition("callLater", $params)) {

					if (!$this->chkCondition("isTransfer", $params)) {
						$newState = 6;
						$newStatusMsg = "Принята";
					}

					if (
						!$this->chkCondition("appointment", $params)
						&&
						$this->chkCondition("isTransfer", $params)
					) {
						$newState = 1;
						$newStatusMsg = "В обработке";
					}

					if (
						$this->chkCondition("appointment", $params)
						&&
						$this->chkCondition("isTransfer", $params)
						&&
						!$this->chkCondition("appStatus", $params)
					) {
						$newState = 2;
						$newStatusMsg = "Обработана";
					}

					if (
						$this->chkCondition("appointment", $params)
						&&
						$this->chkCondition("isTransfer", $params)
						&&
						$this->chkCondition("appStatus", $params)
					) {
						$newState = 3;
						$newStatusMsg = "Завершена";
					}

					//ГОТОВО
					if ($this->chkCondition("isReject", $params)) {
						$newState = 5;
						$newStatusMsg = "Отказ";
					}

				}
			}
				break;

			/*	 Отклонена партнёром 8 */
			case 8 :
			{
				$newState = 8;
				$newStatusMsg = "Отклонена партнёром";
				if (
					$this->chkCondition("appointment", $params)
					&&
					$this->chkCondition("isTransfer", $params)
					&&
					$this->chkCondition("appStatus", $params)
				) {
					$newState = 3;
					$newStatusMsg = "Завершена";
				}

				if ($this->chkCondition("isReject", $params)) {
					$newState = 5;
					$newStatusMsg = "Отказ";
				}

				if ($newState == 3 && $this->LKStatus != 4) {
					// Изменить стстус LK в Завершена
					$this->changeLKStatus(array('LKstatus' => 5));
				}

				if ($newState == 5) {
					// Изменить стстус LK в Отказ
					$this->changeLKStatus(array('LKstatus' => 6));
				}

			}
				break;

			/*	 Удалена 4 */
			case 4 :
			{
				/* Только администратор может изменить статус
				 * Для администратора вызывается метод changeStatusDirectly()
				 */
				return false;
			}

			/*	 Повторный звонок 10 -> Завершена 3 -> Обработана 2 -> отказ 5 -> перезвонить 7 -> Завершена 3 -> В обработке 1 -> Принята 6 */
			case 10 :
			{
				$newState = 10;
				$newStatusMsg = "Повторный звонок";

				if (!$this->chkCondition("isTransfer", $params)) {
					$newState = 6;
					$newStatusMsg = "Принята";
				}

				if (
					!$this->chkCondition("appointment", $params)
					&&
					$this->chkCondition("isTransfer", $params)
				) {
					$newState = 1;
					$newStatusMsg = "В обработке";
				}

				if (
					$this->chkCondition("appointment", $params)
					&&
					$this->chkCondition("isTransfer", $params)
					&&
					!$this->chkCondition("appStatus", $params)
				) {
					$newState = 2;
					$newStatusMsg = "Обработана";
				}

				if (
					$this->chkCondition("appointment", $params)
					&&
					$this->chkCondition("isTransfer", $params)
					&&
					$this->chkCondition("appStatus", $params)
				) {
					$newState = 3;
					$newStatusMsg = "Завершена";
				}

				//ГОТОВО
				if ($this->chkCondition("isReject", $params)) {
					$newState = 5;
					$newStatusMsg = "Отказ";
				}

			}
				break;

			default :
				return false;
		}

		if (isset($newState)) {
			$request = RequestModel::model()->findByPk($this->id);
			if ($request !== null) {
				$request->saveStatus($newState);
			}

		}
	}


	/**
	 *
	 * Изменение статусов заявки со стороны ЛК
	 */
	public function changeLKStatus($params = array())
	{
		$currState = intval($this->LKStatus);
		$sql = "";
		$newState = $currState;

		if (empty($currState)) {
			// начальное состояние не определено -> переводим в состояние -  записана 1
			if (
				!empty($this->attributes['AppointmentDate'])
				&&
				$this->status != 4
			)
				$newState = 1;
		} else {
			switch ($currState) {
				case 1:
				{
					if (isset($params['LKstatus']) && $params['LKstatus'] == 2) {
						$newState = 2;
						$newStatusMsg = "Условно дошёл";
					}

					if ($this->chkCondition("LKReject", $params)) {
						$newState = 3;
						$newStatusMsg = "Отклонена партнёром";
					}
				}
					break;

				case 2:
				{
					if (isset($params['LKsuccess']) && $params['LKsuccess'] == 1) {
						$newState = 4;
						$newStatusMsg = "Принята";
					}

					if ($this->chkCondition("LKReject", $params)) {
						$newState = 3;
						$newStatusMsg = "Отклонена партнёром";
					}
				}
					break;

				case 3:
				{
					if (isset($params['LKsuccess']) && $params['LKsuccess'] == 1) {
						$newState = 4;
						$newStatusMsg = "Принята";
					}

					if (isset($params['LKstatus']) && $params['LKstatus'] == 6) {
						$newState = 6;
						$newStatusMsg = "Отказ";
					}

					if (isset($params['LKstatus']) && $params['LKstatus'] == 5) {
						$newState = 5;
						$newStatusMsg = "Завершена";
					}
				}
					break;

				/*
				case 4: {
					if ( isset($params['LKstatus']) && $params['LKstatus'] == 5 ) {
						$newState = 5;
						$newStatusMsg = "Завершена";
					}
				} break;
				*/
				default:
			}
		}

		if ($currState != $newState) {
			$sql = "UPDATE `request` SET lk_status = '" . $newState . "' WHERE req_id=" . $this->id;
			if (query($sql)) {
				$this->saveLog(array('message' => "Изменен статус ЛК -> '" . $newStatusMsg . "'", 0, 'type' => 6));
			} else {
				throw new Exception("Ошибка выполнения запроса из ЛК");
			}

			//Отклонена
			if ($newState == 3) {
				$sql = "UPDATE `request` SET status = 8 WHERE req_id=" . $this->id;
				if (query($sql)) {
					$this->saveLog(array('message' => "Изменен статус из ЛК -> 'Отклонена партнёром'", 0, 'type' => 6));
				} else {
					throw new Exception("Ошибка выполнения запроса из ЛК");
				}
			}

			// Принята
			if ($newState == 4) {
			}
		}

	}


	/**
	 * Сохранение заявки
	 *
	 * @param array $params
	 * @return int
	 *
	 * @throws Exception
	 */
	public function save($params = array())
	{
		$isNewRecord = true;

		$sqlAdd = "";

		// Проверка переданных параметров. Формирование SQL
		$city = (isset($params['city'])) ? $params['city'] : 1;

		$clinic = (isset($params['clinicId'])) ? $params['clinicId'] : '';
		$sqlAdd .= " clinic_id = " . $clinic . ", ";

		$client = (isset($params['client'])) ? $params['client'] : '';
		$client = ucwords($client);
		$phone = (isset($params['phone'])) ? $params['phone'] : '';

		$callLater = (isset($params['callLater'])) ? $params['callLater'] : '';
		$sqlAdd .= emptyToNull($callLater, 'call_later_time');

		$appointment = (isset($params['appointment'])) ? $params['appointment'] : '';
		$sqlAdd .= emptyToNull($appointment, 'date_admission');

		$app_status = (isset($params['appStatus'])) ? $params['appStatus'] : 0;
		$sqlAdd .= emptyToNull($app_status, 'appointment_status', 0);

		$doctorId = (isset($params['doctorId'])) ? $params['doctorId'] : 0;
		$sqlAdd .= emptyToNull($doctorId, 'req_doctor_id', 0);

		$diagnosticsOther = isset($params['diagnosticsOther']) ? $params['diagnosticsOther'] : '';
		$sqlAdd .= emptyToNull($diagnosticsOther, 'diagnostics_other');

		$isTransfer = (isset($params['isTransfer'])) ? $params['isTransfer'] : '';
		$sqlAdd .= emptyToNull($isTransfer, 'is_transfer');

		$departure = (isset($params['departure'])) ? $params['departure'] : '';
		$sqlAdd .= emptyToNull($departure, 'req_departure');

		$rejectReason = (isset($params['rejectReason'])) ? $params['rejectReason'] : '';
		$sqlAdd .= emptyToNull($rejectReason, 'reject_reason', 0);

		$addClientPhone = isset($params['addClientPhone']) ? $params['addClientPhone'] : '';
		$sqlAdd .= emptyToNull($addClientPhone, 'add_client_phone');

		$dateRecord = (isset($params['dateRecord'])) ? $params['dateRecord'] : '';
		$sqlAdd .= emptyToNull($dateRecord, 'date_record');

		$forListener = isset($params['for_listener']) && $params['for_listener'] ? 1 : 0;
		$sqlAdd .= " for_listener = {$forListener}, ";

		$isHot = isset($params['is_hot']) && $params['is_hot'] ? 1 : 0;
		$sqlAdd .= " is_hot = {$isHot}, ";

		$enterPoint = isset($params['enter_point']) ? $params['enter_point'] : '';
		$sqlAdd .= " enter_point = '{$enterPoint}', ";

		if (isset($params['req_type'])) {
			$sqlAdd .= " req_type = {$params['req_type']}, ";
		}

		//last string $sqlAdd
		$owner = (isset($params['owner'])) ? $params['owner'] : null;
		if (!empty($owner)) {
			$sqlAdd .= " req_user_id = {$owner}, ";
		}

		if (!empty($this->id)) {
			$isNewRecord = false;
			$sql = "UPDATE `request`
					SET
						{$sqlAdd}
						client_name         = '{$client}',
						client_phone        = '{$phone}',
						id_city             = '{$city}'
					WHERE req_id = " . $this->id;
		} else {
			$sql = "INSERT INTO `request`
					SET
						{$sqlAdd}
						client_name = '{$client}',
						client_phone = '{$phone}',
						id_city = '{$city}',
						req_created = UNIX_TIMESTAMP(NOW())
					 ";
		}

		try {
			query($sql);
			$id = ($isNewRecord) ? legacy_insert_id() : $this->id;

			if ($isNewRecord) {
				$request = RequestModel::model()->findByPk($id);
				if ($request !== null) {
					$request->saveStatus(RequestModel::STATUS_ACCEPT);
				}
			}

			return $id;
		} catch (Exception $e) {
			throw new Exception("Ошибка выполнения запроса");
		}

	}


	/**
	 * Добавляем сообщение в лог заявки
	 *
	 * @param string $params
	 */
	public function saveLog($params)
	{
		$type = (isset($params['type'])) ? $params['type'] : 2;
		$owner = (isset($params['owner'])) ? $params['owner'] : 0;
		$message = (isset($params['message'])) ? $params['message'] : "";

		if (!empty($message)) {
			$history = new RequestHistoryModel();
			$history->request_id = $this->id;
			$history->user_id = $owner;
			$history->action = $type;
			$history->text = $message;
			$history->save();
		}
	}
}
