<?php

use dfs\docdoc\models\RequestModel;
use dfs\docdoc\models\RequestHistoryModel;

require_once __DIR__ . "/../dateTimeLib.php";

/**
 * Заявка на запись в клинику
 *
 * Class DocRequest
 *
 * @property int $id
 * @property int $req_id
 * @property int $id_city
 * @property string $client_phone
 * @property string $client_name
 * @property int $req_created
 * @property int $req_status
 * @property int $req_user_id
 * @property int $req_sector_id
 * @property int $req_doctor_id
 * @property int $req_type
 * @property int $source_type
 * @property int $clinic_id
 * @property int $is_transfer
 * @property int $date_admission
 * @property int $call_later_time
 * @property string $last_call_id
 * @property int $partner_id
 * @property int $destination_phone_id
 * @property string $date_record
 * @property string $add_client_phone
 * @property int $transferred_clinic_id
 * @property int $is_hot
 * @property int $for_listener
 * @property string $phone_to
 * @property string $destination_phone
 * @property int $reject_reason
 * @property int $enter_point
 */
class DocRequest
{
	// Статусы
	const STATUS_NEW                = 0;
	const STATUS_PROCESS            = 1;
	const STATUS_RECORD             = 2;
	const STATUS_CAME               = 3;
	const STATUS_REMOVED            = 4;
	const STATUS_REJECT             = 5;
	const STATUS_ACCEPT             = 6;
	const STATUS_CALL_LATER         = 7;
	const STATUS_REJECT_BY_PARTNER  = 8;
	const STATUS_RECALL             = 10;

	// Способы обращения
	const TYPE_WRITE_TO_DOCTOR  = 0;
	const TYPE_PICK_DOCTOR      = 1;
	const TYPE_CALL             = 2;
	const TYPE_CALL_TO_DOCTOR   = 3;

	/**
	 * Виды заявок
	 */
	const KIND_DOCTOR       = 0;
	const KIND_DIAGNOSTICS  = 1;
	const KIND_ANALYSIS     = 2;

	// Источники
	const SOURCE_SITE       = 1;
	const SOURCE_PHONE      = 2;
	const SOURCE_PARTNER    = 3;
	const SOURCE_YANDEX     = 4;
	const SOURCE_IPHONE     = 5;

	// Тип действия в логах
	const LOG_TYPE_ACTION = 1;
	const LOG_TYPE_COMMENT = 2;
	const LOG_TYPE_CHANGE_STATUS = 3;

	// Источник аудиозаписи
	const SOURCE_RECORD = 1;

	// Признак переведенной заявки
	const TRANSFERRED = 1;

	// Время в секундах, при котором заявки считаются одинаковыми - 14 дней
	const DIFF_TIME_FOR_MERGED_REQUEST = 1209600;

	private $_attributes;

	/**
	 * Настройки статусов
	 * @var array
	 */
	static public $statuses = array(
		array("Id" => self::STATUS_NEW, "Title" => "Новая", "Sort" => 1, "Visibility" => "hide"),
		array("Id" => self::STATUS_PROCESS, "Title" => "В обработке", "Sort" => 2, "Visibility" => "show"),
		array("Id" => self::STATUS_RECORD, "Title" => "Пациент записан", "Sort" => 3, "Visibility" => "show"),
		array("Id" => self::STATUS_CAME, "Title" => "Пациент дошел", "Sort" => 4, "Visibility" => "hide"),
		array("Id" => self::STATUS_REMOVED, "Title" => "Удалена", "Sort" => 5, "Visibility" => "hide"),
		array("Id" => self::STATUS_REJECT, "Title" => "Отказ", "Sort" => 6, "Visibility" => "show"),
		array("Id" => self::STATUS_ACCEPT, "Title" => "Принята", "Sort" => 7, "Visibility" => "hide"),
		array("Id" => self::STATUS_CALL_LATER, "Title" => "Перезвонить", "Sort" => 8, "Visibility" => "show"),
		array("Id" => self::STATUS_REJECT_BY_PARTNER, "Title" => "Отклонена партнёром", "Sort" => 9, "Visibility" => "hide"),
		array("Id" => self::STATUS_RECALL, "Title" => "Повторный звонок", "Sort" => 11, "Visibility" => "true"),
	);

