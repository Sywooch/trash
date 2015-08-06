<?php
use dfs\docdoc\models\SmsQueryModel;

	require_once dirname(__FILE__)."/../../lib/php/user.class.php";
	require_once dirname(__FILE__)."/../../lib/php/smsQuery.class.php";
	require_once dirname(__FILE__)."/../../include/common.php";
	require_once dirname(__FILE__)."/../../lib/php/validate.php";


	$user = new user();	 
	$user -> checkRight4page(array('ADM'),'simple');
	

	$lineList	= (isset ($_POST['line']))? $_POST['line'] : array();
	
	if ( count($lineList) > 0 ) {
		
			foreach ($lineList as $key => $data ) {

				$smsModel = SmsQueryModel::model()->findByPk($key);

				if ($smsModel && $smsModel->delete()) {
					$msg = "Удаление SMS из очереди (" . intval($key) . ")";
				} else {
					echo htmlspecialchars(json_encode(array('error' => 'Ошибка удаления сообщения: ' . $key)), ENT_NOQUOTES);
					exit;
				} 
			}
			echo htmlspecialchars(json_encode(array('status'=>'success' )), ENT_NOQUOTES);
			exit;
	
	} else {
		echo htmlspecialchars(json_encode(array('error'=>'Не переданны идентификаторы позиций' )), ENT_NOQUOTES);
		exit;
	}
