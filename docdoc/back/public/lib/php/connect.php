<?php
require_once dirname(__FILE__) . "/errorLog.php";

/**
 * @deprecated
 *
 * @param CDbDataReader $result
 *
 * @return object|stdClass
 */
function fetch_object($result)
{
	return $result->readObject("stdClass", array());
}

/**
 * @deprecated
 *
 * @param CDbDataReader $result
 *
 * @return array
 */
function fetch_array($result)
{
	return $result->read();
}

/**
 * Метод расширяет mysql в стиле PDO::fetchAll
 *
 * @deprecated
 *
 * @param   CDbDataReader $result результат ответа от базы
 * @return  array         возвращает массив данных
 */
function fetch_all($result)
{
	$data = array();

	while ($row = fetch_array($result)) {
		array_push($data, $row);
	}
	return $data;
}

/**
 * @param string $sql
 * @param array $params input parameters (name=>value) for the SQL execution. This is an alternative
 * to {@link bindParam} and {@link bindValue}. If you have multiple input parameters, passing
 * them in this way can improve the performance. Note that if you pass parameters in this way,
 * you cannot bind parameters or values using {@link bindParam} or {@link bindValue}, and vice versa.
 * Please also note that all values are treated as strings in this case, if you need them to be handled as
 * their real data types, you have to use {@link bindParam} or {@link bindValue} instead.
 * @deprecated
 *
 * @return bool|CDbDataReader
 */
function query($sql, $params = array())
{
	$result = Yii::app()
		->getDb()
		->createCommand($sql)
		->query($params);

	return $result;
}

/**
 * @param string $sql
 * @param string $errorText
 * @param array $params input parameters (name=>value) for the SQL execution. This is an alternative
 * to {@link bindParam} and {@link bindValue}. If you have multiple input parameters, passing
 * them in this way can improve the performance. Note that if you pass parameters in this way,
 * you cannot bind parameters or values using {@link bindParam} or {@link bindValue}, and vice versa.
 * Please also note that all values are treated as strings in this case, if you need them to be handled as
 * their real data types, you have to use {@link bindParam} or {@link bindValue} instead.
 *
 * @deprecated
 *
 * @return bool|CDbDataReader
 */
function queryJS($sql, $errorText, $params = [])
{
	$result = query($sql, $params);
	if (!$result) {
		query("rollback");
		echo htmlspecialchars(json_encode(array('error' => $errorText)), ENT_NOQUOTES);
		exit;
	}

	return $result;
}

/**
 * @param CDbDataReader $result
 * @deprecated
 *
 * @return bool|int
 */
function num_rows($result)
{
	if ($result) {
		return $result->getRowCount();
	} else {
		return false;
	}
}

/**
 * @return int The ID generated for an AUTO_INCREMENT column by the previous
 * @deprecated
 */
function legacy_insert_id()
{
	return Yii::app()->getDb()->lastInsertID;
}

/**
 * @deprecated
 *
 * @param CDbDataReader $result
 *
 * @return string
 */
function legacy_result_first($result)
{
	return $result->readColumn(0);
}
