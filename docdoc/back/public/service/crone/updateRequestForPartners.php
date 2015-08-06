<?php

require_once dirname(__FILE__)."/../../include/common.php";
require_once dirname(__FILE__)."/../../remoteApi/lib/Yandex.php";
require_once dirname(__FILE__)."/../../lib/php/croneLocker.php";
require_once dirname(__FILE__).'/../../include/croneList.php';

$croneName = 'croneUpdateRequestForPartners';
$crone = croneList::getConfig($croneName);
$statusPath = LOCK_FILE_CRONE_DIR.$crone['file'];
$status = getCroneStatusParam('', $statusPath);

if(isset($status['isAvailableGlobal']) && $status['isAvailableGlobal'] == 'true'){ // Если глобального разрешения не существует, значит нельзя.

	// Работаем с максимальным количеством зависаний и автоматической разблокировкой
	if(!isset($status['isAvailable']) || $status['isAvailable'] != 'true'){
		if(!isset($status['maxLockedTry']) || $status['maxLockedTry'] >= croneUpdateRequestForPartners_MaxLockedTry){
			$status['isAvailable'] = 'true';
			$status['maxLockedTry'] = '0';
			saveCronStatusParam(array('maxLockedTry' => '0'), $statusPath, $croneName);
		}
	}
	
	if(empty($status['isAvailable']) || $status['isAvailable'] != 'false'){ // А, если не существует разрешения от предыдущего процесса, то даем его.

		saveCronStatusParam(array('isAvailable' => 'false'), $statusPath, $croneName);
		if($status['maxLockedTry'] != 0){
			saveCronStatusParam(array('maxLockedTry' => 0), $statusPath, $croneName);
		}


		$sql = "SELECT t1.req_id, t1.req_status, t1.date_admission,
					   t2.request_api_id, t3.doctor_id, t3.schedule_step
				FROM request t1
				LEFT JOIN request_4_remote_api t2 ON t2.request_id=t1.req_id
				LEFT JOIN doctor_4_clinic t3 ON (t3.doctor_id=t1.req_doctor_id AND t3.clinic_id=t1.clinic_id)
				WHERE t2.update_status='yes'";
		$result = query($sql);

		while($row = fetch_object($result)){

			$api = new Yandex();

			// Update statuses
			$params = array();
			$params['id'] = $row->request_api_id;
			$params['status'] = $row->req_status;
			$response1 = $api->updateBookStatus($params);

			$response2 = true;
			if(!empty($row->doctor_id) && !empty($row->date_admission)){
				// Update slots for resources
				$params = array();
				$params['doctorId'] = $row->doctor_id;
				$params['startTime'] = $row->date_admission;
				$params['step'] = $row->schedule_step;
				$response2 = $api->reportSlots($params);
			}

			if(/*$response1 &&*/ $response2){
				$sql = "UPDATE request_4_remote_api 
						SET update_status = 'no' 
						WHERE request_id=".$row->req_id;
				query($sql);
			}

		}
		saveCronStatusParam(array('isAvailable' => 'true'), $statusPath, $croneName);
	}else{
		echo "Заблокированно предыдущим процессом\n";
		saveCronStatusParam(array('maxLockedTry' => ++$status['maxLockedTry']), $statusPath, $croneName);
		$log = new commonLog($croneName.'.log', 'File  was locked latest process');	
	}
}else{
	echo "Заблокированно администратором системы\n";
	$log = new commonLog($croneName.'.log', 'File  was locked system administrator');
}
