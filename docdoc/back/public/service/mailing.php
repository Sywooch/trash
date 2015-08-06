<?php
	require_once 	dirname(__FILE__)."/../include/common.php";
	require_once 	dirname(__FILE__)."/../lib/php/errorLog.php";
	include_once   	dirname(__FILE__)."/../lib/php/mail.php";	
	
	/* Отправка почтовой рассылки  */
	$cnt = 10; //Колличество писем отправляемых за один раз
	$i = 0;

	$sql = "SELECT idMail as id, crDate, resendCount
			FROM mailQuery
			ORDER BY crDate ASC
			limit ".$cnt;
		
	try{
		$result = query($sql);				
		if (!$result) throw new Exception("Ошибка выполнения запроса");			
			while ($row = fetch_object($result)) {
				if (!sendMessageById($row->id)) {
					if ($row->resendCount >= 3) {
						delMail($row->id); // Удалить сообщение из списка рассылки
					}else{
						repeatMail($row->id, $row->crDate, $row->resendCount); //Поставить в очередь на повторную отправку
					}
				} else {
					delMail($row->id); // Сообщение ушло - чистим базу
				}
			}
	} catch (Exception $e) {
		echo 	$e->getMessage();			
		$errorMsg[] = $e->getMessage();
		Yii::app()->session["errorMsg"] = $errorMsg;
	}	

	

	
	
	
	function delMail ($id){
		$id = intval($id);
		
		$sql = "DELETE FROM
					mailQuery
				WHERE
					idMail = ".$id;

		try{
			$result = query($sql);
			if (!$result) throw new Exception("Ошибка удаления рассылки");	
			return TRUE;
		} catch (Exception $e) {
			echo 	$e->getMessage();			
			$errorMsg[] = $e->getMessage();
			Yii::app()->session["errorMsg"] = $errorMsg;
		}
		return FALSE;
	}
	
	
	function repeatMail ($id, $crDate, $cnt){
		$cnt++;
		$sql = "UPDATE mailQuery SET 
					resendCount = ".$cnt.",
					crDate = DATE_ADD(NOW(), INTERVAL 1 DAY)  
				WHERE idMail=".$id;
		try{
			$result = query($sql);
			if (!$result) throw new Exception("Ошибка обновления времени рассылки");	
			return TRUE;
		} catch (Exception $e) {			
			$errorMsg[] = $e->getMessage();
			Yii::app()->session["errorMsg"] = $errorMsg;
		}
		return FALSE;
	} 	
