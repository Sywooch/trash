<?php
	use dfs\docdoc\models\ClinicModel;

	require_once dirname(__FILE__)."/../../lib/php/user.class.php";
	require_once dirname(__FILE__)."/../../include/common.php";
	require_once dirname(__FILE__)."/../../lib/php/validate.php";
	require_once dirname(__FILE__)."/../../lib/php/imgLib.php";

	$report = ""; 

	$user = new user();
	$user->checkRight4page(array('ADM', 'OPR', 'SOP', 'CNM', 'ACM'), 'simple');
	$userId = $user->idUser;
	
	$id = (isset($_POST['id'])) ? checkField($_POST['id'], "i", 0) : '0';
	$fileName = (isset($_POST['fileName'])) ? checkField($_POST['fileName'], "t", '') : '';

	$clinic = $id > 0 ? ClinicModel::model()->findByPk($id) : null;

	if ($clinic !== null && (!empty($fileName) || !empty($_POST['delete']))) {
		$clinic->logoPath = $fileName ? $fileName : null;

		if (!$clinic->save(true, [ 'logoPath' ])) {
			$report = array("status" => "error", "error" => "Ошибка при сохранении в БД");
			echo htmlspecialchars(json_encode($report), ENT_NOQUOTES);
			exit;
		}

		$report = array("status"=>"success");
	} else {
		$report = array("status"=>"error", "error"=> "Не передан идентификатор");
	}

	echo htmlspecialchars(json_encode($report), ENT_NOQUOTES);
