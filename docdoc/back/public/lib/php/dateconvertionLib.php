<?php	
	function convertDate2DBformat ($dateIn) {
		$dateAr = explode(".",$dateIn);
		if ( count ($dateAr) == 3 )
			return "$dateAr[2]-$dateAr[1]-$dateAr[0]";
		else
			return $dateIn; 	
				
	}
	
	
	
	function convertDate2JSformat($dateIn) {
		$dateAr = explode("-",$dateIn);
		
		$dateNew = "$dateAr[2].$dateAr[1].$dateAr[0]";
		return $dateNew;
	}	
	
	
	
	function getWeekNumber ($dateIn = '') {	
		$dateAr = explode(".",$dateIn);
		$tm = mktime(0, 0, 0, $dateAr[1], $dateAr[0], $dateAr[2]);	
		return date("W",$tm);
	}	  
	
	
	
	function getWeekDayNumber ($dateIn = '') {	 
		$weekDay = "";
		
		$dateAr = explode(".",$dateIn);
		$tm = mktime(0, 0, 0, $dateAr[1], $dateAr[0], $dateAr[2]);	  
		$weekDay = date("w",$tm);
		if ($weekDay == 0) {$weekDay = 7;}
		return $weekDay;
	}
	
	
	
	function getWeekDay ($dateIn = '') {	 
		$weekDay = "";	
		$rusDayWeek = array ("понедельник", "вторник", "среда", "четверг", "пятница", "суббота", "восткресенье");
		
		$dateAr = explode(".",$dateIn);
		$tm = mktime(0, 0, 0, $dateAr[1], $dateAr[0], $dateAr[2]);	  
		return $rusDayWeek[date("w",$tm)];
	}	   


?>