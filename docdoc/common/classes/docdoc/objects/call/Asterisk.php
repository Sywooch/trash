<?php
/**
 * Created by PhpStorm.
 * User: atyutyunnikov
 * Date: 19.02.15
 * Time: 16:00
 */

namespace dfs\docdoc\objects\call;

use FileStorage;

/**
 * Class Asterisk
 * Работа с аудиозаписями загруженными из наших астерисков
 *
 *
 * @package dfs\docdoc\objects\call
 */
class Asterisk extends Provider
{
	/**
	 * @var FileStorage
	 */
	private $_storage = null;

	/**
	 * Получить директорию в которую складываются загруженные файлы, относительно глобальной директории загрузки файлов
	 *
	 * @return mixed
	 */
	public function getLocalPathPrefix()
	{
		return $this->config['local_path'];
	}

	/**
	 * Загрузка файлов за определенную дату
	 *
	 * @param string $date
	 *
	 * @return string[]
	 */
	public function loadFiles($date)
	{
		$storage = $this->getStorage();

		$dateDir = date('/Y/m/d', strtotime($date));
		$remotePath = $this->config['ftp']['remote_path'] . $dateDir;
		$localPath = \Yii::app()->params['phone_providers']['download_dir'] . $this->getLocalPathPrefix() . $dateDir;

		$fileList = $storage->loadListOfRemoteFiles($remotePath);

		$toLoad = [];

		foreach ($fileList as $file) {
			$toLoad[$localPath][] = $file;
		}

		$total = count($fileList);
		$loaded = $storage->loadFiles($toLoad);

		return [count($loaded), $total, $total];
	}

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
	 * Геттер логина от ftp
	 *
	 * @param string $protocol в данной реализации не используется
	 *
	 * @return string
	 */
	public function getLogin($protocol)
	{
		return $this->config['ftp']['login'];
	}

	/**
	 * Геттер пароля от ftp
	 *
	 * @param string $protocol в данной реализации не используется
	 *
	 * @return string
	 */
	public function getPassword($protocol)
	{
		return $this->config['ftp']['password'];
	}

	/**
	 * Геттер ftp урла
	 *
	 * @param string $protocol в данной реализации не используется
	 *
	 * @return mixed
	 */
	public function getUrl($protocol)
	{
		return $this->config['ftp']['url'];
	}

	/**
	 * Папка на нашем ftp сервере, куда бекапить скачанные записи
	 *
	 * @return string
	 */
	public function getStoragePath()
	{
		return $this->config['storage_path'];
	}

	/**
	 * Получить подменный номер
	 *
	 * @param string $file
	 *
	 * @return mixed
	 */
	public function getReplacedPhone($file)
	{
		$array = explode('_', pathinfo($file, PATHINFO_FILENAME));

		if(count($array) > 4 && strlen($array[3]) == 11){
			return $array[3];
		}

		return null;
	}

	/**
	 * Получить телефон клиники из имени файла
	 *
	 * @param string $file
	 *
	 * @return mixed
	 */
	public function getDestinationPhone($file)
	{
		$array = explode('_', pathinfo($file, PATHINFO_FILENAME));

		if(count($array) >= 5 && strlen($array[4]) == 11){
			return $array[4];
		}

		return null;
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
		$array = explode('_', pathinfo($file, PATHINFO_FILENAME));

		if(count($array) > 3 && strlen($array[2]) == 11){
			return $array[2];
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
		$array = explode('_', pathinfo($file, PATHINFO_FILENAME));

		if(count($array) >= 6 && strpos($array[5], 'pid', 0) !== false){
			return (int)str_replace('pid', '', $array[5]);
		}

		return null;
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
		$array = explode('_', pathinfo($file, PATHINFO_FILENAME));

		if(count($array) > 2){
			if(count(explode('-', $array[0])) == 3){
				//старый формат, сначала идет дата
				$t = explode('.', $array[1]);

				if(count($t) == 2){
					list($time,) = $t;
					list($hour, $minute, $seconds) = sscanf($time, "%2d %2d %2d");

					if(is_numeric($hour) && is_numeric($minute) && is_numeric($seconds)){
						$date = "$array[0] $hour:$minute:$seconds";

						if(strtotime($date)){
							return $date;
						}
					}
				}
			} elseif(is_numeric($array[1])) {
				$date = date('Y-m-d H:i:s', $array[1]);
				return $date;
			}

		}

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
		$array = explode('_', pathinfo($file, PATHINFO_FILENAME));

		if(count($array) >= 5){
			if(count(explode('-', $array[0])) != 3 && is_numeric($array[1])){
				return $array[1];
			}
		}

		return null;
	}
}