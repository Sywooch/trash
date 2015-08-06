<?php

namespace dfs\docdoc\reports;

use dfs\docdoc\models\RequestModel;
use dfs\docdoc\models\PartnerModel;

class RequestCollection extends Report
{
	/**
	 * Тип отчетов
	 *
	 * @var array
	 */
	static protected $_reportTypes = [
		'clinics' => 'Клиники',
		'diagnostics' => 'Диагностика',
	];

	/**
	 * Тип заявок
	 *
	 * @var array
	 */
	static protected $_requestTypes = [
		'record' => 'Запись',
		'come' => 'Дошедшие',
	];

	/**
	 * Группировка по диагностикам
	 *
	 * @var array
	 */
	static protected $_diagnosticGroups = [
		19 => 'count_kt',
		21 => 'count_mrt',
	];

	/**
	 * Тип отчета
	 *
	 * @var string
	 */
	protected $_reportType = 'clinics';

	/**
	 * Тип заявок
	 *
	 * @var string | null
	 */
	protected $_requestType = null;

	/**
	 * Начальная дата создания заявки
	 *
	 * @var int
	 */
	protected $_dateBegin = 0;

	/**
	 * Конечная дата создания заявки
	 *
	 * @var int
	 */
	protected $_dateEnd = 0;

	/**
	 * Начальная дата для дошедших пациентов
	 *
	 * @var int
	 */
	protected $_dateAdmissionBegin = 0;

	/**
	 * Конечная дата для дошедших пациентов
	 *
	 * @var int
	 */
	protected $_dateAdmissionEnd = 0;

	/**
	 * Отчет по диагностике или нет
	 *
	 * @var bool
	 */
	protected $_isDiagnostic = false;

	/**
	 * Город
	 *
	 * @var int | null
	 */
	protected $_cityId = null;


	/**
	 * @return array
	 */
	static public function getReportTypes() {
		return self::$_reportTypes;
	}

	/**
	 * @return array
	 */
	static public function getRequestTypes() {
		return self::$_requestTypes;
	}

	/**
	 * @return string
	 */
	public function getReportType()
	{
		return $this->_reportType;
	}

	/**
	 * @return string
	 */
	public function getRequestType()
	{
		return $this->_requestType;
	}

	/**
	 * @return string
	 *
	 */
	public function getPeriodBegin()
	{
		return date('d.m.Y', $this->_dateBegin);
	}

	/**
	 * @return string
	 *
	 */
	public function getPeriodEnd()
	{
		return date('d.m.Y', $this->_dateEnd);
	}

	/**
	 * @return string | null
	 *
	 */
	public function getAdmissionPeriodBegin()
	{
		return $this->_dateAdmissionBegin === 0 ? null : date('d.m.Y', $this->_dateAdmissionBegin);
	}

	/**
	 * @return string | null
	 *
	 */
	public function getAdmissionPeriodEnd()
	{
		return $this->_dateAdmissionEnd === 0 ? null : date('d.m.Y', $this->_dateAdmissionEnd);
	}


	/**
	 * @param string $type
	 *
	 * @return $this
	 */
	public function setReportType($type)
	{
		if (isset(self::$_reportTypes[$type])) {
			$this->_reportType = $type;
		}

		return $this;
	}

	/**
	 * @param string $type
	 *
	 * @return $this
	 */
	public function setRequestType($type)
	{
		if (isset(self::$_requestTypes[$type])) {
			$this->_requestType = $type;
		}

		return $this;
	}

	/**
	 * @param bool $isDiagnostic
	 *
	 * @return $this
	 */
	public function setDiagnostic($isDiagnostic = true)
	{
		$this->_isDiagnostic = $isDiagnostic;

		return $this;
	}

	/**
	 * @param int $cityId
	 *
	 * @return $this
	 */
	public function setCityId($cityId)
	{
		$this->_cityId = $cityId;

		return $this;
	}

	/**
	 * Установка периода выборки заявок
	 * 
	 * @param int $dateBegin
	 * @param int $dateEnd
	 *
	 * @return $this
	 */
	public function setPeriod($dateBegin, $dateEnd)
	{
		$current = time();
		$this->_dateBegin = $this->convertDate($dateBegin, $current);
		$this->_dateEnd = $this->convertDate($dateEnd, $current);

		return $this;
	}

	/**
	 * Установка периода для фильтрации по дате дошедших
	 *
	 * @param int $dateAdmissionBegin
	 * @param int $dateAdmissionEnd
	 *
	 * @return $this
	 */
	public function setAdmissionPeriod($dateAdmissionBegin, $dateAdmissionEnd)
	{
		$this->_dateAdmissionBegin = $this->convertDate($dateAdmissionBegin);
		$this->_dateAdmissionEnd = $this->convertDate($dateAdmissionEnd);

		return $this;
	}

	/**
	 * Запуск формирования отчета
	 *
	 * @throws \CException
	 */
	public function execute()
	{
		switch ($this->_reportType) {
			case 'clinics':
				$this->_reportData = $this->reportCountRequests();
				break;

			case 'diagnostics':
				$this->_reportData = $this->reportCountRequestsByDiagnosticGroups();
				break;

			default:
				$this->_reportData = [];
				throw new \CException('Unknown report type');
				break;
		}
	}


