<?php
	function checkField ( $field, $method = "i", $defValue = "", $withConvert = false, $enumList = array() ) {
		$out = '';
		
		switch ($method) {
			case 'i' : $out = ( $field != '' ) ? intval(  $field ) : $defValue; break;
			case 't' : $out = ( $field != '' ) ? trim(  $field ) : $defValue; break;
			case 'st' : $out = ( $field != '' ) ? strong(  $field ) : $defValue; break;
			case 'ms' : $out = ( $field != '' ) ? middleStrong(  $field ) : $defValue; break;
			case 'txt' : $out = ( $field != '' ) ? textOnly(  $field ) : $defValue; break;
			case 'f' : $out = ( !empty( $field) ) ? floatval(  $field ) : $defValue; break;
			case 'h' : $out = ( !empty( $field) ) ? trim(  $field ) : $defValue; break;
			case 'e' : $out = ( !empty( $field) ) ? enum( $field, $enumList ) : $defValue; break;
			case 'dt' : $out = ( !empty( $field) ) ? dateCheck(trim($field)) : $defValue; break;
			case 'time' : $out = ( !empty( $field) ) ? timeCheck(trim($field)) : $defValue; break;
			case 'phone' : $out = ( !empty( $field) ) ? checkPhoneNumber(trim($field), 'msk_spb_mobile') : $defValue; break;
			case 'dig' : $out = ( !empty( $field) ) ? checkNumber(trim($field)) : $defValue; break;
		}
	
		if ( $withConvert ) { 
			$out = iconv( "UTF-8", "WINDOWS-1251", $out ); 
		}
		
		if ( $method != 'h' ) {
			//$out = htmlspecialchars( strip_tags( trim( $out ) ), ENT_NOQUOTES, 'utf-8' );
			$out = str_replace("\\'","'", htmlspecialchars( strip_tags( trim( $out ) ), ENT_NOQUOTES, 'utf-8' ));
			$out = str_replace("'","\'", $out);
		} else {
			//$out = htmlspecialchars( strip_tags( trim( chop( $out )), "<p><a><br><br/><h1><h2><h3><b><font><img><ul><li><ol>" ), ENT_QUOTES , 'cp1251' );
			$out = (strip_tags( trim( $out ), "<p><a><br><br/><h1><h2><h3><div><b><img><ul><li><ol><strong><em><table><tr><td><tbody><th><hr><u><i>" ));
		}
			 
		return $out;
	}

/**
 * Проверка массива на целочисленные значения
 * @param $arr
 * @return array
 */
function checkArrayToInt($arr)
{
	if (is_array($arr)) {
		$arr = array_map(function ($v) {
			return (int)$v;
		}, $arr);
	} else {
		$arr = array();
	}

	return $arr;
}
	
	function enum ($field, $enumList = array() ) {
		 if ( !empty( $field) && count($enumList) > 0 ) {
		 	foreach ($enumList as $key) 
		 		if ( $key == $field ) return $key;	
		 	
		 }
		
		return;
	}
	
	
	function strong($field) {
		$content = mb_eregi_replace('[^(0-9a-z а-я A-ZА-Я)]', '', $field);

		return $content;
	}
	
	
	function middleStrong($field) {
		$content = mb_eregi_replace('[^(0-9a-z а-я A-ZА-Я_@/.,-/!/?)]', '', $field);

		return $content;
	}
	
	function textOnly($field) {
		$content = mb_eregi_replace('[^(0-9a-z а-я A-ZА-Я_@/.,/$#/*/!/?)]', '', $field);

		return $content;
	}
	
	
	
	
	function checkNumber($field) {
		$content = preg_replace("/[\D]/",'',$field);
		
		return $content;
	}
	
	
	
	function checkPhoneNumber($phone, $operatorCheck = 'all') {
		global $DEFCode; 
		
		$phone = modifyPhone($phone);
		
		// Проверка на оператора
		switch ($operatorCheck) {
			case 'all' : break; 
			case 'mobile' : {
								$ext = substr($phone, 1, 3);
								if ( !in_array($ext, $DEFCode['mobile'])) $phone = ""; 
							} break;
			case 'msk_spb_mobile' : {
								$ext = substr($phone, 1, 3);
								if ( !in_array($ext, $DEFCode['mobile']) && !in_array($ext, $DEFCode['msk']) && !in_array($ext, $DEFCode['spb']) ) $phone = ""; 
							} break;
			default: break;
		}
		
		return $phone;
		
	}

    function formatTextField($txt) {
        $txt = trim($txt);
        if(!empty($txt))
			/** Для нового дизайна */
            //$txt = '<p>'.str_replace(array("\r\n","\n\r","\n","\r"), "</p><p>", $txt).'</p>';

			$txt = str_replace(array("\r\n","\n\r","\n","\r"), "<br />", $txt);

        return $txt;
    }
	
	
	
	
	function clearStyle ( $content ) {
		
		$content = preg_replace('/(<(?!(a|img|table|tr|td|hr|div|u|p|ul|li|i|em|h1|h2|h3|h4))[^\s>]+)(\s[^>]*)?/ism','$1', $content);

		return $content;
	}
	
	
	function modifyPhone ( $phone ) {
		$element = "";
		
		$element = preg_replace("/[\D]/",'',$phone);
		if ( !empty($element) )  {
			if ( substr($element, 0,1) == '8' && strlen($element) > 8) {
				$element= '7'.substr($element, 1, strlen($element));
			} else if (strlen($element) == 7 ) {
				$element= '7495'.$element;
			}
		}
		
		return $element;
	}

