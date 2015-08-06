<?php

namespace dfs\docdoc\api\rpc;

use dfs\docdoc\api\BaseAPI;
use dfs\docdoc\models\DoctorClinicModel;
use dfs\docdoc\models\RequestModel;

// DocDoc RPC API v1.0

class API_v100 extends BaseAPI
{
	public $clinicId = null;

	/*
	 *  Идентификатор партнера
	 *  @var int
	 */
	private $_partnerId;

	public $log = 'rpc_api.log';

	protected $_error = array(
		'PARSE_ERROR'       => array('code' => -32700, 'message' => 'Parse error'),
		'INV_REQUEST'       => array('code' => -32600, 'message' => 'Invalid Request'),
		'METHOD_NOT_FOUND'  => array('code' => -32601, 'message' => 'Method not found'),
		'INV_PARAMS'        => array('code' => -32602, 'message' => 'Invalid params'),
		'INTERNAL_ERROR'    => array('code' => -32603, 'message' => 'Internal error'),
		'INV_COMPANY'       => array('code' => -32404, 'message' => 'Invalid organization'),
		'INV_SERVICE'       => array('code' => -32405, 'message' => 'Invalid service'),
		'INV_SLOT'          => array('code' => -32406, 'message' => 'Invalid slot'),
		'INV_BOOK'          => array('code' => -32407, 'message' => 'Invalid book record'),
		'INV_REQ_STATUS'    => array('code' => -32501, 'message' => 'Invalid book state transition')
	);

	protected $_status = array(
		0 => 'ACCEPTED',
		1 => 'USER_NOTIFIED',
		2 => 'APPROVED',
		3 => 'COME',
//        4 => 'REJECTED',
		5 => 'CANCELLED_BY_USER',
		6 => 'ACCEPTED',
		7 => 'USER_NOTIFIED',
		8 => 'CANCELLED_BY_ORGANIZATION',
		9 => 'COME'
	);

	/**
	 * Конструктор. Инициализирует параметры и данные для запроса
	 *
	 * @param array $params
	 */
	public function __construct($params = []) {
		if(parent::__construct($params))
			$this->setError('INV_REQUEST');

		if($this->dataType == 'json') {
			$this->result['jsonrpc'] = "2.0";
		}

		if(isset($this->params['data'])) {
			$this->method = $this->params['data']->method;
		}

		if (isset($this->params['partnerId'])) {
			$this->_partnerId = $this->params['partnerId'];
		}
		return true;
	}

	/*
	 * Получение свободных слотов
	 */
	public function getSlots() {
		$params = $this->params['data']->params;

		if(!isset($params[1]))
			$this->setError('INV_SERVICE');
		else {
			$params[1] = is_array($params[1]) ? $params[1] : array($params[1]);
			if($this->setClinic()){
				$data = array();
				$startTime = time();
				$endTime = $startTime + 3600*24*7;
				if(isset($params->attributes->from) && isset($params->attributes->to)){
					$startTime = strtotime($params->attributes->from);
					$endTime = strtotime($params->attributes->to);
				}

				if(isset($params[2])){
					$params[2] = is_array($params[2]) ? $params[2] : array($params[2]);
					$result = array();
					$sched = new \Schedule();
					$sched->setClinic($this->clinicId);
					$i = 0;
					foreach($params[2] as $doctorId){
						if(!$this->isDoctorAvailable($doctorId))
							return $this->setError('INV_PARAMS');
						$sched->setDoctor($doctorId);
						$curTime = $startTime;
						while($curTime <= $endTime){
							$times = normalizeInterval($sched->getSchedulePool(date('d.m.Y', $curTime)), $sched->getStep());
							foreach($times as $time){
								$result[$i]['resourceId'] = "$doctorId";
								$result[$i]['slotId'] = "$doctorId-".strtotime(date('Y-m-d', $curTime).' '.$time[0]);
								$result[$i]['attributes']['from'] = date('Y-m-d', $curTime).'T'.$time[0];
								$result[$i]['attributes']['to'] = date('Y-m-d', $curTime).'T'.$time[1];
								$i++;
							}
							$curTime += 3600*24;
						}
					}
					$this->setResult($result);
				} else {

				}
			} else {
				$this->setError('INV_COMPANY');
			}
		}

	}


