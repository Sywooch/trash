<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 16.07.14
 * Time: 11:05
 */

namespace dfs\docdoc\objects\call;

use dfs\docdoc\models\RequestRecordModel;

/**
 * Interface ProviderInterface
 *
 * @package dfs\docdoc\objects\call
 */
interface ProviderInterface
{
	/**
	 * @return integer
	 */
	public function getId();

	/**
	 * @return string
	 */
	public function getName();

	/**
	 * Получить директорию в которую складываются загруженные файлы, относительно глобальной директории загрузки файлов
	 *
	 * @return string
	 */
	public function getLocalPathPrefix();

	/**
	 * @param string $date
	 *
	 * @return string[]
	 */
	public function loadFiles($date);

	/**
	 * Получить список записей не вставленных в базу за интервал
	 *
	 * @param string $dateFrom
	 * @param string $dateTo
	 *
	 * @return string[]
	 */
	public function getNotInsertedFilesInInterval($dateFrom, $dateTo);

	/**
	 * @param string $date
	 *
	 * @return mixed
	 */
	public function setDateDir($date);

	/**
	 * @return string
	 */
	public function getDateDir();

	/**
	 * @param $file
	 *
	 * @return RequestRecordModel
	 */
	public function createRecord($file);

	/**
	 * @param $protocol
	 *
	 * @return string
	 */
	public function getLogin($protocol);

	/**
	 * @param $protocol
	 *
	 * @return string
	 */
	public function getPassword($protocol);

	/**
	 * @param $protocol
	 *
	 * @return mixed
	 */
	public function getUrl($protocol);

	/**
	 * Папка на нашем ftp сервере,
	 *
	 * @return string
	 */
	public function getStoragePath();

	/**
	 * Получить подменный номер
	 *
	 * @param string $file
	 *
	 * @return mixed
	 */
	public function getReplacedPhone($file);

	/**
	 * Получить телефон клиники из имени файла
	 *
	 * @param string $file
	 *
	 * @return string|null
	 */
	public function getDestinationPhone($file);

	/**
	 * Получить номер телефона с которого звонили
	 *
	 * @param string $file
	 *
	 * @return string|null
	 */
	public function getCallerPhone($file);

	/**
	 * Узнать идентификатор партнера из названия файла
	 *
	 * @param string $file
	 *
	 * @return string|null
	 */
	public function getPartnerId($file);

	/**
	 * Получить дау создания записи
	 *
	 * @param string $file
	 *
	 * @return string|null
	 */
	public function getCreatedTime($file);

	/**
	 * Получить идентификатор звонка
	 *
	 * @param string $file
	 *
	 * @return mixed
	 */
	public function getCallId($file);
} 
