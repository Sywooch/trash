<?php
	require_once 	dirname(__FILE__)."/common.php";

	if (isset($_GET['city'])) {
		$city = Yii::app()->city;
		$city->changeCity(checkField($_GET['city'], "i", 1));
		Yii::app()->session['city'] = $city->getCityId();
	}
