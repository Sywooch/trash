<?php

use dfs\docdoc\models\RequestModel;

    require_once dirname(__FILE__)."/../include/common.php";
    require_once dirname(__FILE__)."/../lib/php/RemoteAPI.php";
    require_once LIB_PATH."php/request.class.php";
    require_once LIB_PATH.'php/schedule.class.php';
    
    $requestId = isset($_POST['requestId']) ? checkField($_POST['requestId'], 'i', null) : null;
    
    if(!empty($requestId)){
        $partnerId = 1; // Yandex API
        $sql = "SELECT request_id, doctor_schedule_ids FROM request_4_remote_api WHERE request_id='".$requestId."'";
        $result = query($sql);
        if(num_rows($result) == 1) {
            $row = fetch_array($result);
            $requestId = $row['request_id'];
            $scheduleIds = unserialize($row['doctor_schedule_ids']);

			$request = RequestModel::model()->findByPk($requestId);
			if ($request !== null) {
				$request->saveStatus(RequestModel::STATUS_REJECT);
			}


			$result = query("START TRANSACTION");
            $sql = "INSERT INTO `request_history` SET
                        request_id = " . $requestId . ", 
                        created = now(), 
                        action = 1, 
                        user_id = 0, 
                        text = 'Статус заявки изменен через API. Партнёр #" . $partnerId . "'";
            $result = query($sql);
            if (!$result) {
                queryJS($sql, 'error');
            }  
            if(count($scheduleIds) > 0){
                $sql = "SELECT req_doctor_id, clinic_id FROM request WHERE req_id=".$requestId;
                $result = query($sql);
                $row = fetch_array($result);
                $sched = new Schedule();
                $sched->setDoctor($row['req_doctor_id']);
                $sched->setClinic($row['clinic_id']);
                foreach($scheduleIds as $scheduleId)
                    $sched->cancelRecord($scheduleId);
            }
            $result = query("commit");
            setSuccess();

        } else {
            queryJS($sql, 'error');
        }
    } else {
        queryJS($sql, 'error');
    }
    

?>
