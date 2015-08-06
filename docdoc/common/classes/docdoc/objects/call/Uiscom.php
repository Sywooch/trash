<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 16.07.14
 * Time: 10:45
 */

namespace dfs\docdoc\objects\call;

use dfs\common\config\Environment;
use FileStorage;
use dfs\docdoc\models\CallLogModel;
use dfs\docdoc\objects\Loader;

/**
 * Class Uiscom
 *
 * @package dfs\docdoc\objects\phone
 */
class Uiscom extends Provider
{
	/**
	 * @var FileStorage
	 */
	private $_storage = null;

	/**
	 * @var bool
	 */
	private $_logs_is_loaded = false;

	/**
	 * Получить сторадж, работающий с ftp
	 *
	 * @return FileStorage
	 */
	protected function getStorage()
	{
		if (is_null($this->_storage)) {
			$config = $this->config;
			$this->_storage =
				new FileStorage($config['ftp']['url'], $config['ftp']['login'], $config['ftp']['password']);
		}

		$this->_storage->connect();

		return $this->_storage;
	}



	/**
	 * Загружает файлы на локальный диск
	 * Возвращает массив вида [скопированно, попыток скопировать, всего файлов обошел]
	 *
	 * @param $date
	 *
	 * @return array
	 */
	public function loadFiles($date)
	{
		$storage = $this->getStorage();

		$remotePath = $this->config['ftp']['remote_path'];
		$localPath = \Yii::app()->params['phone_providers']['download_dir'] . $this->getLocalPathPrefix();

		$fileList = $storage->loadListOfRemoteFiles($remotePath);

		$toLoad = [];
		$try = 0;

		foreach($fileList as $file){
			$parseArray = explode("_", $file);
			$dateArray = explode("-", $parseArray[0]);

			if ($parseArray[0] == $date) {
				$dir = $localPath . DIRECTORY_SEPARATOR . "etc";

				if (count($dateArray) == 3) {
					$dir = $localPath . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $dateArray);
				}

				$toLoad[$dir][] = $file;
				$try++;
			}
		}

		$copy = count($storage->loadFiles($toLoad));