	/*
	 * Записаться на прием
	 */
	public function book() {
		$result = null;

		require_once ROOT_PATH. "/back/public/api/service/createRequest.php";

		$params = $this->params['data']->params[0];

		if($this->setClinic()) {

			if(!isset($params->bookType) || !isset($params->bookId))
				return $this->setError('INV_PARAMS');

			if($params->bookType == 'dynamic' || $params->bookType == 'dynamic_service_only' || $params->bookType == 'dynamic_resource_only') {
				if(!isset($params->slotIds))
					return $this->setError('INV_SLOT');
			}
			/*else {
				if(!isset($params->serviceId))
					return $this->setError('INV_SERVICE');
				if(!isset($params->resourceId))
					return $this->setError('INV_PARAMS');
			}*/

			$data['clinic'] = $this->clinicId;
			$data['bookId'] = $params->bookId;
			$data['partner'] = $this->_partnerId;
			$data['reqType'] = 0;
			$data['source'] = 4;

			$data['client'] = (isset($params->fullname) && !empty($params->fullname)) ? $params->fullname : 'Yandex';
			$data['phone'] = isset($params->phone) ? $params->phone : null;
			$data['doctor'] = isset($params->resourceId) ? $params->resourceId : null;
			$data['slotIds'] = isset($params->slotIds) ? $params->slotIds : null;
			$data['comment'] = isset($params->comment) ? $params->comment : null;
			$data['dateTime'] = isset($params->dateTime) ? $params->dateTime : null;

			if(isset($params->serviceId) && !empty($params->serviceId))
				$data['speciality'] = (int) $params->serviceId;

			if(!empty($data['client']) && !empty($data['phone'])){

				if($params->bookType == 'dynamic' || $params->bookType == 'dynamic_service_only' || $params->bookType == 'dynamic_resource_only'){
					if(!empty($data['slotIds'])){
						if($scheduleIds = $this->reserveSlots($data['slotIds'])){
							$data['doctorScheduleIds'] = $scheduleIds;
							$result = createRequest($data, $params->bookType);
						} else
							return $this->setError('INV_SLOT');
					}
				} else {
					if(!empty($data['dateTime']))
						$data['time'] = strtotime($data['dateTime']);
					if(!empty($data['doctor'])) {
						if($this->isDoctorAvailable($data['doctor']))
							$result = createRequest($data, $params->bookType);
						else
							$this->setError('INV_PARAMS');
					} else
						$result = createRequest($data, $params->bookType);
				}

			} else
				$this->setError('INV_PARAMS');

			if(isset($result['Response']['status']) && $result['Response']['status'] == 'success'){
				$result = array(
					'status' => 'ACCEPTED',
					'url'    => 'http://docdoc.ru/request/thanks/id/'.$data['bookId']
				);
				$this->setResult($result);
			} else {
				$this->setError('INV_PARAMS');
			}

		}
	}

	/*
	 * Получение статуса по заявке
	 */
	public function getBookStatus(){
		if(isset($this->params['data']->params[0])){
			$bookId = $this->params['data']->params[0];
			$sql = "SELECT t1.req_status AS status
                    FROM request t1
                    LEFT JOIN request_4_remote_api t2 ON t2.request_id=t1.req_id
                    WHERE t2.request_api_id='".$bookId."'";
			$result = query($sql);
			if(num_rows($result) == 1) {
				$request = fetch_array($result);
				if(isset($this->_status[$request['status']]))
					$this->setResult($this->_status[$request['status']]);
				else
					$this->setError('INTERNAL_ERROR');
			} else {
				$this->setError('INV_BOOK');
			}
		} else {
			$this->setError('INV_PARAMS');
		}
	}

	/*
	 * Отменить заявку
	 */
	public function cancelBook(){
		if(isset($this->params['data']->params[0])){
			$bookId = $this->params['data']->params[0];
			$partnerId = 1;
			$sql = "SELECT request_id, doctor_schedule_ids FROM request_4_remote_api WHERE request_api_id='".$bookId."'";
			$result = query($sql);
			if(num_rows($result) == 1) {
				$row = fetch_array($result);
				$requestId = $row['request_id'];
				$scheduleIds = unserialize($row['doctor_schedule_ids']);

				$result = query("START TRANSACTION");
				$sql = "UPDATE request SET is_hot=0 WHERE req_id=".$requestId;
				$result = query($sql);
				if (!$result) {
					$this->setError('INTERNAL_BOOK');
				}

				$request = RequestModel::model()->findByPk($requestId);
				if ($request !== null) {
					$request->saveStatus(RequestModel::STATUS_REJECT);
				}

				$sql = "INSERT INTO `request_history` SET
                            request_id = " . $requestId . ",
                            created = now(),
                            action = 1,
                            user_id = 0,
                            text = 'Статус заявки изменен через API. Партнёр #" . $partnerId . "'";
				$result = query($sql);
				if (!$result) {
					$this->setError('INTERNAL_BOOK');
				}
				if(count($scheduleIds) > 0){
					$sql = "SELECT req_doctor_id, clinic_id FROM request WHERE req_id=".$requestId;
					$result = query($sql);
					$row = fetch_array($result);
					$sched = new \Schedule();
					$sched->setDoctor($row['req_doctor_id']);
					$sched->setClinic($row['clinic_id']);
					foreach($scheduleIds as $scheduleId)
						$sched->cancelRecord($scheduleId);
				}
				$result = query("commit");
				$this->setResult(1);
			} else {
				$this->setError('INV_BOOK');
			}
		} else {
			$this->setError('INV_PARAMS');
		}
	}

