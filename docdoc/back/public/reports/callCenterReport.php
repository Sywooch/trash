<?php
	require_once	dirname(__FILE__)."/../include/header.php";
	require_once	dirname(__FILE__)."/../lib/php/serviceFunctions.php";
	require_once	dirname(__FILE__)."/../lib/php/models/user.class.php";

	$user = new user();
	$user -> checkRight4page(array('ADM','SAL','SOP'));

	pageHeader(dirname(__FILE__)."/xsl/callCenterReport.xsl");

	$crDateFrom	= ( isset($_GET["crDateShFrom"]) ) ? checkField ($_GET["crDateShFrom"], "t", "01.".date("m.Y") ) : "01.".date("m.Y"); 
	$crDateTill	= ( isset($_GET["crDateShTill"]) ) ? checkField ($_GET["crDateShTill"], "t", date("d.m.Y")) : date("d.m.Y");
	$operator	= ( isset($_GET["operator"]) ) ? checkField ($_GET["operator"], "i", "") : "";

	
	
	
	$xmlString = '<srvInfo>';
	$xmlString .= $user -> getUserXML();
	$xmlString .= '<CrDateShFrom>'.$crDateFrom.'</CrDateShFrom>';
	$xmlString .= '<CrDateShTill>'.$crDateTill.'</CrDateShTill>';
	$xmlString .= getCityXML();
	$xmlString .= '</srvInfo>';
	
	$city 	= getCityId();
	setXML($xmlString);
	
	
	
	$xmlString = '<dbInfo>';
	
	$paramsUser = array();
	$paramsUser ['status'] = 'enable';
	$paramsUser ['right'] = 'OPR';
	$ownerList = getUserList($paramsUser);
	if (count($ownerList) >  0 ) {
		
		$xmlString .= '<OwnerData>';		
		foreach ($ownerList as $owner) {
			$timearray = array("Extra" =>array(),"Fast" =>array(),"Middle" =>array(),"Slow" =>array(),"FuckUp" =>array());
			$xmlString .= '<Report>';
			$xmlString .= '<Owner id="'.$owner['id'].'">'.$owner['lastName'].'</Owner>';
			$sql = "SELECT count(t2.req_id) as cnt
						FROM request t2
						WHERE 
							(t2.req_type = 1 OR t2.req_type = 0) 
							AND 
							t2.req_user_id = ".$owner['id']."
							AND
							t2.req_created between (UNIX_TIMESTAMP('".convertDate2DBformat($crDateFrom)."')) AND (UNIX_TIMESTAMP('".convertDate2DBformat($crDateTill)."')+3600*24)
							AND
							t2.req_status <> 4";
				$result = query($sql);
				$row = fetch_object($result);
				$xmlString .= '<Total>'.$row->cnt.'</Total>';
					
			
			
				$sql = "SELECT 
							req_created as stTime,  
							DATE_FORMAT( req_created,'%H') AS crTime,
							req_id
						FROM request
						WHERE 
							(req_type = 1 OR req_type = 0) 
							AND 
							req_user_id = ".$owner['id']."
							AND
							req_created between (UNIX_TIMESTAMP('".convertDate2DBformat($crDateFrom)."')) AND (UNIX_TIMESTAMP('".convertDate2DBformat($crDateTill)."')+3600*24)
							AND
							req_status <> 4
							AND
							DATE_FORMAT( FROM_UNIXTIME(req_created),'%H') BETWEEN 9 AND 21";
				//echo $sql."<br>";
				
				/*
				$sql = "SELECT 
							UNIX_TIMESTAMP(t1.created) as stTime,
							t1.created as two,  
							DATE_FORMAT( t1.created,'%H') AS crTime,
							t2.req_id
						FROM request_history t1, request t2
						WHERE 
							t1.request_id = t2.req_id
							AND
							(t2.req_type = 1 OR t2.req_type = 0) 
							AND 
							t2.req_user_id = ".$owner['id']."
							AND
							t2.req_created between (UNIX_TIMESTAMP('".convertDate2DBformat($crDateFrom)."')) AND (UNIX_TIMESTAMP('".convertDate2DBformat($crDateTill)."')+3600*24)
							AND
							t1.action = 1
							AND
							t1.text LIKE 'Создание заявки с сайта%'
							AND
							t2.req_status <> 4
							AND
							DATE_FORMAT( t1.created,'%H') BETWEEN 9 AND 21";
				*/
				$result = query($sql);	
				while ($row = fetch_object($result)) {
					$sql2 = "SELECT 
								UNIX_TIMESTAMP(t1.created) as stTime,
								t1.created as two  
							FROM request_history t1
							WHERE 
								t1.request_id = ".$row->req_id."
								AND
								
								t1.text LIKE 'Звонок клиенту%'
							ORDER BY t1.created ASC
							LIMIT 1";
					$result2 = query($sql2);
					$row2 = fetch_object($result2);
		
					if ( isset($row2->stTime) ) {
						$delta = date("H:i:s", mktime(0, 0, ($row2->stTime - $row->stTime) ));  
					} else { $delta = -1;}
					
					if ( isset($row2->stTime) ) {
						$d = ($row2->stTime - $row->stTime);
						if ($d <= 60 ) {
							array_push($timearray["Extra"],$row->req_id);
						} else if ($d <= 300) {
							array_push($timearray["Fast"],$row->req_id);
						} else if ($d <= 900) {
							array_push($timearray["Middle"],$row->req_id);
						} else if ($d <= 3600) {
							array_push($timearray["Slow"],$row->req_id);
						} else {
							array_push($timearray["FuckUp"],$row->req_id);
						}
					}
					
					
					//echo $d." <a href='/request/request.htm?id=".$row->req_id."' target='_blanc'>".$row->req_id."</a> <span style='color:green'>".$delta."</span><br>";
					
				}
				
				$xmlString .= '<Reports>';	
				foreach ($timearray as $key => $data) {
					$xmlString .= "<".$key.">".count($data)."</".$key.">";
				}
				$xmlString .= '</Reports>';	
				$xmlString .= '</Report>';
			}
			$xmlString .= '</OwnerData>';	
	}

	
	
	
	
	
	
	
	$paramsUser = array();
	$paramsUser ['status'] = 'enable';
	$paramsUser ['right'] = 'OPR';
	$ownerList = getUserList($paramsUser);
	if (count($ownerList) >  0 ) {
		
		$xmlString .= '<OwnerDataTwo>';		
		foreach ($ownerList as $owner) {
			$timearray = array("Extra" =>array(),"Fast" =>array(),"Middle" =>array(),"Slow" =>array(),"FuckUp" =>array());
			$xmlString .= '<Report>';
			$xmlString .= '<Owner id="'.$owner['id'].'">'.$owner['lastName'].'</Owner>';
			$sql = "SELECT count(t2.req_id) as cnt
						FROM request t2
						WHERE 
							t2.req_type = 2 
							AND 
							t2.req_user_id = ".$owner['id']."
							AND
							t2.req_created between (UNIX_TIMESTAMP('".convertDate2DBformat($crDateFrom)."')) AND (UNIX_TIMESTAMP('".convertDate2DBformat($crDateTill)."')+3600*24)
							AND
							t2.req_status <> 4";
				$result = query($sql);
				$row = fetch_object($result);
				$xmlString .= '<Total>'.$row->cnt.'</Total>';
					
			
			
				$sql = "SELECT 
							req_created as stTime,  
							DATE_FORMAT( req_created,'%H') AS crTime,
							req_id
						FROM request
						WHERE 
							req_type = 2 
							AND 
							req_user_id = ".$owner['id']."
							AND
							req_created between (UNIX_TIMESTAMP('".convertDate2DBformat($crDateFrom)."')) AND (UNIX_TIMESTAMP('".convertDate2DBformat($crDateTill)."')+3600*24)
							AND
							req_status <> 4";
							
				$result = query($sql);	
				while ($row = fetch_object($result)) {
					$sql2 = "SELECT 
								UNIX_TIMESTAMP(t1.created) as stTime
							FROM request_history t1
							WHERE 
								t1.request_id = ".$row->req_id."
								AND
								LOWER(t1.text) LIKE 'перевед%'
							ORDER BY t1.created ASC
							LIMIT 1";
					$result2 = query($sql2);	
					$row2 = fetch_object($result2);
					
					if ( isset($row2->stTime) ) {
						$delta = date("H:i:s", mktime(0, 0, ($row2->stTime - $row->stTime) ));  
					} else { $delta = -1;}
					
					if ( isset($row2->stTime) ) {
						$d = ($row2->stTime - $row->stTime);
						if ($d <= 60 ) {
							array_push($timearray["Extra"],$row->req_id);
						} else if ($d <= 300) {
							array_push($timearray["Fast"],$row->req_id);
						} else if ($d <= 900) {
							array_push($timearray["Middle"],$row->req_id);
						} else if ($d <= 3600) {
							array_push($timearray["Slow"],$row->req_id);
						} else {
							array_push($timearray["FuckUp"],$row->req_id);
						}
					}
				}	
					
				
				
				$xmlString .= '<Reports>';	
				foreach ($timearray as $key => $data) {
					$xmlString .= "<".$key.">".count($data)."</".$key.">";
				}
				$xmlString .= '</Reports>';	
				$xmlString .= '</Report>';
			}
			$xmlString .= '</OwnerDataTwo>';	
	}
	
	
	
	
	$paramsUser = array();
	$paramsUser ['status'] = 'enable';
	$paramsUser ['right'] = 'OPR';
	$ownerList = getUserList($paramsUser);
	if (count($ownerList) >  0 ) {
		
		$xmlString .= '<OwnerDataThree>';		
		foreach ($ownerList as $owner) {
			$timearray = array("Extra" =>array(),"Fast" =>array(),"Middle" =>array(),"Slow" =>array(),"FuckUp" =>array());
			$xmlString .= '<Report>';
			$xmlString .= '<Owner id="'.$owner['id'].'">'.$owner['lastName'].'</Owner>';
			$sql = "SELECT count(t2.req_id) as cnt
						FROM request t2
						WHERE 
							(t2.req_type = 1 OR t2.req_type = 0) 
							AND 
							t2.req_user_id = ".$owner['id']."
							AND
							t2.req_created between (UNIX_TIMESTAMP('".convertDate2DBformat($crDateFrom)."')) AND (UNIX_TIMESTAMP('".convertDate2DBformat($crDateTill)."')+3600*24)
							AND
							t2.req_status <> 4";
				$result = query($sql);
				$row = fetch_object($result);
				$xmlString .= '<Total>'.$row->cnt.'</Total>';
					
			
			
		$sql = "SELECT 
							req_created as stTime,  
							DATE_FORMAT( req_created,'%H') AS crTime,
							req_id
						FROM request
						WHERE 
							(req_type = 1 OR req_type = 0) 
							AND 
							req_user_id = ".$owner['id']."
							AND
							req_created between (UNIX_TIMESTAMP('".convertDate2DBformat($crDateFrom)."')) AND (UNIX_TIMESTAMP('".convertDate2DBformat($crDateTill)."')+3600*24)
							AND
							req_status <> 4
							AND
							DATE_FORMAT( FROM_UNIXTIME(req_created),'%H') BETWEEN 9 AND 21";
				//echo $sql."<br>";
				
				
				$result = query($sql);	
				while ($row = fetch_object($result)) {
					$sql2 = "SELECT 
								UNIX_TIMESTAMP(t1.created) as stTime,
								t1.created as two  
							FROM request_history t1
							WHERE 
								t1.request_id = ".$row->req_id."
								AND
								LOWER(t1.text) LIKE 'перевед%'
							ORDER BY t1.created ASC
							LIMIT 1";
					$result2 = query($sql2);
					$row2 = fetch_object($result2);
		
					if ( isset($row2->stTime) ) {
						$delta = date("H:i:s", mktime(0, 0, ($row2->stTime - $row->stTime) ));  
					} else { $delta = -1;}
					
					if ( isset($row2->stTime) ) {
						$d = ($row2->stTime - $row->stTime);
						if ($d <= 60 ) {
							array_push($timearray["Extra"],$row->req_id);
						} else if ($d <= 300) {
							array_push($timearray["Fast"],$row->req_id);
						} else if ($d <= 900) {
							array_push($timearray["Middle"],$row->req_id);
						} else if ($d <= 3600) {
							array_push($timearray["Slow"],$row->req_id);
						} else {
							array_push($timearray["FuckUp"],$row->req_id);
						}
					}
				}
					
				
				
				$xmlString .= '<Reports>';	
				foreach ($timearray as $key => $data) {
					$xmlString .= "<".$key.">".count($data)."</".$key.">";
				}
				$xmlString .= '</Reports>';	
				$xmlString .= '</Report>';
			}
			$xmlString .= '</OwnerDataThree>';	
	}
	$xmlString .= '</dbInfo>';
	setXML($xmlString);

	pageFooter();
?>