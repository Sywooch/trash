<?php
use dfs\docdoc\models\SmsQueryModel;

	/* Отправка SMS рассылки  */
	set_time_limit(30);

	require_once dirname(__FILE__)."/../../include/common.php";
	require_once dirname(__FILE__)."/../../lib/php/smsQuery.class.php";
	require_once dirname(__FILE__)."/../../lib/php/emailQuery.class.php";
	require_once dirname(__FILE__)."/../../lib/php/croneLocker.php";
	require_once dirname(__FILE__).'/../../include/croneList.php';
	
	
	//register_shutdown_function('shutdown');
	$croneName = 'croneSMS';
	define ("logName", $croneName.".log");
	
	
	
	$crone = croneList::getConfig($croneName);
	$statusPath = LOCK_FILE_CRONE_DIR.$crone['file'];
	$status = getCroneStatusParam('', $statusPath);



	$log = new commonLog(logName, "Start ".$croneName );

	// Если глобального разрешения не существует, значит нельзя.
	if ( isset($status['isAvailableGlobal']) && $status['isAvailableGlobal'] == 'true' ) { 
		
		// Работаем с максимальным количеством зависаний и автоматической разблокировкой
		if ( !isset($status['isAvailable']) || $status['isAvailable'] != 'true'){
			if ( !isset($status['maxLockedTry']) || $status['maxLockedTry'] >= croneSMS_MaxLockedTry){
				$status['isAvailable'] = 'true';
				$status['maxLockedTry'] = '0';
				saveCronStatusParam(array('maxLockedTry' => '0'), $statusPath, $croneName);
			}
		}
		
		// Если не существует разрешения от предыдущего процесса, то даем его.
		if ( empty($status['isAvailable']) || $status['isAvailable'] != 'false' ) { 
			saveCronStatusParam(array('isAvailable' => 'false'), $statusPath, $croneName);
			$log = new commonLog(logName, "File  ".$croneName." locked" );
			
			if ( $status['maxLockedTry'] != 0 ) {
				saveCronStatusParam(array('maxLockedTry' => 0), $statusPath, $croneName);
				$log = new commonLog(logName, "maxLockedTry set 0" );
			}
			
	
			/* Отправка SMS рассылки  */
			if (checkSMSquery() ) {

				// Проверка на взлом рассылки
				if ( filterSending() ) {

					$sql = "SELECT
								sms.idMessage as id,
								sms.phoneTo,
								sms.message,
								sms.status,
								sms.gateId
							 FROM
								SMSQuery sms
							 WHERE
								sms.status = 'new'
							ORDER BY sms.priority, sms.CrDate ASC
							LIMIT ".SMS_POOL;
					//echo $sql;
					$result = query($sql);
					while ($row = fetch_object($result)) {

						// Проверяем большое количество отправляемых SMS на данный номер
						if ( filterSending4Number($row -> phoneTo) ) {
							$smsModel = SmsQueryModel::model()->findByPk($row->id);
							if ($smsModel && $smsModel->sendSms() ) {
							} else {
								setSMSstatus($row -> id, 'error');
							}
						} else {
							saveCronStatusParam(array('isAvailable' => 'true'), $statusPath, $croneName);
							exit;  // выход из рассылки для предотвращения цикличной отправки SMS администратору
						}
					}
				}
			} else {
				echo "Рассылка отключена";
			}
			saveCronStatusParam(array('isAvailable' => 'true'), $statusPath, $croneName);
		}else{
			echo "Заблокированно предыдущим процессом\n";
			saveCronStatusParam(array('maxLockedTry' => ++$status['maxLockedTry']), $statusPath, $croneName);
			$log = new commonLog(logName, 'File  was locked latest process');	
		}
	} else {
		echo "Заблокированно администратором системы\n";
		$log = new commonLog(logName, 'File  was locked system administrator');
	}
	
	$log = new commonLog(logName, " " );
	$log = new commonLog(logName, " " );




	function boll2str ($x) {
		return (is_bool($x) ? ($x ? "true":"false"):$x);
	}








//	Проверка на резкое возрастание отправляемых сообщений
function filterSending () {
	global $ADMIN_SMS_PHONE;

	$sql = "SELECT
				COUNT(*) AS cnt
			 FROM
					SMSQuery sms
			 WHERE 
				sms.status = 'new'";
	//echo $sql;
	$result = query($sql);
	$row = fetch_object($result);
	$cnt =  $row -> cnt;

	if ( $cnt > SMS_LIMIT ) {
		$mailBody = "SMS очередь остановлена. Превышен лимит запросов: ".$cnt." из ".SMS_LIMIT." от ".date("d.m.Y H:i");
		stopSMSquery($mailBody);

		$params = array(
			"emailTo" => Yii::app()->params['email']['support'],
			"message" => $mailBody,
			"subj" => $mailBody." Сервер: ".SERVER_BACK,
			"priority" => 1
		);
		@emailQuery::addMessage($params); 


		foreach ($ADMIN_SMS_PHONE as $phones ) {
			SmsQueryModel::sendSmsToNumber($phones, $mailBody, SmsQueryModel::TYPE_SYSTEM_MSG, true);
		}
		return false;
	}
	return true;	
}



//	Проверка на резкое возрастание отправляемых сообщений для конкретного номера
function filterSending4Number ($phone) {
	global $ADMIN_SMS_PHONE;

	$sql = "SELECT
				COUNT(*) AS cnt
			 FROM
				 SMSQuery sms
			 WHERE 
				sms.status = 'new'
				AND 
				sms.phoneTo = '".$phone."'";
	//echo $sql;
	$result = query($sql);
	$row = fetch_object($result);
	$cnt =  $row -> cnt;
	//echo $cnt; 

	if ( $cnt > SMS_LIMIT_PER_PHONE ) {

		$message = "Блокировка номера ".$phone.". Превышен лимит запросов: ".$cnt." из ".SMS_LIMIT_PER_PHONE." от ".date("d.m.Y H:i");
		$params = array(
			"emailTo" => Yii::app()->params['email']['support'],
			"message" => $message,
			"subj" => $message." Сервер: ".SERVER_BACK,
			"priority" => 1
		);
		@emailQuery::addMessage($params); 

		foreach ($ADMIN_SMS_PHONE as $phones ) {
			SmsQueryModel::sendSmsToNumber($phones, $message, SmsQueryModel::TYPE_SYSTEM_MSG, true);
		}


		$sql = "UPDATE 
					SMSQuery
				SET
					status = 'error'
				WHERE
					status = 'new'
					AND
					phoneTo = '".$phone."'";
		//echo $sql;
		$result = query($sql);

		return false;
	}
	return true;

}

function setSMSstatus ($id, $status) {
	$id = intval($id);

	$sql = "UPDATE SMSQuery SET
					status = '".$status."',
					sendDate = now()
			WHERE idMessage = ".$id;
	//echo $sql;
	try {
		$result = query($sql);
		return true;
	} catch (Exception $e) {
		return false;
	}
}
