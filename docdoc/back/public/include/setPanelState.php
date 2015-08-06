<?php
	require_once 	dirname(__FILE__)."/common.php";

	$state = (isset($_GET['state'])) ? checkField($_GET['state'], "t", "open") : 'open';
	
	switch ($state) {
		case 'open'	: Yii::app()->session['panel'] = 'open'; break;
		case 'close': Yii::app()->session['panel'] = 'close'; break;
	}
