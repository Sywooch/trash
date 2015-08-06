<?php
	require_once dirname(__FILE__)."/../../include/common.php";
	require_once dirname(__FILE__)."/../../lib/php/models/illness.class.php";
	require_once dirname(__FILE__)."/../../lib/php/commonFunctions.php";
	

	$report = "";

	$user = new user();
	$user -> checkRight4page(array('ADM', 'CNM', 'ACM'), 'noHead');
	$userId = $user -> idUser;
	
	$cityId = getCityId();

	
	$id			= (isset($_POST['id'])) ? checkField($_POST['id'], "i", 0) : '0';
	$title		= (isset($_POST['title'])) ? checkField($_POST['title'], "t", "") : '';
	$sectorId	= (isset($_POST['sectorId'])) ? checkField($_POST['sectorId'], "i", "") : '';
	
	$descr	 	= (isset($_POST['descr'])) ? checkField($_POST['descr'], "t", "") : '';
	$symptom	= (isset($_POST['symptom'])) ? checkField($_POST['symptom'], "t", "") : '';
	$treatment	= (isset($_POST['treatment'])) ? checkField($_POST['treatment'], "t", "") : '';
	
	$textArticle = (isset($_POST['textArticle'])) ? checkField($_POST['textArticle'], "h", "") : '';
	
	$alias		= (isset($_POST['alias'])) ? checkField($_POST['alias'], "t", "") : '';
	$fullName	= (isset($_POST['fullName'])) ? checkField($_POST['fullName'], "t", "") : '';
	$metaTitle	= (isset($_POST['metaTitle'])) ? checkField($_POST['metaTitle'], "t", "") : '';
	$metaKeyWd	= (isset($_POST['metaKeyWd'])) ? checkField($_POST['metaKeyWd'], "t", "") : '';
	$metaDescr	= (isset($_POST['metaDescr'])) ? checkField($_POST['metaDescr'], "t", "") : '';
	$status		= (isset($_POST['isDiasble'])) ? checkField($_POST['isDiasble'], "i", 0) : 0;
	
	
	 

	/*	Валидация	*/
	if ( empty($title) ) 
		setException("Не заполнено поле 'Название'");
	
	$textArticle = clearStyle($textArticle);
	
	if ( empty($alias) )
		setException("Не заполнено поле 'Alias'");
	
		
			
	/*	Сохранение	*/
	$result = query("START TRANSACTION");
	$sqlAdd = "";
	
	$params = array();
	$params['title'] 		= $title;
	$params['sectorId'] 	= $sectorId;
	$params['descr'] 		= $descr;
	$params['symptom'] 		= $symptom;
	$params['treatment'] 	= $treatment;
	
	$params['textArticle'] 	= $textArticle;
	$params['alias'] 		= $alias;
	$params['fullName'] 	= $fullName;
	$params['metaTitle'] 	= $metaTitle;
	$params['metaKeyWd'] 	= $metaKeyWd;
	$params['metaDescr']	= $metaDescr;
	$params['status'] 		= $status;

	
	if ( $id > 0 )  {
		$illness = new Illness($id);
		
		if ( !$illness -> modifyData ($params) ) {
			$result = query("rollback");
			setException("Ошибка сохранения данных");
		};
				
		$msg = "Сохранение заболевания \"$title\"  (id = $id)";
		$log = new logger();
		$log -> setLog($user->idUser, 'U_ILL', $msg);
		
	} else {
		/*		Новая запись	*/

		$illness = new Illness();
		
		if ( !($id = $illness -> create ($params)) ) {
			$result = query("rollback");
			setException("Ошибка создания статьи");
		};
				
		$msg = "Создание заболевания \"$title\"  (id = $id)";
		$log = new logger();
		$log -> setLog($user->idUser, 'C_ILL', $msg);
	}

	$result = query("commit");

	echo htmlspecialchars(json_encode(array('status'=>'success', 'id' => $id)), ENT_NOQUOTES);
