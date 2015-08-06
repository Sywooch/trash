<?php
/**
 * Сохранет сообщение в лог
 *
 * @param int    $requestId
 * @param string $message
 * @param int    $owner
 * @param int    $type
 * @param bool   $withredirect
 *
 * @return bool
 */
function saveLog($requestId, $message, $owner, $type = 3, $withredirect = true)
{
	$sql = "INSERT INTO `request_history` SET
		request_id = " . $requestId . ",
		created = now(),
		action = $type,
		user_id = " . $owner . ",
		text = :text";
	$result = query($sql, [
		':text' => $message,
	]);
	if (!$result) {
		setXMLerror("Ошибка добавления записи в лог", $withredirect);
	}

	return true;
}

/**
 * Сохранет сообщение в лог
 * И отдаёт JS
 *
 * @param int    $requestId
 * @param string $message
 * @param int    $owner
 * @param int    $type
 * @param bool   $withredirect
 */
function saveLogJS($requestId, $message, $owner, $type = 3, $withredirect = true)
{
	$sql = "INSERT INTO `request_history` SET
		request_id = " . $requestId . ",
		created = now(),
		action = $type,
		user_id = " . $owner . ",
		text = :text";
	queryJS($sql, 'Ошибка добавления записи в лог', [
		':text' => $message,
	]);
}

function setXMLerror($error, $withredirect = true)
{
	global $id;

	query("rollback");
	Yii::app()->session['error'] = $error;
	if ($withredirect) {
		header("Location: /request/request.htm?id=" . $id);
	}
	exit;
}

function setJsonError($errorText)
{
	query("rollback");
	echo htmlspecialchars(json_encode(array('error' => $errorText)), ENT_NOQUOTES);
	exit;
}