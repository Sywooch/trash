<?php
use dfs\docdoc\models\RequestModel;
use dfs\docdoc\objects\Rejection;

	require_once dirname(__FILE__)."/../../include/common.php";
	require_once dirname(__FILE__)."/../../lib/php/request.class.php";
	require_once dirname(__FILE__)."/../php/requestAcionLib.php";
	require_once dirname(__FILE__) . "/../../lib/php/RequestInterface.php";


	$report = "";

	$user = new user();
	$user -> checkRight4page(array('ADM','SOP'),'simple');
	$userId = $user -> idUser;
	

	$status	= (isset($_POST['status']) && $_POST['status'] !== '') ? checkField($_POST['status'], "i", 0) : null;
	$ch	= (isset($_POST['ch'])) ? $_POST['ch'] : array();
	$rejectReasonId = (isset($_POST['rejectReasonId'])) ? (int)$_POST['rejectReasonId'] : null;

	/*	Валидация	*/
	if ( $status === null ) {
		echo htmlspecialchars(json_encode(array('error'=>'Не передан статус')), ENT_NOQUOTES);
		exit;
	}
	if ( count($ch) ==  0 ) {
		echo htmlspecialchars(json_encode(array('error'=>'Не переданы заявки')), ENT_NOQUOTES);
		exit;
	}

	if($rejectReasonId && !in_array($rejectReasonId, Rejection::getAllReasons())){
		echo htmlspecialchars(json_encode(array('error'=>'Причины отказа не существует')), ENT_NOQUOTES);
		exit;
	}

	$result = query("START TRANSACTION");

	$typeView = isset($_POST['typeView']) ? $_POST['typeView'] : null;

	switch ($typeView) {
		case RequestInterface::VIEW_PARTNERS:
			foreach ($ch as $key) {
				$request = RequestModel::model()->findByPk($key);
				if ($request !== null) {
					$request->partner_status = $status;
					$request->save();
				}

				saveLogJS($key, "Групповое изменение партнёрского статуса на " . $status, $userId, 3);
			}
			break;

		default:
			foreach ($ch as $key) {
				if ($request = RequestModel::model()->findByPk($key)) {
					$request->req_status = $status;
					$savedFields = ['req_status'];

					if($rejectReasonId){
						$request->reject_reason = $rejectReasonId;
						$savedFields[] = 'reject_reason';
					}

					$request->save(true, $savedFields);
				}

				saveLogJS($key, "Групповое изменение статуса на " . $status, $userId, 3);
			}
			break;
	}

	$result = query("commit");


	echo htmlspecialchars(json_encode(array('status'=>'success')), ENT_NOQUOTES);
