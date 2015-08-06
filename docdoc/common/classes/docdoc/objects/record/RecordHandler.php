<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 30.07.14
 * Time: 0:14
 */

namespace dfs\docdoc\objects\record;

use dfs\docdoc\models\RequestRecordModel;
use FileStorage;
use getID3;
use dfs\docdoc\objects\call\Provider;

/**
 * Класс для работы с записью как с файлом
 *
 * Class Handler
 */
class RecordHandler
{
	/**
	 * @var \dfs\docdoc\models\RequestRecordModel|null
	 */
	protected $record = null;

	/**
	 * @var bool
	 */
	protected $isLocal = true;

	/**
	 * @var null|FileStorage
	 */
	protected $storage = null;

	/**
	 * @param RequestRecordModel $record
	 */
	public function __construct(RequestRecordModel $record)
	{
		$this->record = $record;
	}

	/**
	 * Возвращает ручку файла
	 *
	 * @return null|resource
	 */
	public function openFile()
	{
		$handle = null;

		$localFileName = $this->buildLocalPath();

		if ($this->isLocal && file_exists($localFileName)) {
			$handle = fopen($localFileName, "r");
		} else {
			$this->isLocal = false;
			$remoteFileName = $this->buildRemotePath();

			if ($remoteFileName) {
				$handle = $this->openRemoteFile($remoteFileName);
			}
		}

		return $handle;
	}

	/**
	 * Возвращает абсолютный локальный путь к файлу
	 *
	 * @return null|string
	 */
	public function buildLocalPath()
	{
		$localPath = \Yii::app()->params['phone_providers']['download_dir'];
		$buildDatePath = true;

		if ($provider = $this->getProvider()) {
			//новая схема через конфиг
			$localPath .= $provider->getLocalPathPrefix();
		} else {
			switch (intval($this->record->source)) {
				//0,1,2 были константами и теперь нигде не используются.
				case 0:
					$localPath .= '/doctors';
					break;
				case 1:
					$localPath .= '/docdoc_calls_2';
					break;
				case 2:
					$localPath .= '/diagnostica';
					break;
				case RequestRecordModel::SOURCE_MANUAL_UPLOADED:
					// тут полный путь
					$localPath = \Yii::app()->params['records_upload_dir'];
					$buildDatePath = false;
					break;
			}
		}

		if ($buildDatePath) {
			$dateDir = date("Y/m/d", strtotime($this->record->getCreatedDate()));
			$localPath .= '/' . $dateDir;
		}

		$localPath .= '/' . $this->record->record;

		return $localPath;
	}

	/**
	 * Возвращает абсолютный удаленный путь к файлу
	 *
	 * @return null|string
	 */
	public function buildRemotePath()
	{
		$ftpPath = null;

		if ($provider = $this->getProvider()) {
			//новая схема через конфиг
			$ftpPath = $provider->getStoragePath();
		} else {
			switch (intval($this->record->source)) {
				//0,1,2 были константами и теперь нигде не используются.
				case 0:
					$ftpPath = '/docdoc_calls';
					break;
				case 1:
					$ftpPath = '/docdoc_calls_2';
					break;
				case 2:
					$ftpPath = '/diagnostica_calls';
					break;
			}
		}

		if ($ftpPath) {
			$dateDir = date("Y/m/d", strtotime($this->record->getCreatedDate()));
			$ftpPath = $ftpPath . '/' . $dateDir . '/' . $this->record->record;
		}

		return $ftpPath;
	}

	/**
	 *  Открывает удаленный файл через ftp
	 *
	 * @param $file
	 *
	 * @return null|resource
	 */
	protected function openRemoteFile($file)
	{
		$handle = $this->getStorage()
			->openFile($file);

		return $handle;
	}

	/**
	 * Размер файла
	 *
	 * @return int
	 */
	public function getFileSize()
	{
		if ($this->isLocal) {
			$size = filesize($this->buildLocalPath());
		} else {
			$size = $this->getStorage()
				->getFileSize($this->buildRemotePath());
		}

		return $size;
	}

	/**
	 * Гетер для стораджа, для работы с ftp
	 *
	 * @return \FileStorage|null
	 */
	public function getStorage()
	{
		if (is_null($this->storage)) {
			$params = \Yii::app()->params['storageRecords'];
			$login = $params['login'];
			$password = $params['password'];
			$url = $params['url'];

			$this->storage = new FileStorage($url, $login, $password);
			$this->storage->connect();
		}

		return $this->storage;
	}

	/**
	 * Получает провайдера для записи
	 *
	 * @return \dfs\docdoc\objects\call\ProviderInterface|null
	 */
	public function getProvider()
	{
		$provider = Provider::findById($this->record->source);
		return $provider;
	}

	/**
	 * Получение длительности записи
	 *
	 * @return float|int
	 * @throws \CException
	 */
	public function getDuration()
	{
		$filename = $this->buildLocalPath();

		if (file_exists($filename)) {
			$id3 = new getID3();
			$fileInfo = $id3->analyze($filename);

			if (empty($fileInfo['playtime_seconds'])) {
				throw new \CException("Найдена пустая аудиозапись {$this->record->record}");
			}

			$duration = floor($fileInfo['playtime_seconds']);
		} else {
			throw new \CException("Не найдена аудиозапись {$this->record->record}");
		}

		return $duration;
	}
} 
