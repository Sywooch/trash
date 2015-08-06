<?php
	function formatTime( $time )
	{
		$tm = explode(',', $time);
		$shift = date('Z');
		$format = ($tm[0] >= 3600)?'h:i:s':'i:s';  			    
		return  empty($tm[1])?date($format, $tm[0] - $shift):date($format, $tm[0] - $shift) .'.'. $tm[1];
	}
	
	
	function fromFormatedTimeIntoSec( $time )
	{
		$tm = explode(':', $time);
		
		if ( count($tm) == 1 ) {
			return intval($tm[0]);
		} else if ( count($tm) == 2 ) {
			return intval($tm[1]) + intval($tm[0])*60;
		} else if ( count($tm) == 3 ) {
			return intval($tm[2]) + intval($tm[1])*60 + intval($tm[0])*60*60;
		} else {
			return "0";
		}
	}
	
	
	function timeRemain ( $time ) {
		$currenttime = time(); // текущая дата и время в unix формате
	    $date_time_string = $row['time_k']; // это дата и время из базы данных
	    $dt_elements = explode(' ',$date_time_string);
	    $date_elements = explode('-',$dt_elements[0]);
	    $time_elements =  explode(':',$dt_elements[1]);
	    $newtime= mktime($time_elements[0], $time_elements[1],$time_elements[2], $date_elements[1],$date_elements[2], $date_elements[0]);
	    
	    $days=floor(($newtime-time())/86400);
	    $hours=floor(($newtime-time())/3600-($days*24));
	    $mins=floor(($newtime-time())/60-($days*1440)-($hours*60));
	    $secs=floor(($newtime-time())-($days*86400)-($hours*3600)-($mins*60));
	    

	    return array($days, $hours, $mins, $secs );
	}
	
	
	function getRusMonth($month, $padeg = 'nominative'){
		if ( $month < 1 ) $month = 12 + $month;
		if ( $month > 12 ) $month = $month - 12;
		if($month > 12 || $month < 1) return FALSE;
		
		switch($padeg) {
			case 'genitive' : $aMonth = array('января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');break;
			default : $aMonth = array('январь', 'февраль', 'март', 'апрель', 'май', 'июнь', 'июль', 'август', 'сентябрь', 'октябрь', 'ноябрь', 'декабрь');
		}
		
		return $aMonth[$month - 1];
	}
	
	
	
	function weekDaysDictXML($withWorkDays = false) {
		$xml  = "";
		
		$xml  = "<WeekDays>";
		if ($withWorkDays) {
			$xml .= "<Element id = '0'>"."рабочая неделя"."</Element>";
		}
		
		$xml .= "<Element id = '1'>"."понедельник"."</Element>";
		$xml .= "<Element id = '2'>"."вторник"."</Element>";
		$xml .= "<Element id = '3'>"."среда"."</Element>";
		$xml .= "<Element id = '4'>"."четверг"."</Element>";
		$xml .= "<Element id = '5'>"."пятница"."</Element>";
		$xml .= "<Element id = '6'>"."суббота"."</Element>";
		$xml .= "<Element id = '7'>"."воскресенье"."</Element>";
		
		$xml .= "</WeekDays>";
		return $xml;
	}
	
	
	
	
	/**
	 * 
	 * Возвращает массив пар (первое число месяца, последнее число месяца) меясцев между двумя датами
	 * @param $dateStart = "12.05.2013"
	 * @param $dateEnd	= "25.07.2014"
	 */
	function monthBetweenTwoDate ($dateStart, $dateEnd) {
		$monthArray = array();
		
		if ( !empty($dateStart) && !empty($dateEnd) ) {
			$dateStartArr = explode(".", $dateStart);
			$curTimeshtamp = strtotime("01.".$dateStartArr[1].".".$dateStartArr[2]);
			$lastTimeshtamp = strtotime($dateEnd);
			
			$current = date("d.m.Y",$curTimeshtamp );
			while ( $curTimeshtamp <= $lastTimeshtamp ) {
				$dateStartArr = explode(".", $current);
				$lastDay = date("t", strtotime($current));
			
				array_push($monthArray, array( "01.".$dateStartArr[1].".".$dateStartArr[2], $lastDay.".".$dateStartArr[1].".".$dateStartArr[2]) );
				$curTimeshtamp = strtotime($current."+1 month");
				$current = date("d.m.Y",$curTimeshtamp ); 
			}
		}
		
		
		return $monthArray;
	}
?>