	/**
	 * Имена клиентов по умолчанию
	 * @var array
	 */
	static private $_defaultClientNames = array('asterisk', 'звонок на врача', 'звонок через партнера');

	/**
	 * Constructor
	 *
	 * @param null $id
	 */
	public function __construct($id = null)
	{
		if (!empty($id)) {
			$this->loadModel($id);
		}
	}

	/**
	 * Setter
	 *
	 * @param string $name
	 * @param void $value
	 */
	public function __set($name, $value)
	{
		$this->_attributes[$name] = $value;
	}

	/**
	 * Getter
	 *
	 * @param string $name
	 *
	 * @return void|bool
	 */
	public function __get($name)
	{
		if (!isset($this->_attributes[$name])) {
			return false;
		}
		return $this->_attributes[$name];
	}

	/**
	 * Получение всех данных о заявке
	 *
	 * @return mixed
	 */
	public function getAttributes()
	{
		return $this->_attributes;
	}


	/**
	 * Получение названий статусов
	 *
	 * @return array
	 */
	public static function getStatusNames()
	{
		$data = array();
		foreach (self::$statuses as $item) {
			$data[$item['Id']] = $item['Title'];
		}

		return $data;
	}

	/**
	 * Получение названий видов заявок
	 *
	 * @return string[]
	 */
	static public function getKindNames()
	{
		return array(
			self::KIND_DOCTOR       => 'Врач',
			self::KIND_DIAGNOSTICS  => 'Диагностика',
			// Закрываем анализы пока функционал не доделан
			// self::KIND_ANALYSIS     => 'Анализы',
		);
	}

	/**
	 * Получение названий способов обращений
	 *
	 * @return string[]
	 */
	static public function getTypeNames()
	{
		return array(
			self::TYPE_WRITE_TO_DOCTOR  => 'Запись к врачу',
			self::TYPE_PICK_DOCTOR      => 'Подбор врача',
			self::TYPE_CALL             => 'Телефонное обращение',
			self::TYPE_CALL_TO_DOCTOR   => 'Звонок в клинику',
		);
	}

	/**
	 * Получение дефолтных значений фио пациентов
	 */
	static public function getDefaultClientNames()
	{
		return self::$_defaultClientNames;
	}

	/**
	 * Получение исключающих статусов заявок для ЛК
	 *
	 * @return array
	 */
	static public function getExcludeStatuses()
	{
		return array(
			self::STATUS_NEW,
			self::STATUS_REMOVED,
			self::STATUS_ACCEPT,
			self::STATUS_PROCESS,
		);
	}

	/**
	 * Установить статус
	 *
	 * @param int $status
	 *
	 * @return bool
	 */
	public function setStatus($status)
	{
		$this->_attributes['req_status'] = $status;
		return true;
	}

	/**
	 * Установить статус повторный вызов
	 *
	 * @return bool
	 */
	public function setRecall()
	{
		if ($this->_attributes['req_status'] == self::STATUS_REJECT
			|| $this->_attributes['req_status'] == self::STATUS_REMOVED
		) {
			$this->setStatus(self::STATUS_RECALL);
			$this->_attributes['reject_reason'] = 0;
			$this->addHistory("Изменен статус -> 'Повторный звонок'", self::LOG_TYPE_CHANGE_STATUS);
		}
		return true;
	}

	/**
	 * Установить статус и время перезвона
	 *
	 * @param $time
	 * @return bool
	 */
	public function setToCall($time)
	{
		$this->setStatus(self::STATUS_CALL_LATER);
		$this->_attributes['call_later_time'] = $time;
		return true;
	}

