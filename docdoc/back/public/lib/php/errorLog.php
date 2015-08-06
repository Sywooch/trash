<?php

use dfs\common\filesystem\FileHandler;

require_once dirname(__FILE__) . "/user.class.php";
require_once dirname(__FILE__) . "/mail.php";
require_once dirname(__FILE__) . "/emailQuery.class.php";

class backLog
{
	public $filePath;
	public $source;

	public function __construct()
	{
		$this->filePath = dirname(__FILE__) . '/../../log/back.log';
	}

	public function setSource($name)
	{
		$name = str_replace(BASEDIR, "", $name);
		$this->source = $name;
	}

	public function setSourceName($name)
	{
		$this->source = $name;
	}

	public function log($mess, $type = 'ALL')
	{

		$source = $this->source;

		// Логирование отключено
		if ($type == "OFF") {
			return false;
		}

		if ((trim($mess)) == '' && !file_exists($this->filePath)) {
			return false;
		}

		if (
			LOG_LEVEL == 'ALL'
			||
			(LOG_LEVEL == $type && $type == 'CRITICAL')
			||
			(LOG_LEVEL == 'WARNING' && ($type == 'CRITICAL' || $type == 'WARNING'))
		) {

			$text = date("d.m.Y H:i:s ") . "\t[" . getmypid() . "]\t" . $type . "\t" . $mess . "\t" . $source . "\r\n";

			FileHandler::write($this->filePath, $text);
		}

		return true;
	}
}

/**
 *
 * Класс для записи лога
 * $fileName = имя лога; $mess - текст строки
 * $log = new commonLog('asterisk.log', 'Системное сообщение');
 *
 */
class commonLog
{

	public function __construct($fileName, $mess)
	{
		$filePath = BASEDIR . 'log/';

		if (trim($mess) == '' && !file_exists($filePath . $fileName)) {
			return false;
		}

		$mess = str_replace("\r\n", '; ', $mess);
		$mess = str_replace("\n", '', $mess);

		$text = date("d.m.Y H:i:s ") . "[" . getmypid() . "]\t" . $mess . "\r\n";

		FileHandler::write($filePath . $fileName, $text);

		return true;
	}
}

/* Запись сообщений в лог  ('date', 'message', 'code_error', $_SERVER['REQUEST_URI'], $userId ) */
//$idUser = $user -> idUser;
//$log = new msgLog('проверка ', 35, $idUser);
class msgLog
{
	const LOG_NAME = 'messages.log';

	public function __construct($mess, $code = 0, $userId = 0)
	{
		if ((trim($mess)) == '') {
			return false;
		}
		$file_path = BASEDIR . '/log/' . self::LOG_NAME;
		$mess      = str_replace("\r\n", '', $mess);
		$mess      = str_replace("\n", '', $mess);
		$mess      = str_replace("\t", ' ', $mess);
		$ip        = getIp();
		$source    = 'SRC:';
		if (isset($_SERVER) && array_key_exists('REQUEST_URI', $_SERVER)) {
			$source .= $_SERVER['REQUEST_URI'];
		} elseif (isset($argv) && array_key_exists(0, $argv)) {
			$source .= 'console(cli)=' . $argv['0'];
		} else {
			$source .= 'unknown';
		}
		$text =
			date("d.m.Y H:i:s ") .
			" '" .
			$mess .
			"' " .
			$code .
			" " .
			$source .
			" UserId:" .
			$userId .
			" Ip:" .
			$ip .
			" \r\n";

		FileHandler::write($file_path, $text);

		return true;
	}
}

class errLog
{
	const LOG_NAME = 'err.log';

	public function __construct($params = '')
	{
		$file_path = BASEDIR . '/log/' . self::LOG_NAME;
		ob_start();
		$req = ob_get_contents();
		ob_end_clean();
		$source = 'SRC:';
		if (isset($_SERVER) && array_key_exists('REQUEST_URI', $_SERVER)) {
			$source .= $_SERVER['REQUEST_URI'];
		} elseif (isset($argv) && array_key_exists(0, $argv)) {
			$source .= 'console(cli)=' . $argv['0'];
		} else {
			$source .= 'unknown';
		}
		$text = date("d.m.Y H:i:s ") . " '" . $req . "' " . $source . "\r\n";

		FileHandler::write($file_path, $text);

		return true;
	}
}

/**
 * Логгирование пользователя
 *
 */
class logger
{
	/**
	 * Запись сообщения в БД*
	 *
	 * @param int    $id   Id пользователя
	 * @param int    $code Идентификатор действие
	 * @param string $msg  Сообщение
	 */
	public function setLog($id, $code, $msg)
	{
		$sql = "
			INSERT INTO
				log_back_user
			SET
				user_id = " . intval($id) . ",
				log_code_id = :code,
				message = :msg,
				crDate = NOW()
		";
		try {
			$result = query($sql, [':msg' => trim($msg), ':code' => trim($code),]);
			if (!$result) {
				throw new Exception("Ошибка добавления записи  в БД " . date("d.m.Y H:i:s "));
			}
		} catch (Exception $e) {
			$errorMsg[]           = $e->getMessage();
			Yii::app()->session["errorMsg"] = $errorMsg;
		}
	}
}

/*	Вывод ошибок если есть в srvInfo	*/
function getErr2XML()
{
	$xml = '';

	$errorMsg = Yii::app()->session['errorMsg'];
	if ($errorMsg && is_array($errorMsg)) {
		$xml .= '<Errors>';
		foreach ($errorMsg as $mess) {
			$xml .= '<ErrorMess>' . iconv("UTF-8", "WINDOWS-1251", $mess) . '</ErrorMess>';
		}
		$xml .= '</Errors>';
	}

	return $xml;
}

function getIp()
{
	$ip = false;
	if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ipa[] = trim(strtok($_SERVER['HTTP_X_FORWARDED_FOR'], ','));
	}

	if (isset($_SERVER['HTTP_CLIENT_IP'])) {
		$ipa[] = $_SERVER['HTTP_CLIENT_IP'];
	}

	if (isset($_SERVER['REMOTE_ADDR'])) {
		$ipa[] = $_SERVER['REMOTE_ADDR'];
	}

	if (isset($_SERVER['HTTP_X_REAL_IP'])) {
		$ipa[] = $_SERVER['HTTP_X_REAL_IP'];
	}

	if (isset($ipa)) {
		foreach ($ipa as $ips) {
			if (isValidIp($ips)) {
				$ip = $ips;
				break;
			}
		}
	}

	return $ip;
}

function isValidIp($ip = null)
{
	if (preg_match("#^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})$#", $ip)) {
		return true;
	}

	return false;
}
