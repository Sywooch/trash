<?php
	require_once	dirname(__FILE__)."/../../lib/php/user.class.php";
	require_once	dirname(__FILE__)."/../../lib/php/dateconvertionLib.php";



	/*	Список логов	*/
	function getLogListXML ($params=array()) {
		$xml = "";
		$sqlAdd = " ";
		$startPage = 1;
		$step = 100;

		$owner = new user();

		if (count($params) > 0) {

			//  Дата создания добавил
			if	( isset($params['crDateFrom']) && !empty ($params['crDateFrom'])  )  {
				$sqlAdd .= " AND date(t1.crDate) >= date('".convertDate2DBformat($params['crDateFrom'])."') " ;
			}
			if	( isset($params['crDateTill']) && !empty ($params['crDateTill'])  )  {
				$sqlAdd .= " AND date(t1.crDate) <= date('".convertDate2DBformat($params['crDateTill'])."') " ;
			}
			
			if	( isset($params['login']) && !empty ($params['login']) )  {
				$sqlAdd .= " AND t2.user_login = '".$params['login']."'";
			}


			if	( isset($params['idLogCode']) && !empty ($params['idLogCode'])  )  {
				$sqlAdd .= " AND t1.log_code_id = '".$params['idLogCode']."'";
			}
		}


		//Права доступа к показу статей
		$sqlRightRest = "";
		if ( $owner -> checkRight4userByCode(array('ADM')) ) {
			$sqlRightRest = " 1 = 1 ";
		} /*else if ( $owner -> checkRight4userByCode(array('WRT')) ) {
			$sqlRightRest = " t1.ownerId = ".$owner -> idUser;
		} */ else {
			$sqlRightRest = " 1 = 0 ";
		}

		$sql = "SELECT
					t1.log_id as logId,  t1.user_id as userId, t1.message,  t2.user_login, concat(t2.user_lname,' ',t2.user_fname) as ownerName, t1.log_code_id,
					DATE_FORMAT( t1.crDate,'%d.%m.%Y  %H.%i') AS crDate
				FROM log_back_user  t1
				LEFT JOIN `user` t2 ON (t2.user_id = t1.user_id)
				WHERE ".$sqlRightRest.$sqlAdd. "
				GROUP BY t1.log_id
				ORDER BY  t1.log_id DESC";


		//echo $sql;
		if ( isset($params['step']) && intval($params['step']) > 0 ) $step = $params['step'];
		if ( isset($params['startPage']) && intval($params['startPage']) > 0 ) $startPage = $params['startPage'];

		list($sql, $str) = pager( $sql, $startPage, $step, "loglist"); // функция берется из файла pager.xsl с тремя параметрами. параметр article тут не нужен
		$xml .= $str;
		//echo $str."<br/>";

		$result = query($sql);
		if (num_rows($result) > 0) {
			$xml .= "<LogList>";
			while ($row = fetch_object($result)) {
				$xml .= "<Element id=\"".$row -> logId."\">";
				$xml .= "<CrDate>".$row -> crDate."</CrDate>";
				$xml .= "<Message><![CDATA[".$row -> message."]]></Message>";
				$xml .= "<LogDict id=\"".$row ->log_code_id."\"/>";
				if ($row -> userId == 0 ) {
					$xml .= "<NickName id=\"0\">Система</NickName>";
				} else {
					$xml .= "<NickName id=\"".$row -> userId."\">".$row -> user_login." (".$row -> ownerName.")</NickName>";
				}

				$xml .= "</Element>";
			}
			$xml .= "</LogList>";
		}
		return $xml;
	}



	function getLogDictXML () {
		$xml = "";


		$sql = "SELECT
					t1.log_code_id, t1.title
				FROM log_dict t1
				ORDER BY  t1.log_code_id DESC";

		$result = query($sql);
		if (num_rows($result) > 0) {
			$xml .= "<LogDict>";
			while ($row = fetch_object($result)) {
				$xml .= "<Element id=\"".$row -> log_code_id."\">".$row -> title."</Element>";
			}
			$xml .= "</LogDict>";
		}
		return $xml;
	}