		return [$copy, $try, count($fileList)];
	}

	/**
	 * Убиваю конект
	 */
	public function __destruct()
	{
		if (!is_null($this->_storage)) {
			$this->_storage->disconnect();
		}
	}

	/**
	 * Геттер для папки относительно директории для всех записей
	 *
	 * @return string
	 */
	public function getLocalPathPrefix()
	{
		return $this->config['local_path'];
	}

	/**
	 * Геттер для логинов
	 *
	 * @param string $protocol
	 *
	 * @return string
	 */
	public function getLogin($protocol)
	{
		switch ($protocol){
			case 'ftp':
				$login = $this->config['ftp']['login'];
				break;
			case 'http':
			default:
			$login = $this->config['http']['login'];
		}

		return $login;
	}

	/**
	 * Геттер для паролей
	 *
	 * @param string $protocol
	 *
	 * @return mixed
	 */
	public function getPassword($protocol)
	{
		switch ($protocol){
			case 'ftp':
				$password = $this->config['ftp']['password'];
				break;
			case 'http':
			default:
			$password = $this->config['http']['password'];
		}

		return $password;
	}

	/**
	 * Геттер для урлов
	 *
	 * @param $protocol
	 *
	 * @return mixed
	 */
	public function getUrl($protocol)
	{
		switch ($protocol){
			case 'ftp':
				$url = $this->config['ftp']['url'];
				break;
			case 'http':
			default:
				$url = $this->config['http']['url'];
		}

		return $url;
	}

	/**
	 * Геттер для папки относительно корня ftp хранилища
	 * без учета директорий основанных на дате
	 *
	 * @return string
	 */
	public function getStoragePath()
	{
		return $this->config['storage_path'];
	}

	/**
	 * Загружеает логи к нам в базу
	 *
	 * @return void
	 */
	public function loadLogs()
	{
		if(!$this->_logs_is_loaded){
			if(!Environment::isTest()){
				$loader = new Loader($this->getLogin('http'), $this->getPassword('http'), $this->getUrl('http'));
				$loader->fetchAll();
			}

			$this->_logs_is_loaded = true;
		}
	}

	/**
	 * Получить подменный номер
	 *
	 * @param string $file
	 *
	 * @return null|string
	 */
	public function getReplacedPhone($file)
	{
		$this->loadLogs();

		$replacedPhone = null;

		$parseData = $this->parseFileName($file);

		if (isset($parseData['phoneTo']) &&
			isset($parseData['phoneFrom']) &&
			isset($parseData['data4DB']) &&
			isset($parseData['time'])
		) {
			$clinicPhone = $parseData['phoneTo'];
			$callerPhone = $parseData['phoneFrom'];
			$dateTime = $parseData['data4DB'] . " " . $parseData['time'];

			$replacedPhone = CallLogModel::getReplacedPhone($callerPhone, $clinicPhone, $dateTime);
		}

		return $replacedPhone;
	}

	/**
	 * Получить телефон клиники из имени файла
	 *
	 * @param string $file
	 *
	 * @return string|null
	 */
	public function getDestinationPhone($file)
	{
		$dph = null;

		if (preg_match('/to_(\d+)/', $file, $matches)) {
			$dph = $matches[1];
		}

		return $dph;
	}

	/**
	 * Получить номер телефона с которого звонили
	 *
	 * @param string $file
	 *
	 * @return string|null
	 */
	public function getCallerPhone($file)
	{
		$callerPhone = null;

		if (preg_match('/from_(\d+)/', $file, $matches)) {
			$callerPhone = $matches[1];
		}

		return $callerPhone;
	}


	/**
	 * Парсит имя файла
	 *
	 * @param string $file
	 *
	 * @return array
	 */
	protected function parseFileName($file)
	{
		$data = [];

		if (!empty($file)) {
			$tmp = explode("_", $file);

			if (count($tmp) == 7) {
				$dateArray = explode("-", $tmp[0]);
				$timeArrayHMS = explode(",", $tmp[1]);
				$timeArray = explode(".", $timeArrayHMS[0]);

				if(count($timeArray) >= 3){
					$timeNow =
						mktime($timeArray[0], $timeArray[1], $timeArray[2], $dateArray[1], $dateArray[2], $dateArray[0]);

					$data['unixTimestamp'] = $timeNow;
					$data['data'] = $dateArray[2] . "." . $dateArray[1] . "." . $dateArray[0]; //22.11.2013
					$data['data4DB'] = $dateArray[0] . "-" . $dateArray[1] . "-" . $dateArray[2]; //2013-11-22
					$data['year'] = $dateArray[0];
					$data['month'] = $dateArray[1];
					$data['day'] = $dateArray[2];
					$data['time'] = $timeArray[0] . ":" . $timeArray[1] . ":" . $timeArray[2];
					$data['phoneFrom'] = $tmp[3];
					$data['phoneTo'] = $tmp[5];
				}

			}
		}

		return $data;
	}

	/**
	 * Возвращает список не удавшихся звонков
	 *
	 * @return array
	 */
	public function getFailedLogs()
	{
		$loader = new Loader($this->getLogin('http'), $this->getPassword('http'), $this->getUrl('http'));
		return $loader->getCenrexAll();
	}

	/**
	 * Получить дау создания записи
	 *
	 * @param string $file
	 *
	 * @return string|null
	 */
	public function getCreatedTime($file)
	{
		$parseData = $this->parseFileName($file);

		if (isset($parseData['data4DB']) && isset($parseData['time'])) {
			$dateTime = $parseData['data4DB'] . " " . $parseData['time'];

			return $dateTime;
		}

		return null;
	}

	/**
	 * Узнать идентификатор партнера из названия файла
	 *
	 * @param string $file
	 *
	 * @return string|null
	 */
	public function getPartnerId($file)
	{
		return null;
	}

	/**
	 * Получить идентификатор звонка
	 *
	 * @param string $file
	 *
	 * @return mixed
	 */
	public function getCallId($file)
	{
		return null;
	}
} 
