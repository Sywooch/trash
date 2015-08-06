<?php

/**
 * Class FileUploader
 *
 */
class FileUploader
{

	private $_allowedExtensions = array();
	private $_sizeLimit = 10485760;
	private $_id;
	private $_file;

	/**
	 * @param array $allowedExtensions
	 * @param int $sizeLimit
	 * @param $file
	 * @param int $id
	 */
	public function __construct($allowedExtensions = array(), $sizeLimit = 10485760, $file, $id = 0)
	{
		$allowedExtensions = array_map("strtolower", $allowedExtensions);

		$this->_allowedExtensions = $allowedExtensions;
		$this->_sizeLimit = $sizeLimit;
		$this->_file = $file;

		$this->checkServerSettings();

		$this->_id = $id;
	}

	/**
	 * Check server settings
	 *
	 */
	private function checkServerSettings()
	{
		$postSize = $this->toBytes(ini_get('post_max_size'));
		$uploadSize = $this->toBytes(ini_get('upload_max_filesize'));

		if ($postSize < $this->_sizeLimit || $uploadSize < $this->_sizeLimit) {
			$size = max(1, $this->_sizeLimit / 1024 / 1024) . 'M';
			die("{'error':'increase post_max_size and upload_max_filesize to $size'}");
		}
	}

	/**
	 * Convert string to bytes
	 * @param string $str
	 * @return integer
	 */
	private function toBytes($str)
	{
		$val = trim($str);
		$last = strtolower($str[strlen($str)-1]);
		switch($last) {
			case 'g': $val *= 1024;
			case 'm': $val *= 1024;
			case 'k': $val *= 1024;
		}
		return $val;
	}

	/**
	 * Resize file
	 * @param $file
	 * @return bool
	 */
	public function resizeFile($file)
	{
		if (!img_resize_2($file, widthPrv, heightPrv, widthMin, heightMin, 90)) {
			return false;
		}
		return true;
	}

	/**
	 * Upload file
	 * @param string $uploadDirectory
	 * @param bool $replaceOldFile
	 * @return array
	 */
	public function handleUpload($uploadDirectory, $replaceOldFile = FALSE)
	{
		if (!is_writable($uploadDirectory)) {
			return array('error' => "Ошибка на сервере. Директория недоступна.");
		}

		if (!$this->_file) {
			return array('error' => 'Файл не передан');
		}

		$size = $this->getSize();

		if ($size == 0) {
			return array('error' => 'Файл пустой');
		}

		if ($size > $this->_sizeLimit) {
			return array('error' => 'Файл превышает допустимый размер');
		}

		/*	Имя файла - timestamp	*/
		$pathinfo = pathinfo($this->_file);
		$ext = $pathinfo['extension'];

		$filename = $this->_id;

		if ($this->_allowedExtensions && !in_array(strtolower($ext), $this->_allowedExtensions)) {
			$these = implode(', ', $this->_allowedExtensions);
			return array('error' => 'Файл другого типа. Разрешено выкладывать только ' . $these . '.');
		}

		if (!$replaceOldFile) {
			while (file_exists($uploadDirectory . $filename . '.' . $ext)) {
				$filename .= rand(10, 99);
			}
		}

		if ($this->save($uploadDirectory . $filename . '.' . $ext)) {
			return array(
				'success' => true,
				'file' => $filename . "." . $ext,
				'filePath' => 'http://' . SERVER_FRONT . '/upload/kliniki/logo/' . $filename . "." . $ext . "?param=" . rand(0, 1000),
			);
		} else {
			return array('error' => 'Не получилось сохранить файл. Загрузка прервана');
		}
	}

	/**
	 * Save file
	 * @param string $path
	 * @return bool
	 */
	private function save($path)
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

		chmod($path, FILE_MODE);

		return true;
	}

	/**
	 * Get file size
	 * @return int
	 * @throws Exception
	 */
	private function getSize()
	{
		if (isset($_SERVER["CONTENT_LENGTH"])){
			return (int)$_SERVER["CONTENT_LENGTH"];
		} else {
			throw new Exception('Getting content length is not supported.');
		}
	}
}