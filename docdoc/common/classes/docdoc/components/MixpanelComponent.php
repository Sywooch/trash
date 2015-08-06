<?php
namespace dfs\docdoc\components;

use dfs\docdoc\models\ComagicLogModel;
use dfs\docdoc\models\PartnerModel;
use dfs\docdoc\models\RequestModel;
use dfs\docdoc\models\RatingStrategyModel;
use Mixpanel;
use Yii;

/**
 * Class MixpanelComponent
 *
 * Инициализация библиотеки Mixpanel
 *
 * @package dfs\docdoc\components
 */
class MixpanelComponent extends \CApplicationComponent
{
	/**
	 * Токен Mixpanel
	 * @var string
	 */
	public $token = null;
	/**
	 * Параметры для инициализации
	 * @var array
	 */
	public $options = [];

	/**
	 * Объект для работы с Mixpanel
	 * @var Mixpanel
	 */
	private $_mixpanel;

	/**
	 * Данные для событий при загрузке страницы
	 * @var array
	 */
	private $_tracks = [];

	/**
	 * Инициализация компонента
	 * @return void
	 */
	public function init()
	{
		parent::init();
		$this->_mixpanel = Mixpanel::getInstance($this->token, $this->options);
	}

	/**
	 * Получение объекта Mixpanel
	 * @return Mixpanel
	 */
	public function getMixpanel()
	{
		return $this->_mixpanel;
	}

	/**
	 * Получение токена
	 * @return string
	 */
	public function getToken()
	{
		return $this->token;
	}

	/**
	 * Получение данных для событий при загрузке страницы
	 * @return array
	 */
	public function getTracks()
	{
		return $this->_tracks;
	}

	/**
	 * Получение данных для событий при загрузке страницы
	 *
	 * @param $name
	 * @param $params
	 *
	 * @return $this
	 */
	public function addTrack($name, $params = null)
	{
		$this->_tracks[$name] = $params;

		return $this;
	}

	/**
	 * Отправка события
	 *
	 * @param string $eventName
	 * @param RequestModel $request
	 */
	public function sendEvent($eventName, RequestModel $request)
	{
		// Больше не отправляем события в MixPanel
		return;
	}

	/**
	 * Регистрация события
	 *
	 * @param RequestModel $request
	 */
	public function event(RequestModel $request)
	{
		$eventName = $this->getEventName($request);

		//если этот клиент не зарегистрирован в микспанели, регистрируем
		if (!is_null($request->client)
			&& $request->client->registered_in_mixpanel < 1
			&& !empty($request->client_phone)
		) {
			$this->_createAlias($request->client_phone);
			$request->client->saveRegisteredInMixPanel();
		}

		$this->sendEvent($eventName, $request);
	}

	/**
	 * Получение distinct_id микспанели из cookies
	 *
	 * @return null|string
	 */
	public function getMixPanelDistinctId()
	{
		$keys = Yii::app()->request->cookies->getKeys();
		foreach ($keys as $k) {
			if (substr($k, -9) === '_mixpanel') {
				$mp_cookie = Yii::app()->request->cookies[$k];

				if (!isset($mp_cookie->value)) {
					return null;
				}

				$value = json_decode($mp_cookie->value);
				if (isset($value->distinct_id)) {
					return $value->distinct_id;
				}

				return null;
			}
		}

		return null;
	}

	/**
	 * Определение имени события для статуса
	 *
	 * @param RequestModel $request
	 *
	 * @return string
	 */
	public function getEventName($request)
	{
		$method = null;

		//если создана новая заявка или заявка изменила тип с диагностики на врача
		if ($request->getIsNewRecord() || $request->isChanged('kind')) {
			$method = 'AppCreated';
		}
		elseif ((int)$request->req_status !== (int)$request->getOldStatus()) {
			switch ($request->req_status) {
				case RequestModel::STATUS_REJECT:
					$method = 'AppRefused';
					break;
				case RequestModel::STATUS_RECORD:
					$method = 'AppApproved';
					break;
				case RequestModel::STATUS_CAME:
					$method = 'AppVisit';
					break;
				case RequestModel::STATUS_REMOVED:
					$method = 'AppDeleted';
					break;
			}
		}

		return $method;
	}

