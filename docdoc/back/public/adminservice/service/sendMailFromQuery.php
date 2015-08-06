<?php	   	  
	require_once dirname(__FILE__)."/../../lib/php/user.class.php";
	require_once dirname(__FILE__)."/../../include/common.php";
	require_once dirname(__FILE__)."/../../lib/php/validate.php";
	include_once dirname(__FILE__)."/../../lib/php/mail.php";	


	define ("maxTrySendMessage", 5); //Колличество попыток отправки почты
	define ("maxCount4Send", 10); //Колличество писем отправляемых за один раз

	$sendMessage = 0;
	$readMessage = 0;
	
		
	$user = new user();	 
	$user -> checkRight4page(array('ADM'),'simple');

	$sql = "SELECT idMail as id, crDate, resendCount
			FROM mailQuery
			ORDER BY crDate ASC
			limit ".maxCount4Send;
		
	try{
		$result = query($sql);				
		while ($row = fetch_object($result)) {
			if (!sendMessageById($row->id)) { // Сообщение не отправлено
				if ( $row->resendCount >= maxTrySendMessage) {
					delMail ( $row->id ); // Удалить сообщение из списка рассылки
				}else{
					repeatMail( $row->id, $row->crDate, $row->resendCount ); //Поставить в очередь на повторную отправку
				}
			} else {
				delMail($row->id); // Сообщение ушло - чистим базу
				$sendMessage ++;
			}
			$readMessage ++;
		}
		
		$msg = "Ручной запуск e-mail рассылки";
		$log = new logger();
		$log -> setLog( $user -> idUser, 'S_EML', $msg);
		echo htmlspecialchars(json_encode(array('status'=>'success', 'sendMessage' => $sendMessage, 'readMessage' => $readMessage )), ENT_NOQUOTES);
		exit;
	} catch (Exception $e) {
		echo htmlspecialchars(json_encode(array('error'=>'Ошибка. '.$e->getMessage())), ENT_NOQUOTES);
		exit;
	}	


	
	
	
	function delMail ($id){
		$id = intval($id);
		
		$result = query("START TRANSACTION");
		
		$sql = "DELETE FROM
					mailQuery
				WHERE
					idMail = ".$id;

		queryJS ($sql, 'Ошибка удаления рассылки');
		$result = query("commit");
	}
	
	
	function repeatMail ($id, $crDate, $cnt){
		$cnt++;
		
		$result = query("START TRANSACTION");
		$sql = "UPDATE mailQuery SET 
					resendCount = ".$cnt.",
					crDate = DATE_ADD(NOW(), INTERVAL 1 DAY)  
				WHERE idMail=".$id;
		queryJS ($sql, 'Ошибка обновления времени рассылки');
		$result = query("commit");

	}
