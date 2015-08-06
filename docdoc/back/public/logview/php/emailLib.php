<?php
	require_once	dirname(__FILE__)."/../../lib/php/user.class.php";
	require_once	dirname(__FILE__)."/../../lib/php/dateconvertionLib.php";



	/*	Список логов	*/
	function getEmailListXML ($params=array()) {
		$xml = "";
		$sqlAdd = " ";
		$startPage = 1;
		$step = 100;

		$sql = "SELECT
					idMail as id, emailTo, subj, message, crDate, resendCount
				 FROM
				 	mailQuery
				ORDER BY CrDate DESC";

		//echo $sql;
		if ( isset($params['step']) && intval($params['step']) > 0 ) $step = $params['step'];
		if ( isset($params['startPage']) && intval($params['startPage']) > 0 ) $startPage = $params['startPage'];

		list($sql, $str) = pager( $sql, $startPage, $step); // функция берется из файла pager.xsl
		$xml .= $str;
		//echo $str."<br/>";

		$result = query($sql);
		if (num_rows($result) > 0) {
			$xml .= "<EmailList>";
			while ($row = fetch_object($result)) {
				$xml .= "<Element id=\"".$row -> id."\">";
				$xml .= "<CrDate>".$row -> crDate."</CrDate>";
				$xml .= "<EmailTo>".$row -> emailTo."</EmailTo>";
				$xml .= "<ResendCount>".$row -> resendCount."</ResendCount>";
				$xml .= "<Subj><![CDATA[".$row -> subj."]]></Subj>";
				$xml .= "<Message><![CDATA[".$row -> message."]]></Message>";
				$xml .= "</Element>";
			}
			$xml .= "</EmailList>";
		}
		return $xml;
	}




