<?php	   	  
	require_once dirname(__FILE__)."/../../lib/php/user.class.php";
	require_once dirname(__FILE__)."/../../include/common.php";
	require_once dirname(__FILE__)."/../../lib/php/validate.php";


	$user = new user();	 
	$user -> checkRight4page(array('ADM'),'simple');
	

	$lineList	= (isset ($_POST['line']))? $_POST['line'] : array();
	if ( count($lineList) > 0 ) {
		$result = query("START TRANSACTION");
		
		try{
			foreach ($lineList as $key => $data ) { 
				$sql = "DELETE FROM mailQuery WHERE idMail = ".intval($key);
				$result = query($sql);
				
				$msg = "Удаление записи из e-mail рассылки (".intval($key).")";
				$log = new logger();
				$log -> setLog( $user -> idUser, 'D_ELL', $msg);
			}
			
			$result = query("commit");
			echo htmlspecialchars(json_encode(array('status'=>'success' )), ENT_NOQUOTES);
			exit;

		} catch (Exception $e) {
			echo htmlspecialchars(json_encode(array('error'=>'Ошибка. '.$e->getMessage())), ENT_NOQUOTES);
			exit;
		}	
	} else {
		echo htmlspecialchars(json_encode(array('error'=>'Не переданны идентификаторы позиций' )), ENT_NOQUOTES);
		exit;
	}