	/**
	 * Количество заявок для каждой клиники
	 *
	 * @return array
	 */
	private function reportCountRequests()
	{
		if ($this->_requestType === null) {
			$tables = [];
			foreach (self::$_requestTypes as $type => $title) {
				$tables[] = $this->sqlRequestCount($type);
			}
			$tableSql = '(' . implode(') UNION (', $tables) . ')';
		} else {
			$tableSql = $this->sqlRequestCount($this->_requestType);
		}

		$sql = $this->_isDiagnostic ? $this->sqlDiagnostics($tableSql) : $this->sqlClinics($tableSql);

		$values = [
			'date_begin' => $this->_dateBegin,
			'date_end' => $this->_dateEnd + 86400,
		];

		if ($this->_cityId !== null) {
			$values['city_id'] = $this->_cityId;
		}

		if ($this->_dateAdmissionBegin && $this->_dateAdmissionEnd) {
			$values['date_admission_begin'] = $this->_dateAdmissionBegin;
			$values['date_admission_end'] = $this->_dateAdmissionEnd + 86400;
		}

		return \Yii::app()->getDb()
			->createCommand($sql)
			->bindValues($values)
			->queryAll();
	}

	/**
	 * Количество заявок для каждой клиники с разделением на КТ, МРТ и прочее
	 *
	 * @return array
	 * @throws \CException
	 */
	private function reportCountRequestsByDiagnosticGroups()
	{
		$this->_isDiagnostic = true;

		$data = [];
		foreach ($this->reportCountRequests() as $row) {
			$key = $row['clinic_id'] . '-' . $row['type'];
			if (!isset($data[$key])) {
				$data[$key] = [
					'clinic_id'   => $row['clinic_id'],
					'clinic_name' => $row['clinic_name'],
					'count_kt'    => 0,
					'count_mrt'   => 0,
					'count_other' => 0,
					'type'        => $row['type'],
				];
			}
			$countKey = isset(self::$_diagnosticGroups[$row['diagnostics_id']]) ? self::$_diagnosticGroups[$row['diagnostics_id']] : 'count_other';
			$data[$key][$countKey] += $row['count'];
		}

		return $data;
	}

	/**
	 * Запрос с разбивкой по клиникам
	 *
	 * @param string $tableSql
	 *
	 * @return string
	 */
	private function sqlClinics($tableSql)
	{
		return 'SELECT t.clinic_id as clinic_id,
 			CASE c.short_name WHEN "" THEN c.name ELSE c.short_name END as clinic_name,
			COUNT(t.req_id) as count,
			t.type as type
			FROM (' . $tableSql . ') as t
				INNER JOIN clinic AS c ON (t.clinic_id = c.id)
			GROUP BY t.clinic_id, t.type
			ORDER BY clinic_name, type';
	}

	/**
	 * Запрос с разбивкой по диагностикам
	 *
	 * @param string $tableSql
	 *
	 * @return string
	 */
	private function sqlDiagnostics($tableSql)
	{
		return 'SELECT t.clinic_id as clinic_id,
				CASE c.short_name WHEN "" THEN c.name ELSE c.short_name END as clinic_name,
				COUNT(t.req_id) as count,
				t.type as type,
				t.diagnostics_id as diagnostics_id
			FROM (' . $tableSql . ') as t
				INNER JOIN clinic as c ON (t.clinic_id = c.id)
			GROUP BY t.clinic_id, t.type, t.diagnostics_id
			ORDER BY clinic_name, type';
	}

	/**
	 * Подзапрос для подсчета количества заявок
	 *
	 * @param string     $type
	 *
	 * @throws \CException
	 * @return string
	 */
	private function sqlRequestCount($type)
	{
		$select = [
			'CASE c.parent_clinic_id WHEN 0 THEN c.id ELSE c.parent_clinic_id END as clinic_id',
			'r.req_id as req_id',
			'"' . $type . '" as type',
		];
		$join = [
			'INNER JOIN clinic AS c ON (r.clinic_id = c.id)'
		];
		$where = [
			'r.kind = ' . ($this->_isDiagnostic ? RequestModel::KIND_DIAGNOSTICS : RequestModel::KIND_DOCTOR),
			'r.req_status <> ' . RequestModel::STATUS_REMOVED,
			'r.source_type <> ' . RequestModel::SOURCE_YANDEX,
			'r.req_created >= :date_begin AND r.req_created < :date_end',
			// 112014
			'(r.partner_id IS NULL OR r.partner_id <> ' . PartnerModel::SMART_MEDIA_2 . ")",
		];

		if ($this->_dateAdmissionBegin && $this->_dateAdmissionEnd) {
			$where[] = 'r.date_admission >= :date_admission_begin AND r.date_admission < :date_admission_end';
		}

		if ($this->_cityId !== null) {
			$where[] = 'id_city = :city_id';
		}

		if ($this->_isDiagnostic) {
			$select[] = 'CASE d.parent_id WHEN 0 THEN d.id ELSE d.parent_id END as diagnostics_id';
			$join[] = 'LEFT JOIN diagnostica AS d ON (r.diagnostics_id = d.id)';
		}

		switch ($type) {
			case 'come':
				$where[] = 'r.req_status = ' . RequestModel::STATUS_CAME;
				break;

			case 'record':
				$where[] = 'r.date_admission is not null AND r.date_admission > 0';
				break;
		}

		return 'SELECT ' . implode(', ', $select) . ' FROM request AS r ' . implode("\n", $join) . ' WHERE ' . implode(' AND ', $where);
	}
}
