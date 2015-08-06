<?php	   	  
	require_once dirname(__FILE__)."/../../lib/php/user.class.php";
	require_once dirname(__FILE__)."/../../include/common.php";
	require_once dirname(__FILE__)."/../../lib/php/validate.php";


	$user = new user();	 
	$user -> checkRight4page(array('ADM','CNM', 'ACM'),'simple');
	
	$title		= (isset($_POST['title'])) ? checkField($_POST['title'], "t", '') : '';
	$type		= (isset($_POST['type'])) ? checkField($_POST['type'], "t", '') : 'none';
	$year		= (isset($_POST['year'])) ? checkField($_POST['year'], "t", '') : '';
	
	
	/*	Валидация	*/
	if ( empty($title) ) {
		echo htmlspecialchars(json_encode(array('error'=>'Необходимо ввести название учебного заведения')), ENT_NOQUOTES);
		exit;
	}

	$sql = "SELECT education_id, type, title FROM education_dict WHERE lower(title) LIKE '".strtolower($title)."'";
	$result = query($sql);
	if (num_rows($result) == 1) {
		$row = fetch_object($result);
		$id = $row -> education_id;
		$db_type =  $row -> type;
		$db_title =  $row -> title;
	} else {
		$result = query("START TRANSACTION");
		$sql = "INSERT INTO education_dict SET
					title='".$title."',
					type = '".$type."'";
		queryJS ($sql, 'Ошибка добавления данных в справочник');
		$id = legacy_insert_id();
		$db_type = $type;
		$db_title =  $title;
		$result = query("commit");
		
		//$user = new user();
		$msg = "Добавление записи в справочник Education id = $id";
		$log = new logger();
		$log -> setLog( $user -> idUser, 'C_EDU', $msg);
	}
	echo htmlspecialchars(json_encode(array('status'=>'success', 'id' => $id, 'type' => $db_type, 'title' => $db_title)), ENT_NOQUOTES);
