<?php
use dfs\docdoc\objects\call\Provider;

require_once __DIR__ . "/../dateTimeLib.php";

/**
 * Class Record
 */
class Record
{

	// Типы аудиозаписей
	const TYPE_UNDEFINED = 0;
	const TYPE_IN = 1;
	const TYPE_OUT = 2;
	const TYPE_TRANSFER = 3;

	// Источники аудиозаписей
	const SOURCE_REQUEST = 0;
	const SOURCE_CALL_TO_DOCTOR = 1;
	const SOURCE_DIAGNOSTIC = 2;

	private $_attributes;

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
	 * @param void   $value
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
	 * Сохранение записи
	 *
	 * @return bool
	 */
	public function save()
	{
		if (isset($this->_attributes['record_id'])) {
			$result = $this->update();
		} else {
			$result = $this->create();
		}

		return $result;
	}

	/**
	 * Создание записи
	 *
	 * @return bool
	 */
	private function create()
	{
		$this->_attributes['month'] = date('n');
		$this->_attributes['year'] = date('Y');

		$sql = "INSERT INTO request_record SET
					request_id = {$this->_attributes['request_id']},
					clinic_id  = {$this->_attributes['clinic_id']},
					record     = '{$this->_attributes['record']}',
					type       = {$this->_attributes['type']},
					month      = {$this->_attributes['month']},
					year       = {$this->_attributes['year']},
					crDate     = NOW()";
		$result = query($sql);

		return $result ? true : false;
	}

	/**
	 * Изменение записи
	 *
	 * @return bool
	 */
	private function update()
	{

		$sql = "UPDATE request_record SET
					request_id = {$this->_attributes['request_id']},
					clinic_id  = {$this->_attributes['clinic_id']},
					record     = '{$this->_attributes['record']}',
					type       = {$this->_attributes['type']},
					month      = {$this->_attributes['month']},
					year       = {$this->_attributes['year']},
					crDate     = NOW()";
		$result = query($sql);

		return $result ? true : false;
	}

	/**
	 * Загрузка модели
	 *
	 * @param $id
	 */
	private function loadModel($id)
	{
		$sql = "SELECT
					t1.*
				FROM request_record t1
				WHERE t1.record_id={$id}";
		$result = query($sql);
		if (num_rows($result) == 1) {
			$row = fetch_array($result);
			foreach ($row as $attr => $value) {
				$this->$attr = $value;
			}
		}
	}

	/**
	 * Получение аудиозаписей
	 *
	 * @param array $params
	 *
	 * @return array
	 */
	static public function getItems($params = array())
	{
		$data = array();

		$sqlAdd = "1=1 ";
		if (!empty($params['requestId'])) {
			$sqlAdd .= " AND t1.request_id = {$params['requestId']}";
		}
		if (!empty($params['clinicId'])) {
			$sqlOr = empty($params['lk_status'])
				? ""
				: " OR isAppointment = 'yes'";
			$sqlAdd .= " AND (t1.clinic_id = {$params['clinicId']}{$sqlOr})";
		}
		$sql = "SELECT
					t1.*
				FROM request_record t1
				WHERE
					{$sqlAdd}
				ORDER BY t1.crDate";
		$result = query($sql);
		if (num_rows($result)) {
			while ($row = fetch_array($result)) {
				$timestamp = strtotime($row['crDate']);
				$row['crHour'] = date('H', $timestamp);
				$row['crMin'] = date('i', $timestamp);
				$row['fDate'] = date('d.m.Y', $timestamp);
				$row['fDateTime'] = date('d.m.Y H:i', $timestamp);
				$row['filename'] = 'https://' . Yii::app()->params['hosts']['back'] . '/2.0/record/download/' . $row['record_id'];
				$row['fDuration'] = formatTime($row['duration']);
				array_push($data, $row);
			}
		}

		return $data;
	}
}
