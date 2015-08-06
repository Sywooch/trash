<?php

/**
 * FTP сервер с аудиозаписями
 *
 * Class FileStorage
 */
class FileStorage
{
	private $host;
	private $login;
	private $password;
	private $conn;

	/**
	 * @param string $host
	 * @param string $login
	 * @param string $password
	 */
	public function __construct($host, $login, $password)
	{
		$this->host = $host;
		$this->login = $login;
		$this->password = $password;
	}

	/**
	 * Подключение к серверу
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function connect()
	{
		$this->conn = $this->conn ?: ftp_connect($this->host);

		if (!$this->conn) {
			throw new Exception("Error: Failed to connect");
		}

		if (!ftp_login($this->conn, $this->login, $this->password)) {
			throw new Exception("Error: Can`t change directory");
		}

		return true;
	}

	/**
	 * @return mixed
	 * @throws Exception
	 */
	public function getConnection()
	{
		is_null($this->conn) && $this->connect();

		return $this->conn;
	}

	/**
	 * Отключение от сервера
	 *
	 * @return bool
	 */
	public function disconnect()
	{
		return $this->conn ? ftp_close($this->conn) : false;
	}

	/**
	 * Получить список файлов с удаленного сервера
	 *
	 * @param string $remotePath
	 *
	 * @return string[]
	 * @throws Exception
	 */
	public function loadListOfRemoteFiles($remotePath)
	{
		$connection = $this->getConnection();

		if (!ftp_chdir($connection, $remotePath)) {
			throw new Exception("Error: Not authorized");
		}

		$contents = ftp_nlist($connection, ".");

		return $contents;
	}

	/**
	 * Загружает файлы
	 *
	 * @param array $filesToLoad в формате [локальная папка => [удаленный файл1, удаленный файл2]]
	 *
	 * @return string[]
	 */
	public function loadFiles(array $filesToLoad)
	{
		$loaded = [];

		foreach ($filesToLoad as $dir => $files) {
			foreach ($files as $file) {
				$tmpLocalFile = $dir . DIRECTORY_SEPARATOR . $file . ".tmp";
				$localFile = $dir . DIRECTORY_SEPARATOR . $file;

				// Конвертированный файл в mp3
				$parseFileName = pathinfo($localFile);
				$localFileConverted = $dir . DIRECTORY_SEPARATOR . $parseFileName['filename'] . ".mp3";

				// Проверка на директорию по дню
				if (!file_exists($dir)) {
					if (!mkdir($dir, DIR_MODE, true)) {
						return $loaded;
					}
				}

				if (!file_exists($localFileConverted) && !file_exists($localFile)) {
					if (ftp_get($this->conn, $tmpLocalFile, $file, FTP_BINARY)) {
						rename($tmpLocalFile, $localFile);
						chmod($localFile, FILE_MODE);
						$loaded[] = $localFile;
					} else {
						\Yii::log("Error: File can`t be copied from {$file}", CLogger::LEVEL_ERROR);
					}
				} else {
					\Yii::log("{$file} already exist");
				}
			}
		}

		return $loaded;
	}

	/**
	 * @param string $filename
	 *
	 * @return bool
	 */
	public function getFileSize($filename)
	{
		$size = ftp_size($this->conn, $filename);
		return $size;
	}

	/**
	 * Открывает файл
	 *
	 * @param string $filename
	 * @param string $mode
	 *
	 * @return resource
	 */
	public function openFile($filename, $mode = 'r')
	{
		$file = null;

		if ($this->getFileSize($filename) !== -1) {
			$fullName = sprintf("ftp://%s:%s@%s%s", $this->login, $this->password, $this->host, $filename);
			$file = fopen($fullName, $mode);
		}

		return $file;
	}

	/**
	 * Загрузка файлов на сервер
	 *
	 * @param string $file
	 * @param string $path
	 *
	 * @throws Exception
	 *
	 * @return bool;
	 */
	public function uploadFile($file, $path)
	{
		Yii::setPathOfAlias('ext', ROOT_PATH . '/common/vendor/hguenot');

		$ftp = new GFtpComponent("ftp://$this->login:$this->password@$this->host:21", 90, false);
		$ftp->connect();
		$ftp->login();

		$dirArr = [
			[
				'path' => $path,
				'name' => date("Y"),
			],
			[
				'path' => $path . "/" . date("Y"),
				'name' => date("m"),
			],
			[
				'path' => $path . "/" . date("Y") . "/" . date("m"),
				'name' => date("d"),
			],
		];

		try {
			foreach ($dirArr as $ftpDir) {
				$dirList = $ftp->ls($ftpDir['path'], true);
				$dirNames = [];

				foreach ($dirList as $dir) {
					$dirNames[] = $dir->filename;
				}

				// Проверяет наличие директории и создает  ее, если она отсутствует
				if (!in_array($ftpDir['name'], $dirNames)) {
					Yii::log("Try create dir " . $ftpDir['name']);

					$ftp->mkdir($ftpDir['path'] . DIRECTORY_SEPARATOR . $ftpDir['name']);
					Yii::log("Directory " . $ftpDir['path'] . DIRECTORY_SEPARATOR . $ftpDir['name'] . " was created ");
				}
			}
		} catch (Exception $e) {
			throw new CException("Ошибка создания директорий на ftp сервере:" . $e->getMessage());
		}

		$ftpDir = $dirArr[2]['path'] . DIRECTORY_SEPARATOR . $dirArr[2]['name'];
		$fileInfo = pathinfo($file);
		$fileName = $fileInfo['filename'] . '.' .$fileInfo['extension'];

		$ftp->put(FTP_BINARY, $file, $ftpDir . DIRECTORY_SEPARATOR . $fileName);
		return true;
	}
}