	/**
	 * Получение списка аудизаписей
	 *
	 * @return array
	 */
	public function getAudioList()
	{
		$records = array();

		if ($this->_attributes['id'] > 0) {
			$sql = "SELECT
						t1.record_id AS Id,
						t1.request_id AS RequestId,
						t1.record AS FileName,
						DATE_FORMAT(t1.crDate, '%d.%m.%Y') AS CrDate,
						DATE_FORMAT(t1.crDate, '%H:%i') AS CrTime,
						DATE_FORMAT(t1.crDate, '%d.%m.%Y %H:%i') AS CrDateTime,
						t1.duration AS Duration,
						t1.source AS Type,
						t1.isAppointment AS IsAppointment,
						t1.isVisit AS IsVisit
					FROM request_record t1
					WHERE
						t1.request_id = " . $this->_attributes['id'] . "
					ORDER BY t1.crDate DESC";
		}
		$result = query($sql);
		if (num_rows($result) > 0) {
			$i = 0;
			while ($row = fetch_array($result)) {
				array_push($records, $row);
				$records[$i]['DurationSec'] = $row ['Duration'];
				$records[$i]['DurationFormated'] = formatTime($row ['Duration']);
				$i++;
			}
		}

		return $records;
	}

	/**
	 * Получение истории действий
	 *
	 * @return array
	 */
	public function getHistory()
	{
		$history = array();

		if ($this->_attributes['id'] > 0) {
			$sql = "SELECT
						t1.id,
						t1.request_id AS RequestId,
						t1.text AS Text,
						DATE_FORMAT(t1.created, '%d.%m.%Y') AS CrDate,
						DATE_FORMAT(t1.created, '%H:%i') AS CrTime,
						t1.action AS Type,
						t1.user_id AS UserId,
						t2.user_login as Nick,
						concat(t2.user_fname,' ',t2.user_lname) AS UserName
					FROM request_history t1
					LEFT JOIN `user` t2 ON (t1.user_id = t2.user_id)
					WHERE
						t1.request_id = " . $this->_attributes['id'] . "
					ORDER BY t1.created DESC, t1.id DESC";
		}
		$result = query($sql);
		if (num_rows($result) > 0) {
			while ($row = fetch_array($result)) {
				array_push($history, $row);
			}
		}

		return $history;
	}

	/**
	 * Получение даты в заданном формате
	 *
	 * @param        $attr
	 * @param string $format
	 *
	 * @return bool|string
	 */
	public function getFormattedDate($attr, $format = 'd.m.y H:i:s')
	{
		$date = isset($this->_attributes[$attr]) ? strtotime($this->_attributes[$attr]) : 0;
		return $date > 0 ? date($format, $date) : '';
	}

	/**
	 * Получение списка статусов
	 *
	 * @return bool|void
	 */
	static public function getStatuses()
	{
		return self::$statuses;
	}

	/**
	 * Добавление аудиозаписи
	 *
	 * @param string $fileName
	 * @param array $params
	 *
	 * @return bool|int
	 */
	public function addRecord($fileName, $params = array())
	{

		$duration =
			(isset($params['duration']) && intval($params['duration']) > 0) ? intval($params['duration']) : 'null';
		$recordDatetime =
			(isset($params['crDateTime']) && $params['crDateTime']) ? $params['crDateTime'] : 'NOW()';
		$clinicId = isset($params['clinicId']) && !is_null($params['clinicId']) ? $params['clinicId'] : 0;
		$source   = isset($params['source']) ? $params['source'] : self::SOURCE_RECORD;

		$year = date('Y', strtotime($recordDatetime));
		$month = date('n', strtotime($recordDatetime));

		if ($this->_attributes['id'] > 0 && !empty($fileName)) {
			$sql = "INSERT INTO request_record SET
						request_id = {$this->_attributes['id']},
						record = '{$fileName}',
						crDate = '{$recordDatetime}',
						duration = {$duration},
						clinic_id = {$clinicId},
						year = {$year},
						month = {$month},
						source = {$source}";
			query($sql);
			$id = legacy_insert_id();
			return $id;
		}

		return false;
	}

