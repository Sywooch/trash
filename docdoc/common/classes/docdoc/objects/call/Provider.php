<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 16.07.14
 * Time: 10:43
 */

namespace dfs\docdoc\objects\call;

use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\models\RequestRecordModel;
use dfs\docdoc\models\ClinicPartnerPhoneModel;
use getID3;

/**
 * Провайдер телефонии
 *
 * Class Provider
 *
 * @package dfs\docdoc\objects\call
 */
abstract class Provider implements ProviderInterface
{
	/**
	 * @var array
	 */
	protected $config;

	/**
	 * Строка, содержащая в себе путь, основанный на дате. Пример 2014/12/1
	 *
	 * @var null|string
	 */
	protected $dateDir = null;

	/**
	 * Берет настройки из конфига
	 *
	 * @return array
	 */
	private static function getConfig()
	{
		return \Yii::app()->params['phone_providers'];
	}

	/**
	 * Геттер для id
	 *
	 * @return int
	 */
	public function getId()
	{
		return $this->config['id'];
	}

	/**
	 * Геттер для имени
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->config['name'];
	}

	/**
	 * Возвращает всех провайдеров телефонии, пока это только Uiscom.
	 *
	 * @return ProviderInterface[]
	 */
	public static function getAll()
	{
		$all = [];

		foreach (self::getConfig()['config'] as $c) {
			$all[] = self::createProviderFromConfig($c);
		}

		return $all;
	}

	/**
	 * @param array $config
	 *
	 * @return ProviderInterface
	 */
	protected static function createProviderFromConfig(array $config)
	{
		$class = $config['class'];
		unset($config['class']);
		$provider = new $class;
		$provider->config = $config;

		return $provider;
	}

	/**
	 * Ищет провайдера по id
	 *
	 * @param $id
	 *
	 * @return ProviderInterface|null
	 */
	public static function findById($id)
	{
		$obj = null;
		$config = self::getConfig()['config'];

		$find = array_filter(
			$config,
			function ($x) use ($id) {
				return $x['id'] == $id;
			}
		);

		if($find){
			$obj = self::createProviderFromConfig(array_shift($find));
		}

		return $obj;
	}

	/**
	 * Сравнивает записи в директории и в базе и возвращает список не загруженных в базу
	 *
	 * @param string $dateFrom
	 * @param string $dateTo
	 *
	 * @return array
	 */
	public function getNotInsertedFilesInInterval($dateFrom, $dateTo)
	{

		$db = \Yii::app()->getDb();

		$command = $db->createCommand(
			"select record from request_record
				where crDate between :date1 and :date2 and record not like :like and source = :source"
		);
		$command->bindValues(
			[
				':date1'   => $dateFrom,
				':date2'   => $dateTo,
				':like'    => '%record%',
				':source' => $this->getId(),
			]
		);

		$excludeList = $command->queryColumn();

		$dir = self::getConfig()['download_dir'] . $this->getLocalPathPrefix() . $this->dateDir;

		$notParsed = [];

		if (file_exists($dir) && is_dir($dir)) {
			$notParsed = array_diff(scandir($dir), $excludeList);
		}

		$to_return = [];

		foreach ($notParsed as $file) {
			if (is_file($dir . "/" . $file) && pathinfo($file, PATHINFO_EXTENSION) === 'mp3') {
				$to_return[] = $file;
			}
		}

		return $to_return;
	}

	/**
	 * Создает запись на основе файла
	 *
	 * @param string $file
	 *
	 * @return RequestRecordModel
	 */
	public function createRecord($file)
	{
		$fullFileName =
			\Yii::app()->params['phone_providers']['download_dir'] .
			$this->getLocalPathPrefix() .
			$this->dateDir .
			DIRECTORY_SEPARATOR .
			$file;

		$clinicId = null;
		$dateTime = $this->getCreatedTime($file);
		!$dateTime && $dateTime = date('c', $_SERVER['REQUEST_TIME']);

		$replacedPhone = null;
		$duration = $this->getDuration($fullFileName);
		$clinicPhone = $this->getDestinationPhone($file);
		$replacedPhone = $this->getReplacedPhone($file);
		$callId = $this->getCallId($file);

		if($clinicPhone){
			//ищу по подменному телефону для партнера
			$clinicPartnerPhone = ClinicPartnerPhoneModel::model()
				->byPhone($replacedPhone)
				->find();
			$clinic = !is_null($clinicPartnerPhone) ? $clinicPartnerPhone->clinic : null;

			//если нет партнерского телефона, тот ищем в клинике
			if (is_null($clinic)) {
				//ищу по телефону и подменным, если не нахожу- только по телефону.
				//если за один запрос, запрос получается сложный
				if (!$clinic = ClinicModel::model()->byPhone($clinicPhone)->byReplacedPhone($replacedPhone)->find()) {
					$clinic = ClinicModel::model()->byPhone($clinicPhone)->find();
				}
			}

			if ($clinic instanceof ClinicModel) {
				$clinicId = $clinic->id;
			}
		}

		$record = new RequestRecordModel();
		$record->record = $file;
		$record->crDate = $dateTime;
		$record->duration = $duration;
		$record->clinic_id = $clinicId;
		$record->year = date('Y', strtotime($dateTime));
		$record->month = date('n', strtotime($dateTime));
		$record->source = $this->getId();
		$record->replaced_phone = $replacedPhone;
		$record->external_call_id = $callId;

		if ($record->save()) {
			if (!$replacedPhone) {
				trigger_error(
					"Не найден подменный телефон для " . $record->record . " id = " . $record->record_id,
					E_USER_WARNING
				);
			}
		} else {
			foreach ($record->getErrors() as $errors) {
				foreach ($errors as $e) {
					trigger_error("При сохранении записи $file произошла ошибка $e", E_USER_WARNING);
				}
			}
		}

		return $record;
	}

	/**
	 * Установить директорию, относительно даты, аля 2014/12/01
	 *
	 * @param string $date
	 *
	 * @return mixed|void
	 */
	public function setDateDir($date)
	{
		$this->dateDir = date('/Y/m/d', strtotime($date));
	}

	/**
	 * Геттер для пути, основанном на дате записи
	 *
	 * @return null|string
	 */
	public function getDateDir()
	{
		return $this->dateDir;
	}

	/**
	 * Определяет длительность записи по полному пути к файлу
	 *
	 * @param string $fileName
	 *
	 * @return int
	 */
	public function getDuration($fileName)
	{
		$duration = 0;

		if (file_exists($fileName)) {
			$id3 = new getID3();
			$fileInfo = $id3->analyze($fileName);

			if (!empty($fileInfo['playtime_seconds'])) {
				$duration = floor($fileInfo['playtime_seconds']);
			}
		}

		return $duration;
	}
}
