<?php
use dfs\docdoc\models\RequestModel;
require_once dirname(__FILE__)."/../include/common.php";
require_once dirname(__FILE__)."/../lib/php/validate.php";

$id = (isset($_POST['reqId'])) ? checkField($_POST['reqId'], "i", 0) : 0;
$action = (isset($_POST['reqAction'])) ? checkField($_POST['reqAction'], "t", '') : '';
$comment = (isset($_POST['reqDeclineComment'])) ? checkField($_POST['reqDeclineComment'], "h", '', false, 1000) : '';

if ( $id == 0 ) setException ("Не передан идентификатор");
if ( $action == '' ) setException ("Не передано действие");

$status = false;
switch ($action) {
    case 'approve': $status = 8; $statusName = 'Одобрена'; break;
    case 'decline': $status = 9; $statusName = 'Отклонена'; break;
}

if ( $status == 9 && $comment == '' ) setException ("Не передан комментарий");

$result = query("START TRANSACTION");

if($status){

	$request = RequestModel::model()->findByPk($id);
	if ($request !== null) {
		$request->saveStatus($status);
	}

    $sql = "INSERT INTO `request_history` SET
                    request_id = ".$id.",
                    created = now(),
                    action = 1,
                    user_id = 0,
                    text = 'Изменение статуса на \"$statusName\"'";
    queryJS ($sql, 'Ошибка записи истории заявки');

    if($status == 9){
        if(!empty($comment)){
            $sql = "INSERT INTO `request_history` SET
                            request_id = ".$id.",
                            created = now(),
                            action = 5,
                            user_id = 0,
                            text = '$comment'";
            queryJS ($sql, 'Ошибка записи истории заявки');
        }
    }
} else {
    setException ("Не передано действие");
}

$result = query("commit");

setSuccess();

?>