	/**
	 * Добавить логи
	 *
	 * @param string $text   Текст сообщения
	 * @param int    $action
	 * @param int    $userId
	 *
	 * @return bool|int
	 */
	public function addHistory($text, $action = 2, $userId = 0)
	{
		if ($this->_attributes['id'] > 0) {
			$history = new RequestHistoryModel();
			$history->request_id = $this->_attributes['id'];
			$history->user_id = $userId;
			$history->action = $action;
			$history->text = $text;

			if ($history->save()) {
				return $history->getPrimaryKey();
			}
		}

		return false;
	}

	/**
	 * Сохранить аудиоданные
	 *
	 * @param array $isAppointment
	 * @param array $isVisit
	 *
	 * @return bool
	 */
	public function saveAudioData($isAppointment = array(), $isVisit = array())
	{
		if ($this->_attributes['id'] > 0) {
			$data = $this->getAudioList();
			if (count($data) > 0) {
				foreach ($data as $row) {
					$id = $row['Id'];
					$clinicId = !empty($this->_attributes['clinic_id']) ? $this->_attributes['clinic_id'] : 0;
					if (isset($isAppointment[$id]) && $isAppointment[$id] == 'yes') {
						$sql = "UPDATE request_record SET isAppointment = 'yes', clinic_id = {$clinicId} WHERE record_id = " . $id;
					} else {
						$sql = "UPDATE request_record SET isAppointment = 'no' WHERE record_id = " . $id;
					}

					query($sql);
					if (isset($isVisit[$id]) && $isVisit[$id] == 'yes') {
						$sql = "UPDATE request_record SET isVisit = 'yes', clinic_id = {$clinicId} WHERE record_id = " . $id;
					} else {
						$sql = "UPDATE request_record SET isVisit = 'no' WHERE record_id = " . $id;
					}
					query($sql);
				}
			}

			return true;
		}
	}

	/**
	 * Получение id последней заявки с таким же номером телефона клиента
	 *
	 * @param $phone
	 * @param array $exceptPhones
	 * @param array $addParams
	 *
	 * @return null
	 */
	static public function getLastIdByPhone($phone, $exceptPhones = array(), $addParams = array())
	{
		$id = null;
		$sqlAdd = "";

		if (empty($phone)) {
			return $id;
		}

		if (count($exceptPhones) > 0) {
			$exceptPhones = implode(',', $exceptPhones);
			$sqlAdd .= " AND t1.client_phone NOT IN ({$exceptPhones})";
		}

		if (count($addParams)) {
			if (isset($addParams['clinicId']) && is_int($addParams['clinicId'])) {
				$sqlAdd .= " AND t1.clinic_id={$addParams['clinicId']}";
			}
			if (isset($addParams['type']) && !is_null($addParams['type'])) {
				if (is_array($addParams['type'])) {
					$addParams['type'] = implode(',', $addParams['type']);
				}
				$sqlAdd .= " AND t1.req_type IN ({$addParams['type']})";
			}
		}

		$time = time() - self::DIFF_TIME_FOR_MERGED_REQUEST;
		$sql = "SELECT t1.req_id
				FROM request t1
				WHERE (t1.client_phone='{$phone}' OR t1.add_client_phone='{$phone}')
					AND t1.req_status <> " . self::STATUS_CAME . "
					AND t1.req_created>{$time}
					{$sqlAdd}
				ORDER BY t1.req_created DESC
				LIMIT 1";

		$result = query($sql);
		if (num_rows($result)) {
			$row = fetch_object($result);
			$id = $row->req_id;
		}

		return $id;
	}

	/**
	 * Добавление номера телефона адресата
	 *
	 * @param $phone
	 *
	 * @return bool
	 */
	public function addDestinationPhone($phone)
	{
		$sql = "SELECT id FROM phone WHERE number='{$phone}'";
		$result = query($sql);
		if (num_rows($result)) {
			$row = fetch_object($result);
			$this->_attributes['destination_phone_id'] = $row->id;
		} else {
			$sql = "INSERT INTO phone SET number='{$phone}'";
			$result = query($sql);
			if (!$result) {
				return false;
			}
			$this->_attributes['destination_phone_id'] = legacy_insert_id();
		}
		return true;

	}

