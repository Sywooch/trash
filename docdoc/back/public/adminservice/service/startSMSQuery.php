<?php	   	  
	require_once dirname(__FILE__)."/../../lib/php/user.class.php";
	require_once dirname(__FILE__)."/../../lib/php/smsQuery.class.php";
	require_once dirname(__FILE__)."/../../include/common.php";
	require_once dirname(__FILE__)."/../../lib/php/validate.php";


	$user = new user();	 
	$user -> checkRight4page(array('ADM'),'simple');
	

	$action	= ( isset($_GET["action"]) ) ? checkField ($_GET["action"], "e", "", true, array("start", "stop")) : "";
	
	if ( !empty ($action)) {
		if ( $action == 'start') {
			
			startSMSquery("SMS рассылка запущена администратором ".date("d.m.Y H:m:i"));
			echo htmlspecialchars(json_encode(array('status'=>'start' )), ENT_NOQUOTES);
		} else if ( $action == 'stop' ) {
			stopSMSquery("SMS рассылка остановлена администратором ".date("d.m.Y H:m:i"));
			echo htmlspecialchars(json_encode(array('status'=>'stop' )), ENT_NOQUOTES);
		} else {
			echo htmlspecialchars(json_encode(array('error'=>'Ошибочное действие' )), ENT_NOQUOTES);
		}

	} else {
		echo htmlspecialchars(json_encode(array('error'=>'Не переданно действие' )), ENT_NOQUOTES);
	}
