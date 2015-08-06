<?php
	require_once dirname(__FILE__)."/../include/common.php";

	$cityId = (isset($_GET['cityid'])) ? checkField($_GET['cityid'], "i", 1) : 1;

	Yii::app()
		->city
			->changeCity($cityId)
			->redirect();

	// Оставлено для совместимости со старым механизмом сессий, для городов уже не актуально
	session_destroy();
?>