	/**
	 * Возвращает параметры для MixPanel API
	 *
	 * @param RequestModel $request
	 * @param string|null  $eventName
	 *
	 * @return string[]
	 */
	public function getEvent(RequestModel $request, $eventName = null)
	{
		$event = [
			'Spec'       => null,
			'Clinic'     => null,
			'Metro'      => null,
			'Area'       => null,
			'Name'       => null,
			'Price'      => null,
			'Discount'   => null,
			'Reviews'    => null,
			'Rating'     => null,
			'Experience' => null,
			'Awards'     => null,
			'Photo'      => null,
			'City'       => null,
			'Type'       => null,
			'DocID'      => null,
			'ClinID'     => null,
			'AppID'      => null,
			'PartnerID'  => 0,
			'RatingStrategy' => 0,
		];

		$event['Type'] = $request->enter_point;
		$event['PartnerID'] = (int)$request->partner_id;
		$ratingComponent = Yii::app()->getComponent('rating', false);
		$ratingComponent instanceof RatingComponent && $event['RatingStrategy'] = $ratingComponent->getId(RatingStrategyModel::FOR_DOCTOR);

		if ($request->sector !== null) {
			$event['Spec'] = $request->sector->name;
		}

		if ($request->clinic !== null) {
			$event['Clinic'] = $request->clinic->name;
			$event['Metro']  = (count($request->clinic->stations)) ? $request->clinic->stations[0]->name : '';
			$event['Area']   = ($request->clinic->district !== null) ? $request->clinic->district->name : '';
			$event['City'] = $request->city->title;
			$event['ClinID'] = $request->clinic->id;

		}

		if ($request->doctor !== null) {
			$event['Name']  = $request->doctor->name;
			$event['Price'] = ($request->doctor->special_price > 0) ? $request->doctor->special_price : $request->doctor->price;
			$event['Discount'] = ($request->doctor->special_price > 0) ? true : false;
			$event['Reviews'] = $request->doctor->getOpinionCount();
			$event['Rating'] = $request->doctor->getDoctorRating();
			$event['Experience'] = date("Y") - $request->doctor->experience_year;
			$event['Awards'] = $request->doctor->text_degree;
			$event['Photo'] = !empty($request->doctor->image);
			$event['DocID'] = $request->doctor->id;
		}

		$event['AppID'] = $request->req_id;

		switch ($eventName) {
			case 'AppCreated':
				$event['time'] = $request->req_created;
				break;
			default:
				//pass
		}

		return $event;
	}

	/**
	 * Создает ивент из лога comagic
	 *
	 * @param ComagicLogModel $log
	 * @param RequestModel    $request
	 *
	 * @return mixed
	 */
	protected function createCallEvent(ComagicLogModel $log, RequestModel $request)
	{
		$event['numa'] = $log->numa;
		$event['duration'] = $log->duration;
		$event['utm_source'] = $log->utm_source;
		$event['utm_medium'] = $log->utm_medium;
		$event['utm_term'] = $log->utm_term;
		$event['utm_content'] = $log->utm_content;
		$event['utm_campaign'] = $log->utm_campaign;
		$event['search_engine'] = $log->search_engine;
		$event['search_query'] = $log->search_query;
		$event['page_url'] = $log->page_url;
		$event['referrer'] = $log->referrer;
		$event['numb'] = $log->numb;

		$event['time'] = $request->req_created - 1;

		return $event;
	}

	/**
	 * Идентификация
	 *
	 * @param string $alias номер телефона
	 */
	private function _identify($alias)
	{
		if ($this->token === null) return;

		$this->_mixpanel->identify($alias);
	}


	/**
	 * создание алияса
	 *
	 * @param string $alias номер телефона
	 */
	private function _createAlias($alias)
	{
		if ($this->token === null) return;

		//если у нас есть микспанелевские куки, значит у нас клиент с сайта,
		//привязываемся к одентификатору микспанели
		//иначе генерим свой id
		$distinct_id = $this->getMixPanelDistinctId();
		if ($distinct_id === null) {
			$distinct_id = md5(microtime());
		}

		$this->_mixpanel->createAlias($distinct_id, $alias);
	}

	/**
	 * Фиксирование стоимости покупки
	 *
	 * @param string $alias номер телефона
	 * @param float $cost стоимость
	 */
	private function _trackCharge($alias, $cost)
	{
		if ($this->token === null) return;

		$this
			->_mixpanel
			->people
			->trackCharge($alias, $cost);
	}

	/**
	 * Фиксирование события
	 *
	 * @param string $eventName
	 * @param string[] $event
	 */
	private function _track($eventName, $event)
	{
		if ($this->token === null) return;

		$this->_mixpanel->track($eventName, $event);
	}

	/**
	 * Установка персоны
	 *
	 * @param string $alias номер телефона
	 * @param string[] $params
	 */
	private function _setPeople($alias,	$params)
	{
		if ($this->token === null) return;

		$this
			->_mixpanel
			->people
			->set($alias, $params);
	}

	/**
	 * Шлем ивент Call
	 *
	 * @param RequestModel    $request
	 * @param ComagicLogModel $log
	 */
	public function sendCallEvent(RequestModel $request, ComagicLogModel $log)
	{
		// Больше не отправляем события в MixPanel
		return;
	}
}