	/**
	 * Сохранение заявки
	 *
	 * @return bool
	 */
	public function save()
	{
		$preparedData = $this->prepareData();

		if (isset($this->_attributes['id'])) {
			$result = $this->update($preparedData);
		} else {
			$result = $this->create($preparedData);
		}

		return $result;
	}

	/**
	 * Создание заявки
	 *
	 * @param array $data
	 * @return bool
	 */
	protected function create($data = array())
	{
		$req_created = is_int($this->_attributes['req_created'])
			? $this->_attributes['req_created']
			: strtotime($this->_attributes['req_created']);

		$request = new RequestModel(RequestModel::SCENARIO_REPLACED_PHONE);

		$request->client_phone = !empty($this->_attributes['client_phone']) ? $this->_attributes['client_phone'] : null;
		$request->req_created = $req_created;
		$request->clinic_id = !empty($this->_attributes['clinic_id']) ? $this->_attributes['clinic_id'] : null;
		$request->id_city = !empty($this->_attributes['id_city']) ? $this->_attributes['id_city'] : null;
		$request->client_name = !empty($this->_attributes['client_name']) ? $this->_attributes['client_name'] : null;
		$request->source_type = !empty($this->_attributes['source_type']) ? $this->_attributes['source_type'] : null;
		$request->is_transfer = !empty($this->_attributes['is_transfer']) ? $this->_attributes['is_transfer'] : null;
		$request->last_call_id = !empty($this->_attributes['last_call_id']) ? $this->_attributes['last_call_id'] : null;
		$request->req_type = !empty($this->_attributes['req_type']) ? $this->_attributes['req_type'] : null;
		$request->destination_phone_id = !empty($this->_attributes['destination_phone_id']) ? $this->_attributes['destination_phone_id'] : null;
		$request->is_hot = !empty($this->_attributes['is_hot']) ? $this->_attributes['is_hot'] : null;
		$request->for_listener = !empty($this->_attributes['for_listener']) ? $this->_attributes['for_listener'] : null;
		$request->partner_id = !empty($this->_attributes['partner_id']) ? $this->_attributes['partner_id'] : null;
		$request->enter_point = !empty($this->_attributes['enter_point']) ? $this->_attributes['enter_point'] : null;
		$request->kind = !empty($this->_attributes['kind']) ? $this->_attributes['kind'] : null;
		$request->req_status = !empty($this->_attributes['req_status']) ? $this->_attributes['req_status'] : null;

		if (!$request->save()) {
			return false;
		}

		$this->_attributes['id'] = $request->req_id;

		return true;
	}

	/**
	 * Изменение заявки
	 *
	 * @param array $data
	 * @return bool
	 */
	protected function update($data = array())
	{

		$sql = "UPDATE request SET
					client_phone            = '{$data['client_phone']}',
					add_client_phone        = '{$data['add_client_phone']}',
					client_name             = '{$data['client_name']}',
					clinic_id               = {$data['clinic_id']},
					req_doctor_id           = {$data['req_doctor_id']},
					req_sector_id           = {$data['req_sector_id']},
					id_city                 = {$data['id_city']},
					req_user_id             = {$data['req_user_id']},
					reject_reason           = {$data['reject_reason']},
					date_record             = '{$data['date_record']}',
					call_later_time         = '{$data['call_later_time']}',
					date_admission          = {$data['date_admission']},
					destination_phone_id    = {$data['destination_phone_id']},
					is_hot                  = {$data['is_hot']},
					for_listener            = {$data['for_listener']},
					kind                    = {$data['kind']}
				WHERE req_id = " . $this->_attributes['id'];
		$result = query($sql);

		$request = RequestModel::model()->findByPk($this->_attributes['id']);
		if ($request !== null) {
			$request->saveStatus($data['req_status']);
		}

		return $result ? true : false;
	}

