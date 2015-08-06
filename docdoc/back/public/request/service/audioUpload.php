<?php
require_once dirname(__FILE__) . "/../../lib/php/user.class.php";
require_once dirname(__FILE__) . "/../../include/common.php";
require_once dirname(__FILE__) . "/../../lib/php/validate.php";
require_once dirname(__FILE__) . "/../php/requestAcionLib.php";
require_once dirname(__FILE__) . "/../../lib/php/translit.php";

use dfs\docdoc\models\RequestRecordModel;
use dfs\docdoc\models\RequestModel;

$requestId = (isset($_REQUEST['id'])) ? checkField($_REQUEST['id'], "i", 0) : '0';

$user = new user();
$user->checkRight4page(array('ADM', 'OPR', 'SOP', 'LIS'), 'simple');
$userId = $user->idUser;

/*	Settings	*/
$allowedExtensions = array("mp3", "MP3");
$sizeLimit = 5 * 1024 * 1024; // max file size in bytes

class qqUploadedFileXhr
{
	function save($path)
	{
		$input = fopen("php://input", "r");
		$temp = tmpfile();
		$realSize = stream_copy_to_stream($input, $temp);
		fclose($input);

		if ($realSize != $this->getSize()) {
			return false;
		}

		$target = fopen($path, "w");
		fseek($temp, 0, SEEK_SET);
		stream_copy_to_stream($temp, $target);
		fclose($target);

		return true;
	}

	function getName()
	{
		return $_GET['qqfile'];
	}

	function getSize()
	{
		if (isset($_SERVER["CONTENT_LENGTH"])) {
			return (int)$_SERVER["CONTENT_LENGTH"];
		} else {
			throw new Exception('Getting content length is not supported.');
		}
	}
}

class qqUploadedFileForm
{
	/**
	 * Save the file to the specified path
	 *
	 * @return boolean TRUE on success
	 */
	function save($path)
	{
		if (!move_uploaded_file($_FILES['qqfile']['tmp_name'], $path)) {
			return false;
		}
		return true;
	}

	function getName()
	{
		return $_FILES['qqfile']['name'];
	}

	function getSize()
	{
		return $_FILES['qqfile']['size'];
	}
}

class qqFileUploader
{
	private $allowedExtensions = array();
	private $sizeLimit = 10485760;
	private $file;
	private $requestId;

	function __construct(array $allowedExtensions = array(), $sizeLimit = 10485760, $requestId = 0)
	{
		$allowedExtensions = array_map("strtolower", $allowedExtensions);

		$this->allowedExtensions = $allowedExtensions;
		$this->sizeLimit = $sizeLimit;

		$this->checkServerSettings();

		if (isset($_GET['qqfile'])) {
			$this->file = new qqUploadedFileXhr();
		} elseif (isset($_FILES['qqfile'])) {
			$this->file = new qqUploadedFileForm();
		} else {
			$this->file = false;
		}

		$this->requestId = $requestId;
	}

	private function checkServerSettings()
	{
		$postSize = $this->toBytes(ini_get('post_max_size'));
		$uploadSize = $this->toBytes(ini_get('upload_max_filesize'));

		if ($postSize < $this->sizeLimit || $uploadSize < $this->sizeLimit) {
			$size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';
			die("{'error':'increase post_max_size and upload_max_filesize to $size'}");
		}
	}

	private function toBytes($str)
	{
		$val = trim($str);
		$last = strtolower($str[strlen($str) - 1]);
		switch ($last) {
			case 'g':
				$val *= 1024;
			case 'm':
				$val *= 1024;
			case 'k':
				$val *= 1024;
		}
		return $val;
	}

	/**
	 * Returns array('success'=>true) or array('error'=>'error message')
	 */

	function handleUpload($uploadDirectory, $replaceOldFile = false)
	{
		if (!is_writable($uploadDirectory)) {
			return array('error' => "Ошибка на сервере. Директория недоступна.");
		}

		if (!$this->file) {
			return array('error' => 'Файл не передан');
		}

		$size = $this->file->getSize();

		if ($size == 0) {
			return array('error' => 'Файл пустой');
		}

		if ($size > $this->sizeLimit) {
			return array('error' => 'Файл превышает допустимый размер');
		}

		/*	Имя файла - timestamp	*/
		$pathinfo = pathinfo(translit($this->file->getName()));
		$filename = $pathinfo['filename'];
		$filename = preg_replace('/^[\W]/', "", $filename);
		$ext = $pathinfo['extension'];
		//$filename = date("YmdHis")."_".$this->doctorId;
		//$filename = $this->requestId;

		if ($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)) {
			$these = implode(', ', $this->allowedExtensions);
			return array('error' => 'Файл другого типа. Разрешено выкладывать только ' . $these . '.');
		}

		if (!$replaceOldFile) {
			while (file_exists($uploadDirectory . $filename . '.' . $ext)) {
				$filename .= rand(10, 99);
			}
		}

		//если файл сохранился
		if ($this->file->save($uploadDirectory . $filename . '.' . $ext)) {

			$time = time();

			if (!$this->requestId) {
				return array('error' => 'Нельзя сохранить запись. Заявка не выбрана.');
			}

			$record = new RequestRecordModel();
			$record->request_id = $this->requestId;
			$record->record = $filename . '.' . $ext;
			$record->crDate = date('Y-m-d H:i:s', $time);
			$record->source = RequestRecordModel::SOURCE_MANUAL_UPLOADED;
			$record->year = date('Y', $time);
			$record->month = date('n', $time);

			if($record->save()){
				$record->request->setScenario(RequestModel::SCENARIO_RECORD_APPOINTMENT);
				$record->request->addHistory("Загружена запись разговора (" . $record->record . ")");

				return array('success' => true, 'fileNewNаme' => $record->record);
			} else {
				trigger_error(var_export($record->getErrors(), true), E_USER_WARNING);
				return array('error' => 'Не получилось прикрепить запись на уровне БД');
			}

		} else {
			return array('error' => 'Не получилось сохранить файл. Загрузка прервана');
		}
	}
}

$uploader = new qqFileUploader($allowedExtensions, $sizeLimit, $requestId);
$result = $uploader->handleUpload(Yii::app()->params['records_upload_dir'] . "/");
echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
