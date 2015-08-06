<?php
/* Отправка E-mail рассылки  */
set_time_limit(30);

require_once dirname(__FILE__)."/../../include/common.php";
require_once dirname(__FILE__)."/../../lib/php/emailQuery.class.php";
require_once dirname(__FILE__)."/../../lib/php/mail.php";
require_once dirname(__FILE__)."/../../lib/php/croneLocker.php";
require_once dirname(__FILE__).'/../../include/croneList.php';

$croneName = "croneEmail";
$crone = croneList::getConfig($croneName);
$statusPath = LOCK_FILE_CRONE_DIR.$crone['file'];
$status = getCroneStatusParam('', $statusPath);

$log = new commonLog($croneName.".log", "Start ".$croneName );


if(isset($status['isAvailableGlobal']) && $status['isAvailableGlobal'] == 'true'){ // Если глобального разрешения не существует, значит нельзя.
	
	// Работаем с максимальным количеством зависаний и автоматической разблокировкой
	if(!isset($status['isAvailable']) || $status['isAvailable'] != 'true'){
		if(!isset($status['maxLockedTry']) || $status['maxLockedTry'] >= croneEmail_MaxLockedTry){
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
		$log = new commonLog($croneName.'.log', 'File  '.$croneName.' locked');

		$sql = "SELECT
					idMail as id, 
					DATE_FORMAT( crDate,'%d.%m.%Y %H:%i') AS CrDate
				FROM `mailQuery` 
				WHERE 
					status = 'new'
					OR 
					status = 'resend'
					AND 
					(
					resendCount < ".EMAIL_TRY_COUNT."  
					OR 
					resendCount IS NULL
					)  
				ORDER BY CrDate ASC";
		$result = query($sql);

		$log = new commonLog($croneName.".log", "Get ".num_rows($result)." emails ");
		while ($row = fetch_object($result)) {
			try {
				$mail = sendMessageById($row->id);
				$log = new commonLog($croneName.".log", "Message send to ".$mail->emailTo." Subj: ".$mail->subj );

				$mail->delete();
				$log = new commonLog($croneName.".log", "Message deleted");
			} catch( Exeption $e ) {
				$log = new commonLog($croneName.".log", "Exeption: ".$e);

				saveCronStatusParam(array('isAvailable' => 'true'), $statusPath, $croneName);
				$log = new commonLog($croneName.".log", "File  ".$croneName." unlocked");

				$log = new msgLog("Ошибка отправки сообщщения. Id=".$row ->id." ");

				emailQuery::addCount($row ->id);
				$log = new commonLog($croneName.".log", "Add try count to ".$row ->id);
			}
		}
		saveCronStatusParam(array('isAvailable' => 'true'), $statusPath, $croneName);
		$log = new commonLog($croneName.".log", "File  ".$croneName." unlocked");

	} else {
		echo "Заблокированно предыдущим процессом\n";
		saveCronStatusParam(array('maxLockedTry' => ++$status['maxLockedTry']), $statusPath, $croneName);
		$log = new commonLog($croneName.".log", "File  was locked latest process");
	}
}else{
	echo "Заблокированно администратором системы\n";
	$log = new commonLog($croneName.'.log', 'File  was locked system administrator');
}

$log = new commonLog($croneName.".log", " " );
$log = new commonLog($croneName.".log", " " );

