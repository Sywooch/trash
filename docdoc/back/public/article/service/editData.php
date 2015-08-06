<?php
	require_once dirname(__FILE__)."/../../include/common.php";
	require_once dirname(__FILE__)."/../../lib/php/models/article.class.php";
	require_once dirname(__FILE__)."/../../lib/php/commonFunctions.php";
	

	$report = "";

	$user = new user();
	$user -> checkRight4page(array('ADM', 'CNM', 'ACM'), 'noHead');
	$userId = $user -> idUser;
	
	$cityId = getCityId();

	
	$id			= (isset($_POST['id'])) ? checkField($_POST['id'], "i", 0) : '0';
	$title		= (isset($_POST['title'])) ? checkField($_POST['title'], "t", "") : '';
	$sectionId	= (isset($_POST['sectionId'])) ? checkField($_POST['sectionId'], "i", "") : '';
	
	$textDesc 	= (isset($_POST['textDesc'])) ? checkField($_POST['textDesc'], "t", "") : '';
	$textArticle = (isset($_POST['textArticle'])) ? checkField($_POST['textArticle'], "h", "") : '';
	
	$alias		= (isset($_POST['alias'])) ? checkField($_POST['alias'], "t", "") : '';
	$metaTitle	= (isset($_POST['metaTitle'])) ? checkField($_POST['metaTitle'], "t", "") : '';
	$metaKeyWd	= (isset($_POST['metaKeyWd'])) ? checkField($_POST['metaKeyWd'], "t", "") : '';
	$metaDescr	= (isset($_POST['metaDescr'])) ? checkField($_POST['metaDescr'], "t", "") : '';
	$status		= (isset($_POST['isDiasble'])) ? checkField($_POST['isDiasble'], "i", 0) : 0;
	
	
	 

	/*	Валидация	*/
	if ( empty($title) ) 
		setException("Не заполнено поле 'Название'");
	
	$textArticle = clearStyle($textArticle);
	if ( empty($textArticle) )
		setException("Не заполнено поле 'Текст'");

	if ( empty($alias) )
		setException("Не заполнено поле 'Alias'");
	
		
			
	/*	Сохранение	*/
	$result = query("START TRANSACTION");
	$sqlAdd = "";
	
	$params = array();
	$params['title'] 		= $title;
	$params['sectionId'] 	= $sectionId;
	$params['textDesc'] 	= $textDesc;
	$params['textArticle'] 	= $textArticle;
	$params['alias'] 		= $alias;
	$params['metaTitle'] 	= $metaTitle;
	$params['metaKeyWd'] 	= $metaKeyWd;
	$params['metaDescr']	= $metaDescr;
	$params['status'] 		= $status;
		
	if ( $id > 0 )  {
		$article = new Article($id);
		
		if ( !$article -> modifyData ($params) ) {
			$result = query("rollback");
			setException("Ошибка сохранения данных");
		};
				
		$msg = "Сохранение статьи \"$title\"  (id = $id)";
		$log = new logger();
		$log -> setLog($user->idUser, 'U_ART', $msg);
		
	} else {
		/*		Новая запись	*/

		$article = new Article();
		
		if ( !($id = $article -> create ($params)) ) {
			$result = query("rollback");
			setException("Ошибка создания статьи");
		};
				
		$msg = "Создание статьи \"$title\"  (id = $id)";
		$log = new logger();
		$log -> setLog($user->idUser, 'C_ART', $msg);
	}

	$result = query("commit");

	echo htmlspecialchars(json_encode(array('status'=>'success', 'id' => $id)), ENT_NOQUOTES);
