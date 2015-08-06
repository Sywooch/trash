<?php
	require_once 	dirname(__FILE__)."/common.php";


	$params = array();
	
	if ( count($_REQUEST) > 0 ) {
		foreach ($_REQUEST as $key => $value) {
			$params[$key] = $value;
		}
	}
	
	$name = ( isset($params['nameSession']) ) ? $params['nameSession'] : 'params';
	Yii::app()->session[$name] = $params;