	/*
	 * Резервирование слотов
	 * @param array $slots
	 * @return boolean
	 */
	protected function reserveSlots($slots){
		$resultData = array();

		foreach($slots as $slot){
			$data = explode('-', $slot);
			if(count($data) == 2){
				$doctorId = $data[0];
				if($this->isDoctorAvailable($doctorId)) {
					$date = date('d.m.Y h:i', $data[1]);
					$sched = new \Schedule();
					$sched->setDoctor($doctorId);
					$sched->setClinic($this->clinicId);
					if($result = $sched->reserveRecord4DoctorExt($date, 'partner=1')){
						if($result == -1)
							return false;
						else
							$resultData[] = $result;
					}
				} else {
					$this->setError('INV_SLOT');
				}
			}
		}

		return $resultData;
	}

	/**
	 * Проверка врача на возможногсть записи к врачу
	 *
	 * @param integer $doctorId
	 *
	 * @return boolean
	 */
	protected function isDoctorAvailable($doctorId) {
		$sql = "SELECT DISTINCT t1.id
                FROM doctor t1
                INNER JOIN doctor_4_clinic t2 ON t2.doctor_id=t1.id and t2.type = " . DoctorClinicModel::TYPE_DOCTOR . "
                INNER JOIN clinic t3 ON t3.id=t2.clinic_id
                WHERE t1.status=3
                    AND t3.status=3
                    AND t1.id=".$doctorId;
		$result = query($sql);
		if(num_rows($result) == 1)
			return true;
		else
			return false;
	}

	/*
	 * Вызов метода API
	 * @return boolean
	 */
	protected function callMethod () {

		try {
			if(!$this->validateRequest())
				return false;

			if(method_exists($this, $this->method)) {

				if(isset($this->params['data']->params))
					call_user_func(array('self', $this->method));
				else
					$this->setError('INV_PARAMS');
			} else {
				$this->setError('METHOD_NOT_FOUND');
			}

			$this->result['id'] = $this->params['data']->id;

		} catch(Exception $e){
			$this->setError('INTERNAL_ERROR');
		}

	}

	/*
	 * Установить ID клиники
	 * @return boolean
	 */
	protected function setClinic(){
		//var_dump($this->params['data']->params);
		if(isset($this->params['data']->params[0]->organizationId))
			$this->clinicId = $this->params['data']->params[0]->organizationId;
		elseif(isset($this->params['data']->params[0]))
			$this->clinicId = $this->params['data']->params[0];
		else
			return $this->setError('INV_COMPANY');

		$sql = "SELECT COUNT(*) AS cnt
                    FROM clinic
                    WHERE open_4_yandex='yes' AND status=3 AND id=".$this->clinicId;
		$result = query($sql);
		$row = fetch_array($result);
		if($row['cnt'] == 1) {
			return true;
		} else {
			return $this->setError('INV_COMPANY');
		}
	}

	/*
	 * Установить результат
	 */
	protected function setResult ($data) {
		$this->result['result'] = $data;
		$this->addLog('Success');
	}

	/*
	 * Установить ошибку
	 * @param string $errorCode
	 * @return boolean
	 */
	protected function setError ($error) {
		$extData = '';
		if(isset($this->params['data']))
			$extData = ' - '.json_encode($this->params['data']);

		if(isset($this->_error[$error])){
			$this->result['error'] = $this->_error[$error];
			$this->addLog('Error: '.$this->_error[$error]['message'].$extData);
		} else {
			$this->result['error'] = array('code' => -404, 'message' => $error);
			$this->addLog('Error: '.$error.$extData);
		}

		return false;

	}

	/*
	 * Валидация запроса на соответсвие JSON RPC 2.0
	 * @return boolean
	 */
	protected function validateRequest() {
		$data = $this->params['data'];

		if(!isset($data->jsonrpc)
			|| !isset($data->id)
			|| !isset($data->params)
			|| $data->jsonrpc != '2.0')
		{
			$this->setError('INV_REQUEST');
			$this->result['id'] = null;
			return false;
		}

		return true;
	}

}

?>
