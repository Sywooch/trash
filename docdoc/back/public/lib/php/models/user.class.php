<?php

/**
* 
* Получает массив пользователй по входным параметрам
*/
function getUserList ($params=array()) {
		$data = array(); 
		$sqlAdd = "";	
		
		if (count($params) > 0) { 
			if	( isset($params['userName']) && !empty ($params['userName']) )  {
				$sqlAdd .= " AND t1.lower(user_lname) LIKE '%".strtolower($params['userName'])."%' " ;
			}  
			
			if	( isset($params['status']) && !empty ($params['status'])  )  {
				$sqlAdd .= " AND t1.status = '".$params['status']."' " ;
			}
			
			if	( isset($params['right']) && !empty ($params['right'])  )  {
				$sqlAdd .= " AND rd.code LIKE '".$params['right']."' " ;
			}
		}
		
		$sql = "SELECT 
					t1.user_id as id, 
					t1.user_email as email, 
					t1.user_lname as lastName,  
					t1.user_fname as firstName, 
					t1.status,
					t1.user_login as login, 
					t1.phone, 
					t1.skype
				FROM `user` t1 
				LEFT JOIN  right_4_user r4u ON (t1.user_id = r4u.user_id)
				LEFT JOIN  user_right_dict rd ON (rd.right_id = r4u.right_id)
				WHERE 1=1 ".$sqlAdd."
				GROUP BY t1.user_id
				ORDER BY t1.user_id";
		//echo $sql."<br>";	   
		
		$result = query($sql);
		if (num_rows($result) > 0) {
			while ($row = fetch_array($result)) {
				array_push($data, $row);
			}
		}
		return $data;
	}
?>