	/**
	 * Формат входных данных
	 *
	 * @return array
	 */
	protected function prepareData()
	{
		$data = array();
		$data['req_user_id']            = !empty($this->_attributes['req_user_id']) ? $this->_attributes['req_user_id'] : 'NULL';
		$data['req_sector_id']          = !empty($this->_attributes['req_sector_id']) ? $this->_attributes['req_sector_id'] : 'NULL';
		$data['date_admission']         = !empty($this->_attributes['date_admission']) ? $this->_attributes['date_admission'] : 'NULL';
		$data['client_phone']           = !empty($this->_attributes['client_phone']) ? $this->_attributes['client_phone'] : 'NULL';
		$data['partner_id']             = !empty($this->_attributes['partner_id']) ? $this->_attributes['partner_id'] : 'NULL';
		$data['destination_phone_id']   = !empty($this->_attributes['destination_phone_id']) ? $this->_attributes['destination_phone_id'] : 'NULL';
		$data['call_later_time']        = !empty($this->_attributes['call_later_time']) ? $this->_attributes['call_later_time'] : 'NULL';
		$data['last_call_id']           = !empty($this->_attributes['last_call_id']) ? $this->_attributes['last_call_id'] : 'NULL';
		$data['clinic_id']              = !empty($this->_attributes['clinic_id']) ? $this->_attributes['clinic_id'] : 'NULL';
		$data['req_status']             = !empty($this->_attributes['req_status']) ? $this->_attributes['req_status'] : self::STATUS_NEW;
		$data['source_type']            = !empty($this->_attributes['source_type']) ? $this->_attributes['source_type'] : self::SOURCE_SITE;
		$data['is_transfer']            = !empty($this->_attributes['is_transfer']) ? $this->_attributes['is_transfer'] : 0;
		$data['client_name']            = !empty($this->_attributes['client_name']) ? $this->_attributes['client_name'] : '';
		$data['for_listener']           = !empty($this->_attributes['for_listener']) ? $this->_attributes['for_listener'] : 0;
		$data['is_hot']                 = !empty($this->_attributes['is_hot']) ? $this->_attributes['is_hot'] : 0;
		$data['id_city']                = !empty($this->_attributes['id_city']) ? $this->_attributes['id_city'] : 1;
		$data['date_record']            = !empty($this->_attributes['date_record']) ? $this->_attributes['date_record'] : 'NULL';
		$data['kind']                   = !empty($this->_attributes['kind']) ? $this->_attributes['kind'] : 0;
		$data['enter_point']            = !empty($this->_attributes['enter_point']) ? $this->_attributes['enter_point'] : '';
		$data['add_client_phone']       = !empty($this->_attributes['add_client_phone']) ? $this->_attributes['add_client_phone'] : 'NULL';
		$data['req_doctor_id']          = !empty($this->_attributes['req_doctor_id']) ? $this->_attributes['req_doctor_id'] : 'NULL';
		$data['reject_reason']          = !empty($this->_attributes['reject_reason']) ? $this->_attributes['reject_reason'] : 'NULL';

		if (empty($this->_attributes['req_created'])) {
			$this->_attributes['req_created'] = time();
		}

		return $data;
	}

	/**
	 * Загрузка модели
	 *
	 * @param $id
	 */
	protected function loadModel($id)
	{
		$sql = "SELECT
					t1.*,
					t1.req_user_id AS owner_id,
					t2.name AS ClinicName,
					t2.asterisk_phone AS phone_to,
					t3.number AS destination_phone
				FROM request t1
				LEFT JOIN clinic t2 ON t2.id=t1.clinic_id
				LEFT JOIN phone t3 ON t3.id=t1.destination_phone_id
				WHERE t1.req_id=" . $id;
		$result = query($sql);
		if (num_rows($result) == 1) {
			$row = fetch_array($result);
			$this->_attributes['id'] = $row['req_id'];
			foreach ($row as $attr => $value) {
				$this->$attr = $value;
			}
		}
	}

}
