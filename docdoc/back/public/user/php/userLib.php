<?php
	include_once	dirname(__FILE__)."/../../lib/php/user.class.php";
	
	
	function getUserListXML ($params=array()) {
		$xml = ""; 
		$sqlAdd = "";	
		
		if (count($params) > 0) { 
			if	( isset($params['userName']) && !empty ($params['userName']) )  {
				$sqlAdd .= " AND lower(user_lname) like '%".strtolower($params['userName'])."%' " ;
			}  
			
			if	( isset($params['status']) && !empty ($params['status'])  )  {
				$sqlAdd .= " AND status = '".$params['status']."' " ;
			}
			
			if	( isset($params['right']) && !empty ($params['right'])  )  {
				$sqlAdd .= " AND t2.code LIKE '".$params['right']."' " ;
			}
		}
		
		$sql = "SELECT 
					user_id, user_email as email, user_lname as lastName,  user_fname as firstName, status,
					user_login as login, phone, skype
				FROM `user` 
				WHERE 1=1 ".$sqlAdd."
				ORDER BY user_id";
		//echo $sql."<br>";	   
		
		$result = query($sql);
		if (num_rows($result) > 0) {
			$xml .= "<UserList>";
			while ($row = fetch_object($result)) {
				$xml .= "<Element id=\"".$row -> user_id."\">"; 
				$xml .= "<LastName>".$row -> lastName."</LastName>";
				$xml .= "<FirstName>".$row -> firstName."</FirstName>";
				$xml .= "<Status>".$row -> status."</Status>";   
				$xml .= "<Email>".$row -> email."</Email>";
				$xml .= "<Phone>".$row -> phone."</Phone>";
				$xml .= "<Skype>".$row -> skype."</Skype>";
				$xml .= "<Login>".$row -> login."</Login>";
				$sql = "	SELECT 
								t1.right_id as rightId, t2.code 
							FROM right_4_user t1
							LEFT JOIN  user_right_dict t2 ON (t1.right_id=t2.right_id)
							WHERE t1.user_id=".$row -> user_id;	 
				$resultAdd = query ($sql);
				if (num_rows($resultAdd) > 0) {
					$xml .= "<Rights>";
					while ($rowAdd = fetch_object($resultAdd)) {
						$xml .= "<Right id=\"".$rowAdd->rightId."\">".strtoupper($rowAdd->code)."</Right>";
					}
					$xml .= "</Rights>";
				}
			//$currUser = new user();
				//$currUser ->  getUserById($row -> userId);
				//$xml .= $currUser -> getUserXML();
				//$xml .= getRight4UserIdXML($row -> userId);
				$xml .= "</Element>";
			}
			$xml .= "</UserList>";
		}
		return $xml;
	}
	
	
	
	
	function getRightList () {
		$xml = ""; 
					
		$sql = "SELECT 
					right_id, title, code
				FROM user_right_dict
				ORDER BY right_id";
		//echo $sql."<br>";	   
		
		$result = query($sql);
		if (num_rows($result) > 0) { 
			$xml .= "<RightList>";
			while ($row = fetch_object($result)) {
				$xml .= "<Element id=\"".$row -> right_id."\" code=\"".$row -> code."\">".$row -> title."</Element>";
			}	
			$xml .= "</RightList>";
		}
		return $xml;
	}
	
	
	
	
	
	function getErrForm($formName = 'formData') {
		$xml = '';
		if ( isset(Yii::app()->session[$formName]) ) {
			$xml .= '<'.$formName.'>'; 
			foreach(Yii::app()->session[$formName] as $name => $value) {
				//$xml .= '<name'.$name.'><![CDATA['.$value.']]></name'.$name.'>'; 
				$xml .= '<'.$name.'><![CDATA['.$value.']]></'.$name.'>'; 
			}						  
			$xml .= '</'.$formName.'>'; 
			//echo $xml; 
		}
		return $xml;
	} 
