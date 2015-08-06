<?php	   	  
	require_once dirname(__FILE__)."/../../lib/php/user.class.php";
	require_once dirname(__FILE__)."/../../include/common.php";
	require_once dirname(__FILE__)."/../../lib/php/validate.php";


	$user = new user();	 
	$user -> checkRight4page(array('ADM'),'simple');
	

	$cnt = 0;

	$sql = "SELECT
				count(idMail) as cnt
			 FROM
			 	mailQuery";
		
	try{
		$result = query($sql);				
		$row = fetch_object($result);
		$cnt = $row -> cnt;
		
		$result = query("DELETE FROM mailQuery");
		
		$msg = "Удаление e-mail рассылки (все записи)";
		$log = new logger();
		$log -> setLog( $user -> idUser, 'D_EML', $msg);
		echo htmlspecialchars(json_encode(array('status'=>'success', 'message' => $cnt )), ENT_NOQUOTES);
		exit;
	} catch (Exception $e) {
		echo htmlspecialchars(json_encode(array('error'=>'Ошибка. '.$e->getMessage())), ENT_NOQUOTES);
		exit;
	}	
