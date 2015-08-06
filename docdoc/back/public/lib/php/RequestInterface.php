<?php
/**
 * Класс интерфейса заявок
 * Class RequestInterface
 */
class RequestInterface
{

	/**
	 * Виды интерфейсов
	 */
	const VIEW_DEFAULT       = 'default';
	const VIEW_CALL_CENTER   = 'call_center';
	const VIEW_DOC_LISTENER  = 'doc_listener';
	const VIEW_DIAG_LISTENER = 'diag_listener';
	const VIEW_PARTNERS      = 'partners';

	/**
	 * Вид интерфейса заявки
	 * @var string
	 */
	private $_view;

	/**
	 * Отображаемые фильтры для каждого вида интерфейса
	 * @var array
	 */
	private $_filters = array(
		self::VIEW_DEFAULT       => array(
			'kind',
			'req_type',
			'req_status',
			'req_sector_id',
			'req_user_id',
			'req_created',
			'date_admission',
			'diagnostic_id',
			'client_phone',
			'client_name',
			'source_type',
			'req_id',
			'destination_phone_id',
			'clinic_id',
			'date_record',
			'clinic_not_found',
			'partner',
			'city',
		),
		self::VIEW_CALL_CENTER   => array(
			'kind',
			'req_type',
			'req_status',
			'req_sector_id',
			'req_user_id',
			'req_created',
			'date_admission',
			'diagnostic_id',
			'client_phone',
			'client_name',
			'source_type',
			'req_id',
			'destination_phone_id',
			'clinic_id',
			'date_record',
			'clinic_not_found',
			'partner',
			'city',
		),
		self::VIEW_DOC_LISTENER  => array(
			'kind',
			'req_created',
			'clinic_id',
			'req_user_id',
			'req_status',
			'source_type',
			'req_id',
			'client_phone',
			'client_name',
			'date_admission',
			'date_record',
			'clinic_not_found',
			'partner',
			'diagnostic_id',
			'city',
		),
		self::VIEW_DIAG_LISTENER => array(
			'req_created',
			'clinic_id',
			'req_user_id',
			'req_status',
			'source_type',
			'diagnostic_id',
			'req_id',
			'client_phone',
			'client_name',
			'date_admission',
			'clinic_not_found',
			'partner',
			'city',
		),
		self::VIEW_PARTNERS       => array(
			'kind',
			'req_type',
			'req_status',
			'req_sector_id',
			'req_user_id',
			'req_created',
			'date_admission',
			'diagnostic_id',
			'client_phone',
			'client_name',
			'source_type',
			'req_id',
			'destination_phone_id',
			'clinic_id',
			'date_record',
			'clinic_not_found',
			'partner',
			'partner_status',
			'billing_status',
			'city',
		),
	);

	/**
	 * Разрешенные роли пользовтаелей
	 * @var array
	 */
	private $_roles = array(
		self::VIEW_DEFAULT          => array('ADM', 'SOP'),
		self::VIEW_CALL_CENTER      => array('ADM', 'SOP', 'OPR'),
		self::VIEW_DOC_LISTENER     => array('ADM', 'CNM', 'ACM', 'LIS'),
		self::VIEW_DIAG_LISTENER    => array('ADM', 'CNM', 'ACM', 'LIS'),
		self::VIEW_PARTNERS         => array('ADM', 'CNM', 'ACM', 'SOP'),
	);

	/**
	 * Конструктор
	 * @param string $view
	 */
	public function __construct($view = self::VIEW_DEFAULT)
	{
		$this->_view = $view;
	}

	/**
	 * Получение отображаемых фильтров
	 * @return mixed
	 */
	public function getFilters()
	{
		return $this->_filters[$this->_view];
	}

	/**
	 * Разрешенные роли пользователей
	 * @return mixed
	 */
	public function getAllowedRoles()
	{
		return $this->_roles[$this->_view];
	}

	/**
	 * Проверка на БО слухача
	 * @return bool
	 */
	public function isDocListener()
	{
		return $this->_view === self::VIEW_DOC_LISTENER;
	}

	/**
	 * Проверка на БО слухача по диагностике
	 * @return bool
	 */
	public function isDiagListener()
	{
		return $this->_view === self::VIEW_DIAG_LISTENER;
	}

	/**
	 * Проверка на БО слухача
	 * @return bool
	 */
	public function isListener()
	{
		$listeners = array(self::VIEW_DIAG_LISTENER, self::VIEW_DOC_LISTENER);
		return in_array($this->_view, $listeners);
	}

	/**
	 * Проверка на БО оператора
	 * @return bool
	 */
	public function isCallCenter()
	{
		return $this->_view === self::VIEW_CALL_CENTER;
	}

	/**
	 * Проверка на все звонки
	 * @return bool
	 */
	public function isDefault()
	{
		return $this->_view === self::VIEW_DEFAULT;
	}

	/**
	 * Получение XML со списком выбранных параметров поиска
	 * @param $filters
	 * @return string
	 */
	static public function getFilterXml($filters)
	{
		$xml = '<FilterParams>';
		if (count($filters)) {
			foreach ($filters as $key => $items) {
				if (count($items)) {
					$xml .= "<{$key}>";
					foreach ($items as $id) {
						$xml .= "<ElementId>{$id}</ElementId>";
					}
					$xml .= "</$key>";
				}
			}
		}
		$xml .= '</FilterParams>';

		return $xml;
	}
} 