/**
 * Формат номера телефона
 *
 * @param $number
 * @param string $code
 *
 * @return string
 */
function formatPhone($number)
{
	$newPhone = "";

	$element = preg_replace("/[\D]/", '', $number);
	if (substr($element, 0, 1) == '8' && strlen($element) > 8) {
		$element = '7' . substr($element, 1, strlen($element));
	} else if (strlen($element) == 7) {
		$element = '7495' . $element;
	} else if (strlen($element) == 10) {
		$element = '7' . $element;
	}

	if (!empty($element)) {
		$newPhone = "+7 (" . substr($element, 1, 3) . ") " . substr($element, 4, 3) . "-" . substr($element, 7, 2) . "-" . substr($element, 9, 2);
	}

	return $newPhone;
}
	
	function formatPhone4DB ( $number ) {
		$newPhone = "";
		
		$element = preg_replace("/[\D]/",'',$number);

		if ( substr($element, 0,1) == '8' && strlen($element) > 8) {
			$element= '7'.substr($element, 1, strlen($element));
		} else if (strlen($element) == 7 ) {
			$element= '7495'.$element;
		} else if (strlen($element) == 10 ) {
			$element= '7'.$element;
		}  
		
		$newPhone = $element;
		
		return $newPhone;	
	}
        
	function checkPhone ($number) {
    	$number = preg_replace("/[\D]/",'',$number);
            
		if(strlen($number) == 11)
			return true;
        else
			return false;
	}
	
        
	/*
	function checkEmail($email){
		return filter_var($email, FILTER_VALIDATE_EMAIL)!=false;
	}
	*/
	
	
	function checkEmail ( $email ) {
		$email = trim($email);
		$Syntax='/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,3})$/';
		if ( empty($email) || !preg_match($Syntax, $email) ) {
			return FALSE;
		} else {
			return TRUE; 			
		}
	}
	
	
	function dateCheck ( $dateIn) {
		$dateArray = explode(".", $dateIn);
		if (count($dateArray) == 3 ) 
			if ( checkdate($dateArray[1], $dateArray[0], $dateArray[2]) )
				return $dateIn;
		else 
			return false;
	}
	
	
	function timeCheck ( $timeIn ) {
		$timeOut = "";
		
		if (preg_match("/\d{2}:\\d{2}/",$timeIn, $matches) ) {
			$timeOut = $matches[0];
		} else if (preg_match("/(\d{1}):(\\d{2})/",$timeIn, $matches) ) {
			$timeOut = "0".$matches[0].":".$matches[1];
		} else if (preg_match("/(\d{2})/",$timeIn, $matches) ) {
			$timeOut = $matches[0].":00";
		} else if (preg_match("/\d{1}/",$timeIn, $matches) ) {
			$timeOut = "0".$matches[0].":00";
		}
		
		$timeArr = explode(":", $timeOut);
		if ( intval($timeArr[0]) > 24 || intval($timeArr[1]) > 59 ) 
			$timeOut = "";
		
		return $timeOut;
	}
	
	
	function checkYear ( $year ) {
		
		$year = intval($year);
		
		if ( empty($year) || $year < 1900 || $year > 2100 ) {
			return false;
		} else {
			return true; 			
		}
	}
	
	
	// Замена пустой строки на null при вставке данных в БД
	function emptyToNull ( $data, $sqlField, $emptyValue = 'NULL') {
		$str = "";
		
		if ( !empty($data) ) {
			$str = " ".$sqlField." = '".$data."', ";
		} else {
			$str = " ".$sqlField." = ".$emptyValue.", ";
		}
		return $str;
	}

?>
